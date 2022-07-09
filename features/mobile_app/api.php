<?php



function convertNumber($srting, $toPersian=true)
{
    $en_num = array('0','1','2','3','4','5','6','7','8','9');
    $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    if( $toPersian )
        return str_replace($en_num, $fa_num, $srting);
    else
        return str_replace($fa_num, $en_num, $srting);
}



add_filter( 'rest_endpoints', function( $endpoints ){
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});


add_action('rest_api_init' , 're_rests_routs');
function re_rests_routs(){
    $productEndpoint  = 'products';
    $viewEndpoint     = 'views';
    $userEndpoint     = 'users';
    $settingEndpoint  = 'setting';
    $reviewEndpoint   = 'review';
    $cartEndpoint     = 'cart';
    $refEndpoint      = 'referral';
    $paymentEndpoint  = 'payment';
    $version    = 'v1';
    $app        = 're';

    $namespace_product  = $app.'/'.$version."/".$productEndpoint ;
    $namespace_view     = $app.'/'.$version."/".$viewEndpoint ;
    $namespace_user     = $app.'/'.$version."/".$userEndpoint ;
    $namespace_setting  = $app.'/'.$version."/".$settingEndpoint ;
    $namespace_review   = $app.'/'.$version."/".$reviewEndpoint ;
    $namespace_payment  = $app.'/'.$version."/".$paymentEndpoint ;
    $namespace_cart     = $app.'/'.$version."/".$cartEndpoint ;
    $namespace_ref      = $app.'/'.$version."/".$refEndpoint ;



//// Comment
    register_rest_route($namespace_review, '/comments', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 're_get_comments' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_review, '/add_comment', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 're_add_comment' ,
        'permission_callback' => '__return_true',
    ));

/////Cart


    register_rest_route($namespace_cart, 'create_cart_token', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 're_create_cart_token' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_cart, 'check_referral', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 're_check_referral' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_cart, 'check_coupon', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 're_check_coupon',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_cart, 'payment', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 're_payment',
        'permission_callback' => '__return_true',
    ));




////  View
    register_rest_route( $namespace_view, '/get_sliders/' , array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 're_get_sliders' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_view , '/get_search_result/' , array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 're_get_search_result',
        'permission_callback' => '__return_true',
    ));


    register_rest_route($namespace_view , '/get_main_banner/' , array(
        'methods'   =>  WP_REST_Server::READABLE ,
        'callback'  => 're_get_main_banner',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_view , '/get_delivery_times/' , array(
        'methods'   =>  WP_REST_Server::READABLE ,
        'callback'  => 're_get_delivery_times',
        'permission_callback' => '__return_true',
    ));



/// Order List
    register_rest_route($namespace_product , 'get_downToUp_product' , array(
        'methods'  => WP_REST_Server::READABLE ,
        'callback' => 're_get_downToUp_product',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_product , 'get_upToDown_product' , array(
        'methods'  => WP_REST_Server::READABLE ,
        'callback' => 're_get_upToDown_product',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_best_sells/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_best_sells',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product, '/get_new_products/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_new_products',
        'permission_callback' => '__return_true',
    ));




///// Product

    register_rest_route($namespace_product , 'get_offers' , array(
        'methods'  => WP_REST_Server::READABLE ,
        'callback' => 're_get_offers',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product, '/get_discount_products' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_discount_products',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_all_products/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_all_products',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_product_by_cats/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_product_by_cats',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_home_page_products/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_home_page_products',
        'permission_callback' => '__return_true',
    ));

/// Order
    register_rest_route($namespace_product , '/add_order/' , array(
        'methods' => WP_REST_Server::CREATABLE ,
        'callback' => 're_add_order',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_product , '/get_orders/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_orders',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_product , '/get_order_single/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_order_single',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_ordered_products/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_ordered_products',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_product , '/get_product_by_attr/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_product_by_attr',
        'permission_callback' => '__return_true',
    ));


    register_rest_route( $namespace_product , '/get_all_product_attr/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_all_product_attr',
        'permission_callback' => '__return_true',
    ));



////  User
    register_rest_route( $namespace_user , '/login_or_register' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_login_or_register',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_user , '/get_user_data/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_user_data',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_user , '/create_user/' , array(
        'methods'  =>  WP_REST_Server::CREATABLE  ,
        'callback' => 're_create_user' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_user , '/update_user/' , array(
        'methods'  =>  WP_REST_Server::EDITABLE  ,
        'callback' => 're_update_user' ,
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_user , '/referral_gift_checker/' , array(
        'methods'  =>  WP_REST_Server::READABLE  ,
        'callback' => 're_referral_gift_checker' ,
        'permission_callback' => '__return_true',
    ));
    ///Favorite

    register_rest_route($namespace_user , '/add_favorite/' , array(
        'methods' => WP_REST_Server::CREATABLE ,
        'callback' => 're_add_favorite',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_user , '/remove_favorite/' , array(
        'methods' => WP_REST_Server::DELETABLE ,
        'callback' => 're_remove_favorite',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_product , '/get_favorite_products/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_favorite_products',
        'permission_callback' => '__return_true',
    ));

    ///Referral

    register_rest_route( $namespace_ref , '/set_introduce_code/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_set_introduce_code',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_ref , '/get_referral_gifts/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_referral_gifts',
        'permission_callback' => '__return_true',
    ));

    register_rest_route( $namespace_ref , '/get_referral_details/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_referral_details',
        'permission_callback' => '__return_true',
    ));


    /// Settings

    register_rest_route($namespace_setting , '/get_limit_cart/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_limit_cart',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_setting , '/get_discount_timer/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_discount_timer',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_setting , '/get_optional_update/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_optional_update',
        'permission_callback' => '__return_true',
    ));

    register_rest_route($namespace_setting , '/get_required_update/' , array(
        'methods' => WP_REST_Server::READABLE ,
        'callback' => 're_get_required_update',
        'permission_callback' => '__return_true',
    ));




}




///Referral
function re_set_introduce_code($request){
    $request    = $request->get_params();
    $user_id    = $request['user_id'];
    $referral   = $request['referral_code'];
    $full_name  = $request['full_name'];

    global $wpdb;
    $table_name = $wpdb->prefix . "re_referral";

    $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE self_referral= $referral  ;");
    if ($retrieve_data[0]->id > 0){

        $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id= $user_id ;");

        if ($retrieve_data != null){

            if ($retrieve_data[0]->parent_referral == null){
                $data = ['parent_referral' => $_POST['referral_code'] , 'full_name'  =>  $full_name ];
                $where = ['user_id' => $user_id];
                $format = ['%d' , '%s'];
                $where_format = ['%d'];
                $wpdb->update($table_name, $data, $where, $format, $where_format);
                $result =  'Recorded';
            }else{
                $data = [  'full_name'  =>  $full_name  ];
                $where = ['user_id' => $user_id];
                $format = [ '%s'];
                $where_format = ['%d'];
                $wpdb->update($table_name, $data, $where, $format, $where_format);
                $result = 'Referral Code Already Recorded';
            }


        }else{
            $phone = get_userdata($user_id);
            $data  = [
                'user_id'        =>  $user_id ,
                'phone'          =>  $phone->user_login,
                'date_register'  =>  jdate('Y-m-d H:i:s'),
                'full_name'      =>  $full_name ,
                'purchase_ids'   =>  serialize([]),
                'self_referral'  =>  rand( 1000 , 9999 ) . substr( $user_id ,-1  ,  1) ,
                'parent_referral' => $referral
            ];
            $format = [ '%d' ,'%s' ,'%s'  ,'%s'  ,'%s' ,'%d'  ,'%d' ];
            $wpdb->insert( $table_name , $data , $format );
            $result = 'Recorded';
        }

    }else{
        $result = 'Code is Wrong';
    }


    return new WP_REST_Response( array( 'Response' => $result ) , 200);

}



function re_get_referral_gifts($request){
    $request  = $request->get_params() ;
    $user_id  = $request['user_id'];
    $Gifts=[];
    global $wpdb;
    $table_name = $wpdb->prefix . "re_referral";


    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

    $re_ref_gifts = unserialize( get_option('re_ref_cal_gifts'  , false ) );
    if ( $retrieve_data[0]->score >= $re_ref_gifts['roof'] ){
        $which_gifts = $re_ref_gifts['status'];

        if( $which_gifts == 1){
            $Gifts = [
                'status' => 'percent' ,
                'amount' =>  $amount = $re_ref_gifts['amount']
            ];



        }elseif ($which_gifts == 2){
            $Gifts = [
                'status' => 'fixed_cart' ,
                'amount' =>  $amount = $re_ref_gifts['amount']
            ];


        }elseif($which_gifts==3) {

            $Gifts = [
                'status' => 'product' ,
                'amount' =>  $amount = $re_ref_gifts['amount']
            ];
        }
    }else{
        $Gifts = [
            'status' => null ,
            'amount' => null
        ];
    }

    return new WP_REST_Response(array('Response' =>$Gifts ) , 200);
}



function re_get_referral_details($request){
    $request  = $request->get_params() ;
    $user_id  = $request['user_id'];
    global $wpdb;
    $table_name = $wpdb->prefix . "re_referral";


    $retrieve_data  = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );
    $retrieve_child = $wpdb->get_results( "SELECT * FROM $table_name WHERE parent_id={$user_id};" );


    $details = array(
        'parent_id'      => $retrieve_data[0]->parent_id ,
        'self_referral'  => $retrieve_data[0]->self_referral ,
        'score'          => $retrieve_data[0]->score ,
        'remaining'      => $retrieve_data[0]->remaining ,
        'child_count'    => count($retrieve_child) ,
    );


    return new WP_REST_Response(array('Response' => $details ) , 200);
}



