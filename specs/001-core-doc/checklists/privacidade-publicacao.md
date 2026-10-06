# Checklist de privacidade e publicação — OVPDH

**Tarefa**: T014

**Versão**: 1.0.0

**Data**: 5 de outubro de 2026

**Aplicação**: toda primeira publicação, republicação e publicação de documento derivado

## 1. Regra de uso

Este checklist é um bloqueio de sistema, não uma recomendação. `PublishOccurrenceVersion` só conclui quando todos os itens obrigatórios aplicáveis possuem responsável, instante, versão e evidência. Marcar “não se aplica” exige justificativa.

As respostas são versionadas com a ocorrência e o checksum da prévia. Se o conteúdo público mudar depois da conferência, as confirmações perdem validade.

### Resultado de cada item

| Resultado | Efeito |
| --- | --- |
| `confirmado` | requisito atendido e evidenciado |
| `nao_se_aplica` | permitido somente com justificativa |
| `pendente` | bloqueia publicação |
| `reprovado` | bloqueia publicação e exige correção |

## 2. Identificação da conferência

- [ ] ocorrência e versão aprovadas estão identificadas;
- [ ] checksum da prévia corresponde ao conteúdo que será publicado;
- [ ] conferente possui permissão e não é curador autor do caso;
- [ ] eventual exceção de administrador autor possui justificativa destacada;
- [ ] data/hora, conferente e resultado ficam no histórico imutável.

## 3. Sustentação e linguagem

- [ ] cada afirmação factual relevante do resumo público possui ao menos uma fonte vinculada;
- [ ] violações, vítimas/grupos, agentes/instituições e fontes estão relacionados de forma compreensível;
- [ ] itens sem vínculo foram corrigidos ou justificados;
- [ ] possível duplicidade de caso foi analisada;
- [ ] o texto distingue alegação documentada, apuração, decisão judicial e condenação;
- [ ] linguagem não atribui culpa além do que as fontes sustentam;
- [ ] relato interno não foi copiado automaticamente para o resumo público;
- [ ] observações de curadoria e pendências internas não aparecem na prévia.

## 4. Pessoas e risco de reidentificação

- [ ] nenhum nome de vítima, agente, denunciante, depoente ou testemunha aparece em texto, título, legenda, URL, arquivo ou metadado;
- [ ] idade exata, profissão, religião, orientação sexual e deficiência não aparecem individualmente;
- [ ] faixa etária, gênero, raça/cor ou condição ampla só aparecem quando relevantes e seguros;
- [ ] combinações de data, local, cargo, unidade, evento raro e perfil demográfico foram avaliadas em conjunto;
- [ ] citações e excertos não permitem reconhecer a pessoa pelo contexto;
- [ ] instituição, unidade, patente/cargo foram generalizados quando identificariam uma pessoa;
- [ ] contagens pequenas ou recortes raros foram suprimidos/generalizados quando houver risco;
- [ ] a prévia foi revisada como um visitante externo a veria, não campo a campo isoladamente.

Qualquer nome individual é bloqueio absoluto. “O nome já saiu na imprensa” não autoriza republicação pelo OVPDH.

## 5. Localização e tempo

- [ ] município e UF estão corretos e podem ser públicos;
- [ ] bairro/comunidade foi explicitamente autorizado ou generalizado;
- [ ] endereço, número, complemento, ponto de referência identificável e coordenada exata não aparecem;
- [ ] mapa usa somente derivação pública (`deslocado`, `centroide`, `area` ou `omitido`);
- [ ] precisão apresentada ao público corresponde à política aprovada;
- [ ] data/hora foram reduzidas quando a precisão contribuir para reidentificação;
- [ ] arquivos e textos não reintroduzem localização removida da ficha.

## 6. Fontes, documentos e mídia

- [ ] cada fonte pública possui título/referência apropriados e classe de acesso conferida;
- [ ] direitos de divulgação foram confirmados e registrados;
- [ ] documento sensível usa arquivo derivado aprovado, nunca o original restrito;
- [ ] tarjas removeram permanentemente texto, imagem, camadas, comentários e conteúdo pesquisável;
- [ ] metadados do arquivo foram limpos quando revelavam autor, pessoa, dispositivo ou localização;
- [ ] OCR, miniaturas, legendas, transcrições e versões para download foram conferidos;
- [ ] links externos não expõem material restrito nem contornam a decisão do Observatório;
- [ ] áudio e vídeo foram editados para remover voz, rosto, placa, uniforme nominal ou outro identificador quando necessário;
- [ ] o checksum do derivado aprovado é o mesmo do arquivo servido publicamente;
- [ ] alternativa acessível não reintroduz informação removida.

