<?php

namespace App\Jobs;

use App\Models\Deputado;
use App\Services\CamaraApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SincronizarDespesas;

/**
 * Job para sincronizar os deputados utilizando a API pública da Câmara dos Deputados.
 *
 * Este job faz a paginação dos deputados, atualiza ou cria seus registros
 * locais e dispara jobs de sincronização das despesas relacionadas a cada deputado.
 */
class SincronizarDeputados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cria uma nova instância do job.
     */
    public function __construct() {}

    /**
     * Executa o job de sincronização dos deputados.
     *
     * @param CamaraApiService $api Serviço para comunicação com a API da Câmara
     * @return void
     */
    public function handle(CamaraApiService $api): void
    {
        $pagina = 1;

        // Realiza paginação até que não haja mais deputados a buscar
        do {
            // Busca uma página de deputados da API
            $dados = $api->listarDeputados($pagina);

            // Coleta os deputados da resposta (ou array vazio)
            $deputados = collect($dados['dados'] ?? []);

            // Itera sobre cada deputado retornado
            $deputados->each(function ($dadosBasicos) use ($api) {
                $idApi = $dadosBasicos['id'];

                // Busca detalhes do deputado pelo ID na API
                $info = $api->detalhesDeputado($idApi)['dados'] ?? null;

                if (!$info) {
                    // Loga o aviso caso não consiga buscar detalhes do deputado
                    Log::warning("Erro ao buscar detalhes do deputado ID {$idApi}");
                    return;
                }

                // Atualiza ou cria o deputado no banco local
                $deputado = Deputado::updateOrCreate(
                    ['id_api' => $idApi],
                    [
                        'nome' => $info['nome'],
                        'partido' => $info['ultimoStatus']['siglaPartido'] ?? null,
                        'uf' => $info['ultimoStatus']['siglaUf'] ?? null,
                        'email' => $info['ultimoStatus']['gabinete']['email'] ?? null,
                        'telefone' => $info['ultimoStatus']['gabinete']['telefone'] ?? null,
                        'url_foto' => $info['ultimoStatus']['urlFoto'] ?? null,
                    ]
                );

                // Dispara um job para sincronizar as despesas desse deputado
                SincronizarDespesas::dispatch($deputado);
            });

            // Verifica se há próxima página para continuar a paginação
            $temMais = collect($dados['links'])->contains(fn($l) => $l['rel'] === 'next');
            $pagina++;

        } while ($temMais);
    }
}
