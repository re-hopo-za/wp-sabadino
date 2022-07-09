<?php



namespace Sabadino\features\product_slider;


use Elementor\Plugin;
use Sabadino\features\product_slider\widgets\ProductSliderWidget;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}




/**
 * Do all addon related works
 */
final class ProductsSlider
{

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var ProductSliderWidget The single instance of the class.
     */

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {


        Products::get_instance();
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'missing_elementor_plugin']);
            return;
        }
        if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('admin_notices', [$this, 'missing_woocommerce_plugin']);
            return;
        }
        if (!version_compare(ELEMENTOR_VERSION, '2.0.0' , '>=')) {
            add_action('admin_notices', [$this, 'minimum_elementor_version']);
            return;
        }
        if (version_compare(PHP_VERSION, 7, '<')) {
            add_action('admin_notices', [$this, 'minimum_php_version']);
            return;
        }
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        add_action('elementor/init', [ $this, 'addCategory'] );

        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueueStyles']  );
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueueScripts']  );


        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueueStyles']  );
        add_action('elementor/frontend/after_register_scripts', [$this, 'enqueueScripts']  );

        define('ZA_PRODUCT_SLIDER_ROOT'   , ZA_FEATURES_PATH.'/product_slider/');
        define('ZA_PRODUCT_SLIDER_ASSETS' , plugin_dir_url(__DIR__ ).'product_slider/assets/');
//        ProductSliderWidget::get_instance();

    }

    /**
     * Admin Notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function missing_elementor_plugin()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            esc_html('"%1$s" requires "%2$s" to be installed and activated.', 'wpce`'),
            '<strong>' . esc_html('Product Carousel Slider for Elementor', 'wpce`') . '</strong>',
            '<strong>' . esc_html('Elementor', 'wpce`') . '</strong>'
        );

        printf('<div class="notice notice-error"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin Notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function missing_woocommerce_plugin()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            esc_html('"%1$s" requires "%2$s" to be installed and activated.', 'wpce`'),
            '<strong>' . esc_html('Product Carousel Slider for Elementor', 'wpce`') . '</strong>',
            '<strong>' . esc_html('WooCommerce', 'wpce`') . '</strong>'
        );

        printf('<div class="notice notice-error"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function minimum_elementor_version()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html('"%1$s" requires "%2$s" version %3$s or greater.', 'wpce`'),
            '<strong>' . esc_html('Product Carousel Slider for Elementor', 'wpce`') . '</strong>',
            '<strong>' . esc_html('Elementor', 'wpce`') . '</strong>',
            '2.0.0'
        );

        printf('<div class="notice notice-error"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function minimum_php_version()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html('"%1$s" requires "%2$s" version %3$s or greater.', 'wpce`'),
            '<strong>' . esc_html('Product Carousel Slider for Elementor', 'wpce`') . '</strong>',
            '<strong>' . esc_html('PHP', 'wpce`') . '</strong>',
            7
        );

        printf('<div class="notice notice-error"><p>%1$s</p></div>', $message);

    }

    /**
     * Enqueue Styles
     *
     * Load all required stylesheets
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function enqueueStyles()
    {

        wp_enqueue_style(
            'wb-slick-library',
            ZA_PRODUCT_SLIDER_ASSETS . 'slick/slick.css',
            [],
            ZA_SABADINO_SCRIPTS_VERSION
        );
        wp_enqueue_style(
            'wb-slick-theme',
            ZA_PRODUCT_SLIDER_ASSETS . 'slick/slick-theme.css',
            ['wb-slick-library'],
            ZA_SABADINO_SCRIPTS_VERSION
        );



    }

    /**
     * Enqueue Admin Styles and Scripts
     *
     * Load Admin stylesheets and scripts
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_scripts_styles()
    {


    }

    /**
     * Enqueue Scripts
     *
     * Load all required scripts
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function enqueueScripts()
    {

        wp_enqueue_script(
            'wb-slick-library' ,
            ZA_PRODUCT_SLIDER_ASSETS . 'slick/slick.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );

        wp_enqueue_script(
            'ajax-queue-script' ,
            ZA_PRODUCT_SLIDER_ASSETS . 'jquery.ajaxQueue.min.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );


        wp_enqueue_script(
            'za-cart-script',
            ZA_PRODUCT_SLIDER_ASSETS . 'cart.js',
            ['jquery' ,'wb-slick-library'],
            ZA_SABADINO_SCRIPTS_VERSION
        );

        wp_localize_script(
            'za-cart-script' ,
            're_cart_ob' ,
            [
                'admin_url' => admin_url('admin-ajax.php') ,
                'home_url'  => home_url()
            ]
        );



    }

    /**
     * Register Widget
     *
     * Register Elementor Before After Image Comparison Slider From Here
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function register_widgets()
    {
        $this->includes();
        $this->register_slider_widgets();
    }

    /**
     * Include Files
     *
     * Load widgets php files.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function includes()
    {
        ProductSliderWidget::get_instance();
    }

    /**
     * Register Woo Product Carousel Widget
     *
     * Register the Woo Product Carousel Widget from here
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function register_slider_widgets()
    {
        Plugin::instance()->widgets_manager->register_widget_type( ProductSliderWidget::get_instance() );
    }



    public function addCategory() {
        Plugin::$instance->elements_manager->add_category(
            'za-product-slider',
            [
                'title' => esc_html( 'Web Builders Element' ),
                'icon' => 'fa fa-plug'
            ]
        );
    }



}











