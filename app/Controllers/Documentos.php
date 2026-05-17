<?php

namespace App\Controllers;

class Documentos extends BaseController
{
    public function index(): string   { return view('documentos/index', ['title' => 'Documentos']); }
    public function novo(): string    { return view('documentos/form',  ['title' => 'Novo']); }
    public function salvar()          { return redirect()->back()->with('message', 'Salvo!'); }
    public function show(string $s): string  { return view('documentos/show',  ['title' => $s]); }
    public function editar(string $s): string { return view('documentos/form', ['title' => 'Editar']); }
    public function upload(): string  { return view('documentos/upload', ['title' => 'Upload']); }
    public function armazenar()       { return redirect()->back()->with('message', 'Enviado!'); }
}
