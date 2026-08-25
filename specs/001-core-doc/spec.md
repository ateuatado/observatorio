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
- **FR-027**: O sistema DEVE registrar uma ou várias violações dentro de cada ocorrência, com tipo, conduta, enquadramento jurídico, meios/instrumentos e regiões/lesões quando informados.
- **FR-028**: O sistema DEVE permitir vincular cada vítima, agente/instituição e fonte às violações específicas às quais se relaciona.
- **FR-029**: O sistema DEVE registrar fontes individualmente e permitir depoimentos e arquivos associados, com classe de acesso própria.
- **FR-030**: Valores usados em filtros e indicadores DEVEM usar catálogos administráveis e preservar de forma distinta `não informado`, `não coletado`, `não se aplica` e `dado ausente no legado`.
- **FR-031**: O sistema DEVE armazenar localização territorial padronizada e, quando houver, geometria PostGIS com precisão, fonte e regra de visibilidade pública.
- **FR-032**: Toda entidade e relação importada do legado DEVE manter tabela, identificador de origem, lote, resultado e evidência da transformação.
- **FR-033**: Denúncias recebidas DEVEM permanecer separadas das ocorrências verificadas; a conversão ou vinculação não pode apagar o registro de entrada nem expor o contato do denunciante.
- **FR-034**: Relato público, observações internas, dados identificáveis e localização exata DEVEM ser campos ou estruturas distinguíveis, com autorização independente.
- **FR-035**: Nomes individuais de vítimas, agentes, denunciantes, depoentes e testemunhas NÃO DEVEM ser exibidos publicamente.
- **FR-036**: A publicação de documento sensível DEVE usar uma cópia derivada na qual o conteúdo protegido tenha sido removido permanentemente; sobreposição visual de tarja não é anonimização suficiente.
- **FR-037**: Acesso acadêmico a dados restritos DEVE ser concedido por projeto e ocorrência, com prazo, justificativa, supervisor e auditoria.
- **FR-038**: O sistema DEVE impedir sobrescrita silenciosa de alterações concorrentes e manter comparação de versões para campos relevantes.
- **FR-039**: A edição de ocorrência publicada DEVE criar versão de trabalho sem alterar a versão pública até nova aprovação e publicação.
- **FR-040**: Curadores DEVEM poder sugerir valores de catálogo durante o cadastro; somente administradores podem homologar, fundir, renomear ou inativar esses valores.
- **FR-041**: O sistema DEVE permitir criar coleções editoriais de ocorrências publicadas, com título, descrição, ordem e estado de publicação, sem copiar dados restritos para a coleção.

### Dados e validações da ocorrência

Os campos abaixo definem a primeira versão do formulário de ocorrência. Os campos marcados como obrigatórios para revisão podem ficar vazios enquanto o registro estiver em `rascunho`.

| Campo | Descrição | Obrigatório para revisão | Regra de validação |
| --- | --- | --- | --- |
| Título | Identificação breve da ocorrência. | Sim | Entre 10 e 255 caracteres. |
| Data da ocorrência | Data em que o fato ocorreu. | Sim | Não pode ser futura. Quando a data exata for desconhecida, registrar período aproximado no relato. |
| Precisão/período da data | Indica se a data é exata, aproximada ou um intervalo. | Sim | Não se deve inventar uma data exata para fatos com referência temporal imprecisa. |
| Período do dia | Manhã, tarde, noite ou madrugada. | Não | Usa catálogo; não é deduzido automaticamente quando a fonte não informa. |
| Cidade | Município onde ocorreu o fato. | Sim | Seleção padronizada; permitir cadastrar localidade não encontrada para revisão. |
| Bairro/localidade | Bairro, comunidade, distrito ou equivalente. | Não | Texto de até 150 caracteres. |
| UF | Unidade federativa do local. | Sim | Sigla válida de UF brasileira. |
| Endereço/referência do local | Informação de trabalho para localizar o fato. | Não | Endereço exato é restrito por padrão; a apresentação pública usa nível territorial aprovado. |
| Tipos de violência | Classificações aplicáveis ao fato. | Sim | Ao menos uma classificação deve ser selecionada. |
| Relato | Descrição factual do caso e das fontes disponíveis. | Sim | Mínimo de 50 caracteres. Não deve incluir dados pessoais desnecessários. |
| Resumo público | Texto revisado para apresentação externa. | Sim para publicar | Não reutiliza automaticamente observações internas nem dados restritos. |
| Fonte(s) | Referências usadas no registro, como URL, documento, processo ou entrevista. | Sim | Ao menos uma fonte identificável. |
| Observações internas | Anotações de trabalho que não são públicas. | Não | Nunca exibidas na área pública. |
| Relevância interna | Marca de priorização do trabalho. | Não | Não é apresentada como julgamento público de gravidade sem critério homologado. |

