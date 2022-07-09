<?php




function re_add_affiliate_endpoint() {
    add_rewrite_endpoint( 'affiliate-template', EP_ROOT | EP_PAGES );
    flush_rewrite_rules();

} add_action( 'init', 're_add_affiliate_endpoint' );


function re_affiliate_query_vars( $vars ) {
    $vars[] = 'affiliate-template';
    return $vars;
} add_filter( 'query_vars', 're_affiliate_query_vars', 0 );



function re_affiliate_my_account_menu_items( $items ) {
    if ( !isset( $items['affiliate-template'] ) ){
        $items['affiliate-template'] =  'همکاری در فروش';
    }
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 're_affiliate_my_account_menu_items' );



function re_affiliate_endpoint_content() {
//   $user = get_user_by('id' , get_current_user_id() );
    if (current_user_can('re_vendor') ){
        include RE_AFFILIATE_PATH.'/template/affiliate_account.php';
    }else{
        include RE_AFFILIATE_PATH.'/template/affiliate_register.php';
    }
}add_action( 'woocommerce_account_affiliate-template_endpoint', 're_affiliate_endpoint_content' );




