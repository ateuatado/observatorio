# Caderno de validação da interface — protocolo do grupo OVP

**Versão:** 0.1.0  
**Data:** 25 de agosto de 2026  
**Tarefa relacionada:** T011  
**Página:** `public/caderno-validacao-interface.html`  
**Estado:** pronto para aplicação-piloto

## 1. Objetivo

Submeter os fluxos e wireframes da Etapa 1 ao grupo do OVP em linguagem não técnica, verificando se participantes representativos conseguem compreender o cadastro, a curadoria e a publicação segura sem explicações adicionais.

## 2. Material de aplicação

O caderno HTML contém:

- orientação e identificação funcional do participante;
- três cenários inteiramente fictícios;
- tarefas simuladas e decisões esperadas;
- escalas de clareza de 1 a 5;
- perguntas objetivas sobre fluxo, relações e privacidade;
- campos de observação;
- salvamento automático local;
- download das respostas em JSON para consolidação;
- versão adequada para impressão.

Nenhuma resposta é transmitida automaticamente ao servidor. O participante baixa o arquivo e o encaminha à coordenação pelo canal combinado.

## 3. Participantes recomendados

- coordenação;
- curadores;
- voluntários/documentalistas;
- estudantes e pesquisadores;
- responsáveis pelo acervo e produtos acadêmicos;
- pessoa com conhecimento do sistema atual ou do banco legado.

Um mesmo participante responde pelo papel mais próximo de sua experiência. Nome ou código de identificação é opcional; o papel é obrigatório para permitir análise por grupo funcional.

## 4. Cenários

### Cenário 1 — Registro simples

Uma notícia pública relata possível violência durante abordagem policial. Há data aproximada, município, uma vítima não identificada, uma instituição conhecida e um link público.

Verifica:

- criação de rascunho mínimo;
- compreensão das sete seções;
- salvamento parcial;
- vínculo básico entre violação, vítima, agente e fonte;
- envio para revisão.

### Cenário 2 — Registro complexo

Um dossiê fictício reúne múltiplas vítimas, agentes, violações, fontes e depoimentos que sustentam relações diferentes.

Verifica:

- compreensão das relações N:N;
- prevenção de duplicidade;
- associação seletiva de fontes;
- trabalho dividido por seções;
- percepção de completude e pendências.

### Cenário 3 — Registro sensível

Um caso fictício contém nomes, endereço exato, depoimento restrito e documento que exige derivação expurgada.

Verifica:

- distinção entre interno, restrito e público;
- anonimização de pessoas;
- generalização territorial;
- separação entre original e derivação pública;
- prévia exata e bloqueios de publicação.

## 5. Aplicação sugerida

1. A coordenação envia o link e informa prazo de três a cinco dias.
2. Cada participante responde individualmente, sem reunião guiada, em 30–40 minutos.
3. O participante usa “Baixar minhas respostas” e envia o arquivo JSON à coordenação.
4. A coordenação preserva os arquivos originais em pasta restrita.
5. As respostas são consolidadas por papel, cenário e pergunta.
6. O grupo realiza reunião de até 60 minutos para divergências e decisões bloqueadoras.
7. As decisões são registradas em `validacao-cenarios.md` sem identificar participantes desnecessariamente.

## 6. Critérios de avaliação

| Dimensão | Evidência esperada |
| --- | --- |
| Compreensão | Participante identifica a próxima ação e a seção adequada. |
| Terminologia | Nomes das áreas e ações são compreensíveis para o trabalho real. |
| Completude | Campos ou relações importantes ausentes são identificados. |
| Privacidade | Participante distingue com segurança o que nunca será público. |
| Curadoria | Aprovação e publicação são entendidas como ações separadas. |
| Autonomia | O percurso é concluído sem explicação verbal externa. |

## 7. Portões de aprovação

- ao menos 80% avaliam o percurso com nota 4 ou 5;
- nenhuma ação crítica depende de orientação verbal;
- nenhuma resposta majoritária permite publicar nome individual, endereço exato, coordenada precisa ou documento original restrito;
- aprovação e publicação são reconhecidas como etapas separadas;
- problemas classificados como bloqueadores são resolvidos antes de T012–T014 e da implementação das Views.

## 8. Classificação das decisões

- **Aprovada:** não exige alteração.
- **Aprovada com ajuste:** mudança localizada, sem alterar o modelo.
- **Pendente institucional:** depende de decisão do OVP.
- **Rejeitada:** fluxo ou termo deve ser redesenhado.
- **Fora da Etapa 1:** relevante, mas não impede o MVP.

## 9. Proteção e retenção

- Os cenários não usam dados reais.
- O formulário não solicita dados pessoais além de identificação opcional do participante.
- Respostas ficam no navegador até serem apagadas pelo participante.
- Arquivos recebidos devem permanecer em pasta de trabalho restrita.
- O relatório consolidado usa resultados agregados e comentários anonimizados quando possível.

## 10. Próximos passos após o piloto

1. testar o caderno com duas pessoas da equipe;
2. corrigir linguagem, navegação ou exportação;
3. disponibilizar o link ao grupo completo;
4. consolidar respostas em `interface/validacao-cenarios.md`;
5. atualizar wireframes e marcar T011 como concluída apenas após decisão do grupo.

