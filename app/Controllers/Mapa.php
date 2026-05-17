<?php

namespace App\Controllers;

class Mapa extends BaseController
{
    public function index(): string    { return view('mapa/index', ['title' => 'Mapa']); }
    public function novo(): string     { return view('mapa/form',  ['title' => 'Novo']); }
    public function salvar()           { return redirect()->back()->with('message', 'Salvo!'); }
    public function show(string $s): string   { return view('mapa/show', ['title' => $s]); }
    public function editar(string $s): string { return view('mapa/form', ['title' => 'Editar']); }
    public function upload(): string   { return view('mapa/upload', ['title' => 'Upload']); }
    public function armazenar()        { return redirect()->back()->with('message', 'Enviado!'); }
}
