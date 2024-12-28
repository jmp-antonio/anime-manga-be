<?php

namespace Tests\Feature;

use App\Models\Anime;
use App\Models\MangaLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class MangaLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    /* 
        SHOW
    */

    public function test_show()
    {
        // Create a manga link
        $mangaLink = MangaLink::factory()->create();

        // Make a GET request to the show endpoint
        $response = $this->getJson("api/manga-links/{$mangaLink->id}");

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'success',
                'data' => [
                    'id' => $mangaLink->id,
                    'url' => $mangaLink->url,
                    'anime_id' => $mangaLink->anime_id,
                ],
            ]);
    }

    public function test_show_not_found()
    {
        // Make a GET request to the show endpoint with a non-existing ID
        $response = $this->getJson('api/manga-links/999');

        // Assert the response
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    /* 
        STORE
    */
    public function test_store()
    {
        // Create an anime instance
        $anime = Anime::factory()->create();

        // Prepare the data for the request
        $data = [
            'url' => 'https://example.com/manga-link',
            'anime_id' => $anime->id,
        ];

        // Make a POST request to the store endpoint
        $response = $this->postJson('api/manga-links', $data);

        // Assert the response
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Manga link added successfully',
                'data' => [
                    'url' => $data['url'],
                    'anime_id' => $data['anime_id'],
                ],
            ]);

        // Assert that the manga link was created in the database
        $this->assertDatabaseHas('manga_links', $data);
    }

    public function test_store_validation_failure()
    {
        // Make a POST request to the store endpoint with invalid data
        $response = $this->postJson('api/manga-links', []);

        // Assert the response
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['url', 'anime_id']);
    }

    /* 
        UDPATE
    */

    public function test_update()
    {
        // Create an anime instance
        $anime = Anime::factory()->create();

        // Create a manga link instance
        $mangaLink = MangaLink::factory()->create(['anime_id' => $anime->id]);

        // Prepare the data for the update request
        $data = [
            'url' => 'https://example.com/updated-manga-link',
            'anime_id' => $anime->id,
        ];

        // Make a PUT request to the update endpoint
        $response = $this->putJson("api/manga-links/{$mangaLink->id}", $data);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Manga link updated successfully',
                'data' => [
                    'url' => $data['url'],
                    'anime_id' => $data['anime_id'],
                ],
            ]);

        // Assert that the manga link was updated in the database
        $this->assertDatabaseHas('manga_links', $data);
    }

    public function test_update_not_found()
    {
        // Create an anime instance
        $anime = Anime::factory()->create();

        // Prepare the data for the update request
        $data = [
            'url' => 'https://example.com/updated-manga-link',
            'anime_id' => $anime->id,
        ];

        // Make a PUT request to the update endpoint with a non-existing ID
        $response = $this->putJson('api/manga-links/999', $data);

        // Assert the response
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }

    public function test_update_validation_failure()
    {
        // Create an anime instance
        $anime = Anime::factory()->create();

        // Create a manga link instance
        $mangaLink = MangaLink::factory()->create(['anime_id' => $anime->id]);

        // Make a PUT request to the update endpoint with invalid data
        $response = $this->putJson("api/manga-links/{$mangaLink->id}", []);

        // Assert the response
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['url', 'anime_id']);
    }

    /* 
        DELETE
    */

    public function test_delete()
    {
        // Create an anime instance
        $anime = Anime::factory()->create();

        // Create a manga link instance
        $mangaLink = MangaLink::factory()->create(['anime_id' => $anime->id]);

        // Make a DELETE request to the delete endpoint
        $response = $this->deleteJson("api/manga-links/{$mangaLink->id}");

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Manga link deleted successfully',
            ]);

        // Assert that the manga link was deleted from the database
        $this->assertDatabaseMissing('manga_links', ['id' => $mangaLink->id]);
    }

    public function test_delete_not_found()
    {
        // Make a DELETE request to the delete endpoint with a non-existing ID
        $response = $this->deleteJson('api/manga-links/999');

        // Assert the response
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Item not found',
            ]);
    }
}
