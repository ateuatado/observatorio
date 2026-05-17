<?php

namespace App\Controllers;

class Relatorios extends BaseController
{
    public function index(): string    { return view('relatorios/index', ['title' => 'Relatorios']); }
    public function novo(): string     { return view('relatorios/form',  ['title' => 'Novo']); }
    public function salvar()           { return redirect()->back()->with('message', 'Salvo!'); }
    public function show(string $s): string   { return view('relatorios/show', ['title' => $s]); }
    public function editar(string $s): string { return view('relatorios/form', ['title' => 'Editar']); }
    public function upload(): string   { return view('relatorios/upload', ['title' => 'Upload']); }
    public function armazenar()        { return redirect()->back()->with('message', 'Enviado!'); }
}
