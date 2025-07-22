<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Deputado;
use App\Models\Despesa;
use App\Services\CamaraApiService;

/**
 * Comando Artisan para sincronizar deputados e suas despesas
 * utilizando a API pública da Câmara dos Deputados.
 *
 * Este comando percorre todas as páginas de deputados da API
 * e para cada deputado encontrado, obtém seus dados pessoais
 * e suas despesas e persiste no banco de dados local.
 */
class SincronizarDeputados extends Command
{
    /**
     * Nome e assinatura do comando Artisan.
     */
    protected $signature = 'sincronizar:deputados';

    /**
     * Descrição do comando no Artisan.
     */
    protected $description = 'Sincroniza os deputados e suas despesas com a API da Câmara dos Deputados';

    /**
     * Injeta a service que encapsula as requisições HTTP à API da Câmara.
     */
    public function __construct(protected CamaraApiService $api)
    {
        parent::__construct();
    }

    /**
     * Executa o comando de sincronização.
     *
     * @return int Código de status da execução (0 = sucesso, 1 = erro)
     */
    public function handle(): int
    {
        $this->info("Iniciando sincronização de deputados...");

        $pagina = 1;
        $totalDeputados = 0;
        $totalDespesas = 0;

        // Loop paginado para buscar todos os deputados
        do {
            $dados = $this->api->listarDeputados($pagina);
            $deputados = collect($dados['dados'] ?? []);

            $deputados->each(function ($dadosBasicos) use (&$totalDeputados, &$totalDespesas) {
                $idApi = $dadosBasicos['id'];

                // Busca detalhes do deputado
                $info = $this->api->detalhesDeputado($idApi)['dados'] ?? null;

                if (!$info) {
                    $this->warn("Não foi possível buscar detalhes do deputado ID {$idApi}");
                    return;
                }

                // Cria ou atualiza registro do deputado no banco de dados
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

                $totalDeputados++;

                // Loop paginado para buscar todas as despesas do deputado
                $paginaDespesas = 1;
                do {
                    $despesasJson = $this->api->listarDespesas($idApi, $paginaDespesas);
                    $despesas = collect($despesasJson['dados'] ?? []);

                    // Itera sobre as despesas e salva no banco
                    $despesas->each(function ($gasto) use ($deputado, &$totalDespesas) {
                        Despesa::updateOrCreate(
                            ['id' => $gasto['idDocumento']],
                            [
                                'deputado_id' => $deputado->id,
                                'tipo_despesa' => $gasto['tipoDespesa'],
                                'data' => $gasto['dataDocumento'],
                                'valor' => $gasto['valorDocumento'],
                                'descricao' => $gasto['descricao'],
                            ]
                        );
                        $totalDespesas++;
                    });

                    // Verifica se existe próxima página de despesas
                    $temMaisDespesas = collect($despesasJson['links'])
                        ->contains(fn($link) => $link['rel'] === 'next');

                    $paginaDespesas++;
                } while ($temMaisDespesas);

                $this->info("Deputado {$deputado->nome} sincronizado.");
            });

            // Verifica se existe próxima página de deputados
            $temMaisDeputados = collect($dados['links'])
                ->contains(fn($link) => $link['rel'] === 'next');

            $pagina++;

        } while ($temMaisDeputados);

        $this->info("Sincronização finalizada.");
        $this->info("Deputados sincronizados: {$totalDeputados}");
        $this->info("Despesas sincronizadas: {$totalDespesas}");

        return 0;
    }
}