function re_payment($params){
    $params   = $params->get_params();
    $payment  = $params['payment'];
    $token    = $params['token'];
    $time     = $params['time'];
    $locSo    = $params['location'];

    global $wpdb; 
    $cart_table_name = $wpdb->prefix .'re_cart';



    $location = array(
        "lat" => explode('*'  , $locSo )[0] ,
        "lng" => explode('*'  , $locSo )[1]
    );

    $result = $wpdb->get_results("SELECT * FROM {$cart_table_name} WHERE token='{$token}'");
    $total = (int) $result[0]->total;
    if ($payment == 0){
        $wpdb->update( $cart_table_name ,
            array(
                'pay'           => 2 ,
                're_location' =>  serialize($location) ,
                'time' =>$time
            ) ,
            array(
                'token'       =>   $token
            )
        ); 

        $user_id    = (int) $result[0]->user_id; 

        $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS"; 

        $userData  = get_user_by('id' ,  $user_id );

        if(!empty($userData)) {

            $userPhone  = get_user_meta( $user_id, 'billing_phone', true);
            $order_note = get_user_meta( $user_id, 'order_note', true);
            $addr       = get_user_meta( $user_id, 'billing_address_1', true);

            $address = array(
                'first_name' => $userData->first_name,
                'last_name' => $userData->last_name,
                'email' => $userData->user_email,
                'phone' => $userPhone,
                'address_1' => $addr
            );

            $order = wc_create_order( array( 'customer_id' => $user_id ) );

            $order_id = $order->get_id();
            $detailsStatus = true;
            try {
                $order->set_payment_method('cod');
                $order->set_payment_method_title('پرداخت در محل پس از تحویل');
                $order->add_order_note( $order_note ,$user_id , true );
                $order->set_customer_note( $order_note );
                $order->set_address( $address, 'billing');
                $order->set_created_via('pragmatically');

            } catch (Exception $e) {
                $detailsStatus = false;
            }

            if ($detailsStatus === true) {
                
                $order->update_status("processing", 'Imported order', true); 
                $products = unserialize( $result[0]->products );

                foreach ($products as $id => $count) { 
                    try {
                        $order->add_product( wc_get_product( (int) $id),    $count);

                    } catch (WC_Data_Exception $e) {
                    }
                    if(wc_get_product($id)->get_manage_stock()){
                        wc_update_product_stock( $id , wc_get_product($id)->get_stock_quantity() - $count );

                    } 
                } 
                $order->calculate_totals(); 
                update_post_meta( $order_id, 'daypart', $time);
                update_post_meta( $order_id, '_order_shipping_location', $location ); 
                $productName = array();


                foreach ($order->get_items() as $item_id => $item_data) {
                    $productName[] = "- ".$item_data->get_name()." * ".$item_data->get_quantity()."
";
                }

                $total = convertNumber( number_format($order->get_total()) );
                $pName = implode(" ", $productName);
  
                $text = sprintf('سلام %s  
سفارش شما به شماره %s ثبت شد
آیتم های سفارش : %s 
مبلغ سفارش : %s  تومان
زمان ارسال %s', $userData->first_name , $order->get_id() , $pName , $total , $time );



                $text_admin = sprintf('سفارش  %s به شماره %s ثبت شد
آیتم های سفارش : %s 
مبلغ سفارش : %s  تومان
از اپلیکیشن در محل
زمان ارسال : %s' , $userData->first_name , $order->get_id() , $pName , $total  , $time);

                $fromDate = explode('|' , $time);
                $time  =  explode('_'  , $fromDate[0])[0].":00:00";
                $date  = explode('-' , $fromDate[1] );
                $date  = jalali_to_gregorian( $date[0] , $date[1] , $date[2] , '-' );
                $value =  strtotime($date.' '.$time ) ;
                update_post_meta( $order_id, 're_micro_time', $value);

  

                if ($order->save()) {
                    $table_name = $wpdb->prefix . "re_referral";
                    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

                    $ref_calculate = unserialize( get_option('re_ref_set_calculate'  , true ));

                    if ($ref_calculate['status'] != 1) {

                        $parent_id = $retrieve_data[0]->parent_id;

                        if ($parent_id > 0){
                            $purchase_ids = unserialize( $retrieve_data[0]->purchase_ids );
                            $child_purchase_count = count($purchase_ids) ;
                            $retrieve_data_for_parent = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$parent_id};" );

                            if ($ref_calculate['status'] == 2) {

                                if ($child_purchase_count <= (int) $ref_calculate['ref_term_count']) {

                                    if ($ref_calculate['ref_term_type'] == 'p') {
                                        $income = ( (int) $ref_calculate['ref_term_amount'] * (int) $order->get_total() ) / 100;
                                    } else {
                                        $income = (int) $ref_calculate['ref_term_amount'];
                                    }

                                    $pa_data =[
                                        'score'     => $retrieve_data_for_parent[0]->score + $income ,
                                        'score_ids' => $retrieve_data_for_parent[0]->score_ids . ','.$order->get_id().'_'.$user_id
                                    ];


                                    $purchase_ids [] =  $order->get_id()  ;
                                    $purchase_ids =  serialize($purchase_ids);
                                    $data = [
                                        'purchase_ids'   =>  $purchase_ids
                                    ];
                                }

                            } elseif ($ref_calculate['status'] == 3) {
                                foreach ($ref_calculate['data'] as $item) {

                                    if (   $item['termnumber'] == $child_purchase_count + 1  ) {
                                        if ($item['termtype'] == 'percent') {
                                            $income = ( (int) $item['termvalue'] * (int) $order->get_total()) / 100;
                                        } else {
                                            $income = (int)$item['termvalue'];
                                        }
                                        $pa_data =[
                                            'score'      => $retrieve_data_for_parent[0]->score + $income ,
                                            'score_ids' => $retrieve_data_for_parent[0]->score_ids . ','.$order->get_id().'_'.$user_id
                                        ];

                                        $purchase_ids [] =  $order->get_id()  ;
                                        $purchase_ids =  serialize($purchase_ids);
                                        $data = [
                                            'purchase_ids'   =>  $purchase_ids
                                        ];
                                    }
                                }
                            }

                            $where = ['id' => $retrieve_data[0]->id];
                            $format = ['%s'];
                            $where_format = ['%d'];
                            $wpdb->update( $table_name, $data, $where, $format, $where_format);

                            $where_pa = ['id' => $retrieve_data_for_parent[0]->id];
                            $format_pa = ['%d', '%s'];
                            $where_format_pa = ['%d'];
                            $wpdb->update($table_name, $pa_data, $where_pa, $format_pa, $where_format_pa); 

                        }
                    }  
                }




                wp_remote_post( $path, array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'headers' => array(),
                    'body' => array(
                        'username' => '09014332145',
                        'password' => 'Saha12024680Ary@1052417',
                        'to' => '09128381226',
                        'from' => '50004000332145',
                        'text' => $text_admin,
                        'isFlash' => false,
                    ),
                    'cookies' => array()) );

  

                    wp_remote_post( $path, array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'headers' => array(),
                        'body' => array(
                            'username' => '09014332145',
                            'password' => 'Saha12024680Ary@1052417',
                            'to' => $userPhone,
                            'from' => '50004000332145',
                            'text' => $text,
                            'isFlash' => false,
                        ),
                        'cookies' => array()) );


                $wpdb->update( $cart_table_name ,
                    array(
                        'pay'           => 22
                    ) ,
                    array(
                        'token'       =>   $token
                    )
                );

                return new WP_REST_Response(array('Status' => 'Order Recorded')  , 200 );
            }else{
                $wpdb->update( $cart_table_name ,
                    array(
                        'pay'           => 222
                    ) ,
                    array(
                        'token'       =>   $token
                    )
                );
                return new WP_REST_Response(array('Status' => 'detailsStatus')  , 200 );
            }

        }else{
            $wpdb->update( $cart_table_name ,
                array(
                    'pay'           => 22
                ) ,
                array(
                    'token'       =>   $token
                )
            );
            return new WP_REST_Response(array('Status' => 'userDataError')  , 200 );
        }

    }else{
		 
        header("Content-Type: text/html");
        $user_id = (int)$result[0]->user_id;
        $user_mobile = get_user_meta($user_id, 'billing_phone', true);
        $revertURL  = 'https://sabadino.com/wp-content/plugins/sabadino/features/mobile_portal/re_in_portal.php';

        try {
            $context = stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false
                    ]
                ]
            );
            $client = new \SoapClient( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' ,  [
                'stream_context' => $context
            ]);
        }catch (Exception $e) {
            $client = null;
            add_option('sabadino_error', $e->getMessage());
        }

        
        $foktor_id = time() + 1;
        $order_id = time();

        $parameters = array(
            'terminalId' => 6015447,
            'userName' => 'Sabadino1400',
            'userPassword' => 66908243,
            'orderId' => $order_id,
            'amount' => $total.'0',
            'localDate' => date("Ymd"),
            'localTime' => date("His"), 
            'callBackUrl' => $revertURL,
            'payerId' => 0
        );
        $result = $client->bpPayRequest($parameters);

        $PayResult = explode(',', $result->return);
 
        if ($PayResult[0] == "0") {

            echo '<p>' . __('Connecting to payment gateway...', 'wc_beh') . '</p>';
            $RefID = $PayResult[1];

                  $wpdb->update( $cart_table_name ,
            array(
                'pay'           => 1 ,
                're_location' =>  serialize($location) ,
                'time'        =>   $time ,
              'order_id' => $order_id
            ) ,
            array(
                'token'       =>   $token
            )
        );


      

            $html = '<form name="behpardakht" method="post" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat">
				<input type="hidden" name="RefId" value="' . $RefID . '">';

            if (!empty($user_mobile) && is_numeric($user_mobile)) {
                $html .= '<input type="hidden" id="mobileNo" name="MobileNo" value="' . $user_mobile . '" />';
            }
            $html .= '<script type="text/javascript" language="JavaScript">
                                document.behpardakht.submit();
                                </script>
                                </form>';
            die($html); 
        }

    }

}




//// views  ////



function re_get_sliders(){

    foreach ( get_option( 're_and_app_slider' ) as $item ){
        $slides[]= array( 'url' => $item['slider'] , "header" => ''  );
    }

    if( !empty($slides) ){
        return new WP_REST_Response(  $slides  , 200);
    }else{
        return new WP_REST_Response( array('Result' => [] ) , 200);
    }

}



