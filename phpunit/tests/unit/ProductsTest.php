<?php

use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary-ictoria/php/products.php';

 class ProductsTest extends TestCase
 {
    public function test_instance()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_Ictoria_Products'));
    }

    public function test_construct_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Products');
        $method = $reflector->getMethod('__construct');
        $this->assertTrue($method->isPrivate());
    }

    public function test_instance_is_singleton()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Products');
        $property = $reflector->getProperty('_instance');
        $property->setAccessible(true);
        $this->assertNull($property->getValue());
    }

    public function test_render_html_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Ictoria_Products', 'render_html'));
    }

    public function test_render_html_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Products');
        $method = $reflector->getMethod('render_html');
        $this->assertTrue($method->isPrivate());
    }

    public function test_show_product_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Ictoria_Products', 'show_product_options'));
    }

    public function test_show_product_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Products');
        $method = $reflector->getMethod('show_product_options');
        $this->assertTrue($method->isPrivate());
    }
 }

?>