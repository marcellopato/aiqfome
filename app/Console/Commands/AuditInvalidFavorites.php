<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Favorite;
use Illuminate\Support\Facades\Http;

class AuditInvalidFavorites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:audit-invalid-favorites {--delete : Remove os favoritos inválidos automaticamente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audita e (opcionalmente) remove favoritos com product_id inexistente na API externa.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Auditando favoritos inválidos...');
        $invalids = [];
        $favorites = Favorite::all();
        foreach ($favorites as $favorite) {
            $response = Http::get('https://fakestoreapi.com/products/' . $favorite->product_id);
            if (!$response->ok()) {
                $invalids[] = $favorite;
                $this->warn("Favorito inválido: id={$favorite->id}, client_id={$favorite->client_id}, product_id={$favorite->product_id}");
                if ($this->option('delete')) {
                    $favorite->delete();
                    $this->error('Removido!');
                }
            }
        }
        $this->info("Total de favoritos auditados: {$favorites->count()}");
        $this->info("Total de inválidos: " . count($invalids));
        if ($this->option('delete')) {
            $this->info('Favoritos inválidos removidos.');
        } else {
            $this->info('Use --delete para remover automaticamente os inválidos.');
        }
    }
}