function re_get_search_result($request_data){

    $params   = $request_data->get_params();
    $keyword  = $params['keyword'];


    $keyword2 =  mb_substr($keyword, 0, 3);

    if(!trim($keyword) == ''){


        global $wpdb;
        $table = $wpdb->prefix."posts";

        $pre_results     = " SELECT ID FROM $table where post_type='product' and  post_title  like '%$keyword%' and post_status='publish'";
        $post_ids  = $wpdb->get_results($pre_results,ARRAY_A);

        if (empty($pre_results)){
            $re_query     = " SELECT ID FROM $table where post_type='product' and post_title  like '%$keyword2%' and post_status='publish'";
            $post_ids  = $wpdb->get_results($re_query,ARRAY_A);
        }

        if ($post_ids) {

        $p_ids =[];
        foreach ( $post_ids as $key => $val ){
            $p_ids [] = $val['ID'];
        }

        $product_results = wc_get_products(
            array(
                'order'     => 'ASC',
                'status'    => 'publish ' ,
                'include'   =>  $p_ids
            )
        );


        foreach ( $product_results as $post) {

            $children = [];
            if ($post->is_type('variable')){
                $handle = new WC_Product_Variable( $post->get_id() );
                $variations = $handle->get_available_variations();

                foreach ($variations as $var) {

                    $children [] = [
                        'id'            =>  $var['variation_id'] ,
                        'name'          =>  array_values($var['attributes'])[0] ,
                        'price'         =>  $var['display_price'],
                        'regular_price' =>  $var['display_regular_price'] ,
                        'stock'         =>  $var['max_qty'] != "" ? $var['max_qty'] : 0  
                    ];
                }
            }

            $products[] = array(
                'id' => $post->get_id() ,
                'is_variable' => $post->is_type('variable') ,
                'children' => $children ,
                'name' => $post->get_title(),
                'on_sale'  =>  $post->get_sale_price()  > 0  ,
                'sale_percent' => re_calculate_discount_percent( $post )  ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'price' => re_get_last_price_variable( $post , 'price'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'stock' =>  re_get_product_stock( $post ) ,
                'image' => get_the_post_thumbnail_url( $post->get_id() ,'single-post-thumbnail'  )
            );

        }
            return new WP_REST_Response($products , 200 );

    }else{
            return new WP_REST_Response( array('Result' => [] ) , 404 );
    }

    }else{
        return new WP_REST_Response( array('Result' => 'keyword is not set') , 400 );
    }


}




function re_get_main_banner(){

    foreach ( get_option( 're_and_app_banner' ) as $item ){
        $banner= array( 'url' => $item['banner'] , "header" => ''  );
    }


    if( !empty($banner) ){
        return new WP_REST_Response(array('Result' => $banner ), 200);
    }else{
        return new WP_REST_Response( array('Result' => []) , 200);
    }


}




function re_get_delivery_times(){

  
       // $products = getProductsInCart();
       // $disable_time_1 = disableDay( $products ,1 );
       // $disable_time_2 = disableDay( $products ,2 );
       // $disable_time_3 = disableDay( $products ,3 );
       // $disable_time_4 = disableDay( $products ,4 );
       // $disable_time_5 = disableDay( $products ,5 );
  
  
  
        $disable_time_1 =  true;
        $disable_time_2 =  true;
        $disable_time_3 =  true;
        $disable_time_4 =  true; 
        $disable_time_5 =  true;
  
  
    $options = get_option( 'delivering_time_options' ,true );

    $vocations = explode( ","  ,  $options['vocations_manual'] );

    $re_morning_active   = $options['morning_active'];
    $re_afternoon_active = $options['afternoon_active'];
    $re_evening_active   = $options['evening_active'];
    $re_slider_input     = $options['slider_input'];
    $hours               = date_i18n('H:i');

    $time_1 = '';
    $time_2 = '';
    $time_3 = '';
    $days   = 0;

    if( $hours > $options['limit_morning'] ) {
        $time_1 = "disabled" ;
    }
    if( $hours >= $options['limit_afternoon']  ) {
        $time_2 = "disabled" ;
    }
    if( $hours >= $options['limit_evening']) {
        $time_3 = "disabled" ;
    }

    $disable_today = true;
    if( ($re_morning_active   == 0   ||  $time_1 == 'disabled') &&
        ($re_afternoon_active == 0   ||  $time_2 == 'disabled') &&
        ($re_evening_active   == 0   ||  $time_3 == 'disabled') ) {
        $disable_today = false;
    }


    $date_1 = jdate('Y/m/d' ,'' , '' , '' ,'en' );
    $date_2 = jdate('Y/m/d' , strtotime("+1 day"), '' , '' ,'en' ) ;
    $date_3 = jdate('Y/m/d' , strtotime("+2 day"), '' , '' ,'en' ) ;
    $date_4 = jdate('Y/m/d' , strtotime("+3 day"), '' , '' ,'en' ) ;
    $date_5 = jdate('Y/m/d' , strtotime("+4 day"), '' , '' ,'en' ) ;

    $capacity_1 = true ;
    $capacity_1_main = get_option( $date_1 );
    if( !empty( $capacity_1_main ) ){
        $capacity_1_main = explode("|", $capacity_1_main );
        $capacity_1_main = $capacity_1_main[0] + $capacity_1_main[1] + $capacity_1_main[2];
        if($capacity_1_main >= (int) $options['limit_one'] ){
            $capacity_1 = false;
        }
    }

    $capacity_2= true ;
    $capacity_2_main = get_option( $date_2 );

    if( !empty( $capacity_2_main ) ){
        $capacity_2_main = explode("|",$capacity_2_main);
        $capacity_2_main =  $capacity_2_main[0] + $capacity_2_main[1] + $capacity_2_main[2];
        if($capacity_2_main >= (int) $options['limit_two'] ){
            $capacity_2 = false;
        }
    }

    $capacity_3 = true ;
    $capacity_3_main = get_option( $date_3 );
    if ( !empty( $capacity_3_main )){
        $capacity_3_main = explode("|",$capacity_3_main);
        $capacity_3_main = $capacity_3_main[0] + $capacity_3_main[1] + $capacity_3_main[2];
        if($capacity_3_main >= (int) $options['limit_three'] ){
            $capacity_3 = false;
        }
    }

    $capacity_4 = true ;
    $capacity_4_main = get_option( $date_4 );
    if ( !empty( $capacity_4_main ) ){
        $capacity_4_main = explode("|",$capacity_4_main);
        $capacity_4_main =  $capacity_4_main[0] + $capacity_4_main[1] + $capacity_4_main[2];
        if($capacity_4_main >= (int) $options['limit_four'] ){
            $capacity_4 = false;
        }
    }

    $capacity_5 = true ;
    $capacity_5_main = get_option( $date_5 );
    if ( !empty( $capacity_5_main) ){
        $capacity_5_main = explode("|",$capacity_5_main);
        $capacity_5_main =  $capacity_5_main[0] + $capacity_5_main[1] + $capacity_5_main[2];
        if($capacity_5_main >= (int) $options['limit_five'] ){
            $capacity_5 = false;
        }
    }


    $first_time  = $options['first_time_start'].'_'.$options['first_time_end'];
    $second_time = $options['second_time_start'].'_'.$options['second_time_end'];
    $third_time  = $options['third_time_start'].'_'.$options['third_time_end'];





    if( !in_array( $date_1 ,$vocations ) and $capacity_1 and $disable_today and $disable_time_1 and
        friday_p( $options ,"" )  ){

        $ret_days[] = array(
            'day'    => 'امروز'  ,
            'date'   =>  jdate("Y-m-d") ,
            'morning'  => array(
                'time' => $first_time ,
                'status'=>  ($re_morning_active == 1  and $time_1 != 'disabled') ? 'active' : 'disabled' ,
            ) ,
            'afternoon'  => array(
                'time' => $second_time ,
                'status'=>  ($re_afternoon_active==1 and $time_2 != 'disabled') ? 'active' : 'disabled' ,
            ) ,
            'evening'  => array(
                'time' => $third_time ,
                'status'=>  ($re_evening_active==1   and $time_3 != 'disabled') ? 'active' : 'disabled' ,
            )
        );

        $test = array_map (function ($entry) {
            $xx = [];
            $xx[] =$entry['morning']['status'];
            $xx[] =$entry['afternoon']['status'];
            $xx[] =$entry['evening']['status'];
            return $xx;
        }, $ret_days);

        if( !in_array('active' , $test[0] ) ){
            $ret_days=[];
        }else{
            $days++;
        }
    }


    if( $days < $re_slider_input and !in_array(  $date_2  ,  $vocations  ) and  $capacity_2  and $disable_time_2 and
        friday_p( $options ,"+1 day" )  ){

        $ret_days[] = array(
            'day'    => 'فردا'  ,
            'date'   =>  jdate("Y-m-d", strtotime("+1 day")) ,
            'morning'  => array(
                'time' => $first_time ,
                'status'=>  $re_morning_active==1   ? 'active' : 'disabled' ,
            ) ,
            'afternoon'  => array(
                'time' => $second_time ,
                'status'=>  $re_afternoon_active==1 ? 'active' : 'disabled' ,
            ) ,
            'evening'  => array(
                'time' => $third_time ,
                'status'=>  $re_evening_active==1  ? 'active' : 'disabled' ,
            )
        );

        $days++; }



    if( $days < $re_slider_input and !in_array(  $date_3  ,  $vocations  )  and  $capacity_3  and $disable_time_3 and
       friday_p( $options ,"+2 day" ) ){

        $ret_days[] = array(
            'day'    => jdate("l", strtotime("+2 day"))  ,
            'date'   => jdate("Y-m-d", strtotime("+2 day")) ,
            'morning'  => array(
                'time' => $first_time ,
                'status'=>  $re_morning_active==1   ? 'active' : 'disabled' ,
            ) ,
            'afternoon'  => array(
                'time' => $second_time ,
                'status'=>  $re_afternoon_active==1 ? 'active' : 'disabled' ,
            ) ,
            'evening'  => array(
                'time' => $third_time ,
                'status'=>  $re_evening_active==1  ? 'active' : 'disabled' ,
            )
        );

        $days++; }



    if( $days < $re_slider_input and  !in_array(  $date_4  ,  $vocations  ) and $capacity_4  and $disable_time_4 and
        friday_p( $options ,"+3 day" )   ){

        $ret_days[] = array(
            'day'    => jdate("l", strtotime("+3 day"))  ,
            'date'   => jdate("Y-m-d", strtotime("+3 day")) ,
            'morning'  => array(
                'time' => $first_time ,
                'status'=>  $re_morning_active==1   ? 'active' : 'disabled' ,
            ) ,
            'afternoon'  => array(
                'time' => $second_time ,
                'status'=>  $re_afternoon_active==1 ? 'active' : 'disabled' ,
            ) ,
            'evening'  => array(
                'time' => $third_time ,
                'status'=>  $re_evening_active==1  ? 'active' : 'disabled' ,
            )
        );

        $days++; }


    if( $days < $re_slider_input and  !in_array(  $date_5  ,  $vocations  ) and $capacity_5  and $disable_time_5 and
        friday_p( $options ,"+4 day" )   ){

        $ret_days[] = array(
            'day'    => jdate("l", strtotime("+4 day"))  ,
            'date'   => jdate("Y-m-d", strtotime("+4 day")) ,
            'morning'  => array(
                'time' => $first_time ,
                'status'=>  $re_morning_active==1   ? 'active' : 'disabled' ,
            ) ,
            'afternoon'  => array(
                'time' => $second_time ,
                'status'=>  $re_afternoon_active==1 ? 'active' : 'disabled' ,
            ) ,
            'evening'  => array(
                'time' => $third_time ,
                'status'=>  $re_evening_active==1  ? 'active' : 'disabled' ,
            )
        );

        $days++; }

    if($days == 0){
        $ret_days[] = array( 'لطفا در روزهای آتی مراجعه بفرمایید');
    }

    if( !empty($ret_days) ){
        return new WP_REST_Response( $ret_days , 200);
    }


}

function friday_p( $options ,$timestamp ){
    if( $options == 1 && jdate("w", strtotime($timestamp), '' , '' ,'en') == 6   ){
        return false;
    }
    return true;

}


function getProductsInCart( $items )
    { 
        if ( !empty( $items ) ){
            foreach ( $items as $item ){
                $post_id   = $item['product_id'];
                $post_meta = (int) get_post_meta( $post_id  ,'hold_deliver_time' ,true );
                $products [ $post_id ] = $post_meta;
            }
        } 
        return $products;
    }


function disableDay( $products ,$time  )
    {
        if ( $products ){
            foreach ( $products as $product ){
                if ( $product >= $time ){
                    return false;
                }
            }
        }
        return true;
    }

///Cart


function re_create_cart_token($params)
{


    $cart_table_name = 'wp_re_cart';
    global $wpdb;

    $json = $params->get_json_params();

    $user_id  = $json['user_id'];
    $coupon   = $json['coupon'];
    $products = $json['products'];




    if ( defined( 'WC_ABSPATH' ) ) {
        // WC 3.6+ - Cart and other frontend functions are not included for REST requests.
        include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
        include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
        include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
    }else{
        $woo_url = get_home_path().'wp-content/plugins/woocommerce/';
        include_once $woo_url . 'includes/wc-cart-functions.php';
        include_once $woo_url . 'includes/wc-notice-functions.php';
        include_once $woo_url . 'includes/wc-template-hooks.php';
    }

    if ( null === WC()->session ) {
        $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

        WC()->session = new $session_class();
        WC()->session->init();
    }

    if ( null === WC()->customer ) {
        WC()->customer = new WC_Customer( $user_id, false );
    }

    if ( null === WC()->cart ) {
        WC()->cart = new WC_Cart();
        WC()->cart->empty_cart( true );
    }

    $output = serialize($products);




    foreach ($products as $pId => $pCount) {

//        if ( wc_get_product($pId)->is_type('variable') ) {
//
//            try {
//                WC()->cart->add_to_cart( wp_get_post_parent_id($pId) , $pCount , $pId, null , null );
//
//            } catch (Exception $e) {
//            }
//            if (!empty($coupon)) {
//                WC()->cart->apply_coupon($coupon);
//            }
//        }else{
            try {
                WC()->cart->add_to_cart(  $pId , $pCount );
            } catch (Exception $e) {
            }
            if (!empty($coupon)) {
                WC()->cart->apply_coupon($coupon);
            }
//        }
    }
        $couAmount = WC()->cart->get_discount_total();
        $total = (int) WC()->cart->get_cart_contents_total();


    $result = $wpdb->get_results("SELECT * FROM {$cart_table_name} WHERE user_id={$user_id} and pay<3;");

    if (!empty($result)) {

        $wpdb->update($cart_table_name ,
            array(
                'coupon' => $coupon,
                'coupon_amount' => $couAmount,
                'products' => $output,
                'total' => $total,
            ),
            array(
                'user_id' =>  $user_id,
                'id' => (int) $result[0]->id
            )
        );

        $token = $result[0]->token;
    } else {
        $token = uniqid($user_id, false);

        $data = array(
            'user_id' => $user_id,
            'products' => $output,
            'coupon' => $coupon,
            'coupon_amount' => $couAmount,
            'total' => $total,
            'token' => $token,
        );

        $format = array('%d', '%s', '%s', '%d', '%d', '%s'   );
        $wpdb->insert($cart_table_name, $data, $format);


    }

    return new WP_REST_Response(array('token' => $token), 200);

}


//
//function re_check_referral($params){
//
//    $amount = 0;
//    $params     = $params->get_params();
//    $user_id    = $params['user_id'];
//    $table_intro_name = 'wp_re_introduce_code';
////    $result = $wpdb->get_results("SELECT * FROM {$table_intro_name} WHERE self_user_id={$parent_id} ;");
//
//
//    $re_sell_items  = (int) get_user_meta($user_id , 're_score_items',true);
//    if($re_sell_items >= 5){
//        $co       = new WC_Coupon('introduce_code_discount');
//        $amount = $co->get_amount();
//    }
//
//
//    return (int) $amount;
//}
//


function re_check_coupon($params){


    $params     = $params->get_json_params();
    $user_id    = $params['user_id'];
    $products   = $params['products'];
    $coupon     = $params['coupon'];
    $regular_price = 0;
    $msg        = [];



    if ( defined( 'WC_ABSPATH' ) ) {
        // WC 3.6+ - Cart and other frontend functions are not included for REST requests.
        include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
        include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
        include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
    }else{
        $woo_url = get_home_path().'wp-content/plugins/woocommerce/';
        include_once $woo_url . 'includes/wc-cart-functions.php';
        include_once $woo_url . 'includes/wc-notice-functions.php';
        include_once $woo_url . 'includes/wc-template-hooks.php';
    }

    if ( null === WC()->session ) {
        $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

        WC()->session = new $session_class();
        WC()->session->init();
    }

    if ( null === WC()->customer ) {
        WC()->customer = new WC_Customer( $user_id, false );
    }

    if ( null === WC()->cart ) {
        WC()->cart = new WC_Cart();
        WC()->cart->empty_cart( true );
    }



    if(!empty($products)){
        foreach ($products as $id => $count) {
            $Pro_item = wc_get_product($id);


            $productList[] = array(
                'id' => $Pro_item->get_id() ,
                'name' => $Pro_item->get_name() ,
                'count' => $count ,
                'on_sale'  => $Pro_item->get_sale_price() ,
                'sale_percent' => re_calculate_discount_percent( $Pro_item ) ,
                'price' => re_get_last_price_variable( $Pro_item , 'price') ,
                'factor' => $Pro_item->get_meta('_factor_count') == null ? 1 : $Pro_item->get_meta('_factor_count'),
                'total_price' => $Pro_item->get_price()  * $count,
                'regular_price' => re_get_last_price_variable( $Pro_item , 'regular') ,
                'image' => wp_get_attachment_image_src( $Pro_item->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' =>  re_get_product_stock( $Pro_item )
            );

            try {
                WC()->cart->add_to_cart($id, $count);
                if( !empty($coupon) ) {
                    $coupon = new WC_Coupon($coupon);
                    if( WC()->cart->apply_coupon($coupon->get_code() ) ) {
                        $coupon->save();
                    }
                }
            } catch (Exception $e) {
            }
            $regular_price += $Pro_item->get_regular_price() * $count;

        }


        WC()->cart->calculate_totals();

        $products = array(
            'regular_price'   => $regular_price ,
            'total'           => WC()->cart->get_totals()['subtotal'] ,
            'final'           => WC()->cart->get_cart_contents_total() ,
            'discount_amount' => WC()->cart->get_cart_discount_total() ,
            'cart_discount'   => $regular_price  -   WC()->cart->get_cart_contents_total() ,
            'profit_percent'  => round(($regular_price  -   WC()->cart->get_cart_contents_total()   ) * 100 / $regular_price)
        );

        return new WP_REST_Response(
            array('Status'     => $products ,
                'productList'  => $productList ,
                'CouponMessage'=> $msg ) ,
            200) ;

    }else{
        return new WP_REST_Response(
            array(
                'Status' => (object) [] ,
                'productList' =>  [] ,
                'CouponMessage'=>[] ) ,
            200);
    }


}







///// products //////



function re_get_all_product_attr(){



    $attribute_taxonomies = wc_get_attribute_taxonomies();
    $taxonomy_terms = array();

    if ( $attribute_taxonomies ) :
        foreach ($attribute_taxonomies as $tax) :
            if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) :
                $taxonomy_terms[$tax->attribute_name] = get_terms( wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name&hide_empty=0' );
            endif;
        endforeach;
    endif;

    return $taxonomy_terms ;

}



function re_get_product_by_attr($req){

    $params   = $req->get_params();
    $count    = (int) $params['count'];
    $term     =       $params['term'];
    $termVal  =       $params['termVal'];



    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'posts_per_page'        => '12',
        'tax_query'             => array(
            array(
                'taxonomy'      => 'type-of-meat',
                'field'         => 'term_id', //This is optional, as it defaults to 'term_id'
                'terms'         => 55,
                'operator'      => '=' // Possible values are 'IN', 'NOT IN', 'AND'.
            )
        )
    );
    $products = new WP_Query($args);

    $pp = [];

    foreach ( $products as $p ){
        $pp[] = $p;
    }
    return $pp;

    $posts = wc_get_products( array( 'customvar' => 'somevalue' ) );

//    $posts = wc_get_products(
//        array(
//            'orderby'   => 'meta_value_num',
//            'meta_key'  => '_regular_price',
//            'order'     => 'DESC',
//            'limit'     => $count  ,
//            'status'    => 'publish '
//        )
//    );
    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){




            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0 ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' => re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => []), 200);
    }
}



