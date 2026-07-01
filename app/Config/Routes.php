<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =====================================================================
// ROTAS PÚBLICAS
// =====================================================================
$routes->get('/', 'Public\Home::index');
$routes->get('sobre', 'Public\Sobre::index');

// Histórico
$routes->get('historico', 'Public\Historico::index');
$routes->get('historico/(:num)', 'Public\Historico::show/$1');

// Produtos
$routes->get('produtos', 'Public\Produtos::index');
$routes->get('produtos/(:num)', 'Public\Produtos::show/$1');

// Link PUC (redirect)
$routes->get('pucminas', function() {
    return redirect()->to('https://www.pucminas.br');
});

// =====================================================================
// AUTENTICAÇÃO (Shield)
// =====================================================================
service('auth')->routes($routes);

// =====================================================================
// ÁREA RESTRITA — requer login
// =====================================================================
$routes->group('painel', ['filter' => 'session'], function($routes) {

    // Dashboard
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Ocorrências
    $routes->get('ocorrencias', 'Admin\Ocorrencias::index');
    $routes->get('ocorrencias/nova', 'Admin\Ocorrencias::create', ['filter' => 'permission:ocorrencias.create']);
    $routes->post('ocorrencias/nova', 'Admin\Ocorrencias::store', ['filter' => 'permission:ocorrencias.create']);
    $routes->get('ocorrencias/(:num)', 'Admin\Ocorrencias::show/$1');
    $routes->get('ocorrencias/(:num)/editar', 'Admin\Ocorrencias::edit/$1', ['filter' => 'permission:ocorrencias.edit']);
    $routes->post('ocorrencias/(:num)/editar', 'Admin\Ocorrencias::update/$1', ['filter' => 'permission:ocorrencias.edit']);
    $routes->post('ocorrencias/(:num)/status', 'Admin\Ocorrencias::updateStatus/$1', ['filter' => 'permission:ocorrencias.review']);

    // Vítimas
    $routes->get('vitimas', 'Admin\Vitimas::index');
    $routes->get('vitimas/nova', 'Admin\Vitimas::create', ['filter' => 'permission:ocorrencias.create']);
    $routes->post('vitimas/nova', 'Admin\Vitimas::store', ['filter' => 'permission:ocorrencias.create']);
    $routes->get('vitimas/(:num)/editar', 'Admin\Vitimas::edit/$1', ['filter' => 'permission:ocorrencias.edit']);
    $routes->post('vitimas/(:num)/editar', 'Admin\Vitimas::update/$1', ['filter' => 'permission:ocorrencias.edit']);

    // Agressores
    $routes->get('agressores', 'Admin\Agressores::index');
    $routes->get('agressores/novo', 'Admin\Agressores::create', ['filter' => 'permission:ocorrencias.create']);
    $routes->post('agressores/novo', 'Admin\Agressores::store', ['filter' => 'permission:ocorrencias.create']);
    $routes->get('agressores/(:num)/editar', 'Admin\Agressores::edit/$1', ['filter' => 'permission:ocorrencias.edit']);
    $routes->post('agressores/(:num)/editar', 'Admin\Agressores::update/$1', ['filter' => 'permission:ocorrencias.edit']);

    // Revisão e Curadoria
    $routes->get('revisao', 'Admin\Revisao::index', ['filter' => 'permission:ocorrencias.review']);
    $routes->get('revisao/(:num)', 'Admin\Revisao::show/$1', ['filter' => 'permission:ocorrencias.review']);
    $routes->post('revisao/(:num)/acao', 'Admin\Revisao::acao/$1', ['filter' => 'permission:ocorrencias.review']);

    // Relatórios
    $routes->get('relatorios', 'Admin\Relatorios::index', ['filter' => 'permission:relatorios.view']);

    // Administração de Usuários (admin+)
    $routes->get('usuarios', 'Admin\Usuarios::index', ['filter' => 'permission:users.manage']);
    $routes->get('usuarios/novo', 'Admin\Usuarios::create', ['filter' => 'permission:users.create']);
    $routes->post('usuarios/novo', 'Admin\Usuarios::store', ['filter' => 'permission:users.create']);
    $routes->get('usuarios/(:num)/editar', 'Admin\Usuarios::edit/$1', ['filter' => 'permission:users.edit']);
    $routes->post('usuarios/(:num)/editar', 'Admin\Usuarios::update/$1', ['filter' => 'permission:users.edit']);
    $routes->post('usuarios/(:num)/toggle', 'Admin\Usuarios::toggleStatus/$1', ['filter' => 'permission:users.edit']);

    // Histórico (admin)
    $routes->get('historico', 'Admin\Historico::index', ['filter' => 'permission:historico.manage']);
    $routes->get('historico/novo', 'Admin\Historico::create', ['filter' => 'permission:historico.manage']);
    $routes->post('historico/novo', 'Admin\Historico::store', ['filter' => 'permission:historico.manage']);

    // Produtos (admin)
    $routes->get('produtos-admin', 'Admin\ProdutosAdmin::index', ['filter' => 'permission:produtos.manage']);
    $routes->get('produtos-admin/novo', 'Admin\ProdutosAdmin::create', ['filter' => 'permission:produtos.manage']);
    $routes->post('produtos-admin/novo', 'Admin\ProdutosAdmin::store', ['filter' => 'permission:produtos.manage']);
});
