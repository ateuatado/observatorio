<?php

namespace App\Services;

use App\Models\AcervoDocumentoModel;

/**
 * Serviço responsável por varrer o filesystem de arquivos históricos
 * e popular/sincronizar a tabela `acervo_documentos`.
 *
 * Características:
 *  - Idempotente: re-executar não cria duplicatas (usa hash SHA-256)
 *  - Extrai metadados do nome do arquivo e da estrutura de pastas
 *  - Relata progresso via callback para uso em CLI ou AJAX
 */
class PdfIndexerService
{
    private AcervoDocumentoModel $model;
    private string $basePath;

    /** Mapeamento de veículos de imprensa comuns nos nomes de arquivo */
    private const VEICULOS = [
        'Brasil de Fato'   => 'Brasil de Fato',
        'Ponte Jornalismo' => 'Ponte Jornalismo',
        'The Intercept'    => 'The Intercept',
        'Agência Pública'  => 'Agência Pública',
        'Agencia Publica'  => 'Agência Pública',
        'Folha de'         => 'Folha de S.Paulo',
        'UOL'              => 'UOL',
        'G.1'              => 'G1',
        'G1'               => 'G1',
        'Rudsonews'        => 'Rudsonews',
    ];

    /** Meses em português para extração do nome da pasta */
    private const MESES_PT = [
        'janeiro' => 1, 'fevereiro' => 2, 'março' => 3, 'marco' => 3,
        'abril' => 4, 'maio' => 5, 'junho' => 6,
        'julho' => 7, 'agosto' => 8, 'setembro' => 9,
        'outubro' => 10, 'novembro' => 11, 'dezembro' => 12,
    ];

    public function __construct()
    {
        $this->model    = new AcervoDocumentoModel();
        $this->basePath = ROOTPATH . 'arquivos' . DIRECTORY_SEPARATOR;
    }

    /**
     * Varre o diretório base e indexa todos os PDFs novos.
     *
     * @param callable|null $progressoCallback fn(string $msg, int $novos, int $total)
     * @return array{novos: int, ja_existentes: int, erros: int, total_encontrados: int}
     */
    public function indexar(?callable $progressoCallback = null): array
    {
        $stats = ['novos' => 0, 'ja_existentes' => 0, 'erros' => 0, 'total_encontrados' => 0];

        if (!is_dir($this->basePath)) {
            log_message('error', "PdfIndexerService: diretório não encontrado: {$this->basePath}");
            return $stats;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $arquivo) {
            if (strtolower($arquivo->getExtension()) !== 'pdf') {
                continue;
            }

            $stats['total_encontrados']++;

            try {
                $resultado = $this->indexarArquivo($arquivo->getRealPath());

                if ($resultado === 'novo') {
                    $stats['novos']++;
                    if ($progressoCallback) {
                        ($progressoCallback)($arquivo->getFilename(), $stats['novos'], $stats['total_encontrados']);
                    }
                } else {
                    $stats['ja_existentes']++;
                }
            } catch (\Exception $e) {
                $stats['erros']++;
                log_message('error', "PdfIndexerService: {$arquivo->getRealPath()}: " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Indexa um único arquivo PDF. Retorna 'novo' | 'existente'.
     */
    public function indexarArquivo(string $caminhoAbsoluto): string
    {
        $hash = hash_file('sha256', $caminhoAbsoluto);

        if ($this->model->existePorHash($hash)) {
            return 'existente';
        }

        $nomeArquivo  = basename($caminhoAbsoluto);
        $camRel       = $this->caminhoRelativo($caminhoAbsoluto);

        [$ano, $mes]  = $this->extrairAnoPastaEMes($caminhoAbsoluto);
        $idInterno    = $this->extrairIdInterno($nomeArquivo);
        $veiculo      = $this->extrairVeiculo($nomeArquivo);
        $dataDoc      = $this->extrairDataDoNome($nomeArquivo);

        $this->model->insert([
            'caminho_relativo' => $camRel,
            'hash_sha256'      => $hash,
            'nome_arquivo'     => $nomeArquivo,
            'tamanho_bytes'    => filesize($caminhoAbsoluto),
            'pasta_ano'        => $ano,
            'pasta_mes'        => $mes,
            'id_interno'       => $idInterno,
            'veiculo_imprensa' => $veiculo,
            'data_documento'   => $dataDoc,
            'status'           => 'pendente',
            'ia_processado'    => 0,
            'indexado_em'      => date('Y-m-d H:i:s'),
        ]);

        return 'novo';
    }

    // -------------------------------------------------------------------------
    // Extração de metadados
    // -------------------------------------------------------------------------

    /**
     * Extrai ano e mês da estrutura de pastas.
     * @return array{int|null, int|null}
     */
    public function extrairAnoPastaEMes(string $caminho): array
    {
        $partes = explode(DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $caminho));
        $ano    = null;
        $mes    = null;

        foreach ($partes as $parte) {
            if (preg_match('/\b(200[5-9]|201[0-9]|202[0-9])\b/', $parte, $m)) {
                $ano = (int)$m[1];
            }
            $parteLower = mb_strtolower($parte);
            foreach (self::MESES_PT as $nomeMes => $numMes) {
                if (str_contains($parteLower, $nomeMes)) {
                    $mes = $numMes;
                    break;
                }
            }
        }

        return [$ano, $mes];
    }

    /**
     * Extrai o ID interno do arquivo (ex: "ID.42.0" → "ID.42.0").
     */
    public function extrairIdInterno(string $nome): ?string
    {
        if (preg_match('/^ID\.(\d+(?:\.\d+)*)\./i', $nome, $m)) {
            return 'ID.' . $m[1];
        }
        return null;
    }

    /**
     * Extrai o veículo de imprensa do nome do arquivo.
     */
    public function extrairVeiculo(string $nome): ?string
    {
        foreach (self::VEICULOS as $padrao => $veiculo) {
            if (stripos($nome, $padrao) !== false) {
                return $veiculo;
            }
        }
        return null;
    }

    /**
     * Tenta extrair uma data do nome do arquivo.
     * Padrões: DD_MM_YYYY | DD/MM/YYYY | YYYY-MM-DD
     */
    public function extrairDataDoNome(string $nome): ?string
    {
        if (preg_match('/(\d{1,2})[_\/](\d{1,2})[_\/](\d{4})/', $nome, $m)) {
            $dia = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mes = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $ano = $m[3];
            if (checkdate((int)$mes, (int)$dia, (int)$ano)) {
                return "{$ano}-{$mes}-{$dia}";
            }
        }
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $nome, $m)) {
            if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
                return "{$m[1]}-{$m[2]}-{$m[3]}";
            }
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function caminhoRelativo(string $caminhoAbsoluto): string
    {
        return ltrim(str_replace(
            [ROOTPATH, '\\'],
            ['', '/'],
            $caminhoAbsoluto
        ), '/');
    }
}
