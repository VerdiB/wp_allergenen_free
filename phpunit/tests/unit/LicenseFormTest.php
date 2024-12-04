<?php 
use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary-ictoria/php/forms/allergen_form_license.php';
require_once '/var/www/html/wp-content/plugins/allergens-dietary-ictoria/php/forms/Iallergen_form.php';

final class LicenseFormTest extends TestCase
{
    public function test_activate()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_License_Form'));
    }

    public function test_show_form_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_License_Form', 'showForm'));
    }

    public function test_construct_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_License_Form');
        $method = $reflector->getMethod('__construct');
        $this->assertTrue($method->isPublic());
    }

    public function test_submit_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_License_Form', 'submit'));
    }

    public function test_submit_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_License_Form');
        $method = $reflector->getMethod('submit');
        $this->assertTrue($method->isPublic());
    }

    public function test_show_form_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_License_Form');
        $method = $reflector->getMethod('showForm');
        $this->assertTrue($method->isPublic());
    }

    public function test_sanitize_method_exists()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_License_Form', 'sanitize'));
    }

    public function test_sanitize_is_public()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_License_Form');
        $method = $reflector->getMethod('sanitize');
        $this->assertTrue($method->isPublic());
    }

}



?>