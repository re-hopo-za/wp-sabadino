<?php


/*
Plugin Name: درگاه ایران کیش 2
Description: Provides simple front end registration and login forms
Version: 1.0
Author: Reza Hossein Pour
*/





 if( isset($_POST['ResCode']) ){
     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php';
     $ResCode 		= (isset($_POST['ResCode']) && $_POST['ResCode'] != "") ? $_POST['ResCode'] : "";
     if ($ResCode == '0')
     {
         $options = [
             'cache_wsdl'     => WSDL_CACHE_NONE,
             'trace'          => 1,
             'stream_context' => stream_context_create(
                 [
                     'ssl' => [
                         'verify_peer'       => false,
                         'verify_peer_name'  => false,
                         'allow_self_signed' => true
                     ]
                 ]
             )
         ];
         $client = new SoapClient( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' ,$options);

         //$soap = new \SoapClient($this->serverUrl);
//         $client 				= new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
         $namespace 		    ='http://interfaces.core.sw.bps.com/';
         $orderId 				= (isset($_POST['SaleOrderId']) && $_POST['SaleOrderId'] != "") ? $_POST['SaleOrderId'] : "";
         $verifySaleOrderId     = (isset($_POST['SaleOrderId']) && $_POST['SaleOrderId'] != "") ? $_POST['SaleOrderId'] : "";
         $verifySaleReferenceId = (isset($_POST['SaleReferenceId']) && $_POST['SaleReferenceId'] != "") ? $_POST['SaleReferenceId'] : "";

         //var_dump([$orderId ,$verifySaleOrderId,$verifySaleReferenceId ]);

         $parameters = array(
             'terminalId' 		=> 6015447,
             'userName' 			=> 'Sabadino1400',
             'userPassword' 		=> 66908243,
             'orderId' 			=> $orderId,
             'saleOrderId' 		=> $verifySaleOrderId,
             'saleReferenceId' 	=> $verifySaleReferenceId
         );

         $result = $client->call('bpVerifyRequest', $parameters, $namespace);

         if($result == 0)
         {
             $result = $client->call('bpSettleRequest', $parameters, $namespace);

             if($result == 0)
             {
                 $portal_table_name = 'wp_re_portal';
                 global $wpdb;
                 $DB_result = $wpdb->get_results("SELECT * FROM {$portal_table_name} WHERE order_id={$orderId}");
                 re_add_order_in_verify($DB_result[0]->cart_token);

                 //-- تمام مراحل پرداخت به درستی انجام شد.
                 die("عملیات پرداخت با موفقیت انجام شد, شناسه پیگیری تراکنش : {$verifySaleReferenceId}");
             } else {
                 $client->call('bpReversalRequest', $parameters, $namespace);

                 //-- نمایش خطا
                 $error_msg = (isset($result) && $result != "") ? $result : "خطا در ثبت درخواست واریز وجه";
                 die($error_msg);
             }
         } else {
             $client->call('bpReversalRequest', $parameters, $namespace);

             //-- نمایش خطا
             $error_msg = (isset($result) && $result != "") ? $result : "خطا در عملیات وریفای تراکنش";
             die($error_msg);
         }
     } else {
         //-- نمایش خطا
         $error_msg = (isset($ResCode) && $ResCode != "") ? $ResCode : "تراکنش ناموفق";
         die($error_msg);
     }
 }








function re_add_order_in_verify(  $cart_token ){

    global $wpdb;
    $cart_table_name = $wpdb->prefix .'re_cart';
    $table_name      = $wpdb->prefix ."re_referral";



    $result = $wpdb->get_results("SELECT * FROM {$cart_table_name} WHERE token='{$cart_token}'");


    $user_id    = (int) $result[0]->user_id;
    $coupon     =       $result[0]->coupon;
    $time       =       $result[0]->time;
    $location   =       unserialize($result[0]->location);
    $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";



    $userData  = get_user_by('id' ,  $user_id );

    $userPhone  = get_user_meta($user_id, 'billing_phone', true);
    $order_note = get_user_meta($user_id, 'order_note', true);
    $addr       = get_user_meta($user_id, 'billing_address_1', true);




    $address = array(
        'first_name' => $userData->first_name,
        'last_name' => $userData->last_name,
        'email' => $userData->user_email,
        'phone' => $userPhone,
        'address_1' => $addr
    );

    $order = wc_create_order(array('customer_id' => $user_id));
    $order_id = $order->get_id();

    try {
        $order->set_payment_method('portal');
        $order->add_order_note( $order_note  ,$user_id , true );
        $order->set_created_via('pragmatically');
        $order->set_address($address, 'billing');
    } catch (Exception $e) {
    }



    $order->update_status("processing", 'Imported order', true);

    $products = unserialize($result[0]->products );

    foreach ($products as $item => $count) {
        $order->add_product(wc_get_product($item), $count);
    }






    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

    $re_ref_gifts = unserialize( get_option('re_ref_cal_gifts'  , false ) );
    if ( $retrieve_data[0]->score >= $re_ref_gifts['roof'] ){
        $which_gifts = $re_ref_gifts['status'];

        if( $which_gifts == 1){
            $coupon_code = $user_id.'_'.$order_id.'_percent';
            $amount = $re_ref_gifts['amount'] ;
            $discount_type = 'percent';

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 7,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post( $coupon );

            update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
            update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
            update_post_meta( $new_coupon_id, 'individual_use', 'no' );
            update_post_meta( $new_coupon_id, 'product_ids', '' );
            update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
            update_post_meta( $new_coupon_id, 'usage_limit', 1 );
            update_post_meta( $new_coupon_id, 'expiry_date', '' );
            update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
            update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

            $order->apply_coupon($coupon_code);


        }elseif ($which_gifts == 2){

            $coupon_code = $user_id.'_'.$order_id.'_fixed';
            $amount = $re_ref_gifts['amount'] ;
            $discount_type = 'fixed_cart';

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 7,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post( $coupon );

            update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
            update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
            update_post_meta( $new_coupon_id, 'individual_use', 'no' );
            update_post_meta( $new_coupon_id, 'product_ids', '' );
            update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
            update_post_meta( $new_coupon_id, 'usage_limit', 1 );
            update_post_meta( $new_coupon_id, 'expiry_date', '' );
            update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
            update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

            $order->apply_coupon($coupon_code);



        }elseif($which_gifts==3) {
            $order->add_product ( wc_get_product( (int) $re_ref_gifts['amount']) , 1 );
            $coupon_code = $user_id.'_'.$order_id.'_'.$re_ref_gifts['amount'];
            $amount =  wc_get_product( $re_ref_gifts['amount'] )->get_sale_price() ;

            $discount_type = 'fixed_cart';

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 7,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post( $coupon );

            update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
            update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
            update_post_meta( $new_coupon_id, 'individual_use', 'no' );
            update_post_meta( $new_coupon_id, 'product_ids', '' );
            update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
            update_post_meta( $new_coupon_id, 'usage_limit', 1 );
            update_post_meta( $new_coupon_id, 'expiry_date', '' );
            update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
            update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

            $order->apply_coupon($coupon_code);
        }


        if (!empty($order->get_coupon_codes())){
            $mines_score  = $re_ref_gifts['roof'] ;

            $data_mines = [
                'score' => $retrieve_data[0]->score - $mines_score ,
            ];

            $where_mines = ['id' => $retrieve_data[0]->id];
            $format_mines = ['%d'];
            $where_format_mines = ['%d'];
            $wpdb->update( $table_name, $data_mines, $where_mines, $format_mines, $where_format_mines);
        }


    }else{
        if (!empty($coupon) ) {
            $order->apply_coupon($coupon);
        }
    }






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



    if (isset($time)) {
        $dates = $time;
        $count = $order->get_item_count();

        $ex = explode("|", $dates);
        $database = get_option($ex[1]);

        $database_index = explode("|", $database);
        $morning = $database_index[0];
        $afternoon = $database_index[1];
        $evening = $database_index[2];
        if ($ex[0] == get_option('re_first_time_start') . get_option('re_first_time_end')) {
            $morning = $morning + $count;
        } elseif ($ex[0] == get_option('re_second_time_start') . get_option('re_second_time_end')) {
            $afternoon = $afternoon + $count;
        } elseif ($ex[0] == get_option('re_third_time_start') . get_option('re_third_time_start')) {
            $evening = $evening + $count;
        }
        update_option($ex[1], $morning . '|' . $afternoon . '|' . $evening);
    }
    update_post_meta($order->get_id(), 'daypart', $time);
    update_post_meta( $order->get_id(), '_order_shipping_location', $location );




    $fromDate = explode('|' , $time);
    $time  =  explode('_'  , $fromDate[0])[0].":00:00";
    $date  = explode('-' , $fromDate[1] );
    $date  = jalali_to_gregorian( $date[0] , $date[1] , $date[2] , '-' );
    $value =  strtotime($date.' '.$time ) ;
    update_post_meta(  $order->get_id() , 're_micro_time' , $value );





    $productName = array();


    foreach ($order->get_items() as $item_id => $item_data) {
        $productName[] = $item_data->get_name();
    }

    $total = convertNumbers(number_format($order->get_total()));
    $pName = implode(" , ", $productName);

    $text = " سلام {$userData->last_name}
       سفارس {$order->get_id()} دریافت شد و هم اکنون در وضعیت در حال انجام می باشد.
        آیتم های سفارش :
       {$pName}
       مبلغ سفارش : {$total} تومان";

    $text_admin = sprintf('سفارش  %s به شماره %s ثبت شد
آیتم های سفارش : %s 
مبلغ سفارش : %s  تومان
از اپلیکیشن درگاه' , $userData->first_name , $order->get_id() , $pName , $total );

    $order->save();




    wp_remote_post( $path, array(
        'method' => 'POST',
        'timeout' => 45,
        'headers' => array(),
        'body' => array(
            'username' => '09014332145',
            'password' => 'Saha12024680Ary@1052417',
            'to' => '09109399490',
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


    ?>

    <a href="intent://123#Intent;scheme=galshop;package=com.sabadino.android;end" class="re_hossein_galshop">رفتن به فروشگاه </a>
    <script type="text/javascript">
        document.getElementsByClassName("re_hossein_galshop")[0].click();
    </script>
    <?php



}





