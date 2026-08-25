# Status de continuidade — 24 de agosto de 2026

## Objetivo atual

Preparar o OVPDH para operar sobre PostgreSQL com PostGIS, preservando o legado e deixando a importação para uma etapa controlada e auditável.

## Concluído

- Especificações funcionais, MER, plano de migração e relatórios HTML foram produzidos em `specs/001-core-doc/` e `public/`.
- O aplicativo foi preparado para PostgreSQL no arquivo `app/Config/Database.php`:
  - driver `Postgre`;
  - charset `UTF8`;
  - porta `5432`;
  - schema `public`.
- A migration-base `2024-01-01-000001_CreateOvpdhTables` foi tornada compatível com PostgreSQL: os campos `status` e `prioridade` usam `VARCHAR`, não `ENUM`.
- Foi criada a migration `2026-08-24-000002_CreateProjectedOvpdhTables`, com:
  - catálogos e relações normalizadas de violações, condições, meios e lesões;
  - rastreabilidade de origem e de mapeamentos do legado;
  - tabela espacial `ocorrencia_geometrias`, com `geometry(Point, 4326)` e índice GIST;
  - verificação de PostgreSQL e PostGIS antes da execução.
- Ajustes foram enviados ao repositório nos commits `c29b4ca` e `5cc5660`.

## Estado confirmado no VPS

- PostgreSQL e PostGIS estão instalados; a extensão `postgis` do banco `observatorio` reportou versão `3.6.4`.
- As migrations foram aplicadas com sucesso:
  - `App / CreateOvpdhTables` — lote 1;
  - `App / CreateProjectedOvpdhTables` — lote 2;
  - `CodeIgniter Shield / CreateAuthTables` — lote 3;
  - `CodeIgniter Settings / CreateSettingsTable` e `AddContextColumn` — lote 3.
- A tabela geográfica `public.ocorrencia_geometrias` existe e o PostGIS respondeu corretamente.
- Antes da aplicação, foi verificado que os nomes das novas tabelas não colidiam com as tabelas legadas consultadas.

## Cuidados preservados

- Não executar novamente `php spark migrate` ou `php spark migrate --all` como etapa de importação: as migrations já estão registradas.
- A importação do legado deve ser um comando próprio, idempotente e com modo de simulação; não deve ser uma migration automática.
- Não incluir o arquivo `.env`, senhas, dumps ou o diretório `bancos/` no Git.
- Há arquivos operacionais modificados em `writable/` no VPS; são esperados e não devem ser versionados.
- Antes da importação, confirmar a existência e a guarda de um dump recente do banco de produção.

## Próxima etapa

O inventário estrutural e quantitativo do backup foi concluído e o modelo lógico de toda a aplicação foi consolidado em `arquitetura-dados.md` e `mer-projetado.md`. A ordem proposta passa a ser:

O plano técnico da Etapa 1 está em `plan.md` e o backlog granular versionado está em `tasks.md`.

1. Homologar o mapa de status legado, a finalidade dos atributos sensíveis e os vocabulários ainda pendentes. A política de visibilidade e o tratamento das denúncias no MVP já foram definidos.
2. Comparar os esquemas `public` e `old` com acesso autenticado de somente leitura ao PostgreSQL local.
3. Produzir o mapa físico campo a campo origem → destino.
4. Criar migrations complementares para relações N:N, depoimentos, denúncias, catálogos, auditoria e rastreabilidade por entidade.
5. Implementar um comando CLI de importação com `--dry-run`, lotes, logs e conciliação de contagens.
6. Projetar os wireframes das sete seções validadas do cadastro e da curadoria.

## Relatórios HTML para acompanhamento

O ponto de entrada para leitores não técnicos é `public/relatorios.html`. A partir dele estão disponíveis os relatórios atuais:

- `public/relatorio-01-especificacoes-etapa-1.html` — visão geral do que será construído;
- `public/relatorio-06-arquitetura-dados.html` — organização e proteção das informações;
- `public/relatorio-07-plano-etapa-1.html` — módulos e ordem da primeira entrega;
- `public/relatorio-08-backlog-etapa-1.html` — resumo das 190 tarefas e suas frentes de trabalho.

No VPS, os mesmos documentos serão acessíveis pelos caminhos `/relatorios.html` e `/relatorio-*.html` depois da atualização da cópia de desenvolvimento.

## Estado do ambiente local de desenvolvimento

- O serviço PostgreSQL 18 está instalado e em execução, mas a sessão de desenvolvimento não possui credencial para consultar a instância.
- O backup custom `bancos/pcvi_backup_260821` pôde ser inspecionado diretamente e confirmou 48 tabelas em `public`, 3.685 ocorrências e as relações registradas no inventário.
- A aplicação local ainda lê o `.env` MySQL e encontra o banco demonstrativo `ovpdh`, com 12 ocorrências.
- A troca do `.env` local para PostgreSQL deve ser feita separadamente, após disponibilizar a credencial e definir o destino dos dados demonstrativos.

## Primeiro passo sugerido para retomar

Disponibilizar uma conta PostgreSQL de somente leitura para o banco restaurado. Depois, executar a comparação de `public` e `old` e gerar o mapa de transformação; não é necessário aplicar novamente as migrations já registradas.
