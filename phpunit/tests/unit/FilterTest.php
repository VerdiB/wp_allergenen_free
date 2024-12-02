<?php

use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary-ictoria/php/filter.php';


final class FilterTest extends TestCase
{
    public function test_instance()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_Ictoria_Filter'));
    }

    public function test_create_filter_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Ictoria_Filter', 'create_filter'));
    }

    public function test_create_filter_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Filter');
        $method = $reflector->getMethod('create_filter');
        $this->assertTrue($method->isPublic());
    }

    public function test_filter_query_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Ictoria_Filter', 'filter_query'));
    }

    public function test_filter_query_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Filter');
        $method = $reflector->getMethod('filter_query');
        $this->assertTrue($method->isPublic());
    }

    public function test_instance_is_singleton()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Filter');
        $property = $reflector->getProperty('_instance');
        $property->setAccessible(true);
        $this->assertNull($property->getValue());
    }
    
    public function test_construct_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Ictoria_Filter');
        $method = $reflector->getMethod('__construct');
        $this->assertTrue($method->isPrivate());
    }
}
?>