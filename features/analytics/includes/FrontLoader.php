<?php


namespace Sabadino\features\analytics\includes;




class FrontLoader
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

        add_action('wp_enqueue_scripts' , [ $this ,'enqueues'] );
        add_action('wp_ajax_za_search_products'  , array( $this , 'searchProducts' ));

    }

    public static function enqueues()
    {
        $meta   = get_user_meta( get_current_user_id() , 'za_dashboard_access',true);
        if ( is_page( 'sabadino-reporter') && (current_user_can('administrator') || !empty($meta)) ){

            wp_enqueue_script(
                'za_jquery_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/jquery-1.10.1.min.js'
            );
            wp_register_script(
                'za_persian_date_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/persian-date.js'
            );
            wp_register_script(
                'za_datepicker_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/persian-datepicker.js'
            );
            wp_enqueue_script(
                'za_highstock_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/highstock.js'
            );

            wp_enqueue_script(
                'za_exporting_js'       ,
                ZA_ANALYTICS_ASSETS.'js/vendor/exporting.js'
            );
            wp_enqueue_script(
                'za_export_data_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/export-data.js'
            );
            wp_enqueue_script(
                'za_coloraxis_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/coloraxis.js'
            );
            wp_enqueue_script(
                'za_accessibility_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/accessibility.js'
            );
            wp_enqueue_script(
                'za_variable_pie_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/variable-pie.js'
            );
            wp_enqueue_script(
                'za_highchart_data_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/data.js'
            );
            wp_enqueue_script(
                'za_moment_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/moment.js'
            );
            wp_enqueue_script(
                'za_series_label_js'      ,
                ZA_ANALYTICS_ASSETS.'js/vendor/series-label.js'
            );

            wp_enqueue_script(
                'za_analytics_js'      ,
                ZA_ANALYTICS_ASSETS.'analytics.js' ,
                [ 'za_jquery_js','za_datepicker_js','za_persian_date_js'] ,
                time()
            );

            wp_localize_script(
                'za_analytics_js' ,
                'sabadino_analytics_object',
                [
                    'new_users_daily'      => Processor::newUsersCounter() ,
                    'sales_last_month'     => Processor::lastMonthSales() ,
                    'total_sales_pie'      => Processor::totalSales() ,
                    'five_products'        => Processor::fiveProducts() ,
                    'five_products_keys'   => Processor::fiveProductsKeys(true ) ,
                    'specific_product'     => Processor::specificProduct() ,
                    'courses_keys'         => Processor::coursesKeys( true ) ,
                    'dailySales'           => Processor::dailySales() ,
                    'daily_user_register'  => Processor::dailyUserRegister(),
                    'ajax_url'             => admin_url( 'admin-ajax.php' ) ,
                    'sabadino_nonce'       => wp_create_nonce('za-analytics-page'),
                    'final_users_list'     => AjaxHandler::finalUsersList() ,
                    'final_sms_list'       => AjaxHandler::getSMSList( 200 ) ,
                ]
            );

            wp_enqueue_style(
                'za_bootstrap_css'      ,
                ZA_ANALYTICS_ASSETS.'css/vendor/bootstrap.min.css'
            );
            wp_enqueue_style(
                'za_light_css'      ,
                ZA_ANALYTICS_ASSETS.'css/dore.light.blue.css'
            );
            wp_enqueue_style(
                'za_analytics_css'      ,
                ZA_ANALYTICS_ASSETS.'analytics.css'
            );
            wp_enqueue_style(
                'hwp_persian_datepicker_css'      ,
                ZA_ANALYTICS_ASSETS.'css/vendor/persian-datepicker.css'
            );
        }
    }




}
