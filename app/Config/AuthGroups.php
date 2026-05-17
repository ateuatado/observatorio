<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'colaborador';

    /**
     * --------------------------------------------------------------------
     * Groups — Observatório de Violência Policial
     * --------------------------------------------------------------------
     * Perfis definidos conforme deliberação da equipe do OVP.
     * Cada perfil corresponde a um nível de responsabilidade no fluxo
     * de cadastro, curadoria e validação dos dados.
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Administrador',
            'description' => 'Responsável técnico. Gerencia usuários e configurações do sistema.',
        ],
        'curador' => [
            'title'       => 'Curador',
            'description' => 'Valida ocorrências, cria Ações de Segurança e faz vínculos analíticos.',
        ],
        'curador_juridico' => [
            'title'       => 'Curador Jurídico',
            'description' => 'Acessa dados sigilosos e acrescenta pareceres jurídicos aos registros.',
        ],
        'pesquisador' => [
            'title'       => 'Pesquisador',
            'description' => 'Registra e publica ocorrências. Acessa dados restritos (não sigilosos).',
        ],
        'colaborador' => [
            'title'       => 'Colaborador',
            'description' => 'Registra ocorrências básicas. O conteúdo fica em revisão até ser validado.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions — OVP
     * --------------------------------------------------------------------
     * Permissões granulares do sistema.
     * Prefixos: ocorrencias | acoes | dados | juridico | admin | users
     */
    public array $permissions = [
        // Ocorrências
        'ocorrencias.criar'    => 'Pode registrar novas ocorrências',
        'ocorrencias.publicar' => 'Pode publicar ocorrências diretamente (sem fila de revisão)',
        'ocorrencias.editar'   => 'Pode editar ocorrências existentes de qualquer pesquisador',
        'ocorrencias.deletar'  => 'Pode excluir (soft-delete) ocorrências',
        'ocorrencias.sigilosa' => 'Pode alterar visibilidade para sigiloso',

        // Ações de Segurança
        'acoes.gerir'          => 'Pode criar, editar e arquivar Ações de Segurança',
        'acoes.vincular'       => 'Pode vincular/desvincular ocorrências a Ações de Segurança',

        // Visibilidade de dados
        'dados.restrito'       => 'Pode visualizar registros com visibilidade restrita',
        'dados.sigiloso'       => 'Pode visualizar registros com visibilidade sigilosa',

        // Pareceres jurídicos
        'juridico.anotar'      => 'Pode acrescentar e editar pareceres jurídicos nos registros',

        // Administração
        'admin.access'         => 'Pode acessar o painel administrativo',
        'users.create'         => 'Pode criar novos usuários',
        'users.edit'           => 'Pode editar usuários existentes',
        'users.delete'         => 'Pode desativar usuários',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix — OVP
     * --------------------------------------------------------------------
     * Define quais permissões cada grupo possui.
     */
    public array $matrix = [
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
        ],
        'curador' => [
            'ocorrencias.criar',
            'ocorrencias.publicar',
            'ocorrencias.editar',
            'ocorrencias.deletar',
            'ocorrencias.sigilosa',
            'acoes.gerir',
            'acoes.vincular',
            'dados.restrito',
            'dados.sigiloso',
            'juridico.anotar',
        ],
        'curador_juridico' => [
            'dados.restrito',
            'dados.sigiloso',
            'juridico.anotar',
        ],
        'pesquisador' => [
            'ocorrencias.criar',
            'ocorrencias.publicar',
            'ocorrencias.editar',
            'dados.restrito',
        ],
        'colaborador' => [
            'ocorrencias.criar',
        ],
    ];
}