function re_get_home_page_products(){
 
    $product_used  = [];
    $count         = (int) get_option( 're_display_product_count' , true );


    $args_best_sale = wc_get_products(
        array(
            'tag' => array( 'offer-products'  ),
            'limit'   =>  $count ,
            'status' =>  'publish'
        )
    );
    $offer_product = [ ];
    foreach ($args_best_sale as $offer){
        array_push($product_used , $offer->get_id() ) ;

        $offer_product[] = array(
            'id' => $offer->get_id() ,
            'name' => $offer->get_name() ,
            'on_sale'  => $offer->get_sale_price() > 0 ,
            'sale_percent' => re_calculate_discount_percent( $offer )  ,
            'price' =>  re_get_last_price_variable( $offer , 'price') ,
            'factor' => $offer->get_meta('_factor_count') == null ? 1 : $offer->get_meta('_factor_count'),
            'regular_price' => re_get_last_price_variable( $offer , 'regular') ,
            'image' => wp_get_attachment_image_src( $offer->get_image_id() ,'single-post-thumbnail'  )[0] ,
            'stock' => re_get_product_stock( $offer )
        );
    }




////  BEST SALE
    $args_best_sale = wc_get_products(
        array(
            'orderby'   => 'meta_value_num',
            'meta_key'  => 'total_sales',
            'order' => 'DESC',
            'exclude' => $product_used ,
            'limit'  => $count ,
            'status' =>  'publish'
        )
    );
    $best_sale_product = [ ];
    foreach ($args_best_sale as $best_sale){
        array_push($product_used , $best_sale->get_id() ) ;

        $sell_per_best_sale = 0;
        if(  $best_sale->get_sale_price() > 0 ? true : false ) {
            $sell_per_best_sale = round(($best_sale->get_regular_price()  - $best_sale->get_price()   ) * 100 / $best_sale->get_regular_price());
        }


        $best_sale_product[] = array(
            'id' => $best_sale->get_id() ,
            'name' => $best_sale->get_name() ,
            'on_sale'  => $best_sale->get_sale_price() > 0 ? true : false ,
            'sale_percent' => $sell_per_best_sale ,
            'price' => re_get_last_price_variable( $best_sale , 'price'),
            'factor' => $best_sale->get_meta('_factor_count') == null ? 1 : $best_sale->get_meta('_factor_count'),
            'regular_price' => re_get_last_price_variable( $best_sale , 'regular') ,
            'image' => wp_get_attachment_image_src( $best_sale->get_image_id() ,'single-post-thumbnail'  )[0] ,
            'stock' => re_get_product_stock( $best_sale )

        );
    }




//// MORE DISCOUNT
    $args_more_discount = wc_get_products(
        array(
            'limit'  => $count ,
            'order' => 'DESC',
            'orderby'   => 'meta_value_num',
            'exclude' => $product_used ,
            'meta_key'  => '_sale_price',
            'status' =>  'publish'
        )
    );

    $more_discount_product = [ ];
    foreach ($args_more_discount as $more_discount){
        array_push($product_used , $more_discount->get_id() ) ;





        $more_discount_product[] = array(
            'id' => $more_discount->get_id() ,
            'name' => $more_discount->get_name() ,
            'on_sale'  => $more_discount->get_sale_price() > 0 ,
            'sale_percent' => re_calculate_discount_percent( $more_discount ) ,
            'price' => re_get_last_price_variable( $more_discount , 'price') ,
            'factor' => $more_discount->get_meta('_factor_count') == null ? 1 : $more_discount->get_meta('_factor_count'),
            'regular_price' => re_get_last_price_variable( $more_discount , 'regular') ,
            'image' => wp_get_attachment_image_src( $more_discount->get_image_id() ,'single-post-thumbnail'  )[0] ,
            'stock' =>  re_get_product_stock( $more_discount )
        );
    }




//// NEW PRODUCTS
    $args_new_product = wc_get_products(
        array(
            'order' => 'DESC',
            'orderby' => 'ID' ,
            'limit' => $count ,
            'exclude' => $product_used ,
            'status' =>  'publish'
        )
    );

    $new_products = [ ];
    foreach ($args_new_product as $new_product){
        array_push($product_used , $new_product->get_id() ) ;

        $new_products[] = array(
            'id' => $new_product->get_id() ,
            'name' => $new_product->get_name() ,
            'on_sale'  => $new_product->get_sale_price() > 0 ,
            'sale_percent' =>  re_calculate_discount_percent( $new_product )  ,
            'price' => re_get_last_price_variable( $new_product , 'price') ,
            'factor' => $new_product->get_meta('_factor_count') == null ? 1 : $new_product->get_meta('_factor_count'),
            'regular_price' => re_get_last_price_variable( $new_product , 'regular')  ,
            'image' => wp_get_attachment_image_src( $new_product->get_image_id() ,'single-post-thumbnail'  )[0] ,
            'stock' =>  re_get_product_stock( $new_product )
        );
    }

    return new WP_REST_Response( array(
        'Offer_Products' => $offer_product ,
        'BEST_SALE'      => $best_sale_product ,
        'More_discount'  => $more_discount_product ,
        'New_Products'   => $new_products ,
    ), 200);




}



