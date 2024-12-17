<?php
// exit if user can access this file directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists("Allergens_Dietary_Allergy_Attachment_Queries")) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergy_attachment.php';
}

if (!class_exists("Allergens_Dietary_Allergy_Product_Queries")) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergy_product.php';
}

if (!class_exists("Allergens_Dietary_Allergen_Queries")) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergen.php';
}

// this class contains functions used to add/remove allergens and dietary options to/from a WooCommerce product
class Allergens_Dietary_Product_Settings
{
	private static $_instance = null;
	private array $_allergens;
	private array $_attachedAllergens = array();

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Allergens_Dietary_Product_Settings();
		}
	}

	public function __construct()
	{
		$this->_allergens = Allergens_Dietary_Allergen_Queries::getInstance()->getAllAllergens();

		add_filter('woocommerce_product_data_tabs', array($this, 'data_tab'));
		add_action('woocommerce_product_data_panels', array($this, 'data_fields'));
		// add_action( 'woocommerce_process_product_meta_(product_type)','save_product_options' );
		// add_action( 'woocommerce_process_product_meta',array($this, 'save_product_options'), 10, 1 );
		add_action('save_post', array($this, 'save_product_options'));

	}

	// function that sets the name of the menu tab for this plugin
	public function data_tab($product_data_tabs)
	{
		$product_data_tabs['allergens-tab'] = array(
			'label' => __('Allergens', 'allergens-dietary'),
			'target' => 'allergens_dietary_ictoria_product_data',
		);
		return $product_data_tabs;
	}

	/**
	 * @param none
	 * @brief This method shows the form to add/update allergens .\
	 * function that shows all available options when the menu tab of this plugin is selected
	 * @return void
	 * @since 1.0.0
	 * @date 30-9-2024
	 */
	public function data_fields()
	{
		global $post;
		$options = Allergens_Dietary_Allergy_Attachment_Queries::getInstance();
		$allergens = $options->getAllAllergyAttachmments();
		$this->_attachedAllergens = Allergens_Dietary_Allergy_Product_Queries::getInstance()->getAllergyProduct($post->ID);

		$tmpArr = array();
		//add a faux value to the array to make sure the count is correct
		$tmpArr[] = 'faux value';
		foreach ($this->_attachedAllergens as $allergen) {
			$tmpArr[] = $allergen['allergy_name'];
		}
		$this->_attachedAllergens = $tmpArr;

		?>
		<div id="allergens_dietary_ictoria_product_data" class="panel woocommerce_options_panel">
		<h2><?php echo esc_html__( 'Select allergen(s) and/or dietary restrictions:', 'allergens-dietary' ); ?></h2>
		<?php foreach ( $allergens as $allergen ) : ?>
			<?php 
				// Controleer of het item is aangevinkt
				$checked = in_array( $allergen['allergy_name'], $this->_attachedAllergens, true ) ? 'checked' : ''; 
			?>
			<div class="allergen-field">
				<input 
					type="checkbox" 
					class="checkbox" 
					value="1" 
					name="<?php echo esc_attr( $this->replace_space_chars( $allergen['allergy_name'] ) . '_allergens_dietary_ictoria' ); ?>" 
					<?php echo esc_attr( $checked ); ?>
				/>
				<span class="description">
					<img 
						style="max-height: 40px; max-width: 40px;" 
						alt="<?php echo esc_attr( $allergen['allergy_name'] ); ?>" 
						src="<?php echo esc_url( $allergen['attachment_path'] ); ?>"
					/>
					&nbsp;<?php echo esc_html( $allergen['allergy_name'] ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
	<br/>
	<?php

	}

	// function that stores all selected options in the productdata of the currently selected product

	/**
	 * @param int $post_id
	 * @author ictoriabv
	 * @important This method is not yet completed the function on deleting and adding allergens is still bugged
	 * @brief This method saves or deletes the selected allergens and dietary restrictions to the product. depending on the (de-)selected options 
	 * @return void
	 * @since 1.0.0
	 * @date 5-11-2024
	 */
	public function save_product_options($post_id)
	{
		$allergensSelected = array();
		$allergenNames = array();
		unset($this->_attachedAllergens);
		$this->_attachedAllergens = Allergens_Dietary_Allergy_Product_Queries::getInstance()->getAllergyProduct($post_id);

		foreach ($this->_allergens as $allergen) {
			if (isset($_POST[($this->replace_space_chars($allergen['allergy_name']) . '_allergens_dietary_ictoria')])) {
				$allergensSelected[] = $allergen['allergy_name'];
			}
		}
		// Set old allergen name settings
		foreach ($this->_attachedAllergens as $allergen) {
			$allergenNames[] = $allergen['allergy_name'];
		}
		
		$new_diff = array_diff($allergensSelected, $allergenNames);
		$old_diff = array_diff($allergenNames, $allergensSelected);

		// If the new doesnt contain allergens from the old one, Delete the old.
		if ($old_diff) {
			foreach ($old_diff as $allergen) {
				Allergens_Dietary_Allergy_Product_Queries::getInstance()->deleteAllergyProduct($post_id, $allergen);
			}
		}
		// If the old doesn't contain allergens from the new one, Add the new.
		if ($new_diff) {
			foreach ($new_diff as $allergen) {
				Allergens_Dietary_Allergy_Product_Queries::getInstance()->addAllergyProduct($post_id, $allergen);
			}
		}

		return;
	}

	private function replace_space_chars(string $allergens): string
	{
		return (preg_match('/\s/', $allergens)) ? str_replace(' ', '_', $allergens) : $allergens;
	}
}
