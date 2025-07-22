<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CamaraApiService
{
    protected string $baseUrl = 'https://dadosabertos.camara.leg.br/api/v2';

    /**
     * Retorna uma lista paginada de deputados.
     */
    public function listarDeputados(int $pagina = 1, int $itens = 100): array
    {
        return Http::get("{$this->baseUrl}/deputados", [
            'pagina' => $pagina,
            'itens' => $itens,
            'ordem' => 'ASC',
            'ordenarPor' => 'nome',
        ])->json();
    }

    /**
     * Retorna os detalhes de um deputado específico.
     */
    public function detalhesDeputado(int $id): array
    {
        return Http::get("{$this->baseUrl}/deputados/{$id}")->json();
    }

    /**
     * Retorna as despesas paginadas de um deputado.
     */
    public function listarDespesas(int $idDeputado, int $pagina = 1, int $itens = 100): array
    {
        return Http::get("{$this->baseUrl}/deputados/{$idDeputado}/despesas", [
            'pagina' => $pagina,
            'itens' => $itens,
        ])->json();
    }
}
