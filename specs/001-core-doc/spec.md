# Feature Specification: Core Documentation OVPDH

**Feature Branch**: `001-core-doc`

**Created**: 2026-07-26

**Status**: Draft

**Input**: Levantamento e especificação das funcionalidades, tabelas e regras de negócio atuais do sistema Observatório de Violência Policial e Direitos Humanos (OVPDH).

## Finalidade do Sistema

O sistema registra informações sobre abusos de legalidades e graves violações aos direitos humanos cometidas pelo Estado.

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
Usuários com acesso ao painel (usuários comuns, voluntários, colaboradores e administradores) devem poder registrar e editar detalhes completos de uma ocorrência (dados do fato, vítimas envolvidas e agressores identificados).
**Why this priority**: É o *core business* do observatório (coleta e armazenamento de violações).
**Independent Test**: Testado logando no painel como curador e criando uma nova ocorrência com vítimas e agressores associados.

**Acceptance Scenarios**:
1. **Given** um curador autenticado, **When** ele preenche e salva o formulário de ocorrência, **Then** os dados são salvos com status inicial `rascunho`.
2. **Given** um curador, **When** salva um novo registro, **Then** ele é criado com status `rascunho` e permanece fora da área pública.
3. **Given** um curador, **When** conclui o preenchimento necessário, **Then** ele pode enviar o registro de `rascunho` para `em_revisao`.
4. **Given** um usuário comum, **When** conclui o preenchimento necessário de uma ocorrência de sua autoria, **Then** ele pode enviá-la de `rascunho` para `em_revisao`.

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
3. **Given** um usuário comum autenticado, **When** acessa suas ocorrências, **Then** visualiza todos os registros de sua autoria, independentemente do status.

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
- **FR-012**: O sistema DEVE validar os campos obrigatórios antes de enviar uma ocorrência para revisão.
- **FR-013**: O sistema DEVE permitir salvar uma ocorrência incompleta como `rascunho`.
- **FR-014**: O sistema DEVE identificar dados pessoais sensíveis de vítimas e restringir seu acesso à equipe autorizada.
- **FR-015**: O sistema DEVE registrar data/hora de criação e última alteração, bem como o usuário responsável, para cada ocorrência.
- **FR-016**: O sistema DEVE permitir classificar a ocorrência por um ou mais tipos de violência.
- **FR-017**: O sistema DEVE permitir que o usuário comum crie, edite e envie para revisão somente ocorrências de sua autoria.
- **FR-018**: O sistema DEVE disponibilizar pesquisa interna com filtros compatíveis com o perfil de acesso do usuário.
- **FR-019**: O sistema DEVE gerar indicadores agregados sem expor dados pessoais ou permitir reidentificação de vítimas.

### Dados e validações da ocorrência

Os campos abaixo definem a primeira versão do formulário de ocorrência. Os campos marcados como obrigatórios para revisão podem ficar vazios enquanto o registro estiver em `rascunho`.

| Campo | Descrição | Obrigatório para revisão | Regra de validação |
| --- | --- | --- | --- |
| Título | Identificação breve da ocorrência. | Sim | Entre 10 e 255 caracteres. |
| Data da ocorrência | Data em que o fato ocorreu. | Sim | Não pode ser futura. Quando a data exata for desconhecida, registrar período aproximado no relato. |
| Cidade | Município onde ocorreu o fato. | Sim | Seleção padronizada; permitir cadastrar localidade não encontrada para revisão. |
| Bairro/localidade | Bairro, comunidade, distrito ou equivalente. | Não | Texto de até 150 caracteres. |
| UF | Unidade federativa do local. | Sim | Sigla válida de UF brasileira. |
| Tipos de violência | Classificações aplicáveis ao fato. | Sim | Ao menos uma classificação deve ser selecionada. |
| Relato | Descrição factual do caso e das fontes disponíveis. | Sim | Mínimo de 50 caracteres. Não deve incluir dados pessoais desnecessários. |
| Fonte(s) | Referências usadas no registro, como URL, documento, processo ou entrevista. | Sim | Ao menos uma fonte identificável. |
| Observações internas | Anotações de trabalho que não são públicas. | Não | Nunca exibidas na área pública. |