### Dados de vítimas

Uma ocorrência pode não ter vítima individual identificada, mas deve registrar a informação disponível sobre pessoas ou grupos afetados. Quando houver vítima identificada, os dados abaixo são registrados em entidade própria associada à ocorrência.

| Campo | Obrigatório para revisão | Regra de validação |
| --- | --- | --- |
| Nome | Não | Campo sempre restrito e nunca exibido publicamente; deixar vazio quando desnecessário ou não sustentado por fonte. |
| Anonimizada | Sim | Compatibilidade e controle interno. Na área pública, toda vítima é anonimizada independentemente deste valor. |
| Faixa etária/idade | Não | Idade entre 0 e 120 internamente; somente faixa etária ampla pode ser publicada. |
| Gênero | Não | Seleção padronizada, com opção “não informado”. |
| Raça/cor | Não | Seleção padronizada, com opção “não informado”. |
| Condição | Não | Ex.: criança/adolescente, pessoa com deficiência, população em situação de rua; permitir múltiplas opções. |
| Desfecho | Não | Ex.: ferimento, ameaça, prisão, desaparecimento, morte, não informado. |

### Dados de agentes e instituições envolvidos

Uma ocorrência pode ter nenhum, um ou vários agentes ou instituições envolvidos. O vínculo registra uma participação atribuída e sustentada pelas fontes, sem antecipar conclusão jurídica. A ausência de identificação individual não impede o registro da ocorrência.

| Campo | Obrigatório para revisão | Regra de validação |
| --- | --- | --- |
| Tipo de agente | Sim, quando houver agressor registrado | Categoria padronizada do agente estatal envolvido. |
| Órgão/instituição | Não | Nome do órgão, corporação ou instituição relacionada. |
| Identificado | Sim, quando houver agressor registrado | Indica se há identificação individual confirmada. |
| Nome/identificação | Não | Campo sempre restrito; só deve ser preenchido quando houver fonte que o sustente e nunca é exibido publicamente. |
| Observações | Não | Contexto sobre a participação atribuída ao agente ou instituição. |

### Regras de proteção de dados

- Nomes de vítimas, agentes públicos, denunciantes, depoentes e testemunhas nunca são exibidos na área pública.
- Curadores e administradores autorizados podem consultar os dados completos no ambiente privado; cada consulta ou download de dado restrito gera auditoria.
- Participantes acadêmicos veem dados anonimizados por padrão. O acesso à identidade depende de autorização específica por projeto e ocorrência, com prazo, justificativa, supervisão e auditoria.
- Instituição, corporação, unidade, patente, cargo ou função do agente podem ser publicados após curadoria, desde que a combinação não permita identificação individual indireta; quando houver risco, o dado deve ser generalizado.
- Município e UF podem ser públicos. Bairro ou comunidade dependem de curadoria. Endereço exato, número e coordenada precisa são sempre restritos; mapas públicos usam área ou ponto generalizado.
- Faixa etária, gênero, raça/cor e condição ou grupo afetado podem ser publicados quando relevantes e quando a combinação não permitir reidentificação. Idade exata, profissão, religião, orientação sexual e deficiência permanecem restritas por padrão.
- Campos sensíveis devem ser evitados quando não forem essenciais para documentar a violação.
- Relato interno e resumo público são conteúdos independentes; o sistema nunca copia automaticamente o relato interno para publicação.
- Documentos sensíveis mantêm um original restrito e uma versão pública derivada. A tarja deve remover permanentemente o conteúdo na cópia pública, e não apenas cobri-lo visualmente.
- A publicação exige prévia exata da página pública e revisão de dados identificáveis, fontes, documentos, localização e risco de reidentificação.

