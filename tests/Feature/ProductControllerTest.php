<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\ProductController;

class ProductControllerTest extends ShopApiControllerTestCase
{
    /**
     * @var int
     */
    static int $itemCount = 3;

    protected function setUp(): void
    {
        $this->controller = new ProductController();
        parent::setUp();
    }
}

