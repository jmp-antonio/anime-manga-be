<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    /* 
        INDEX
    */

    public function test_index()
    {
        // Create multiple authors for pagination
        Author::factory()->count(5)->create();

        $response = $this->getJson('api/authors?page=1&sort_by=id&sort_direction=asc');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'first_name',
                            'last_name',
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
            ])
            ->assertJson(['message' => 'success']);
    }


    /* 
        GET OPTIONS
    */

    public function test_get_options()
    {
        $author = Author::factory()->create();

        $response = $this->getJson('api/authors/get-options');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'success',
                'data' => [
                    [
                        'id' => $author->id,
                        'first_name' => $author->first_name,
                        'last_name' => $author->last_name,
                    ]
                ]
            ]);
    }

    /* 
        SHOW
    */

    public function test_show()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("api/authors/{$author->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'success',
                'data' => [
                    'id' => $author->id,
                    'first_name' => $author->first_name,
                    'last_name' => $author->last_name,
                ]
            ]);
    }

    public function test_show_returns_not_found_when_author_does_not_exist()
    {
        // Act: Make a GET request to the show endpoint with a non-existing ID
        $response = $this->getJson('api/authors/9999'); // Assuming 9999 does not exist

        // Assert: Check the response status and message
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }

    /* 
        STORE
    */

    public function test_store_can_create_an_author()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $response = $this->postJson('api/authors', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Author created successfully',
                'data' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]);

        $this->assertDatabaseHas('authors', $data);
    }

    public function test_store_missing_first_name()
    {
        $data = [
            'last_name' => 'Doe',
        ];

        $response = $this->postJson('api/authors', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['first_name']);
    }

    public function test_store_missing_last_name()
    {
        $data = [
            'first_name' => 'John',
        ];

        $response = $this->postJson('api/authors', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['last_name']);
    }

    public function test_store_invalid_data()
    {
        $data = [
            'first_name' => '', // Invalid: empty first name
            'last_name' => 'Doe',
        ];

        $response = $this->postJson('api/authors', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['first_name']);
    }

    /* 
        UPDATE
    */

    public function test_update_can_modify_an_author()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Prepare the updated data
        $updatedData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ];

        // Act: Make a PUT request to update the author
        $response = $this->putJson("/api/authors/{$author->id}", $updatedData);

        // Assert: Check the response status and structure
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Author updated successfully',
                'data' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                ]
            ]);

        // Verify the record was updated in the database
        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_update_returns_not_found_when_author_does_not_exist()
    {
        // Arrange: Prepare the updated data
        $updatedData = [
            'first_name' => 'Updated Name',
            'last_name' => 'Updated Last Name',
        ];

        // Act: Make a PUT request to update a non-existing author
        $response = $this->putJson('/api/authors/9999', $updatedData);

        // Assert: Check the response status and message
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }

    public function test_update_missing_first_name()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Act: Attempt to update the author without a first name
        $response = $this->putJson("/api/authors/{$author->id}", [
            'last_name' => 'Smith', // Missing first name
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['first_name']);
    }

    public function test_update_missing_last_name()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Act: Attempt to update the author without a last name
        $response = $this->putJson("/api/authors/{$author->id}", [
            'first_name' => 'Jane', // Missing last name
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['last_name']);
    }

    public function test_update_invalid_data()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Act: Attempt to update the author with invalid data
        $response = $this->putJson("/api/authors/{$author->id}", [
            'first_name' => '', // Invalid: empty first name
            'last_name' => 'Smith',
        ]);

        // Assert: Check the response status and structure
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
            ->assertJsonValidationErrors(['first_name']);
    }

    /* 
        DELETE
    */

    public function test_destroy_can_delete_an_author()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Act: Make a DELETE request to delete the author
        $response = $this->deleteJson("/api/authors/{$author->id}");

        // Assert: Check the response status
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Author deleted successfully']);

        // Verify the record was deleted from the database
        $this->assertDatabaseMissing('authors', [
            'id' => $author->id,
        ]);
    }

    public function test_destroy_returns_not_found_when_author_does_not_exist()
    {
        // Act: Attempt to delete a non-existing author
        $response = $this->deleteJson('/api/authors/9999'); // Assuming 9999 does not exist

        // Assert: Check the response status and message
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }

    public function test_destroy_already_deleted_author()
    {
        // Arrange: Create an author record
        $author = Author::factory()->create();

        // Act: First, delete the author
        $this->deleteJson("/api/authors/{$author->id}");

        // Act: Attempt to delete the same author again
        $response = $this->deleteJson("/api/authors/{$author->id}");

        // Assert: Check the response status and message
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['message' => 'Item not found']);
    }
}
