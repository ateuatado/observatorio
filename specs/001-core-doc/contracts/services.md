# Contratos dos Services e transições — OVPDH

**Tarefa**: T013

**Versão**: 1.0.0

**Data**: 5 de outubro de 2026

**Status**: contrato normativo para implementação

## 1. Objetivo

Este documento define a fronteira entre Controllers, Services e persistência. Cada Service representa um caso de uso, aplica autorização e regras de negócio, executa alterações em transação e devolve um resultado previsível. Controllers não repetem essas regras e Views não decidem permissões ou estados.

Os contratos são independentes do transporte: podem ser usados por formulário HTML, comando de migração ou futura API sem duplicar a regra de negócio.

## 2. Convenções comuns

### 2.1 Entrada

Todo comando de escrita recebe um DTO imutável com:

| Campo | Tipo | Regra |
| --- | --- | --- |
| `actorId` | `int` | usuário autenticado; nunca vem confiável do formulário |
| `occurrenceId` | `int|null` | obrigatório quando o agregado já existe |
| `expectedVersion` | `int|null` | obrigatório em alteração concorrente |
| `payload` | DTO específico | somente campos permitidos para o caso de uso |
| `requestId` | `string` | identificador para correlação e idempotência quando aplicável |

O Controller obtém `actorId` da sessão Shield. IDs, status, autoria e campos de auditoria enviados pelo navegador não têm autoridade.

### 2.2 Resultado

Services devolvem `ServiceResult<T>`:

```text
success: bool
data: T|null
errors: ServiceError[]
warnings: ServiceWarning[]
meta: { occurrenceVersion?, transition?, requestId? }
```

`ServiceError` contém `code`, `field|null`, `section|null` e `params`; a tradução da mensagem pertence à camada de apresentação. Erros previstos não lançam exceção de infraestrutura.

### 2.3 Códigos comuns

| Código | Significado | Resposta HTTP sugerida |
| --- | --- | ---: |
| `AUTHENTICATION_REQUIRED` | sessão ausente | 401 |
| `FORBIDDEN` | perfil sem permissão | 403 |
| `OBJECT_SCOPE_DENIED` | sem acesso à ocorrência | 403, sem revelar dados |
| `NOT_FOUND` | registro inexistente no escopo permitido | 404 |
| `INVALID_STATE` | ação incompatível com o estado | 409 |
| `VERSION_CONFLICT` | `expectedVersion` desatualizada | 409 |
| `VALIDATION_FAILED` | campos ou relações inválidos | 422 |
| `PRIVACY_GATE_FAILED` | checklist de publicação incompleto | 422 |
| `DUPLICATE_REQUEST` | mesma operação já processada | 409 ou sucesso idempotente |
| `INFRASTRUCTURE_FAILURE` | falha não prevista | 500, sem dados sensíveis |

### 2.4 Ordem obrigatória de execução

1. autenticar ator;
2. carregar o mínimo necessário no escopo autorizado;
3. verificar permissão de ação e de objeto;
4. verificar estado e versão esperada;
5. validar entrada e invariantes;
6. executar a transação;
7. registrar revisão/auditoria dentro da mesma transação quando for parte da mudança;
8. confirmar a transação;
9. devolver DTO de saída sem campos restritos desnecessários.

Falha em qualquer etapa não produz alteração parcial. Logs recebem IDs e códigos, nunca nomes, contatos, depoimentos ou endereços.

## 3. Autorização por objeto

| Papel | Regra sobre ocorrência |
| --- | --- |
| usuário comum | cria; lê e edita os próprios rascunhos/rejeitados; consulta internamente aprovados/publicados conforme escopo |
| curador | trabalha em ocorrências autorizadas; não aprova, rejeita, desaprova, publica ou despublica registro próprio |
| administrador | mesmas ações e gestão; atuação curatorial em registro próprio é exceção com justificativa obrigatória e auditoria destacada |
| superadministrador | não recebe permissões de ocorrência por causa do nome do grupo; depende de permissão explícita |

Uma autorização acadêmica amplia somente leitura restrita no escopo, período e finalidade concedidos. Ela não concede edição ou curadoria.

## 4. Máquina de estados

### 4.1 Transições permitidas

