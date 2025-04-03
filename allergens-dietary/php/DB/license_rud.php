<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Pro_License_RUD
{
    public function __construct()
    {
      $this->getLicenseKey();
      $this->updateLicense();
      $this->deleteLicense();
    }

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
    
    }

    // Functie voor het handelen van een aantal edge-cases. 
    public function deleteLicense()
    {

    }

}