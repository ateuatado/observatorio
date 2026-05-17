<?php

namespace App\Services;

use App\Models\AcervoDocumentoModel;

/**
 * Serviço de análise de conteúdo de PDFs.
 *
 * Estratégia: extração de texto via smalot/pdfparser + heurística local.
 * Se uma API_KEY de Gemini estiver configurada em .env, usa a IA.
 */
class PdfAnalysisService
{
    private AcervoDocumentoModel $model;

    private const TIPOS_VIOLENCIA = [
        'execução' => 'execucao', 'execucao' => 'execucao',
        'chacina' => 'chacina',
        'tortura' => 'tortura',
        'abuso de poder' => 'abuso_poder', 'abuso_poder' => 'abuso_poder',
        'morte em custódia' => 'morte_custodia', 'morte custodia' => 'morte_custodia',
        'desaparecimento' => 'desaparecimento',
        'ameaça' => 'ameaca', 'ameaca' => 'ameaca',
    ];

    public function __construct()
    {
        $this->model = new AcervoDocumentoModel();
    }

    /**
     * Extrai texto do PDF e executa análise. Salva resultado no banco.
     * Retorna o array de análise.
     */
    public function analisar(int $documentoId): array
    {
        $doc = $this->model->find($documentoId);
        if (!$doc) {
            throw new \InvalidArgumentException("Documento #{$documentoId} não encontrado.");
        }

        $caminhoAbsoluto = ROOTPATH . $doc['caminho_relativo'];

        // 1. Extrair texto
        $texto = $this->extrairTexto($caminhoAbsoluto);
        if ($texto) {
            $this->model->salvarTexto($documentoId, $texto);
        }

        // 2. Analisar
        $apiKey = env('GEMINI_API_KEY');
        $analise = $apiKey
            ? $this->analisarComGemini($texto ?? '', $doc['nome_arquivo'], $apiKey)
            : $this->analisarHeuristico($texto ?? '', $doc);

        // 3. Salvar
        $this->model->salvarAnalise($documentoId, $analise);

        return $analise;
    }

    // -------------------------------------------------------------------------
    // Extração de texto
    // -------------------------------------------------------------------------

    public function extrairTexto(string $caminhoAbsoluto): ?string
    {
        if (!file_exists($caminhoAbsoluto)) {
            return null;
        }

        // Tentativa 1: smalot/pdfparser
        try {
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($caminhoAbsoluto);
                $texto  = $pdf->getText();
                if (strlen(trim($texto)) > 50) {
                    return mb_substr($texto, 0, 50000); // limite de 50k chars
                }
            }
        } catch (\Exception $e) {
            log_message('warning', "PdfAnalysisService: smalot falhou em {$caminhoAbsoluto}: " . $e->getMessage());
        }

