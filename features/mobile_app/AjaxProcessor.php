<?php


namespace Sabadino\features\mobile_app;

class AjaxProcessor
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
        add_action('wp_ajax_re_and_app_slider', [$this, 're_and_app_slider']);
        add_action('wp_ajax_re_and_app_banner', [$this, 're_and_app_banner']);
        add_action('wp_ajax_re_set_discount_time', [$this, 're_set_discount_time']);
        add_action('wp_ajax_re_minimum_purchase_amount', [$this, 're_minimum_purchase_amount']);
        add_action('wp_ajax_re_display_product_count', [$this, 're_display_product_count']);
    }



    public function re_and_app_slider()
    {
        $data = $_POST['sliders'];
        update_option('re_and_app_slider', $data);
        exit();
    }


    public function re_and_app_banner()
    {
        $data = $_POST['banners'];
        update_option('re_and_app_banner', $data);
        exit();
    }


    public function re_set_discount_time()
    {
        $discount_date = $_POST['discount_date'];
        $discount_time = $_POST['discount_time'];
        update_option('re_set_discount_time', $discount_date . '|' . $discount_time);
        exit();
    }


    public function re_minimum_purchase_amount()
    {
        $purchase_min = $_POST['purchase_min'];
        update_option('re_minimum_purchase_amount', $purchase_min);
        exit();
    }


    public function  re_display_product_count()
    {
        $product_count = $_POST['product_count'];
        update_option('re_display_product_count', $product_count);
        exit();
    }

}



 