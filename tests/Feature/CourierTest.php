<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourierTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_courier_list(): void
    {
        Courier::factory()->count(5)->create();

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_couriers_are_paginated(): void
    {
        Courier::factory()->count(15)->create();

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('per_page', 10)
            ->assertJsonPath('total', 15);
    }

    public function test_couriers_are_sorted_by_name_by_default(): void
    {
        Courier::factory()->create(['name' => 'Zack']);
        Courier::factory()->create(['name' => 'Agung']);
        Courier::factory()->create(['name' => 'Budi']);

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Agung')
            ->assertJsonPath('data.1.name', 'Budi')
            ->assertJsonPath('data.2.name', 'Zack');
    }

    public function test_can_sort_couriers_by_registration_date(): void
    {
        $oldest = Courier::factory()->create([
            'created_at' => now()->subDays(3),
        ]);

        $newest = Courier::factory()->create([
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/couriers?sort=created_at');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $newest->id)
            ->assertJsonPath('data.1.id', $oldest->id);
    }

    public function test_can_search_couriers_using_multiple_keywords(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
        ]);

        Courier::factory()->create([
            'name' => 'Budi Santoso',
        ]);

        $response = $this->getJson(
            '/api/couriers?search=budi+agung'
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Budiono Hadi Agung');
    }

    public function test_can_filter_couriers_by_multiple_levels(): void
    {
        Courier::factory()->create(['level' => 1]);
        Courier::factory()->create(['level' => 2]);
        Courier::factory()->create(['level' => 3]);
        Courier::factory()->create(['level' => 4]);

        $response = $this->getJson('/api/couriers?level=2,3');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['level' => 2])
            ->assertJsonFragment(['level' => 3]);
    }

    public function test_can_show_courier_details(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->getJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk()
            ->assertJsonPath('id', $courier->id)
            ->assertJsonPath('name', $courier->name);
    }

    public function test_can_store_courier(): void
    {
        $data = [
            'name' => 'Budi Agung',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Surabaya, Indonesia',
            'level' => 3,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/couriers', $data);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Budi Agung');

        $this->assertDatabaseHas('couriers', [
            'email' => 'budi@example.com',
            'level' => 3,
        ]);
    }

    public function test_store_validation_rejects_invalid_data(): void
    {
        $response = $this->postJson('/api/couriers', [
            'name' => '',
            'phone' => '',
            'email' => 'invalid-email',
            'level' => 6,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'phone',
                'email',
                'level',
            ]);

        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_can_update_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            [
                'name' => 'Updated Courier',
                'level' => 5,
            ]
        );

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Courier')
            ->assertJsonPath('data.level', 5);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Updated Courier',
            'level' => 5,
        ]);
    }

    public function test_update_validation_rejects_invalid_data(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            [
                'level' => 10,
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['level']);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'level' => $courier->level,
        ]);
    }

    public function test_can_delete_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('couriers', [
            'id' => $courier->id,
        ]);
    }

    public function test_returns_404_for_nonexistent_courier(): void
    {
        $response = $this->getJson('/api/couriers/99999');

        $response->assertNotFound();
    }
}
