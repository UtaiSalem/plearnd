<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory(5)->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    public function test_requester_cannot_view_users_list(): void
    {
        $requester = User::factory()->create(['role' => 'requester']);

        $response = $this->actingAs($requester)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'username' => 'newuser',
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
            'user_type' => 'employee',
            'employee_id' => 'EMP123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'role' => 'staff',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['role' => 'requester']);

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'username' => 'updateduser',
            'name' => 'Updated User',
            'email' => 'updateduser@example.com',
            'role' => 'staff',
            'user_type' => 'student',
            'student_id' => '660000',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'updateduser',
            'role' => 'staff',
        ]);
    }

    public function test_admin_can_suspend_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/status", [
            'status' => 'suspended',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'suspended',
        ]);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post("/admin/users/{$admin->id}/status", [
            'status' => 'suspended',
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => 'active',
        ]);
    }
}
