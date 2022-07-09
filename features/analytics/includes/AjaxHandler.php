<?php


namespace Sabadino\features\analytics\includes;

class AjaxHandler
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

        add_action('wp_ajax_za_analytics_user_export' , [ $this , 'userProcessor'] );
        add_action('wp_ajax_za_search_products'       , [ $this , 'searchProducts' ]);
        add_action('wp_ajax_za_send_sms'              , [ $this , 'sendSMS'] );
        add_action('wp_ajax_za_get_the_user_list'     , [ $this , 'theUserList' ]);
        add_action('wp_ajax_za_get_sms_list'          , [ $this , 'getSMSList' ]);
        add_action('wp_ajax_za_remove_sms_list'       , [ $this , 'removeSMSList' ]);

    }




    public function searchProducts(){
        global $wpdb;
        $keyword    = sanitize_text_field(  $_POST['keyword'] );


        $sub_query = '';
        if ( isset($_POST['exclude'] ) && !empty($_POST['exclude']) && is_array( $_POST['exclude'] ) ){
            $exclude_ids = implode("','" , $_POST['exclude'] );
            $sub_query = " ID NOT IN ('$exclude_ids') AND ";
        }

        $re_query   = $wpdb->prepare("SELECT post_title , ID FROM {$wpdb->posts} WHERE ".$sub_query." post_type = 'product' AND (post_title LIKE '%$keyword%' or ID=%d) ; ",$keyword);
        $products  = $wpdb->get_results($re_query,ARRAY_A);

        $result = [];
        foreach ((array)$products as $product ){
            $result  [] = array(
                'id'    => $product['ID'] ,
                'title' => $product['post_title']
            );
        }
        if ( !empty($result) ){
            wp_send_json(  $result   , 200 );
        }else{
            wp_send_json( array('Result' => 'Not Found') , 404 );
        }

    }


    public static function userProcessor()
    {
        self::checkNonce( $_POST['nonce'] , 'za-analytics-page' );
        self::checkPermission();

        $total_from         = !empty( $_POST['purchase_total_from'] ) ? $_POST['purchase_total_from']  : null;
        $total_to           = !empty( $_POST['purchase_total_to'] )   ? $_POST['purchase_total_to']    : null;
        $order_count_min    = !empty( $_POST['order_count_min'] )     ? $_POST['order_count_min']      : null;
        $order_count_max    = !empty( $_POST['order_count_max'] )     ? $_POST['order_count_max']      : null;
        $buy_first_time     = !empty( $_POST['buy_first_time'] )      ? $_POST['buy_first_time']       : null;
        $buy_last_time      = !empty( $_POST['buy_last_time'] )       ? $_POST['buy_last_time']        : null;
        $include_products   = !empty( $_POST['include_products'] )    ? $_POST['include_products']     : null;
        $exclude_products   = !empty( $_POST['exclude_products'] )    ? $_POST['exclude_products']     : null;
        $register_from      = !empty( $_POST['from_register'] )       ? $_POST['from_register']        : null;
        $register_until     = !empty( $_POST['until_register'] )      ? $_POST['until_register']       : null;
        $without_purchase   =  $_POST['without_purchase'] == 'true';


        $all_orders         = maybe_unserialize( get_option( 'za_all_purchase_list' ,true ) );


        if ( $without_purchase ){
            foreach ( $all_orders as $order_count_k => $order_count_v ){
                if ( isset( $order_count_v['order_count'] ) ){
                    unset( $all_orders[$order_count_k] );
                }
            }
        }else{

            if (  !is_null( $total_from ) || !is_null( $total_to ) ){
                foreach ( $all_orders as $key_emount => $val_amount ){
                    if ( isset( $val_amount['line_total'] ) ){
                        $total = (int) $val_amount['line_total'];
                        if ( !is_null( $total_from ) && $total < $total_from ){
                            unset( $all_orders[$key_emount] );
                        }
                        if ( !is_null( $total_to ) && (int) $total > $total_to ){
                            unset( $all_orders[$key_emount] );
                        }
                    }else{
                        unset( $all_orders[$key_emount] );
                    }
                }
            }

            if (  !is_null( $order_count_min ) || !is_null( $order_count_max ) ){
                foreach ( $all_orders as $order_count_k => $order_count_v ){
                    if ( isset( $order_count_v['order_count'] ) ){
                        $order_count = (int) $order_count_v['order_count'];
                        if ( !is_null( $order_count_min ) &&  $order_count < $order_count_min ){
                            unset( $all_orders[$order_count_k] );
                        }
                        if ( !is_null( $order_count_max ) &&  $order_count > $order_count_max ){
                            unset( $all_orders[$order_count_k] );
                        }
                    }else{
                        unset( $all_orders[$order_count_k] );
                    }
                }
            }

            if (  !is_null( $register_from ) || !is_null( $register_until ) ){
                foreach ( $all_orders as $register_key => $register_val ){
                    if ( isset( $register_val['register_date'] ) ){
                        $register_date = $register_val['register_date'];
                        if ( !is_null( $register_from ) &&  $register_date < date('Y-m-d' , $register_from ) ){
                            unset( $all_orders[$register_key] );
                        }
                        if ( !is_null( $register_until ) &&  $register_date > date('Y-m-d' , $register_until ) ){
                            unset( $all_orders[$register_key] );
                        }
                    }else{
                        unset( $all_orders[$register_key] );
                    }
                }
            }



            if (  !is_null( $buy_first_time ) || !is_null( $buy_last_time ) ){
                foreach ( $all_orders as $buy_time_key => $buy_time_val ){
                    if ( isset( $buy_time_val['orders'] ) ){
                        if ( !is_null( $buy_first_time ) &&  $buy_time_val['orders'][$buy_time_val['order_count']]['date'] < date('Y_m_d H:i:s' , $buy_first_time ) ){
                            unset( $all_orders[$buy_time_key] );
                        }
                        if ( !is_null( $buy_last_time ) &&  $buy_time_val['orders'][1]['date'] > date('Y_m_d' , $buy_last_time ) ){
                            unset( $all_orders[$buy_time_key] );
                        }
                    }else{
                        unset( $all_orders[$buy_time_key] );
                    }
                }
            }



            if (  !is_null( $include_products ) || !is_null( $exclude_products ) ){
                foreach ( $all_orders as $products_key => $products_val ){

                    $products_items  = [];
                    foreach ( $products_val['orders'] as $key => $val  ){
                        if( isset( $val['items'] )  && is_array( $val['items'] )){
                            $products_items = array_merge( $products_items ,  array_keys( $val['items'] ) ) ;
                        }
                    }

                    if ( count( array_intersect( $include_products , $products_items ) ) <= 0 ){
                        unset( $all_orders[$products_key] );
                    }
                    if ( count( array_intersect( $exclude_products , $products_items ) ) > 0 ){
                        if ( isset( $all_orders[$products_key] )){
                            unset( $all_orders[$products_key] );
                        }
                    }
                }
            }
        }


        if ( !empty( $all_orders ) ){
            $final_user = [
                'items'         => $all_orders ,
                'description'   => '' ,
                'date'          => '' ,
                'message'       => '' ,
                'count'         => count( $all_orders ) ,
                'send_count'    => 0 ,
                'status'        => false
            ];
            $option_key  = substr( md5( microtime() ),rand( 0 ,26 ),12 );
            $key = '_za_final_user_list_'.$option_key;
            update_option( $key ,$final_user );
            wp_send_json([ 'code' => $key , 'count' => count( $all_orders ) ], 200 );
        }else{
            wp_send_json([ 'result' => 'empty' ], 404 );
        }
    }


    public static function finalUsersList()
    {
        global $wpdb;
        $user_list  = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '%_za_final_user_list_%' ");

        $finall_user = [];
        foreach ( $user_list as $item ){
            $val = maybe_unserialize( $item->option_value  );

            $finall_user[ $item->option_name  ] =  [
                'description' => $val['description'] ,
                'date'        => $val['date']        ,
                'count'       => $val['count']       ,
                'message'     => $val['message']     ,
                'status'      => $val['status']
                ];
        }

        return $finall_user;
    }


    public static function sendSMS()
    {
        self::checkNonce( $_POST['nonce'] , 'za-analytics-page' );
        self::checkPermission();

        if ( !isset( $_POST['code'] ) ) wp_send_json( ['status' => 'error'] , 500 );
        $code = $_POST['code'];
        global $wpdb;
        $the_user_list  = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name = '{$code}' ");

        $val = maybe_unserialize( $the_user_list[0]->option_value  );
        $val['description'] = isset( $_POST['description'] ) ? $_POST['description'] : '' ;
        $val['date']        = isset( $_POST['date'] )        ? date_i18n( 'Y-m-d H:i:s', $_POST['date'] ) : '' ;
        $val['message']     = isset( $_POST['message'] )     ? $_POST['message']     : '' ;
        $val['status']      = $_POST['status'] == 'true';

        update_option( $_POST['code'] , $val );
        wp_send_json( ['status' => 'ok'] , 200 );
    }


    public static function getSMSList( $ajaxcall =  true) {

        if ( $ajaxcall !== 200  ){
            self::checkNonce( $_POST['nonce'] , 'za-analytics-page' );
            self::checkPermission();
        }
        global $wpdb;
        $user_list   = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '%_za_final_user_list_%'  ");
        $success     = 0 ;
        $failed      = 0 ;
        $tr          = '';

        if ( !empty( $user_list ) ){
            foreach ( $user_list as $user_item ){
                $val = maybe_unserialize( $user_item->option_value  );

                if ( !empty( $val['items'] ) && is_array( $val['items']  ) ){
                    foreach ( $val['items'] as $list ){
                        if ( isset( $val['sms_count'] ) && $val['sms_count']  > 0 ){
                            if ( isset( $list['sms_count'] ) && $list['sms_count'] <= 0 ){
                                $failed  = $failed + 1;
                            }else{
                                $success = $success + 1;
                            }
                        }
                    }

                    $status = $val['status'] == true ? 'enable' : 'disable';

                    $tr .=
                        '<tr class="'.$status.' ">
                            <td> '.$user_item->option_name.' </td> 
                            <td> '. count( $val['items'] ).' </td>
                            <td> '.$val['message'].' </td>
                            <td> '.$val['date'] .'</td>
                            <td> '.$success.' </td>
                            <td> '.$failed.' </td>
                            <td> '.$status.' </td>
                            <td  class="remove-sms-list" data-list-name="'.$user_item->option_name.'">  
                                <svg  height="45" viewBox="0 0 74 74" width="45" xmlns="http://www.w3.org/2000/svg"  >
                                    <path d="m61.909 23.754h-49.818a3.368 3.368 0 0 1 -3.365-3.365v-6.676a3.369 3.369 0 0 1 3.365-3.366h49.818a3.369 3.369 0 0 1 3.365 3.366v6.676a3.368 3.368 0 0 1 -3.365 3.365zm-49.818-11.407a1.367 1.367 0 0 0 -1.365 1.366v6.676a1.366 1.366 0 0 0 1.365 1.365h49.818a1.366 1.366 0 0 0 1.365-1.365v-6.676a1.367 1.367 0 0 0 -1.365-1.366z"/>
                                    <path d="m53.589 72h-33.178a4.4 4.4 0 0 1 -4.373-4.085l-3-45.094a1 1 0 0 1 1-1.067h45.93a1 1 0 0 1 1 1.067l-3 45.092a4.4 4.4 0 0 1 -4.379 4.087zm-38.489-48.246 2.933 44.026a2.39 2.39 0 0 0 2.378 2.22h33.178a2.39 2.39 0 0 0 2.378-2.221l2.933-44.025z"/>
                                    <path d="m26.613 65.952a2.629 2.629 0 0 1 -2.613-2.472l-1.677-32.832a2.852 2.852 0 1 1 5.7-.268l1.225 32.837a2.628 2.628 0 0 1 -2.633 2.734zm-1.442-36.32a.855.855 0 0 0 -.852.9l1.672 32.838a.63.63 0 0 0 1.084.387.624.624 0 0 0 .173-.461l-1.226-32.84a.852.852 0 0 0 -.851-.824z"/>
                                    <path d="m37 65.952a2.642 2.642 0 0 1 -2.633-2.594l-.23-32.843a2.863 2.863 0 1 1 5.726-.007v.007l-.23 32.836a2.643 2.643 0 0 1 -2.633 2.601zm0-36.32a.87.87 0 0 0 -.863.876l.23 32.829a.633.633 0 0 0 1.266-.007l.23-32.825a.87.87 0 0 0 -.863-.873z"/>
                                    <path d="m47.387 65.952a2.637 2.637 0 0 1 -2.634-2.634c0-.012 0-.088 0-.1l1.225-32.837a2.852 2.852 0 1 1 5.7.272l-1.672 32.818a2.632 2.632 0 0 1 -2.619 2.481zm1.442-36.32a.852.852 0 0 0 -.851.825l-1.225 32.835a.629.629 0 0 0 1.256.068l1.671-32.814v-.009a.855.855 0 0 0 -.852-.9z"/>
                                    <path d="m47.314 12.347h-20.628a1 1 0 0 1 -1-1v-3.647a4.7 4.7 0 0 1 4.695-4.7h13.238a4.7 4.7 0 0 1 4.7 4.7v3.652a1 1 0 0 1 -1.005.995zm-19.628-2h18.628v-2.647a2.7 2.7 0 0 0 -2.695-2.7h-13.238a2.7 2.7 0 0 0 -2.695 2.7z"/>
                                </svg> 
                            </td>
                      </tr>';
                }
            }
        }

        if (  $ajaxcall !== 200   ){
            wp_send_json( [ 'result'=> $tr ] ,200 );
        }

        return $tr;
    }






    public static function removeSMSList()
    {
        self::checkNonce( $_POST['nonce'] , 'za-analytics-page' );
        self::checkPermission();

        if ( !isset( $_POST['code'] ) ) wp_send_json( ['status' => 'error'] , 500 );
        $code   = $_POST['code'];
        $status = delete_option( $code );
        if ( $status ){
            wp_send_json( ['result' => true ] ,200 );
        }else{
            wp_send_json( ['result' => false ],500 );
        }

    }







    public static function checkPermission()
    {
        if ( !current_user_can('administrator' ) ){
            wp_send_json_error('permission error', 403 );
        }
    }


    public static function checkNonce( $nonce  , $action )
    {
        if ( !isset( $nonce ) || empty( $nonce ) ){
            wp_send_json_error('invalid nonce', 403 );
        }else{
            if (!wp_verify_nonce( $nonce, $action)  ){
                wp_send_json_error('invalid nonce', 403 );
            }
        }
    }









}


