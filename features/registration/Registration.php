<?php

namespace Sabadino\features\registration;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Sabadino\includes\Functions;

class Registration
{
    public static $prefixNumber = [];

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {

        add_action( 'woocommerce_account_dashboard', [$this ,'za_account_links'], 10 );

        add_shortcode('za_registration_icon' , [$this ,'iconHtml']);
        add_action('wp_footer' , [$this ,'formHtml']);
        add_filter( 'woocommerce_save_account_details_required_fields', [$this , 'hideUserFields'] );
        add_action('template_redirect', [$this , 'redirectGuest']);



        add_action( 'wp_ajax_nopriv_re_confirm_code', [$this , 'confirmCode']  );
        add_action( 'wp_ajax_nopriv_re_send_phone'  , [$this , 'sendPhone' ] );



    }


    public static function iconHtml( $echo = true )
    {
        $currentID = get_current_user_id();
        $name = $currentID  == 0   ? 'ورود' :  self::getName( $currentID );
        $html = '
        <div id="re_profile_con">
            <a href="javascript:void(0)">  
                <span>'. $name .' </span>
                <svg width="18" height="20"  xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                     viewBox="0 0 512 512"  xml:space="preserve">
                    <g>
                        <g>
                            <path d="M437.02,330.98c-27.883-27.882-61.071-48.523-97.281-61.018C378.521,243.251,404,198.548,404,148
                                C404,66.393,337.607,0,256,0S108,66.393,108,148c0,50.548,25.479,95.251,64.262,121.962
                                c-36.21,12.495-69.398,33.136-97.281,61.018C26.629,379.333,0,443.62,0,512h40c0-119.103,96.897-216,216-216s216,96.897,216,216
                                h40C512,443.62,485.371,379.333,437.02,330.98z M256,256c-59.551,0-108-48.448-108-108S196.449,40,256,40
                                c59.551,0,108,48.448,108,108S315.551,256,256,256z"/>
                        </g>
                    </g>
                </svg>
            </a>
        </div>';
        if ( !$echo ){
            return $html;
        }
        echo $html;
    }