| Origem | Comando | Destino | Justificativa | Gate |
| --- | --- | --- | --- | --- |
| `rascunho` | `SubmitOccurrenceForReview` | `em_revisao` | opcional | completude para revisão |
| `rejeitado` | iniciar correção | `rascunho` | opcional; preservar rejeição anterior | permissão de edição |
| `em_revisao` | `ApproveOccurrence` | `aprovado` | comentário opcional | revisor independente + completude |
| `em_revisao` | `RejectOccurrence` | `rejeitado` | obrigatória | revisor independente |
| `aprovado` | `PublishOccurrenceVersion` | `publicado` | opcional | checklist de privacidade completo + prévia confirmada |
| `aprovado` | desaprovar para correção | `rascunho` | obrigatória | revisor independente |
| `publicado` | `UnpublishOccurrence` | `aprovado` | obrigatória | risco/erro descrito |
| estado não publicado | `ArchiveOccurrence` | `arquivado` | obrigatória | curador/admin |

`arquivado` não possui saída comum. Restauração será caso de uso administrativo próprio, se aprovada. Não existe transição direta de `rascunho` para `aprovado` ou `publicado`.

### 4.2 Regras de versão

- Cada escrita compara `expectedVersion` com `versao_lock`.
- Submissão cria snapshot imutável em `ocorrencia_versoes`.
- Aprovação identifica explicitamente a versão revisada.
- Publicação usa exatamente a versão aprovada e sua prévia; não reconstrói conteúdo a partir do estado mutável.
- Editar uma ocorrência publicada cria versão de trabalho vinculada à publicação vigente. A projeção pública não muda.
- Cada transição cria `ocorrencia_revisoes` com ator, ação, estados, versão e comentário.

## 5. Contratos de documentação

### 5.1 `CreateOccurrenceDraft`

**Entrada**: `actorId`, título opcional e `requestId`.

**Pré-condições**: permissão `occurrences.create`; conta ativa.

**Efeito**: cria ocorrência `rascunho`, autoria imutável, versão 1 e atribuição inicial ao autor. Pode gerar título interno provisório.

**Saída**: `OccurrenceDraftView { id, status, title, version, sections }`.

**Idempotência**: mesmo `requestId` do ator devolve o rascunho já criado.

### 5.2 `UpdateOccurrenceIdentification`

**Entrada**: ocorrência, versão esperada, título, relato interno, datas/precisão, período, importância e locais.

**Estados**: `rascunho` ou `rejeitado`; numa ocorrência publicada, atua somente sobre a nova versão de trabalho.

**Regras**: usuário comum edita apenas autoria própria; município e UF são coerentes; endereço fica restrito; `data_fim >= data_inicio`; relato interno nunca preenche resumo público automaticamente.

**Saída**: versão nova, completude recalculada e avisos de privacidade/duplicidade.

### 5.3 `ManageOccurrenceViolations`

**Entrada**: conjunto desejado de violações, tipo, conduta, enquadramento, meios e lesões, com IDs estáveis de item.

**Regras**: IDs de catálogo devem estar ativos para novos vínculos; itens históricos inativos permanecem legíveis; remoção após submissão é versionada, não destrutiva; duplicatas semânticas geram aviso.

**Saída**: violações persistidas e itens sem vítima/agente/fonte vinculados.

### 5.4 `ManageOccurrenceVictims`

**Entrada**: perfis de vítima/grupo, demografia necessária, condições, violações relacionadas e bloco restrito separado.

**Regras**: nome é opcional e restrito; idade válida é 0–120 no contrato físico; religião, orientação sexual, deficiência e outros dados sensíveis exigem finalidade informada; uma única violação pode receber vínculo automático confirmado; com várias, o vínculo é explícito.

**Saída**: itens sanitizados para o perfil do ator, relações e alertas de reidentificação.

### 5.5 `ManageOccurrenceActors`

**Entrada**: classe/tipo institucional, órgão/unidade, cargo/função, identificação restrita, fonte da identificação e violações relacionadas.

**Regras**: identificação individual só é aceita com fonte; nome sempre restrito; atribuição documentada não é condenação; classe e tipo institucional devem ser coerentes.

**Saída**: agentes/instituições e alertas de combinação identificável.

### 5.6 `ManageOccurrenceSources`

**Entrada**: tipo, título, data, URL/referência, denúncia, restrição, arquivos, depoimentos e violações sustentadas.

**Regras**: URL não é obrigatória para fonte física/entrevista; arquivo público precisa de direitos e derivação expurgada quando houver original sensível; checksum identifica conteúdo; depoimento começa restrito.

**Saída**: fontes, estado dos arquivos e alegações sem sustentação.

