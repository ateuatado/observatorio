# Tarefas: Etapa 1 do OVPDH

**Versão do documento**: 0.2.0

**Data**: 25 de agosto de 2026

**Entrada**: `spec.md`, `plan.md`, `arquitetura-dados.md`, `mer-projetado.md`

**Status**: Backlog em execução; interface pronta para validação de cenários

## Formato e uso

Cada item segue `[ID] [P?] [US?] descrição com caminho`.

- **[P]**: pode ser executado em paralelo com outras tarefas da mesma fase quando não houver dependência indicada;
- **[US]**: história de entrega definida em `plan.md`;
- cada tarefa deve caber preferencialmente em até um dia de desenvolvimento; se crescer, deve ser dividida antes de começar;
- implementação inclui atualização dos testes e da documentação afetada;
- itens concluídos são marcados com `[x]`, sem renumerar IDs existentes.

## Fase 0 — Especificação e desenho

**Objetivo**: fechar a baseline e tornar a interface implementável sem decisões implícitas.

- [x] T001 Consolidar requisitos funcionais e regras de acesso em `specs/001-core-doc/spec.md`.
- [x] T002 Consolidar arquitetura e relações do legado em `specs/001-core-doc/arquitetura-dados.md` e `specs/001-core-doc/mer-projetado.md`.
- [x] T003 Atualizar PostgreSQL/PostGIS e versão 1.1.0 em `.specify/memory/constitution.md`.
- [x] T004 Criar plano técnico versionado em `specs/001-core-doc/plan.md`.
- [x] T005 Criar backlog granular versionado em `specs/001-core-doc/tasks.md`.
- [x] T006 [P] Inventariar rotas e telas atuais versus módulos-alvo em `specs/001-core-doc/contracts/routes.md`.
- [x] T007 [P] Produzir mapa de navegação por perfil em `specs/001-core-doc/interface/mapa-navegacao.md`.
- [x] T008 [P] Produzir wireframes de baixa fidelidade do painel e das sete seções em `specs/001-core-doc/interface/wireframes-internos.md`.
- [x] T009 [P] Produzir wireframes da fila, comparação e prévia pública em `specs/001-core-doc/interface/wireframes-curadoria.md`.
- [x] T010 [P] Produzir wireframes da busca, coleção e caso público em `specs/001-core-doc/interface/wireframes-publicos.md`.
- [ ] T011 Validar os wireframes com casos simples, complexo e sensível e registrar decisões em `specs/001-core-doc/interface/validacao-cenarios.md` (depende de T008–T010).
- [ ] T012 Produzir dicionário físico canônico e mapa legado → destino em `specs/001-core-doc/data-model.md` (depende das decisões bloqueadoras do plano).
- [ ] T013 Definir contratos dos Services e transições em `specs/001-core-doc/contracts/services.md` (depende de T012).
- [ ] T014 Criar checklist de privacidade/publicação em `specs/001-core-doc/checklists/privacidade-publicacao.md`.

**Checkpoint 0**: navegação, wireframes, dicionário e contratos aprovados; tarefas físicas podem ser refinadas sem alterar o modelo conceitual.

---

## Fase 1 — US1 Fundação segura e templates

**Objetivo**: autenticar usuários, aplicar permissões e fornecer shells reutilizáveis.

**Teste independente**: contas de cada perfil acessam somente os menus e rotas autorizados; conteúdo restrito não aparece no HTML da resposta negada.

### Ambiente e banco de teste

- [ ] T015 [US1] Criar grupo PostgreSQL de teste em `app/Config/Database.php`, sem credenciais versionadas.
- [ ] T016 [US1] Documentar preparação do banco PostgreSQL/PostGIS de teste em `specs/001-core-doc/quickstart.md`.
- [ ] T017 [P] [US1] Criar helper de fixtures/autenticação em `tests/_support/AuthTestHelper.php`.
- [ ] T018 [P] [US1] Criar helper de limpeza transacional PostgreSQL em `tests/_support/PostgresTestCase.php`.
- [ ] T019 [US1] Substituir testes de exemplo por teste de saúde PostgreSQL/PostGIS em `tests/database/PostgresHealthTest.php`.

