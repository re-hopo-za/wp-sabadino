<?php


namespace Sabadino\features\analytics\includes;




use Sabadino\includes\Functions;

class Loader
{
    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {


        if ( !is_admin() && Functions::isPage('sabadino-reporter') ){
            Processor::get_instance();
            FrontLoader::get_instance();
            AjaxHandler::get_instance();
        }

        CustomRoute::get_instance();





//        add_action('woocommerce_after_register_post_type', [PurchaseProcessor::get_instance(),'get_instance'] ,99 );

    }

}

new Loader();