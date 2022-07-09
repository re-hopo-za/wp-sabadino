<?php



namespace Sabadino\features\analytics\includes;


class AnalyticsUI
{

    protected static $_instance = null;
    public static function get_instance(){
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    public function __construct()
    {
        echo file_get_contents(ZA_ANALYTICS_TEMPLATES.'/main.html');
    }

}