function re_get_downToUp_product($req){
    $params = $req->get_params();
    $count         = (int) get_option( 're_display_product_count' , true );
    $posts = wc_get_products(
        array(
            'orderby'   => 'meta_value_num',
            'meta_key'  => '_regular_price',
            'order'     => 'DESC',
            'limit'     => $count  ,
            'status'    => 'publish '
        )
    );
    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0 ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' =>  re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => []), 200);
    }
}



function re_get_upToDown_product($req){
    $params = $req->get_params();
    $count         = (int) get_option( 're_display_product_count' , true );
    $posts = wc_get_products(
        array(
            'orderby'   => 'meta_value_num',
            'meta_key'  => '_regular_price',
            'order'     => 'ASC',
            'limit'     => $count  ,
            'status'    => 'publish '
        )
    );
    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0 ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' =>  re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => [] ), 200);
    }
}




function re_get_offers($req){
    $count         = (int) get_option( 're_display_product_count' , true );
    $posts = wc_get_products(
        array(
            'orderby'   => 'meta_value_num',
            'meta_key'  => 'total_sales',
            'order'     => 'ASC',
            'limit'     => $count  ,
            'status'    => 'publish ' ,
            'tag'      => 'offer-products'
        )
    );

    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0 ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' =>  re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular'),
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' =>  re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => [] ) , 200);
    }
}




function re_get_best_sells($req){
    $params = $req->get_params();
    $count         = (int) get_option( 're_display_product_count' , true );
    $posts = wc_get_products(
        array(
            'orderby'   => 'meta_value_num',
            'meta_key'  => 'total_sales',
            'order' => 'DESC',
            'limit'  => $count,
            'status' =>  'publish'
        )
    );

    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0  ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' => re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => [] ), 200);
    }

}



function re_get_discount_products($req){
    $params = $req->get_params();
    $count         = (int) get_option( 're_display_product_count' , true );
    $posts = wc_get_products(
        array(
            'limit'  => $count ,
            'order' => 'DESC',
            'orderby'   => 'meta_value_num',
            'meta_key'  => '_sale_price',
            'status' =>  'publish'
        )
    );

    $related = array();


    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0 ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0],
                'stock' => re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' => [] ), 200);
    }
}




