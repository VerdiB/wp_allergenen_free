<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Allergens_Dietary_Allergen_Queries')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergen.php';
}



class Allergens_Dietary_Filter
{
	private static $_instance = null;
	private array $_allergens;
	private string $_filter_nonce;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Allergens_Dietary_Filter();
		}
		return self::$_instance;
	}

	public function load_css()
	{
		// wp_register_style('allergens-dietary-css', plugins_url(ALLERGENS_DIETARY_NAME . '/assets/css/allergens-dietary.css'));
		// wp_enqueue_style('allergens-dietary-css');

		wp_enqueue_style('wp-admin');
		wp_enqueue_style('buttons'); // WordPress button styles
		wp_enqueue_style('forms');   // WordPress form styles
		wp_enqueue_style('list-tables'); // WordPress table styles
		wp_enqueue_style('dashboard'); // Dashboard styles

	}

	public function load_js()
	{
		wp_register_script('Allergens_Dietary_Show_Allergens', plugins_url(ALLERGENS_DIETARY_NAME . '/assets/js/script.js'),array(), ALLERGENS_DIETARY_VERSION, true);
		wp_enqueue_script('Allergens_Dietary_Show_Allergens');
	}

	private function __construct()
	{
		add_action('wp_enqueue_scripts', array($this, 'load_css')); // For frontend
		add_action('wp_enqueue_scripts', array($this, 'load_js')); // For frontend

		add_action('woocommerce_before_shop_loop', array($this, 'create_filter'));
		add_action('woocommerce_product_query', array($this, 'filter_query'));
		$this->_allergens = Allergens_Dietary_Allergen_Queries::getInstance()->getAllAllergens();
	}

	public function create_filter()
	{
		if (isset($_POST['allergen-filter-nonce']) && !empty($_POST['allergen-filter-nonce'])){
			// $nonce = sanitize_text_field(wp_unslash($_POST['allergen-filter-nonce']));
			if (!wp_verify_nonce($this->_filter_nonce, 'allergen-filter-action')){
				wp_die(esc_html(__('Something went wrong!','allergens-dietary')));
			}
		}
		$diet_arr = array();
		$allergen_arr = array();
		foreach ($this->_allergens as $allergen) {
			if ((int) $allergen['is_allergy'] === 0) {
				$diet_arr[] = $allergen;
			} else {
				$allergen_arr[] = $allergen;
			}
		}

		// Create variable that is used in the loops
		?>
	<div class="wrap">
		<button class="filter-button woocommerce wc-block-catalog-sorting has-font-size has-small-font-size" id="ictoria-filter-dropdown-button">
			<?php echo esc_html__('Show allergen filters', 'allergens-dietary'); ?>
		</button>
		<div id="ictoria-filter-dropdown" style="display: none;">
			<form action="" method="post" class="">
				<?php
				wp_nonce_field('allergen-filter-action', 'allergen-filter-nonce'); 
				?>
				<div class="ictoria-filter-container">

					<!-- Allergens Section -->
					<div class="checkbox-container">
						<div class="filter-header">
							<h3><?php echo esc_html__('Allergens', 'allergens-dietary'); ?></h3>
						</div>
						<div class="checkbox-group">
							<?php foreach ($allergen_arr as $allergen) : 

								$checked = isset($_POST['allergen_filter_options'][$allergen['allergy_name']]) ? 'checked' : '';
							?>
								<div class="checkbox-item">
									<input 
										type="checkbox" 
										id="<?php echo esc_attr($allergen['allergy_name']); ?>" 
										class="checkbox" 
										name="allergen_filter_options[<?php echo esc_attr($allergen['allergy_name']); ?>]" 
										value="<?php echo esc_attr($allergen['allergy_name']); ?>" 
										<?php echo esc_attr($checked); ?> 
									/>
									<label for="<?php echo esc_attr($allergen['allergy_name']); ?>">
										<?php echo esc_html__('No ', 'allergens-dietary') . esc_html($allergen['allergy_name']); ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Dietary Restrictions Section -->
					<div class="checkbox-container">
						<div class="filter-header">
							<h3><?php echo esc_html__('Dietary restrictions', 'allergens-dietary'); ?></h3>
						</div>
						<div class="checkbox-group">
							<?php foreach ($diet_arr as $diet) : 
								$checked = isset($_POST['allergen_filter_options'][$diet['allergy_name']]) ? 'checked' : '';
							?>
								<div class="checkbox-item">
									<input 
										type="checkbox" 
										id="<?php echo esc_attr($diet['allergy_name']); ?>" 
										class="checkbox" 
										name="allergen_filter_options[<?php echo esc_attr($diet['allergy_name']); ?>]" 
										value="<?php echo esc_attr($diet['allergy_name']); ?>" 
										<?php echo esc_attr($checked); ?> 
									/>
									<label for="<?php echo esc_attr($diet['allergy_name']); ?>">
										<?php echo esc_html($diet['allergy_name']); ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<!-- Actions Section -->
				<div class="filter-actions">
					<button type="submit" name="allergen_filter" class="filter-button">
						<?php echo esc_html__('Apply Filters', 'allergens-dietary'); ?>
					</button>
					<a 
						href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
						class="filter-button filter-reset" 
						onclick="return confirmResetInput();"
					>
						<?php echo esc_html__('Clear Filters', 'allergens-dietary'); ?>
					</a>
				</div>
			</form>
		</div>
	</div>
	<?php
	}

	/**
	 * @param object $query
	 * @return void
	 * @brief Calls the db and activates a query where the result will be given to woocommerce
	 * Where the input is either  allergens and/or dietary restrictions
	 * So that a customer can see selected products with certain dietary restrictions
	 * and won't see any products containing selected allergens
	 * @author ictoriabv
	 * @since 0.16.5.1
	 * @date 18-11-2024
	 */
	public function filter_query($query)
	{
		if ($query->is_main_query() && is_shop() && isset($_POST['allergen_filter'])) {
			if(!empty(sanitize_text_field(wp_unslash($_POST['allergen-filter-nonce']))) &&
			wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['allergen-filter-nonce'])), 'allergen-filter-action'))
			{
				$this->_filter_nonce = sanitize_text_field(wp_unslash($_POST['allergen-filter-nonce']));
				$selected_options = isset($_POST['allergen_filter_options']) ? array_map('sanitize_text_field', wp_unslash($_POST['allergen_filter_options'])) : array();
				$selected_allergens = array();
				$selected_diatary = array();

				// Sort the selected options
				foreach ($this->_allergens as $allergen) {
					if (isset($selected_options[$allergen['allergy_name']])){
						//check if the allergen is an allergy or diatary restriction
						//where 0 is a dietary restriction and 1 is an allergy
						if ($allergen['is_allergy'] == 0) {
							$selected_diatary[] = $allergen['allergy_name'];
						} else {
							$selected_allergens[] = $allergen['allergy_name'];
						}
					}
				}
				
				// Check if there are any options selected
				if (! empty($selected_options)) {
					$filtered_products = Allergens_Dietary_Allergy_Product_Queries::getInstance()->getFilteredProducts($selected_allergens, $selected_diatary);
					$product_arr = array();
					foreach ($filtered_products as $product) {
						$product_arr[] = $product['product_id'];
					}
					$query->set('post__in', $product_arr);
				}
				
			}
		}
	}
}