### Listas padronizadas iniciais

As listas a seguir devem ser apresentadas como opções pesquisáveis por digitação, com sugestões recentes e agrupadas quando necessário. Todas devem incluir `não informado` quando aplicável, distinguindo ausência de informação, dado não coletado, dado não aplicável e dado ausente no legado.

Quando nenhuma opção servir, o curador pode selecionar `outro` e sugerir um valor. A sugestão entra em uma pendência de qualificação e preserva o texto original; somente administrador pode aprovar, fundir, renomear ou inativar itens do catálogo. Alterações de apresentação nunca mudam o identificador histórico usado pelos registros existentes.

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
| Curador | Cria e edita registros de trabalho; envia registros para revisão; aprova, desaprova, publica, despublica, rejeita e arquiva registros. Consulta dados restritos com auditoria. Não pode executar ações de curadoria sobre registro do qual seja autor. |
| Administrador | Todas as ações de curador, além da gestão do sistema, usuários, perfis e permissões. Pode atuar em registros próprios quando necessário, com auditoria obrigatória. |
| Superadministrador | Alterações relativas ao site. O escopo exato de administração de conteúdo institucional será detalhado em feature própria; este perfil não recebe automaticamente permissões sobre ocorrências ou usuários. |

Participante acadêmico utiliza uma conta individual de usuário comum. Por padrão, recebe apenas dados anonimizados. Acesso restrito adicional é concedido por projeto e ocorrência, com prazo, justificativa, curador supervisor e escopo explícito; a autorização pode ser revogada sem apagar o histórico do trabalho realizado.

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
| qualquer estado não publicado | Arquivar | `arquivado` | Curador ou administrador, com justificativa |

Cada transição exige uma entrada no histórico de revisão. A justificativa é obrigatória para rejeição; comentários nas demais ações são opcionais.

Uma ocorrência publicada não é alterada diretamente. A edição abre uma nova versão de trabalho vinculada à versão pública vigente. A versão anterior continua visível até a nova revisão ser aprovada e publicada. Em caso de risco ou erro grave, curador ou administrador pode despublicar imediatamente, com justificativa obrigatória.

### Telas e fluxos principais

#### Painel inicial

Após a autenticação, o sistema apresenta atalhos conforme o perfil:

- **Usuário comum**: nova ocorrência, minhas ocorrências e consulta interna de registros aprovados.
- **Curador**: fila de revisão, nova ocorrência, registros sob sua responsabilidade e consulta interna.
- **Administrador**: recursos do curador, gestão de usuários, perfis e configurações do sistema.
- **Superadministrador**: gestão do conteúdo institucional do site, conforme permissões específicas.

#### Cadastro de ocorrência

1. O usuário seleciona **Nova ocorrência**.
2. O sistema cria um `rascunho`, registra autoria e data e pode gerar título provisório.
3. O cadastro é organizado em identificação do fato, violações, vítimas/grupos, agentes/instituições, fontes/evidências, privacidade/versão pública e curadoria.
4. O sistema permite salvar como `rascunho` a qualquer momento e mostra a completude de cada seção sem exigir preenchimento linear.
5. Quando houver somente uma violação, vítimas, agentes e fontes podem ser vinculados automaticamente a ela. Com várias violações, o usuário informa os vínculos; itens sem relação geram alerta, mas podem permanecer no rascunho.
6. Ao selecionar **Enviar para curadoria**, o sistema valida data ou período aproximado, município, relato interno, ao menos uma violação, informação disponível sobre vítima/grupo, uma fonte e classificação de privacidade.
7. Se a validação for aprovada, o status passa para `em_revisao`, uma entrada é criada no histórico e o registro aparece na fila de curadoria.
8. Se houver pendências, o sistema destaca a seção e os campos a corrigir e mantém o registro como `rascunho`.

