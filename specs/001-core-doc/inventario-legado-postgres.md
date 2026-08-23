# Inventário do Legado PostgreSQL — OVPDH

**Documento**: 04  
**Data**: 23 de agosto de 2026  
**Status**: Homologação restaurada para análise; PostGIS pendente

## Escopo da restauração

O backup `pcvi_backup_260821` foi restaurado no banco local PostgreSQL `observatorio`, criado para homologação. Foram carregadas todas as tabelas, dados e relacionamentos não espaciais. A extensão PostGIS não está instalada no servidor local; por isso, as tabelas `ovpdh_gis`, o objeto `spatial_ref_sys` e as views dependentes de geolocalização ficaram pendentes.

| Item | Resultado |
| --- | --- |
| Banco de homologação | `observatorio` (PostgreSQL, UTF8) |
| Esquemas restaurados | `public`, `old` e `analitico` |
| Tabelas em `public` | 48 |
| Tabelas em `old` | 48 |
| Views analíticas carregadas | 4 |
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
| Relações vítima–violação | 5.493 |
| Relações autor–violação | 5.070 |
| Relações fonte–violação | 3.651 |

O volume confirma que o modelo é relacional e não deve ser transformado em uma única tabela de ocorrências. Uma ocorrência pode conter várias violações, vítimas, autores e fontes; essas relações devem ser preservadas na evolução do novo banco.

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

## Pendência espacial

A extensão PostGIS e as tabelas `ovpdh_gis` não foram restauradas. As views de geolocalização também permanecem pendentes. A instalação do PostGIS deve ocorrer somente em homologação, em versão compatível com o PostgreSQL local; depois disso, a carga espacial deve ser executada e validada separadamente.

## Próximas ações recomendadas

1. Comparar contagens e chaves entre os esquemas `public` e `old`.
2. Instalar PostGIS na homologação e restaurar os objetos espaciais em lote separado.
3. Extrair o dicionário completo de colunas, chaves e catálogos controlados.
4. Validar o mapa de status com a curadoria.
5. Atualizar o MER para incorporar violações, fontes, depoimentos e classificações do legado.
6. Só então escrever as migrations do novo modelo e o script de importação versionado.
