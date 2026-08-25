# Inventário do Legado PostgreSQL — OVPDH

**Documento**: 04

**Atualizado em**: 24 de agosto de 2026
**Status**: Backup e homologação inventariados; modelo lógico extraído

## Escopo da restauração

O backup `pcvi_backup_260821` foi restaurado anteriormente no PostgreSQL de homologação `observatorio`. Em 24 de agosto, o arquivo custom `PGDMP` também foi inspecionado diretamente com as ferramentas PostgreSQL 18, sem restauração adicional e sem alteração do conteúdo. Essa segunda leitura confirmou tabelas, colunas e contagens abaixo.

| Item | Resultado |
| --- | --- |
| Banco de homologação | `observatorio` (PostgreSQL, UTF8) |
| Esquemas restaurados | `public`, `old` e `analitico` |
| Tabelas em `public` | 48 |
| Tabelas em `old` | 48 |
| Views no esquema `analitico` presentes no backup | 153 |
| Chaves estrangeiras em `public` | 65 |
| Tamanho após restauração | 29 MB |

O esquema `old` deve ser tratado como referência histórica. Antes de qualquer importação na aplicação nova, é preciso comparar `old` e `public` para confirmar se representam cópias equivalentes, versões distintas ou conjunto parcialmente sobreposto. A carga para o novo sistema não deve importar os dois esquemas sem essa decisão, pois pode duplicar dados.

## Volumes principais

| Domínio | Registros no esquema `public` |
| --- | ---: |
| Ocorrências | 3.685 |
| Violações | 4.656 |
| Vítimas | 5.093 |
| Autores institucionais | 4.280 |
| Fontes | 5.534 |
| Depoimentos | 1.397 |
| Denúncias recebidas | 56 |
| Relações vítima–violação | 5.493 |
| Relações autor–violação | 5.070 |
| Relações fonte–violação | 3.651 |
| Relações violação–meio/instrumento | 4.795 |
| Relações violação–lesão/região do corpo | 1.308 |

O volume confirma que o modelo é relacional e não deve ser transformado em uma única tabela de ocorrências. Uma ocorrência pode conter várias violações, vítimas, autores e fontes; essas relações devem ser preservadas na evolução do novo banco.

## Estrutura dos principais registros

| Tabela legado | Campos de domínio confirmados |
| --- | --- |
| `ovpdh_ocorrencia` | data, importância, observação, observação de curadoria, período do dia, status, endereço, município, referência imprecisa e autorias/datas de inclusão e alteração |
| `ovpdh_violacao` | ocorrência, tipo, conduta, enquadramento jurídico, justificativa, observação e autorias/datas |
| `ovpdh_vitima` | ocorrência, tipo de entidade, nome, idade, raça/cor, gênero, escolaridade, habitação, nacionalidade, ocupação, orientação sexual, religião/credo, deficiência, tutela estatal e observação |
| `ovpdh_autor` | ocorrência, classe/tipo institucional, cargo/função, nome sigiloso, observação e autorias/datas |
| `ovpdh_fonte` | ocorrência, data, título, acesso, tipo, audiovisual, indicação de denúncia, observação e autorias/datas |
| `ovpdh_depoimento` | fonte, tipo de depoente, nome, texto, observação e autorias/datas |
| `ovpdh_denuncia` | narrativa, data/local, autor informado, denunciante/contato, pedidos de apoio jurídico, psicológico e assistencial, status e observações de curadoria |
| `ovpdh_gis` | ocorrência, ponto PostGIS 4326, latitude e longitude |

O cadastro-alvo precisa preservar também as relações vítima–violação, autor–violação e fonte–violação. A migration projetada em 24 de agosto ainda não contém as três tabelas de relação nem o domínio de denúncias/depoimentos.

## Catálogos confirmados