### Dados de vítimas

Uma ocorrência pode não ter vítima individual identificada, mas deve registrar a informação disponível sobre pessoas ou grupos afetados. Quando houver vítima identificada, os dados abaixo são registrados em entidade própria associada à ocorrência.

| Campo | Obrigatório para revisão | Regra de validação |
| --- | --- | --- |
| Nome | Não | Campo restrito; deixar vazio quando não divulgado ou quando houver risco à pessoa. |
| Anonimizada | Sim | Define se o nome pode aparecer para usuários não autorizados; o padrão é `sim`. |
| Faixa etária/idade | Não | Idade entre 0 e 120 ou faixa etária padronizada. |
| Gênero | Não | Seleção padronizada, com opção “não informado”. |
| Raça/cor | Não | Seleção padronizada, com opção “não informado”. |
| Condição | Não | Ex.: criança/adolescente, pessoa com deficiência, população em situação de rua; permitir múltiplas opções. |
| Desfecho | Não | Ex.: ferimento, ameaça, prisão, desaparecimento, morte, não informado. |

### Dados de agressores e instituições

Uma ocorrência pode ter nenhum, um ou vários agentes ou instituições vinculados. A ausência de identificação individual não impede o registro da ocorrência.

| Campo | Obrigatório para revisão | Regra de validação |
| --- | --- | --- |
| Tipo de agente | Sim, quando houver agressor registrado | Categoria padronizada do agente estatal envolvido. |
| Órgão/instituição | Não | Nome do órgão, corporação ou instituição relacionada. |
| Identificado | Sim, quando houver agressor registrado | Indica se há identificação individual confirmada. |
| Nome/identificação | Não | Campo restrito; só deve ser preenchido quando houver fonte que o sustente. |
| Observações | Não | Contexto sobre a participação atribuída ao agente ou instituição. |

### Regras de proteção de dados

- Dados pessoais de vítimas, testemunhas e pessoas vulneráveis não são exibidos na área pública.
- O nome de vítima só pode ser exibido internamente a curadores e administradores quando necessário ao trabalho de documentação; registros anonimizados devem ocultá-lo por padrão.
- Campos sensíveis devem ser evitados quando não forem essenciais para documentar a violação.
- A publicação exige revisão de dados identificáveis, fontes e texto do relato para evitar exposição indevida ou risco às pessoas envolvidas.

### Listas padronizadas iniciais

As listas a seguir devem ser apresentadas como opções de cadastro. Todas devem incluir `não informado` quando aplicável, para diferenciar ausência de informação de dado não coletado. A equipe administrativa poderá revisar e ampliar essas listas sem alterar os registros já existentes.

#### Tipos de violência

- Agressão física ou tortura
- Ameaça, intimidação ou coação
- Abuso de autoridade
- Detenção, prisão ou abordagem arbitrária
- Desaparecimento forçado
- Execução, homicídio ou morte decorrente de intervenção do Estado
- Invasão de domicílio ou violação de propriedade
- Racismo, discriminação ou violência motivada por preconceito
- Violência sexual
- Violação de liberdade de expressão, manifestação ou organização
- Outros

#### Tipo de agente estatal

- Polícia Militar
- Polícia Civil
- Polícia Federal
- Polícia Penal
- Guarda Civil/Municipal
- Forças Armadas
- Sistema prisional ou socioeducativo
- Agente público de outro órgão
- Instituição estatal não identificada
- Não informado

#### Raça/cor

- Branca
- Preta
- Parda
- Amarela
- Indígena
- Não informado

#### Gênero

- Mulher cisgênero
- Homem cisgênero
- Mulher transgênero
- Homem transgênero
- Pessoa não binária
- Outro
- Não informado

#### Faixa etária

- 0 a 11 anos
- 12 a 17 anos
- 18 a 29 anos
- 30 a 59 anos
- 60 anos ou mais
- Não informado

#### Desfecho da violação

- Sem ferimento informado
- Ferimento físico
- Ferimento psicológico
- Ameaça ou intimidação
- Detenção ou prisão
- Desaparecimento
- Morte
- Não informado

#### Condições e grupos afetados

