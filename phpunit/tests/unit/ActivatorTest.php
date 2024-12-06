<?php

use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/activator.php';

final class ActivatorTest extends TestCase
{
    public function test_activate()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_Activator'));
    }

    public function test_initial_counter_value()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Activator');
        $property = $reflector->getProperty('counter');
        $property->setAccessible(true);
        $this->assertEquals(0, $property->getValue());
    }


    public function test_create_tables_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Activator', 'create_tables'));
    }

    public function test_create_tables_is_private()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Activator');
        $method = $reflector->getMethod('create_tables');
        $this->assertTrue($method->isPrivate());
    }

}
?>