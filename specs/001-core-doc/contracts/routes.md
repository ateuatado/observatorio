# Contrato de rotas e telas — Etapa 1

**Versão:** 0.1.0  
**Data:** 25 de agosto de 2026  
**Tarefa:** T006  
**Estado:** proposta para validação de interface

## 1. Objetivo

Este documento confronta as rotas e telas existentes com a arquitetura-alvo. Ele preserva endereços já utilizados, explicita lacunas e define o contrato de navegação que orientará Controllers, filtros, menus e testes.

## 2. Inventário atual

### Portal público

| Rota atual | Tela/ação | Situação | Destino na Etapa 1 |
| --- | --- | --- | --- |
| `GET /` | Página inicial | Manter | Portal com acesso a casos, coleções, acervo, produções e institucional. |
| `GET /sobre` | Sobre o OVPDH | Manter | Conteúdo institucional. |
| `GET /historico` | Lista do acervo | Manter | Evoluir filtros e acessibilidade. |
| `GET /historico/{id}` | Item do acervo | Manter | Preservar URL e melhorar metadados/arquivos. |
| `GET /produtos` | Produções acadêmicas | Manter | Evoluir busca e filtros. |
| `GET /produtos/{id}` | Produção acadêmica | Manter | Preservar URL. |
| `GET /pucsp` | Redirecionamento institucional | Manter | Continuar como redirecionamento explícito. |
| `GET /relatorios.html` | Documentação do desenvolvimento | Manter | Página estática de acompanhamento, fora do domínio funcional. |

### Área autenticada

| Rota atual | Tela/ação | Situação | Destino na Etapa 1 |
| --- | --- | --- | --- |
| `GET /painel` e `/painel/dashboard` | Dashboard | Migrar | Controller `Painel\Dashboard`; `/painel/dashboard` redireciona para `/painel`. |
| `GET /painel/ocorrencias` | Lista geral | Evoluir | Separar “minhas”, “atribuídas” e consulta interna por escopo. |
| `GET/POST /painel/ocorrencias/nova` | Cadastro monolítico | Substituir gradualmente | Criar rascunho mínimo e redirecionar para editor em sete seções. |
| `GET /painel/ocorrencias/{id}` | Detalhe interno | Evoluir | Resumo interno com ações autorizadas. |
| `GET/POST /painel/ocorrencias/{id}/editar` | Edição monolítica | Compatibilidade | Redirecionar para seção de identificação do novo editor. |
| `POST /painel/ocorrencias/{id}/status` | Alteração genérica de status | Substituir | Ações nomeadas e protegidas: enviar, aprovar, rejeitar, publicar etc. |
| `/painel/vitimas/*` | CRUD isolado | Incorporar | Vítimas passam a ser geridas dentro da ocorrência; URLs antigas redirecionam quando houver vínculo. |
| `/painel/agressores/*` | CRUD isolado | Incorporar | Renomear conceito visível para “Agentes e instituições”; preservar compatibilidade técnica durante a migração. |
| `/painel/revisao/*` | Fila e revisão | Migrar | Novo prefixo `/painel/curadoria`; rotas antigas redirecionam. |
| `GET /painel/relatorios` | Relatórios internos | Evoluir | Novo namespace `Relatorios` e filtros de agregação segura. |
| `/painel/usuarios/*` | Gestão de usuários | Reorganizar | Prefixo canônico `/painel/admin/usuarios`. |
| `/painel/historico/*` | Gestão do acervo | Reorganizar | Prefixo `/painel/editorial/acervo`. |
| `/painel/produtos-admin/*` | Gestão de produções | Reorganizar | Prefixo `/painel/editorial/producoes`. |

## 3. Lacunas confirmadas

- Não há busca pública de ocorrências nem página pública de caso.
- Não há coleções editoriais de casos.
- A ficha atual divide vítimas e agentes em CRUDs externos, contrariando o agregado projetado.
- O status é alterado por uma operação genérica, sem contrato explícito por transição.
- Administração, curadoria e trabalho documental compartilham o mesmo namespace visual e de Controller.
- Não há telas de catálogos, autorizações acadêmicas, auditoria ou pendências de qualificação.
- Não há prévia pública exata nem comparação entre versões.
- O menu atual reflete grupos históricos, não a matriz canônica de ações e escopo por registro.
- Views atuais contêm estilos, scripts e eventos inline que deverão migrar para ativos em `public/assets/`.

## 4. Rotas canônicas propostas

### Públicas

| Método e rota | Finalidade |
| --- | --- |
| `GET /ocorrencias` | Buscar somente versões publicadas. |
| `GET /ocorrencias/{slug}` | Consultar a versão pública vigente de um caso. |
| `GET /colecoes` | Listar coleções editoriais ativas. |
| `GET /colecoes/{slug}` | Consultar uma coleção e seus casos ainda publicados. |
| `GET /historico` e `/historico/{id}` | Acervo histórico. |
| `GET /produtos` e `/produtos/{id}` | Produções acadêmicas. |
| `GET /sobre` | Conteúdo institucional. |

