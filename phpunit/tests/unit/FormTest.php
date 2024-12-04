<?php

use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/forms/allergen_form.php';
require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/forms/Iallergen_form.php';

final class FormTest extends TestCase
{
    public function test_activate()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_License_Form'));
    }

    public function test_is_singleton()
    {
        $reflector = new ReflectionClass('Allergens_Dietary_Form');
        $property = $reflector->getProperty('_instance');
        $property->setAccessible(true);
        $this->assertNull($property->getValue());
    }

    // public function test_form_type()
    // {
    //     $reflector = new ReflectionClass('Allergens_Dietary_Form');
    //     $property = $reflector->getProperty('_formType');
    //     $property->setAccessible(true);
    //     $this->assertNull($property->getValue());
    // }

    // public function test_form_object()
    // {
    //     $reflector = new ReflectionClass('Allergens_Dietary_Form');
    //     $property = $reflector->getProperty('_formObject');
    //     $property->setAccessible(true);
    //     $this->assertNull($property->getValue());
    // }

    public function test_get_instance()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Form', 'getInstance'));
    }

    public function test_set_form_type()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Form', 'setFormType'));
    }

    public function test_get_form_type()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Form', 'getFormType'));
    }

    public function test_show_form()
    {
        $this->assertTrue(method_exists('Allergens_Dietary_Form', 'showForm'));
    }

    // public function test_form_type_not_supported()
    // {
    //     $this->expectException(Exception::class);
    //     $reflector = new ReflectionClass('Allergens_Dietary_Form');
    //     $property = $reflector->getProperty('_formType');
    //     $property->setAccessible(true);
    //     $property->setValue('TEST');
    //     $form = Allergens_Dietary_Form::getInstance();
    //     $this->assertInstanceOf('Allergens_Dietary_License_Form', $form);	
    // }

    // public function test_form_type_supported()
    // {
    //     $reflector = new ReflectionClass('Allergens_Dietary_Form');
    //     $property = $reflector->getProperty('_formType');
    //     $property->setAccessible(true);
    //     $property->setValue(FormType::LICENSE);
    //     $form = Allergens_Dietary_Form::getInstance();
    //     $this->assertInstanceOf('Allergens_Dietary_License_Form', $form);
    // }
}