### Autorização e navegação

- [ ] T020 [P] [US1] Revisar grupos e permissões canônicos em `app/Config/AuthGroups.php`.
- [ ] T021 [P] [US1] Criar catálogo de permissões dos módulos em `app/Config/Permissions.php`.
- [ ] T022 [US1] Criar serviço de autorização por ocorrência em `app/Services/Authorization/OccurrencePolicy.php`.
- [ ] T023 [US1] Criar filtro para permissão e escopo de registro em `app/Filters/OccurrenceAccessFilter.php`.
- [ ] T024 [US1] Registrar filtros e aliases em `app/Config/Filters.php`.
- [ ] T025 [US1] Reorganizar grupos de rotas públicas e internas em `app/Config/Routes.php`, preservando URLs existentes por redirecionamento.
- [ ] T026 [P] [US1] Criar teste de matriz de permissões em `tests/security/PermissionMatrixTest.php`.
- [ ] T027 [P] [US1] Criar teste contra IDOR em ocorrências alheias em `tests/security/OccurrenceObjectAccessTest.php`.

### Layouts e componentes

- [ ] T028 [P] [US1] Criar layout de autenticação em `app/Views/layouts/auth.php`.
- [ ] T029 [P] [US1] Criar layout interno em `app/Views/layouts/internal.php`.
- [ ] T030 [P] [US1] Revisar layout público em `app/Views/layouts/public.php`.
- [ ] T031 [US1] Migrar login para o layout de autenticação em `app/Views/auth/login.php`.
- [ ] T032 [P] [US1] Criar navegação interna por permissão em `app/Views/components/internal_nav.php`.
- [ ] T033 [P] [US1] Criar breadcrumb em `app/Views/components/breadcrumb.php`.
- [ ] T034 [P] [US1] Criar mensagens e erros de formulário em `app/Views/components/flash_messages.php` e `app/Views/components/validation_errors.php`.
- [ ] T035 [P] [US1] Criar estilos dos layouts em `public/assets/css/layouts.css`.
- [ ] T036 [P] [US1] Criar comportamento de navegação em `public/assets/js/navigation.js`, sem eventos inline.
- [ ] T037 [US1] Migrar dashboard atual para `app/Controllers/Painel/Dashboard.php` e `app/Views/painel/dashboard.php`.
- [ ] T038 [P] [US1] Testar renderização e menus por perfil em `tests/feature/LayoutAndNavigationTest.php`.
- [ ] T039 [P] [US1] Testar login, logout e redirecionamento seguro em `tests/feature/AuthenticationFlowTest.php`.

**Checkpoint US1**: fundação pronta; US2, partes visuais de US3 e conteúdo institucional de US5 podem avançar em paralelo.

---

## Fase 2 — Modelo físico compartilhado

**Objetivo**: criar estruturas que bloqueiam as histórias de domínio.

