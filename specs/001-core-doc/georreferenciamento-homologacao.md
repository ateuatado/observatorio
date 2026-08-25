# Georreferenciamento em Homologação — OVPDH

**Documento**: 05  
**Data**: 23 de agosto de 2026  
**Status**: PostGIS habilitado e acervo espacial restaurado

## Resultado

O PostGIS 3.6 foi ativado no banco PostgreSQL de homologação `observatorio`. Os objetos espaciais do backup foram restaurados, sem modificar o arquivo de origem.

| Item | Resultado |
| --- | --- |
| Extensão | PostGIS 3.6 com GEOS, PROJ e estatísticas espaciais |
| Tabela espacial principal | `public.ovpdh_gis` |
| Geometrias carregadas | 937 |
| Tipo de geometria | `POINT` |
| Referência espacial | SRID 4326 — WGS 84 (latitude/longitude) |
| Views no esquema `analitico` | 153 |

## Modelo espacial legado

| Coluna | Tipo | Papel |
| --- | --- | --- |
| `id_id` | `bigint` | Identificador da ocorrência vinculada. |
| `geolocalizacao` | `geometry` | Ponto espacial usado em mapas e análises territoriais. |
| `latitude` | `double precision` | Latitude registrada no legado. |
| `longitude` | `double precision` | Longitude registrada no legado. |

## Regras para a nova aplicação

- Geometrias devem ser mantidas no banco como `geometry(Point, 4326)` ou `geography(Point, 4326)`, com decisão registrada antes da migration.
- Latitude e longitude devem ser validadas em conjunto com a geometria; a geometria será a fonte de verdade espacial.
- Coordenadas exatas não devem ser exibidas publicamente quando puderem identificar vítimas, testemunhas ou locais sensíveis.
- A área pública deve aplicar generalização territorial quando necessário, como bairro, município, grade ou ponto aproximado.
- Mapas internos devem respeitar o mesmo controle de acesso dos dados da ocorrência.
- Toda importação espacial deve manter vínculo com o ID de origem e registrar inconsistências de coordenadas em relatório próprio.

## Próxima etapa

Validar os 937 pontos: SRID, limites geográficos, duplicidades, coordenadas fora do território esperado e precisão adequada à política de proteção de dados. O MER já inclui local e geometria; antes das telas de mapa ainda é necessário homologar os níveis `exata`, `aproximada`, `bairro` e `município` e definir qual deles pode ser público em cada classe de ocorrência.
