<?php

namespace Tests\Feature\Api\V1;

use App\Models\Bus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        $adminRole = Role::create(['name' => 'admin']);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@example.com'
        ]);

        // Generate token
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
    }

    public function test_admin_can_get_all_buses()
    {
        // Create some buses for testing
        Bus::factory()->create([
            'reg_no' => 'BUS001',
            'capacity' => 50
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/buses');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'reg_no',
                                 'capacity',
                                 'driver',
                                 'cleaner',
                                 'route',
                                 'status',
                                 'created_at',
                                 'updated_at',
                             ]
                         ]
                     ],
                     'message',
                     'code',
                     'timestamp'
                 ]);
    }

    public function test_admin_can_create_bus()
    {
        $data = [
            'reg_no' => 'NEW001',
            'capacity' => 45,
            'model' => 'Volvo',
            'status' => 'active'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/v1/buses', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'reg_no' => 'NEW001',
                         'capacity' => 45,
                         'model' => 'Volvo',
                         'status' => 'active'
                     ]
                 ]);

        $this->assertDatabaseHas('buses', [
            'reg_no' => 'NEW001',
            'capacity' => 45,
            'model' => 'Volvo',
            'status' => 'active'
        ]);
    }

    public function test_admin_can_update_bus()
    {
        $bus = Bus::create([
            'reg_no' => 'OLD001',
            'capacity' => 40,
            'status' => 'active'
        ]);

        $updateData = [
            'capacity' => 50,
            'status' => 'maintenance'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/v1/buses/{$bus->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'capacity' => 50,
                         'status' => 'maintenance'
                     ]
                 ]);

        $this->assertDatabaseHas('buses', [
            'id' => $bus->id,
            'capacity' => 50,
            'status' => 'maintenance'
        ]);
    }

    public function test_admin_can_delete_bus()
    {
        $bus = Bus::create([
            'reg_no' => 'DEL001',
            'capacity' => 45,
            'status' => 'active'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/v1/buses/{$bus->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Bus deleted successfully'
                 ]);

        $this->assertDatabaseMissing('buses', [
            'id' => $bus->id
        ]);
    }

    public function test_unauthorized_user_cannot_access_buses()
    {
        $response = $this->getJson('/api/v1/buses');
        $response->assertStatus(401);
    }
}