# Wireframes de baixa fidelidade — consulta pública

**Versão:** 0.1.0  
**Data:** 25 de agosto de 2026  
**Tarefa:** T010

## 1. Navegação pública

```text
OVPDH       Início  Casos  Coleções  Acervo  Produções  Sobre    Área da equipe
```

“Casos” e “Coleções” entram no menu apenas quando existir conteúdo publicado. A área pública não usa termos internos como rascunho, curadoria, versão de trabalho ou ID técnico.

## 2. Busca de casos

```text
Casos documentados
Consulte ocorrências revisadas e publicadas pelo Observatório.

[Buscar por tema, lugar ou palavra________________________] [Buscar]

Filtros
Período [de ____ até ____]  UF [Todas ▾]  Município [Todos ▾]
Tipo de violação [Todos ▾]                         [Limpar filtros]

42 resultados                                      Ordenar: mais recentes ▾
┌──────────────────────────────────────────────────────────────────────────────┐
│ Abordagem durante manifestação                                              │
│ São Paulo (SP) · 2024 · Violência física                                    │
│ Resumo público em duas ou três linhas, sem nomes ou localização exata.      │
│ [Consultar caso]                                                             │
├──────────────────────────────────────────────────────────────────────────────┤
│ Outro caso publicado...                                                      │
└──────────────────────────────────────────────────────────────────────────────┘
```

- Os filtros usam parâmetros na URL e funcionam sem JavaScript.
- Contagens pequenas não são usadas para cruzamentos que facilitem reidentificação.
- O estado vazio explica os filtros aplicados e oferece “Limpar filtros”.
- Nenhum resultado exibe nome individual, endereço ou coordenada precisa.

## 3. Página pública do caso

```text
Casos / São Paulo / Abordagem durante manifestação

Abordagem durante manifestação
São Paulo (SP) · período de 2024

RESUMO
Texto público revisado e independente das observações internas.

O QUE FOI DOCUMENTADO
[Violência física] [Abordagem] [Outras categorias autorizadas]

PESSOAS E GRUPOS ATINGIDOS
Informações agregadas e generalizadas, somente quando não identificáveis.

AGENTES E INSTITUIÇÕES
Instituições, unidades, cargos ou patentes autorizados; nunca nomes individuais.

FONTES PÚBLICAS
1. Título da fonte — instituição, data [Abrir fonte externa]
2. Documento derivado e expurgado [Baixar PDF acessível]

LOCALIZAÇÃO
Município ou área generalizada. Sem endereço ou ponto exato.

Sobre este registro: metodologia, data da publicação e última revisão.
```

O caso não apresenta espaço vazio onde um nome foi ocultado. A projeção pública já é construída sem o campo proibido. Documentos derivados podem mostrar tarjas permanentes quando necessário preservar a leitura contextual.

## 4. Lista de coleções

```text
Coleções
Percursos editoriais organizados por tema, território ou período.

┌──────────────────────────────┐  ┌──────────────────────────────┐
│ Manifestações e espaço público│  │ Memória de um território    │
│ 12 casos publicados           │  │ 8 casos publicados          │
│ Breve apresentação editorial  │  │ Breve apresentação editorial│
│ [Explorar coleção]            │  │ [Explorar coleção]          │
└──────────────────────────────┘  └──────────────────────────────┘
```

## 5. Detalhe da coleção

```text
Coleções / Manifestações e espaço público

Manifestações e espaço público
Introdução editorial, período e critérios do recorte.

[12 casos] [2013–2025] [3 localidades]

Casos nesta coleção
[mesmo componente acessível usado na busca pública]

Metodologia e créditos editoriais
```

- A coleção referencia versões publicadas; não publica um caso por si mesma.
- Caso despublicado desaparece imediatamente da coleção pública.
- Ordenação e texto editorial não alteram o conteúdo documental do caso.

## 6. Relação com acervo e produções

Casos, coleções, acervo histórico e produções acadêmicas mantêm páginas próprias, mas podem apresentar relações editoriais explícitas:

- “Este caso integra a coleção…”;
- “Conheça o documento histórico relacionado…”;
- “Leia a produção acadêmica relacionada…”.

Essas relações são curadas, não inferidas automaticamente por semelhança textual.

## 7. Estados e acessibilidade

- Títulos seguem hierarquia única e descritiva.
- Filtros possuem rótulos visíveis e resumo de resultados atualizado.
- Paginação informa página atual e total.
- Links externos e downloads indicam destino e formato.
- Mapas futuros terão alternativa textual; a Etapa 1 pode usar descrição territorial sem mapa.
- Erro temporário nunca expõe detalhes internos, SQL, IDs restritos ou caminhos de arquivo.
- Conteúdo despublicado retorna página não encontrada, sem confirmar existência interna.

## 8. Critérios de aceite de T010

- Busca, coleção e caso público estão desenhados.
- A linguagem é compreensível fora da equipe técnica.
- A projeção pública não contém nomes individuais, endereço exato ou coordenadas precisas.
- Filtros e navegação funcionam progressivamente sem JavaScript.
- Coleções não alteram o estado de publicação dos casos.
- Acervo e produções permanecem integrados à arquitetura do portal.

