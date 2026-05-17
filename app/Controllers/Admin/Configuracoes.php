<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
class Configuracoes extends BaseController {
    public function index(): string { return view('admin/configuracoes/index', ['title' => 'Configuracoes']); }
    public function novo(): string  { return view('admin/configuracoes/form',  ['title' => 'Novo']); }
    public function salvar()        { return redirect()->back()->with('message', 'Salvo!'); }
}
