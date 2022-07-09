<?php




namespace Sabadino\features\analytics;



use Sabadino\features\analytics\includes\Loader;

class Analytics{
    protected static $_instance = null;
    public static function get_instance(){
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {


        define('ZA_ANALYTICS_ROOT'      , ZA_FEATURES_PATH.'/analytics/');
        define('ZA_ANALYTICS_PAGES'     , ZA_ANALYTICS_ROOT.'/pages/' );
        define('ZA_ANALYTICS_PARTIALS'  , ZA_ANALYTICS_ROOT.'/pages/partials/' );
        define('ZA_ANALYTICS_TEMPLATES' , ZA_ANALYTICS_ROOT.'/pages/templates' );
        define('ZA_ANALYTICS_INCLUDES'  , ZA_ANALYTICS_ROOT.'/includes/' );
        define('ZA_ANALYTICS_ASSETS'    , plugin_dir_url(__DIR__ ).'analytics/assets/');

        Loader::get_instance();

    }
}