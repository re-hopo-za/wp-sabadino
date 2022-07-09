<?php


namespace Sabadino\features\mobile_app;


use Sabadino\includes\Functions;

class MobileApp
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
        self::defines();
        self::includes();
    }


    public static function defines()
    {
        define( 'RE_MOBILE_ROOT' , ZA_FEATURES_PATH.'mobile_app/');
        define( 'RE_MOBILE_ASSETS' , plugin_dir_url(__FILE__).'assets/');
    }


    public static function includes()
    {

        require_once ZA_ROOT_PATH.'features/mobile_app/api.php';
//        APIs::get_instance();
        Dashboard::get_instance();
        AjaxProcessor::get_instance();



    }

}





//    $output     = implode(', ', array_map(
//        function ($v, $k) { return sprintf("%s= %s ", $k, $v); },
//        $products,
//        array_keys($products)
//    ));













