<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

class Usuarios extends BaseController
{
    public function index(): string
    {
        $userModel = model(UserModel::class);
        $usuarios  = $userModel->findAll();
        $db        = \Config\Database::connect();
 
         // Enriquecer com grupos
         foreach ($usuarios as &$u) {
             $groups = $db->table('auth_groups_users')
                 ->select('group')
                 ->where('user_id', $u->id)
                 ->get()->getResultArray();
             $u->grupos = array_column($groups, 'group');
         }

        return view('admin/usuarios/index', [
            'title'    => 'Usuários — OVPDH',
            'usuarios' => $usuarios,
            'user'     => auth()->user(),
        ]);
    }

    public function create(): string
    {
        return view('admin/usuarios/create', [
            'title' => 'Novo Usuário — OVPDH',
            'user'  => auth()->user(),
        ]);
    }

    public function store()
    {
        $userModel = model(UserModel::class);

        $newUser = new \CodeIgniter\Shield\Entities\User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'active'   => 1,
        ]);

        $userModel->save($newUser);
        $savedUser = $userModel->findById($userModel->getInsertID());
        $group = $this->request->getPost('group');
        if ($group) {
            $savedUser->addGroup($group);
        }

        return redirect()->to('/painel/usuarios')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(int $id): string
    {
        $userModel = model(UserModel::class);
        $usuario   = $userModel->findById($id);
        if (! $usuario) throw new \CodeIgniter\Exceptions\PageNotFoundException();

        $db = \Config\Database::connect();
        $groups = $db->table('auth_groups_users')
            ->select('group')
            ->where('user_id', $id)
            ->get()->getResultArray();
        $usuario->grupos = array_column($groups, 'group');

        return view('admin/usuarios/edit', [
            'title'   => 'Editar Usuário — OVPDH',
            'usuario' => $usuario,
            'user'    => auth()->user(),
        ]);
    }

    public function update(int $id)
    {
        $userModel = model(UserModel::class);
        $usuario   = $userModel->findById($id);
        if (! $usuario) throw new \CodeIgniter\Exceptions\PageNotFoundException();
 
        $db = \Config\Database::connect();
        // Atualizar grupo
        $db->table('auth_groups_users')->where('user_id', $id)->delete();
        $grupo = $this->request->getPost('group');
        if ($grupo) {
            $usuario->addGroup($grupo);
        }
 
        return redirect()->to('/painel/usuarios')->with('success', 'Usuário atualizado.');
    }
 
    public function toggleStatus(int $id)
    {
        $userModel = model(UserModel::class);
        $usuario   = $userModel->findById($id);
        if (! $usuario) throw new \CodeIgniter\Exceptions\PageNotFoundException();
 
        $db = \Config\Database::connect();
        $newActive = $usuario->active ? 0 : 1;
        $db->table('users')->where('id', $id)->update(['active' => $newActive]);
 
        $msg = $newActive ? 'Usuário ativado.' : 'Usuário desativado.';
        return redirect()->to('/painel/usuarios')->with('success', $msg);
    }
}