| Grupo | Catálogo | Linhas |
| --- | --- | ---: |
| Território | UFs / municípios | 28 / 5.571 |
| Ocorrência | status / período do dia | 6 / 4 |
| Violação | tipos / condutas / meios / regiões-lesões | 28 / 17 / 17 / 7 |
| Jurídico | classes / tipos | 26 / 111 |
| Vítima | tipos de entidade / raça-cor / gênero / escolaridade | 5 / 7 / 7 / 5 |
| Vítima | habitação / nacionalidade / ocupação / orientação sexual / religião | 5 / 7 / 49 / 6 / 12 |
| Agente | classes / tipos institucionais | 19 / 45 |
| Fonte | tipos de fonte / audiovisual / depoente | 262 / 4 / 23 |

Os valores `Dado ausente (bd antigo)`, `Outra` e categorias históricas devem ser preservados como valores de origem. Eles não devem ser fundidos automaticamente com `não informado`.

## Fluxo legado de status

| ID legado | Status legado | Ocorrências |
| --- | --- | ---: |
| 1 | Incluído | 300 |
| 2 | Curadoria | 1.758 |
| 3 | Revisar | 379 |
| 4 | Aprovado | 1.016 |
| 5 | Cancelado | 228 |
| 6 | Teste | 4 |

### Mapeamento proposto para validação humana

| Status legado | Possível status novo | Decisão necessária |
| --- | --- | --- |
| Incluído | `rascunho` | Confirmar se há registros que já deveriam estar em revisão. |
| Curadoria | `em_revisao` | Manter fora do acesso público. |
| Revisar | `rejeitado` ou `rascunho` | Definir se a justificativa histórica é suficiente para caracterizar rejeição. |
| Aprovado | `aprovado` | Decidir se algum subconjunto pode ser convertido para `publicado`; nunca publicar automaticamente. |
| Cancelado | `arquivado` | Preservar fora das consultas correntes. |
| Teste | `arquivado` ou exclusão lógica | Validar amostra antes de qualquer ação. |

Nenhum desses mapeamentos deve ser aplicado automaticamente sem homologação da curadoria.

## Indicadores de qualidade e cobertura

- O catálogo possui 28 UFs e 5.571 municípios.
- Há 52,9% das violações classificadas como `Dado ausente (bd antigo)`.
- As violações mais frequentes com classificação informada incluem morte (873) e agressão física/lesão corporal (788).
- O acervo possui fontes de imprensa diversas; entre as mais recorrentes estão G1 (1.151), AAA (600), Folha de São Paulo (442) e Estadão (292).

O valor `Dado ausente (bd antigo)` deve ser mantido como origem histórica e incorporado a uma fila de qualificação posterior. Ele não pode ser apagado, inferido automaticamente ou tratado como ausência de ocorrência.

## Relações que precisam ser preservadas

1. Ocorrência → violações (`ovpdh_ocorrencia` → `ovpdh_violacao`).
2. Ocorrência → vítimas e vítimas ↔ violações.
3. Ocorrência → autores institucionais e autores ↔ violações.
4. Ocorrência → fontes, fontes ↔ violações e fontes → depoimentos.
5. Violação ↔ meios/instrumentos e violação ↔ lesões/parte do corpo.
6. Ocorrência → localidade, UF e período do dia.
7. Ocorrência, violação, fonte e vítima → autoria de inclusão e alteração.
8. Denúncia → contato restrito, tipo de denunciante, apoio solicitado e eventual ocorrência originada.

## Georreferenciamento habilitado

A tabela `public.ovpdh_gis` contém 937 geometrias do tipo `POINT`, com SRID 4326 (latitude/longitude em WGS 84), vinculadas a ocorrências por `id_id`. As 153 views analíticas do esquema `analitico` estão disponíveis para estudo.

O esquema `old` não possui geometrias carregadas. A validação da cobertura territorial e da qualidade das coordenadas deve ocorrer antes de qualquer mapa público.

## Próximas ações recomendadas

1. Comparar contagens, chaves e alterações entre os esquemas `public` e `old`.
2. Validar a cobertura territorial, o SRID e a qualidade das coordenadas.
3. Homologar catálogos e política de dados sensíveis com a curadoria.
4. Validar o mapa de status e o tratamento das 56 denúncias legadas.
5. Produzir o mapa físico campo a campo origem → destino a partir de `arquitetura-dados.md`.
6. Criar migrations complementares; depois implementar o importador versionado e idempotente.
