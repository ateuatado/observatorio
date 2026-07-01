<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * Grupo padrão para novos usuários registrados.
     */
    public string $defaultGroup = 'voluntario';

    /**
     * Grupos disponíveis no sistema OVPDH.
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Administrador',
            'description' => 'Controle total do sistema. Gerencia configurações, usuários e todos os módulos.',
        ],
        'admin' => [
            'title'       => 'Administrador',
            'description' => 'Gerencia ocorrências, revisões, publicações e usuários comuns.',
        ],
        'voluntario' => [
            'title'       => 'Voluntário',
            'description' => 'Cadastra e edita ocorrências. Não pode publicar — aguarda revisão.',
        ],
        'colaborador' => [
            'title'       => 'Colaborador',
            'description' => 'Revisa e comenta ocorrências cadastradas por voluntários.',
        ],
        'advogado' => [
            'title'       => 'Advogado(a)',
            'description' => 'Acessa dados jurídicos, exporta relatórios e acompanha processos.',
        ],
        'academico' => [
            'title'       => 'Acadêmico(a)',
            'description' => 'Acessa dados consolidados e gera relatórios para pesquisa.',
        ],
        'ativista' => [
            'title'       => 'Ativista de Direitos Humanos',
            'description' => 'Acesso de leitura a dados consolidados e publicados.',
        ],
    ];

    /**
     * Permissões disponíveis no sistema OVPDH.
     */
    public array $permissions = [
        // Admin
        'admin.access'          => 'Acessa a área administrativa',
        'admin.settings'        => 'Acessa configurações do sistema',

        // Usuários
        'users.manage'          => 'Gerencia todos os usuários',
        'users.create'          => 'Cria novos usuários',
        'users.edit'            => 'Edita usuários existentes',
        'users.delete'          => 'Exclui usuários',

        // Ocorrências
        'ocorrencias.create'    => 'Cadastra novas ocorrências',
        'ocorrencias.edit'      => 'Edita ocorrências existentes',
        'ocorrencias.delete'    => 'Exclui ocorrências',
        'ocorrencias.review'    => 'Revisa e aprova ocorrências',
        'ocorrencias.publish'   => 'Publica ocorrências',
        'ocorrencias.view_all'  => 'Visualiza todas as ocorrências (todos status)',

        // Relatórios
        'relatorios.view'       => 'Visualiza relatórios consolidados',
        'relatorios.export'     => 'Exporta relatórios',

        // Histórico e Produtos
        'historico.manage'      => 'Gerencia o acervo histórico',
        'produtos.manage'       => 'Gerencia publicações e produtos acadêmicos',
    ];

    /**
     * Matriz de permissões por grupo.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'ocorrencias.*',
            'relatorios.*',
            'historico.*',
            'produtos.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'ocorrencias.create',
            'ocorrencias.edit',
            'ocorrencias.delete',
            'ocorrencias.review',
            'ocorrencias.publish',
            'ocorrencias.view_all',
            'relatorios.view',
            'relatorios.export',
            'historico.manage',
            'produtos.manage',
        ],
        'voluntario' => [
            'admin.access',
            'ocorrencias.create',
            'ocorrencias.edit',
        ],
        'colaborador' => [
            'admin.access',
            'ocorrencias.view_all',
            'ocorrencias.review',
            'relatorios.view',
        ],
        'advogado' => [
            'admin.access',
            'ocorrencias.view_all',
            'relatorios.view',
            'relatorios.export',
        ],
        'academico' => [
            'admin.access',
            'ocorrencias.view_all',
            'relatorios.view',
            'relatorios.export',
        ],
        'ativista' => [
            'admin.access',
            'ocorrencias.view_all',
            'relatorios.view',
        ],
    ];
}
