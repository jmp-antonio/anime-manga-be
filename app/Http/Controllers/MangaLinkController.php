<?php

namespace App\Http\Controllers;

use App\Models\MangaLink;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreMangaLinkRequest;
use App\Http\Requests\UpdateMangaLinkRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MangaLinkController extends Controller
{
    const SUCCESS_MESSAGE = 'success';
    const NOT_FOUND_MESSAGE = 'Item not found';

    /* 
        POST request - add a new manga link
        ROUTE   /manga-links
        REQUEST BODY
        - url
        - anime_id
    */
    public function store(StoreMangaLinkRequest $request): JsonResponse
    {
        try {
            $mangaLink = MangaLink::create($request->validated());

            return response()->json([
                'status' => self::SUCCESS_MESSAGE,
                'message' => 'Manga link added successfully',
                'data' => $mangaLink,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create manga link', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to create manga link', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        GET request - get single manga link by id
        ROUTE   /manga-links/{id}
        REQUEST PARAMETER
        - id
    */
    public function show(int $id): JsonResponse
    {
        $mangaLink = MangaLink::find($id);

        if (!$mangaLink) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $mangaLink]);
    }

    /* 
        PUT request - update an existing manga link
        ROUTE   /manga-links/{id}
        REQUEST PARAMETER
        - id
        REQUEST BODY
        - url
        - anime_id
    */
    public function update(UpdateMangaLinkRequest $request, $id): JsonResponse
    {
        try {
            $mangaLink = MangaLink::find($id);

            if (!$mangaLink) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $mangaLink->update($request->validated());

            return response()->json([
                'status' => self::SUCCESS_MESSAGE,
                'message' => 'Manga link updated successfully',
                'data' => $mangaLink,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update manga link', [
                'id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to update manga link', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        DELETE request - delete an existing manga link
        ROUTE   /manga-links/{id}
        REQUEST PARAMETER
        - id
    */
    public function destroy($id): JsonResponse
    {
        try {
            $mangaLink = MangaLink::find($id);

            if (!$mangaLink) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $mangaLink->delete();

            return response()->json(['message' => 'Manga link deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete manga link', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);
        }
    }
}
