<?php



// Create User
function re_register_referral( $user_id ){

        $user = get_userdata( $user_id );
        if ( $_SERVER['HTTP_TOKEN']  != 'reza'){
            global $wpdb;
            $table_name = $wpdb->prefix . "re_referral";

            $data  = [
                'user_id'        => $user->ID ,
                'phone'          =>  $user->user_login,
                'purchase_ids'   =>  serialize([]),
                'self_referral'  => rand( 1000 , 9999 ) . substr( $user->ID ,-1  ,  1)
            ];
            $format = [ '%d' ,'%s'  ,'%s' ,  '%d'    ];
            $wpdb->insert( $table_name , $data , $format );
        }
}
add_action( 'user_register', 're_register_referral' , 99, 1 );




//function re_referral_user_update( $user_id  ) {
//
//    $user = get_userdata( $user_id );
//    global $wpdb;
//    $table_name = $wpdb->prefix . "re_referral";
//
//    $data  = [
//         'phone' =>  $user->user_login ,
//         'full_name' => $user->user_firstname.' '.$user->user_lastname
//     ];
//    $where = [ 'user_id' => $user_id  ];
//    $format = [ '%s','%s' ];
//    $where_format = [ '%d'];
//    $wpdb->update( $table_name, $data, $where, $format, $where_format );
// }
//add_action( 'profile_update', 're_referral_user_update', 10, 2 );



function re_referral_process( $order_id ){

    global $wpdb;
    $table_name   = $wpdb->prefix ."re_referral";
    $user_id      = get_current_user_id();
    $order        = wc_get_order($order_id);


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
add_action( 'woocommerce_thankyou', 're_referral_process',10, 1);





function re_discount_when_products_in_cart() {

    global $wpdb;
    $table_name   = $wpdb->prefix ."re_referral";
    $user_id      = get_current_user_id();

    global $woocommerce;
//    $coupon_percent = new WC_Coupon(get_current_user_id().'_'.rand(1000,9999));
//    $coupon_percent->set_discount_type('percent');
//    $coupon_percent->set_amount(7);
//    $coupon_percent->set_usage_limit(1);
//    $order->apply_coupon($coupon_percent->get_code());
//    if( $woocommerce->cart->cart_contents_count > 3 ) {
//        $coupon_code = 'maryscode';
//        if (!$woocommerce->cart->add_discount( sanitize_text_field( $coupon_percent->get_code() ))) {
//            $woocommerce->show_messages();
//        }
////        echo '<div class="woocommerce_message"><strong>You have more than 3 items in your cart, a 10% discount has been added.';
//    }
     $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id={$user_id};" );

//    $re_ref_gifts = unserialize( get_option('re_ref_cal_gifts'  , false ) );
//    if ( $retrieve_data[0]->score >= $re_ref_gifts['roof'] ) {
//        $which_gifts = $re_ref_gifts['status'];
//
//        if ($which_gifts == 1) {
//            $coupon_code = $user_id . '_website_percent';
//            $amount = $re_ref_gifts['amount'];
//            $discount_type = 'percent';
//
//            $coupon = array(
//                'post_title' => $coupon_code,
//                'post_content' => '',
//                'post_status' => 'publish',
//                'post_author' => 7,
//                'post_type' => 'shop_coupon'
//            );
//
//            $new_coupon_id = wp_insert_post($coupon);
//
//            update_post_meta($new_coupon_id, 'discount_type', $discount_type);
//            update_post_meta($new_coupon_id, 'coupon_amount', $amount);
//            update_post_meta($new_coupon_id, 'individual_use', 'no');
//            update_post_meta($new_coupon_id, 'product_ids', '');
//            update_post_meta($new_coupon_id, 'exclude_product_ids', '');
//            update_post_meta($new_coupon_id, 'usage_limit', 1);
//            update_post_meta($new_coupon_id, 'expiry_date', '');
//            update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
//            update_post_meta($new_coupon_id, 'free_shipping', 'no');
//
//            if (!$woocommerce->cart->add_discount(sanitize_text_field($coupon_code)))
//                $woocommerce->show_messages();
//
//
//        } elseif ($which_gifts == 2) {
//
//            $coupon_code = $user_id . '_website_fixed';
//            $amount = $re_ref_gifts['amount'];
//            $discount_type = 'fixed_cart';
//
//            $coupon = array(
//                'post_title' => $coupon_code,
//                'post_content' => '',
//                'post_status' => 'publish',
//                'post_author' => 7,
//                'post_type' => 'shop_coupon'
//            );
//
//            $new_coupon_id = wp_insert_post($coupon);
//
//            update_post_meta($new_coupon_id, 'discount_type', $discount_type);
//            update_post_meta($new_coupon_id, 'coupon_amount', $amount);
//            update_post_meta($new_coupon_id, 'individual_use', 'no');
//            update_post_meta($new_coupon_id, 'product_ids', '');
//            update_post_meta($new_coupon_id, 'exclude_product_ids', '');
//            update_post_meta($new_coupon_id, 'usage_limit', 1);
//            update_post_meta($new_coupon_id, 'expiry_date', '');
//            update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
//            update_post_meta($new_coupon_id, 'free_shipping', 'no');
//
//            if (!$woocommerce->cart->add_discount(sanitize_text_field($coupon_code)))
//                $woocommerce->show_messages();
//
//
//        } elseif ($which_gifts == 3) {
//            $woocommerce->cart->add_to_cart((int)$re_ref_gifts['amount'], 1);
//            $coupon_code = $user_id . '_website_' . $re_ref_gifts['amount'];
//            $amount = wc_get_product($re_ref_gifts['amount'])->get_sale_price();
//
//            $discount_type = 'fixed_cart';
//
//            $coupon = array(
//                'post_title' => $coupon_code,
//                'post_content' => '',
//                'post_status' => 'publish',
//                'post_author' => 7,
//                'post_type' => 'shop_coupon'
//            );
//
//            $new_coupon_id = wp_insert_post($coupon);
//
//            update_post_meta($new_coupon_id, 'discount_type', $discount_type);
//            update_post_meta($new_coupon_id, 'coupon_amount', $amount);
//            update_post_meta($new_coupon_id, 'individual_use', 'no');
//            update_post_meta($new_coupon_id, 'product_ids', '');
//            update_post_meta($new_coupon_id, 'exclude_product_ids', '');
//            update_post_meta($new_coupon_id, 'usage_limit', 1);
//            update_post_meta($new_coupon_id, 'expiry_date', '');
//            update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
//            update_post_meta($new_coupon_id, 'free_shipping', 'no');
//
//            if (!$woocommerce->cart->add_discount(sanitize_text_field($coupon_code)))
//                $woocommerce->show_messages();
//        }


//        if (!empty($order->get_coupon_codes())){
//            $mines_score  = $re_ref_gifts['roof'] ;
//
//            $data_mines = [
//                'score' => $retrieve_data[0]->score - $mines_score ,
//            ];
//
//            $where_mines = ['id' => $retrieve_data[0]->id];
//            $format_mines = ['%d'];
//            $where_format_mines = ['%d'];
//            $wpdb->update( $table_name, $data_mines, $where_mines, $format_mines, $where_format_mines);
//        }
//
//
//    }else{
//        if (!empty($coupon) ) {
//            $order->apply_coupon($coupon);
//        }
//    }
//    }
}
add_action('woocommerce_before_cart_table', 're_discount_when_products_in_cart');


