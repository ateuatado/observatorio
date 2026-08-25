# Wireframes de baixa fidelidade — curadoria e publicação

**Versão:** 0.1.0  
**Data:** 25 de agosto de 2026  
**Tarefa:** T009

## 1. Fila de curadoria

```text
Fila de curadoria
Registros aguardando revisão, correção ou publicação.

[Buscar referência ou título____________] [Buscar]
[Etapa: Todas ▾] [Território ▾] [Responsável ▾] [Risco: Todos ▾]

24 registros                         Ordenar: mais antigo primeiro ▾
┌──────────────────────────────────────────────────────────────────────────────┐
│ Caso        │ Enviado por │ Espera │ Completude │ Alertas     │ Ação        │
│ #1842 ...   │ João        │ 3 dias │ Completo   │ 1 privacidade│ Revisar     │
│ #1801 ...   │ Ana         │ 1 dia  │ Completo   │ Nenhum       │ Revisar     │
└──────────────────────────────────────────────────────────────────────────────┘
```

- A fila padrão prioriza maior tempo de espera; prioridade manual exige critério visível.
- Nome de vítima/agente não aparece na fila.
- Autoria própria mostra “Você é autor — revisão por outro curador” e não oferece ação.
- Filtros permanecem na URL para retorno e compartilhamento interno autorizado.

## 2. Tela de revisão

```text
Fila / Caso #1842                                      EM REVISÃO
Abordagem durante manifestação

[Resumo] [Dados completos] [Comparar versões] [Prévia pública]

┌────────────────────────────────────────┬─────────────────────────────────────┐
│ CONTEÚDO DA OCORRÊNCIA                 │ CHECKLIST DE REVISÃO                │
│ Identificação                          │ [✓] Identificação suficiente        │
│ Violações e vínculos                   │ [✓] Relações sustentadas            │
│ Vítimas/grupos                         │ [ ] Dados sensíveis conferidos      │
│ Agentes/instituições                   │ [ ] Fontes e direitos conferidos    │
│ Fontes/documentos                      │ [ ] Localização pública adequada    │
│                                        │                                     │
│ Alertas aparecem junto ao conteúdo.    │ Comentário obrigatório quando falha │
└────────────────────────────────────────┴─────────────────────────────────────┘

[Solicitar correção] [Rejeitar]                         [Aprovar]
```

Em tela estreita, conteúdo e checklist são empilhados; as ações ficam no fluxo do documento, nunca fixas cobrindo conteúdo.

## 3. Comparação de versões

```text
Comparar versões                     Anterior: v3 ▾  Atual: v4 ▾

Campo                     Versão anterior                Versão atual
──────────────────────────────────────────────────────────────────────────────
Resumo público            “Texto anterior...”            “Texto atualizado...”
Localização pública       Município                      Área generalizada
Fonte principal           Fonte 2                        Fonte 2 + Fonte 5
Vítimas                   1 grupo                        1 grupo (sem mudança)

[Mostrar apenas alterações ✓]                 [Voltar à revisão]
```

- Inclusões, remoções e alterações usam texto/símbolos além de cor.
- Dados restritos permanecem restritos; abrir um valor identificável gera auditoria.
- O comparador não permite edição direta.

## 4. Solicitação de correção

```text
Solicitar correção

Seções afetadas *  [ ] Identificação [ ] Violações [ ] Vítimas ...
Orientação ao responsável *
[__________________________________________________________________________]

O registro voltará a rascunho e manterá todo o histórico.
[Cancelar]                                      [Enviar solicitação]
```

## 5. Aprovação

Aprovar altera `em_revisao` para `aprovado`; não publica automaticamente.

- exige checklist de revisão completo;
- exige confirmação de que o curador não é autor;
- registra versão, responsável e instante;
- oferece, após sucesso, “Abrir prévia pública” como próximo passo.

## 6. Prévia pública exata

```text
Prévia pública — Caso #1842             NÃO PUBLICADO / visualização interna

Esta é a apresentação exata que o público verá.
┌──────────────────────────────────────────────────────────────────────────────┐
│ [cabeçalho do portal público]                                               │
│ Título público                                                               │
│ Município — período                                                          │
│ Resumo público                                                               │
│ Violações | grupos agregados | instituições/patentes | fontes autorizadas   │
│ [documentos derivados autorizados]                                           │
└──────────────────────────────────────────────────────────────────────────────┘

Checklist de publicação
✓ Projeção sem campos classificados como restritos
✓ Precisão territorial aprovada
! Confirmar direito de divulgação do documento derivado

[Voltar à revisão]                                      [Publicar esta versão]
```

A prévia usa o mesmo template e o mesmo objeto de projeção da página pública. Não é uma reprodução manual.

## 7. Publicação, despublicação e arquivamento

| Ação | Confirmação exigida | Resultado |
| --- | --- | --- |
| Publicar | Checklist completo e versão identificada | Cria publicação imutável e torna a projeção consultável. |
| Despublicar | Motivo obrigatório e confirmação explícita | Retira imediatamente do portal e de coleções; mantém histórico. |
| Rejeitar | Justificativa e seções afetadas | Estado `rejeitado`; autoria recebe orientação. |
| Arquivar | Motivo e confirmação | Retira do fluxo sem excluir evidência. |

## 8. Critérios de aceite de T009

- A fila permite identificar o próximo trabalho sem expor PII.
- Autocuradoria é impedida e explicada.
- Revisão, comparação e prévia são contextos distintos.
- Aprovação e publicação são ações separadas.
- Toda decisão relevante exige justificativa/checklist e gera histórico.
- A prévia é produzida pela mesma projeção usada publicamente.