        // Tentativa 2: pdftotext CLI (se disponível)
        $pdftotext = 'pdftotext';
        if (PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                'C:\\Program Files\\xpdf-tools\\bin64\\pdftotext.exe',
                'C:\\poppler\\bin\\pdftotext.exe',
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) { $pdftotext = '"' . $c . '"'; break; }
            }
        }

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ovp_pdf_' . uniqid() . '.txt';
        $cmd = $pdftotext . ' ' . escapeshellarg($caminhoAbsoluto) . ' ' . escapeshellarg($tmp) . ' 2>&1';
        exec($cmd, $saida, $ret);

        if ($ret === 0 && file_exists($tmp)) {
            $texto = file_get_contents($tmp);
            @unlink($tmp);
            return mb_substr($texto ?: '', 0, 50000);
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Análise heurística (sem IA externa)
    // -------------------------------------------------------------------------

    public function analisarHeuristico(string $texto, array $doc): array
    {
        $nome    = $doc['nome_arquivo'] ?? '';
        $textoBusca = mb_strtolower($texto . ' ' . $nome);

        $tipo    = $this->detectarTipo($textoBusca);
        $resumo  = $this->gerarResumoHeuristico($texto, $nome, $tipo);
        $campos  = $this->extrairCamposHeuristico($textoBusca, $doc);

        return [
            'tipo'   => $tipo,
            'resumo' => $resumo,
            'campos' => $campos,
        ];
    }

    private function detectarTipo(string $texto): string
    {
        // Indicadores de caso de violência policial
        $indicadoresCaso = [
            'pm ', 'polícia', 'policia', 'policial', 'tiro', 'bala', 'morto',
            'morreu', 'assassinado', 'execução', 'chacina', 'tortura', 'preso',
            'batalhão', 'batalhao', 'operação policial', 'abordagem',
        ];
        // Indicadores de estudo/análise
        $indicadoresEstudo = [
            'relatório', 'relatorio', 'pesquisa', 'estudo', 'análise', 'analise',
            'dados de', 'levantamento', 'metodologia', 'conclusão',
        ];

        $pontoCaso   = 0;
        $pontoEstudo = 0;

        foreach ($indicadoresCaso as $ind) {
            if (str_contains($texto, $ind)) $pontoCaso++;
        }
        foreach ($indicadoresEstudo as $ind) {
            if (str_contains($texto, $ind)) $pontoEstudo++;
        }

        if ($pontoCaso >= 2 && $pontoCaso > $pontoEstudo) return 'caso';
        if ($pontoEstudo >= 2 && $pontoEstudo > $pontoCaso) return 'estudo';
        if ($pontoCaso >= 1) return 'caso';
        return 'indefinido';
    }

    private function gerarResumoHeuristico(string $texto, string $nome, string $tipo): string
    {
        // Usa o nome do arquivo como base do resumo quando texto é escasso
        $nomeBase = pathinfo($nome, PATHINFO_FILENAME);
        $nomeBase = preg_replace('/^ID\.\d+(?:\.\d+)*\.\s*/i', '', $nomeBase);
        $nomeBase = preg_replace('/\d{1,2}[_\/]\d{1,2}[_\/]\d{4}/', '', $nomeBase);
        $nomeBase = trim($nomeBase, ' ._-');

        if (strlen($texto) > 200) {
            // Primeira frase substantiva do texto
            $frases = preg_split('/[.!?]\s+/', strip_tags($texto), 5);
            foreach ($frases as $frase) {
                $frase = trim($frase);
                if (strlen($frase) > 60) {
                    return mb_substr($frase, 0, 300) . '...';
                }
            }
        }

        return $nomeBase ?: "Documento sem texto extraível ({$tipo}).";
    }

    private function extrairCamposHeuristico(string $texto, array $doc): array
    {
        $campos = [
            'data_fato'          => $doc['data_documento'] ?? null,
            'municipio'          => null,
            'tipo_violencia'     => null,
            'vitimas_fatais'     => null,
            'vitimas_nao_fatais' => null,
        ];

        // Detectar tipo de violência
        foreach (self::TIPOS_VIOLENCIA as $palavra => $valor) {
            if (str_contains($texto, $palavra)) {
                $campos['tipo_violencia'] = $valor;
                break;
            }
        }

        // Detectar municípios comuns de SP
        $municipios = ['são paulo', 'sao paulo', 'guarulhos', 'campinas', 'santos', 'osasco', 'diadema'];
        foreach ($municipios as $mun) {
            if (str_contains($texto, $mun)) {
                $campos['municipio'] = ucwords($mun);
                break;
            }
        }

        // Vítimas fatais: "X mortos" ou "matou X"
        if (preg_match('/(\d+)\s+mortos?/i', $texto, $m)) {
            $campos['vitimas_fatais'] = (int)$m[1];
        }
        if (preg_match('/(\d+)\s+feridos?/i', $texto, $m)) {
            $campos['vitimas_nao_fatais'] = (int)$m[1];
        }

        return $campos;
    }

    // -------------------------------------------------------------------------
    // Análise via Gemini API
    // -------------------------------------------------------------------------

    public function analisarComGemini(string $texto, string $nomeArquivo, string $apiKey): array
    {
        $textoTruncado = mb_substr($texto, 0, 8000); // limite de tokens

        $prompt = <<<EOT
Você é um assistente do Observatório de Violências Policiais (OVP). Analise o documento abaixo e responda APENAS com um JSON válido, sem texto adicional, seguindo exatamente esta estrutura:

{
  "tipo": "caso" | "estudo" | "outro" | "indefinido",
  "resumo": "Resumo objetivo do documento em 2-3 frases",
  "campos": {
    "data_fato": "YYYY-MM-DD ou null",
    "municipio": "Nome do município ou null",
    "estado": "UF ou null",
    "tipo_violencia": "execucao|chacina|tortura|abuso_poder|morte_custodia|desaparecimento|ameaca ou null",
    "vitimas_fatais": número ou null,
    "vitimas_nao_fatais": número ou null,
    "descricao_livre": "Descrição do ocorrido em 1 parágrafo ou null"
  }
}

Nome do arquivo: {$nomeArquivo}

Conteúdo do documento:
{$textoTruncado}
EOT;

        $payload = json_encode([
            'contents' => [['parts' => [['text' => $prompt]]]],
        ]);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $resposta  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$resposta) {
            log_message('error', "PdfAnalysisService Gemini: HTTP {$httpCode}");
            return $this->analisarHeuristico($texto, ['nome_arquivo' => $nomeArquivo, 'data_documento' => null]);
        }

        try {
            $data   = json_decode($resposta, true, 512, JSON_THROW_ON_ERROR);
            $jsonStr = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            // Remove marcadores de bloco de código se presentes
            $jsonStr = preg_replace('/^```json\s*/i', '', trim($jsonStr));
            $jsonStr = preg_replace('/\s*```$/', '', $jsonStr);
            $parsed  = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);

            return [
                'tipo'   => $parsed['tipo']   ?? 'indefinido',
                'resumo' => $parsed['resumo']  ?? '',
                'campos' => $parsed['campos']  ?? [],
            ];
        } catch (\JsonException $e) {
            log_message('error', "PdfAnalysisService Gemini: JSON inválido — " . $e->getMessage());
            return $this->analisarHeuristico($texto, ['nome_arquivo' => $nomeArquivo, 'data_documento' => null]);
        }
    }
}
