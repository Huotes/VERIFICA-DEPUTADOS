<?php

namespace App\Jobs;

use App\Models\Despesa;
use App\Models\Deputado;
use App\Services\CamaraApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Job para sincronizar as despesas de um deputado específico
 * utilizando a API pública da Câmara dos Deputados.
 *
 * Este job é executado de forma assíncrona e processa todas
 * as páginas de despesas do deputado informado no construtor.
 */
class SincronizarDespesas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Instância do deputado que terá as despesas sincronizadas.
     *
     * @var Deputado
     */
    protected Deputado $deputado;

    /**
     * Cria uma nova instância do job.
     *
     * @param Deputado $deputado Deputado a ser sincronizado
     */
    public function __construct(Deputado $deputado)
    {
        $this->deputado = $deputado;
    }

    /**
     * Executa o job de sincronização das despesas.
     *
     * @param CamaraApiService $api Serviço de comunicação com a API da Câmara
     * @return void
     */
    public function handle(CamaraApiService $api): void
    {
        $pagina = 1;

        // Pagina os resultados enquanto houver despesas a processar
        do {
            // Faz a requisição para buscar despesas do deputado na página atual
            $resposta = $api->listarDespesas($this->deputado->id_api, $pagina);

            // Obtém os dados da resposta (ou array vazio)
            $despesas = collect($resposta['dados'] ?? []);

            // Itera sobre cada despesa retornada
            $despesas->each(function ($gasto) {
                // Cria ou atualiza a despesa no banco de dados
                Despesa::updateOrCreate(
                    ['id' => $gasto['idDocumento']],
                    [
                        'deputado_id' => $this->deputado->id,
                        'tipo_despesa' => $gasto['tipoDespesa'],
                        'data' => $gasto['dataDocumento'],
                        'valor' => $gasto['valorDocumento'],
                        'descricao' => $gasto['descricao'],
                    ]
                );
            });

            // Verifica se existe um link para a próxima página
            $temMais = collect($resposta['links'])->contains(fn($l) => $l['rel'] === 'next');

            // Avança para a próxima página
            $pagina++;

        } while ($temMais); // Continua enquanto houver mais páginas
    }
}
