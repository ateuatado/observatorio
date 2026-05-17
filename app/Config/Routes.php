<?php

use CodeIgniter\Router\RouteCollection;

/**
 * OVP-SP — Rotas do sistema
 *
 * @var RouteCollection $routes
 */

// ================================================================
// ÁREA PÚBLICA — sem autenticação
// ================================================================
$routes->get('/',          'Home::index');
$routes->get('/sobre',     'Home::sobre');
$routes->get('/ocorrencias',          'Ocorrencias::index');           // listagem pública
$routes->get('/ocorrencias/(:num)',   'Ocorrencias::show/$1');         // detalhe público
$routes->get('/estudos',              'Estudos::index');               // listagem pública

// Rotas estáticas de estudos com filtro de sessão — antes do (:segment)
$routes->get('/estudos/admin',   'Estudos::listar', ['filter' => 'session']);
$routes->get('/estudos/novo',    'Estudos::novo',   ['filter' => 'session']);
$routes->post('/estudos/salvar', 'Estudos::salvar', ['filter' => 'session']);

// (:segment) público — slugs reais de estudos publicados
$routes->get('/estudos/(:segment)', 'Estudos::show/$1');

// ================================================================
// AUTENTICAÇÃO — Shield
// ================================================================
service('auth')->routes($routes);

// ================================================================
// ÁREA AUTENTICADA — requer login
// ================================================================
$routes->group('', ['filter' => 'session'], static function ($routes) {

    // Dashboard principal
    $routes->get('dashboard', 'Dashboard::index');

    // ----------------------------------------------------------------
    // OCORRÊNCIAS
    // ----------------------------------------------------------------
    $routes->get('ocorrencias/novo',            'Ocorrencias::novo',       ['filter' => 'permissao:ocorrencias.criar']);
    $routes->post('ocorrencias/salvar',         'Ocorrencias::salvar',     ['filter' => 'permissao:ocorrencias.criar']);
    $routes->get('ocorrencias/(:num)/editar',   'Ocorrencias::editar/$1',  ['filter' => 'permissao:ocorrencias.editar']);
    $routes->post('ocorrencias/(:num)/update',  'Ocorrencias::update/$1',  ['filter' => 'permissao:ocorrencias.editar']);
    $routes->get('ocorrencias/(:num)/deletar',  'Ocorrencias::deletar/$1', ['filter' => 'permissao:ocorrencias.deletar']);
    $routes->post('ocorrencias/(:num)/publicar','Ocorrencias::publicar/$1',['filter' => 'permissao:ocorrencias.publicar']);

    // ----------------------------------------------------------------
    // AÇÕES DE SEGURANÇA (somente Curador+)
    // ----------------------------------------------------------------
    $routes->get('acoes-seguranca',                          'AcoesSeguranca::index',         ['filter' => 'permissao:acoes.gerir']);
    $routes->get('acoes-seguranca/novo',                     'AcoesSeguranca::novo',          ['filter' => 'permissao:acoes.gerir']);
    $routes->post('acoes-seguranca/salvar',                  'AcoesSeguranca::salvar',        ['filter' => 'permissao:acoes.gerir']);
    $routes->get('acoes-seguranca/(:num)',                   'AcoesSeguranca::show/$1',       ['filter' => 'permissao:dados.restrito']);
    $routes->get('acoes-seguranca/(:num)/editar',            'AcoesSeguranca::editar/$1',     ['filter' => 'permissao:acoes.gerir']);
    $routes->post('acoes-seguranca/(:num)/update',           'AcoesSeguranca::update/$1',     ['filter' => 'permissao:acoes.gerir']);
    $routes->post('acoes-seguranca/(:num)/arquivar',         'AcoesSeguranca::arquivar/$1',   ['filter' => 'permissao:acoes.gerir']);
    $routes->post('acoes-seguranca/(:num)/vincular',         'AcoesSeguranca::vincular/$1',   ['filter' => 'permissao:acoes.vincular']);
    $routes->post('acoes-seguranca/(:num)/desvincular/(:num)','AcoesSeguranca::desvincular/$1/$2', ['filter' => 'permissao:acoes.vincular']);

    // ----------------------------------------------------------------
    // VÍTIMAS
    // ----------------------------------------------------------------
    $routes->get('vitimas',                   'Vitimas::index');
    $routes->get('vitimas/novo',              'Vitimas::novo');
    $routes->post('vitimas/salvar',           'Vitimas::salvar');
    $routes->get('vitimas/(:num)',            'Vitimas::show/$1');
    $routes->get('vitimas/(:num)/editar',     'Vitimas::editar/$1');
    $routes->post('vitimas/(:num)/update',    'Vitimas::update/$1');
    $routes->get('vitimas/(:num)/deletar',    'Vitimas::deletar/$1');

    // ----------------------------------------------------------------
    // DOCUMENTOS
    // ----------------------------------------------------------------
    $routes->get('documentos',            'Documentos::index');
    $routes->get('documentos/upload',     'Documentos::upload');
    $routes->post('documentos/armazenar', 'Documentos::armazenar');

    // ----------------------------------------------------------------
    // ESTUDOS (área autenticada)
    // ----------------------------------------------------------------
    $routes->get('estudos/admin',             'Estudos::listar');
    $routes->get('estudos/novo',              'Estudos::novo');
    $routes->post('estudos/salvar',           'Estudos::salvar');
    $routes->get('estudos/(:any)/editar',     'Estudos::editar/$1');
    $routes->post('estudos/(:num)/update',    'Estudos::update/$1');
    $routes->post('estudos/(:num)/publicar',  'Estudos::publicar/$1');
    $routes->get('estudos/(:num)/deletar',    'Estudos::deletar/$1');

    // ----------------------------------------------------------------
    // AUDITORIA HISTÓRICA
    // ----------------------------------------------------------------
    $routes->get('auditoria-historica',                           'AuditoriaHistorica::index');
    $routes->get('auditoria-historica/(:num)',                    'AuditoriaHistorica::show/$1');
    $routes->post('auditoria-historica/(:num)/analisar',          'AuditoriaHistorica::analisar/$1');
    $routes->get('auditoria-historica/(:num)/auditar',            'AuditoriaHistorica::auditar/$1');
    $routes->post('auditoria-historica/(:num)/importar',          'AuditoriaHistorica::importar/$1');
    $routes->post('auditoria-historica/(:num)/descartar',         'AuditoriaHistorica::descartar/$1');
    $routes->post('auditoria-historica/reindexar',                'AuditoriaHistorica::reindexar');
    $routes->get('auditoria-historica/arquivo/(:num)',            'AuditoriaHistorica::servirArquivo/$1');

    // ----------------------------------------------------------------
    // ANÁLISE
    // ----------------------------------------------------------------
    $routes->get('relatorios', 'Relatorios::index');
    $routes->get('mapa',       'Mapa::index');

    // ----------------------------------------------------------------
    // ADMIN (requer permissão admin.access)
    // ----------------------------------------------------------------
    $routes->group('admin', ['filter' => 'permissao:admin.access'], static function ($routes) {
        $routes->get('usuarios',          'Admin\Usuarios::index');
        $routes->get('usuarios/novo',     'Admin\Usuarios::novo');
        $routes->post('usuarios/salvar',  'Admin\Usuarios::salvar');
        $routes->get('configuracoes',     'Admin\Configuracoes::index');
    });
});
