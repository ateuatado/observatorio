<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro de permissão granular — OVP
 *
 * Uso nas rotas:
 *   ['filter' => 'permissao:acoes.gerir']
 *   ['filter' => 'permissao:dados.sigiloso']
 *
 * Retorna 403 quando o usuário está autenticado mas não possui a permissão.
 * Redireciona para /login quando não está autenticado.
 */
class PermissaoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Não autenticado → login
        if (! auth()->loggedIn()) {
            return redirect()->to('/login')
                ->with('error', 'Faça login para acessar esta área.');
        }

        // Sem permissão especificada na rota → bloqueia por segurança
        if (empty($arguments)) {
            return $this->negar403('Acesso negado: permissão não especificada.');
        }

        $user = auth()->user();

        // Verifica cada permissão exigida (AND: todas devem ser satisfeitas)
        foreach ($arguments as $permissao) {
            if (! $user->can($permissao)) {
                return $this->negar403(
                    "Seu perfil não tem autorização para esta ação ({$permissao})."
                );
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada necessário
    }

    // ----------------------------------------------------------------
    // Helpers privados
    // ----------------------------------------------------------------

    private function negar403(string $mensagem)
    {
        // Requisição AJAX → resposta JSON
        if (service('request')->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['erro' => $mensagem]);
        }

        // Requisição normal → página de erro com mensagem
        return redirect()->back()
            ->with('error', $mensagem)
            ->withCookies();
    }
}
