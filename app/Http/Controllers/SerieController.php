<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Serie;
use App\Models\Season;

class SerieController extends Controller
{
    // 1. BUSCADOR: (Sin cambios, es una consulta externa)
    public function search(Request $request)
    {
        $query = $request->query('q');
        $response = Http::get("https://api.tvmaze.com/search/shows?q={$query}");
        return response()->json($response->json());
    }

    // 2. GUARDAR: Usamos el ID del usuario logueado
    public function store(Request $request)
    {
        $tvmazeId = $request->tvmaze_id;
        $show = Http::get("https://api.tvmaze.com/shows/{$tvmazeId}")->json();

        // Creamos la serie asociada al usuario que envía el Token
        $serie = Serie::create([
            'user_id' => $request->user()->id, // <--- Correcto
            'tvmaze_id' => $tvmazeId,
            'title' => $show['name'],
            'poster_url' => $show['image']['original'] ?? ($show['image']['medium'] ?? null),
            'premiered' => $show['premiered'] ?? null,
            'is_completed' => false
        ]);

        $seasons = Http::get("https://api.tvmaze.com/shows/{$tvmazeId}/seasons")->json();

        foreach ($seasons as $s) {
            Season::create([
                'series_id' => $serie->id,
                'season_number' => $s['number'],
                'is_seen' => false
            ]);
        }

        return response()->json([
            'message' => '¡Serie y temporadas guardadas!',
            'data' => $serie->load('seasons')
        ], 201);
    }

    // 3. LISTAR: ¡IMPORTANTE! Añadimos (Request $request)
    public function index(Request $request) // <--- Faltaba el $request aquí
    {
        // Solo devolvemos las series del usuario autenticado
        return Serie::with('seasons')
            ->where('user_id', $request->user()->id)
            ->get();
    }

    // 4. ACTUALIZAR TEMPORADA
    public function updateSeason(Request $request, $seasonId)
    {
        $season = Season::findOrFail($seasonId);

        // Opcional: Podrías verificar aquí si la serie pertenece al usuario

        $season->update([
            'is_seen' => $request->is_seen,
            // 'seen_at' => $request->is_seen ? now() : null // Actívalo solo si tienes la columna
        ]);

        return response()->json(['message' => 'Temporada actualizada', 'data' => $season]);
    }

    // 5. ELIMINAR: Con filtro de seguridad
    public function destroy(Request $request, $id)
    {
        // Buscamos la serie que tenga ese ID Y que sea del usuario
        $serie = Serie::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $serie->delete();

        return response()->json(['message' => 'Serie eliminada']);
    }

    // 6. ACTUALIZAR STATUS: Con filtro de seguridad
    public function updateStatus(Request $request, $id)
    {
        $serie = Serie::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $serie->update([
            'is_completed' => $request->is_completed
        ]);

        return response()->json(['message' => 'Estado actualizado', 'data' => $serie]);
    }
}