<?php

namespace Sabadino\includes;



class Enqueues
{

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if ( !is_admin() ){
            add_action('wp_enqueue_scripts' , [ $this , 'scripts' ], 99 );
        }
    }

    public function scripts()
    {
        wp_enqueue_script(
            'za_sabadino_main_script' ,
            ZA_ASSETS_URL.'js/sabadino.js',
            array(),
            ZA_SABADINO_SCRIPTS_VERSION ,
            false
        );

        wp_localize_script(
            'za_sabadino_main_script' ,
            'sabadino_main_object',
            [
                'ajax_url'        => admin_url( 'admin-ajax.php' ) ,
                'sabadino_nonce'  => wp_create_nonce('za-main-nonce'),
            ]
        );

        wp_enqueue_style(
            'za_sabadino_main_style' ,
            ZA_ASSETS_URL.'css/sabadino.css',
            array(),
            ZA_SABADINO_SCRIPTS_VERSION
        );


    }


}