    public function formHtml()
    {
        $form = ' 
           <div id="re_registration_first"> 
              <div class="registerCode">
                 <div>
                    <a href="javascript:void(0)" id="cancel_register">
                       <svg xmlns="http://www.w3.org/2000/svg"   x="0px" y="0px" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve">
                          <g>
                             <g>
                                <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717
                                   L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859
                                   c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287
                                   l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285
                                   L284.286,256.002z"/>
                             </g>
                          </g>
                      </svg>
                   </a>
              </div>
        
              <div>
                 <svg fill="#ff6233" id="Capa_1" xmlns="http://www.w3.org/2000/svg"  x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                    <g>
                       <g>
                          <path d="M437.02,330.98c-27.883-27.882-61.071-48.523-97.281-61.018C378.521,243.251,404,198.548,404,148
                             C404,66.393,337.607,0,256,0S108,66.393,108,148c0,50.548,25.479,95.251,64.262,121.962
                             c-36.21,12.495-69.398,33.136-97.281,61.018C26.629,379.333,0,443.62,0,512h40c0-119.103,96.897-216,216-216s216,96.897,216,216
                             h40C512,443.62,485.371,379.333,437.02,330.98z M256,256c-59.551,0-108-48.448-108-108S196.449,40,256,40
                             c59.551,0,108,48.448,108,108S315.551,256,256,256z"/>
                       </g>
                    </g>
                 </svg>
                   <p>  ورود/عضویت </p>
              </div>
          </div>
        
              <div class="registerCode">
                  <label for="re_phone">شماره موبایل خود را وارد کنید : <span style="color:#FF6233"> * </span></label>
                  <input type="tel"  placeholder="09109399490" id="re_phone" maxlength="11" >
              </div>
        
              <div class="registerCode">
                  <a href="javascript:void(0)" id="re_send_phone">ارسال کد تایید</a>
              </div>
         </div> ';


        $confirmForm = '
            <div id="re_registration_first">
               <div class="confirmCode">
                  <div>
                     <a href="javascript:void(0)" class="re_resend_code">
                        <svg version="1.1"   fill="#ff6233" xmlns="http://www.w3.org/2000/svg"   x="0px" y="0px"
                            viewBox="0 0 490.787 490.787" style="enable-background:new 0 0 490.787 490.787;" xml:space="preserve">
                        <path   d="M362.671,490.787c-2.831,0.005-5.548-1.115-7.552-3.115L120.452,253.006
                           c-4.164-4.165-4.164-10.917,0-15.083L355.119,3.256c4.093-4.237,10.845-4.354,15.083-0.262c4.237,4.093,4.354,10.845,0.262,15.083
                           c-0.086,0.089-0.173,0.176-0.262,0.262L143.087,245.454l227.136,227.115c4.171,4.16,4.179,10.914,0.019,15.085
                           C368.236,489.664,365.511,490.792,362.671,490.787z"/>
                        <path d="M362.671,490.787c-2.831,0.005-5.548-1.115-7.552-3.115L120.452,253.006c-4.164-4.165-4.164-10.917,0-15.083L355.119,3.256
                           c4.093-4.237,10.845-4.354,15.083-0.262c4.237,4.093,4.354,10.845,0.262,15.083c-0.086,0.089-0.173,0.176-0.262,0.262
                           L143.087,245.454l227.136,227.115c4.171,4.16,4.179,10.914,0.019,15.085C368.236,489.664,365.511,490.792,362.671,490.787z"/>
                        </svg>
                        <span>برگشت</span>
                     </a>
                  </div>
                  <div class="re_register_notice_con">
                     <p>کد تایید به شماره
                        <span id="phone_number"></span>
                        ارسال شد
                     </p>
                     <p id="code_error_msg">  </p>
                  </div>
               </div> 
               <div class="confirmCode">
                  <input type="tel" placeholder="کد تایید را وارد کنید" id="re_code" maxlength="4" >
               </div> 
               <div class="confirmCode">
                  <a href="javascript:void(0)" id="re_confirm_code" class="btnSendColorFalse">تایید</a>
               </div> 
               <div class="confirmCode resend-code-con">
                  <p class="re_timer_con">برای ارسال مجدد کد
                    <span id="re_timer">1</span> ثانیه صبر کنید
                  </p>
               </div> 
            </div>';

        wp_enqueue_script(
            'za_sabadino_register_script' ,
            ZA_ROOT_URL.'/features/registration/assets/registration.js',
            ['jquery'],
            ZA_SABADINO_SCRIPTS_VERSION ,
            false
        );

        wp_enqueue_style(
            'za_sabadino_register_style' ,
            ZA_ROOT_URL.'/features/registration/assets/registration.css',
            [],
            ZA_SABADINO_SCRIPTS_VERSION
        );

        wp_localize_script(
            'za_sabadino_register_script' ,
            're_registration' ,
                [
                'button_name'     => self::iconHtml(false ) ,
                'register_status' => !empty( get_current_user_id() ) ? 1 : 0  ,
                'profileWOO'      => get_permalink( get_option('woocommerce_myaccount_page_id')) ,
                'admin_url'       => admin_url( 'admin-ajax.php' ) ,
                'ajax_nonce'      => wp_create_nonce('re_registration_nonce') ,
                'reg'             => get_current_user_id() ,
                'confirmForm'     => $confirmForm,
                'form'            => $form
                ]
        );
    }

    public static function getName( $userID )
    {
        if ( $userID < 1  ) return true ;

        $userData  = get_userdata(  $userID );
        if( !empty( $userData->first_name)  ){
            return $userData->first_name;

        }elseif( !empty( $userData->last_name) ){
            return $userData->last_name;

        }else{
            return $userData->user_login;
        }
    }

    public function hideUserFields( $required_fields ) {
        unset($required_fields["account_display_name"]);
        unset($required_fields["account_last_name"]);
        return $required_fields;
    }

    public function redirectGuest()
    {
        if( !is_admin() ){
            global $wp;
            $r_url =  explode('/' , $_SERVER['REQUEST_URI'] );
            if (
                in_array( 'my-account'  , $r_url ) ||
                $wp->request == 'cart'                    ||
                $wp->request == 'checkout'    ) {
                if( get_current_user_id() <= 0 ) {
                    wp_redirect( home_url() );
                }
            }
        }
    }
 
    public static function sendCode( $phone ,$ranCode ,$text ){

        $result_serv = Functions::sendSmsByService( $text ,$phone );
        if ( !$result_serv ){
            $result_ads = Functions::sendSmsByAds( $text ,$phone );
            if ( !$result_ads ){
                return false;
            }
        }
        return  true;
    }

    public static function registrationOption()
    {
        $option = get_option('re_registration_code');
        if( !empty( $option ) ){
            return maybe_unserialize( $option );
        }
        add_option('re_registration_code' ,maybe_serialize([]) );
        return [];
    }

