<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    const SUCCESS_MESSAGE = 'success';
    const NOT_FOUND_MESSAGE = 'Item not found';

    /* 
        GET request - get list of authors
        ROUTE   /authors
        QUERY PARAMETERS
        - sort_by
        - sort_direction
        - current_page
        - name
    */
    public function index(Request $request): JsonResponse
    {
        Log::info('getAllAuthors method called');

        // sorting and pagination
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $currentPage = $request->query('page', 1);

        $columnsToGet = ['id', 'first_name', 'last_name'];

        // filter options
        $name = $request->query('name', '');

        $authors = Author::filter($name) // filter using model scopes
            ->orderBy($sortBy, $sortDirection)
            ->paginate(2, $columnsToGet, 'page', $currentPage);

        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $authors], Response::HTTP_OK);
    }

    /* 
        GET request - get dropdown options for authors
        ROUTE   /authors/get-options
    */
    public function getOptions(): JsonResponse
    {
        $authors = Author::all(['id', 'first_name', 'last_name']);
        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $authors]);
    }

    /* 
        GET request - get single data by id
        ROUTE   /authors/{id}
        REQUEST PARAMETER
        - id
    */
    public function show(int $id): JsonResponse
    {
        $author = Author::find($id);

        if (!$author) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => self::SUCCESS_MESSAGE, 'data' => $author]);
    }


    /* 
        POST request - create a new author
        ROUTE   /authors
        REQUEST BODY
        - first_name
        - last_name
    */
    public function store(StoreAuthorRequest $request): JsonResponse
    {
        try {
            $author = Author::create($request->validated());

            return response()->json(['message' => 'Author created successfully', 'data' => $author], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create author', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to create author', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        PUT request - update an existing author
        ROUTE   /authors/{id}
        REQUEST PARAMETER
        - id
        REQUEST BODY
        - first_name
        - last_name
    */
    public function update(UpdateAuthorRequest $request, int $id): JsonResponse
    {
        try {
            $author = Author::find($id);

            if (!$author) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $author->update($request->validated());

            return response()->json(['message' => 'Author updated successfully', 'data' => $author]);
        } catch (\Exception $e) {
            Log::error('Failed to update author', [
                'id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Failed to update author', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /* 
        DELETE request - delete an existing author
        ROUTE   /authors/{id}
        REQUEST PARAMETER
        - id
    */
    public function destroy(int $id): JsonResponse
    {
        try {
            $author = Author::find($id);

            if (!$author) return response()->json(['message' => self::NOT_FOUND_MESSAGE], Response::HTTP_NOT_FOUND);

            $author->delete();

            return response()->json(['message' => 'Author deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete author', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to delete author', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
