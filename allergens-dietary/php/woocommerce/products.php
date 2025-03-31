<?php
// exit if user can access this file directly.
if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Allergens_Dietary_Allergy_Product_Queries')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergy_product.php';
}

if (! class_exists('Allergens_Dietary_Allergy_Attachment_Queries')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergy_attachment.php';
}

// this class contains functions used on the front-end product pages
class Allergens_Dietary_Products {
	protected static ?self $_instance = null;

	public static function instance(): self
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		add_filter('allergens_dietary_ictoria_render_html', array($this, 'render_html'));
		add_action('woocommerce_single_product_summary', array($this, 'show_product_options'));
		add_action('woocommerce_after_shop_loop_item_title', array($this, 'show_product_options'));
	}

	// get all active product options added by this plugin of the product that is currently being loaded by WooCommerce.
	public function show_product_options(): void
	{
		global $post;
		$html    = '';
		$allergen_list    = Allergens_Dietary_Allergy_Product_Queries::getInstance()->getAllergyProduct($post->ID);

		if (! empty($allergen_list)) {
			// set the WP filter that will call the hook used to render the plugin options of this product
			$html = apply_filters('allergens_dietary_ictoria_render_html', $allergen_list);
		}
		if (! empty($html)) {
			echo wp_kses($html, array(
				'img' => array(
					'style' => array(),
					'class' => array(),
					'src' => array(),
					'alt' => array(),
					'title' => array(),
				),
			));
		}
	}

	// generate the html to display all relevant options for the given product.
	public function render_html(array $data): string
	{
		$html = array();
		$attachments_instance = Allergens_Dietary_Allergy_Attachment_Queries::getInstance();
		$attachments_list = array();

		foreach ($data as $value) {
			$attachments_list[] = $attachments_instance->getallergyAttachment($value['allergy_name'], false);
		}

		if (! empty($attachments_list)) {
			foreach ($attachments_list as $attachment) {
				$icon_url = esc_url($attachment['attachment_path']);
				$title    = esc_attr($attachment['allergy_name']);
				$alt 	= esc_attr($attachment['allergy_description']);
				$html[]   = "<img style='max-height: 40px; max-width: 40px;' class='allergen-icon' src='{$icon_url}' alt='{$alt}' title='{$title}' />";
			}
		}
		return implode('', $html);
	}
}
