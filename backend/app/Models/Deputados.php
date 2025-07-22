<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deputados extends Model
{
    protected $table = 'deputados';
    protected $primaryKey = 'id';

    // Definindo os campos que podem ser preenchidos via mass assignment (opcional mas recomendado)
    protected $fillable = [
        'nome',
        'partido',
        'uf',
        'email',
        'telefone',
        'url_foto',
        'id_api',
    ];

    // Relacionamento: um deputado tem muitas despesas
    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class, 'deputado_id', 'id');
    }
}