### Trabalho documental

| Método e rota | Finalidade/proteção |
| --- | --- |
| `GET /painel` | Painel condicionado às permissões. |
| `GET /painel/ocorrencias` | Minhas ocorrências e ocorrências atribuídas. |
| `POST /painel/ocorrencias` | Criar rascunho mínimo. |
| `GET /painel/ocorrencias/{id}` | Resumo interno, condicionado ao escopo do registro. |
| `GET /painel/ocorrencias/{id}/editar/{secao}` | Abrir uma das sete seções. |
| `PUT /painel/ocorrencias/{id}/{secao}` | Salvar a seção com bloqueio otimista. |
| `POST /painel/ocorrencias/{id}/enviar-revisao` | Validar completude e enviar. |
| `GET/POST /painel/ocorrencias/{id}/equipe` | Consultar/alterar responsáveis e colaboradores autorizados. |

Os valores aceitos para `{secao}` são: `identificacao`, `violacoes`, `vitimas`, `agentes`, `fontes`, `privacidade` e `historico`.

### Curadoria

| Método e rota | Finalidade/proteção |
| --- | --- |
| `GET /painel/curadoria` | Fila de revisão. |
| `GET /painel/curadoria/{id}` | Revisão estruturada de registro alheio. |
| `GET /painel/curadoria/{id}/comparar` | Comparar versões. |
| `GET /painel/curadoria/{id}/previa-publica` | Renderizar a projeção pública exata. |
| `POST /painel/curadoria/{id}/solicitar-correcao` | Voltar a rascunho com justificativa. |
| `POST /painel/curadoria/{id}/aprovar` | Passar de revisão para aprovado. |
| `POST /painel/curadoria/{id}/rejeitar` | Rejeitar com justificativa. |
| `POST /painel/curadoria/{id}/publicar` | Publicar versão aprovada após checklist. |
| `POST /painel/curadoria/{id}/despublicar` | Retirar publicação com justificativa obrigatória. |
| `POST /painel/curadoria/{id}/arquivar` | Arquivar registro não publicado. |

### Administração, governança e editorial

| Prefixo | Recursos principais |
| --- | --- |
| `/painel/admin/usuarios` | Usuários, estado, grupos e vínculos institucionais. |
| `/painel/admin/catalogos` | Itens, sugestões, homologação, fusão e inativação. |
| `/painel/admin/autorizacoes` | Autorizações acadêmicas restritas, temporárias e auditadas. |
| `/painel/editorial/colecoes` | Coleções e ordenação de versões publicadas. |
| `/painel/editorial/acervo` | Acervo histórico. |
| `/painel/editorial/producoes` | Produções acadêmicas. |
| `/painel/governanca/auditoria` | Eventos e acessos restritos. |
| `/painel/governanca/pendencias` | Pendências de qualificação. |
| `/painel/relatorios/indicadores` | Indicadores internos com agregação segura. |

## 5. Compatibilidade e redirecionamentos

| Origem antiga | Destino canônico | Regra |
| --- | --- | --- |
| `/painel/dashboard` | `/painel` | `301` após estabilização; inicialmente `302`. |
| `/painel/revisao` | `/painel/curadoria` | Preservar filtros compatíveis na query string. |
| `/painel/revisao/{id}` | `/painel/curadoria/{id}` | Verificar autorização novamente no destino. |
| `/painel/usuarios/*` | `/painel/admin/usuarios/*` | Manter métodos e CSRF; nunca redirecionar mutações POST. |
| `/painel/historico/*` | `/painel/editorial/acervo/*` | Redirecionar apenas GET; adaptar formulários antes de remover POST antigo. |
| `/painel/produtos-admin/*` | `/painel/editorial/producoes/*` | Mesmo critério do acervo. |
| `/painel/ocorrencias/{id}/editar` | `/painel/ocorrencias/{id}/editar/identificacao` | GET com reautorização por objeto. |

## 6. Regras transversais do contrato

1. Toda rota interna exige sessão; o caso de uso repete a autorização para o registro concreto.
2. Ocultar menu ou botão nunca substitui autorização no servidor.
3. Mutações usam CSRF, método explícito e redirecionamento pós-sucesso.
4. IDs internos não são usados como identificador público; casos e coleções usam slug estável.
5. Apenas a projeção `publicado` chega às rotas públicas.
6. Downloads restritos passam por Controller/Service, autorização e auditoria; não usam caminho público direto.
7. Erros de autorização não revelam a existência de registros fora do escopo.
8. Rotas antigas só são removidas depois de testes de redirecionamento e regressão.

## 7. Critérios de aceite de T006

- Todas as rotas declaradas atualmente em `app/Config/Routes.php` foram classificadas.
- Cada área-alvo possui prefixo, finalidade e proteção identificáveis.
- As lacunas entre telas existentes e escopo aprovado estão explícitas.
- URLs públicas existentes são preservadas.
- Redirecionamentos não transformam uma mutação em outra requisição implicitamente.