- Criança ou adolescente
- Pessoa idosa
- Pessoa com deficiência
- Pessoa LGBTQIAPN+
- Pessoa negra
- Pessoa indígena
- Pessoa migrante ou refugiada
- Pessoa em situação de rua
- Pessoa privada de liberdade
- Outro grupo em situação de vulnerabilidade

### Perfis e autorização

| Perfil | Acesso permitido |
| --- | --- |
| Usuário comum | Consulta interna de registros `aprovado`; cria, edita e exclui somente seus próprios registros em `rascunho` ou `rejeitado`; envia seus próprios registros para revisão. Não revisa, aprova, rejeita, publica ou despublica ocorrências. |
| Curador | Cria, edita e exclui registros de trabalho; envia registros para revisão; aprova, desaprova, publica, despublica e rejeita registros. Não pode executar ações de curadoria sobre um registro do qual seja autor. |
| Administrador | Todas as ações de curador, além da gestão do sistema, usuários, perfis e permissões. Pode atuar em registros próprios quando necessário, com auditoria obrigatória. |
| Superadministrador | Alterações relativas ao site. O escopo exato de administração de conteúdo institucional será detalhado em feature própria; este perfil não recebe automaticamente permissões sobre ocorrências ou usuários. |

### Ciclo de vida das ocorrências

| Estado atual | Ação | Próximo estado | Perfil autorizado |
| --- | --- | --- | --- |
| `rascunho` | Enviar para revisão | `em_revisao` | Autor do registro, curador ou administrador |
| `em_revisao` | Aprovar | `aprovado` | Curador ou administrador; curador não pode ser o autor do registro |
| `em_revisao` | Rejeitar | `rejeitado` | Curador ou administrador; curador não pode ser o autor do registro |
| `aprovado` | Publicar | `publicado` | Curador ou administrador; curador não pode ser o autor do registro |
| `aprovado` | Desaprovar para correção | `rascunho` | Curador ou administrador; curador não pode ser o autor do registro |
| `publicado` | Despublicar | `aprovado` | Curador ou administrador; curador não pode ser o autor do registro |
| `rejeitado` | Editar e reenviar | `rascunho` | Autor do registro, curador ou administrador |

Cada transição exige uma entrada no histórico de revisão. A justificativa é obrigatória para rejeição; comentários nas demais ações são opcionais.

### Telas e fluxos principais

#### Painel inicial

Após a autenticação, o sistema apresenta atalhos conforme o perfil:

- **Usuário comum**: nova ocorrência, minhas ocorrências e consulta interna de registros aprovados.
- **Curador**: fila de revisão, nova ocorrência, registros sob sua responsabilidade e consulta interna.
- **Administrador**: recursos do curador, gestão de usuários, perfis e configurações do sistema.
- **Superadministrador**: gestão do conteúdo institucional do site, conforme permissões específicas.

#### Cadastro de ocorrência

1. O usuário seleciona **Nova ocorrência**.
2. Preenche os dados do fato, vítimas, agressores/instituições e fontes.
3. O sistema permite salvar como `rascunho` a qualquer momento.
4. Ao selecionar **Enviar para curadoria**, o sistema valida os campos obrigatórios.
5. Se a validação for aprovada, o status passa para `em_revisao`, uma entrada é criada no histórico e o registro aparece na fila de curadoria.
6. Se houver pendências, o sistema destaca os campos a corrigir e mantém o registro como `rascunho`.

O usuário comum só pode alterar ou excluir registros de sua autoria enquanto estiverem em `rascunho` ou `rejeitado`. Depois do envio, ele pode acompanhar o status e os comentários da curadoria, mas não pode editar o conteúdo até que o registro retorne para correção.

#### Minhas ocorrências

A tela lista apenas registros do autor autenticado, com título, data do fato, cidade, status, data da última alteração e último comentário de curadoria. Deve permitir filtrar por status e pesquisar por título ou cidade.

#### Fila de curadoria

Disponível a curadores e administradores, exibe registros em `em_revisao`, ordenados do mais antigo para o mais recente. A tela mostra autor, data de envio, local, tipo de violência e alertas de dados incompletos ou sensíveis.

Ao abrir uma ocorrência, o curador pode:

