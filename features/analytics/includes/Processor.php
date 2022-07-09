<?php


namespace Sabadino\features\analytics\includes;


class Processor
{

    protected static $orders;

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
        self::fiveProductsKeys();
        self::getAllOrders();
    }

    public static function newUsersCounter()
    {
        global $wpdb;
        $last_year = date('Y-m-d' , strtotime('-1 year') );
        $users  = $wpdb->get_results(
            " SELECT CONCAT( UNIX_TIMESTAMP( date( user_registered) ) , '000' ) AS d_key , COUNT( ID ) AS d_val 
                     FROM {$wpdb->users} where user_registered > '{$last_year}'
                       GROUP BY CONCAT( UNIX_TIMESTAMP(date(user_registered ) ) , '000' ); "
        );
        $final_new_users = [];
        foreach ( $users as $item ){
            $final_new_users[] = [ (int) $item->d_key , (int) $item->d_val ];
        }
        if ( !empty( $final_new_users ) ){
            return $final_new_users;
        }else{
            return [];
        }
    }


    public static function dailyUserRegister()
    {

        global $wpdb;
        $last_week = date('Y-m-d' , strtotime('-7 days') );
        $row_users  = $wpdb->get_results(
            " SELECT SUBSTRING(user_registered,1,10)  AS d_key , COUNT( ID ) AS d_val 
                     FROM {$wpdb->users} where user_registered > '{$last_week}'
                       GROUP BY SUBSTRING(user_registered,1,10)  ; "
        );
        $today       =  date('Y-m-d' , strtotime( 'now' ) );
        $yesterday   =  date('Y-m-d' , strtotime('-1 day' ) );
        $b_yesterday =  date('Y-m-d' , strtotime('-2 days' ) );



        $final_users = [ 'today' => 0 , 'yesterday' => 0 , 'b_yesterday'=> 0 ,'week' => 0  ];


        foreach ( $row_users as $user ){

            if ( $user->d_key >= $last_week ){
                if (  $user->d_key == $today ){
                    $final_users['today'] = $user->d_val;
                }
                if (  $user->d_key == $yesterday ){
                    $final_users['yesterday'] = $user->d_val;
                }
                if (  $user->d_key == $b_yesterday ){
                    $final_users['b_yesterday'] = $user->d_val;
                }

                $final_users['week'] = $final_users['week'] + $user->d_val;
            }
        }

        return !empty( $final_users ) ? $final_users : [];
    }


    public static function getAllOrders():void
    {
        global $wpdb;

        $post             = $wpdb->posts;
        $order_table      = $wpdb->prefix.'woocommerce_order_items';
        $order_meta_table = $wpdb->prefix.'woocommerce_order_itemmeta';

        $orders  = $wpdb->get_results(
            " SELECT order_t.order_item_id as id ,DATE_FORMAT(post.post_modified, '%Y-%m-%d') as date  , order_t_meta.* FROM {$post} AS post
                  INNER JOIN {$order_table} AS  order_t ON order_t.order_id = post.ID 
                    LEFT JOIN   {$order_meta_table} AS order_t_meta ON order_t.order_item_id = order_t_meta.order_item_id 
                      AND (order_t_meta.meta_key = '_product_id' OR order_t_meta.meta_key = '_line_total') 
                    WHERE post_type ='shop_order' AND post_status = 'wc-completed' AND 
                       post_date > DATE_SUB(NOW(), INTERVAL 1 MONTH ) "
        );
        $final_orders = [];
        foreach ( $orders as $order ){
            $final_orders[ $order->order_item_id ][$order->meta_key] = $order->meta_value;
            $final_orders[ $order->order_item_id ]['date'] = $order->date;
        }
        self::$orders =  $final_orders;
    }


    public static function fiveProducts()
    {
        $final_orders  = self::$orders;
        $five_products = self::fiveProductsKeys();

        $final_data= [
            date_i18n('Y_m_d' , strtotime('now')  )    => $five_products ,
            date_i18n('Y_m_d' , strtotime("-1 day")  ) => $five_products ,
            date_i18n('Y_m_d' , strtotime("-2 days") ) => $five_products ,
            date_i18n('Y_m_d' , strtotime("-3 days") ) => $five_products ,
            date_i18n('Y_m_d' , strtotime("-4 days") ) => $five_products ,
            date_i18n('Y_m_d' , strtotime("-5 days") ) => $five_products ,
            date_i18n('Y_m_d' , strtotime("-6 days") ) => $five_products
        ];


        foreach ( $final_orders as $order ){
            if ( isset( $order['_line_total'] ) &&  isset( $order['_product_id'] ) &&
                 isset( $order['date']) && $order['_line_total'] > 0  &&
                 array_key_exists( $order['_product_id'] ,$five_products ) &&
                 array_key_exists( date_i18n('Y_m_d' , strtotime($order['date'])) ,$final_data ) ){
                 $final_data[ date_i18n('Y_m_d' , strtotime($order['date'])) ][$order['_product_id']] =
                     (int) $final_data[  date_i18n('Y_m_d' , strtotime($order['date'])) ][$order['_product_id']] + $order['_line_total'];
            }
        }
        arsort($final_data);
        return !empty( $final_data ) ? $final_data : [];
    }


    public static function totalSales()
    {

        $row_orders   = self::$orders;
        $final_orders = [];

        $orders = [];
        foreach ( $row_orders as $order ){


            if( isset( $order['_line_total'] ) && isset( $order['_product_id'] ) && $order['_line_total'] > 0 ){
                if( isset( $orders[ $order['_product_id'] ]  )){
                    $orders[ $order['_product_id'] ] = (int) $orders[ $order['_product_id'] ] + $order['_line_total'];
                }else{
                    $orders[ $order['_product_id'] ] = $order['_line_total'];
                }
            }
        }
        arsort($orders );
        $orders = array_slice( $orders, 0, 6, true );


        foreach ( $orders as $key => $val  ){
            $final_orders [] = [get_post( $key )->post_title => $val ];
        }
        return !empty( $final_orders ) ? $final_orders : [];
    }


    public static function lastMonthSales()
    {
        $row_orders   = self::$orders;
        $final_orders = [];


        $last_month = date_i18n('Y_m_d' , strtotime( '-30 days '));
        foreach ( $row_orders as $order ){
            if ( isset($order['date']) && isset($order['_line_total'])){
                if ( date_i18n('Y_m_d' , strtotime($order['date']) ) > $last_month ){
                    if( isset( $final_orders[  date_i18n('Y_m_d' , strtotime($order['date']) ) ]  )){
                        $final_orders[  date_i18n('Y_m_d' , strtotime($order['date']) ) ] =
                            (int) $final_orders[ date_i18n('Y_m_d' , strtotime($order['date']) )] + $order['_line_total'];
                    }else{
                        $final_orders[  date_i18n('Y_m_d' , strtotime($order['date']) ) ] =  $order['_line_total'];
                    }
                }
            }
        }


        $final_orders = [
            'keys'   => array_keys( $final_orders ) ,
            'values' => array_values( $final_orders )
        ];

        return !empty( $final_orders ) ? $final_orders : [];
    }


    public static function specificProduct()
    {
        $row_orders   = self::$orders;
        $final_orders = self::coursesKeys();

        $last_month = date_i18n('Y-m-d' , strtotime( '-30 days '));
        foreach ( $row_orders as $order ){
            if ( isset( $order['date']) && isset( $order['_line_total']) && isset( $order['_product_id']) ){
                if ( date_i18n('Y-m-d' , strtotime($order['date']) ) > $last_month ){
                    if( isset( $final_orders[$order['_product_id']] [ date_i18n('Y-m-d' , strtotime($order['date']) ) ] )){
                        $final_orders[$order['_product_id']] [ date_i18n('Y-m-d' , strtotime($order['date']) ) ] =
                            (int) $final_orders[$order['_product_id']] [ date_i18n('Y-m-d' , strtotime($order['date']) ) ] + $order['_line_total'];
                    }else{
                        $final_orders[$order['_product_id']] [ date_i18n('Y-m-d' , strtotime($order['date']) ) ] = $order['_line_total'];
                    }
                }
            }
        }
        return !empty( $final_orders ) ? $final_orders : [];
    }


    public static function salesItems()
    {
        $row_orders   = self::$orders;

        $yesterday   = date_i18n('Y-m-d' , strtotime( '-1 day'));
        $this_month  = date_i18n('Y-m-d' , strtotime( '-30 days'));
        $this_year   = date_i18n('Y-m-d' , strtotime( '-1 year'));
        $yes_total   = 0;
        $month_total = 0;
        $year_total  = 0;
        foreach ( $row_orders as $order ){
            if ( isset( $order['date']) && isset( $order['_line_total']) ){
                if ( date_i18n('Y-m-d' , strtotime($order['date']) ) == $yesterday ){
                    $yes_total += $order['_line_total'];
                }
                if ( date_i18n('Y-m-d' , strtotime($order['date']) ) > $this_month ){
                    $month_total += $order['_line_total'];
                }
                if ( date_i18n('Y-m-d' , strtotime($order['date']) ) > $this_year ){
                    $year_total += $order['_line_total'];
                }
            }
        }

        return !empty( $final_orders ) ? $final_orders : [];
    }




    public static function fiveProductsKeys( $call = false )
    {
        global $wpdb;
        $post_meta_table  = $wpdb->postmeta;
        $posts_table      = $wpdb->posts;
        $five_products    = [];
        $products  = $wpdb->get_results(
            "SELECT DISTINCT post_id AS id ,posts.post_title AS title  FROM {$post_meta_table} AS meta 
                INNER JOIN {$posts_table} AS posts ON posts.ID = meta.post_id 
                WHERE meta_key = '_product_show_order' AND meta_value > 0 AND  posts.post_status ='publish'
                ORDER BY meta_value DESC LIMIT 5 ;"
        );
        if ( $call ){
            foreach ( $products as $product ){
                $five_products[$product->id] = $product->title;
            }
        }else{
            foreach ( $products as $product ){
                $five_products[$product->id] = 0;
            }
        }


        return $five_products;
    }



    public static function coursesKeys( $course_name = false )
    {
        global $wpdb;
        $posts_table      = $wpdb->posts;
        $post_meta_table  = $wpdb->postmeta;
        $monthKeys     = self::monthKeys();
        $final_courses = [];
        $final_product = [];
        $products  = $wpdb->get_results(
            "SELECT DISTINCT post_id AS id ,posts.post_title AS title  FROM {$post_meta_table} AS meta 
                INNER JOIN {$posts_table} AS posts ON posts.ID = meta.post_id 
                WHERE meta_key = '_product_show_order' AND meta_value > 50 AND  posts.post_status ='publish'
                ORDER BY meta_value DESC LIMIT 5 ;"
        );

        foreach ( $products as $product ){
            $final_courses[$product->id] = $monthKeys;
            $final_product[$product->id] = $product->title;
        }
        return $course_name ? $final_product : $final_courses;
    }



    public static function monthKeys( $days = 30)
    {
        $keys = [];
        $keys[ date_i18n('Y-m-d' , strtotime('now'))] = 0   ;
        $keys[ date_i18n('Y-m-d' , strtotime('-1 day'))] = 0   ;

        for( $i=2; $i < $days; $i++ ){
            $keys[ date_i18n('Y-m-d' , strtotime('-'.$i.' days')) ] = 0  ;
        }

        return $keys;
    }


    public static function dailySales()
    {

        $today       = date_i18n('Y_m_d' , strtotime( 'now' ) );
        $yesterday   = date_i18n('Y_m_d' , strtotime('-1 day' ));
        $b_yesterday = date_i18n('Y_m_d' , strtotime('-2 days' ));
        $this_week   = date_i18n('Y_m_d' , strtotime('-7 days' ));

        $row_orders   = self::$orders;
        $final_orders = [ 'today' => 0 , 'yesterday' => 0 , 'b_yesterday'=> 0 ,'week' => 0  ];


        foreach ( $row_orders as $order ){

            if ( isset($order['date']) &&
                 isset($order['_line_total']) &&
                 date_i18n('Y_m_d' , strtotime($order['date']) ) >= $this_week ){

                if ( date_i18n('Y_m_d' , strtotime($order['date']) ) == $today ){
                    $final_orders['today'] = (int) $final_orders['today'] + $order['_line_total'];
                }

                if ( date_i18n('Y_m_d' , strtotime($order['date']) ) == $yesterday ){
                    $final_orders['yesterday'] =
                        (int) $final_orders['yesterday'] + $order['_line_total'];
                }

                if ( date_i18n('Y_m_d' , strtotime($order['date']) ) == $b_yesterday ){
                    $final_orders['b_yesterday'] =
                        (int) $final_orders['b_yesterday'] + $order['_line_total'];
                }
                $final_orders['week'] =
                    (int) $final_orders['week'] + $order['_line_total'];

            }
        }

        return !empty( $final_orders ) ? $final_orders : [];
    }







}