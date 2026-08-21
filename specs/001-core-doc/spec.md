# Feature Specification: Core Documentation OVPDH

**Feature Branch**: `001-core-doc`

**Created**: 2026-07-26

**Status**: Draft

**Input**: Levantamento e especificação das funcionalidades, tabelas e regras de negócio atuais do sistema Observatório de Violência Policial e Direitos Humanos (OVPDH).

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Acesso Público ao Acervo e Histórico (Priority: P1)
Usuários públicos (cidadãos, pesquisadores, jornalistas) devem poder acessar o site público para visualizar o Acervo Histórico, informações sobre o OVPDH e Produções Acadêmicas publicadas.
**Why this priority**: É a "vitrine" do projeto e o seu principal objetivo público (informar a sociedade).
**Independent Test**: Pode ser testado acessando a rota `/`, `/historico`, `/produtos` sem necessidade de login.

**Acceptance Scenarios**:
1. **Given** um usuário anônimo, **When** acessa a Home, **Then** ele visualiza a apresentação do projeto e os atalhos.
2. **Given** um usuário anônimo, **When** acessa o Acervo, **Then** ele visualiza somente registros com status `publicado`.

---

### User Story 2 - Cadastro e Gestão de Ocorrências (Priority: P1)
Usuários com acesso ao painel (voluntários, colaboradores, admins) devem poder registrar e editar detalhes completos de uma ocorrência (dados do fato, vítimas envolvidas e agressores identificados).
**Why this priority**: É o *core business* do observatório (coleta e armazenamento de violações).
**Independent Test**: Testado logando no painel como curador e criando uma nova ocorrência com vítimas e agressores associados.

**Acceptance Scenarios**:
1. **Given** um curador autenticado, **When** ele preenche e salva o formulário de ocorrência, **Then** os dados são salvos com status inicial `rascunho`.
2. **Given** um curador, **When** salva um novo registro, **Then** ele é criado com status `rascunho` e permanece fora da área pública.
3. **Given** um curador, **When** conclui o preenchimento necessário, **Then** ele pode enviar o registro de `rascunho` para `em_revisao`.

---

### User Story 3 - Curadoria e Revisão (Priority: P2)
Administradores e coordenadores devem revisar ocorrências cadastradas pela equipe antes que elas sejam aprovadas e (se aplicável) se tornem públicas ou consolidadas no banco de dados.
**Why this priority**: Garante a veracidade e a qualidade dos dados (evita *fake news* ou dados inconsistentes).
**Independent Test**: Testado através do fluxo `em_revisao` → `aprovado` → `publicado` e do registro de cada ação na tabela `ocorrencia_revisoes`.

**Acceptance Scenarios**:
1. **Given** um registro em `em_revisao`, **When** um curador diferente de seu autor o aprova, **Then** o status passa para `aprovado` e a ação é registrada no histórico.
2. **Given** um registro `aprovado`, **When** um curador o publica, **Then** o status passa para `publicado` e o registro se torna visível na área pública.
3. **Given** um registro publicado, **When** um curador o despublica, **Then** o status retorna para `aprovado`, deixa de ser público e a ação é registrada no histórico.
4. **Given** um registro em revisão ou aprovado, **When** um curador o rejeita, **Then** o status passa para `rejeitado`, o motivo é obrigatório e a ação é registrada no histórico.

---

### User Story 4 - Gestão de Usuários e Perfis (Priority: P2)
O Administrador deve poder gerenciar os membros da equipe, criando novas contas e atribuindo os perfis definidos. O Superadministrador deve poder realizar as alterações relativas ao site.
**Why this priority**: O sistema possui acesso restrito e várias mãos alimentam o banco, necessitando de auditoria.

**Acceptance Scenarios**:
1. **Given** um administrador autenticado, **When** cria ou altera uma conta, **Then** ele pode atribuir somente os perfis previstos nesta especificação.
2. **Given** um usuário comum autenticado, **When** pesquisa o acervo interno, **Then** encontra somente registros com status `aprovado`.