### 5.7 `EvaluateOccurrenceCompleteness`

**Entrada**: ocorrência, versão e alvo `draft|review|approval|publication`.

**Efeito**: somente leitura; devolve requisitos agrupados por seção, severidade `error|warning|info` e código estável.

**Para revisão exige**: data ou período aproximado, município, relato interno, uma violação, informação disponível sobre vítima/grupo, uma fonte e classificação de privacidade. Relações ausentes e possível duplicidade são explicitadas.

**Para publicação exige adicionalmente**: versão aprovada, resumo público, local público generalizado, sustentação por fonte, documentos/direitos conferidos, dados pessoais revisados, risco de reidentificação avaliado e prévia exata confirmada.

### 5.8 `SubmitOccurrenceForReview`

**Entrada**: ocorrência, versão esperada e comentário opcional.

**Pré-condições**: autor ou perfil autorizado; estado `rascunho`; completude `review` sem erros.

**Transação**: cria snapshot, muda para `em_revisao`, encerra edição comum, cria revisão e entrada na fila.

**Saída**: status, número/checksum da versão submetida e data de envio.

### 5.9 `OccurrenceVersionService`

Operações: `openWorkingCopy`, `createSnapshot`, `compare`, `assertExpectedVersion` e `getPublishedBaseline`.

Não recebe decisão de negócio sobre aprovação. `compare` mascara campos restritos para quem não possui acesso. Snapshots submetidos, aprovados ou publicados são imutáveis.

## 6. Contratos de curadoria

### 6.1 Regra comum de independência

`ReviewOccurrence` aplica antes de toda decisão:

- curador autor: negar com `SELF_REVIEW_FORBIDDEN`;
- administrador autor: exigir `exceptionJustification`, registrar evento `ADMIN_SELF_REVIEW_EXCEPTION` e destacar no histórico;
- versão atualmente em revisão deve coincidir com a versão informada;
- consulta a conteúdo restrito gera auditoria separada.

### 6.2 `ApproveOccurrence`

**Entrada**: ocorrência, versão revisada, comentário e justificativa excepcional quando aplicável.

**Pré-condições**: estado `em_revisao`, revisor independente, completude `approval` sem erros.

**Efeito**: `em_revisao → aprovado`, revisão imutável, preservação do snapshot aprovado. Não publica.

### 6.3 `RejectOccurrence`

**Entrada**: ocorrência, versão e motivo não vazio.

**Efeito**: `em_revisao → rejeitado`; registra motivo e devolve edição ao autor. O primeiro salvamento corretivo muda para `rascunho`, preservando o evento de rejeição.

### 6.4 Desaprovar para correção

**Entrada**: ocorrência aprovada, versão e motivo obrigatório.

**Efeito**: `aprovado → rascunho`, abre cópia de trabalho e registra a decisão. Não se aplica à publicação vigente; caso publicado deve primeiro ser despublicado ou receber nova versão de trabalho.

### 6.5 `ArchiveOccurrence`

**Entrada**: ocorrência não publicada e justificativa obrigatória.

**Efeito**: estado `arquivado`, encerra atribuições ativas e preserva versões, fontes e auditoria. Não apaga registros.

## 7. Contratos de publicação

### 7.1 `BuildPublicProjection`

**Entrada**: ocorrência e ID da versão aprovada.

**Efeito**: somente leitura; monta DTO por lista positiva de campos permitidos. Nunca serializa Entities internas nem usa “remover depois”. Aplica generalização territorial/demográfica, inclui somente fontes e derivados autorizados e calcula checksum.

**Saída**: `PublicProjection { versionId, payload, checksum, warnings, blockers }`.

Qualquer nome individual, endereço, geometria exata, contato, observação interna ou atributo sensível proibido em `payload` é falha de segurança.

### 7.2 `PublishOccurrenceVersion`

**Entrada**: ocorrência, versão aprovada, `previewChecksum`, confirmações do checklist e comentário opcional.

**Pré-condições**: estado `aprovado`; ator autorizado e independente; `BuildPublicProjection` sem bloqueadores; checksum da prévia igual ao atual; checklist T014 completo.

**Transação**: grava projeção imutável, substitui publicação vigente, muda estado para `publicado`, registra autor/instante/revisão e invalida caches públicos.

**Idempotência**: repetir mesma versão e checksum devolve a publicação existente.

### 7.3 `UnpublishOccurrence`

