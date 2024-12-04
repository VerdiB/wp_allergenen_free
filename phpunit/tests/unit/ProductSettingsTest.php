<?php
use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary-ictoria/php/product_settings.php';


class ProductSettingsTest extends TestCase
{
    public function test_instance()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_Product_Settings'));
    }

    public function test_instance_is_singleton()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Product_Settings');
        $property = $reflector->getProperty('_instance');
        $property->setAccessible(true);
        $this->assertNull($property->getValue());
    }
    
    public function test_construct_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Product_Settings');
        $method = $reflector->getMethod('__construct');
        $this->assertTrue($method->isPrivate());
    }

    public function test_data_tab_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Product_Settings', 'data_tab'));
    }

    public function test_data_tab_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Product_Settings');
        $method = $reflector->getMethod('data_tab');
        $this->assertTrue($method->isPrivate());
    }

    public function test_data_fields_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Product_Settings', 'data_fields'));
    }

    public function test_data_fields_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Product_Settings');
        $method = $reflector->getMethod('data_fields');
        $this->assertTrue($method->isPrivate());
    }

    public function test_save_product_options_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Product_Settings', 'save_product_options'));
    }

    public function test_save_product_options_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Product_Settings');
        $method = $reflector->getMethod('save_product_options');
        $this->assertTrue($method->isPrivate());
    }
}

?>