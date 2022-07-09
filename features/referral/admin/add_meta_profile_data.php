<?php


function re_add_referral_endpoint() {
    add_rewrite_endpoint( 'referral-template', EP_ROOT | EP_PAGES );
    flush_rewrite_rules();

} add_action( 'init', 're_add_referral_endpoint' );


function re_referral_query_vars( $vars ) {
    $vars[] = 'referral-template';
    return $vars;
} add_filter( 'query_vars', 're_referral_query_vars', 0 );


function re_referral_my_account_menu_items( $items ) {
    if ( !isset( $items['referral-template'] ) ){
        $items['referral-template'] =  'کد معرف';
    }
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 're_referral_my_account_menu_items' );





function re_referral_endpoint_content() {

    global $wpdb;
    $table_name = $wpdb->prefix ."re_referral";
    $user_id    = get_current_user_id();
    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

    if ( (int) $retrieve_data[0]->parent_referral  > 0) {
        include RE_ROOT_REFERRAL . 'template/re-referral-did.php'; 
    } else {
        include RE_ROOT_REFERRAL . 'template/re-referral-do.php'; 
    }
}add_action( 'woocommerce_account_referral-template_endpoint', 're_referral_endpoint_content' );