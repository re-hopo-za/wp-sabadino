<?php


namespace Sabadino\includes;


class Cron
{


    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    public function __construct() {

    }


    public static function schedule() {

        if (!wp_next_scheduled ( 'za_send_sms_handler' )) {
            wp_schedule_event( time(), 'every_5_minutes', 'za_send_sms_handler');
        }
        if (!wp_next_scheduled ( 'za_process_user_purchase_list' )) {
            wp_schedule_event( time(), 'every_10_minutes', 'za_process_user_purchase_list');
        }

    }


    public function customInterval( $schedules ){
        if( !isset( $schedules["every_5_minutes"] ) ){
            $schedules["every_5_minutes"] = [
                'interval' => 5 * 60 ,
                'display'  => __('Once every 5 minutes')
            ];
        }
        if( !isset( $schedules["every_10_minutes"] ) ){
            $schedules["every_10_minutes"] = [
                'interval' => 10 * 60 ,
                'display'  => __('Once every 10 minutes')
            ];
        }
        return $schedules;
    }


    public static function deactivateCron(){
        wp_clear_scheduled_hook("za_process_user_purchase_list");
        wp_clear_scheduled_hook("za_send_sms_handler");
    }


}