O usuário comum só pode alterar ou excluir registros de sua autoria enquanto estiverem em `rascunho` ou `rejeitado`. Depois do envio, ele pode acompanhar o status e os comentários da curadoria, mas não pode editar o conteúdo até que o registro retorne para correção.

#### Colaboração, atribuição e conflitos

- A autoria original da ocorrência é imutável.
- Cada ocorrência tem no máximo um responsável atual, pode ter vários colaboradores e mantém histórico de todas as atribuições.
- O responsável organiza o trabalho; autoria e responsabilidade não concedem permissão para revisar o próprio registro.
- Cada alteração registra usuário, data/hora, seção e versão de origem.
- O sistema usa controle otimista de concorrência. Se duas pessoas alterarem o mesmo dado a partir de versões diferentes, nenhuma edição é sobrescrita silenciosamente; o conflito deve ser apresentado para decisão.
- Alterações em relato, privacidade, vínculos e dados sensíveis oferecem comparação entre valor anterior e novo aos usuários autorizados.

#### Exclusão e preservação

- Um rascunho nunca submetido pode sofrer exclusão lógica pelos perfis autorizados.
- Depois da primeira submissão, a ocorrência não pode ser apagada pela interface: pode ser rejeitada, arquivada, corrigida ou despublicada conforme o fluxo.
- Violações, vítimas, agentes e fontes removidos de uma ocorrência já submetida são desassociados ou marcados como removidos, preservando a versão e a auditoria anteriores.
- Exclusão física fica restrita a procedimento administrativo excepcional, sujeito à política institucional, justificativa e registro de auditoria.

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

Registros `aprovado` podem ser consultados internamente por usuários autenticados conforme o escopo de acesso. Somente curadores e administradores podem publicá-los ou despublicá-los. Antes de publicar, o sistema exige resumo público, precisão territorial pública, conferência dos documentos e dados identificáveis e uma prévia exata do conteúdo externo.

Ao editar um registro `publicado`, o sistema cria uma nova versão de trabalho. A versão pública vigente não muda até que a revisão nova seja aprovada e publicada; cada publicação registra autor, instante e versão substituída.

#### Área pública

A área pública exibe apenas ocorrências `publicado` e apenas os campos autorizados para divulgação. Ela deve oferecer busca e filtros por período, cidade, UF e tipo de violência, sem expor dados pessoais ou observações internas.

Coleções editoriais podem agrupar ocorrências publicadas por tema, território ou período. Uma coleção não torna uma ocorrência pública: se o caso for despublicado, ele deixa de aparecer na coleção automaticamente, preservando apenas o vínculo editorial interno.

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

- **Identidade e acesso**: usuários e permissões do Shield, atribuições de trabalho e eventos imutáveis de auditoria.
- **Denúncias e triagem**: entrada ainda não verificada, contato restrito, apoios solicitados e vínculos posteriores com ocorrências.
- **Ocorrências**: unidade documental do fato, com data/período, local, texto interno/público, status, autoria e curadoria.
- **Violações**: classificações específicas dentro de uma ocorrência, com conduta, enquadramento, meios e lesões/regiões do corpo.
- **Vítimas e grupos afetados**: pessoas ou coletividades, atributos demográficos necessários, condições e dados identificáveis restritos.
- **Agentes e instituições envolvidos**: classe/tipo institucional, unidade, cargo e identificação sustentada por fonte.
- **Fontes, depoimentos e arquivos**: evidências estruturadas e sua relação com as violações documentadas.
- **Território e geografia**: UF, município, local de trabalho e geometria PostGIS com precisão e visibilidade.
- **Curadoria e qualidade**: revisões, atribuições, pendências de qualificação, lotes e rastreabilidade do legado.
- **Conteúdo público**: acervo histórico e produções acadêmicas com estado editorial e arquivos.

O MER consolidado está em `mer-projetado.md`; regras, campos e decisões de implementação estão em `arquitetura-dados.md`.

### Modelo de dados e compatibilidade com o legado

