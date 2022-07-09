<?php

namespace Sabadino\includes;





use Sabadino\features\affiliate\Affiliate;
use Sabadino\features\analytics\Analytics;
use Sabadino\features\delivering_time\DeliveringTime;
use Sabadino\features\live_search\LiveSearch;
use Sabadino\features\mobile_app\MobileApp;
use Sabadino\features\order_delivery\Delivery;
use Sabadino\features\orders_on_excell\ExportOrders;
use Sabadino\features\product_slider\ProductsSlider;
use Sabadino\features\referral\Referral;
use Sabadino\features\registration\Registration;
use Sabadino\features\woocommerce\Woocommerce;
use Sabadino\features\cart\Cart;


class Loader
{

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {
        LiveSearch::get_instance();
        ProductsSlider::get_instance();
        Enqueues::get_instance();
        Functions::get_instance();
        Woocommerce::get_instance();
        Registration::get_instance();
        Analytics::get_instance();
        Delivery::get_instance();
        DeliveringTime::get_instance();
        MobileApp::get_instance();
//        Referral::get_instance();
//        Affiliate::get_instance();
        ExportOrders::get_instance();
        Cart::get_instance();
    }

}