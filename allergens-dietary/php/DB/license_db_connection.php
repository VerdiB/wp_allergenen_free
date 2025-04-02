<?php

if (!defined('ABSPATH')) {
	exit;
}

if(!class_exists('Allergens_Dietary_Pro_License_DB_Connection')){
	require_once ALLERGENS_DIETARY_FREE_DIRNAME . '/php/DB/license_db_connection.php';
}



class Allergens_Dietary_Pro_License_DB_Connection
{
    private string $servername = "126.168.94.21,3306";
    private string  $username = "root";
    private string  $password = "";

    public function __construct()
    {
        $this->databaseConnector();
    }

    private function databaseConnector()
    {
        try 
        {
            $conn = new PDO("mysql:host=$this->servername;dbname=wp-allergenen-pro", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
            return $conn;
        } 
        catch(PDOException $e) 
        {
            echo "Connection failed: " . $e->getMessage();
        }

    }
}