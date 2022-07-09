<?php
/*
 * Template Name: Analytics Template
 * Description: A Page Template For hamfy Plugin.
 */


use Sabadino\features\analytics\includes\AnalyticsUI;

if (!is_user_logged_in()){
    $redirect= '?redirect_to='.urlencode(home_url($_SERVER['REQUEST_URI']));
    nocache_headers();
    wp_redirect(site_url().'/login/'.$redirect,302,'redirect_by_analytics');
    exit();
}else{
    $meta   = get_user_meta( get_current_user_id() , 'hwp_dashboard_access',true);
    if (!current_user_can('administrator') && empty($meta)){
        nocache_headers();
        wp_redirect(site_url().'/403.shtml',301,'redirect_by_analytics');
        exit(403);
    }
}


wp_head();


AnalyticsUI::get_instance();


wp_footer();