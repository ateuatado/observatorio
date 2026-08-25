# Wireframes de baixa fidelidade — painel e ficha interna

**Versão:** 0.1.0  
**Data:** 25 de agosto de 2026  
**Tarefa:** T008  
**Estado:** estrutura para validação; não define identidade visual final

## 1. Shell do painel

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│ OVPDH                         Ajuda              Maria ▾  Sair               │
├───────────────────┬──────────────────────────────────────────────────────────┤
│ VISÃO GERAL       │ Início / Visão geral                                    │
│                   │                                                          │
│ OCORRÊNCIAS       │ Bom dia, Maria                                           │
│ + Nova ocorrência │ Acompanhe seu trabalho e as pendências que exigem ação. │
│ Minhas            │                                                          │
│ Atribuídas        │ [3 rascunhos] [2 devolvidas] [1 em revisão]             │
│ Consulta interna  │                                                          │
│                   │ CONTINUE O TRABALHO                                      │
│ CONTA             │ ┌──────────────────────────────────────────────────────┐ │
│ Minha conta       │ │ Caso / estado / última alteração / próxima ação     │ │
│                   │ └──────────────────────────────────────────────────────┘ │
└───────────────────┴──────────────────────────────────────────────────────────┘
```

Em telas estreitas, o menu é recolhido e aberto por botão identificado como “Menu”. A ordem de foco acompanha a ordem visual.

## 2. Painel por perfil

### Usuário comum

- ação principal: “Nova ocorrência”;
- rascunhos recentes;
- registros devolvidos para correção;
- ocorrências atribuídas;
- explicação curta dos estados.

### Curador

- ação principal: “Abrir fila de curadoria”;
- quantidade aguardando primeira revisão;
- revisões sob sua responsabilidade;
- casos com risco de reidentificação ou pendência;
- nunca oferece autocuradoria.

### Administrador

- ações urgentes de curadoria;
- solicitações de usuário e autorizações a vencer;
- sugestões de catálogo aguardando homologação;
- pendências de qualidade;
- atalhos administrativos sem substituir as listas completas.

## 3. Lista de ocorrências

```text
Minhas ocorrências                              [ + Nova ocorrência ]
Encontre registros por texto, estado, período ou local.

[Buscar por título ou referência________________] [Buscar]
[Escopo: Minhas ▾] [Estado: Todos ▾] [Período ▾] [Mais filtros]

12 resultados                                      Ordenar: alteração recente ▾
┌──────────────────────────────────────────────────────────────────────────────┐
│ Título e referência │ Local/período │ Estado      │ Atualização │ Ação      │
│ Caso exemplo #1842  │ São Paulo/2024│ Rascunho    │ hoje        │ Continuar │
│ Caso exemplo #1720  │ Campinas/2023 │ Em revisão  │ ontem       │ Consultar │
└──────────────────────────────────────────────────────────────────────────────┘
                          [Anterior] Página 1 de 2 [Próxima]
```

- Em tela estreita, cada linha vira bloco sem perder rótulos.
- O estado nunca depende apenas de cor.
- A ação é específica: “Continuar”, “Corrigir”, “Consultar” ou “Revisar”.
- Dados identificáveis não aparecem na listagem.

## 4. Criação do rascunho

O primeiro passo solicita somente o necessário para criar um registro recuperável:

```text
Nova ocorrência
Crie um rascunho. Você poderá completar e revisar as informações depois.

Título provisório *       [________________________________________]
Data ou período conhecido [____/____/______] até [____/____/______]
UF                        [Selecione ▾]
Município                 [Selecione ▾]

[Cancelar]                                      [Criar rascunho e continuar]
```

Nenhum nome de vítima ou agente é solicitado nesta tela.

## 5. Shell da ficha em sete seções

```text
Ocorrências / Caso #1842                         RASCUNHO  Salvo há 2 min
Abordagem durante manifestação — São Paulo, 2024

Completude para revisão: 62%  [███████████░░░░░░]