- aprovar o registro;
- rejeitar o registro, com justificativa obrigatória;
- incluir comentário de orientação;
- consultar o histórico de revisões e as fontes registradas.

Um curador não pode decidir sobre registros de sua própria autoria. O administrador pode fazê-lo em situação excepcional, com justificativa registrada no histórico.

#### Publicação e consulta interna

Registros `aprovado` podem ser consultados internamente por usuários autenticados. Somente curadores e administradores podem publicá-los ou despublicá-los. Antes de publicar, o sistema exibe uma confirmação e exige revisão dos campos públicos, dados pessoais e fontes.

#### Área pública

A área pública exibe apenas ocorrências `publicado` e apenas os campos autorizados para divulgação. Ela deve oferecer busca e filtros por período, cidade, UF e tipo de violência, sem expor dados pessoais ou observações internas.

### Pesquisa e indicadores

#### Pesquisa interna

Usuários autenticados podem consultar ocorrências `aprovado` e `publicado`. Além disso, cada autor pode consultar seus próprios registros em qualquer status. A pesquisa deve permitir combinar os filtros abaixo:

- período da ocorrência;
- UF, cidade e bairro/localidade;
- um ou mais tipos de violência;
- tipo de agente estatal e órgão/instituição;
- status do registro, limitado aos perfis que podem visualizá-lo;
- raça/cor, gênero, faixa etária, condição e desfecho da vítima;
- autor do registro, para curadores e administradores;
- texto livre em título, relato e fontes, respeitando as permissões de acesso.

Dados pessoais, observações internas e campos anonimizados não devem integrar a busca para usuários sem autorização específica.

#### Indicadores internos

Curadores e administradores devem ter acesso a um painel com indicadores agregados, filtráveis por período e território:

- número de ocorrências por status;
- ocorrências por tipo de violência;
- ocorrências por UF, cidade e bairro/localidade;
- vítimas por raça/cor, gênero, faixa etária, condição e desfecho;
- ocorrências por tipo de agente e órgão/instituição;
- tempo médio entre envio para revisão, aprovação e publicação;
- quantidade de registros recebidos, aprovados e rejeitados por período.

Os indicadores devem mostrar somente dados agregados. Quando um recorte resultar em quantidade muito pequena de pessoas, o sistema deve suprimir ou agrupar o dado para evitar reidentificação.

#### Indicadores públicos

A área pública poderá apresentar indicadores apenas de ocorrências `publicado`, com metodologia e período de referência visíveis. A primeira versão deve priorizar totais por período, localidade, tipo de violência e desfecho, sem permitir acesso a dados pessoais ou a contagens que identifiquem vítimas.

### Preparação para gamificação acadêmica futura

A gamificação não faz parte da primeira versão do sistema. A estrutura deve, porém, permitir que futuramente estudantes de graduação e de programas da faculdade participem de atividades de documentação e pesquisa supervisionadas.

#### Princípios obrigatórios

- Pontos, distintivos e classificações só podem considerar registros aprovados pela curadoria; cadastrar muitos rascunhos não gera pontuação.
- A qualidade, a completude, o uso de fontes verificáveis e o cumprimento de prazos devem ter mais peso do que a quantidade de cadastros.
- A avaliação e qualquer pontuação dependem de supervisão de curador ou administrador.
- Rankings não podem ser públicos por padrão e jamais podem associar estudantes a dados pessoais de vítimas, testemunhas ou pessoas investigadas.
- A participação deve respeitar regras institucionais, consentimento quando aplicável, proteção de dados e critérios pedagógicos definidos pela faculdade.
- Mecanismos de competição não podem interferir na independência da curadoria nem acelerar a publicação de casos sem revisão adequada.

#### Ganchos de dados

Quando a funcionalidade for implementada, o sistema poderá acrescentar as entidades abaixo sem alterar o cadastro de ocorrências existente:

| Entidade futura | Finalidade |
| --- | --- |
| `programas_academicos` | Identifica graduação, disciplina, projeto ou programa participante. |
| `participacoes_academicas` | Vincula usuários participantes a programas e períodos de atividade. |
| `contribuicoes_academicas` | Registra contribuições elegíveis, como cadastro, complementação ou pesquisa de fontes, vinculadas à ocorrência e à decisão da curadoria. |
| `pontuacoes_academicas` | Armazena créditos e critérios aplicados após validação, com histórico auditável. |
| `conquistas_academicas` | Define distintivos por objetivos pedagógicos e de qualidade. |

