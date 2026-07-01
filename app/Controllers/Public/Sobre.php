<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;

class Sobre extends BaseController
{
    public function index(): string
    {
        return view('public/sobre', [
            'title' => 'Sobre o OVPDH — Observatório de Violência Policial e Direitos Humanos',
        ]);
    }
}
