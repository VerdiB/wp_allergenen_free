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
      // Maak sql query voor ophalen licentiesleutel, roep global wpdb aan, zet result in een string en return die
      $sql = 'SELECT licentie_sleutel FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseAvailability()
    {
      // Maak sql query voor ophalen beschikbaarheid licentiesleutel, roep global wpdb aan, zet result in een string en return die
      $sql = 'SELECT in_gebruik FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseStartDate()
    {
      // Maak sql query voor ophalen startdatum van de licentiesleutel, roep global wpdb aan, zet result in een string en return die
      $sql = 'SELECT start_datum FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function getLicenseEndDate()
    {
      // Maak sql query voor ophalen einddatum van de licentiesleutel, roep global wpdb aan, zet result in een string en return die
      $sql = 'SELECT eind_datum FROM licenties';
      global $wpdb;
      $result = $wpdb->get_var($sql);

      return $result;
    }

    public function updateLicense()
    {
      // Opzetje functie updaten license in database, met behulp van een sql query en wpdb
      
    }

    public function deleteLicense()
    {
      // Opzetje functie verwijderen license uit database, met behulp van een sql query en wpdb
    }

}