┌──────────────────────┬───────────────────────────────────────────────────────┐
│ 1 Identificação   ✓  │ Título da seção                                     │
│ 2 Violações       !  │ Orientação curta e classificação de privacidade.    │
│ 3 Vítimas/grupos  ◐  │                                                       │
│ 4 Agentes         ○  │ [ campos e componentes da seção ]                    │
│ 5 Fontes          ◐  │                                                       │
│ 6 Privacidade     ○  │ [Salvar rascunho]              [Salvar e continuar] │
│ 7 Histórico       —  │                                                       │
├──────────────────────┴───────────────────────────────────────────────────────┤
│ Pendências para enviar: resumo público, fonte principal e precisão pública. │
│                                              [Enviar para revisão] desativado│
└──────────────────────────────────────────────────────────────────────────────┘
```

Legenda: `✓` completa, `◐` em preenchimento, `!` com pendência, `○` não iniciada e `—` somente leitura.

## 6. Seção 1 — Identificação

Grupos de campos:

- título interno e referência legada;
- data inicial, final e indicação de data aproximada;
- UF, município, bairro/comunidade e local de referência;
- endereço e coordenada exatos em bloco restrito;
- descrição interna do fato;
- responsável e colaboradores, se autorizado.

O bloco restrito tem rótulo textual, explicação de auditoria e não usa apenas cor ou ícone.

## 7. Seção 2 — Violações

```text
Violações                                               [ + Adicionar violação ]

┌ Violação 1 ────────────────────────────────────────────────────────────────┐
│ Tipo * [Violência física ▾]   Conduta [Abordagem ▾]                      │
│ Classe jurídica [_________▾]  Tipo jurídico [_________▾]                 │
│ Meios utilizados [seleção pesquisável e múltipla]                        │
│ Lesões/regiões   [seleção pesquisável e múltipla]                        │
│ Vítimas atingidas [Vítima A] [Grupo B]                                  │
│ Agentes relacionados [Instituição X]                                    │
│ Fontes de apoio [Fonte 1] [Fonte 3]                                     │
│                                                     [Remover violação]   │
└───────────────────────────────────────────────────────────────────────────┘
```

Valores não encontrados podem ser sugeridos, sem entrar imediatamente no catálogo homologado.

## 8. Seção 3 — Vítimas e grupos

Cada item começa pela escolha “Pessoa” ou “Grupo/coletividade”.

- nome, apenas quando necessário, em bloco restrito;
- nome público nunca é oferecido;
- faixa etária para projeção pública; idade exata restrita;
- gênero, raça/etnia e condições, sempre com “não informado” e vocabulários sugeridos;
- profissão/atividade quando pertinente;
- desfecho;
- violações relacionadas;
- relato ou depoimento separado do resumo público.

## 9. Seção 4 — Agentes e instituições

- natureza do item: pessoa, instituição ou unidade;
- classe institucional, instituição, unidade e cargo/patente;
- nome individual em bloco restrito e nunca selecionado para publicação;
- identificação funcional, quando sustentada por fonte;
- violações atribuídas e fontes que sustentam o vínculo;
- grau de confirmação e observações internas.

A interface usa “Agentes e instituições”, não “agressores”, sem alterar silenciosamente o significado legado.

## 10. Seção 5 — Fontes e evidências

- tipo de fonte;
- título/referência e URL;
- autoria ou instituição;
- data de publicação e data de acesso;
- descrição e confiabilidade de trabalho;
- violações sustentadas;
- arquivo original restrito e, quando existir, derivação pública expurgada;
- depoimento associado com perfil de acesso independente.

Upload informa formatos e tamanho antes da escolha do arquivo. Após envio, apresenta nome, tipo, tamanho, estado de verificação e ação permitida.

## 11. Seção 6 — Privacidade e versão pública

```text
Privacidade e versão pública

Resumo público *
[__________________________________________________________________________]

Localização pública  ( ) Município  ( ) Bairro aprovado  ( ) Área generalizada

Elementos que poderão aparecer:
[✓] Tipos de violação     [✓] Instituições/patentes     [ ] Documento derivado

Checklist automático
✓ Nenhum nome individual na projeção estruturada
! O texto do resumo contém um possível endereço — revisar
✓ Coordenada exata substituída por área pública

[Abrir prévia pública]
```

O sistema alerta, mas a decisão de publicação continua exigindo revisão humana autorizada.

## 12. Seção 7 — Curadoria e histórico

- linha do tempo imutável de criação, edições, atribuições e mudanças de estado;
- versão atual e versão pública vigente;
- comentários de curadoria;
- pendências abertas e resolvidas;
- acesso a comparação para quem possui permissão;
- nenhum conteúdo de log reproduz dados restritos.

## 13. Estados essenciais

Os wireframes de implementação devem cobrir: carregando, vazio, sucesso, erro de validação, acesso negado, sessão expirada, conflito de versão, upload rejeitado, catálogo sem resultado e perda de conexão antes do salvamento.

## 14. Critérios de aceite de T008

- Painel, lista, criação e shell da ficha estão definidos.
- As sete seções têm conteúdo e ações identificados.
- Privacidade está integrada ao preenchimento, não relegada ao fim.
- O desenho funciona com envio tradicional do servidor; JavaScript é melhoria progressiva.
- A ficha pode ser usada por teclado e refluída para telas estreitas.

