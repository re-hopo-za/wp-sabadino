<?php
/**
 * Plugin Name: Sabadino
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Your extension's description text.
 * Version: 1.0.0
 * Author: Hossein pour reza
 * Author URI: http://yourdomain.com/
 * Developer: Hossein pour reza
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


use Sabadino\features\analytics\includes\PurchaseProcessor;
use Sabadino\features\analytics\includes\SendSMSProcessor;
use Sabadino\includes\Cron;
use Sabadino\includes\Loader;
use Sabadino\includes\Register;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Sabadino{


    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {
        define('ZA_ASSETS_URL'     , plugin_dir_url(__FILE__).'/assets/');
        define('ZA_ROOT_URL'       , plugin_dir_url(__FILE__) );
        define('ZA_ROOT_PATH'      , plugin_dir_path(__FILE__));
        define('ZA_REGISTER_ROOT'  ,  __FILE__  );
        define('ZA_FEATURES_PATH'  , plugin_dir_path(__FILE__).'features/');
        define('ZA_VERSION'        , '2.1.8');
        define('ZA_DEVELOPER_MODE' , true );

        define('ZA_SABADINO_SCRIPTS_VERSION',  ZA_DEVELOPER_MODE ? time() : ZA_VERSION );
 

        require_once ZA_ROOT_PATH . 'vendor/autoload.php';


        self::withoutHook();
        add_action( 'init'    ,[ $this ,'load'] , 12 );

    }


    public static function withoutHook(){
        Register ::get_instance();
        add_filter( 'cron_schedules' ,[ Cron::get_instance() ,'customInterval'] , 1 );
        add_action( 'za_process_user_purchase_list' ,[ PurchaseProcessor::get_instance() ,'run'] , 10 );
        add_action( 'za_send_sms_handler'           ,[ SendSMSProcessor::get_instance()  ,'run'] , 10 );


    }


    public static function load()
    {
        Loader::get_instance();
    }


}

new Sabadino();




