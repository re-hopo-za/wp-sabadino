<?php


namespace Sabadino\features\analytics\includes;



class PurchaseProcessor
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

    }

    public static function run()
    {

        $time = date_i18n('G' );
        if ( $time > 2 && $time <= 5 ){
            self::listChecker();
        }
    }


    public static function listChecker()
    {
        $today = (int) get_option( 'za_today_list'.date_i18n('d' ) );

        if ( !empty( $today ) ){
            if (  $today > 404 ){
                self::getUsers( (int) get_option(  'za_current_user' , false  ) );
            }
        }else{
            add_option( 'za_today_list'.date_i18n('d' ) ,time() );
            update_option('za_all_purchase_list' , '' );
            update_option('za_current_user' , 0 );
            delete_option( 'za_today_list'.date_i18n('d' ,strtotime('-1 day') ) );
            self::getUsers( 0 );
        }
    }




    public static function getUsers( $specif_user )
    {

        global $wpdb;
        $final_users =  [];


        $users  = $wpdb->get_results(
            "SELECT ID as user_id  ,DATE_FORMAT( user_registered , '%Y-%m-%d') as register ,
                   meta1.meta_value as f_name , meta2.meta_value as l_name  FROM {$wpdb->users} as user_t
                   INNER JOIN {$wpdb->usermeta} meta1 ON meta1.user_id = user_t.ID AND meta1.meta_key = 'first_name' 
                   INNER JOIN {$wpdb->usermeta} meta2 ON meta2.user_id = user_t.ID AND meta2.meta_key = 'last_name'
                   ORDER BY user_t.ID ASC LIMIT  {$specif_user}  , 500 ;"
        );

        if ( !empty( $users ) ) {
            foreach ( $users as $user ){
                $final_users[ $user->user_id ] = [ 'register_date' => $user->register ,'f_name' => $user->f_name  ,'l_name' => $user->l_name  ] ;
            }
            self::getOrders( $final_users );
        }else{
            update_option( 'za_today_list'.date_i18n('d' )  , 404 );
        }
    }

    public static function getOrders( $users )
    {

        if ( !empty( $users ) ) {

            $all_users_orders = [];
            foreach ( $users as $user => $details ) {

                $orders_args = array(
                    'status'         => 'wc-completed',
                    'type'           => 'shop_order',
                    'customer_id'    => (int) $user,
                    'posts_per_page' => -1
                );
                $orders = wc_get_orders( $orders_args );

                $user_orders = [];
                if ($orders) {
                    $order_index = 1;
                    foreach ($orders as $order) {
                        $items = $order->get_items();
                        $orderID = $order->get_id();

                        if (!isset($user_orders['sms_count'])) {
                            $user_orders['sms_count'] = 0;
                        }

                        if (!isset($user_orders['sms_try'])) {
                            $user_orders['sms_try'] = 0;
                        }

                        if (!isset($user_orders['order_count'])) {
                            $user_orders['order_count'] = 1;
                        } else {
                            $user_orders['order_count'] =
                                $user_orders['order_count'] + 1;
                        }

                        if (!isset($user_orders['line_total'])) {
                            $user_orders['line_total'] = (int)$order->get_total();
                        } else {
                            $user_orders['line_total'] =
                                $user_orders['line_total'] + (int)$order->get_total();
                        }

                        if (!isset($user_orders['register_date'])) {
                            $user_orders['register_date'] = $details['register_date'];
                        }

                        if (!isset($user_orders['first_name'])) {
                            $user_orders['first_name'] = $details['f_name'];
                        }

                        if (!isset($user_orders['last_name'])) {
                            $user_orders['last_name'] = $details['l_name'];
                        }

                        if (!isset($user_orders ['orders'][$order_index])) {
                            $user_orders ['orders'][$order_index] = [];
                        }

                        if (!array_key_exists($order_index, $user_orders['orders'])) {
                            $user_orders['orders'][$order_index] = [];
                        }

                        if (!isset($user_orders['phone'])) {
                            $user_orders['phone'] = $order->get_billing_phone();
                        }

                        if (!isset($user_orders[$orderID]['date'])) {
                            $user_orders['orders'][$order_index]['date'] = $order->get_date_created()->date('Y-m-d H:i:s');
                        }

                        $user_orders['orders'][$order_index]['items'] = [];
                        foreach ($items as $item) {
                            if (!array_key_exists($item->get_product_id(), $user_orders['orders'][$order_index]['items'])) {
                                $user_orders['orders'][$order_index]['items'][$item->get_product_id()] = $item->get_quantity();
                            }
                        }
                        $order_index++;
                    }

                } else {
                    $user_orders['phone'] = get_user_meta( $user, 'billing_phone' ,true );
                    $user_orders['register_date'] = $details['register_date'];
                    $user_orders['first_name'] = $details['f_name'];
                    $user_orders['last_name']  = $details['l_name'];
                    $user_orders['sms_count']  = 0;
                    $user_orders['sms_try']    = 0;
                }
                $all_users_orders[$user] = $user_orders;
            }

            if ( !empty( $all_users_orders ) ){
                $all_orders = (array) maybe_unserialize( get_option( 'za_all_purchase_list' ) );
                $all_orders = !empty($all_orders) ? array_merge( $all_orders ,$all_users_orders ) : $all_users_orders;
                update_option('za_current_user',  count($all_orders) );
                update_option('za_all_purchase_list', $all_orders  );
            }
        }
    }



}

