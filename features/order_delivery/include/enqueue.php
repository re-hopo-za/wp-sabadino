<?php


add_action('wp_enqueue_scripts'  , 're_wp_enqueue_scripts' );

function re_wp_enqueue_scripts($hook){

    wp_register_style( 're_delivering_style'  , RE_DELIVERING_ASSETS.'re_deliver.css' , '' , time());
    wp_register_script('re_delivering_script' , RE_DELIVERING_ASSETS.'re_deliver.js' , array('jquery') );
    wp_localize_script('re_delivering_script' , 're_deliver_object' , array(
        'admin_url' => admin_url('admin-ajax.php')
    ));
}
