<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\CategoryController;
use App\Models\Car;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeleteOperationTest extends TestCase
{
    public function test_blocked_category_delete_returns_clean_json(): void
    {
        $category = Category::query()->withCount('cars')->having('cars_count', '>', 0)->first();

        if (! $category) {
            $this->markTestSkipped('No category with cars in database.');
        }

        $admin = User::where('email', 'admin@gmail.com')->first();

        if (! $admin) {
            $this->markTestSkipped('Admin user not found.');
        }

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/motolegacy/api/admin/categories/{$category->id}");

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonMissing(['error'])
            ->assertJsonStructure(['errors' => ['category']]);
    }

    public function test_blocked_car_delete_returns_clean_json(): void
    {
        $car = Car::query()
            ->whereHas('purchaseRequests')
            ->orWhereHas('rentalRequests')
            ->orWhereHas('favoritedBy')
            ->first();

        if (! $car) {
            $this->markTestSkipped('No car with linked records in database.');
        }

        $seller = User::find($car->user_id);

        if (! $seller) {
            $this->markTestSkipped('Car owner not found.');
        }

        Sanctum::actingAs($seller);

        $response = $this->deleteJson("/motolegacy/api/seller/cars/{$car->id}");

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonMissing(['error'])
            ->assertJsonStructure(['errors' => ['car']]);
    }

    public function test_blocked_user_delete_returns_clean_json(): void
    {
        $user = User::query()
            ->whereHas('cars')
            ->where('email', '!=', 'admin@gmail.com')
            ->first();

        if (! $user) {
            $this->markTestSkipped('No deletable user with linked records in database.');
        }

        Sanctum::actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());

        $response = $this->deleteJson("/motolegacy/api/admin/users/{$user->id}");

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonMissing(['error'])
            ->assertJsonStructure(['errors' => ['user']]);
    }

    public function test_blocked_user_delete_does_not_soft_delete_user(): void
    {
        $user = User::query()
            ->whereHas('cars')
            ->where('email', '!=', 'admin@gmail.com')
            ->first();

        if (! $user) {
            $this->markTestSkipped('No user with linked records in database.');
        }

        $user->createToken('should-remain');

        Sanctum::actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());

        $this->deleteJson("/motolegacy/api/admin/users/{$user->id}")->assertStatus(422);

        $this->assertNotSoftDeleted($user);
        $this->assertGreaterThan(0, $user->tokens()->count());
    }

    public function test_allowed_user_delete_revokes_tokens(): void
    {
        $user = User::create([
            'name' => 'Delete Token Test ' . uniqid(),
            'email' => 'delete-token-' . uniqid() . '@example.com',
            'phone' => '1234567',
            'password' => bcrypt('password'),
            'role_id' => \Spatie\Permission\Models\Role::where('name', 'user')->value('id'),
        ]);

        $user->createToken('test-token');

        $this->assertGreaterThan(0, $user->tokens()->count());

        Sanctum::actingAs($user);

        $this->deleteJson('/motolegacy/api/user/delete_account')->assertNoContent();

        $this->assertSoftDeleted($user);
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_allowed_category_delete_returns_no_content(): void
    {
        $category = Category::create([
            'name' => 'Delete Test Category ' . uniqid(),
            'description' => 'temporary',
        ]);

        $admin = User::where('email', 'admin@gmail.com')->first();

        if (! $admin) {
            $this->markTestSkipped('Admin user not found.');
        }

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/motolegacy/api/admin/categories/{$category->id}");

        $response->assertNoContent();
    }
}
