<?php


namespace Sabadino\features\analytics\includes;


class SendSMSProcessor
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
        self::sendSMS();
    }

    public static function sendSMS()
    { 
 
        global $wpdb;
        $sms_lists  = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '%_za_final_user_list_%' ");

        foreach ( $sms_lists as $sms_list ){
            $sms_value = maybe_unserialize( $sms_list->option_value  );

            if(  $sms_value['status'] == true && is_array( $sms_value['items'] ) &&
                 !empty( $sms_value['items'] )  && $sms_value['date'] < date_i18n('Y-m-d H:i:s' ) ){
                foreach ( $sms_value['items'] as $item_key => $item ){
                    if( (int)$item['sms_count'] <= (int)$sms_value['send_count'] && $item['sms_try'] < 3 ){
                        if ( isset( $item['phone'] ) && strlen( $item['phone'] ) > 8 && isset( $sms_value['message'] ) ){
                            $messege = $sms_value['message'];

                            if( strpos( $messege ,'[f_name]'  ) !== false ){
                                if ( isset( $item['first_name'] ) && strlen( $item['first_name'] ) >= 2 ){
                                    $messege = str_replace( '[f_name]' ,$item['first_name'] , $messege );
                                }else{
                                    $messege = str_replace( '[f_name]' , 'مشتری' , $messege );
                                }
                            }

                            if( strpos( $messege , '[l_name]' ) !== false ){
                                if ( isset( $item['last_name'] ) && strlen( $item['last_name'] ) >= 2 ){
                                    $messege = str_replace( '[l_name]' ,$item['last_name']  , $messege );
                                }else{
                                    $messege = str_replace( '[l_name]' ,''  , $messege );
                                }
                            }

                            if( strpos( $messege ,'[total_amount]' ) !== false ){
                                if ( isset( $item['line_total'] ) && strlen( $item['line_total'] ) > 2 ){
                                    $messege = str_replace( '[total_amount]' , number_format( (int) $item['line_total'] ) , $messege );
                                }else{
                                    $messege = str_replace( '[total_amount]' ,'' , $messege );
                                }
                            }

                            if( strpos(  $messege ,'[total_count]'  ) !== false ){
                                if ( isset( $item['order_count'] ) ){
                                    $messege = str_replace( '[total_count]' ,(int) $item['order_count'] , $messege );
                                }else{
                                    $messege = str_replace( '[total_count]' ,  '' , $messege );
                                }
                            }

                            if( strpos( $messege  ,'[register_date]'  ) !== false ){
                                if ( isset( $item['register_date'] ) && strlen( $item['register_date'] ) >= 4 ){
                                    $messege = str_replace( '[register_date]' , date_i18n('Y-m-d' , strtotime($item['register_date']) ) , $messege );
                                }else{
                                    $messege = str_replace( '[register_date]' ,'' , $messege );
                                }
                            }

                            if( strpos( $messege , '[first_buy]' ) !== false ){

                                if ( isset( $item['order_count'] ) && isset( $item['orders'][$item['order_count']]['date'] ) ){
                                    $messege = str_replace( '[first_buy]' ,
                                                date_i18n('Y-m-d' , strtotime( $item['orders'][$item['order_count']]['date'] ) )  , $messege );
                                }else{
                                    $messege = str_replace( '[first_buy]' ,'' , $messege );
                                }

                            }

                            if( strpos( $messege ,'[last_buy]' ) !== false ){
                                if ( isset( $item['orders'][1]['date'] ) ){
                                    $messege = str_replace( '[last_buy]' ,
                                                date_i18n('Y-m-d' , strtotime( $item['orders'][1]['date'] ) )  , $messege );
                                }else{
                                    $messege = str_replace( '[last_buy]' ,'' , $messege );
                                }
                            }

                            if( strpos( $messege ,'[product_list]' ) !== false ){
                                $products_items  = [];
                                if ( !empty( $item['orders'] ) ){
                                    foreach ( $item['orders'] as $key => $val  ){
                                        if( isset( $val['items'] )  && is_array( $val['items'] )){
                                            $products_items = array_merge( $products_items ,  array_keys( $val['items'] ) ) ;
                                        }
                                    }
                                }
                                if ( !empty( $products_items ) ){
                                    $products_items = array_unique( $products_items );
                                    $p_names = '';
                                    foreach ( $products_items as $p_item ){
                                        $p_names .= get_post( (int) $p_item )->post_title;
                                    }
                                    $messege = str_replace( '[product_list]' , $p_names , $messege );
                                }else{
                                    $messege = str_replace( '[product_list]' , '' , $messege );
                                }
                            }

                            if( strpos( $messege ,'[usrt_id]' ) !== false ){
                                if ( !empty( $item_key )){
                                    $messege = str_replace( '[usrt_id]' , $item_key , $messege );
                                }else{
                                    $messege = str_replace( '[usrt_id]' ,'' , $messege );
                                }
                            }

                            if( strpos( $messege ,'[phone]' ) !== false ){
                                if ( isset( $item['phone'] ) ){
                                    $messege = str_replace( '[phone]' , $item['phone']  , $messege );
                                }else{
                                    $messege = str_replace( '[phone]' ,''       , $messege );
                                }
                            }



                            $result = self::smsHandler(  $item['phone'] ,$messege );
                            if ( $result === 1 ){
                                $item['sms_count'] = $item['sms_count'] + 1;
                            }else{
                                $item['sms_try']   = $item['sms_try'] + 1;
                            }
                        }
                    }
                    $sms_value['items'][$item_key] = $item;
                }
                $sms_value['sms_count'] = $sms_value['sms_count']  + 1;
                update_option(  $sms_list->option_name , $sms_value );
            }
        }
    }


    public static function smsHandler( $phone , $message )
    {

        return true;
        $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";
        $response = wp_remote_post( $path, [
                'method'  => 'POST',
                'timeout' => 45,
                'headers' => [],
                'body'   => [
                    'username' => '09014332145*',
                    'password' => '6608',
                    'to'       => $phone,
                    'from'     => '50004000332145',
                    'text'     => $message,
                    'isFlash'  => false,
                ],
                'cookies' => []
            ]
        );

        $result =  json_decode( $response['body'] );
        return $result->Value;

    }





}