<?php


namespace Sabadino\includes;




use Sabadino\features\affiliate\Affiliate;
use Sabadino\features\order_delivery\Delivery;
use Sabadino\features\referral\Referral;

class Register
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

        register_activation_hook( ZA_REGISTER_ROOT   , [ $this , 'activate' ]);
        register_deactivation_hook( ZA_REGISTER_ROOT , [ $this , 'deactivation' ]);
    }

    public function activate()
    {
        Cron::schedule();
        Delivery::registration();
        Referral::get_instance()::re_create_referral_db_table();
        Affiliate::get_instance()::affiliateActivation();
    }


    public function deactivation()
    {
        Cron::deactivateCron();
    }






}