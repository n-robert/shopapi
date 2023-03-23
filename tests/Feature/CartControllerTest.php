<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\CartController;

class CartControllerTest extends ShopApiControllerTestCase
{
    protected function setUp(): void
    {
        $this->controller = new CartController();
        parent::setUp();
    }
}

