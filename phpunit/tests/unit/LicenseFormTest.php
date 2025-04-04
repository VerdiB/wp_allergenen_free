<?php 
use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/forms/allergen_form_license.php';
require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/forms/Iallergen_form.php';

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



    // Jasper examen Unit tests

    public function test_activeren_licentie_1()
    {
        // Arrange
        $data["license_key"] = "12345abcde"; // value is de parameter
        $service = $this->createMock(Allergens_Dietary_License_Form::class);
        $service
            ->expects(self::once())
            ->method('licenseActivator')
            ->with($data)
            ->willReturn("12345abcde");
     
        // Assert
        self::assertStringContainsStringIgnoringCase("12345abcde",$service->licenseActivator($data));
    }

    public function test_activeren_licentie_2()
    {
        // Arrange
        $data["license_key"] = "12345"; // value is de parameter
        $service = $this->createMock(Allergens_Dietary_License_Form::class);
        $service
            ->expects(self::once())
            ->method('licenseActivator')
            ->with($data)
            ->willReturn("Sorry, this license key is not valid");
     
        // Assert
        self::assertStringContainsStringIgnoringCase("sorry",$service->licenseActivator($data));
    }

    public function test_activeren_licentie_3()
    {
        // Arrange
        $data["license_key"] = "1234Pokemon"; // value is de parameter
        $service = $this->createMock(Allergens_Dietary_License_Form::class);
        $service
            ->expects(self::once())
            ->method('licenseActivator')
            ->with($data)
            ->willReturn("Sorry, this license key is taken by another user");
     
        // Assert
        self::assertStringContainsStringIgnoringCase("sorry",$service->licenseActivator($data));
    }



}


?>