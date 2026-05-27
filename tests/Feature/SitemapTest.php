<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_html_page_can_be_rendered()
    {
        // Seed database with a product and category
        $category = Category::create(['name' => 'Test Category']);
        Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 10000,
            'stock' => 10,
            'description' => 'Test description',
            'is_flash_sale' => false,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertSee('Test Category');
    }

    public function test_sitemap_xml_page_can_be_rendered()
    {
        // Seed database with a product and category
        $category = Category::create(['name' => 'Test Category']);
        Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 10000,
            'stock' => 10,
            'description' => 'Test description',
            'is_flash_sale' => false,
        ]);

        $response = $this->get(route('sitemap.xml'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $response->assertSee('/product/1');
        $response->assertSee('category=Test%20Category');
    }
}