## 7. Segurança da projeção

- [ ] a página usa somente `ocorrencia_publicacoes.projecao_publica` vigente;
- [ ] nenhum campo restrito foi serializado e apenas campos da lista positiva estão presentes;
- [ ] busca, filtros, indicadores, mapa e coleções respeitam a mesma projeção;
- [ ] HTML, JSON, atributos, dados estruturados e respostas de erro foram inspecionados;
- [ ] cache anterior foi invalidado sem ser a fonte primária da autorização;
- [ ] ocorrência despublicada desaparece de página, busca, coleção, sitemap e indicadores públicos;
- [ ] acesso direto por ID ou URL não revela versão interna, rascunho ou publicação encerrada;
- [ ] logs e analytics não recebem dados pessoais ou conteúdo restrito.

## 8. Qualidade editorial e acessibilidade

- [ ] título e resumo público são claros, factuais e compreensíveis;
- [ ] siglas e termos técnicos têm explicação ou glossário contextual;
- [ ] período, município/UF, tipos de violência e fontes aparecem de modo consistente;
- [ ] imagens possuem alternativa textual segura e útil;
- [ ] links e arquivos possuem rótulos compreensíveis;
- [ ] prévia foi conferida em largura ampla e móvel;
- [ ] o conteúdo permanece compreensível sem depender apenas de cor, mapa ou imagem;
- [ ] data da publicação/atualização e metodologia aplicável estão visíveis.

## 9. Bloqueios automáticos mínimos

O sistema deve impedir a publicação quando detectar:

1. versão inexistente, não aprovada ou diferente da prévia;
2. resumo público vazio;
3. ausência de município/UF ou de regra territorial pública;
4. violação sem fonte vinculada ou sem justificativa de suficiência aprovada;
5. nome individual em qualquer campo marcado como público;
6. endereço ou geometria exata no payload público;
7. original restrito selecionado como arquivo público;
8. documento sem direitos confirmados;
9. checklist pendente/reprovado;
10. conflito de versão, checksum ou publicação concorrente;
11. curador autor tentando decidir o próprio caso;
12. pendência de qualificação classificada como bloqueadora.

Detecção automática complementa, mas não substitui, a revisão humana de contexto e reidentificação.

## 10. Evidência a armazenar

Para cada execução do checklist:

| Campo | Conteúdo |
| --- | --- |
| ocorrência/versão | IDs canônicos e número da versão |
| projeção | checksum da prévia pública |
| item | código estável do requisito |
| resultado | confirmado, não se aplica, pendente ou reprovado |
| justificativa | obrigatória para não se aplica/reprovado |
| evidência | referência a fonte, derivado, decisão ou teste; sem copiar dado restrito |
| responsável | usuário que confirmou |
| instante | `timestamptz` |

Confirmações não são copiadas automaticamente para nova versão. Itens puramente técnicos podem ser reavaliados automaticamente e ainda assim registram o novo checksum.

## 11. Despublicação emergencial

Ao identificar exposição, risco ou erro grave:

1. curador/administrador informa motivo e categoria;
2. `UnpublishOccurrence` encerra imediatamente a publicação e retorna o caso a `aprovado`;
3. busca, coleções, mapas, sitemap e caches deixam de servi-lo;
4. evento imutável registra quem, quando e por quê, sem repetir o dado exposto;
5. coordenação avalia comunicação, incidente e necessidade de remoção em serviços externos;
6. correção percorre nova versão, aprovação, checklist e publicação.

Não se corrige conteúdo público diretamente nem se apaga o histórico para esconder o incidente.

## 12. Testes de aceite da T014

- [ ] caso seguro com todos os itens confirmados pode ser publicado;
- [ ] cada bloqueio automático impede a operação isoladamente;
- [ ] alterar a prévia invalida o checklist pelo checksum;
- [ ] nome inserido em título, legenda, metadado ou documento é detectado pelo teste correspondente;
- [ ] arquivo derivado e original não são intercambiáveis;
- [ ] acesso direto a caso despublicado deixa de funcionar publicamente;
- [ ] exceções e “não se aplica” ficam justificadas e auditáveis;
- [ ] o teste de projeção comprova ausência estrutural de campos restritos;
- [ ] revisão contextual cobre combinações que reidentificam sem citar nome;
- [ ] nova versão exige nova conferência.

## 13. Responsabilidade

O autor prepara e corrige; o curador independente confere conteúdo, relações e risco; o sistema verifica bloqueios estruturais; o administrador atua em exceções justificadas; a coordenação define respostas a incidentes. Nenhuma dessas camadas substitui as demais.
