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

if (! class_exists('Allergens_Dietary_Notices')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
}

if (! class_exists('Allergens_Dietary_Form')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_form.php';
}


class TestTable extends WP_List_Table
{
    // is used for redirects of the page
    protected const PAGE = 'allergens-test-table';
    
    //instance of the class being called
    private static $_instance = null;

    // is mostlly used to read from
    protected array $_allergens;

    /**
     * @author ictoriabv
     * @brief singleton object makes sure that the class is only called once
     * during lifetime
     * @return object
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance) || is_null(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    /**
     * @author ictoriabv
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function __construct()
    {
        parent::__construct([
            'singular' => 'item',
            'plural' => 'items',
            'ajax' => false,
            'rest_api',
        ]);
    }

    /**
     * @author ictoriabv
     * @brief prepares items for the table and must be called
     * after the instance
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    public function prepare_items()
    {      

        if (!empty($_POST['s']) && check_admin_referer('allergen_val')){
            $this->_allergens = Allergens_Dietary_Allergen_Queries::getInstance()->search_allergen(
                htmlspecialchars(sanitize_text_field(wp_unslash($_POST['s'])))
            );
        }else{
            $this->_allergens = Allergens_Dietary_Allergen_Queries::getItems();
        }
        
        $this->_column_headers = array(
            $this->get_column_headers(), // All columns.
            array(), // Hidden columns.
        );
        
        $this->process_bulk_action();
        
        $this->items = $this->_allergens;
        
        $total_items = count($this->items);
        $items_per_page = $this->get_items_per_page('allergens_per_page');
        $current_page = $this->get_pagenum();

        $this->items = array_slice($this->items, ($current_page - 1) * $items_per_page, $items_per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'items_per_page' => $items_per_page,
            'total_pages' => ceil($total_items / $items_per_page)
        ));
    }

    /**
     * @author ictoriabv
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function get_bulk_actions()
    {
        return array(
            'bulk-change-status' => __('Change status', 'allergens-dietary'),
        );
    }

    /**
     * @author ictoriabv
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function get_column_headers()
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

    /**
     * @author ictoriabv
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
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

    /**
     * @author ictoriabv
     * @brief adds default behaviour on the allergen collumns
     * @param array|object $item
     * @param string $column_name
     * @return string|array
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function column_default($item, $column_name)
    {
        $translationOfAllergenNumbers = $item['is_allergy'] == 1 ? "Allergy" : "Diet";
        $translationOfIsActiveNumbers = $item['is_active'] == 1 ? "Active" : "Inactive";

        switch ($column_name) {
            case 'allergy_name':
                return esc_html($this->column_allergy_name($item));
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

    /**
     * @author ictoriabv
     * @brief defines a custom response on column rows for allergens
     * In this case only to change its status
     * @param array|object $item
     * @return string
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function column_allergy_name(array|object $item){
        $status_nonce = esc_attr(wp_create_nonce("change-status-" . $item['allergy_name']));
        // $page = 'allergens-test-table';
        $status_url = add_query_arg(
            array(
                'page'      =>  self::PAGE,
                'action'    =>  'change-status',
                'item'      =>  $item['allergy_name'],
                'paged'     =>  $this->get_pagenum(),
                '_wpnonce'  =>  $status_nonce
            ),
            admin_url('admin.php')
        );

        $actions = array(
            'change status' =>sprintf(
                '<a href="%s">%s</a>',
                $status_url,
                __('Change Status', 'allergens-dietary')
            )
        );
        return sprintf('%1$s %2$s',$item['allergy_name'] , $this->row_actions($actions));
    }

    /**
     * @author ictoriabv
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="allergens[]" value="%s" />',
            $item['allergy_name']
        );
    }

    /**
     * @author ictoriabv
     * @brief Handles bulk action on all allergens
     * where as for now only changes the status of an allergy/dietary
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function process_bulk_action(){
        //check the nonce
        if(isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])){
            //sanitize the nonce
            $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
            $action = 'bulk-' . $this->_args['plural'];

            //verify nonce
            if(!wp_verify_nonce($nonce, $action)) {
                wp_die(esc_html(__('Security check failed!', 'allergens-dietary')));
            }

            //check if there are allergens in array sends query to db
            if(isset($_POST['allergens']) && !empty($_POST['allergens'])){
                if ('bulk-change-status' === $this->current_action()){
                    $to_change = wp_unslash($_POST['allergens']);
                    $allergen_query_arr = array();
                    foreach($this->_allergens as $allergen){
                        if (in_array($allergen['allergy_name'], $to_change)){
                            $allergen_query_arr[]= $allergen;
                        }
                    }
                    foreach($allergen_query_arr as $allergen_query){
                        $allergen_query['is_active'] =  ($allergen_query['is_active'] == 1)? 0 : 1;
                        Allergens_Dietary_Allergen_Queries::getInstance()->change_status($allergen_query);
                    }
                    wp_redirect(admin_url('admin.php?page=' . self::PAGE . '&paged='. $this->get_pagenum()));
                    exit;
                }
            }
        }
    }

    /**
     * @author ictoriabv
     * @overload from parrent method and can be overloaded still
     * @brief handles custom row actions on the allergen table
     * for this version of the plug-in it will only handle status changes
     * @param object|array $item
     * @param string $column_name
     * @param string $primary
     * @return void
     * @since V0.18.6.0
     * @version V0.18.6.0
     */
    protected function handle_row_actions($item, $column_name, $primary)
    {
        parent::handle_row_actions($item, $column_name, $primary);
        
        if (!empty(sanitize_url(wp_unslash($_GET['item']))) &&
            !empty(sanitize_url(wp_unslash($_GET['action']))) &&
            !empty(sanitize_url(wp_unslash($_GET['page'])))
        ){
            // $table_page = preg_replace('/^https?:\/\//','',sanitize_url(wp_unslash($_GET['page'])));
            $allergen_name = preg_replace('/^https?:\/\//','',sanitize_url(wp_unslash($_GET['item'])));
            $table_action = preg_replace('/^https?:\/\//','', sanitize_url(wp_unslash($_GET['action'])));
            if(check_admin_referer("change-status-" . $allergen_name)){
                if ($table_action === 'change-status'){
                    $allergen_active = array();
                    foreach ($this->_allergens as $allergen){
                        if(array_search($allergen_name, $allergen)){
                            $allergen_active = $allergen;
                            break;
                        }
                    }
                    $allergen_active['is_active'] = ($allergen_active['is_active'] == 1)? 0 : 1;
                    Allergens_Dietary_Allergen_Queries::getInstance()->change_status($allergen_active);
                    
                    wp_redirect(admin_url('admin.php?page=' . self::PAGE . '&paged='. $this->get_pagenum()));
                    exit;
                }
            }
        }
    }
}
