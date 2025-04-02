<?php

if (!defined('ABSPATH')) {
	exit;
}

if(!class_exists('Allergens_Dietary_Pro_License_DB_Connection')){
	require_once ALLERGENS_DIETARY_PRO_DIRNAME . '/php/DB/license_db_connection.php';
}



class Allergens_Dietary_Pro_License_Handler
{
    public function __construct()
    {
      // if (licenseActivator === 1)
      // {
      //     //test condition is always set to true
      //     if(true)
      //     {

      //     }
      // }
      // else
      // {
      $this->licenseActivator();
      // }

      $this->exceededLicenseHandler();
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getLicense()
    {
      $dbConnect = new Allergens_Dietary_Pro_License_DB_Connection();
      $sql = 'SELECT licentieSleutel FROM licenties';
      $statement = $dbConnect->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
      $statement->execute();
      $result = $statement->get_result();
      return $result;
    }

    protected function licenseActivator()
    {
      
    }

    // Functie voor het handelen van een aantal edge-cases. 
    protected function exceededLicenseHandler()
    {

    }

}