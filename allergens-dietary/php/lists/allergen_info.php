<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_License_Info
 * @brief Class that creates the info
 * the user can see the info
 * @author ictoriabv
 * @date 12-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Info
{
    protected static ?self $_instance = null;



    public function showInfo()
    {

        Allergens_Dietary_Activator::load_style();
        Allergens_Dietary_Activator::enqueue_styles();
        
        //flexbox voor tabs
        ?>
        <div id="info_grid" class="nav-tab-wrapper">
            <div>
                <h1 class="premium"><?php echo esc_html__( 'PREMIUM  [Requires licence]', 'allergens-dietary' ); ?></h1>
                <ol>
                    <li class="contains"><?php echo esc_html__( 'Updating allergies', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Changing allergy themes', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Deleting allergies', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Adding allergies', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Custom look on product', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Custom look in store', 'allergens-dietary' ); ?></li>
                </ol>
            </div>

            <div>
                <h1 class="free"><?php echo esc_html__( 'FREE VERSION  [Standard]', 'allergens-dietary' ); ?></h1>
                <ol>
                    <li class="contains"><?php echo esc_html__( 'Connecting allergies to products', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'WordPress theme friendly styles', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'An allergen overview', 'allergens-dietary' ); ?></li>
                    <li class="contains"><?php echo esc_html__( 'Turning the use of allergies on/off', 'allergens-dietary' ); ?></li>
                </ol>
            </div>
        </div>
        <br><br>
        <?php   
        //moet nog aangepast worden in css
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getStyles()
    {
        wp_register_style('allergens-dietary-css', plugins_url(ALLERGENS_DIETARY_NAME . '/assets/css/allergens-dietary.css'));
        wp_enqueue_style('allergens-dietary-admin-css', plugins_url('assets/css/allergens-dietary.css', ALLERGENS_DIETARY_FILE));
    }
}