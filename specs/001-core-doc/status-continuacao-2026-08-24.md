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

Fazer o inventário efetivo do legado no VPS e preparar o mapa de transformação para a estrutura nova. A ordem proposta é:

1. Listar schemas, tabelas e contagens das tabelas de origem.
2. Identificar chaves, datas, fontes, vítimas, autores, violações e coordenadas disponíveis.
3. Definir o mapeamento para `ocorrencias`, `vitimas`, `agressores`, `violacoes`, tabelas de relação e `ocorrencia_geometrias`.
4. Implementar um comando CLI de importação com `--dry-run`, logs e registros em `ocorrencia_origens_legado`.
5. Executar a simulação, conferir totais e validar uma amostra antes da carga definitiva.

## Primeiro comando sugerido para retomar

Este comando é somente leitura e mostra onde estão as tabelas no banco:

```bash
sudo -u postgres psql -d observatorio -c "SELECT table_schema, COUNT(*) AS tabelas FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema NOT IN ('pg_catalog', 'information_schema') GROUP BY table_schema ORDER BY table_schema;"
```

Em seguida, listar as tabelas do schema que contiver o legado, para elaborar o mapeamento sem suposições.
