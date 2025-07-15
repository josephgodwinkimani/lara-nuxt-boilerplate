<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('User Management API', function () {
    describe('Authentication', function () {
        it('requires authentication to access users endpoint', function () {
            $response = $this->getJson('/api/users');

            $response->assertStatus(401);
        });

        it('allows authenticated users to access users endpoint', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users');

            $response->assertStatus(200);
        });
    });

    describe('Read All Users', function () {
        it('returns paginated users list for authenticated user', function () {
            $user = User::factory()->create();
            User::factory(5)->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'email_verified_at',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'meta' => [
                        'count',
                        'format',
                    ],
                ]);
        });

        it('supports pagination with per_page parameter', function () {
            $user = User::factory()->create();
            User::factory(10)->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users?per_page=3');

            $response->assertStatus(200);
            $data = $response->json('data');
            expect(count($data))->toBeLessThanOrEqual(3);
        });
    });

    describe('Create User', function () {
        it('creates a new user with valid data', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $userData = [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->postJson('/api/users', $userData);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]);
        });

        it('validates required fields', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->postJson('/api/users', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
        });

        it('validates unique email', function () {
            $existingUser = User::factory()->create(['email' => 'test@example.com']);
            Sanctum::actingAs($existingUser);

            $userData = [
                'name' => 'Another User',
                'email' => 'test@example.com', // duplicate email
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->postJson('/api/users', $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('validates password confirmation', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $userData = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
            ];

            $response = $this->postJson('/api/users', $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });
    });

    describe('Show User', function () {
        it('returns specific user details', function () {
            $authUser = User::factory()->create();
            $targetUser = User::factory()->create();
            Sanctum::actingAs($authUser);

            $response = $this->getJson("/api/users/{$targetUser->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $targetUser->id,
                        'name' => $targetUser->name,
                        'email' => $targetUser->email,
                    ],
                ]);
        });

        it('returns 404 for non-existent user', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users/99999');

            $response->assertStatus(404);
        });
    });

    describe('Update User', function () {
        it('updates user with valid data', function () {
            $authUser = User::factory()->create();
            $targetUser = User::factory()->create();
            Sanctum::actingAs($authUser);

            $updateData = [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ];

            $response = $this->putJson("/api/users/{$targetUser->id}", $updateData);

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Updated Name',
                        'email' => 'updated@example.com',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $targetUser->id,
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);
        });

        it('validates unique email on update', function () {
            $authUser = User::factory()->create();
            $user1 = User::factory()->create(['email' => 'user1@example.com']);
            $user2 = User::factory()->create(['email' => 'user2@example.com']);
            Sanctum::actingAs($authUser);

            $response = $this->putJson("/api/users/{$user2->id}", [
                'email' => 'user1@example.com', // email already taken
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
    });

    describe('Delete User', function () {
        it('deletes user successfully', function () {
            $authUser = User::factory()->create();
            $targetUser = User::factory()->create();
            Sanctum::actingAs($authUser);

            $response = $this->deleteJson("/api/users/{$targetUser->id}");

            $response->assertStatus(204);
            $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
        });

        it('returns 404 when deleting non-existent user', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->deleteJson('/api/users/99999');

            $response->assertStatus(404);
        });
    });

    describe('Password Management', function () {
        it('resets user password successfully', function () {
            $authUser = User::factory()->create();
            $targetUser = User::factory()->create();
            Sanctum::actingAs($authUser);

            $response = $this->postJson("/api/users/{$targetUser->id}/reset-password", [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

            $response->assertStatus(200)
                ->assertJson(['message' => 'Password reset successfully']);
        });

        it('generates password for user', function () {
            $authUser = User::factory()->create();
            $targetUser = User::factory()->create();
            Sanctum::actingAs($authUser);

            $response = $this->postJson("/api/users/{$targetUser->id}/generate-password");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'password',
                ])
                ->assertJson(['message' => 'Password generated successfully']);
        });
    });

    describe('Multi-Format Support', function () {
        it('returns XML format when requested', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users?format=xml');

            $response->assertStatus(200)
                ->assertHeader('Content-Type', 'application/xml');
        });

        it('returns CSV format when requested', function () {
            $user = User::factory()->create();
            Sanctum::actingAs($user);

            $response = $this->getJson('/api/users?format=csv');

            $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        });
    });
});
