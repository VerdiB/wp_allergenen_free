<?php
// exit if user can access this file directly
if (!defined('ABSPATH')) {
	exit;
}

// if(!enum_exists('FormType')){
// 	require_once ALLERGENS_DIETARY_DIRNAME . '/php/lists/form_type.php';
// }

class Allergens_Dietary_Plugin_Menu
{

	private static $instances = [];

	

	/***
	 * Main instance
	 *
	 * @staticvar   array   $instance
	 * @return      The one true instance
	 */

	
	public static function instance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

	public function __construct()
	{
		if (static::class === self::class) { 
			add_action('admin_menu', [$this, 'addMyAdminMenu'], 5);	
		}
	}

	public function addMyAdminMenu()
	{
		$callback = static::instance();

		add_menu_page(
			__('Allergens and Dietary', 'allergens-dietary'),
			'Ictoria',
			'manage_options',
			'allergens-dietary-options',
			'',
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGFhZ18yIiBkYXRhLW5hbWU9IkxhYWcgMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNS42OSA2LjA3Ij4KICA8ZGVmcz4KICAgIDxzdHlsZT4KICAgICAgLmNscy0xIHsKICAgICAgICBmaWxsOiAjZmZmOwogICAgICAgIHN0cm9rZS13aWR0aDogMHB4OwogICAgICB9CiAgICAgICN3cGFkbWluYmFyIGE6aG92ZXIgc3ZnIHBhdGggewogICAgICBmaWxsOiBibHVlICFpbXBvcnRhbnQ7CiAgICAgfQogICAgICAgCiAgICAgIAogICAgPC9zdHlsZT4KICA8L2RlZnM+CiAgPGcgaWQ9IkxhYWdfMS0yIiBkYXRhLW5hbWU9IkxhYWcgMSI+CiAgICA8cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Im0zLjk4LDBDMS44NS0uMTIuMTksMi4yMSwwLDQuNXMzLjQzLDEuNTMsNC42MSwxLjAyQzYuMTIsNC44Niw2LjE2LjEzLDMuOTgsMFptLS44NS41M2MuMDktLjEuMi0uMTUuMzMtLjE1cy4yNC4wNS4zMy4xNWMuMDkuMS4xNC4yMi4xNC4zN3MtLjA1LjI2LS4xNC4zN2MtLjA5LjEtLjIuMTUtLjMzLjE1cy0uMjQtLjA1LS4zMy0uMTVjLS4wOS0uMS0uMTQtLjIyLS4xNC0uMzdzLjA1LS4yNi4xNC0uMzdabTEuOTguOThjLS41Ny40MS0xLjM4LjI4LTEuNzYsMy43Ny0uMDEuMTEtLjA0LjIxLS4xMy4yOS0uMDkuMDgtLjIxLjExLS4zNS4xMXMtLjI1LS4wNC0uMzUtLjExYy0uMDktLjA4LS4xNi0uMTctLjE0LS4yOS4yNi0xLjE0LjkxLTIsMC0zLjUtLjA3LS4xMS0uMTItLjI2LjAzLS4zLjI1LS4wNi40OS4zLDEuMDMuMjYuMzgtLjAzLDEuMS0uMjgsMS40Mi0uMzkuMjctLjA5LjM0LjA5LjI1LjE2WiIvPgogIDwvZz4KPC9zdmc+'
		);

		add_action('admin_menu', function () {
			remove_submenu_page('allergens-dietary-options', 'allergens-dietary-options');
		}, 999); 

		add_submenu_page(
			'allergens-dietary-options',
			__('Info', 'allergens-dietary'),
			__('Info', 'allergens-dietary'),
			'manage_options',
			'allergens-dietary-Info',
			array(
				$callback, 'Info',
			)
		);

		add_submenu_page(
			'allergens-dietary-options',
			__('Show allergens', 'allergens-dietary'),
			__('Show allergens', 'allergens-dietary'),
			'manage_options',
			'allergens-dietary-show-allergens',
			array(
				$callback,
				'showallergens',
			),
		);

		add_submenu_page(
			'allergens-dietary-options',
			__('License key', 'allergens-dietary'),
			__('License key', 'allergens-dietary'),
			'manage_options',
			'allergens-dietary-license',
			array($callback, 'licenseForm')
		);
	}

	public function myAdminPage()
	{
		// echo the HTML here .......
	}

	public function licenseForm()
	{
		if (!class_exists('Allergens_Dietary_Form')) {
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_form.php';
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/tabs/allergen_tabs.php';
		}
		Allergens_Dietary_Tabs::getInstance()->showtabs();
		Allergens_Dietary_Form::setFormType(FormType::LICENSE);
		Allergens_Dietary_Form::getInstance()->showForm();
	}

	public function showallergens()
	{
		if (!class_exists('Allergens_Dietary_Pro_Show_Allergens')) {
			// require_once ALLERGENS_DIETARY_DIRNAME_PRO . '/php/tables/allergen_show_allergen.php';
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/tables/allergen_show_allergen.php';
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/tabs/allergen_tabs.php';
		}
		Allergens_Dietary_Tabs::getInstance()->showtabs();
		$singleton = Allergens_Dietary_Show_Allergens::getInstance();
		$singleton->table_page();
	}

	public function info()
	{
		if (!class_exists('Allergens_Dietary_Info')) {
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/lists/allergen_info.php';
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/tabs/allergen_tabs.php';
		}
	
		Allergens_Dietary_Tabs::getInstance()->showtabs();
		Allergens_Dietary_Info::getInstance()->showInfo();
	}
}

// call the class and add the menus automatically
// $Allergens_Dietary_Plugin_Menu = Allergens_Dietary_Plugin_Menu::instance();