function re_get_new_products($req){

    $params = $req->get_params();
    $count         = (int) get_option( 're_display_product_count' , true );

    $posts = wc_get_products(
        array(
            'order' => 'DESC',
            'orderby' => 'ID' ,
            'limit' => $count ,
            'status' =>  'publish'
        )
    );

    $related = array();

    if(!empty($posts)){

        foreach ($posts as $post){

            $related[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0  ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price') ,
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular') ,
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' => re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $related), 200);
    } else {
        return new WP_REST_Response(array('Result' =>  []), 200);
    }
}




function re_get_all_products($request_data)
{

    $related = array();
    $params = $request_data->get_params();

    $keyword    = isset($params['id']) ? $params['id'] : '' ;
    $categories = isset($params['categories']) ? $params['categories'] : '' ;
    $count = "-1";


    if( !empty( $keyword ) ) {
        $ids = explode(',', $keyword);
        $posts = wc_get_products(
            array(
                'order' => 'DESC',
                'orderby' => 'ID',
                'include' => $ids ,
                'status' =>  'publish'
            )
        );

        if(!empty($posts)) {
            foreach ($posts as $post) {

                $urls = array();
                foreach ($post->get_gallery_image_ids() as $item) {
                    $urls[] = wp_get_attachment_image_src($item, 'single-post-thumbnail')[0];
                }

                $attr = array();
                $attributes = $post->get_attributes();
                foreach ($attributes as $attribute) {
                    $attribute_name = $attribute['name'];
                    if(substr($attribute_name, 0, 3) == 'pa_') {
                        $attribute_name = substr($attribute_name, 3, strlen($attribute_name));
                    }
                    $attr[] = array(
                        $attribute_name => explode(',', $post->get_attribute($attribute['name']))[0]
                    );
                }


                $args = array('include' => wc_get_related_products($post->get_id()));
                $related_posts = wc_get_products($args);
                $rel = [];
                foreach ($related_posts as $rel_ob) {

                    $rel[] = array(
                        'id' => $rel_ob->get_id(),
                        'name' => $rel_ob->get_name(),
                        'price' => re_get_last_price_variable( $rel_ob , 'price') ,
                        'regular_price' => re_get_last_price_variable( $rel_ob , 'regular') ,
                        'on_sale' => $rel_ob->get_sale_price() > 0 ,
                        'sale_percent' => re_calculate_discount_percent( $rel_ob ) ,
                        'stock' =>  re_get_product_stock( $rel_ob ) ,
                        'factor' => $rel_ob->get_meta('_factor_count') == null ? 1 : $rel_ob->get_meta('_factor_count'),
                        'image' => wp_get_attachment_image_src($rel_ob->get_image_id(), 'single-post-thumbnail')[0],
                    );
                }


                $related[] = array(
                    'id' => $post->get_id(),
                    'name' => $post->get_name(),
                    'description' => strip_tags($post->get_description()),
                    'price' => re_get_last_price_variable( $post , 'price'),
                    'regular_price' => re_get_last_price_variable( $post , 'regular'),
                    'on_sale' => $post->get_sale_price() > 0 ,
                    'sale_percent' => re_calculate_discount_percent( $post ) ,
                    'related_posts' => $rel,
                    'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                    'attributes' => $attr,
                    'categories' => get_the_terms($post->get_id(), 'product_cat'),
                    'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0],
                    'gallery_images' => $urls ,
                    'stock' => re_get_product_stock( $post )
                );
            }

            return new WP_REST_Response($related, 200);
        } else {
            return new WP_REST_Response(array('Result' => []), 200);
        }


    } elseif( !empty( $categories )) {
        $posts = wc_get_products(
            array(
                'order' => 'DESC',
                'orderby' => 'ID',
                'category' => $categories,
                'limit' => $count
            )
        );

        if(!empty( $posts ) ) {

            foreach ($posts as $post) {

                $related[] = array(
                    'id' => $post->get_id(),
                    'name' => $post->get_name(),
                    'on_sale' => $post->get_sale_price() > 0  ,
                    'sale_percent' => re_calculate_discount_percent( $post ),
                    'price' => re_get_last_price_variable( $post , 'price'),
                    'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                    'regular_price' => re_get_last_price_variable( $post , 'regular'),
                    'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0] ,
                    'stock' => re_get_product_stock( $post )
                );
            }

            return new WP_REST_Response(array('Result' => $related), 200);
        } else {
            return new WP_REST_Response(array('Result' => 'Empty'), 200);
        }

    } else {
        $posts = wc_get_products(
            array(
                'order' => 'DESC',
                'orderby' => 'ID',
                'limit' => $count
            )
        );

        if(!empty($posts)) {

            foreach ($posts as $post) {

                $related[] = array(
                    'id' => $post->get_id(),
                    'name' => $post->get_name(),
                    'on_sale' => $post->get_sale_price() > 0 ,
                    'sale_percent' => re_calculate_discount_percent( $post ),
                    'price' => re_get_last_price_variable( $post , 'price'),
                    'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                    'regular_price' => re_get_last_price_variable( $post , 'regular'),
                    'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0] ,
                    'stock' =>  re_get_product_stock( $post )
                );
            }

            return new WP_REST_Response(array('Result' => $related), 200);
        } else {
            return new WP_REST_Response(array('Result' => 'Empty'), 200);
        }

    }
}




function re_get_product_by_cats($request)
{

    $param = $request->get_params();
    $term_name   = $param['term_name'];
    $count = "-1";
    $cats  =   [];
    $related = [];


    $term_exists = term_exists( $term_name, 'product_cat' );
    $term_id =   $term_exists['term_id'];



    if(!empty($term_name) and $term_exists != null) {

        $cat_child = get_terms(array('taxonomy' => 'product_cat', 'parent' => $term_id ) );
//      $cat_slug  = get_term_by('term_id', $term_id, 'category');


        if(!empty($cat_child)){

            $posts = wc_get_products(
                array(
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'category' => $term_name ,
                    'limit' => $count ,
                    'status' =>  'publish'
                )
            );

            foreach ($posts as $post) {

                $related [] = array(
                    'id' => $post->get_id(),
                    'name' => $post->get_name(),
                    'on_sale' => $post->get_sale_price() > 0  ,
                    'sale_percent' => re_calculate_discount_percent( $post ),
                    'price' => re_get_last_price_variable( $post , 'price'),
                    'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                    'regular_price' => re_get_last_price_variable( $post , 'regular'),
                    'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0] ,
                    'stock' => re_get_product_stock( $post )
                );
            }

            foreach ($cat_child as $cat) {
                $term_meta = get_term_meta($cat->term_id);
                $cats [] = array(
                    'term_id' => $cat->term_id,
                    'slug' => $cat->slug,
                    'name' => $cat->name,
                    'parent' => $cat->parent,
                    'image' => wp_get_attachment_image_src($term_meta['thumbnail_id'][0], 'single-post-thumbnail')[0]
                );
            }
            return new WP_REST_Response(array('Result' => array( 'cat' => $cats , 'product'=> $related  )), 200);

        }else {

            $posts = wc_get_products(
                array(
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'category' => $term_name,
                    'limit' => $count ,
                    'status' =>  'publish'
                )
            );

            if(!empty($posts)) {

                foreach ($posts as $post) {

                    $related[] = array(
                        'id' => $post->get_id(),
                        'name' => $post->get_name(),
                        'on_sale' => $post->get_sale_price() > 0  ,
                        'sale_percent' => re_calculate_discount_percent( $post ),
                        'price' => re_get_last_price_variable( $post , 'price'),
                        'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count') ,
                        'regular_price' => re_get_last_price_variable( $post , 'regular'),
                        'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0],
                        'stock' =>  re_get_product_stock( $post )
                    );
                }
                return new WP_REST_Response(array('Result' => array( 'cat' => [] , 'product'=> $related  )), 200);

            } else {
                return new WP_REST_Response(array('Result' => array( 'cat' => [] , 'product'=> []  )), 200);
            }

        }

    } else {
        $terms = get_terms(array('taxonomy' => 'product_cat', 'parent' => 0 ));
        if(!empty($terms)) {
            foreach ($terms as $cat) {
                $term_meta = get_term_meta($cat->term_id);
                $cats [] = array(
                    'term_id' => $cat->term_id,
                    'slug' => $cat->slug,
                    'name' => $cat->name,
                    'parent' => $cat->parent,
                    'image' => wp_get_attachment_image_src($term_meta['thumbnail_id'][0], 'single-post-thumbnail')[0]
                );
            }
            return new WP_REST_Response(array('Result' => array( 'cat' => $cats , 'product'=> []  )), 200);
        } else {
            return new WP_REST_Response(array('Result' => array( 'cat' => [] , 'product'=> []  )), 200);
        }
    }
}




function re_get_ordered_products($request_data){
    $params = $request_data->get_params();
    $user   =(int) $params['user_id'];



    $orderedProduct = array();
    $orderedItem    = array();


    $qus  = new WC_Order_Query( array(
        'limit' => -1,
        'type' => 'shop_order',
        'customer_id' => $user
    ) );

    $orders = $qus->get_orders();


    foreach ( $orders as $order ) {
        $order =  wc_get_order($order)   ;
        foreach ($order->get_items() as $item_id => $item_data) {
            $orderedProduct[] =  $item_data->get_product_id() ;
        }
    }

    if(!empty($orderedProduct)){

        $posts = wc_get_products(
            array(
                'order'     => 'DESC',
                'orderby'   => 'ID',
                'include'   =>   $orderedProduct
            )
        );

        foreach ($posts as $post){
            $orderedItem[] = array(
                'id' => $post->get_id() ,
                'name' => $post->get_name() ,
                'on_sale'  => $post->get_sale_price() > 0  ,
                'sale_percent' => re_calculate_discount_percent( $post ) ,
                'price' => re_get_last_price_variable( $post , 'price'),
                'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                'regular_price' => re_get_last_price_variable( $post , 'regular'),
                'image' => wp_get_attachment_image_src( $post->get_image_id() ,'single-post-thumbnail'  )[0] ,
                'stock' => re_get_product_stock( $post )
            );
        }

        return new WP_REST_Response(array('Result' => $orderedItem), 200);

    }else{
        return new WP_REST_Response( array('Result' => [] ), 200);
    }

}




function re_get_favorite_products($request_data){

    $params  = $request_data->get_params();
    $user_id =(int) $params['user_id'];

    $favorite  =  get_user_meta( $user_id , 'favorite' , true) ;

    $fave_list = explode( ','  , $favorite );


    unset($fave_list[0]);
    $fave_list = array_values($fave_list);

    $user_ex   = get_user_by( 'ID', $user_id );
    if( !empty($user_id)   &&   $user_ex->ID > 0  ) {

        if(!empty($fave_list)) {

            $posts = wc_get_products(
                array(
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'include' => $fave_list ,
                    'status' =>  'publish'
                )
            );


            $faves_product = array();
            foreach ($posts as $post) {


                $faves_product[] = array(
                    'id' => $post->get_id(),
                    'name' => $post->get_name(),
                    'on_sale' => $post->get_sale_price() > 0 ,
                    'sale_percent' => re_calculate_discount_percent( $post ),
                    'price' => re_get_last_price_variable( $post , 'price'),
                    'factor' => $post->get_meta('_factor_count') == null ? 1 : $post->get_meta('_factor_count'),
                    'regular_price' => re_get_last_price_variable( $post , 'regular'),
                    'image' => wp_get_attachment_image_src($post->get_image_id(), 'single-post-thumbnail')[0] ,
                    'stock' => re_get_product_stock( $post )
                );
            }
            return new WP_REST_Response(array('Result' => $faves_product), 200);

        } else {
            return new WP_REST_Response( array('Result' => []) , 200);
        }
    }else{
        return new WP_REST_Response(array('Result' => 'User Not Set Or Not Exists'), 400);
    }

}




//Orders

function re_get_orders($request_data){

    $req       =  $request_data->get_params() ;
    $user_id   =  $req['user_id'];

    $orderedProduct = array();


    if(!empty($user_id) ){


        $qus  = new WC_Order_Query( array(
            'limit' => -1,
            'type' => 'shop_order',
            'customer_id' => $user_id
        ) );

        $orders = $qus->get_orders();


        foreach ( $orders as $order ) {
            $order =  wc_get_order($order);

            $pay_meth = $order->get_payment_method();
            if ($pay_meth ==='place'){
                $payment_meth = 'پرداخت در محل';
            }elseif ($pay_meth === 'portal'){
                $payment_meth = 'پرداخت اینترنتی';
            }else{
                $payment_meth = $order->get_payment_method();
            }

            $status_or = "";
            switch ($order->get_status() ){
                case "processing":
                    $status_or = "در حال انجام";
                    break;

                case "pending":
                    $status_or = "در حال انتظار";
                    break;

                case "on-hold":
                    $status_or = "نگه داشته شده";
                    break;

                case "completed":
                    $status_or = "تکمیل شده";
                    break;

                case "cancelled":
                    $status_or = "لغو شده";
                    break;

                case "refunded":
                    $status_or = "برگشت خورده";
                    break;

                case "failed":
                    $status_or = "شکست خورده";
                    break;


                case "on-deliver":
                    $status_or = "در حال  تحویل";
                    break;

                case "is-packing":
                    $status_or = "در حال بسته بندی";
                    break;
            }

            $t = $order->get_date_created()->date('Y-m-d H:i:s');
            $orderedProduct[] = array(
                'order_id'     => $order->get_id(),
                'last_name'    => $order->get_billing_last_name() ,
                'total'        => $order->get_total(),
                'status'       => $status_or ,
                'order_code'   => $order->get_id() ,
                'payment_type' => $payment_meth,
                'order_date'   => jdate('Y-m-d H:i:s' , strtotime($t)) ,
            );
        }

        if(!empty($orderedProduct)){

            return new WP_REST_Response(array('Result' => $orderedProduct), 200);

        }else{
            return new WP_REST_Response( array('Result' => [] ), 200);
        }
    }else{
        return new WP_REST_Response(array('Result' => 'User Id Not Set'), 400 );
    }
}





function re_get_order_single($request_data){

    $req       =  $request_data->get_params() ;
    $orderID   =  $req['order_id'];

    $orderedProduct = array();
    $product_list  = [];


    if(!empty($orderID)) {
        $orders  = wc_get_order( $orderID );


        if( $orders != false ) {

            $time = $orders->get_date_created()->date('Y-m-d H:i:s');


            $reg_price = 0;
            foreach ( $orders->get_items() as $item ) {
                $productObject= wc_get_product($item->get_product_id());
                $product_list[] = array(
                    'product_name'  => $item->get_name()  ,
                    'product_sku'  => $productObject->get_sku() ,
                    'image' =>  wp_get_attachment_image_src( $productObject->get_image_id() ,'single-post-thumbnail'  )[0],
                    'count' => $item->get_quantity() ,
                    'price' => $productObject->get_price(),
                    'total' => $productObject->get_price() * $item->get_quantity()
                );
                $reg_price += (int) $productObject->get_sale_price() * $item->get_quantity();
            }

            $pay_meth = $orders->get_payment_method();
            if ($pay_meth ==='place'){
                $payment_meth = 'پرداخت در محل';
            }elseif ($pay_meth === 'portal'){
                $payment_meth = 'پرداخت اینترنتی';
            }else{
                $payment_meth = $orders->get_payment_method();
            }

            $status_or = "";
            switch ($orders->get_status() ){
                case "processing":
                    $status_or = "در حال انجام";
                    break;

                case "pending":
                    $status_or = "در حال انتظار";
                    break;

                case "on-hold":
                    $status_or = "نگه داشته شده";
                    break;

                case "completed":
                    $status_or = "تکمیل شده";
                    break;

                case "cancelled":
                    $status_or = "لغو شده";
                    break;

                case "refunded":
                    $status_or = "برگشت خورده";
                    break;

                case "failed":
                    $status_or = "شکست خورده";
                    break;


                case "on-deliver":
                    $status_or = "در حال  تحویل";
                    break;

                case "is-packing":
                    $status_or = "در حال بسته بندی";
                    break;
            }

            $orderedProduct[] = array(
                'order_status'     => $status_or ,
                'get_id'     => $orders->get_id(),
                'get_billing_last_name'      => $orders->get_billing_last_name(),
                'get_billing_first_name'     => $orders->get_billing_first_name(),
                'get_billing_phone'          => $orders->get_billing_phone(),
                'get_billing_address_1'      => $orders->get_billing_address_1(),
                'get_billing_email'          => $orders->get_billing_email(),
                'payment_type' => $payment_meth,
                'get_customer_order_notes'    => $orders->get_customer_order_notes(),
                'is_paid'     => $orders->is_paid(),
                'total'     => $orders->get_total(),
                'coupon_codes'     => $orders->get_coupon_codes()[0] ,
                'discount_total'     => $orders->get_discount_total(),
                'order_date'   => jdate('Y-m-d H:i:s' , strtotime($time)) ,
                'regular_price' => $reg_price ,
                'product_list'     => $product_list,
            );

            return new WP_REST_Response(array('Result' => $orderedProduct), 200);
        }else {
            return new WP_REST_Response(array('Result' => []), 200);
        }

    }else{
        return new WP_REST_Response(array('Result' => 'Order Id Not Set Or Wrong'), 400 );
    }
}






///// User /////


function re_login_or_register($request_data){

    $token   = $request_data->get_header('token');
    $request = $request_data->get_params();
    $phone   = $request['phone'];
    $ranCode = mt_rand(1000,9999);

    $referral_status = get_option('re_change_referral_status' , true);
    $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";
    $text =  convertNumber($ranCode). ' کد ورود شما به سبدینو';




    $user_query = get_users(array('meta_key' => 'billing_phone', 'meta_value' => $phone));

    if (!empty($token) ){
        if (!empty($phone) ){
            if(!empty($user_query)) {

                $response = wp_remote_post($path, array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'headers' => array(),
                        'body' => array(
                            'username' => '09014332145',
                            'password' => 'Saha12024680Ary@1052417',
                            'to' => $phone,
                            'from' => '50004000332145',
                            'text' => $text,
                            'isFlash' => false,
                        ),
                        'cookies' => array()
                    )
                );

                $result =  json_decode($response['body']);

                if ($result->Value == 11){
                    ini_set("soap.wsdl_cache_enabled", "0");
                    $sms_client = new SoapClient('http://api.payamak-panel.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
                    $param["username"]="09014332145";
                    $param["password"]="Saha12024680Ary@1052417";
                    $param["text"]=$ranCode;
                    $param["to"]=$phone;
                    $param["bodyId"]= 16962 ;
                    $data= $sms_client->SendByBaseNumber2($param)->SendByBaseNumber2Result;

                    if ($data != 11 ){
                        return new WP_REST_Response(array('Result' => 1 , 'registration_code'=> $ranCode ,'referral_status'=> $referral_status ), 200);
                    }else{
                        return new WP_REST_Response(array('Result' =>  'An Error Occurred'), 500);
                    }

                }
                return new WP_REST_Response(array('Result' => 1 , 'registration_code'=> $ranCode ,'referral_status'=> $referral_status), 200);

            }else{

                $text =  convertNumber($ranCode). ' کد  ثبت نام  شما در سبدینو';

                $response = wp_remote_post($path, array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'headers' => array(),
                        'body' => array(
                            'username' => '09014332145',
                            'password' => 'Saha12024680Ary@1052417',
                            'to' => $phone,
                            'from' => '50004000332145',
                            'text' => $text,
                            'isFlash' => false,
                        ),
                        'cookies' => array()
                    )
                );

                $result =  json_decode($response['body']);

                if ($result->Value == 11){
                    ini_set("soap.wsdl_cache_enabled", "0");
                    $sms_client = new SoapClient('http://api.payamak-panel.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
                    $param["username"]="09014332145";
                    $param["password"]="Saha12024680Ary@1052417";
                    $param["text"]=$ranCode;
                    $param["to"]=$phone;
                    $param["bodyId"]= 16962 ;
                    $data= $sms_client->SendByBaseNumber2($param)->SendByBaseNumber2Result;

                    if ($data != 11 ){
                        return new WP_REST_Response(array('Result' => 0 , 'registration_code'=> $ranCode ,'referral_status'=> $referral_status), 200);
                    }else{
                        return new WP_REST_Response(array('Result' =>  'An Error Occurred'), 500);
                    }
                }

                return new WP_REST_Response(array('Result' => 0 , 'registration_code'=> $ranCode ,'referral_status'=> $referral_status), 200);
            }

        }else{
            return new WP_REST_Response(array('result' => 'Set Phone Parameter'), 400);
        }
    }else{
        return new WP_REST_Response(array('result' => 'Token Not Set Or Wrong') , 401);
    }


}




function re_create_user($request_data)
{
    $token  = $request_data->get_header('token');
    $params = $request_data->get_params();
    $phone          = $params['phone'];
    $referral_code  = $params['referral_code'];
    $full_name      = $params['full_name'];
    $result         = 1;
    global $wpdb;
    $table_name = $wpdb->prefix . "re_referral";



    if( $token == 'reza') {
        if(!empty($phone) && is_numeric($phone) ) {

            $user_query = get_user_by('login', $phone);
            if(empty($user_query)) {

                $args = array(
                    'user_registered' => date('Y-m-d G:i:s'),
                    'user_login' => $phone ,
                    'role' => 'customer'
                );

                $user_id = wp_insert_user($args);


                $metas = array(
                    'billing_phone' => $phone ,
                    'order_note'    => '' ,
                    're_favorite'   => '' ,
                );

                foreach($metas as $key => $value) {
                    update_user_meta( $user_id , $key, $value );
                }


                if ($referral_code != ''){
                    $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE self_referral= $referral_code  ;");

                    if ($retrieve_data[0]->id > 0) {
                        $data  = [
                            'user_id'        =>  $user_id ,
                            'phone'          =>  $phone,
                            'date_register'  =>  jdate('Y-m-d H:i:s'),
                            'full_name'      =>  $full_name ,
                            'purchase_ids'   =>  serialize([]),
                            'self_referral'  =>  rand( 1000 , 9999 ) . substr( $user_id ,-1  ,  1) ,
                            'parent_referral' => $referral_code
                        ];
                        $format = [ '%d' ,'%s' ,'%s'  ,'%s'  ,'%s' ,'%d'  ,'%d' ];
                        $wpdb->insert( $table_name , $data , $format );
                        $result = 1 ;
                    }else{
                        $result = 0 ;
                    }
                }else{
                    $data  = [
                        'user_id'        =>  $user_id ,
                        'phone'          =>  $phone,
                        'date_register'  =>  jdate('Y-m-d H:i:s'),
                        'full_name'      =>  $full_name != '' ? $full_name : '' ,
                        'purchase_ids'   =>  serialize([]),
                        'self_referral'  =>  rand( 1000 , 9999 ) . substr( $user_id ,-1  ,  1)
                    ];
                    $format = [ '%d' ,'%s' ,'%s'  ,'%s'  ,'%s' ,'%d'  ,'%d' ];
                    $wpdb->insert( $table_name , $data , $format );
                }


                if ($result == 1){
                    return new WP_REST_Response (array('Result' => 'User Created'), 201);
                }else{
                    return new WP_REST_Response (array('Result' => 'Referral Code Is Wrong'), 400);
                }

            } else {

                if ($referral_code != ''){
                    $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE self_referral= $referral_code  ;");
                    if ($retrieve_data[0]->id > 0) {
                        $data = ['parent_referral' => $referral_code , 'full_name'  =>  $user_query->first_name .' '.$user_query->last_name ];
                        $where = ['user_id' => $user_query->ID ];
                        $format = ['%d' , '%s'];
                        $where_format = ['%d'];
                        $wpdb->update($table_name, $data, $where, $format, $where_format);
                        return new WP_REST_Response (array('Result' => 'Referral Updated'), 200);
                    }else{
                        return new WP_REST_Response (array('Result' => 'Referral Code Is Wrong'), 400);
                    }
                }else{
                    return new WP_REST_Response (array('Result' => 'User Exists'), 200);
                }
            }

        } else {
            return new WP_REST_Response (array('Result' => 'Phone Not Set'), 400);
        }
    } else {
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong'), 401);
    }
}




function re_get_user_data($request_data){
    $token  = $request_data->get_header('token');
    $params = $request_data->get_params();
    $phone  = $params['phone'];
    if($token == 'reza') {
        if(!empty($phone)) {
            $users = get_users(array('meta_key' => 'billing_phone', 'meta_value' => $phone));

            if(!empty($users)) {
                $user_meta = array();
                $user_data = array();

                $user_ID = '';
                foreach ($users as $user) {
                    $user_data = array(
                        'ID' => $user->ID,
                        'user_login' => $user->user_login,
                        'user_registered' => $user->user_registered,
                        'display_name' => $user->display_name,
                        'user_email' => $user->user_email,
                    );

                    $user_ID = $user->ID;
                }

                $user_meta[] = get_user_meta($user_ID);


                global  $wpdb;
                $table_name = $wpdb->prefix . "re_referral";
                $referral_data = $wpdb->get_results("SELECT * FROM {$table_name} WHERE user_id=150 ; " );


                $meta = array(
                    'first_name' => $user_meta[0]['first_name'][0],
                    'last_name' => $user_meta[0]['last_name'][0],
                    'billing_phone' => $user_meta[0]['billing_phone'][0],
                    'billing_address_1' => $user_meta[0]['billing_address_1'][0],
                    'order_count' => $user_meta[0]['_order_count'][0] ,
                    'order_note' => $user_meta[0]['order_note'][0],
//                        'self_ref_code'   => $referral_data['self_referral'],
//                        'parent_ref_code' => $referral_data['parent_referral'] ,
//                        'score'           => $referral_data['score'] ,
//                        'remaining '      => $referral_data['remaining']  ,
                );
                return new WP_REST_Response(array('user_data' => $user_data, 'user_meta' => $meta), 200);
            } else {
                return new WP_REST_Response(array('Result' => ' User Not Found'), 404);
            }
        } else {
            return new WP_REST_Response(array('Result' => 'Set Phone Parameter'), 400);
        }
    }else{
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong') , 401);
    }

}




function re_remove_user($request_data){
    $token  = $request_data->get_header('token');
    $params = $request_data->get_params();
    $phone  = $params['phone'];

    if($token == 'reza') {
        if(!empty($phone) && is_numeric($phone)) {


            $user_query = get_user_by( 'login'  ,  $phone  );
            if(!empty($user_query) && $user_query->ID !=2 && $user_query->ID!=7) {

                wp_delete_user( $user_query->ID  );

                return new WP_REST_Response (array('Result' => 'User Deleted'), 200);
            } else {
                return new WP_REST_Response (array('Result' => 'User Not Found'), 404);
            }

        } else {
            return new WP_REST_Response (array('Result' => 'Phone Not Set'), 400);
        }
    }else{
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong') , 401);
    }

}




function re_update_user($request_data){

    $token  = $request_data->get_header('token');

    $body          = $request_data->get_params();
    $user_id       = $body['user_id'];
    $first_name    = $body['first_name'];
    $last_name     = $body['last_name'];
    $user_email    = $body['user_email'];
    $address       = $body['billing_address'];
    $note          = $body['order_note'];
    $referral      = $body['referral'];


    if($token == 'reza') {

        if(!empty($user_id) ) {

            $user_query = get_user_by( 'id'  ,    $user_id   );


            if(!empty( $user_query ) ) {

                wp_update_user( array(
                    'ID'         => $user_query->ID,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'user_email' => $user_email,
                ) );

                $metas = array(
                    'billing_address_1' => $address ,
                    'order_note' => $note ,
                );

                foreach($metas as $key => $value) {
                    update_user_meta( $user_query->ID, $key, $value );
                }

                global $wpdb;
                $table_name = $wpdb->prefix . "re_referral";
                $data  = [
                    'parent_referral' => $referral ,
                ];
                $where = ['user_id' => $user_id];
                $format = [ '%d' ];
                $where_format = ['%d'];
                $wpdb->update( $table_name, $data, $where, $format, $where_format);




                return new WP_REST_Response (array('Result' => 'User Updated'), 200);
            } else {
                return new WP_REST_Response (array('Result' => 'User Not Exists'), 409);
            }

        } else {
            return new WP_REST_Response (array('Result' => 'User Not Set'), 400);
        }
    }else{
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong') , 401);
    }

}




function re_add_favorite($request_data){


    $params = $request_data->get_params();
    $token  = $request_data->get_header('token');

    $user_id     = $params['user_id'];
    $product_id  = $params['product_id'];

    $favorite = get_user_meta( $user_id , 'favorite' , true);

    $user_ex   = get_user_by( 'id', $user_id );

    if($token == 'reza'){

        if( !empty($user_id)   &&   $user_ex != false ) {

            if( is_numeric($product_id) ) {


                if(  strpos($favorite, $product_id) == false  ) {

                    $favorite = $favorite." ,".$product_id;
                    update_user_meta($user_id , 'favorite' , $favorite );

                    return new WP_REST_Response(array('Result' => 'Product Added'), 200);

                }else {
                    return new WP_REST_Response(array('Result' => 'Product Already Added'), 409);
                }

            }else{
                return new WP_REST_Response(array('Result' => 'Product Not Set'), 400);
            }

        }else{
            return new WP_REST_Response(array('Result' => 'User Not Set Or Not Exists'), 400);
        }

    }else{
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong '), 401);
    }

}




function re_remove_favorite($request_data){


    $params = $request_data->get_params();
    $token  = $request_data->get_header('token');
    $user_id     = $params['user_id'];
    $product_id  = $params['product_id'];

    $favorite = get_user_meta( $user_id , 'favorite' , true );

    $check = strpos( $favorite, $product_id);

    if( $token == 'reza') {

        if( $check != false) {


            $favorite =  str_replace( (','.$product_id) , '' , $favorite );

            update_user_meta($user_id , 'favorite' , $favorite );

            return new WP_REST_Response(array('Result' => 'Product Removed'), 200);
        }else {
            return new WP_REST_Response(array('Result' => 'Product Not Exists'), 404);
        }
    }else {
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong '), 401);
    }
}



///Referral

function re_referral_gift_checker($request_data)
{
    $token    = $request_data->get_header('token');
    $params   = $request_data->get_params();
    $user_id  = $params['user_id'];
    $ref_gifts=[];

    if( $token == 'reza') {
        if(!empty($user_id) ) {


            global $wpdb;
            $table_name = $wpdb->prefix . "re_referral";

            $user_id = get_current_user_id();
            $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

            $re_ref_gifts = unserialize( get_option('re_ref_cal_gifts'  , false ));
            if ( $retrieve_data[0]->score >= $re_ref_gifts['roof']){
                $which_gifts = $re_ref_gifts[0]->status;
                if( $which_gifts == 1){
                    $ref_gifts = [
                        'gift_type'   => 'percent' ,
                        'gift_amount' => $re_ref_gifts[0]->amount ,
                    ];
                }elseif ($which_gifts == 2){
                    $ref_gifts = [
                        'gift_type'   => 'fixed' ,
                        'gift_amount' => $re_ref_gifts[0]->amount ,
                    ];
                }elseif($which_gifts==3) {
                    $ref_gifts = [
                        'gift_type'   => 'product' ,
                        'gift_amount' => $re_ref_gifts[0]->amount ,
                    ];
                }

            }

            return new WP_REST_Response ( array ( 'Result' => $ref_gifts), 201);

        } else {
            return new WP_REST_Response (array('Result' => 'UserId Not Set'), 400);
        }
    } else {
        return new WP_REST_Response(array('Result' => 'Token Not Set Or Wrong'), 401);
    }
}





// Settings

function re_get_limit_cart(){

    $responses = get_option( 're_minimum_purchase_amount' , true );

    if( $responses > 0  ){
        return new WP_REST_Response(array('Result' => (int) $responses ) , 200);
    }else{
        return new WP_REST_Response(array('Result' => []), 200);
    }

}



function re_get_discount_timer(){

//   $args = array('category_name' => 'limit-time-discount');
//   $From = get_posts( $args )[0]->post_excerpt;

//   $date_2 = jdate("Y/m/d", strtotime("+1 day") , '' , '' ,'en');

    $From = jdate('Y-m-d' , '' , '' , '' ,'en').'|'.jdate('H:i:s', '' , '' , '' ,'en');

    $From = explode('|' , $From );

    $fromDate = $From[0];
    $fromDate = explode('-' , $fromDate );
    $fromTime = $From[1];
    $From =  jalali_to_gregorian( $fromDate[0] , $fromDate[1] , $fromDate[2] , '-' );


    $re_set_discount_time = explode('|' ,  get_option( 're_set_discount_time' , true ) );




    $toDate = explode('-' , $re_set_discount_time[0] );
    $toTime = $re_set_discount_time[1];
    $To   =  jalali_to_gregorian( $toDate[0] , $toDate[1] , $toDate[2] , '-' );



    $From     = new DateTime($From.' '.$fromTime);
    $To       = new DateTime($To.' '.$toTime );




    if ($To->getTimestamp() - $From->getTimestamp() < 0 ){
        $sec =0;
    }else{

        $interval = $From->diff($To);

        $sec =  $interval->days * 24 * 60 * 60 ;
        $sec += $interval->h * 60 *60;
        $sec += $interval->i * 60 ;
        $sec += $interval->s;

    }

    return new WP_REST_Response(array('Result' => $sec)  , 200);


}



function re_get_required_update(){

    return new WP_REST_Response(array(
        'Result' => '2.1.0' ,
        'urls' => array(
            'bazar' => 'https://cafebazaar.ir/app/galshop' ,
            'website' => 'https://galshop.ir/app/download' ,
            'googlePlay' =>  'https://play.google.com/store/apps/galshop'
        ) ,
        'message'  => 'نسخه جدید منتشر شد شما میتوانید '
    )  , 200);

}



function re_get_optional_update(){

    return new WP_REST_Response(array(
        'Result' => '1.1.0' ,
        'urls' => array(
            'bazar' => 'https://cafebazaar.ir/app/galshop' ,
            'website' => 'https://galshop.ir/app/download' ,
            'googlePlay' =>  'https://play.google.com/store/apps/galshop'
        ) ,
        'message'  => 'نسخه جدید منتشر شد شما میتوانید '
    )  , 200);

}


//Comments

function re_get_comments($request_data)
{

    $params = $request_data->get_params();
    $product_id = $params['product_id'];
    $list = [];
    $total_rate = 0;

    $args = array(
        'post_id' => $product_id,
        'status' => 'approve'
    );


    $comments_query = new WP_Comment_Query;
    $revs = $comments_query->query($args);

    if( !empty($revs)   ){
        foreach ($revs as $rev){
            $list[] = array(
                "comment_ID" => $rev->comment_ID,
                "comment_post_ID" => $rev->comment_post_ID,
                "comment_author" => $rev->comment_author,
                "comment_author_email" => $rev->comment_author != '' ? $rev->comment_author : '',
                "comment_date" => jdate(  $rev->comment_date ),
                "comment_content" => $rev->comment_content,
                "user_id" => $rev->user_id,
                'rated' => get_comment_meta($rev->comment_ID, 'rating', true)
            );

            $total_rate += get_comment_meta($rev->comment_ID, 'rating', true);

        }
        $list [] = array(
            'average' =>  $total_rate/count($list)
        );



        return new WP_REST_Response(array('Result' => $list)  , 200);
    }else{
        return new WP_REST_Response(array('Result' => [] ), 200);
    }

}



function re_add_comment($request_data)
{


    $json             =  $request_data->get_json_params() ;
    $product_id       =  (int) $json['product_id'];
    $comment_rate     =        $json['comment_rate'];
    $user_id          =        $json['user_id'];
    $comment_content  =        $json['comment_content'];

    $user_data = get_user_by('ID' , $user_id);


    $args = array(
        'comment_post_ID' => $product_id,
        'comment_type' => 'review'  ,
        'user_id'      => $user_data->ID ,
        'comment_content' => $comment_content ,
        'comment_author' => $user_data->first_name." ".$user_data->last_name ,
        'comment_author_email' => $user_data->user_email ,
        'comment_date' =>  date('Y-m-d H:i:s')
    );



    $comm_id = wp_insert_comment( $args );
    if( $comment_rate > 0){
        add_comment_meta($comm_id , 'rating' , $comment_rate );
    }


    if( is_numeric($comm_id ) ){
        return new WP_REST_Response(array('Result' => 'Recorded')  , 200);
    }else{
        return new WP_REST_Response(array('Result' => 'Server Error ' ), 500);
    }

}






function re_get_last_price_variable($ob , $out){
    $type  = $ob->is_type('variable');
    if ($type) {
        $child    = $ob->get_children();
        $last     = wc_get_product(end($child));
        $regular  = $last->get_regular_price();
        $sale     = $last->get_price();
    }else{
        $sale     = $ob->get_price();
        $regular  = $ob->get_regular_price();
    }
    if ($out == 'regular'){
        return $regular;
    }
        return $sale;

}


 function re_get_product_stock($obj){

     $stock = 0;
     if ($obj->get_manage_stock() == true ){
         $stock = $obj->get_stock_quantity();
     }elseif ( $obj->get_manage_stock() == false and $obj->get_stock_status() =='instock' ){
         $stock = 99999;
     }elseif($obj->get_manage_stock()== false and $obj->get_stock_status() =='outofstock') {
         $stock = 0 ;
     }

     return $stock;
 }


 function re_calculate_discount_percent($obj){
     $sell_percent = 0;
     if( $obj->get_sale_price() > 0  ) {
         $sell_percent =
             round(($obj->get_regular_price() -
                     $obj->get_price()) *
                 100 / $obj->get_regular_price()
             );
     }
     return $sell_percent;
 }