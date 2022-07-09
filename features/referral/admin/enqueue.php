<?php


add_action('admin_enqueue_scripts' , function () {

    if ( $_GET['page'] == 'referral') {
        wp_enqueue_script('re_referral_datatable_js', RE_ASSETS_REFERRAL . 'admin_assets/datatables.min.js', array('jquery'), null, true);
        wp_enqueue_script('re_referral_admin_js', RE_ASSETS_REFERRAL . 'admin_assets/referral.js', array('re_referral_datatable_js', 'jquery'), time(), true);
        wp_localize_script('re_referral_admin_js', 're_referral_object', array(
            'admin_url' => admin_url('admin-ajax.php')
        ));

        wp_enqueue_style('re_referral_admin_css', RE_ASSETS_REFERRAL . 'admin_assets/referral.css', null, time());
        wp_enqueue_style('re_referral_datatable_css', RE_ASSETS_REFERRAL . 'admin_assets/datatables.min.css', null, null);
    }

});


add_action('wp_enqueue_scripts' , function () {

//    if ( $_GET['page'] == 'referral') {
//        wp_enqueue_script('re_referral_datatable_js', RE_ASSETS_REFERRAL . 'admin_assets/datatables.min.js', array('jquery'), null, true);
//        wp_enqueue_script('re_referral_admin_js', RE_ASSETS_REFERRAL . 'admin_assets/referral.js', array('re_referral_datatable_js', 'jquery'), time(), true);
//        wp_localize_script('re_referral_admin_js', 're_referral_object', array(
//            'admin_url' => admin_url('admin-ajax.php')
//        ));

    wp_enqueue_style('referral-dashboard-css', RE_ASSETS_REFERRAL . 'referral-dashboard.css', null, time());
    wp_enqueue_script('referral-dashboard-js', RE_ASSETS_REFERRAL . 'referral-dashboard.js', array('jquery'), time(), true);
    wp_localize_script('referral-dashboard-js', 're_referral_dashboard_object', array(
        'admin_url' => admin_url('admin-ajax.php')
    ));

});