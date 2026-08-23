# Diagnóstico do Legado e Plano de Migração Incremental — OVPDH

**Documento**: 03  
**Data**: 23 de agosto de 2026  
**Status**: Diagnóstico concluído; nenhuma migration aplicada

## 1. Evidências analisadas

| Fonte | Resultado |
| --- | --- |
| Aplicação atual | CodeIgniter 4, com migration inicial para `ocorrencias`, `vitimas`, `agressores`, `ocorrencia_revisoes`, `historico` e `produtos`. |
| Backup legado | Arquivo PostgreSQL em formato custom, banco `pcvi`, versão de origem PostgreSQL 17.10. |
| Integridade do backup | SHA-256 `96B900C4DCDD529F8DA6DFA0D53EADF52C1D6EB4053A43901449F1F2AE2D568D`. |

O backup contém os esquemas `public`, `old` e `analitico`. O esquema `analitico` contém views de indicadores e o esquema `old` preserva estruturas históricas. Nenhuma alteração foi feita no backup.

## 2. Diagnóstico

O legado PostgreSQL é mais detalhado que o modelo atual da aplicação. Além de ocorrências, vítimas e autores, ele contém entidades para violações, condutas, meios/instrumentos, lesões, fontes, depoimentos, instituições, classificações demográficas e relacionamentos N:N.

| Domínio legado | Tabelas principais | Tratamento proposto |
| --- | --- | --- |
| Ocorrência | `ovpdh_ocorrencia`, `main_status`, `main_municipio`, `main_uf` | Migrar para ocorrência preservando ID legado, data, observações, endereço, município, status e autoria de inclusão/alteração. |
| Violação | `ovpdh_violacao`, `ovpdh_violacaotipo`, `ovpdh_conduta`, tabelas de meios e lesões | Criar domínio estruturado de violações antes da importação; não reduzir esses dados a um único texto. |
| Vítima | `ovpdh_vitima`, relações de violação, raça/cor, gênero, escolaridade, ocupação e outras classificações | Preservar dados e relações; aplicar anonimização antes da exposição na nova aplicação. |
| Autor institucional | `ovpdh_autor`, instituição, cargo e relação com violação | Mapear para agressores/instituições, preservando cargo, classe institucional, tipo e relação com a violação. |
| Fontes e depoimentos | `ovpdh_fonte`, `ovpdh_depoimento`, relações com violação | Migrar para fontes estruturadas e depoimentos restritos, mantendo vínculo com ocorrência e violação. |
| Indicadores | views no esquema `analitico` | Inventariar fórmulas e reproduzir indicadores validados sem alterar a leitura histórica. |

## 3. Decisão arquitetural

Não haverá conexão direta de produção entre o novo MySQL/MariaDB e o PostgreSQL legado. O processo deve ocorrer em ambiente de homologação, por exportação controlada e scripts de importação versionados.

O banco PostgreSQL legado será mantido como referência imutável. A aplicação nova receberá cópia dos dados necessários, com identificadores de origem e trilha de migração. Nenhum dado é apagado ou atualizado no banco legado.

## 4. Estruturas adicionais necessárias

Além das tabelas já previstas no MER, a preservação completa do legado requer as entidades abaixo antes da carga histórica:

| Tabela projetada | Finalidade |
| --- | --- |
| `violacoes` | Registra cada violação de uma ocorrência, com tipo, conduta, observações e eventual enquadramento jurídico. |
| `violacao_meios_instrumentos` | Vincula uma violação a um ou mais meios/instrumentos. |
| `violacao_lesoes_corpo` | Vincula uma violação a uma ou mais lesões ou regiões do corpo. |
| `ocorrencia_origens_legado` | Guarda sistema de origem, tabela de origem, ID original, data de importação e resultado da migração. |
| `catalogo_legado_mapeamentos` | Mantém a correspondência entre valores legados e catálogos novos, com aprovação humana. |

Essas tabelas são aditivas. O campo atual `ocorrencias.tipo_violencia` será preservado como compatibilidade, enquanto as relações estruturadas passam a suportar pesquisas mais completas.

## 5. Fases de execução

1. **Preservar e restaurar em homologação**: guardar checksum, restaurar uma cópia do PostgreSQL em ambiente isolado e bloquear escrita nela.
2. **Inventariar dados**: obter contagens, chaves, valores de catálogo, status, relações e definições das views analíticas.
3. **Homologar mapa de dados**: a equipe valida a equivalência entre campos e catálogos legados e o novo modelo.
4. **Criar migrations aditivas**: criar as novas tabelas no MySQL/MariaDB, sem alterar ou apagar estruturas existentes.
5. **Executar importação em cópia**: carregar dados com IDs de origem, produzir relatório de erros e conferir contagens.
6. **Validar funcionalmente**: comparar amostras de ocorrências, vítimas, fontes e indicadores com o legado.
7. **Planejar produção**: criar backup verificável, aplicar migrations, importar dados aprovados e validar novamente antes de liberar o sistema.

## 6. Critérios de aceite

- O backup legado permanece inalterado e verificável pelo checksum.
- Toda linha importada tem identificação de origem ou justificativa documentada para não importação.
- Nenhuma ocorrência, vítima, fonte, depoimento, violação ou autor institucional é descartado silenciosamente.
- Contagens por tabela e por status são comparadas entre origem e destino.
- Indicadores históricos selecionados são reproduzidos e validados pela equipe.
- Dados pessoais e depoimentos não são liberados na área pública em nenhuma etapa da importação.
- A reversão é possível porque a importação ocorre em transação ou lote identificável, sem apagar o banco legado.

## 7. Próxima atividade técnica

Restaurar o backup em um PostgreSQL de homologação e produzir o inventário quantitativo: número de registros, catálogo de status, chaves estrangeiras, valores controlados e lista de views analíticas. Essa atividade é somente de leitura sobre a cópia restaurada.
