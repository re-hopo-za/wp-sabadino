<?php


function re_affiliate() {


    $user_id = get_current_user_id();

    $user  = get_user_by('id' , $user_id );
    if ( $user->first_name == '' ){
        wp_update_user(  array( 'ID' => $user_id ,  'first_name' => $_POST['re_first_name'] ,  'last_name' => $_POST['re_last_name']  ) );
    }


    $aff_parent_check = get_user_meta( $user->ID ,'re_affiliate_id' ,true );
    global $wpdb;
    $table_profile = $wpdb->prefix . "re_aff_profile";
    $data = [
        'user_id'        => $user_id                ,
        'date_register'  => jdate('Y-m-d H:i:s')    ,
        'aff_token'      => md5('re_'.$user_id) ,
        'app_target'     => $_POST['re_add_app']    ,
        'how_know'       => $_POST['re_how_find']   ,
        'cart_number'    => $_POST['re_cart_number'],
        'cart_self_name' => $_POST['re_cart_name']  ,
        'adds_link'      => $_POST['re_add_link']  ,
        'parent'         => $aff_parent_check > 0 ? $aff_parent_check : 0
    ];
    $format = [ '%d' ,'%s' ,'%s' ,'%s' ,'%s' ,'%s' ,'%s' ,'%s' ];
    $wpdb->insert( $table_profile , $data , $format );


    $user->remove_role( 'customer' );
    $user->add_role( 're_vendor' );


    exit();
}


add_action( 'wp_ajax_re_affiliate', 're_affiliate' );
add_action( 'wp_ajax_nopriv_re_affiliate', 're_affiliate' );