    public static function sendPhone() {
        if ( !isset( $_POST['ajax_nonce'] ) && wp_verify_nonce(  $_POST['ajax_nonce'] , 'ajax_nonce' ) ){
            wp_send_json( ['status'=> 403  ,'msg' => 'Nonce Error' ] , 403);
        }
        date_default_timezone_set('Asia/Tehran');

        if ( isset( $_POST['phone'] ) && !empty( $_POST['phone']  ) ){
            global $wpdb;
            $mobile         = $_POST['phone'];
            $register_title = 'ثبت نام';
            $user_data      = self::getUsersDetails( $_POST['phone'] );
            $status         = $old_code = false;
            $ranCode        = rand( 1000 , 9999 );
            $user_table     = $wpdb->prefix.'sabadino_users_action';
            $responseResult = true;


            if ( !empty( $user_data ) ){
                if ( $user_data->is_blocked == 0 ){
                    if ( (int) $user_data->send_sms_count < 5 ){
                        $wpdb->update(
                            $user_table ,[
                                'send_sms_count' => $user_data->send_sms_count + 1 ,
                                'try_count' => 0 ,
                                'update_at' =>  date('Y-m-d H:i:s' ,strtotime('now') )
                            ] ,
                            [ 'id' =>  $user_data->id ]
                        );
                        $status = true;
                    }else if ( $user_data->update_at  < date('Y-m-d H:i:s' ,strtotime('-1 hour') ) ){
                        $wpdb->update(
                            $user_table ,[
                                'send_sms_count' => 1 ,
                                'try_count' => 0 ,
                                'update_at' => date('Y-m-d H:i:s' ,strtotime('now') )
                                ],
                                [ 'id' =>  $user_data->id ] );
                        $status = true;
                    }else{
                        $old_code = true;
                    }
                }else{
                    wp_send_json( ['result' =>[ 'text' => 'متاسفانه شما بلاک هستید ( لطفا با پشتیبانی تماس گیرید)' ,'status' => 403 ] ] , 200 );
                }
            }else{
                $inserted_id = self::insertUserAction( $mobile ,$ranCode );
                if( is_numeric( $inserted_id ) ){
                    $status = true;
                }
            }

            if ( $status || $old_code ){
                if( isset( $user_data->user_id ) ){
                    $register_title = 'ورود';
                }
                $text = sprintf('%s  کد  %s  شما %s  '.get_bloginfo('name') ,
                    Functions::convertNumbers( $ranCode )
                    , $register_title
                    , $register_title =='ورود' ? 'به' : 'در'
                );
                if ( !$old_code ){
                    $responseResult = self::sendCode( $mobile ,$ranCode ,$text );
                }else{
                    $ranCode = $user_data->last_code;
                }
                if ( $responseResult ){
                    $wpdb->update(
                            $user_table ,[
                                'last_code' => $ranCode
                            ],
                            [ 'id' =>  $user_data->id ] );
                    wp_send_json( ['result' =>[ 'btnText' => $register_title ,'old_code' => $old_code ,'status' => 200 ] ] ,200  );
                }else{
                    wp_send_json( ['result' =>[ 'text' => 'خطا هنگام ارسال کد' ,'status' => 500 ] ] , 200 );
                }
            }else{
                wp_send_json( ['result' => [ 'text' => 'شماره مجاز نیست ' ,'status' => 200]]  );
            }
        }
    }


    public function confirmCode()
    {
        date_default_timezone_set('Asia/Tehran');
        if ( !isset( $_POST['ajax_nonce'] ) && wp_verify_nonce(  $_POST['ajax_nonce'] , 'ajax_nonce' ) ){
            wp_send_json(array('status'=> 403  ,'msg' => 'Nonce Error' ) , 403 );
        }
        global $wpdb;
        $status     = false;
        $phone      = sanitize_text_field( $_POST['phone'] );
        $inter_code = sanitize_text_field( $_POST['interCode'] );
        $user_data  = self::getUsersDetails( $phone );
        $user_table = $wpdb->prefix.'sabadino_users_action';

        if ( $user_data->is_blocked == 0 ){
            if ( $user_data->update_at < date('Y-m-d H:i:s' ,strtotime('-1 Hours') ) ) {
                $wpdb->update( $user_table ,[ 'try_count' => 0 ],[ 'id' =>  $user_data->id ] );
            }
            if ( $user_data->try_count < 5 ){
                if ( $user_data->last_code == $inter_code ){
                    $wpdb->update( $user_table ,[ 'try_count' => 0 ],[ 'id' =>  $user_data->id ] );
                    $status = true;
                }else{
                    $wpdb->update( $user_table ,[ 'try_count' => $user_data->try_count + 1  ],[ 'id' =>  $user_data->id ] );
                    wp_send_json( ['result' => [ 'text' => 'کد وارد شده اشتباه است ' ,'status' => 400 ]] );
                }
            }else{
                wp_send_json( ['result' => [ 'text' => 'خطای کد اشتباه بیش از حد (مدتی بعد تلاش کنید)' ,'status' => 403 ]] );
            }
        }else{
            wp_send_json( ['result' => [ 'text' => 'متاسفانه شما بلاک هستید' ,'status' => 403 ]] );
        }

        if ( $status ){
            $user_check = get_user_by( 'login' ,$user_data->mobile );

            if( !empty( $user_check ) ){
                $mobile = get_user_meta( $user_check->ID , 'billing_phone' ,true );
                if ( empty( $mobile ) ){
                    add_user_meta( $user_check->ID , 'billing_phone' ,$user_data->mobile );
                }
                $user_id = $user_check->ID;
                wp_set_current_user ( $user_id );
                wp_set_auth_cookie  ( $user_id ,true, false );

            }else{
                $new_user = [
                    'user_login' => $user_data->mobile,
                    'display_name' => $user_data->mobile,
                    'user_pass'  => $user_data->mobile.'za_' ,
                    'nickname' => $user_data->mobile,
                    'role' => 'customer'
                ];
                $user_id = wp_insert_user( $new_user );

                $metas = [
                    'billing_phone' => $user_data->mobile ,
                    're_favorite'   => '' ,
                    're_location'   => ''
                ];

                foreach( $metas as $key => $value ) {
                    update_user_meta( $user_id , $key, $value );
                }
                clean_user_cache( (int) $user_id);
                wp_clear_auth_cookie();
                wp_set_current_user ( $user_id);
                wp_set_auth_cookie  ( $user_id ,true, true );
            }
            $wpdb->update( $user_table ,[ 'user_id' => $user_id ,'try_count' => 1  ],[ 'id' =>  $user_data->id ] ); 
            wp_send_json( ['result' =>[ 'userID' => $user_id ,'url' => home_url() ,'status' => 200 ] ] ,200  );
        }
    }