- [ ] T040 Criar migration de catálogos territoriais e de domínio em `app/Database/Migrations/2026-08-25-000003_CreateCanonicalCatalogs.php` (depende de T012).
- [ ] T041 [P] Criar migration de versões, publicações e atribuições em `app/Database/Migrations/2026-08-25-000004_CreateOccurrenceWorkflowTables.php` (depende de T012).
- [ ] T042 [P] Criar migration de vínculos vítima/agente/fonte–violação em `app/Database/Migrations/2026-08-25-000005_CreateViolationRelations.php` (depende de T012).
- [ ] T043 [P] Criar migration de dados restritos e autorizações acadêmicas em `app/Database/Migrations/2026-08-25-000006_CreateRestrictedDataTables.php` (depende de T012).
- [ ] T044 [P] Criar migration de arquivos e derivações expurgadas em `app/Database/Migrations/2026-08-25-000007_CreateFileTables.php` (depende de T012).
- [ ] T045 [P] Criar migration de auditoria e pendências de qualificação em `app/Database/Migrations/2026-08-25-000008_CreateGovernanceTables.php` (depende de T012).
- [ ] T046 [P] Criar migration de coleções públicas e vínculos em `app/Database/Migrations/2026-08-25-000009_CreateCollections.php` (depende de T012).
- [ ] T047 Criar seeders dos catálogos homologados em `app/Database/Seeds/CanonicalCatalogSeeder.php` (depende de T040).
- [ ] T048 [P] Criar models de catálogos em `app/Models/Catalog/`.
- [ ] T049 [P] Criar models do agregado ocorrência em `app/Models/Occurrence/`.
- [ ] T050 [P] Criar models de auditoria e acesso em `app/Models/Governance/`.
- [ ] T051 [P] Criar entities tipadas em `app/Entities/` para ocorrência, versão, publicação e autorização.
- [ ] T052 Criar teste de subida/rollback seguro das migrations em `tests/database/CanonicalMigrationsTest.php` (depende de T040–T046).
- [ ] T053 [P] Criar testes de unicidade e integridade N:N em `tests/database/ViolationRelationsTest.php`.
- [ ] T054 [P] Criar testes de `RESTRICT` e exclusão lógica em `tests/database/DocumentPreservationTest.php`.
- [ ] T055 [P] Criar testes de geometria 4326 e índice espacial em `tests/database/OccurrenceGeometryTest.php`.

**Checkpoint 2**: esquema canônico reproduzível e testado em PostgreSQL/PostGIS.

---

## Fase 3 — US2 Administração de usuários e catálogos

**Objetivo**: administrar pessoas, vínculos, permissões e vocabulários sem acesso direto ao banco.

**Teste independente**: administrador cria voluntário, define vínculo e permissões; curador sugere catálogo; administrador homologa sem alterar registros históricos.

### Usuários e vínculos

- [ ] T056 [P] [US2] Criar migration de vínculos institucionais em `app/Database/Migrations/2026-08-25-000010_CreateInstitutionalAffiliations.php`.
- [ ] T057 [P] [US2] Criar `InstitutionalAffiliationModel` em `app/Models/Admin/InstitutionalAffiliationModel.php`.
- [ ] T058 [US2] Extrair regras de usuários para `app/Services/Admin/UserManagementService.php`.
- [ ] T059 [US2] Atualizar `app/Controllers/Admin/Usuarios.php` para usar o Service e autorizações.
- [ ] T060 [P] [US2] Atualizar listagem em `app/Views/admin/usuarios/index.php` com vínculo, grupo e estado.
- [ ] T061 [P] [US2] Atualizar criação/edição em `app/Views/admin/usuarios/create.php` e `app/Views/admin/usuarios/edit.php`.
- [ ] T062 [P] [US2] Criar teste de criação/desativação sem perda de autoria em `tests/integration/AdminUserManagementTest.php`.

### Catálogos e glossários

- [ ] T063 [US2] Criar `CatalogService` em `app/Services/Admin/CatalogService.php`.
- [ ] T064 [US2] Criar Controller de catálogos em `app/Controllers/Admin/Catalogos.php`.
- [ ] T065 [P] [US2] Criar listagem de catálogos em `app/Views/admin/catalogos/index.php`.
- [ ] T066 [P] [US2] Criar formulário de item/sugestão em `app/Views/admin/catalogos/form.php`.
- [ ] T067 [P] [US2] Criar componente pesquisável em `app/Views/components/catalog_picker.php`.
- [ ] T068 [P] [US2] Criar progressive enhancement do catálogo em `public/assets/js/catalog-picker.js`.
- [ ] T069 [US2] Adicionar rotas administrativas e de sugestão em `app/Config/Routes.php`.
- [ ] T070 [P] [US2] Testar que curador sugere mas não homologa em `tests/security/CatalogGovernanceTest.php`.
- [ ] T071 [P] [US2] Testar merge/inativação sem troca de significado em `tests/integration/CatalogLifecycleTest.php`.

**Checkpoint US2**: administração funcional e catálogos prontos para alimentar a ficha.