**Entrada**: ocorrência publicada, motivo obrigatório e categoria do risco/erro.

**Efeito**: encerra imediatamente a publicação vigente, muda `publicado → aprovado`, remove o caso das projeções/coleções públicas e registra revisão/auditoria. O vínculo editorial interno é preservado.

Falha de cache não pode manter acesso público: a fonte de verdade pública filtra publicação vigente no banco.

## 8. Administração e governança

### 8.1 `UserManagementService`

Cria, edita, desativa e atribui grupos sem apagar autoria. Impede autorrebaixamento que deixe o sistema sem administrador e registra toda mudança de privilégio. Senhas são delegadas ao Shield e nunca retornam no resultado.

### 8.2 `CatalogService`

Permite buscar ativos/inativos; curador sugere preservando texto original; administrador homologa, funde, renomeia ou inativa. Item usado não é apagado e seu código não muda de significado. Merge mantém mapa de origem e auditoria.

### 8.3 `RestrictedAccessService`

Concede acesso acadêmico por usuário, projeto/ocorrência, escopo, prazo, justificativa e supervisor; revoga sem apagar histórico. Cada leitura/download chama `AuditRestrictedAccess`.

### 8.4 `AuditRestrictedAccess`

Registra ator, entidade, ID, modalidade, finalidade/autorização e instante. Não registra o conteúdo consultado. Falha ao auditar bloqueia acesso restrito, exceto procedimento emergencial institucional ainda não definido.

## 9. Migração e projeções de leitura

Services de migração são executados por comandos autenticados operacionalmente, nunca por rota pública. Recebem lote, modo `dry-run|commit`, versão do transformador e checksum da origem. Reexecução é idempotente; valor não homologado cria `pendencias_qualificacao` e preserva o valor bruto.

Consultas internas e públicas usam Query Services sem métodos de escrita:

- `OccurrenceInternalSearch`: aplica status, autoria e escopo;
- `CurationQueueQuery`: somente `em_revisao`, mais antigos primeiro;
- `PublicOccurrenceSearch`: somente publicações vigentes;
- `PublicIndicatorsQuery`: somente dados publicados, com regra contra contagens reidentificáveis.

## 10. Fronteiras transacionais

| Caso | Deve ocorrer na mesma transação |
| --- | --- |
| salvar seção | dados, relações, incremento de versão e auditoria de mudança |
| enviar para revisão | snapshot, estado, revisão e entrada na fila |
| aprovar/rejeitar | estado, decisão e histórico |
| publicar | projeção, substituição da vigente, estado e histórico |
| despublicar | encerramento público, estado, histórico e marcação de invalidação |
| conceder acesso | autorização e auditoria administrativa |

Arquivos físicos são preparados antes da transação; o banco só referencia objeto confirmado. Falha posterior deixa o objeto para coleta segura, não uma linha apontando para arquivo inexistente.

## 11. Testes contratuais mínimos

1. cada transição permitida funciona e toda transição não listada falha;
2. curador não decide caso próprio; exceção administrativa exige justificativa;
3. conflito de versão não sobrescreve conteúdo;
4. falha intermediária reverte dados e histórico juntos;
5. rascunho incompleto salva, mas não é submetido;
6. projeção pública nasce de lista positiva e não contém campos restritos;
7. checksum diferente da prévia bloqueia publicação;
8. nova edição não altera publicação vigente;
9. despublicação retira o caso imediatamente das consultas públicas;
10. repetição idempotente não duplica rascunho, versão, publicação ou importação;
11. acesso restrito sem autorização ou auditoria falha;
12. erros e logs não revelam existência ou conteúdo fora do escopo.

## 12. Decisões de implementação

- DTOs e resultados ficam em `app/Services/Contracts/` ou namespace equivalente único.
- Policies autorizam; Services orquestram; Models persistem; Events não decidem regra crítica.
- O checklist T014 é uma dependência executável de `PublishOccurrenceVersion`, não somente um formulário visual.
- Notificações e invalidação de cache são pós-confirmação e reexecutáveis; a segurança pública depende do estado no banco.
- Nomes de classes do backlog são normativos; mudanças exigem atualização deste contrato e das tarefas afetadas.

## 13. Critério de conclusão da T013

T013 está concluída quando os casos de uso planejados possuem entrada, autorização, pré-condições, efeitos, saída, erros e fronteira transacional definidos; a máquina de estados não contém transição implícita; e o checklist de publicação pode ser aplicado como gate pelo Service.