Uma contribuição acadêmica deve manter vínculo com a ocorrência, o autor, o programa, o tipo de atividade, a data e a decisão da curadoria. A exclusão ou rejeição de um registro deve suspender qualquer pontuação relacionada até nova decisão de aprovação.

### Key Entities

- **Users (Shield)**: Autenticação, e-mail, senha e perfil de acesso (`usuario_comum`, `curador`, `administrador` ou `superadministrador`).
- **Ocorrencias**: Entidade central (ID, Título, Descrição, Data, Tipo, Bairro, Cidade, Status, User ID autor).
- **Vitimas**: Pessoas afetadas pela ocorrência (ID, Ocorrencia_ID, Nome, Anonimo (bool), Idade, Genero, Raça, Condição, Desfecho).
- **Agressores**: Agentes do Estado identificados na ocorrência (ID, Ocorrencia_ID, Tipo_Agente, Órgão, Identificado).
- **Ocorrencia_Revisoes**: Histórico de curadoria (ID, Ocorrencia_ID, User_ID, Ação, Status_Anterior, Status_Novo, Comentário).
- **Produtos**: Artigos, livros, relatórios do observatório (ID, Titulo, Autores, Tipo, Resumo, Link, etc).
- **Historico**: Dossiês e documentos históricos (ID, Titulo, Descricao, Periodo, Categoria, PDF).

### Modelo de dados e compatibilidade com o legado

O sistema já possui as tabelas `ocorrencias`, `vitimas`, `agressores`, `ocorrencia_revisoes`, `historico` e `produtos`. A evolução da base deve preservar essas tabelas, seus identificadores e os registros existentes. Dados anteriores devem continuar aparecendo para os usuários de acordo com as permissões e status aplicáveis, sem exigir recadastramento.

#### Entidades existentes e evolução prevista

| Entidade existente | Uso preservado | Evolução não destrutiva |
| --- | --- | --- |
| `ocorrencias` | Mantém ID, título, descrição, data, local, fontes, evidências, status e autoria já registrados. | Adicionar campos somente quando necessários, como `observacoes_internas`; normalizar classificações em tabelas auxiliares sem remover `tipo_violencia` e `fontes`. |
| `vitimas` | Mantém todos os registros e campos pessoais já existentes. | Acrescentar classificação de faixa etária se necessário. O campo `anonimo` existente continua sendo a referência de visibilidade. |
| `agressores` | Mantém agente, órgão, unidade, identificação e observações já registrados. | Padronizar categorias por tabelas de referência ou validação, preservando o texto legado original. |
| `ocorrencia_revisoes` | Mantém o histórico de ações já registrado. | Novas ações e comentários são apenas acrescentados; entradas existentes jamais são alteradas ou apagadas. |
| `historico` e `produtos` | Mantêm o acervo e as publicações existentes, inclusive caminhos de arquivos. | Evolução independente, sem mudança de identificadores ou de URLs de arquivos sem redirecionamento. |

#### Novas tabelas previstas

| Tabela | Relação | Finalidade |
| --- | --- | --- |
| `tipos_violencia` | catálogo | Mantém a lista padronizada de tipos de violência. |
| `ocorrencia_tipos_violencia` | N:N com `ocorrencias` | Permite associar mais de um tipo de violência a uma ocorrência, preservando o valor legado em `ocorrencias.tipo_violencia`. |
| `ocorrencia_fontes` | 1:N com `ocorrencias` | Registra cada fonte de forma estruturada: tipo, referência/URL, descrição e data de acesso. |
| `condicoes_vitima` | catálogo | Mantém condições e grupos afetados padronizados. |
| `vitima_condicoes` | N:N com `vitimas` | Permite registrar múltiplas condições para uma vítima. |

As tabelas acadêmicas futuras (`programas_academicos`, `participacoes_academicas`, `contribuicoes_academicas`, `pontuacoes_academicas` e `conquistas_academicas`) só devem ser criadas quando o módulo de gamificação for aprovado institucionalmente.

