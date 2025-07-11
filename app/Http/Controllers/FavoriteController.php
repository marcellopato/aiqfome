<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Client;
use App\Http\Requests\StoreFavoriteRequest;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Favorites",
 *     description="Gerenciamento de produtos favoritos de clientes"
 * )
 *
 * @OA\Schema(
 *     schema="FavoriteProduct",
 *     type="object",
 *     title="FavoriteProduct",
 *     required={"id", "title", "image", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops"),
 *     @OA\Property(property="image", type="string", example="https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=109.95),
 *     @OA\Property(property="review", type="string", example="Produto excelente!"),
 * )
 */
class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/clients/{client}/favorites",
     *     tags={"Favorites"},
     *     summary="Listar favoritos de um cliente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos favoritos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FavoriteProduct"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Cliente não encontrado."))
     *     )
     * )
     */
    public function index($clientId)
    {
        $client = Client::findOrFail($clientId);
        $favorites = $client->favorites()->get();
        // Buscar detalhes dos produtos na API externa
        $products = collect();
        foreach ($favorites as $favorite) {
            $response = Http::get('https://fakestoreapi.com/products/' . $favorite->product_id);
            if ($response->ok()) {
                $products->push($response->json());
            }
        }
        return response()->json($products);
    }

    /**
     * @OA\Post(
     *     path="/api/clients/{client}/favorites",
     *     tags={"Favorites"},
     *     summary="Adicionar produto favorito ao cliente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Favorito adicionado",
     *         @OA\JsonContent(ref="#/components/schemas/FavoriteProduct")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente ou produto não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Produto não encontrado na API externa."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Produto já favoritado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Produto já favoritado."))
     *     )
     * )
     */
    public function store(StoreFavoriteRequest $request, $clientId)
    {
        $client = Client::findOrFail($clientId);
        $productId = $request->input('product_id');
        // Verifica duplicidade
        if ($client->favorites()->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'Produto já favoritado.'], 422);
        }
        // Valida produto na API externa
        $response = Http::get('https://fakestoreapi.com/products/' . $productId);
        if (!$response->ok()) {
            return response()->json(['message' => 'Produto não encontrado na API externa.'], 404);
        }
        $favorite = $client->favorites()->create(['product_id' => $productId]);
        return response()->json($favorite, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{client}/favorites/{product_id}",
     *     tags={"Favorites"},
     *     summary="Remover produto favorito do cliente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="product_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="Favorito removido"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favorito não encontrado",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Favorito não encontrado."))
     *     )
     * )
     */
    public function destroy($clientId, $productId)
    {
        $client = Client::findOrFail($clientId);
        $favorite = $client->favorites()->where('product_id', $productId)->first();
        if (!$favorite) {
            return response()->json(['message' => 'Favorito não encontrado.'], 404);
        }
        $favorite->delete();
        return response()->json(null, 204);
    }
}
