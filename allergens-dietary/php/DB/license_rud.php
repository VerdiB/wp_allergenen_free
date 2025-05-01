<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Pro_License_RUD
{
  // Deze class zorgt voor de READ, UPDATE en DELETE functionaliteit voor de license form

  /**
	 * @brief This method returns the instance of the class.
	 * @return Allergens_Dietary_Pro_License_Rud
	 * @author ictoriabv
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	protected static ?Allergens_Dietary_Pro_License_Rud $instances = null;

	public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances)) {
            self::$instances= new self();
        }
        return self::$instances;
    }

    protected function __construct()
    {
      $this->getLicenseKey();
      $this->getLicenseAvailability();
      $this->getLicenseStartDate();
      $this->getLicenseEndDate();
      $this->updateLicense();
    }

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
      global $wpdb;
      $wpdb->update(
        'licenties',
        array(
          'in_gebruik' => "In gebruik",
        ),
        array(
          'licentie_sleutel' => "12345abcde"
        ) 
      );
    }
}