O sistema já possui as tabelas `ocorrencias`, `vitimas`, `agressores`, `ocorrencia_revisoes`, `historico` e `produtos`. A evolução da base deve preservar essas tabelas, seus identificadores e os registros existentes. Dados anteriores devem continuar aparecendo para os usuários de acordo com as permissões e status aplicáveis, sem exigir recadastramento.

#### Entidades existentes e evolução prevista

| Entidade existente | Uso preservado | Evolução não destrutiva |
| --- | --- | --- |
| `ocorrencias` | Mantém ID, título, descrição, data, local, fontes, evidências, status e autoria já registrados. | Adicionar campos somente quando necessários, como `observacoes_internas`; normalizar classificações em tabelas auxiliares sem remover `tipo_violencia` e `fontes`. |
| `vitimas` | Mantém todos os registros e campos pessoais já existentes. | Normalizar tipo de entidade e demografia; separar identificação restrita. `anonimo` é preservado, mas não substitui a política de acesso campo a campo. |
| `agressores` | Mantém agente, órgão, unidade, identificação e observações já registrados. | Padronizar classe/tipo institucional, separar identificação restrita e criar vínculo N:N com violações. |
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
| `condutas`, `classes_juridicas`, `tipos_juridicos` | catálogos | Preservam contexto da ação e enquadramento já existentes no legado. |
| `vitima_violacoes` | N:N | Registra quais violações atingiram cada vítima. |
| `agressor_violacoes` | N:N | Registra quais violações são atribuídas a cada agente ou instituição. |
| `fonte_violacoes` | N:N | Registra quais violações são sustentadas por cada fonte. |
| `depoimentos` | 1:N com fontes | Preserva depoimentos e tipo de depoente sob regra de acesso própria. |
| `denuncias` e estruturas restritas | entrada/triagem | Preserva relatos recebidos, contatos e apoios solicitados sem tratá-los como ocorrência verificada. |
| `ufs`, `municipios`, `ocorrencia_locais` | território | Padroniza território e separa endereço exato de referência pública. |
| `arquivos` e relações | metadados | Controla checksum, MIME, acesso, direitos e anexos de fontes/conteúdos. |
| `migracao_lotes`, `registros_origem_legado` | rastreabilidade | Torna cada importação auditável e idempotente. |
| `pendencias_qualificacao` | qualidade | Organiza valores ausentes, conflitantes ou não mapeados sem alterar a evidência original. |
| `colecoes` e `colecao_ocorrencias` | editorial N:N | Agrupa versões publicadas de ocorrências em recortes temáticos, territoriais ou históricos. |

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

O PostgreSQL legado usa `Incluído`, `Curadoria`, `Revisar`, `Aprovado`, `Cancelado` e `Teste`. O modelo canônico usa `rascunho`, `em_revisao`, `aprovado`, `publicado`, `rejeitado` e `arquivado`. O mapeamento depende de homologação da curadoria: em especial, `Aprovado` nunca vira `publicado` automaticamente, e `Revisar` pode representar correção ou rejeição. O valor original e o mapeamento aplicado devem permanecer rastreáveis.

#### Critérios de aceite da migração

1. Cada linha de origem possui registro de migração ou justificativa explícita para não importação.
2. Contagens de entidades e relações são conciliadas por tabela, status e lote.
3. IDs de origem permanecem pesquisáveis internamente, mesmo quando o destino usa outro ID canônico.
4. Ocorrências, violações, vítimas, agentes, fontes, depoimentos, denúncias e geometrias preservam suas relações.
5. Nenhum dado pessoal, depoimento ou ponto exato passa a ser público em consequência da migração.
6. Relatórios novos incluem registros legados quando houver dados suficientes; quando não houver, exibem pendência de qualificação sem inferência automática.

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
| Ver nomes, contatos, endereços exatos, depoimentos ou originais restritos | Não por padrão; somente autorização acadêmica específica | Sim, com auditoria | Sim, com auditoria | Não por padrão |
| Ver histórico de revisão | Apenas próprios | Todos | Todos | Não |
| Excluir logicamente | Apenas próprio nunca submetido | Apenas rascunho nunca submetido | Apenas rascunho nunca submetido; demais casos seguem procedimento excepcional | Não |
| Sugerir valor de catálogo | Sim durante cadastro próprio | Sim | Sim | Não |
| Homologar/fundir/inativar catálogo | Não | Não | Sim | Não |

