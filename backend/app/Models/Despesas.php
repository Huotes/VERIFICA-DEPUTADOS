<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $table = 'despesas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'deputado_id',
        'tipo_despesa',
        'data',
        'valor',
        'descricao',
    ];

    // Relacionamento: despesa pertence a um deputado
    public function deputado(): BelongsTo
    {
        return $this->belongsTo(Deputado::class, 'deputado_id', 'id');
    }
}
