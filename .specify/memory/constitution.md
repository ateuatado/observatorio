# Constituição do Projeto — Observatório de Violência Policial e Direitos Humanos (OVPDH)

## Princípios fundamentais

### I. Stack tecnológica definida e autossuficiente

O sistema DEVE utilizar PHP 8.2 ou superior, CodeIgniter 4, CodeIgniter Shield, PostgreSQL com PostGIS e Bootstrap distribuído localmente.

Bibliotecas de CSS, JavaScript, fontes e imagens necessárias à aplicação DEVEM ser servidas pelo próprio projeto, a partir de `public/assets/`; CDNs e dependências de execução hospedadas na internet NÃO DEVEM ser utilizados. Dependências de backend DEVEM ser declaradas e bloqueadas por `composer.json` e `composer.lock`.

Alterações de stack, inclusão de bibliotecas ou serviços externos exigem justificativa na especificação, avaliação de privacidade e segurança, e aprovação antes da implementação.

### II. MVC rigoroso e responsabilidades explícitas

O projeto DEVE preservar o modelo MVC do CodeIgniter 4. Controllers coordenam requisições, autorização e respostas; Models concentram acesso, persistência e regras relativas aos dados; Views apresentam dados já preparados, sem decisões de negócio ou consultas ao banco.

Nenhuma funcionalidade que lê ou grava dados do domínio pode contornar as Models por meio de consultas diretas em Controllers ou Views. Lógicas reutilizáveis e regras de negócio que excedam a coordenação de uma requisição DEVEM ser extraídas para Services, Libraries ou classes de domínio com responsabilidade clara e testes próprios.

### III. Código limpo, seguro e sustentável

Toda alteração DEVE privilegiar clareza, coesão, baixo acoplamento e simplicidade. Nomes DEVEM expressar intenção; métodos e classes DEVEM ter responsabilidade única; duplicação relevante DEVE ser eliminada; e validações, autorização e tratamento de falhas DEVEM ser explícitos.

Código novo DEVE seguir os padrões e convenções do CodeIgniter 4 e PHP, sem introduzir soluções improvisadas que dificultem manutenção. Dados sensíveis, especialmente dados de vítimas e ocorrências em rascunho, DEVEM ser protegidos por autenticação, autorização por permissões e exposição mínima necessária.

### IV. Banco de dados evolutivo e íntegro

Toda mudança de esquema ou dado estrutural DEVE ser feita por migration e, quando apropriado, seeder versionado. Alterações manuais diretamente no banco não são uma forma válida de evolução do sistema.

Migrations DEVEM ser pequenas, ordenadas, reproduzíveis em ambiente limpo e ter `down()` seguro sempre que tecnicamente possível. Elas DEVEM declarar chaves, índices, tipos e restrições compatíveis com a integridade do domínio. Operações destrutivas, irreversíveis ou potencialmente demoradas DEVEM ter plano documentado de backup, migração e reversão antes de chegar ao VPS.

### V. Ativos de interface separados das Views

Arquivos de Views DEVEM conter somente a marcação e a apresentação estritamente necessárias. É vedado incluir CSS ou JavaScript inline, seja em tags `<style>` e `<script>`, seja em atributos de evento HTML.

Folhas de estilo, scripts, imagens, fontes e demais ativos DEVEM ser organizados em `public/assets/` — por exemplo, `css/`, `js/`, `images/` e `fonts/` — e referenciados pelos layouts apropriados. Essa regra vale também para páginas públicas e administrativas.

### VI. Entregas especificadas, testadas e publicáveis

Cada mudança funcional DEVE começar por uma especificação clara, com critérios de aceite verificáveis, e ser planejada em tarefas pequenas e independentes antes da implementação. A implementação DEVE incluir testes proporcionais ao risco: ao menos testes de Model/Service para regras de domínio e testes de integração para rotas, permissões, persistência e fluxos críticos.

Antes de disponibilizar uma mudança, devem ser executados os testes aplicáveis, verificada a migração em ambiente controlado e revisados os critérios de aceite. Falhas de segurança, exposição indevida de dados ou perda de integridade bloqueiam a publicação.

## Operação e publicação no VPS

O VPS é o ambiente de demonstração para parceiros e DEVE acompanhar as versões aprovadas do sistema. Publicações DEVEM partir de uma revisão identificável no Git, nunca de alterações locais não versionadas.

Antes de cada publicação devem ser preservados backup verificável do banco e dos arquivos enviados por usuários, além de um caminho de retorno para a versão anterior. As migrations DEVEM ser executadas de modo controlado e sua conclusão deve ser verificada. Configurações, credenciais e chaves do ambiente DEVEM permanecer fora do versionamento e nunca ser exibidas em logs, commits ou telas.

## Processo de desenvolvimento e qualidade

O Spec Kit é a fonte de verdade para o planejamento: `spec.md` define o comportamento e os critérios de aceite; `plan.md` define a abordagem técnica; `tasks.md` define a execução granular. Nenhuma implementação deve contradizer esses documentos sem que a especificação seja atualizada e revisada primeiro.

Revisões devem verificar a aderência a esta constituição, ao MVC, às permissões do Shield, às migrations, à separação dos ativos de interface e aos testes aplicáveis. Mudanças devem ser pequenas, rastreáveis e fáceis de revisar.

## Governança

Esta constituição prevalece sobre preferências locais de implementação e sobre instruções de feature que a contradigam. Exceções exigem justificativa registrada na especificação ou no plano, avaliação de impacto e aprovação explícita antes de serem implementadas.

Emendas DEVEM atualizar este documento com uma versão semântica, a data e uma descrição da alteração relevante. Revisões de código e de especificação DEVEM confirmar sua conformidade.

### Histórico de emendas

- **1.1.0 — 2026-08-24**: substitui MySQL/MariaDB por PostgreSQL com PostGIS, conforme o modelo canônico e as migrations homologadas.

**Versão**: 1.1.0 | **Ratificada em**: 2026-08-20 | **Última alteração**: 2026-08-24
