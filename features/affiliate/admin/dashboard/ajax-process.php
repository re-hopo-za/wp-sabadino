<?php




function re_aff_set_calculate(){
    update_option('re_aff_set_calculate' , serialize( $_POST['setting']) ) ;
    exit();
}
add_action( 'wp_ajax_re_aff_set_calculate', 're_aff_set_calculate' );
add_action( 'wp_ajax_nopriv_re_aff_set_calculate', 're_aff_set_calculate' );



function re_aff_set_setting(){
    update_option('re_affiliate_settings' , serialize( $_POST['setting']) ) ;
    exit();
}
add_action( 'wp_ajax_re_aff_set_setting', 're_aff_set_setting' );
add_action( 'wp_ajax_nopriv_re_aff_set_setting', 're_aff_set_setting' );


function re_visual_aff_set_setting(){
    update_option('re_visual_aff_set_setting' , serialize( $_POST['setting']) ) ;
    exit();
}
add_action( 'wp_ajax_re_visual_aff_set_setting', 're_visual_aff_set_setting' );
add_action( 'wp_ajax_nopriv_re_visual_aff_set_setting', 're_visual_aff_set_setting' );




