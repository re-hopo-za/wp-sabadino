<?php

add_action('wp_enqueue_scripts' , function (){

    if (is_page('my-account')){
        wp_enqueue_script('re_affiliate_js' ,RE_AFFILIATE_ASSETS.'re-affiliate.js' , array('jquery') ,  time());
        wp_enqueue_script('re_chart' ,RE_AFFILIATE_ASSETS.'chart.js' ,null , 998);
        wp_enqueue_style('re_affiliate_css' ,RE_AFFILIATE_ASSETS.'re-affiliate.css' , '' ,time());
        wp_localize_script('re_affiliate_js' , 're_affiliate' , array(

            'admin_url' => admin_url( 'admin-ajax.php' ) ,
        ));
    }

});

