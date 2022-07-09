<?php




// Visit Action
function re_home_check_affiliate() {

    if ( is_front_page() ) {
        if (isset($_GET['affiliate'])){
            $affiliate = $_GET['affiliate'];


            if (!isset( $_COOKIE['affiliate'] )) {
                setcookie('affiliate', $affiliate, time() + (86400 * 180));
            }

            global $wpdb;
            $aff_profile = $wpdb->prefix . "re_aff_profile";
            $retrieve_profile_data = $wpdb->get_results( "SELECT * FROM $aff_profile WHERE aff_token='".$affiliate."';" );

            $aff_user_id = $retrieve_profile_data[0]->user_id;

            $aff_visit = $wpdb->prefix . "re_aff_visits_report";
            $today =    jdate("Y_m_d", '' , '' , '' ,'en');

            $retrieve_data = $wpdb->get_results( "SELECT * FROM $aff_visit WHERE date_register='".$today."' and aff_user_id={$aff_user_id};" );

            if ( empty($retrieve_data ) ){
                $data = [
                    'aff_user_id'    => $aff_user_id  ,
                    'date_register'  => $today    ,
                    'visit_count'    => 1
                ];
                $format = [ '%d' ,'%s' ,'%d' ];
                $wpdb->insert( $aff_visit , $data , $format );
            }else{
                $data  = [ 'visit_count' => $retrieve_data[0]->visit_count+1 ];
                $where = [ 'aff_user_id' => $aff_user_id , 'date_register' => $today  ];
                $format = [ '%d' ];
                $where_format = [ '%d' , '%s' ];
                $wpdb->update( $aff_visit, $data, $where, $format, $where_format );
            }


        }

    }

}
add_action( 'template_redirect', 're_home_check_affiliate' );



// Create User
function re_register_affiliate($user_id){
    $aff_calculate = unserialize(  get_option('re_aff_set_calculate'  ,true ));


    if (isset( $_COOKIE['affiliate']) && $aff_calculate['status'] != 'o'){
        $affiliate =  $_COOKIE['affiliate'];

        global $wpdb;
        $table_name = $wpdb->prefix . "re_aff_profile";

        $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE aff_token='".$affiliate."';" );

        $data  = [ 'child_register' => $retrieve_data[0]->child_register+1 ];
        $where = [ 'id' =>  $retrieve_data[0]->id   ];
        $format = [ '%d' ];
        $where_format = [ '%d' ];
        $wpdb->update( $table_name, $data, $where, $format, $where_format );

        update_user_meta( $user_id , 're_affiliate_id' , $retrieve_data[0]->user_id );
        update_user_meta( $user_id , 're_affiliate_purchase_count' , 0 );
    }
}
add_action( 'user_register', 're_register_affiliate' , 10, 1 );



function re_affiliate_process($order_id){

    $aff_calculate = unserialize( get_option('re_aff_set_calculate'  , false ));
    $option        = unserialize( get_option('re_affiliate_settings', false ));
    $user          = wp_get_current_user();
    $user_id       = $user->ID;



    /// User Can Affiliate Check
    $user_can_aff_check = true ;
    if( $option['re_exclude_user_change_aff'] == "true" && in_array( 're_vendor', (array) $user->roles ) ){
        $user_can_aff_check = false ;
    }

    if ( $user_can_aff_check===true ){

    /// Time Calculate Check
    if ( $option['re_cal_income_save_order'] == 0  &&  $aff_calculate['status'] != 'o' ){

    $re_affiliate_id = get_user_meta( $user_id , 're_affiliate_id' , true );

            if ( !empty( $re_affiliate_id ) ) {
                global $wpdb;
                $table_name = $wpdb->prefix . "re_aff_profile";
                $order =   wc_get_order( $order_id ) ;

                /// Coupon Check
                $coupon_check = true;
                $coupon = $order->get_coupon_codes();
                if ( $option['re_exclude_coupon'] == "true" ) {
                    if ( count($coupon)  > 0  ){
                        $coupon_check = false ;
                    }
                }

                if ( $coupon_check === true ){

                    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$re_affiliate_id};" );

                    $order_list = explode(',' , $retrieve_data[0]->order_record );
                    if ( !in_array( $order_id , $order_list ) ) {


                        /// Minimum Check
                        $minimum_check = true;
                        if ( is_numeric( $option['re_minimal_cart']) &&  $option['re_minimal_cart'] >= $order->get_total() ) {
                                $minimum_check = false;
                        }

                        $maximum_check = true;
                        /// Maximum Check
                        if ( is_numeric( $option['re_maximal_cart']) &&  $option['re_maximal_cart'] <= $order->get_total() ) {
                            $maximum_check = false;
                        }


                        if ($minimum_check === true) {
                            if ($maximum_check === true) {


                                if ($aff_calculate['status'] == 'a') {

                                    $child_purchase_count = $retrieve_data[0]->child_purchase;
                                    if ($child_purchase_count <= (int)$aff_calculate['term_count']) {

                                        if ($aff_calculate['term_type'] == 'p') {
                                            $income = ((int)$aff_calculate['term_value'] * (int) $order->get_total()) / 100;
                                        } else {
                                            $income = (int)$aff_calculate['term_value'];
                                        }

                                        $data = [
                                            'child_purchase' => $retrieve_data[0]->child_purchase + 1,
                                            'credit' => $retrieve_data[0]->credit + $income ,
                                            'order_record' => $retrieve_data[0]->order_record.$order_id.','
                                        ];
                                    }

                                } elseif ( $aff_calculate['status'] == 'f') {

                                    foreach ($aff_calculate['data'] as $item) {
                                        if ($item['termnumber'] == $retrieve_data[0]->child_purchase + 1) {

                                            if ($item['termtype'] == 'percent') {
                                                $income = ((int)$item['termvalue'] * (int)$order->get_total()) / 100;
                                            } else {
                                                $income = (int)$item['termvalue'];
                                            }
                                            $data = [
                                                'child_purchase' => $retrieve_data[0]->child_purchase + 1 ,
                                                'credit' => $retrieve_data[0]->credit + $income ,
                                                'order_record' => $retrieve_data[0]->order_record .$order_id.','
                                            ];
                                        }
                                    }


                                }

                                $where = ['id' => $retrieve_data[0]->id];
                                $format = ['%d', '%d' ,'%s'];
                                $where_format = ['%d'];
                                $wpdb->update($table_name, $data, $where, $format, $where_format);
                                update_user_meta($user_id, 're_affiliate_purchase_count', (int)get_user_meta( $user_id, 're_affiliate_purchase_count', true) + 1);
                            }
                        }
                    }
               }
           }
       }
   }


}
add_action( 'woocommerce_thankyou', 're_affiliate_process',10, 1);

function convertNumbersOnSite($srting, $toPersian=true)
{
    $en_num = array('0','1','2','3','4','5','6','7','8','9');
    $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    if( $toPersian )
        return str_replace($en_num, $fa_num, $srting);
    else
        return str_replace($fa_num, $en_num, $srting);
}




