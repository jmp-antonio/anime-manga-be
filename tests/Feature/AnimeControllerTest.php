<?php

namespace Tests\Feature;

use App\Models\Anime;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class AnimeControllerTest extends TestCase
{
    use RefreshDatabase;

    /* 
        GET ALL
    */

    public function test_get_all_animes()
    {
        // Arrange: Create some anime records
        Anime::factory()->count(3)->create();

        // Act: Make a GET request to the getAll endpoint
        $response = $this->getJson('/api/animes');

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'author_id',
                            'author' => [
                                'id',
                                'first_name',
                                'last_name',
                            ],
                            'manga_links' => [
                                '*' => [
                                    'id',
                                    'url',
                                    'anime_id',
                                ],
                            ],
                        ],
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    /* 
        SHOW
    */

    public function test_show_anime()
    {
        // Arrange: Create an anime record
        $anime = Anime::factory()->create();

        // Act: Make a GET request to the show endpoint
        $response = $this->getJson('api/animes/' . $anime->id);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'success',
                'data' => [
                    'id' => $anime->id,
                    'title' => $anime->title,
                    'author_id' => $anime->author_id,
                ],
            ]);
    }

    public function test_show_anime_not_found()
    {
        // Act: Make a GET request to the show endpoint with a non-existing ID
        $response = $this->getJson('api/animes/999');

        // Assert: Check the response status and message
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    /* 
        CREATE
    */

    public function test_store_anime()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create(); // Ensure you have an Author model and factory

        // Prepare the data for the new anime
        $data = [
            'title' => 'My Hero Academia',
            'author_id' => $author->id, // Use the created author's ID
        ];

        // Act: Make a POST request to the store endpoint
        $response = $this->postJson('/api/animes', $data);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'author_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Additionally, you can check if the record was actually created in the database
        $this->assertDatabaseHas('animes', [
            'title' => 'My Hero Academia',
            'author_id' => $author->id,
        ]);
    }

    public function test_store_anime_missing_title()
    {
        // Arrange: Prepare the data without the title
        $data = [
            'author_id' => 1, // Assuming this author exists in your database
        ];

        // Act: Make a POST request to the store endpoint
        $response = $this->postJson('/api/animes', $data);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['title']);
    }

    public function test_store_anime_invalid_author_id()
    {
        // Arrange: Prepare the data with an invalid author_id
        $data = [
            'title' => 'My Hero Academia',
            'author_id' => 9999, // Assuming this author does not exist
        ];

        // Act: Make a POST request to the store endpoint
        $response = $this->postJson('/api/animes', $data);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_store_anime_duplicate_title()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create(); // Ensure you have an Author model and factory

        // Create the first anime record
        $animeData = [
            'title' => 'My Hero Academia',
            'author_id' => $author->id,
        ];
        $this->postJson('/api/animes', $animeData); // Create the first anime

        // Act: Attempt to create a duplicate anime record
        $duplicateData = [
            'title' => 'My Hero Academia', // Same title
            'author_id' => $author->id,
        ];
        $response = $this->postJson('/api/animes', $duplicateData);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['title' => ['The title has already been taken.']]);
    }

    /* 
        UPDATE
    */

    public function test_update_anime()
    {
        // Arrange: Create an author and an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Prepare the updated data
        $updatedData = [
            'title' => 'Updated Title',
            'author_id' => $author->id,
        ];

        // Act: Make a PUT request to update the anime
        $response = $this->putJson('/api/animes/' . $anime->id, $updatedData);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'author_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Verify the record was updated in the database
        $this->assertDatabaseHas('animes', [
            'id' => $anime->id,
            'title' => 'Updated Title',
            'author_id' => $author->id,
        ]);
    }

    public function test_update_anime_invalid_id()
    {
        // Arrange: Prepare the updated data
        $author = Author::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'author_id' => $author->id
        ];

        // Act: Make a PUT request to update a non-existing anime
        $response = $this->putJson('/api/animes/9999', $updatedData);

        // Assert: Check the response status and message
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }

    public function test_update_anime_duplicate_title()
    {
        // Arrange: Create two authors and two anime records
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $anime1 = Anime::factory()->create(['title' => 'Unique Title', 'author_id' => $author1->id]);
        $anime2 = Anime::factory()->create(['title' => 'Another Title', 'author_id' => $author2->id]);

        // Act: Attempt to update the first anime with the title of the second anime
        $response = $this->putJson('/api/animes/' . $anime1->id, [
            'title' => 'Another Title', // Duplicate title
            'author_id' => $author1->id,
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['title' => ['The title has already been taken.']]);
    }

    public function test_update_anime_invalid_author_id()
    {
        // Arrange: Create an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Act: Attempt to update the anime with an invalid author_id
        $response = $this->putJson('/api/animes/' . $anime->id, [
            'title' => 'Updated Title',
            'author_id' => 9999, // Invalid author ID
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_update_anime_missing_title()
    {
        // Arrange: Create an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Act: Attempt to update the anime without a title
        $response = $this->putJson('/api/animes/' . $anime->id, [
            'author_id' => $author->id, // Missing title
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['title']);
    }

    public function test_update_anime_missing_author_id()
    {
        // Arrange: Create an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Act: Attempt to update the anime without an author_id
        $response = $this->putJson('/api/animes/' . $anime->id, [
            'title' => 'Updated Title', // Missing author_id
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_update_anime_author_id_only()
    {
        // Arrange: Create an author and an anime record
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author1->id]);

        // Prepare the updated data with all fields
        $updatedData = [
            'title' => $anime->title, // Current title
            'author_id' => $author2->id, // New author ID
        ];

        // Act: Make a PUT request to update the anime
        $response = $this->putJson('/api/animes/' . $anime->id, $updatedData);

        // Assert: Check the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'author_id',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Verify the record was updated in the database
        $this->assertDatabaseHas('animes', [
            'id' => $anime->id,
            'author_id' => $author2->id, // Check for the new author ID
            'title' => $anime->title, // Ensure title remains unchanged
        ]);
    }



    /* 
        DELETE
    */

    public function test_destroy_anime()
    {
        // Arrange: Create an author and an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Act: Make a DELETE request to delete the anime
        $response = $this->deleteJson('/api/animes/' . $anime->id);

        // Assert: Check the response status
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson(['message' => 'Anime deleted successfully']);

        // Verify the record was deleted from the database
        $this->assertDatabaseMissing('animes', [
            'id' => $anime->id,
        ]);
    }

    public function test_destroy_anime_invalid_id()
    {
        // Act: Attempt to delete a non-existing anime
        $response = $this->deleteJson('/api/animes/9999');

        // Assert: Check the response status and message
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }

    public function test_destroy_anime_already_deleted()
    {
        // Arrange: Create an author and an anime record
        $author = Author::factory()->create();
        $anime = Anime::factory()->create(['author_id' => $author->id]);

        // Act: First, delete the anime
        $this->deleteJson('/api/animes/' . $anime->id);

        // Act: Attempt to delete the same anime again
        $response = $this->deleteJson('/api/animes/' . $anime->id);

        // Assert: Check the response status and message
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }
}