#### Regras obrigatórias para migrações

- Toda mudança de estrutura deve ser feita em nova migration incremental; migrations já executadas não devem ser reescritas.
- Nenhuma migration pode apagar tabelas, colunas, arquivos ou registros legados.
- Chaves primárias existentes devem ser preservadas. Novas tabelas devem referenciá-las por chave estrangeira, sem recriar os registros.
- Antes de cada migration, deve existir backup verificável da base e validação em cópia de homologação.
- Campos e tabelas novas devem aceitar `NULL` ou possuir valor padrão seguro enquanto dados antigos não tiverem informação correspondente.
- A aplicação deve tratar valores legados fora das listas atuais como `legado não classificado`, exibindo o texto original e permitindo sua classificação posterior por curador.
- A migração de texto livre para dados estruturados deve copiar, e não substituir, os valores existentes. Por exemplo, `fontes` permanece como histórico textual mesmo depois da criação de `ocorrencia_fontes`.
- Registros com `deleted_at` preenchido permanecem excluídos conforme a regra atual e não devem ser reexpostos automaticamente.
- Ocorrências não devem sofrer exclusão física na aplicação. A exclusão deve usar `deleted_at`, preservando vítimas, agressores e histórico de revisão associados.

#### Compatibilidade de status

O legado usa os status `rascunho`, `em_revisao`, `aprovado`, `publicado` e `arquivado`; a nova especificação acrescenta `rejeitado`. O status `arquivado` deve ser preservado, sem conversão automática, e continuar fora da consulta pública e dos indicadores correntes. A inclusão de `rejeitado` deve ampliar a lista de valores aceitos, nunca invalidar registros existentes.

#### Critérios de aceite da migração

1. A quantidade de registros legados em cada tabela é a mesma antes e depois da migration.
2. Todos os IDs existentes continuam acessíveis pelas mesmas rotas ou por redirecionamento compatível.
3. Ocorrências, vítimas, agressores, produtos e documentos históricos existentes aparecem nas respectivas áreas com as mesmas regras de acesso anteriores.
4. Nenhum dado pessoal passa a ser público em consequência da migration.
5. Relatórios novos incluem os registros legados quando houver dados suficientes para o filtro; quando não houver, o sistema os identifica como não classificados, sem ocultá-los.

### Segurança, privacidade e operação

#### Requisitos de segurança

- **FR-020**: O sistema DEVE usar contas individuais e autenticação provida pelo CodeIgniter Shield; contas compartilhadas não são permitidas.
- **FR-021**: O sistema DEVE aplicar controle de acesso por perfil e por autoria do registro em todas as rotas e operações, inclusive nas APIs e downloads de arquivos.
- **FR-022**: O sistema DEVE exigir conexão HTTPS em produção e proteger cookies de sessão com os atributos `Secure`, `HttpOnly` e `SameSite` adequados.
- **FR-023**: O sistema DEVE registrar eventos de segurança e ações relevantes de dados em trilha de auditoria imutável.
- **FR-024**: O sistema DEVE restringir anexos e uploads a tipos, tamanhos e locais de armazenamento permitidos, impedindo execução direta de arquivos enviados.
- **FR-025**: O sistema DEVE preservar dados pessoais e registros de auditoria em backup protegido, com acesso limitado à equipe autorizada.
- **FR-026**: O sistema DEVE permitir desativar uma conta sem apagar seus registros, autoria ou histórico de auditoria.

#### Matriz de acesso a ocorrências

| Ação | Usuário comum | Curador | Administrador | Superadministrador |
| --- | --- | --- | --- | --- |
| Criar ocorrência | Sim | Sim | Sim | Não, salvo permissão específica |
| Ver rascunho/em revisão | Apenas próprios | Todos | Todos | Não |
| Editar rascunho/rejeitado | Apenas próprios | Registros de trabalho autorizados | Todos | Não |
| Enviar para curadoria | Apenas próprios | Sim | Sim | Não |
| Aprovar ou rejeitar | Não | Sim, exceto autoria própria | Sim, inclusive autoria própria com justificativa | Não |
| Publicar/despublicar | Não | Sim, exceto autoria própria | Sim, inclusive autoria própria com justificativa | Não |
| Ver dados pessoais de vítimas | Apenas quando necessários em registro próprio | Sim, conforme necessidade de curadoria | Sim | Não por padrão |
| Ver histórico de revisão | Apenas próprios | Todos | Todos | Não |
| Excluir logicamente | Apenas próprios em `rascunho`/`rejeitado` | Registros de trabalho autorizados | Todos | Não |

