# Status de continuidade — 5 de outubro de 2026

## Decisão tomada

A coleta da T011 terminou com uma resposta. A coordenação decidiu continuar porque P001 representa a pessoa de maior influência nas decisões do sistema.

O resultado é aceito como **validação de autoridade do projeto**, não como pesquisa representativa de usuários. A baseline de interface está aprovada com ajustes e a T011 foi concluída.

## Estado do backlog

- versão: 0.4.0;
- total: 190 tarefas;
- concluídas: 14;
- Checkpoint 0: concluído;
- próxima sequência: T015–T019.

## Ajustes obrigatórios herdados da validação

- linguagem orientada à tarefa e glossário contextual;
- contexto do caso visível durante o preenchimento;
- relações entre violações, vítimas, agentes e fontes verificáveis;
- alertas de fonte ausente e duplicidade;
- busca e filtro em listas longas;
- conferência geral antes do envio;
- bloqueios explícitos de publicação para dados pessoais, precisão territorial, direitos documentais, prévia e sustentação por fonte.

## Ressalva

Uma nova rodada de usabilidade deve ocorrer quando a ficha funcional estiver disponível e antes da publicação da Etapa 1. Ela verificará compreensão e eficiência, sem reabrir decisões institucionais salvo risco de privacidade ou impedimento operacional.

## Próxima ação

T012–T014 concluídas: dicionário físico, contratos dos Services e checklist bloqueador de privacidade/publicação estão versionados. A máquina de estados, a separação de responsabilidades e as condições de publicação estão fechadas para implementação.

### Pausa de 5 de outubro — T015–T019

A infraestrutura do banco automatizado foi preparada, mas a criação de `ovpdh_test` está bloqueada pelas credenciais locais do PostgreSQL:

- PostgreSQL 18 está ativo em `127.0.0.1:5432`;
- PHP possui `pgsql` e `pdo_pgsql`;
- o `.env` de desenvolvimento continua usando MySQL e não contém credenciais PostgreSQL;
- foram acrescentadas no `.env` local as chaves `database.tests.username` e `database.tests.password`, ainda vazias;
- `app/Config/Database.php` já possui o grupo PostgreSQL `tests` apontando para `ovpdh_test`;
- `quickstart.md`, `AuthTestHelper`, `PostgresTestCase` e `PostgresHealthTest` foram criados;
- lint PHP e testes unitários passaram;
- o teste de saúde falha apenas com `no password supplied`, como esperado enquanto as credenciais estiverem vazias.

**Primeiro lembrete da próxima sessão**: preencher no `.env` local `database.tests.username` e `database.tests.password` com as credenciais do PostgreSQL. A senha não deve ser enviada pelo chat nem versionada.

Depois disso: criar `ovpdh_test`, habilitar PostGIS, executar o teste de saúde e a suíte completa, marcar T015–T019 e versionar a entrega.

Na sequência:

1. T015 — grupo PostgreSQL de teste sem credenciais versionadas;
2. T016 — quickstart do banco PostgreSQL/PostGIS de teste;
3. T017–T018 — helpers de autenticação e limpeza transacional;
4. T019 — teste de saúde PostgreSQL/PostGIS.

## Gancho de retomada

> Retome o status de 5 de outubro. Primeiro lembre que faltam `database.tests.username` e `database.tests.password` no `.env`; depois crie `ovpdh_test`, habilite PostGIS e conclua T015–T019.

