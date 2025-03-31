<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Attachment_Queries
{
	private static array $instances;
	private static ?self $_instance = null;
	protected const PATH = ALLERGENS_DIETARY_DIRNAME . '/assets/icons/custom/';
	protected string $_url;

	public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

	protected function __construct()
	{
		$this->_url = get_home_url() . '/wp-content/plugins/allergens-dietary/assets/icons/custom/';
	}

}