Permissões adicionais devem ser concedidas por regra explícita e auditável; nunca por acesso direto ao banco de dados ou por esconder botões na interface.

#### Classificação e tratamento de dados

| Classe | Exemplos | Tratamento mínimo |
| --- | --- | --- |
| Público | Dados de ocorrências já publicadas e conteúdos institucionais aprovados. | Pode ser exibido no site público após revisão. |
| Interno | Rascunhos, comentários de curadoria, fontes de trabalho e indicadores internos. | Acesso apenas a usuários autenticados e autorizados. |
| Restrito | Nome, identificação, contato e relato sensível de vítimas, testemunhas ou agentes; anexos com dados identificáveis. | Acesso por necessidade de trabalho, sem exibição pública e com registro de acesso quando tecnicamente viável. |

O sistema deve coletar somente os dados pessoais necessários para documentar a ocorrência e deve oferecer orientação no formulário sobre anonimização e dados sensíveis. Solicitações de acesso, correção ou eliminação de dados devem ser avaliadas pela coordenação responsável; nenhuma exclusão deve ocorrer automaticamente quando houver obrigação de preservação, interesse público, pesquisa ou outra base legal aplicável.

#### Auditoria

A trilha de auditoria deve registrar, no mínimo: usuário, data/hora, endereço de origem quando disponível, ação, entidade afetada, identificador do registro, valores de status anterior e novo, comentário e resultado da operação. Alterações em dados pessoais ou em permissões também devem gerar evento de auditoria.

O histórico de revisão de uma ocorrência é imutável para usuários da aplicação. Correções administrativas excepcionais devem criar um novo evento que explique a correção, sem apagar a evidência do evento anterior.

#### Backup e recuperação

- Deve haver backup automático e criptografado da base de dados e dos arquivos enviados.
- A periodicidade, a retenção e o local de armazenamento devem ser definidos pela coordenação e pela política institucional antes da entrada em produção.
- Backups devem ser mantidos separados do servidor principal e testados periodicamente por meio de restauração em ambiente seguro.
- O procedimento de restauração deve documentar responsável, data, escopo, motivo, resultado e quaisquer dados recuperados.
- Antes de migrations em produção, deve ser criado backup verificável e testado o processo em ambiente de homologação com cópia protegida dos dados.

#### Retenção e resposta a incidentes

A política de retenção deve ser aprovada pela instituição responsável, com participação da coordenação do observatório e orientação jurídica/encarregado de dados quando aplicável. Ela deve definir prazos por classe de dado, responsáveis pela revisão e procedimento de descarte seguro. Até essa aprovação, dados e trilhas de auditoria não devem ser eliminados automaticamente.

Em caso de incidente de segurança, a equipe deve conter o acesso, preservar evidências, avaliar o impacto sobre titulares de dados e seguir o procedimento institucional de comunicação e resposta. O sistema deve permitir revogar sessões e desativar contas comprometidas sem apagar registros históricos.

### Acervo Histórico e Produções Acadêmicas

#### Acervo Histórico

O Acervo Histórico apresenta dossiês, documentos e relatos consolidados sobre o tema do observatório. Cada item usa a entidade `historico` e deve conter os dados abaixo.

| Campo | Obrigatório | Regra |
| --- | --- | --- |
| Título | Sim | Entre 5 e 255 caracteres. |
| Descrição | Sim | Resumo acessível do conteúdo. |
| Período | Não | Texto livre para o período histórico, complementado por anos quando conhecidos. |
| Ano inicial e final | Não | Ano com quatro dígitos; o ano final não pode ser menor que o inicial. |
| Categoria | Sim | Categoria padronizada definida pela administração do acervo. |
| Autoria/créditos | Não | Pessoa, instituição ou coleção responsável pelo material. |
| Arquivo principal | Não | PDF ou outro formato documental permitido. |
| Miniatura | Não | Imagem de capa acessível, com texto alternativo obrigatório. |
| Ativo/publicado | Sim | Somente itens ativos são exibidos publicamente. |