---

## Fase 4 — US3 Documentação de ocorrência

**Objetivo**: criar e enviar uma ocorrência completa para revisão.

**Teste independente**: usuário cria rascunho parcial, preenche as sete seções, recebe alertas de vínculo/completude e envia para revisão.

### Casos de uso

- [ ] T072 [US3] Criar `CreateOccurrenceDraft` em `app/Services/Ocorrencias/CreateOccurrenceDraft.php`.
- [ ] T073 [P] [US3] Criar `UpdateOccurrenceIdentification` em `app/Services/Ocorrencias/UpdateOccurrenceIdentification.php`.
- [ ] T074 [P] [US3] Criar `ManageOccurrenceViolations` em `app/Services/Ocorrencias/ManageOccurrenceViolations.php`.
- [ ] T075 [P] [US3] Criar `ManageOccurrenceVictims` em `app/Services/Ocorrencias/ManageOccurrenceVictims.php`.
- [ ] T076 [P] [US3] Criar `ManageOccurrenceActors` em `app/Services/Ocorrencias/ManageOccurrenceActors.php`.
- [ ] T077 [P] [US3] Criar `ManageOccurrenceSources` em `app/Services/Ocorrencias/ManageOccurrenceSources.php`.
- [ ] T078 [US3] Criar `EvaluateOccurrenceCompleteness` em `app/Services/Ocorrencias/EvaluateOccurrenceCompleteness.php` (depende de T073–T077).
- [ ] T079 [US3] Criar `SubmitOccurrenceForReview` em `app/Services/Ocorrencias/SubmitOccurrenceForReview.php` (depende de T078).
- [ ] T080 [US3] Criar `OccurrenceVersionService` em `app/Services/Ocorrencias/OccurrenceVersionService.php` com bloqueio otimista.

### Controller, rotas e Views

- [ ] T081 [US3] Criar Controller do agregado em `app/Controllers/Painel/Ocorrencias.php`.
- [ ] T082 [US3] Adicionar rotas REST-like da ficha em `app/Config/Routes.php`.
- [ ] T083 [P] [US3] Criar listagem “minhas/atribuídas” em `app/Views/painel/ocorrencias/index.php`.
- [ ] T084 [P] [US3] Criar shell da ficha e navegação de seções em `app/Views/painel/ocorrencias/edit.php`.
- [ ] T085 [P] [US3] Criar seção identificação em `app/Views/painel/ocorrencias/sections/identificacao.php`.
- [ ] T086 [P] [US3] Criar seção violações em `app/Views/painel/ocorrencias/sections/violacoes.php`.
- [ ] T087 [P] [US3] Criar seção vítimas/grupos em `app/Views/painel/ocorrencias/sections/vitimas.php`.
- [ ] T088 [P] [US3] Criar seção agentes/instituições em `app/Views/painel/ocorrencias/sections/agentes.php`.
- [ ] T089 [P] [US3] Criar seção fontes/evidências em `app/Views/painel/ocorrencias/sections/fontes.php`.
- [ ] T090 [P] [US3] Criar seção privacidade/versão pública em `app/Views/painel/ocorrencias/sections/privacidade.php`.
- [ ] T091 [P] [US3] Criar seção curadoria/histórico em `app/Views/painel/ocorrencias/sections/curadoria.php`.
- [ ] T092 [P] [US3] Criar componentes repetíveis em `app/Views/components/occurrence/`.
- [ ] T093 [P] [US3] Criar indicador de completude em `app/Views/components/occurrence/completeness.php`.
- [ ] T094 [P] [US3] Criar interação das seções em `public/assets/js/occurrence-editor.js`.
- [ ] T095 [P] [US3] Criar estilos da ficha em `public/assets/css/occurrence-editor.css`.

### Testes