Permissões adicionais devem ser concedidas por regra explícita e auditável; nunca por acesso direto ao banco de dados ou por esconder botões na interface.

#### Classificação e tratamento de dados

| Classe | Exemplos | Tratamento mínimo |
| --- | --- | --- |
| Público | Resumo aprovado, município/UF, localização generalizada, categorias demográficas não identificáveis, instituição/unidade/cargo generalizados e fontes públicas autorizadas. | Só integra a projeção pública após revisão e prévia. |
| Interno | Rascunhos, relato de trabalho, comentários de curadoria, fontes de trabalho e indicadores internos. | Acesso apenas a usuários autenticados e autorizados. |
| Restrito | Nomes de vítimas, agentes, denunciantes, depoentes e testemunhas; contatos; idade exata; endereço/coordenada exatos; atributos sensíveis; depoimentos e arquivos originais identificáveis. | Nunca é público; acesso por necessidade de trabalho e sempre auditado. |

O sistema deve coletar somente os dados pessoais necessários para documentar a ocorrência e deve oferecer orientação no formulário sobre anonimização e dados sensíveis. Solicitações de acesso, correção ou eliminação de dados devem ser avaliadas pela coordenação responsável; nenhuma exclusão deve ocorrer automaticamente quando houver obrigação de preservação, interesse público, pesquisa ou outra base legal aplicável.

#### Auditoria

A trilha de auditoria deve registrar, no mínimo: usuário, data/hora, endereço de origem quando disponível, ação, entidade afetada, identificador do registro, valores de status anterior e novo, comentário e resultado da operação. Alterações em dados pessoais, permissões, autorizações acadêmicas e catálogos também geram evento.

Toda visualização ou download de nome, contato, endereço exato, coordenada precisa, depoimento ou documento original restrito gera evento de acesso. O log registra quem, quando, qual registro e qual modalidade de acesso, mas nunca copia o conteúdo sensível consultado.

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

### Artefatos de planejamento

- `plan.md`: arquitetura modular, layouts CodeIgniter, escopo da Etapa 1, dependências e portões de qualidade.
- `tasks.md`: backlog granular versionado, com IDs estáveis, histórias, caminhos de arquivo, checkpoints e trilhas paralelas.
- `arquitetura-dados.md`: modelo conceitual e lógico homologado.
- `mer-projetado.md`: relações e prioridades de implementação do banco.

## Success Criteria *(mandatory)*

### Measurable Outcomes
- **SC-001**: Dados usados em filtros e indicadores estruturados em PostgreSQL, com catálogos rastreáveis e relações preservadas para estatísticas por território, violação e atributos autorizados.
- **SC-002**: Separação clara entre Visão Pública (site institucional) e Visão Administrativa (gestão de dados interna).
- **SC-003**: Garantia de integridade referencial (Ex: Vítimas são apagadas ou preservadas corretamente ao apagar uma ocorrência).
- **SC-004**: 100% das linhas importadas possuem resultado e identificação de origem; nenhuma relação N:N é descartada silenciosamente.
- **SC-005**: Prévia de publicação não contém campos classificados como restritos nem coordenadas mais precisas que a política aprovada.

## Assumptions
- O banco de dados canônico será PostgreSQL com PostGIS, hospedado na VPS e reproduzido em homologação.
- O MySQL local atual contém apenas dados demonstrativos e será tratado separadamente na transição.
- Assumimos que o portal principal da PUC-SP apenas fará links (redirecionamentos) para as páginas públicas desta VPS, sem integração direta de APIs no momento.
- A autenticação é restrita à equipe do observatório; cidadãos comuns não criam contas de usuário, no máximo poderão preencher um formulário público no futuro (ainda não especificado).
- O backup PostgreSQL `pcvi_backup_260821` é a fonte histórica candidata; o esquema `old` não será importado junto com `public` sem comparação e regra de deduplicação.
