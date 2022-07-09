<?php


/*
Plugin Name: درگاه ایران کیش 2
Description: Provides simple front end registration and login forms
Version: 1.0
Author: Reza Hossein Pour
*/



 $fileName = explode('/' , $_SERVER['REQUEST_URI'] );
 
  
 if( isset($_POST['ResCode']) && end($fileName) == 're_in_portal.php' ){ 
  

     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
     include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php';
     header('Content-Type: text/html; charset=utf-8');
     $ResCode 		= (isset($_POST['ResCode']) && $_POST['ResCode'] != "") ? $_POST['ResCode'] : ""; 
   
     if ($ResCode == '0')
     {
       
         $client 				= new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl'); 
         $namespace 			 ='http://interfaces.core.sw.bps.com/';
         $orderId 				= (isset($_POST['SaleOrderId']) && $_POST['SaleOrderId'] != "") ? $_POST['SaleOrderId'] : "";
         $verifySaleOrderId 		= (isset($_POST['SaleOrderId']) && $_POST['SaleOrderId'] != "") ? $_POST['SaleOrderId'] : "";
         $verifySaleReferenceId 	= (isset($_POST['SaleReferenceId']) && $_POST['SaleReferenceId'] != "") ? $_POST['SaleReferenceId'] : "";

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
                 re_add_order_in_verify( $orderId );
 
             } else {
                 $client->call('bpReversalRequest', $parameters, $namespace); 
                 re_pay_fail( $result );
             }
         } else {
             $client->call('bpReversalRequest', $parameters, $namespace); 
           re_pay_fail( $result );
         }
     } else { 
         re_pay_fail( $result );
     }
 }






function re_add_order_in_verify(  $orderId ){

    global $wpdb;
    $cart_table_name = 'wp_re_cart';
    $result = $wpdb->get_results("SELECT * FROM {$cart_table_name} WHERE order_id='{$orderId}'");
   

    $user_id    = (int) $result[0]->user_id;
    $coupon     =       $result[0]->coupon;
    $time       =       $result[0]->time;
    $location   =       unserialize($result[0]->location);
//    $intro_code =  false;
//    $table_intro_name = 'wp_re_introduce_code';
    $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";



    $userData  = get_user_by('id' ,  $user_id );

    $userPhone  = get_user_meta($user_id, 'billing_phone', true);
    $order_note = get_user_meta($user_id, 'order_note', true);
    $addr       = get_user_meta($user_id, 'billing_address_1', true);

    $address = [
        'first_name' => $userData->first_name,
        'last_name' => $userData->last_name,
        'email' => $userData->user_email,
        'phone' => $userPhone,
        'address_1' => $addr
    ];

    $order = wc_create_order(array('customer_id' => $user_id));
    $detailsStatus = true;
    try {
        $order->set_payment_method('iran_kish_2');
        $order->set_payment_method_title('پرداخت موفق از درگاه ایران کیش');
        $order->add_order_note($order_note);

        $order->set_address($address, 'billing');
    } catch (Exception $e) {
        $detailsStatus = false;
    }



    $order->update_status("completed", 'Imported order', true);

    $products = unserialize($result[0]->products );
 
    foreach ($products as $item => $count) {
        $order->add_product(wc_get_product($item), $count);
    }


//            $re_sell_items = (int)get_user_meta($user_id, 're_score_items', true);
    $re_sell_items = 0;
    if ($re_sell_items >= 5 ) {

        $co = new WC_Coupon('introduce_code_discount');
        if ($order->apply_coupon($co->get_code()) === true) {
            $co->save();
            $intro_code = true;
        }

    } else {
        if (!empty($coupon)) {
            $order->apply_coupon($coupon);
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
 

    update_post_meta( $order->get_id() , 're_micro_time' , date('U' ,strtotime('+1 day' ) )  );
  
 
    $order->calculate_totals(); 



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
                'pay'           => 11 , 
            ) ,
            array(
                'order_id' => $orderId
            )
        );
  
    ?>
    <a href="intent://123#Intent;scheme=galshop;package=com.sabadino.android;end" class="re_hossein_galshop">رفتن به فروشگاه </a>
    <script type="text/javascript">
             document.getElementsByClassName("re_hossein_galshop")[0].click();
     </script>

    <?php



}





