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
2. **Given** um usuário anônimo, **When** acessa o Acervo, **Then** ele visualiza a lista de ocorrências consolidadas e publicadas.

---

### User Story 2 - Cadastro e Gestão de Ocorrências (Priority: P1)
Usuários com acesso ao painel (voluntários, colaboradores, admins) devem poder registrar e editar detalhes completos de uma ocorrência (dados do fato, vítimas envolvidas e agressores identificados).
**Why this priority**: É o *core business* do observatório (coleta e armazenamento de violações).
**Independent Test**: Testado logando no painel e criando uma nova ocorrência com vítimas e agressores associados.

**Acceptance Scenarios**:
1. **Given** um colaborador autenticado, **When** ele preenche e salva o formulário de ocorrência, **Then** os dados são salvos com status inicial (ex: rascunho ou em_revisao).

---

### User Story 3 - Curadoria e Revisão (Priority: P2)
Administradores e coordenadores devem revisar ocorrências cadastradas pela equipe antes que elas sejam aprovadas e (se aplicável) se tornem públicas ou consolidadas no banco de dados.
**Why this priority**: Garante a veracidade e a qualidade dos dados (evita *fake news* ou dados inconsistentes).
**Independent Test**: Testado através do fluxo de mudança de status da ocorrência (de `em_revisao` para `aprovado` ou `publicado`) e registro do histórico de revisão na tabela `ocorrencia_revisoes`.

---

### User Story 4 - Gestão de Usuários e Perfis (Priority: P2)
O Super Administrador ou Administrador deve poder gerenciar os membros da equipe (voluntários, advogados, acadêmicos, etc), criando novas contas e atribuindo grupos de permissão (Roles).
**Why this priority**: O sistema possui acesso restrito e várias mãos alimentam o banco, necessitando de auditoria.

### Edge Cases
- O que acontece quando uma ocorrência tenta ser aprovada mas faltam dados obrigatórios (ex: data ou cidade)?
- Como o sistema lida com vítimas que exigem anonimato absoluto por segurança?
- Como o sistema protege os dados das ocorrências em rascunho contra vazamentos públicos?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE permitir cadastro de ocorrências detalhando Local (Cidade/Bairro), Data, Tipo de Violência e Relato.
- **FR-002**: O sistema DEVE permitir associar múltiplas Vítimas a uma única Ocorrência (relacionamento 1:N).
- **FR-003**: O sistema DEVE permitir associar múltiplos Agressores/Instituições a uma única Ocorrência (relacionamento 1:N).
- **FR-004**: O sistema DEVE possuir um módulo de Autenticação (implementado via CodeIgniter Shield) com suporte a Grupos de Permissão (Admin, Voluntario, Colaborador, Advogado, Academico, Ativista).
- **FR-005**: O sistema DEVE manter um registro (log/revisão) de quem alterou o status da ocorrência e quando.
- **FR-006**: O sistema DEVE ter uma área pública para exibição de Produções Acadêmicas (tabela `produtos`).
- **FR-007**: O sistema DEVE ter uma área pública para exibição de resumos ou relatos históricos consolidados (tabela `historico`).

### Key Entities

- **Users (Shield)**: Autenticação, e-mail, senha e grupo/perfil de acesso.
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