- [ ] T096 [P] [US3] Testar rascunho parcial em `tests/integration/OccurrenceDraftTest.php`.
- [ ] T097 [P] [US3] Testar vínculos automáticos e múltiplos em `tests/unit/ViolationLinkingTest.php`.
- [ ] T098 [P] [US3] Testar obrigatoriedade progressiva em `tests/unit/OccurrenceCompletenessTest.php`.
- [ ] T099 [P] [US3] Testar conflito de versão em `tests/integration/OccurrenceConcurrencyTest.php`.
- [ ] T100 [US3] Testar percurso completo até `em_revisao` em `tests/feature/OccurrenceSubmissionFlowTest.php`.

**Checkpoint US3**: registro chega à fila sem depender da interface de curadoria.

---

## Fase 5 — US4 Curadoria e publicação versionada

**Objetivo**: revisar ocorrência alheia, proteger dados e publicar versão estável.

**Teste independente**: curador não autor revisa, rejeita ou aprova, confere prévia e publica; edição posterior não altera a página vigente.

- [ ] T101 [P] [US4] Criar `ReviewOccurrence` em `app/Services/Curadoria/ReviewOccurrence.php`.
- [ ] T102 [P] [US4] Criar `ApproveOccurrence` em `app/Services/Curadoria/ApproveOccurrence.php`.
- [ ] T103 [P] [US4] Criar `RejectOccurrence` em `app/Services/Curadoria/RejectOccurrence.php`.
- [ ] T104 [P] [US4] Criar `BuildPublicProjection` em `app/Services/Publicacao/BuildPublicProjection.php`.
- [ ] T105 [US4] Criar `PublishOccurrenceVersion` em `app/Services/Publicacao/PublishOccurrenceVersion.php` (depende de T104).
- [ ] T106 [P] [US4] Criar `UnpublishOccurrence` em `app/Services/Publicacao/UnpublishOccurrence.php`.
- [ ] T107 [P] [US4] Criar `ArchiveOccurrence` em `app/Services/Curadoria/ArchiveOccurrence.php`.
- [ ] T108 [US4] Criar fila em `app/Controllers/Curadoria/Fila.php`.
- [ ] T109 [US4] Criar revisão em `app/Controllers/Curadoria/Ocorrencias.php`.
- [ ] T110 [US4] Adicionar rotas de curadoria em `app/Config/Routes.php`.
- [ ] T111 [P] [US4] Criar fila e filtros em `app/Views/curadoria/fila/index.php`.
- [ ] T112 [P] [US4] Criar comparação de versões em `app/Views/curadoria/ocorrencias/compare.php`.
- [ ] T113 [P] [US4] Criar checklist/ações em `app/Views/curadoria/ocorrencias/review.php`.
- [ ] T114 [P] [US4] Criar prévia exata em `app/Views/curadoria/ocorrencias/public_preview.php`.
- [ ] T115 [P] [US4] Criar componente de alerta de reidentificação em `app/Views/components/privacy_warning.php`.
- [ ] T116 [P] [US4] Testar proibição de autocuradoria em `tests/security/SelfReviewTest.php`.
- [ ] T117 [P] [US4] Testar transições e justificativas em `tests/unit/OccurrenceWorkflowTest.php`.
- [ ] T118 [P] [US4] Testar projeção sem campos restritos em `tests/security/PublicProjectionPrivacyTest.php`.
- [ ] T119 [P] [US4] Testar publicação versionada em `tests/integration/OccurrencePublicationVersionTest.php`.
- [ ] T120 [US4] Testar revisão → publicação → nova edição → republicação em `tests/feature/CurationPublicationFlowTest.php`.

**Checkpoint US4**: versão pública segura disponível para o portal.

---

## Fase 6 — US5 Consulta pública

**Objetivo**: permitir que qualquer visitante encontre e consulte somente dados publicados.

**Teste independente**: visitante pesquisa ocorrência publicada; IDs de rascunho, dados restritos e versões antigas de trabalho retornam indisponíveis.

