<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAnimeRequest;
use App\Http\Requests\UpdateAnimeRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AnimeController extends Controller
{
    const SUCCESS_MESSAGE = 'success';
    const NOT_FOUND_MESSAGE = 'Item not found';
    /* 
        GET request - get list of items
        ROUTE   /animes
        QUERY PARAMETERS
        - sort_by
        - sort_direction
        - current_page
        - title
        - author

    */
    public function index(Request $request): JsonResponse
    {
        Log::info('getAll method called');

        // sorting and pagination
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $currentPage = $request->query('page', 1);

        $columnsToGet = ['id', 'title', 'author_id'];

        // filter options
        $title = $request->query('title', '');
        $author = $request->query('author', '');

        $animes = Anime::filter($title, $author) // filter using model scopes
            ->orderBy($sortBy, $sortDirection)
            ->paginate(3, $columnsToGet, 'page', $currentPage);

        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $animes], Response::HTTP_OK);
    }

    /* 
        GET request - get single data by id
        ROUTE   /animes/{id}
        REQUEST PARAMETER
        - id
    */
    public function show(int $id): JsonResponse
    {
        $anime = Anime::find($id);

        if (!$anime) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $anime]);
    }

    /* 
        POST request - create a new anime
        ROUTE   /animes
        REQUEST BODY
        - title
        - author_id
    */
    public function store(StoreAnimeRequest $request): JsonResponse
    {
        try {
            $anime = Anime::create($request->validated());

            return response()->json(['status' => self::SUCCESS_MESSAGE, 'message' => 'Anime created successfully', 'data' => $anime], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create anime', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to create anime', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        PUT request - update an existing anime
        ROUTE   /animes/{id}
        REQUEST PARAMETER
        - id
        REQUEST BODY
        - title
        - author_id
    */
    public function update(UpdateAnimeRequest $request, int $id): JsonResponse
    {
        try {
            $anime = Anime::find($id);

            if (!$anime) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $anime->update($request->validated());

            return response()->json(['status' => self::SUCCESS_MESSAGE, 'message' => 'Anime updated successfully', 'data' => $anime]);
        } catch (\Exception $e) {
            Log::error('Failed to update anime', [
                'id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to update anime', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        DELETE request - delete an existing anime
        ROUTE   /animes/{id}
        REQUEST PARAMETER
        - id
    */
    public function destroy(int $id): JsonResponse
    {
        try {
            $anime = Anime::find($id);

            if (!$anime) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $anime->delete();

            return response()->json(['message' => 'Anime deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete anime', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to delete anime', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
