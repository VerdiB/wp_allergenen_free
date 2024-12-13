<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . '/wp-admin/includes/class-wp-list-table.php');
}

if (!class_exists('Allergens_Dietary_Allergen_Queries')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergen.php';
}

if ( ! class_exists( 'Allergens_Dietary_Notices' ) ) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
}

if ( ! class_exists( 'Allergens_Dietary_Form' ) ) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_form.php';
}

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * @class Allergens_Dietary_Show_Allergens
 * @brief Class that shows the allergens
 * the user can see the already created allergies
 * @author Ictoria
 * @date 24-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Show_Allergens extends WP_List_Table
{

    protected static $_instance = [];
    protected static int $_page = 0;
    protected static string $message = "";
    // Page is statisch zodat er maar 1 is, en de zelfde waarde blijft.

    protected function __construct()
    {
        parent::__construct([
            'singular' => 'item',
            'plural' => 'items',
            'ajax' => false,
        ]);
        self::$_page = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : (self::$_page === null ? 0 : self::$_page);


        // $notice = Allergens_Dietary_Notices::getInstance();
        // $notice->display_admin_notice(Notice_Types::WARNING, __('is great success', 'allergens-dietary'));
    }

    protected $table_action_options = ['change_status'];

    public $search_query;

    public function get_items_per_pages(): int
    {
        return get_option('items_per_page', 10);
    }
    protected function set_items_per_pages($value)
    {
        update_option('items_per_page', $value);
    }



    public function get_table_columns_and_data()
    {
        $columns = Allergens_Dietary_Allergen_Queries::getColumns();

        $column_names = [];

        foreach ($columns as $column) {
            $column_names[$column["Field"]] = ucfirst(str_replace('_', ' ', $column['Field']));
        }

        $data = Allergens_Dietary_Allergen_Queries::getItems();

        $allergy_names = [];

        foreach ($data as $row) {
            $allergy_names[] = $row['allergy_name'];
        }

        return [
            'columns' => $column_names,
            'data' => $allergy_names,
        ];

    }

    public function column_default($item, $column_name)
    {
        $translationOfAllergenNumbers = $item['is_allergy'] == 1 ? "Allergy" : "Diet";
        $translationOfIsActiveNumbers = $item['is_active'] == 1 ? "Active" : "Inactive";

        switch ($column_name) {
            case 'allergy_name':
                return esc_html($item[$column_name]);
            case 'allergy_description':
                return esc_html($item[$column_name]);
            case 'is_allergy':
                return $translationOfAllergenNumbers;
            case 'is_active':
                return $translationOfIsActiveNumbers;
            default:
                return array($item, true);
        }
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="post[]" value="%s"/>', $item['allergy_name']);
    }

    public function single_row($item)
    {
        $this->column_location_id($item);
        echo '<tr class="inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-post">';
        $this->single_row_columns($item);
        echo '</tr>';
    }

      public function handle_row_actions($item, $column_name, $primary)
    {
        if ($primary !== $column_name) {
            return '';
        }
        if ( ! is_plugin_active( 'allergens-dietary-pro/allergens-dietary-pro.php' ) ) {
            $valid_actions = $this->table_action_options;
        } elseif ( class_exists( 'Allergens_Dietary_Pro_Show_Allergens' ) ) {
            // Only access the class if it exists
            $valid_actions = Allergens_Dietary_Pro_Show_Allergens::getInstance()->table_action_options;
        } else {
            // Fallback if the class doesn't exist but the plugin is active
            $valid_actions = $this->table_action_options;
        }

        $action_links = array();


        foreach ($valid_actions as $action) {
            if ( ! is_plugin_active( 'allergens-dietary-pro/allergens-dietary-pro.php' ) ) {
                $action_links[$action] = Allergens_Dietary_Show_Allergens::getInstance()->build_action_url($action, $item);
            } elseif ( class_exists( 'Allergens_Dietary_Pro_Show_Allergens' ) ) {
                $action_links[$action] = Allergens_Dietary_Pro_Show_Allergens::getInstance()->build_action_url($action, $item);
            }else{
                $action_links[$action] = Allergens_Dietary_Show_Allergens::getInstance()->build_action_url($action, $item);
            }
        }

        return $this->row_actions($action_links);
    }


    protected function build_action_url($action, $item) // loop-build actions for quick actions.
    {
        $color = "blue";

    if (esc_attr($action) == 'change_status'){
        return sprintf(
            '<a style="color: ' . $color . ';" href="?page=%s' . (self::$_page > 0 ? '&paged=' . strval(self::$_page) : '') . '&item=%s&action=%s&_wpnonce=%s">%s</a>',
            esc_attr($_REQUEST['page']),
            esc_attr($item['allergy_name']),
            esc_attr($action),
            wp_create_nonce('allergens_' . $action),
            ucfirst(str_replace('_', ' ', $action)),
        );
    }
    }

    protected function handle_search()
    {
        if (isset($_POST['search'])) {
            $this->search_query = isset($_POST['search'])
                ? ($this->search_query !== $_POST['search'] ? sanitize_text_field($_POST['search']) : $this->search_query)
                : '';
        }
    }

    protected function handle_items_per_page()
    {
        if (isset($_POST['items_per_page'])) {
            if ($_POST['items_per_page'] < 1) {
                $this->set_items_per_pages(10);
                return;
            }
            if (!empty($_POST['items_per_page'])) {
                $this->set_items_per_pages(!empty($_POST['items_per_page']) ? $_POST['items_per_page'] : 10);
            }
        }
    }

    public function get_bulk_actions()
    {
        $actions = array();
        $actions['change_status'] = __('Change status', 'allergens-dietary');
        return $actions;
    }


    protected function single_row_columns($item)
    {

        list($columns, $hidden, $sortable, $primary) = $this->get_column_info();

        foreach ($columns as $column_name => $column_display_name) {
            $classes = "$column_name column-$column_name";
            if ($primary === $column_name) {
                $classes .= ' has-row-actions column-primary';
            }

            if (in_array($column_name, $hidden, true)) {
                $classes .= ' hidden';
            }

            $data = 'data-colname="' . esc_attr(wp_strip_all_tags($column_display_name)) . '"';

            $attributes = "class='$classes' $data";

            if ('cb' === $column_name) {
                echo '<th scope="row" class="check-column">';
                echo $this->column_cb($item);
                echo '</th>';
            } elseif (method_exists($this, '_column_' . $column_name)) {
                echo call_user_func(
                    array($this, '_column_' . $column_name),
                    $item,
                    $classes,
                    $data,
                    $primary
                );
            } elseif (method_exists($this, 'column_' . $column_name)) {
                echo "<td $attributes>";
                echo call_user_func(array($this, 'column_' . $column_name), $item);
                echo $this->handle_row_actions($item, $column_name, $primary);
                echo '</td>';
            } else {
                echo "<td $attributes>";
                if ($column_name !== "allergy_name"){
                    echo $this->column_default($item, $column_name);
                }else{
                    echo "<span class='allergen_name'>" . $this->column_default($item, $column_name) . "</span>";
                }
                echo $this->handle_row_actions($item, $column_name, $primary);
                echo '</td>';
            }
        }
    }

    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'allergy_name' => __('Allergy name', 'allergens-dietary'),
            'allergy_description' => __('Allergy description', 'allergens-dietary'),
            'is_allergy' => __('Allergy or Dietary', 'allergens-dietary'),
            'is_active' => __('Status', 'allergens-dietary'),
        );
        return $columns;
    }

    public function prepare_items()
    {
        $data = Allergens_Dietary_Allergen_Queries::getItems();

        $this->_column_headers = [$this->get_columns(), [], []];

        if (!empty($this->search_query)) {
            $this->items = array_filter($data, function ($item) {
                return stripos($item['allergy_name'], $this->search_query) !== false;
            });
        } else {
            $this->items = $data;
        }

        $total_items = count($this->items);

        $per_page = $this->get_items_per_page('my_list_table_per_page', $this->get_items_per_pages());
        $current_page = $this->get_pagenum();

        // Fetch data for the current page
        $this->items = array_slice($this->items, ($current_page - 1) * $per_page, $per_page);

        // Set pagination args
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }


    public function process_quick_action()
    {
        if (isset($_GET['action']) && isset($_GET['item'])) {
            $item = sanitize_text_field($_GET['item']);
            $action = sanitize_text_field($_GET['action']);
            $nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Verify nonce based on action
            if ($action === 'change_status' && !wp_verify_nonce($nonce, 'allergens_change_status')) {
                self::$message = __("Security check failed for changing status!", 'allergens-dietary');
            }

            // Perform action based on case
            switch ($action) {
                case 'change_status':
                    self::$message = __("Status changed", 'allergens-dietary');
                    Allergens_Dietary_Allergen_Queries::getInstance()->singleActivationUpdate(self::$_page, self::$message);
                    return self::$message;
                break;
            }
        }
    }

    public function process_bulk_action($data)
    {
        // Check if nonce is set and not empty
        if (isset($_GET['_wpnonce']) && !empty($_GET['_wpnonce'])) {
            $nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $action = $this->current_action();
            foreach ($this->table_action_options as $bulk_action) {
                if ($action === $bulk_action) {
                    $nonce_action = 'bulk_' . $bulk_action;
                    break;
                }
            }
            // Verify the nonce with the correct action
            if (!wp_verify_nonce($nonce, $nonce_action)) {
                wp_die('Invalid token.');
            }
        }

        if (!isset($data['item'])) {
            return;
        }

        $action = $this->current_action();
        switch ($action) {
            case 'change_status':
                self::$message = __("Status changed", 'allergens-dietary');
                Allergens_Dietary_Allergen_Queries::getInstance()->activationUpdate($data, self::$message);
                break;
        }
    }

    public function items_per_page_form($text, $input_id, $label, $which) // Custom form for selecting items per page.
    {
        if (empty($_POST['items_per_page']) && !$this->has_items()) {
            return;
        }
        $acceptable_values = array(10, 20, 50, 100);
        if ('top' === $which) {
            $this->screen->render_screen_reader_content('heading_pagination');
?>
            <span class="item-select-box" style="float: right; margin-right: 10px;">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo $text; ?>:</label>
                <span><?php echo $label; ?></span>
                <select id="items_per_page" name="items_per_page">
                    <?php
                    foreach ($acceptable_values as $value) {
                        if ($value == 10) {
                    ?>
                            <option value="<?php echo $value ?>" <?php echo $this->get_items_per_pages() == 10 ? 'selected' : (in_array($this->get_items_per_pages(), $acceptable_values) ? '' : 'selected'); ?>><?php echo $value ?>
                            </option>
                        <?php
                        } else {
                        ?>
                            <option value="<?php echo $value ?>" <?php echo $this->get_items_per_pages() == $value ? 'selected' : '' ?>>
                                <?php echo $value ?>
                            </option>
                    <?php
                        }
                    }
                    ?>
                </select>
                <?php submit_button($text, '', '', false, array('id' => 'items-per-page-submit')); ?>
            </span>
        <?php
        }
        if ('bottom' === $which) {
        ?>
            <span class="item-select-box" style="float: right; margin-right: 10px;">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo $text; ?>:</label>
                <span><?php echo $label; ?></span>
                <span
                    class="tablenav-paging-text"><?php echo !empty($this->get_items_per_pages()) ? $this->get_items_per_pages() : null; ?></span>
            </span>
        <?php
        }
    }

    public function search_box($text, $input_id) // a WP_LIST_TABLE FUNCTION; Built custom for name and position reasons.
    {
        if (empty($_REQUEST['search']) && !$this->has_items()) {
            return;
        }

        $input_id = $input_id . '-search-input';

        if (!empty($_REQUEST['orderby'])) {
            if (is_array($_REQUEST['orderby'])) {
                foreach ($_REQUEST['orderby'] as $key => $value) {
                    echo '<input type="hidden" name="orderby[' . esc_attr($key) . ']" value="' . esc_attr($value) . '" />';
                }
            } else {
                echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST['orderby']) . '" />';
            }
        }
        if (!empty($_REQUEST['order'])) {
            echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST['order']) . '" />';
        }
        if (!empty($_REQUEST['post_mime_type'])) {
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr($_REQUEST['post_mime_type']) . '" />';
        }
        if (!empty($_REQUEST['detached'])) {
            echo '<input type="hidden" name="detached" value="' . esc_attr($_REQUEST['detached']) . '" />';
        }

        ?>
        <span class="search-box" style="float: right; margin-bottom: 10px;">
            <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo esc_attr($input_id); ?>" name="search"
                value="<?php echo isset($this->search_query) ? $this->search_query : '' ?>" />
            <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
        </span>
    <?php
    }

    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$_instances[$cls])) {
            self::$_instance[$cls] = new static();
        }

        return self::$_instance[$cls];
    }

    protected function display_tablenav($which) // WP_LIST_TABLE FUNCTION!
    {
        if ('top' === $which) {
            wp_nonce_field('bulk-' . $this->_args['plural']);
        }
    ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">

            <?php if ($this->has_items()): ?>
                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions($which); ?>
                </div>
            <?php
            endif;
            $this->extra_tablenav($which);
            $this->pagination($which);
            $this->items_per_page_form('Select', 'items-per-page', 'Allergies per page:', $which);
            ?>

            <br class="clear" />
        </div>
<?php
    }

    
    public function table_page() {
        $table = new Allergens_Dietary_Show_Allergens();
        $table->handle_search();
        $table->handle_items_per_page();
        $table->prepare_items();
        echo '<form action="#" method="POST" id="show_allergens_form" enctype="multipart/form-data">';
        $table->search_box('Search', 'allergens');
            $table->display();
        echo "</form>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['messaged'])){
        $type = Notice_Types::INFO;
        $notice = Allergens_Dietary_Notices::getInstance();
        $notice->display_admin_notice($type, htmlspecialchars($_GET['messaged']));
    }
    if (isset($_POST['action'])){
        if ($_POST['action'] = -1){
            Allergens_Dietary_Form::setFormType(FormType::ALLERGENS);
            Allergens_Dietary_Form::getInstance()->submitUpdate();
        }
    }

    if (isset($_POST['action']) && isset($_POST['post'])) {
        $process_action = sanitize_text_field($_POST['action']);
        $process_item = array_map('sanitize_text_field', $_POST['post']);
        $process_data = ['action' => $process_action, 'item' => $process_item];
        Allergens_Dietary_Show_Allergens::getInstance()->process_bulk_action($process_data);
    }
    if (isset($_POST['search'])) {
        $search_query = sanitize_text_field($_POST['search']);
        $table = Allergens_Dietary_Show_Allergens::getInstance();
        $table->search_query = $search_query;
        $table->prepare_items();
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['messaged'])){
                $type = Notice_Types::INFO;
                $notice = Allergens_Dietary_Notices::getInstance();
                $notice->display_admin_notice($type, htmlspecialchars($_GET['messaged']));
            }
            $table = Allergens_Dietary_Show_Allergens::getInstance();

            $table->process_quick_action();
    }
}