- [ ] T121 [US5] Criar `PublicOccurrenceQuery` em `app/Services/Publicacao/PublicOccurrenceQuery.php`.
- [ ] T122 [US5] Criar Controller público em `app/Controllers/Public/Ocorrencias.php`.
- [ ] T123 [US5] Adicionar rotas públicas de busca e detalhe em `app/Config/Routes.php`.
- [ ] T124 [P] [US5] Criar busca pública em `app/Views/public/ocorrencias/index.php`.
- [ ] T125 [P] [US5] Criar detalhe público em `app/Views/public/ocorrencias/show.php`.
- [ ] T126 [P] [US5] Criar filtros acessíveis em `app/Views/components/public_filters.php`.
- [ ] T127 [P] [US5] Criar estilos públicos em `public/assets/css/public-occurrences.css`.
- [ ] T128 [P] [US5] Testar que somente `publicado` aparece em `tests/feature/PublicOccurrenceSearchTest.php`.
- [ ] T129 [P] [US5] Testar ausência de PII e coordenada exata no HTML em `tests/security/PublicOccurrenceLeakTest.php`.
- [ ] T130 [P] [US5] Testar busca, paginação e filtros em `tests/integration/PublicOccurrenceQueryTest.php`.

**Checkpoint US5 — MVP vertical**: administrador configura, usuário cadastra, curador publica e visitante consulta.

---

## Fase 7 — US6 Colaboração e acesso acadêmico

**Objetivo**: distribuir trabalho sem confundir autoria, responsabilidade e revisão.

- [ ] T131 [P] [US6] Criar `AssignmentService` em `app/Services/Ocorrencias/AssignmentService.php`.
- [ ] T132 [P] [US6] Criar `RestrictedAccessGrantService` em `app/Services/Governanca/RestrictedAccessGrantService.php`.
- [ ] T133 [US6] Criar Controller de equipe da ocorrência em `app/Controllers/Painel/EquipeOcorrencia.php`.
- [ ] T134 [US6] Criar Controller de autorizações em `app/Controllers/Admin/AutorizacoesAcesso.php`.
- [ ] T135 [P] [US6] Criar painel de responsável/colaboradores em `app/Views/painel/ocorrencias/team.php`.
- [ ] T136 [P] [US6] Criar gestão de autorização em `app/Views/admin/autorizacoes/index.php`.
- [ ] T137 [P] [US6] Testar responsável único e histórico em `tests/integration/OccurrenceAssignmentTest.php`.
- [ ] T138 [P] [US6] Testar expiração/revogação de acesso em `tests/security/RestrictedAccessGrantTest.php`.
- [ ] T139 [P] [US6] Testar que colaborador não herda curadoria em `tests/security/CollaboratorReviewIsolationTest.php`.

---

## Fase 8 — US7 Governança e qualidade

**Objetivo**: auditar acessos e resolver pendências sem alterar evidência histórica.

- [ ] T140 [P] [US7] Criar `AuditService` em `app/Services/Governanca/AuditService.php`.
- [ ] T141 [P] [US7] Criar `QualificationQueueService` em `app/Services/Governanca/QualificationQueueService.php`.
- [ ] T142 [US7] Integrar auditoria de leitura/download aos Services restritos em `app/Services/`.
- [ ] T143 [US7] Criar Controller de auditoria em `app/Controllers/Governanca/Auditoria.php`.
- [ ] T144 [US7] Criar Controller de pendências em `app/Controllers/Governanca/Pendencias.php`.
- [ ] T145 [P] [US7] Criar consulta de auditoria em `app/Views/governanca/auditoria/index.php`.
- [ ] T146 [P] [US7] Criar fila de qualidade em `app/Views/governanca/pendencias/index.php`.
- [ ] T147 [P] [US7] Testar que log não replica conteúdo sensível em `tests/security/AuditContentMinimizationTest.php`.
- [ ] T148 [P] [US7] Testar resolução de pendência com preservação original em `tests/integration/QualificationQueueTest.php`.

---

## Fase 9 — US8 Gestão editorial e coleções

**Objetivo**: administrar acervo, produções e agrupamentos públicos de ocorrências.

