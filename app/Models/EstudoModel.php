<?php

namespace App\Models;

use CodeIgniter\Model;

class EstudoModel extends Model
{
    protected $table         = 'estudos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'titulo',
        'slug',
        'resumo',
        'conteudo',
        'autores',
        'publicado',
        'destaque',
        'arquivo_pdf',
    ];

    protected $useTimestamps = true;

    protected $validationRules = [
        'titulo' => 'required|min_length[5]',
        'slug'   => 'required|is_unique[estudos.slug,id,{id}]',
    ];

    /**
     * Gera slug a partir do título.
     */
    public function gerarSlug(string $titulo): string
    {
        $slug = strtolower($titulo);
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', trim($slug));
        return $slug;
    }
}
