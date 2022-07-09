<?php


namespace Sabadino\includes;





use Exception;
use Melipayamak\MelipayamakApi;  

class Functions
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
        add_shortcode('html_tag' ,[$this ,'echoHtml' ]  );

        if (!function_exists('jdate') ){
            include ZA_ROOT_PATH.'vendor/jdate/jdate.php';
        }


    }

    public function echoHtml($content , $atts)
    {
        echo $content;
    }

    public static function isPage( $page )
    {
        $server = $_SERVER['REQUEST_URI'];
        $server = explode( '/' ,$server );
        return in_array( $page ,$server );
    }

    public static function sendSmsByAds( $text  ,$user )
    {
        $path = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";
        $response = wp_remote_post( $path, [
            'method' => 'POST',
            'timeout' => 45,
            'headers' => [],
            'body' => [
                'username' => '09014332145',
                'password' => 'Saha12024680Ary@1052417',
                'to' => $user ,
                'from' => '50004000332145',
                'text' => $text,
                'isFlash' => false,
            ],
            'cookies' => [] ]
        );
        $result = json_decode( $response['body'] ); 
        return $result->Value > 1000;
    }

    public static function sendSmsByService( $code ,$user )
    {
        try{
            $username = '09014332145';
            $password = 'Saha12024680Ary@1052417';
            $api = new MelipayamakApi($username,$password);
            $sms = $api->sms();
            $to = $user;
            $from = '50004000332145';
            $text = $code;
            $response = $sms->send($to,$from,$text);
            add_option('re_sms_error' ,$response);
            $json = json_decode($response);
           add_option('re_sms_errodddr' ,$json);
            return $json->Value > 1000;
        }catch(Exception $e){
          add_option('re_sms_errodddr' ,$e);
            return false;
        }
    } 
  

    public static function statusTranslater( $status )
    {
        $status_or = '';
        switch ( $status ){
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
        return $status_or;
    }


    public static function convertNumbers( $string ,$toPersian = true )
    {
        $en_num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $fa_num = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        if ( $toPersian )
            return str_replace( $en_num ,$fa_num ,$string );
        else
            return str_replace( $fa_num ,$en_num ,$string );
    }


    public static function indexChecker( $data ,$index ,$default = '' )
    {
        if( !empty( $data ) && is_array( $data ) && isset( $data[$index] ) ){
            return $data[$index];
        }elseif ( !empty( $data ) && is_object( $data ) && isset( $data->$index ) ){
            return $data->$index;
        }
        return $default;
    }


    public static function getPaymentType( $order )
    {
        if ( !empty( $order ) && is_object( $order ) ){
            $payment_method = $order->get_payment_method();
            if ( !empty( $order->get_payment_method_title() ) ){
                return $order->get_payment_method_title() ;
            }
            elseif ( $payment_method == 'cod' ){
                return 'پرداخت آنلاین';
            }
            elseif ( $payment_method == 'iran_kish_2' ){
                return 'پرداخت در محل';
            }
            elseif ( $payment_method == 'WC_BehPardakht' ){
                return 'پرداخت در محل';
            }
            elseif ( $payment_method == 'WC_tally' ){
                return 'پرداخت در محل';
            }

        }
        return 'نامشخص';
    }


//cod
//WC_BehPardakht
//place
//WC_BehPardakht
//WC_BehPardakht
//cod
//WC_BehPardakht
//WC_BehPardakht
//WC_tally
//WC_BehPardakht
//portal




}