- [ ] T149 [P] [US8] Criar Models de coleções em `app/Models/Editorial/CollectionModel.php` e `app/Models/Editorial/CollectionOccurrenceModel.php`.
- [ ] T150 [US8] Criar `CollectionService` em `app/Services/Editorial/CollectionService.php`.
- [ ] T151 [P] [US8] Extrair gestão de histórico para `app/Services/Editorial/HistoricalCollectionService.php`.
- [ ] T152 [P] [US8] Extrair gestão de produtos para `app/Services/Editorial/AcademicProductService.php`.
- [ ] T153 [US8] Criar Controllers em `app/Controllers/Editorial/`.
- [ ] T154 [P] [US8] Criar Views administrativas de coleção em `app/Views/editorial/colecoes/`.
- [ ] T155 [P] [US8] Criar índice e detalhe público em `app/Views/public/colecoes/`.
- [ ] T156 [US8] Adicionar rotas editoriais e públicas em `app/Config/Routes.php`.
- [ ] T157 [P] [US8] Testar coleção contendo apenas versões publicadas em `tests/security/PublicCollectionPrivacyTest.php`.
- [ ] T158 [P] [US8] Testar ordenação e ativação editorial em `tests/integration/CollectionEditorialFlowTest.php`.

---

## Fase 10 — US9 Indicadores iniciais

**Objetivo**: fornecer agregados úteis sem risco de reidentificação.

- [ ] T159 [US9] Definir consultas e limiares de agregação em `specs/001-core-doc/contracts/indicators.md`.
- [ ] T160 [US9] Criar `InternalIndicatorService` em `app/Services/Relatorios/InternalIndicatorService.php`.
- [ ] T161 [P] [US9] Criar `PublicIndicatorService` em `app/Services/Relatorios/PublicIndicatorService.php`.
- [ ] T162 [US9] Criar Controller em `app/Controllers/Relatorios/Indicadores.php`.
- [ ] T163 [P] [US9] Criar dashboard em `app/Views/relatorios/indicadores/index.php`.
- [ ] T164 [P] [US9] Criar componentes de gráfico/tabela em `app/Views/components/indicators/`.
- [ ] T165 [P] [US9] Testar supressão de grupos pequenos em `tests/security/IndicatorReidentificationTest.php`.
- [ ] T166 [P] [US9] Testar totais sem dupla contagem de violações em `tests/integration/IndicatorAggregationTest.php`.

---

## Fase 11 — US10 Migração do legado

**Objetivo**: importar dados preservando IDs de origem, relações e resultado de cada linha.

- [ ] T167 [US10] Concluir comparação `public` × `old` em `specs/001-core-doc/legado-comparacao-schemas.md`.
- [ ] T168 [US10] Homologar mapa de status em `specs/001-core-doc/legado-mapa-status.md`.
- [ ] T169 [US10] Criar migration de lotes/rastreabilidade geral em `app/Database/Migrations/2026-08-25-000011_CreateMigrationTracking.php`.
- [ ] T170 [P] [US10] Criar leitor PostgreSQL somente leitura em `app/Services/Migracao/LegacyReader.php`.
- [ ] T171 [P] [US10] Criar transformadores por entidade em `app/Services/Migracao/Transformers/`.
- [ ] T172 [US10] Criar orquestrador idempotente em `app/Services/Migracao/LegacyImportService.php`.
- [ ] T173 [US10] Criar comando `ovpdh:import-legacy` em `app/Commands/ImportLegacy.php` com `--dry-run`, `--batch` e `--resume`.
- [ ] T174 [P] [US10] Criar relatório de conciliação em `app/Services/Migracao/ReconciliationReport.php`.
- [ ] T175 [P] [US10] Testar idempotência e retomada em `tests/integration/LegacyImportIdempotencyTest.php`.
- [ ] T176 [P] [US10] Testar contagens e relações N:N em `tests/integration/LegacyImportReconciliationTest.php`.
- [ ] T177 [P] [US10] Testar que nenhuma linha com erro desaparece em `tests/integration/LegacyImportErrorTrackingTest.php`.
- [ ] T178 [US10] Executar `--dry-run` em homologação e registrar resultado em `specs/001-core-doc/relatorios/importacao-dry-run-v1.md`.

