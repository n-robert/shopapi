<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ShopApiController;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShopApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var int
     */
    static int $itemCount = 1;

    /**
     * @var string
     */
    protected string $apiUrl;

    /**
     * @var ShopApiController
     */
    protected ShopApiController $controller;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $names;

    /**
     * @var Model
     */
    protected Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->controller ?? new ShopApiController();
        $this->name = $this->controller->name;
        $this->names = $this->controller->names;
        $this->apiUrl = 'api/' . $this->names  . '/';
        $this->model = $this->controller->model;
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function testLogin()
    {
        $this->actingAs(
            user: User::factory()->create(),
            guard: 'api'
        );
        $this->assertAuthenticated('api');
    }

    /** @test */
    public function testLogout()
    {
        $this->assertFalse($this->isAuthenticated('api'));
    }

    /** @test */
    public function testStore()
    {
        $this->testLogin();

        $data = $this->model::factory()->make()->toArray();
        $response = $this->post($this->apiUrl, $data);
        $response->assertOk();
        $original= $response->getOriginalContent();
        $payload = $original['payload'];

        $response->assertJson($original);
        $this->assertDatabaseHas($this->names, $payload);
    }

    /** @test */
    public function testShow()
    {
        $this->testLogin();

        $data = $this->model::factory()->make()->toArray();
        $storeResponse = $this->post($this->apiUrl, $data);
        $original= $storeResponse->getOriginalContent();
        $payload = $original['payload'];

        $storeResponse->assertOk();
        $storeResponse->assertJson($original);
        $this->assertDatabaseHas($this->names, $payload);

        $id = $payload['id'];
        $response = $this->get($this->apiUrl . $id);
        $response->assertOk();
        $response->assertJson($response->getOriginalContent());
    }

    /** @test */
    public function testIndex()
    {
        $this->testLogin();

        $n = static::$itemCount;

        while ($n) {
            $data = $this->model::factory()->make()->toArray();
            $this->post($this->apiUrl, $data);
            $n--;
        }

        $response = $this->get($this->apiUrl);
        $response->assertOk();
        $response->assertJsonCount(static::$itemCount, 'payload');
        $response->assertJson($response->getOriginalContent());
    }

    /** @test */
    public function testUpdate()
    {
        $this->testLogin();

        $item = $this->model::factory()->make()->toArray();
        $storeResponse = $this->post($this->apiUrl, $item);
        $original= $storeResponse->getOriginalContent();
        $payload = $original['payload'];

        $storeResponse->assertOk();
        $storeResponse->assertJson($original);
        $this->assertDatabaseHas($this->names, $payload);

        $id = $payload['id'];
        $data = $this->model::factory()->make()->toArray();
        $response = $this->put($this->apiUrl . $id, $data);
        $original= $response->getOriginalContent();
        $payload = $original['payload'];

        $response->assertJson($original);
        $this->assertDatabaseHas($this->names, $payload);
    }

    /** @test */
    public function testDelete()
    {
        $this->testLogin();

        $item = $this->model::factory()->make()->toArray();
        $storeResponse = $this->post($this->apiUrl, $item);
        $original= $storeResponse->getOriginalContent();
        $payload = $original['payload'];

        $storeResponse->assertOk();
        $storeResponse->assertJson($original);
        $this->assertDatabaseHas($this->names, $payload);

        $response = $this->delete($this->apiUrl . $payload['id']);
        $response->assertOk();
        $response->assertJson($response->getOriginalContent());
        $this->assertDatabaseMissing($this->names, $payload);
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        $this->artisan('passport:install');
        $this->artisan('db:seed', ['--class' => 'StatusSeeder']);
        $this->artisan('db:seed', ['--class' => 'DeliverySeeder']);
        $this->artisan('db:seed', ['--class' => 'PaymentSeeder']);
    }

    protected function createRequest(array $data): \Illuminate\Http\Request
    {
        return new \Illuminate\Http\Request($data);
    }
}