A página pública deve oferecer busca por título, descrição, período, categoria e autoria, além de filtros por categoria e intervalo de anos. Itens inativos permanecem preservados no banco, mas fora da área pública.

#### Produções Acadêmicas

O módulo de Produções Acadêmicas reúne artigos, livros, relatórios, monografias, dissertações, teses e outros materiais relacionados ao observatório. Cada item usa a entidade `produtos` e deve conter os dados abaixo.

| Campo | Obrigatório | Regra |
| --- | --- | --- |
| Título | Sim | Entre 5 e 255 caracteres. |
| Autores | Sim | Créditos completos, em texto estruturado para futura normalização. |
| Tipo | Sim | Artigo, livro, capítulo, relatório, TCC, dissertação, tese ou outro. |
| Resumo | Não | Síntese do conteúdo em linguagem acessível. |
| Ano | Sim | Ano com quatro dígitos, sem data futura. |
| Veículo/publicação | Não | Periódico, editora, evento ou instituição. |
| DOI | Não | Deve seguir formato de DOI quando informado. |
| Link externo | Não | URL válida e acessível por HTTPS quando disponível. |
| Arquivo | Não | Documento autorizado para download, somente quando houver permissão de divulgação. |
| Palavras-chave | Não | Termos separados e pesquisáveis. |
| Ativo/publicado | Sim | Somente itens ativos são exibidos publicamente. |

A página pública deve permitir busca por título, autor, palavra-chave e resumo, além de filtros por tipo e ano. Links externos e arquivos devem ser apresentados de forma clara, indicando quando o conteúdo está hospedado fora do sistema.

#### Gestão editorial e permissões

| Ação | Usuário comum | Curador | Administrador | Superadministrador |
| --- | --- | --- | --- | --- |
| Consultar itens públicos | Sim | Sim | Sim | Sim |
| Consultar itens inativos | Não | Não, salvo permissão específica | Sim | Sim |
| Criar ou editar rascunho editorial | Não | Não, salvo permissão específica | Sim | Sim |
| Ativar, desativar ou publicar item | Não | Não, salvo permissão específica | Sim | Sim |
| Excluir logicamente item | Não | Não | Sim | Sim |

Cada alteração editorial deve guardar autoria e data/hora. A remoção de item ou arquivo deve ser lógica sempre que possível, preservando o registro e permitindo recuperação pela administração.

#### Arquivos, direitos e acessibilidade

- Antes do envio, o responsável deve confirmar que possui autorização para disponibilizar o arquivo, miniatura e metadados.
- Arquivos não podem conter dados pessoais ou material protegido sem base de divulgação adequada.
- O sistema deve aceitar somente formatos permitidos e verificar tamanho, tipo de conteúdo e nome do arquivo antes de armazená-lo.
- Todo arquivo público deve ter título descritivo; imagens devem conter texto alternativo; PDFs devem ser preferencialmente pesquisáveis e acessíveis.
- A substituição de arquivo deve preservar referência ao arquivo anterior no histórico editorial ou permitir sua recuperação administrativa.

## Success Criteria *(mandatory)*

### Measurable Outcomes
- **SC-001**: Dados perfeitamente normalizados no SGBD relacional, permitindo extração de estatísticas precisas por cidade, gênero e raça.
- **SC-002**: Separação clara entre Visão Pública (site institucional) e Visão Administrativa (gestão de dados interna).
- **SC-003**: Garantia de integridade referencial (Ex: Vítimas são apagadas ou preservadas corretamente ao apagar uma ocorrência).

## Assumptions
- Assumimos que o banco de dados principal será o MySQL/MariaDB hospedado na mesma VPS.
- Assumimos que o portal principal da PUC-SP apenas fará links (redirecionamentos) para as páginas públicas desta VPS, sem integração direta de APIs no momento.
- A autenticação é restrita à equipe do observatório; cidadãos comuns não criam contas de usuário, no máximo poderão preencher um formulário público no futuro (ainda não especificado).