---

## Fase 12 — Acessibilidade, segurança e entrega

- [ ] T179 [P] Revisar navegação por teclado e foco em todas as Views da Etapa 1.
- [ ] T180 [P] Revisar rótulos, ajuda, mensagens de erro e contraste dos componentes.
- [ ] T181 [P] Aplicar e testar CSP sem scripts inline em `app/Config/ContentSecurityPolicy.php`.
- [ ] T182 [P] Testar upload por MIME, tamanho e armazenamento não executável em `tests/security/SecureUploadTest.php`.
- [ ] T183 [P] Testar CSRF nas mutações críticas em `tests/security/CsrfProtectionTest.php`.
- [ ] T184 [P] Testar paginação e tempo das listas com volume equivalente ao legado em `tests/integration/LegacyScaleQueryTest.php`.
- [ ] T185 Executar suíte completa com `composer test` e corrigir falhas.
- [ ] T186 Validar migrations em banco PostgreSQL vazio e cópia de homologação.
- [ ] T187 Executar checklist de privacidade de `specs/001-core-doc/checklists/privacidade-publicacao.md`.
- [ ] T188 Atualizar `specs/001-core-doc/status-continuacao-2026-08-24.md` com o resultado da entrega.
- [ ] T189 Preparar plano de backup, deploy e rollback em `specs/001-core-doc/release/etapa-1.md`.
- [ ] T190 Publicar a Etapa 1 no VPS somente após aprovação dos portões de qualidade.

---

## Dependências resumidas

| Bloco | Depende de | Libera |
| --- | --- | --- |
| Fase 0 | Specs atuais | Refinamento físico e wireframes |
| US1 | Fase 0 parcialmente; T015–T039 | Todas as áreas autenticadas |
| Modelo físico | T012 | US2–US7 e US10 |
| US2 | US1 + catálogos físicos | Administração e seletores da ficha |
| US3 | US1 + modelo físico + catálogo | US4, US6 e US10 |
| US4 | US3 | US5 e indicadores públicos |
| US5 | US4 | MVP vertical demonstrável |
| US6 | US2 + US3 | Trabalho acadêmico supervisionado |
| US7 | US2 + auditoria física | Governança operacional |
| US8 | US1 + coleções físicas | Conteúdo/coleções públicas |
| US9 | US4 + regras de agregação | Indicadores iniciais |
| US10 | T012 + US3 + acesso ao legado | Carga histórica |

## Trilhas paralelas sugeridas

Após US1 e o modelo físico:

- **Trilha A — Domínio**: T072–T100, depois T101–T120;
- **Trilha B — Administração**: T056–T071, depois T131–T148;
- **Trilha C — Interface pública/editorial**: T121–T130 e T149–T158;
- **Trilha D — Dados/migração**: T167–T178, iniciando pela documentação e transformadores sem escrever na origem;
- **Trilha E — Qualidade**: testes marcados `[P]`, acessibilidade e segurança ao longo de todas as trilhas.

Tarefas que alteram `app/Config/Routes.php` devem ser coordenadas ou integradas em sequência para evitar conflito de arquivo. Migrations recebem numeração definitiva no momento da implementação, preservando a ordem deste plano.

## Estratégia incremental

1. concluir Fase 0, US1 e modelo físico;
2. executar US2 e US3;
3. validar cadastro com usuários reais;
4. executar US4 e US5;
5. demonstrar o MVP vertical e ajustar;
6. acrescentar US6–US9 conforme prioridade;
7. executar US10 somente após dry-run, conciliação e aprovação.

## Histórico do documento

- **0.2.0 — 2026-08-25**: T006–T010 concluídas com inventário de rotas, mapa de navegação por perfil e wireframes internos, de curadoria e públicos.
- **0.1.0 — 2026-08-24**: primeira decomposição granular, com 190 tarefas, histórias, dependências, paralelização e critérios de checkpoint.
