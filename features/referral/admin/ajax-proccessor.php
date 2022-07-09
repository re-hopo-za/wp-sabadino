<?php function re_ref_set_calculate()
{
    update_option('re_ref_set_calculate', serialize($_POST['settings']));
    exit();
}

add_action('wp_ajax_re_ref_set_calculate', 're_ref_set_calculate');
add_action('wp_ajax_nopriv_re_ref_set_calculate', 're_ref_set_calculate');
function re_ref_cal_gifts()
{
    update_option('re_ref_cal_gifts', serialize($_POST['settings']));
    exit();
}

add_action('wp_ajax_re_ref_cal_gifts', 're_ref_cal_gifts');
add_action('wp_ajax_nopriv_re_ref_cal_gifts', 're_ref_cal_gifts');
function re_set_referral_code()
{

    global $wpdb;
    $table_name = $wpdb->prefix . "re_referral";
    $user_id = get_current_user_id();
    $user = get_userdata( $user_id );
    $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE self_referral={$_POST['referral_code'] };");
    if ($retrieve_data[0]->id > 0){
   
        $retrieve_data = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id={$user_id};");
        if ($retrieve_data != null){
        
                if ($retrieve_data[0]->parent_referral == null){
                    $data = ['parent_referral' => $_POST['referral_code'] , 'full_name'  =>  $user->first_name . ' ' . $user->last_name  ];
                    $where = ['user_id' => $user_id];
                    $format = ['%d' , '%s'];
                    $where_format = ['%d'];
                    $wpdb->update($table_name, $data, $where, $format, $where_format);
                    echo wp_json_encode(['status' => 1]);
                    exit();
                }else{
                    $data = [  'full_name'  =>  $user->first_name . ' ' . $user->last_name  ];
                    $where = ['user_id' => $user_id];
                    $format = [ '%s'];
                    $where_format = ['%d'];
                    $wpdb->update($table_name, $data, $where, $format, $where_format);
                    echo wp_json_encode(['status' => 2]);
                    exit();
                }
        
        
        }else{
            $data  = [
                'user_id'        =>  $user->ID ,
                'phone'          =>  $user->user_login,
                'date_register'  =>  jdate('Y-m-d H:i:s'),
                'full_name'      =>  $user->first_name . ' ' . $user->last_name ,
                'purchase_ids'   =>  serialize([]),
                'self_referral'  =>  rand( 1000 , 9999 ) . substr( $user->ID ,-1  ,  1) ,
                'parent_referral' => $_POST['referral_code']
            ];
            $format = [ '%d' ,'%s' ,'%s'  ,'%s'  ,'%s' ,'%d'  ,'%d' ];
            $wpdb->insert( $table_name , $data , $format );
            echo wp_json_encode( array('status' => 3 ) );
            exit();
        }

    }else{
        echo wp_json_encode(['status' => 0]);
        exit();
    }
}

add_action('wp_ajax_re_set_referral_code', 're_set_referral_code');
add_action('wp_ajax_nopriv_re_set_referral_code', 're_set_referral_code');






add_action('wp_ajax_re_change_referral_status', 're_change_referral_status');
add_action('wp_ajax_nopriv_re_change_referral_status', 're_change_referral_status');
function re_change_referral_status()
{
    update_option('re_change_referral_status',  $_POST['status'] );
    exit();
}






