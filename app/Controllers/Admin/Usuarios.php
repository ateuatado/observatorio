<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
class Usuarios extends BaseController {
    public function index(): string { return view('admin/usuarios/index', ['title' => 'Usuarios']); }
    public function novo(): string  { return view('admin/usuarios/form',  ['title' => 'Novo']); }
    public function salvar()        { return redirect()->back()->with('message', 'Salvo!'); }
}
