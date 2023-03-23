<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\OrderController;

class OrderControllerTest extends ShopApiControllerTest
{
    protected function setUp(): void
    {
        $this->controller = new OrderController();
        parent::setUp();
    }
}

