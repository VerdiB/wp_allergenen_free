<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Pro_License_RUD
{
  // Deze class zorgt voor de READ, UPDATE en DELETE functionaliteit voor de license form

    public function __construct()
    {
      $this->getLicenseKey();
      $this->updateLicense();
      $this->deleteLicense();
    }

    // getInstance werkt nog niet,$_instance is undefined. 
    // Hier moet naar gekeken worden

    // public static function getInstance()
    // {
    //     if (self::$_instance === null) {
    //         self::$_instance = new self();
    //     }
    //     return self::$_instance;
    // }

    public function getLicenseKey()
    {
      $sql = 'SELECT licentie_sleutel FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseAvailability()
    {
      $sql = 'SELECT in_gebruik FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseStartDate()
    {
      $sql = 'SELECT start_datum FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseEndDate()
    {
      $sql = 'SELECT eind_datum FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function updateLicense()
    {
      // Opzetje functie updaten license in database
      
    }

    public function deleteLicense()
    {
      // Opzetje functie verwijderen license uit database
    }

}