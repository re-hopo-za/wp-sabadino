<?php


namespace Sabadino\features\referral;


class Referral
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
        add_action( 'wp_ajax_re_referral_action', [$this , 're_referral_action'] );

    }


    public static function defines()
    {
        define('RE_ROOT_REFERRAL'   , ZA_FEATURES_PATH.'referral/');
        define('RE_ADMIN_REFERRAL'  , RE_ROOT_REFERRAL.'/admin/');
        define('RE_PUBLIC_REFERRAL' , RE_ROOT_REFERRAL.'/public/');
        define('RE_ASSETS_REFERRAL' , plugin_dir_url(__FILE__).'assets/');
    }


    public static function includes()
    {
        include RE_ADMIN_REFERRAL.'enqueue.php';
        include RE_ADMIN_REFERRAL.'process.php';
        include RE_ADMIN_REFERRAL.'dashboard.php';
        include RE_ADMIN_REFERRAL.'ajax-proccessor.php';
        include RE_ADMIN_REFERRAL.'add_meta_profile_data.php';
    }


    public static function re_referral_action(){
        ?>
        <div class="userRefCon">
            <div class="item-1">

            </div>
            <div class="item-2">

            </div>
            <div class="item-3">

            </div>
        </div>

        <?php

        exit();
    }

    public static function re_create_referral_db_table()
    {
        include  RE_ADMIN_REFERRAL.'db-table.php';
    }


}





