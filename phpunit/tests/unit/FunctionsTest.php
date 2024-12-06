<?php
use PHPUnit\Framework\TestCase;

require_once '/var/www/html/wp-content/plugins/allergens-dietary/php/functions.php';

class FunctionsTest extends TestCase
{
    public function test_get_options()
    {
        $this->assertTrue(class_exists('Allergens_Dietary_Functions'));
    }
}

?>