function re_pay_fail($error){
    if ($error == 110){
        $er ='انصراف از خرید' ;
    }elseif ($error == 100){
        $er ='خطای داخلی ' ;
    }else{
        $er = 'خطا در سرور درگاه ';
    }
    ?>
    <!doctype html>
    <html lang="fa">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>صفحه درگاه</title>


        <style>

            .fail-con{
                width: 90%;
                margin: 0 auto;
                box-shadow: 0 0 10px #ccc;
                padding-top: 30px;
                margin-top: 20px;
            }
            .fail-con svg{
                width: 250px;
                margin: 0 auto;
                display: block;
            }

            .fail-con .header{
                width: 100%;
                text-align: center;
            }
            .fail-con .header h2{
                text-shadow: 0 0 5px #ddd;
            }
            .fail-con .content{
                width: 100%;
                color: #CD0909;
                font-size: 25px;
                text-align: center;
            }
            .fail-con .notice{
                background-color: #eee;
                text-align: center;
                padding: 2px 0;
            }


        </style>

    </head>
    <body>
    <div class="fail-con">
        <div class="header">
            <h2>پرداخت ناموفق</h2>
            <h6><?php echo $_POST['resultCode']; ?></h6>
        </div>
        <div class="icon">
            <svg id="Capa_1" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 511 455">
                <defs>
                    <style>.cls-1{fill:#ff6233;}</style>
                </defs>
                <path d="M471.5,28H39.5A39.55,39.55,0,0,0,0,67.5v256A39.55,39.55,0,0,0,39.5,363h160a7.5,7.5,0,0,0,0-15H39.5A24.53,24.53,0,0,1,15,323.5V67.5A24.53,24.53,0,0,1,39.5,43h432A24.53,24.53,0,0,1,496,67.5v256a7.5,7.5,0,0,0,15,0V67.5A39.55,39.55,0,0,0,471.5,28Z"
                      transform="translate(0 -28)"/>
                <path d="M207.5,292H63.5a7.5,7.5,0,0,0,0,15h144a7.5,7.5,0,0,0,0-15Z" transform="translate(0 -28)"/>
                <path class="cls-1"
                      d="M151,155.5v-32A23.52,23.52,0,0,0,127.5,100h-48A23.52,23.52,0,0,0,56,123.5v32A23.52,23.52,0,0,0,79.5,179h48A23.52,23.52,0,0,0,151,155.5Zm-80,0V147h8.5a7.5,7.5,0,0,0,0-15H71v-8.5a8.51,8.51,0,0,1,8.5-8.5H96v49H79.5A8.51,8.51,0,0,1,71,155.5Zm56.5,8.5H111V115h16.5a8.51,8.51,0,0,1,8.5,8.5V132h-8.5a7.5,7.5,0,0,0,0,15H136v8.5A8.51,8.51,0,0,1,127.5,164Z"
                      transform="translate(0 -28)"/>
                <path d="M56,251.5a7.5,7.5,0,0,0,15,0v-16a7.5,7.5,0,0,0-15,0Z" transform="translate(0 -28)"/>
                <path d="M80,235.5v16a7.5,7.5,0,0,0,15,0v-16a7.5,7.5,0,0,0-15,0Z" transform="translate(0 -28)"/>
                <path d="M104,235.5v16a7.5,7.5,0,0,0,15,0v-16a7.5,7.5,0,0,0-15,0Z" transform="translate(0 -28)"/>
                <path d="M128,235.5v16a7.5,7.5,0,0,0,15,0v-16a7.5,7.5,0,0,0-15,0Z" transform="translate(0 -28)"/>
                <path d="M175,251.5v-16a7.5,7.5,0,0,0-15,0v16a7.5,7.5,0,0,0,15,0Z" transform="translate(0 -28)"/>
                <path d="M199,251.5v-16a7.5,7.5,0,0,0-15,0v16a7.5,7.5,0,0,0,15,0Z" transform="translate(0 -28)"/>
                <path d="M215.5,228a7.5,7.5,0,0,0-7.5,7.5v16a7.5,7.5,0,0,0,15,0v-16A7.5,7.5,0,0,0,215.5,228Z"
                      transform="translate(0 -28)"/>
                <path d="M247,251.5v-16a7.5,7.5,0,0,0-15,0v16a7.5,7.5,0,0,0,15,0Z" transform="translate(0 -28)"/>
                <path d="M415.5,179a39.5,39.5,0,0,0,0-79h-48a39.5,39.5,0,0,0,0,79ZM343,139.5A24.53,24.53,0,0,1,367.5,115h48a24.5,24.5,0,0,1,0,49h-48A24.53,24.53,0,0,1,343,139.5Z"
                      transform="translate(0 -28)"/>
                <path class="cls-1"
                      d="M351.5,228A127.5,127.5,0,1,0,479,355.5,127.65,127.65,0,0,0,351.5,228Zm0,240A112.5,112.5,0,1,1,464,355.5,112.63,112.63,0,0,1,351.5,468Z"
                      transform="translate(0 -28)"/>
                <path class="cls-1"
                      d="M412.8,294.2a7.49,7.49,0,0,0-10.6,0l-50.7,50.69L300.8,294.2a7.5,7.5,0,0,0-10.6,10.6l50.69,50.7L290.2,406.2a7.5,7.5,0,1,0,10.6,10.6l50.7-50.69,50.7,50.69a7.5,7.5,0,0,0,10.6-10.6l-50.69-50.7,50.69-50.7A7.49,7.49,0,0,0,412.8,294.2Z"
                      transform="translate(0 -28)"/>
            </svg>
        </div>

        <div class="content">
            <p><?php echo $er; ?></p>
        </div>
        <div class="notice">
            <span>برای بازگشت به برنامه از دکمه  برگشت استفاده کنید</span>
        </div>
    </div>


    </body>
    </html>
    <?php 
  




}
function convertNumbers($srting, $toPersian=true)
{
    $en_num = array('0','1','2','3','4','5','6','7','8','9');
    $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    if( $toPersian )
        return str_replace($en_num, $fa_num, $srting);
    else
        return str_replace($fa_num, $en_num, $srting);
}








