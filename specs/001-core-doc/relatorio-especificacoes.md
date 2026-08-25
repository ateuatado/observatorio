# Relatório de Especificações do Sistema OVPDH

**Projeto:** Observatório de Violência Policial e Direitos Humanos (OVPDH)

**Atualizado em:** 24 de agosto de 2026
**Situação da especificação:** Modelo de dados lógico consolidado; decisões de curadoria e privacidade pendentes de homologação.

## 1. Apresentação

O OVPDH será um sistema de informação para apoiar o registro, a organização, a revisão e a divulgação responsável de dados sobre violência policial e direitos humanos. Seu objetivo é oferecer uma área pública de consulta para a sociedade e uma área restrita de trabalho para a equipe do Observatório.

O sistema é pensado para atender pesquisadores, jornalistas, parceiros institucionais e a comunidade em geral, sem expor informações que ainda não tenham sido verificadas pela equipe responsável.

## 2. Organização geral do sistema

O sistema terá duas áreas principais:

- **Área pública:** apresentará informações institucionais sobre o Observatório, o acervo histórico, as produções acadêmicas e somente os registros que tenham sido oficialmente publicados.
- **Área interna:** será acessada por usuários autorizados para cadastrar, pesquisar, editar, revisar e administrar os registros e os usuários do sistema.

Essa separação evita que informações preliminares, incompletas ou sensíveis sejam expostas antes da revisão.

## 3. Registros de ocorrências

A ocorrência é o agregado central do sistema. Cada registro reúne data/período, local, relato interno, resumo público e seu estado de curadoria. Uma ocorrência contém uma ou várias violações e pode envolver várias vítimas, agentes ou instituições e fontes.

O legado confirmou relações específicas entre vítima–violação, agente–violação e fonte–violação. Por isso, o formulário não deve reduzir o caso a uma lista plana: ele será organizado em etapas para ocorrência, violações, pessoas/grupos afetados, agentes/instituições, fontes/evidências e privacidade/publicação.

O backup também contém 56 denúncias recebidas. Denúncia é entrada de triagem, não ocorrência verificada; contato e pedidos de apoio permanecem restritos, e qualquer conversão mantém o protocolo original.

## 4. Etapas de revisão e publicação

Para assegurar a qualidade e a confiabilidade do acervo, toda ocorrência seguirá um ciclo de trabalho:

| Situação | Significado |
| --- | --- |
| **Rascunho** | Registro em preparação. Ainda pode ser corrigido ou complementado e não é público. |
| **Em revisão** | Registro enviado para análise de outra pessoa autorizada. |
| **Aprovado** | Registro validado pela curadoria. Pode ser consultado internamente por usuários comuns, mas ainda não aparece no site público. |
| **Publicado** | Registro autorizado para divulgação e visível no site público. |
| **Rejeitado** | Registro que não foi aceito na revisão. A justificativa da rejeição é obrigatória; ele pode ser corrigido e submetido novamente. |

Após a aprovação, um registro pode ser publicado. Caso seja necessário retirar uma informação do site, ele pode ser despublicado e volta à situação de aprovado. Também é possível desaprovar um registro para que retorne a rascunho e seja corrigido.

Todas essas ações ficam registradas no histórico do sistema, com identificação da pessoa responsável, data, horário, situação anterior, nova situação e comentário quando houver. Isso permite acompanhar as decisões tomadas sobre cada registro.

## 5. Perfis de acesso

Para evitar excesso de níveis de acesso, foram definidos quatro perfis principais:

| Perfil | Responsabilidades |
| --- | --- |
| **Usuário comum** | Pesquisa, na área interna, registros que já estejam aprovados. Não altera registros. |
| **Curador** | Cadastra, edita e exclui registros; envia registros para revisão; aprova, rejeita, publica, despublica e devolve registros para correção. Não realiza curadoria de um registro criado por ele próprio. |
| **Administrador** | Realiza todas as ações de curadoria e administra o sistema, os usuários, os perfis e as permissões. Suas ações também são registradas. |
| **Superadministrador** | Realiza alterações relacionadas ao site institucional. As funções exatas desse perfil serão detalhadas em uma especificação própria. |

O impedimento de o curador revisar o próprio registro busca preservar a imparcialidade do processo. Em situações excepcionais, o administrador poderá atuar no próprio registro, ficando a ação registrada para auditoria.

## 6. Proteção e divulgação dos dados

Somente registros com situação **publicado** serão disponibilizados no site público. Registros em rascunho, em revisão, aprovados ou rejeitados permanecem fora da área pública.

O sistema adotará autenticação para a área interna e permissões de acesso para cada perfil. Relato público, observações internas, identificação pessoal, depoimentos, endereço exato e geometria terão classificações de acesso distintas.

Ficou definido que nomes individuais nunca serão públicos. Curadores consultam dados completos com auditoria; alunos veem dados anonimizados por padrão e só recebem acesso restrito por projeto e ocorrência. Município e UF podem ser públicos, bairro depende de curadoria e endereço/coordenada exatos são restritos. Documentos públicos sensíveis serão cópias efetivamente expurgadas, mantendo o original protegido.

## 7. Tecnologia e qualidade do desenvolvimento

O sistema será desenvolvido com as seguintes tecnologias:

- PHP 8.2 ou superior;
- CodeIgniter 4;
- CodeIgniter Shield para autenticação e permissões;
- PostgreSQL com PostGIS para o banco de dados e georreferenciamento;
- Bootstrap instalado localmente para a interface.

O projeto não dependerá de arquivos de estilo, scripts, fontes ou imagens carregados da internet. Esses recursos ficarão armazenados no próprio sistema, o que reduz dependências externas e aumenta a previsibilidade de funcionamento.

O desenvolvimento seguirá o padrão MVC: as telas apresentam os dados, os controladores organizam as requisições e as Models realizam o acesso e a organização dos dados. Mudanças no banco serão registradas por migrations, permitindo reproduzir a estrutura do sistema com segurança em outros ambientes.

## 8. Publicação e manutenção

O sistema ficará em uma VPS para que parceiros possam acompanhá-lo. As atualizações serão publicadas a partir de versões identificadas no repositório Git. Antes de atualizações que afetem dados, serão previstos backup do banco de dados e dos arquivos enviados, execução controlada das migrations e possibilidade de retorno à versão anterior.

Credenciais e configurações sensíveis não serão incluídas no código versionado.

## 9. Próximas definições necessárias

Antes de iniciar as migrations complementares, ainda precisam ser homologados:

1. O mapa dos status legados para o fluxo novo.
2. A finalidade e a necessidade dos atributos demográficos sensíveis.
3. Os critérios mínimos de suficiência de fontes.
4. Os parâmetros técnicos para generalização geográfica.
5. As funções concretas do superadministrador na manutenção do site institucional.

O documento `arquitetura-dados.md` consolida as entidades, relações, regras físicas e consequências para a interface. A ficha interna já foi organizada em sete seções e pode seguir para wireframes; as decisões pendentes condicionam a migração e os detalhes de validação, não o esqueleto da interface.