    public static function logoutIntruders()
    {
        $phone     = get_user_meta( get_current_user_id() ,'billing_phone' , true );
        $option    = get_option( 'intruders' , true );
        $option    = !empty( $option ) ? $option : [];
        $intruders = maybe_unserialize( $option );
        $intruders = empty( $intruders ) ? [] : $intruders;
        if( !empty( $phone ) && in_array( substr( $phone, 0, 4 ) , $intruders  ) ){
            wp_logout();
        }
    }

    public static function za_account_links() {
        ?>
        <div class="sabadino-my-account-links">
            <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                <div class="<?php echo esc_attr( $endpoint ); ?>-link">
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }


    public static function getUsersDetails( $mobile )
    {
        $mobile = self::ConvertNumberToEnglish(  sanitize_text_field( $mobile ) );
        if ( !empty( $mobile ) && is_numeric( $mobile ) ) {
            $is_valid  = false;
            $number    = '';
            $phoneUtil = PhoneNumberUtil::getInstance();

            try {
                $number = $phoneUtil->parse( $mobile ,"IR" );
            } catch (NumberParseException $e) {
                wp_send_json( ['result' =>[ 'text' => 'شماره نامعتبر' ,'status' => 403 ] ] , 200 );
            }

            if ( !$phoneUtil->isValidNumber( $number ) ) {
                try {
                    $number = $phoneUtil->parse('00' . $mobile, "IR" );
                } catch ( NumberParseException $e ) {
                    wp_send_json( ['result' =>[ 'text' => 'شماره نامعتبر' ,'status' => 403 ] ] , 200 );
                }
                if ( $phoneUtil->isValidNumber( $number ) ) {
                    $is_valid = true;
                }
            }else{
                $is_valid = true;
            }
            if( $is_valid ){
                global $wpdb;
                $user_table = $wpdb->prefix.'sabadino_users_action';
                return $wpdb->get_row(
                        "SELECT * FROM {$user_table} WHERE mobile = {$mobile} ;"
                );
            }
        }
        wp_send_json( ['result' =>[ 'text' => 'شماره نامعتبر' ,'status' => 403 ] ] , 200 );
    }

    public static function insertUserAction( $mobile ,$code )
    {
        global $wpdb;
        $user_table = $wpdb->prefix.'sabadino_users_action';
        $data = [
            'mobile' => self::ConvertNumberToEnglish( $mobile ) ,
            'send_sms_count' => 1 ,
            'try_count' => 0 ,
            'update_at' => date('Y-m-d H:i:s' ,strtotime('now') ) ,
            'last_code' => $code
        ];
        $format = ['%s' ,'%d'];
        $wpdb->insert( $user_table ,$data ,$format );
        return $wpdb->insert_id;
     }

    public static function ConvertNumberToEnglish( $string ) {
        $persian_num = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $latin_num   = range( 0, 9 );
        $string=str_replace(['٤', '٥', '٦','٨'],['4','5','6','8'],$string);
        $string      = str_replace( $persian_num,$latin_num,  $string );
        return $string;
    }

}