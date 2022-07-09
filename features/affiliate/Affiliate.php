<?php


namespace Sabadino\features\affiliate;


class Affiliate
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
        add_action('admin_enqueue_scripts' , [$this , 'adminEnqueues'] );
    }


    public static function defines()
    {
        define('RE_AFFILIATE_PATH'   , ZA_FEATURES_PATH.'affiliate/' );
        define('RE_AFFILIATE_ADMIN'  , RE_AFFILIATE_PATH.'admin/' );
        define('RE_AFFILIATE_ASSETS' , plugin_dir_url (__FILE__).'assets/' );
    }


    public static function includes()
    {
        if ( !is_admin() ){
            include  RE_AFFILIATE_ADMIN.'enqueue.php';
            include  RE_AFFILIATE_ADMIN.'add_meta_profile.php';
            include  RE_AFFILIATE_ADMIN.'ajax_process.php';
            include  RE_AFFILIATE_ADMIN.'process.php';
        }else{
            include  RE_AFFILIATE_ADMIN.'dashboard/dashboard.php';
            include  RE_AFFILIATE_ADMIN.'dashboard/ajax-process.php';
        }
    }


    public static function adminEnqueues()
    {
        if ( isset( $_GET['page']) ){
            if ( $_GET['page'] == 'affiliate-manager' ) {

                wp_enqueue_script('re_affiliate_admin_js' , RE_AFFILIATE_ASSETS.'admin-assets/aff-admin.js' , array('jquery' )   );

                wp_localize_script('re_affiliate_admin_js' , 're_aff_data' , array(
                    'aff_admin_url'        => admin_url( 'admin-ajax.php' )
                ));
                wp_enqueue_style( 're_affiliate_admin_css' , RE_AFFILIATE_ASSETS.'admin-assets/aff-admin.css' , 99999 , time()   );

            }
        }
    }


    public static function affiliateActivation()
    {
        include RE_AFFILIATE_ADMIN.'db-table.php';
        add_role('re_vendor'  , 'همکاری در فروش'  ,   array( 'read' => true, 'level_0' => true ));
    }

}