### Edge Cases
- Um registro não pode passar para `em_revisao`, `aprovado` ou `publicado` se não atender aos campos obrigatórios que serão definidos para a respectiva etapa.
- O comportamento de anonimização de vítimas será especificado quando os campos e as regras de dados forem definidos.
- Registros em `rascunho`, `em_revisao`, `aprovado` e `rejeitado` não são exibidos publicamente. Somente registros `publicado` são expostos no site público.
- A rejeição exige justificativa; a ação fica no histórico de revisão.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE permitir cadastro de ocorrências detalhando Local (Cidade/Bairro), Data, Tipo de Violência e Relato.
- **FR-002**: O sistema DEVE permitir associar múltiplas Vítimas a uma única Ocorrência (relacionamento 1:N).
- **FR-003**: O sistema DEVE permitir associar múltiplos Agressores/Instituições a uma única Ocorrência (relacionamento 1:N).
- **FR-004**: O sistema DEVE possuir um módulo de autenticação implementado via CodeIgniter Shield, com os perfis `usuario_comum`, `curador`, `administrador` e `superadministrador`.
- **FR-005**: O sistema DEVE manter um registro (log/revisão) de quem alterou o status da ocorrência e quando.
- **FR-006**: O sistema DEVE ter uma área pública para exibição de Produções Acadêmicas (tabela `produtos`).
- **FR-007**: O sistema DEVE ter uma área pública para exibição de resumos ou relatos históricos consolidados (tabela `historico`).
- **FR-008**: O ciclo de vida de uma ocorrência DEVE utilizar os status `rascunho`, `em_revisao`, `aprovado`, `publicado` e `rejeitado`.
- **FR-009**: O sistema DEVE permitir publicar e despublicar registros. Despublicar um registro o move de `publicado` para `aprovado`.
- **FR-010**: O sistema DEVE permitir desaprovar um registro, retornando-o de `aprovado` para `rascunho` para correção.
- **FR-011**: Toda transição de status, publicação, despublicação, desaprovação, rejeição e exclusão DEVE gerar entrada imutável no histórico de revisão, com autor, data/hora, ação, status anterior, status novo e comentário quando aplicável.

### Perfis e autorização

| Perfil | Acesso permitido |
| --- | --- |
| Usuário comum | Pesquisa interna somente de registros `aprovado`; não cria, edita, revisa, publica ou exclui registros. |
| Curador | Cria, edita e exclui registros de trabalho; envia registros para revisão; aprova, desaprova, publica, despublica e rejeita registros. Não pode executar ações de curadoria sobre um registro do qual seja autor. |
| Administrador | Todas as ações de curador, além da gestão do sistema, usuários, perfis e permissões. Pode atuar em registros próprios quando necessário, com auditoria obrigatória. |
| Superadministrador | Alterações relativas ao site. O escopo exato de administração de conteúdo institucional será detalhado em feature própria; este perfil não recebe automaticamente permissões sobre ocorrências ou usuários. |

### Ciclo de vida das ocorrências

| Estado atual | Ação | Próximo estado | Perfil autorizado |
| --- | --- | --- | --- |
| `rascunho` | Enviar para revisão | `em_revisao` | Curador ou administrador |
| `em_revisao` | Aprovar | `aprovado` | Curador ou administrador; curador não pode ser o autor do registro |
| `em_revisao` | Rejeitar | `rejeitado` | Curador ou administrador; curador não pode ser o autor do registro |
| `aprovado` | Publicar | `publicado` | Curador ou administrador; curador não pode ser o autor do registro |
| `aprovado` | Desaprovar para correção | `rascunho` | Curador ou administrador; curador não pode ser o autor do registro |
| `publicado` | Despublicar | `aprovado` | Curador ou administrador; curador não pode ser o autor do registro |
| `rejeitado` | Editar e reenviar | `rascunho` | Curador ou administrador |

Cada transição exige uma entrada no histórico de revisão. A justificativa é obrigatória para rejeição; comentários nas demais ações são opcionais.

### Key Entities

- **Users (Shield)**: Autenticação, e-mail, senha e perfil de acesso (`usuario_comum`, `curador`, `administrador` ou `superadministrador`).
- **Ocorrencias**: Entidade central (ID, Título, Descrição, Data, Tipo, Bairro, Cidade, Status, User ID autor).
- **Vitimas**: Pessoas afetadas pela ocorrência (ID, Ocorrencia_ID, Nome, Anonimo (bool), Idade, Genero, Raça, Condição, Desfecho).
- **Agressores**: Agentes do Estado identificados na ocorrência (ID, Ocorrencia_ID, Tipo_Agente, Órgão, Identificado).
- **Ocorrencia_Revisoes**: Histórico de curadoria (ID, Ocorrencia_ID, User_ID, Ação, Status_Anterior, Status_Novo, Comentário).
- **Produtos**: Artigos, livros, relatórios do observatório (ID, Titulo, Autores, Tipo, Resumo, Link, etc).
- **Historico**: Dossiês e documentos históricos (ID, Titulo, Descricao, Periodo, Categoria, PDF).

## Success Criteria *(mandatory)*

### Measurable Outcomes
- **SC-001**: Dados perfeitamente normalizados no SGBD relacional, permitindo extração de estatísticas precisas por cidade, gênero e raça.
- **SC-002**: Separação clara entre Visão Pública (site institucional) e Visão Administrativa (gestão de dados interna).
- **SC-003**: Garantia de integridade referencial (Ex: Vítimas são apagadas ou preservadas corretamente ao apagar uma ocorrência).

## Assumptions
- Assumimos que o banco de dados principal será o MySQL/MariaDB hospedado na mesma VPS.
- Assumimos que o portal principal da PUC-SP apenas fará links (redirecionamentos) para as páginas públicas desta VPS, sem integração direta de APIs no momento.
- A autenticação é restrita à equipe do observatório; cidadãos comuns não criam contas de usuário, no máximo poderão preencher um formulário público no futuro (ainda não especificado).
