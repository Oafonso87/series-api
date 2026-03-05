<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Serie;
use App\Models\Season;

class SerieController extends Controller
{
    // 1. BUSCADOR: Recibe un texto y pregunta a TVMaze
    public function search(Request $request)
    {
        $query = $request->query('q');

        // Llamada a la API externa
        $response = Http::get("https://api.tvmaze.com/search/shows?q={$query}");

        return response()->json($response->json());
    }

    // 2. GUARDAR: Toma una serie de TVMaze y la mete en tu Supabase
    public function store(Request $request)
    {
        // Necesitaremos el ID que TVMaze le da a la serie
        $tvmazeId = $request->tvmaze_id;

        // A. Consultamos los detalles de la serie a TVMaze
        $show = Http::get("https://api.tvmaze.com/shows/{$tvmazeId}")->json();

        // B. Guardamos la serie en nuestra tabla 'series'
        // De momento usamos el user_id = 1 (tú)
        $serie = Serie::create([
            'user_id' => 1,
            'tvmaze_id' => $tvmazeId,
            'title' => $show['name'],
            'poster_url' => $show['image']['original'] ?? null,
            'is_completed' => false
        ]);

        // C. Consultamos las temporadas de esa serie a TVMaze
        $seasons = Http::get("https://api.tvmaze.com/shows/{$tvmazeId}/seasons")->json();

        // D. Recorremos y guardamos cada temporada en 'seasons'
        foreach ($seasons as $s) {
            Season::create([
                'series_id' => $serie->id,
                'season_number' => $s['number'],
                'is_seen' => false
            ]);
        }

        return response()->json([
            'message' => '¡Serie y temporadas guardadas con éxito!',
            'data' => $serie->load('seasons')
        ], 201);
    }

    // 3. LISTAR: Devuelve todas tus series con sus temporadas
    public function index()
    {
        // De momento traemos las del usuario 1
        return Serie::with('seasons')->where('user_id', 1)->get();
    }

    // 4. ACTUALIZAR TEMPORADA: Marcar como vista
    public function updateSeason(Request $request, $seasonId)
    {
        $season = Season::findOrFail($seasonId);

        $season->update([
            'is_seen' => $request->is_seen,
            'seen_at' => $request->is_seen ? now() : null // Si la ve, ponemos fecha hoy. Si no, null.
        ]);

        return response()->json(['message' => 'Temporada actualizada', 'data' => $season]);
    }

    // 5. ELIMINAR: Borra una serie y sus temporadas (por la cascada de la BBDD)
    public function destroy($id)
    {
        $serie = Serie::findOrFail($id);
        $serie->delete();

        return response()->json(['message' => 'Serie eliminada']);
    }
}
