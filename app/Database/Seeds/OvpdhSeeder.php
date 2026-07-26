<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OvpdhSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedHistorico();
        $this->seedProdutos();
        $this->seedOcorrencias();
    }

    private function seedUsers(): void
    {
        $users = auth()->getProvider();

        $usersData = [
            ['email' => 'admin@ovpdh.pucsp.br',       'username' => 'admin',       'password' => 'Admin@2024!',   'group' => 'admin',        'name' => 'Administrador OVPDH'],
            ['email' => 'superadmin@ovpdh.pucsp.br',  'username' => 'superadmin',  'password' => 'Super@2024!',   'group' => 'superadmin',   'name' => 'Super Administrador'],
            ['email' => 'voluntario@ovpdh.pucsp.br',  'username' => 'voluntario1', 'password' => 'Voluntario@24', 'group' => 'voluntario',   'name' => 'Maria das Graças Silva'],
            ['email' => 'colaborador@ovpdh.pucsp.br', 'username' => 'colaborador1','password' => 'Colab@2024!',   'group' => 'colaborador',  'name' => 'João Paulo Ferreira'],
            ['email' => 'advogado@ovpdh.pucsp.br',    'username' => 'advogado1',   'password' => 'Adv@2024!',     'group' => 'advogado',     'name' => 'Dr. Carlos Eduardo Mendes'],
            ['email' => 'academico@ovpdh.pucsp.br',   'username' => 'academico1',  'password' => 'Acad@2024!',    'group' => 'academico',    'name' => 'Profa. Dra. Ana Lúcia Borges'],
            ['email' => 'ativista@ovpdh.pucsp.br',    'username' => 'ativista1',   'password' => 'Ativi@2024!',   'group' => 'ativista',     'name' => 'Pedro Henrique Costa'],
        ];

        foreach ($usersData as $ud) {
            $existing = $users->where('username', $ud['username'])->first();
            if ($existing) {
                continue;
            }

            $user = new \CodeIgniter\Shield\Entities\User([
                'username' => $ud['username'],
                'email'    => $ud['email'],
                'password' => $ud['password'],
                'active'   => 1,
            ]);
            $users->save($user);
            $savedUser = $users->findById($users->getInsertID());
            $savedUser->addGroup($ud['group']);

            // Salvar nome no user_identities ou meta
            $this->db->table('users')->where('id', $savedUser->id)->update(['last_active' => date('Y-m-d H:i:s')]);
        }
    }

    private function seedHistorico(): void
    {
        $historicos = [
            [
                'titulo'      => 'Relatório de Violência Policial em Minas Gerais — 1964-1968',
                'descricao'   => 'Compilação de casos documentados pela Profa. Dra. Helena Ferreira Campos durante os primeiros anos da ditadura militar, com depoimentos de sobreviventes e familiares de vítimas da repressão estatal em Minas Gerais.',
                'periodo'     => '1964 - 1968',
                'ano_inicio'  => 1964,
                'ano_fim'     => 1968,
                'categoria'   => 'Ditadura Militar',
                'arquivo_pdf' => null,
                'autora'      => 'Profa. Dra. Helena Ferreira Campos',
                'ativo'       => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Dossiê dos Desaparecidos Políticos de Belo Horizonte',
                'descricao'   => 'Documentação histórica sobre os casos de desaparecimento político ocorridos na capital mineira entre 1969 e 1975. Inclui fichas de investigação, cartas de familiares e correspondências com organismos internacionais de direitos humanos.',
                'periodo'     => '1969 - 1975',
                'ano_inicio'  => 1969,
                'ano_fim'     => 1975,
                'categoria'   => 'Desaparecidos Políticos',
                'arquivo_pdf' => null,
                'autora'      => 'Profa. Dra. Helena Ferreira Campos',
                'ativo'       => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'A Repressão nas Favelas — BH Anos 70',
                'descricao'   => 'Estudo sobre as operações policiais de remoção forçada e repressão nas comunidades periféricas de Belo Horizonte durante a década de 1970, com foco na Vila Operária e no Aglomerado da Serra.',
                'periodo'     => '1970 - 1979',
                'ano_inicio'  => 1970,
                'ano_fim'     => 1979,
                'categoria'   => 'Violência Urbana',
                'arquivo_pdf' => null,
                'autora'      => 'Profa. Dra. Helena Ferreira Campos',
                'ativo'       => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Memórias da Resistência — Mulheres e a Ditadura em MG',
                'descricao'   => 'Coletânea de relatos e documentos sobre a resistência feminina à ditadura militar em Minas Gerais. Inclui depoimentos de 42 mulheres que sofreram perseguição, prisão arbitrária e tortura entre 1964 e 1985.',
                'periodo'     => '1964 - 1985',
                'ano_inicio'  => 1964,
                'ano_fim'     => 1985,
                'categoria'   => 'Gênero e Ditadura',
                'arquivo_pdf' => null,
                'autora'      => 'Profa. Dra. Helena Ferreira Campos',
                'ativo'       => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Violência Policial na Redemocratização — 1985-1995',
                'descricao'   => 'Análise comparativa das práticas de violência policial no período de transição democrática, demonstrando a persistência de padrões autoritários nas forças de segurança mineiras.',
                'periodo'     => '1985 - 1995',
                'ano_inicio'  => 1985,
                'ano_fim'     => 1995,
                'categoria'   => 'Redemocratização',
                'arquivo_pdf' => null,
                'autora'      => 'Profa. Dra. Helena Ferreira Campos',
                'ativo'       => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('historico')->insertBatch($historicos);
    }

    private function seedProdutos(): void
    {
        $produtos = [
            [
                'titulo'        => 'Letalidade Policial em Minas Gerais: Uma análise das ocorrências de 2018-2022',
                'autores'       => 'BORGES, A. L.; FERREIRA, J. P.; SANTOS, R. M.',
                'tipo'          => 'Artigo Científico',
                'resumo'        => 'Este artigo analisa os dados de letalidade policial registrados em Minas Gerais entre 2018 e 2022, com foco na distribuição geográfica, perfil racial das vítimas e correlação com indicadores socioeconômicos. Os resultados apontam para uma concentração desproporcional de mortes em comunidades negras e periféricas.',
                'ano'           => 2023,
                'publicacao'    => 'Revista Brasileira de Segurança Pública',
                'doi'           => '10.31060/rbsp.2023.v17.n2.1847',
                'link_externo'  => '#',
                'palavras_chave'=> 'letalidade policial; direitos humanos; raça; Minas Gerais',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'        => 'Racismo Institucional e Violência Policial: Perspectivas a partir do OVPDH-PUC',
                'autores'       => 'CAMPOS, H. F.; BORGES, A. L.',
                'tipo'          => 'Livro/Capítulo',
                'resumo'        => 'Obra que reúne reflexões teóricas e empíricas sobre o racismo estrutural e institucional como determinante das práticas de violência policial no Brasil, com dados coletados e sistematizados pelo Observatório de Violência Policial e Direitos Humanos ao longo de uma década.',
                'ano'           => 2022,
                'publicacao'    => 'Editora PUC São Paulo',
                'doi'           => null,
                'link_externo'  => '#',
                'palavras_chave'=> 'racismo institucional; violência policial; direitos humanos; PUC São Paulo',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'        => 'Mapeamento de Ocorrências de Abuso de Autoridade pela PMMG: 2019-2021',
                'autores'       => 'MENDES, C. E.; SILVA, M. G.; COSTA, P. H.',
                'tipo'          => 'Relatório de Pesquisa',
                'resumo'        => 'Relatório técnico contendo o mapeamento sistemático de ocorrências de abuso de autoridade praticadas por agentes da Polícia Militar de Minas Gerais no período 2019-2021, com análise jurídica das responsabilizações e impunidade.',
                'ano'           => 2022,
                'publicacao'    => 'OVPDH — PUC São Paulo',
                'doi'           => null,
                'link_externo'  => '#',
                'palavras_chave'=> 'abuso de autoridade; PMMG; impunidade; direitos humanos',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'        => 'Juventude Negra e Violência Policial: Dados do OVPDH 2020-2023',
                'autores'       => 'BORGES, A. L.; FERREIRA, J. P.',
                'tipo'          => 'Artigo Científico',
                'resumo'        => 'Análise dos dados do Observatório sobre violência policial contra jovens negros no período pandêmico e pós-pandêmico, demonstrando o agravamento das condições de vulnerabilidade e o aprofundamento do genocídio da juventude negra no estado.',
                'ano'           => 2024,
                'publicacao'    => 'Cadernos de Saúde Pública',
                'doi'           => '10.1590/0102-311XEN025624',
                'link_externo'  => '#',
                'palavras_chave'=> 'juventude negra; violência policial; genocídio; pandemia; Minas Gerais',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'        => 'Violência Obstétrica e Agentes do Estado: Uma análise interseccional',
                'autores'       => 'SILVA, M. G.; BORGES, A. L.',
                'tipo'          => 'Dissertação de Mestrado',
                'resumo'        => 'Dissertação que examina as interseções entre violência obstétrica e violência do Estado, com foco no sistema público de saúde de Belo Horizonte, investigando casos onde agentes públicos estão diretamente envolvidos em práticas violentas contra mulheres parturientes.',
                'ano'           => 2023,
                'publicacao'    => 'PUC São Paulo — PPGCS',
                'doi'           => null,
                'link_externo'  => '#',
                'palavras_chave'=> 'violência obstétrica; violência do estado; interseccionalidade; gênero; raça',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'        => 'Boletim OVPDH #12 — Semestral 2024',
                'autores'       => 'Equipe OVPDH — PUC São Paulo',
                'tipo'          => 'Boletim Informativo',
                'resumo'        => 'Décima segunda edição do Boletim semestral do Observatório, com síntese dos casos registrados, análise de tendências, casos em destaque e agenda de direitos humanos. Período: Janeiro–Junho 2024.',
                'ano'           => 2024,
                'publicacao'    => 'OVPDH — PUC São Paulo',
                'doi'           => null,
                'link_externo'  => '#',
                'palavras_chave'=> 'boletim; violência policial; direitos humanos; 2024',
                'ativo'         => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('produtos')->insertBatch($produtos);
    }

    private function seedOcorrencias(): void
    {
        $tipos = ['Homicídio', 'Lesão Corporal', 'Abuso de Autoridade', 'Tortura', 'Prisão Arbitrária', 'Violência Sexual', 'Execução Extrajudicial'];
        $status_list = ['rascunho', 'em_revisao', 'aprovado', 'publicado'];
        $cidades = ['Belo Horizonte', 'Contagem', 'Betim', 'Ribeirão das Neves', 'Santa Luzia', 'Vespasiano'];
        $bairros_bh = ['Aglomerado da Serra', 'Morro das Pedras', 'Barreiro', 'Vila Pinho', 'Cabana do Pai Tomás', 'Favela do Alemão', 'Conjunto Flamengo', 'Pedreira Prado Lopes', 'Ribeiro de Abreu', 'Taquaril'];
        $orgaos = ['Polícia Militar de MG', 'Polícia Civil de MG', 'Guarda Municipal de BH', 'Batalhão de Choque PMMG', 'ROTAM — PMMG', 'Força Tática PMMG'];

        $ocorrencias = [];
        $vitimas     = [];
        $agressores  = [];

        $casos = [
            ['titulo' => 'Jovem baleado durante abordagem no Aglomerado da Serra', 'tipo' => 'Homicídio', 'bairro' => 'Aglomerado da Serra', 'status' => 'publicado', 'descricao' => 'Jovem de 22 anos foi alvejado por disparos de arma de fogo durante operação policial no Aglomerado da Serra. Segundo testemunhas, a vítima estava desarmada e tentava fugir quando foi atingida com três tiros pelas costas. A PM afirma que houve confronto, versão contestada por moradores e familiares.'],
            ['titulo' => 'Operação policial resulta em 4 mortes no Morro das Pedras', 'tipo' => 'Execução Extrajudicial', 'bairro' => 'Morro das Pedras', 'status' => 'publicado', 'descricao' => 'Durante operação realizada pela ROTAM na comunidade do Morro das Pedras, quatro homens foram mortos. Laudos do IML indicam disparos a curta distância em pelo menos dois dos corpos, levantando suspeitas de execução sumária.'],
            ['titulo' => 'Moradora relata tortura em delegacia após prisão arbitrária', 'tipo' => 'Tortura', 'bairro' => 'Cabana do Pai Tomás', 'status' => 'aprovado', 'descricao' => 'Mulher de 34 anos foi detida sem mandado judicial e submetida a sessões de tortura psicológica e física na delegacia do Barreiro. O caso foi encaminhado ao Ministério Público após denúncia do Coletivo de Defesa dos Direitos Humanos.'],
            ['titulo' => 'Guarda municipal espanca vendedor ambulante no centro de BH', 'tipo' => 'Lesão Corporal', 'bairro' => 'Centro', 'status' => 'publicado', 'descricao' => 'Imagens de câmeras de segurança registraram guardas municipais agredindo violentamente um vendedor ambulante de 45 anos durante ação de fiscalização no Hipercentro de BH. A vítima foi hospitalizada com costelas fraturadas.'],
            ['titulo' => 'Adolescente de 16 anos detido ilegalmente por 48 horas', 'tipo' => 'Prisão Arbitrária', 'bairro' => 'Ribeiro de Abreu', 'status' => 'em_revisao', 'descricao' => 'Adolescente foi detido sem comunicação ao Ministério Público ou à família por mais de 48 horas, em clara violação ao ECA e às garantias constitucionais. O caso está sendo apurado pela Corregedoria da PM.'],
            ['titulo' => 'Homem morre após ação do BOPE em Contagem', 'tipo' => 'Homicídio', 'bairro' => 'Ressaca', 'status' => 'publicado', 'descricao' => 'Morador de Contagem foi morto durante operação policial realizada pelo Batalhão de Operações Especiais. Familiares afirmam que a vítima foi retirada de casa ainda com vida e encontrada morta horas depois em área de mata.'],
            ['titulo' => 'Violência sexual durante revista íntima em delegacia de BH', 'tipo' => 'Violência Sexual', 'bairro' => 'Floresta', 'status' => 'aprovado', 'descricao' => 'Três mulheres denunciam terem sido submetidas a revista íntima vexatória e constrangimento de natureza sexual por agentes policiais durante detenção em delegacia da Zona Leste de BH.'],
            ['titulo' => 'PM atira em trabalhador que retornava do trabalho noturno', 'tipo' => 'Lesão Corporal', 'bairro' => 'Taquaril', 'status' => 'publicado', 'descricao' => 'Trabalhador de 38 anos levou tiro na perna ao ser confundido com suspeito durante abordagem policial no bairro Taquaril. Ele estava com o uniforme de trabalho e a carteira de identificação funcional.'],
            ['titulo' => 'Denúncia de abuso de autoridade em blitz na BR-040', 'tipo' => 'Abuso de Autoridade', 'bairro' => 'Chácaras', 'status' => 'rascunho', 'descricao' => 'Motorista denuncia ter sofrido ameaças e coação por parte de policiais rodoviários durante blitz na BR-040, nas proximidades de BH. Segundo o denunciante, os agentes exigiram propina para liberar o veículo.'],
            ['titulo' => 'Criança de 8 anos baleada durante operação policial em favela', 'tipo' => 'Homicídio', 'bairro' => 'Vila Pinho', 'status' => 'publicado', 'descricao' => 'Uma criança de 8 anos foi atingida por um projétil perdido durante operação da Polícia Militar no Aglomerado Vila Pinho. A criança não resistiu aos ferimentos e foi a óbito no Hospital João XXIII. O caso gerou comoção na comunidade.'],
            ['titulo' => 'Acampamento de sem-teto destruído em ação violenta da PM', 'tipo' => 'Abuso de Autoridade', 'bairro' => 'Barreiro', 'status' => 'em_revisao', 'descricao' => 'Acampamento com 47 famílias sem-teto foi destruído por policiais militares em cumprimento a reintegração de posse, com uso desproporcional de força e gás lacrimogênio contra famílias com crianças e idosos.'],
            ['titulo' => 'Idoso espancado durante operação policial em Betim', 'tipo' => 'Lesão Corporal', 'bairro' => 'Citrolândia', 'status' => 'aprovado', 'descricao' => 'Homem de 67 anos foi agredido fisicamente por policiais militares durante operação de saturação no bairro Citrolândia, em Betim. A vítima alega ter sido confundida com suspeito e não recebeu assistência médica imediata.'],
        ];

        foreach ($casos as $i => $caso) {
            $data = date('Y-m-d', strtotime('-' . rand(10, 730) . ' days'));
            $cidade = in_array($caso['bairro'], ['Ressaca', 'Citrolândia']) ? ($caso['bairro'] === 'Ressaca' ? 'Contagem' : 'Betim') : 'Belo Horizonte';

            $ocorrencias[] = [
                'titulo'          => $caso['titulo'],
                'descricao'       => $caso['descricao'],
                'data_ocorrencia' => $data,
                'tipo_violencia'  => $caso['tipo'],
                'local_descricao' => 'Via pública / ' . $caso['bairro'],
                'bairro'          => $caso['bairro'],
                'cidade'          => $cidade,
                'estado'          => 'MG',
                'status'          => $caso['status'],
                'prioridade'      => $i < 3 ? 'urgente' : 'normal',
                'user_id'         => 3,
                'created_at'      => date('Y-m-d H:i:s', strtotime($data)),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('ocorrencias')->insertBatch($ocorrencias);

        // Vítimas
        $nomesVitimas = ['João Silva', 'Pedro Oliveira', 'Carlos Souza', 'Ana Maria', 'Marcos Pereira', 'Lucas Santos', 'Fernanda Costa', 'Roberto Lima', 'José Alves', 'Antônia Rocha', 'Criança Não Identificada', 'Rafael Cardoso'];
        $racas = ['Preta', 'Parda', 'Parda', 'Parda', 'Preta', 'Preta', 'Parda', 'Preta', 'Parda', 'Preta', 'Preta', 'Preta'];
        $generos = ['Masculino', 'Masculino', 'Masculino', 'Feminino', 'Masculino', 'Masculino', 'Feminino', 'Masculino', 'Masculino', 'Feminino', 'Masculino', 'Masculino'];
        $idades = [22, 25, 31, 34, 19, 28, 30, 38, 16, 42, 8, 67];
        $desfechos = ['Óbito', 'Óbito', 'Sobrevivente — em tratamento', 'Sobrevivente — em tratamento', 'Liberado', 'Óbito', 'Sobrevivente', 'Sobrevivente', 'Liberado', 'Sobrevivente', 'Óbito', 'Hospitalizado'];

        for ($i = 1; $i <= 12; $i++) {
            $vitimas[] = [
                'ocorrencia_id'   => $i,
                'nome'            => $nomesVitimas[$i-1],
                'anonimo'         => 0,
                'idade'           => $idades[$i-1],
                'genero'          => $generos[$i-1],
                'raca_etnia'      => $racas[$i-1],
                'condicao_social' => 'Baixa renda',
                'desfecho'        => $desfechos[$i-1],
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('vitimas')->insertBatch($vitimas);

        // Agressores
        for ($i = 1; $i <= 12; $i++) {
            $agressores[] = [
                'ocorrencia_id' => $i,
                'tipo_agente'   => $i % 3 === 0 ? 'Policial Civil' : 'Policial Militar',
                'orgao'         => $orgaos[array_rand($orgaos)],
                'identificado'  => $i % 4 === 0 ? 1 : 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('agressores')->insertBatch($agressores);

        // Revisões
        $revisoes = [];
        for ($i = 1; $i <= 6; $i++) {
            $revisoes[] = [
                'ocorrencia_id'   => $i,
                'user_id'         => 4,
                'acao'            => 'aprovado',
                'status_anterior' => 'em_revisao',
                'status_novo'     => 'aprovado',
                'comentario'      => 'Caso verificado e fontes confirmadas.',
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('ocorrencia_revisoes')->insertBatch($revisoes);
    }
}
