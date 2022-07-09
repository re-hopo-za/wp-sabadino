<?php


namespace Sabadino\features\delivering_time;


class DeliveringPublic
{


    public $options = [];

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
        $this->addNotExistsDate();
        add_action( 'wp_enqueue_scripts', [$this ,'enqueues'] );
        add_action( 'woocommerce_thankyou',[$this ,'thankyouPageWc'] ,10 ,1 );
        add_action( 'woocommerce_before_order_notes' ,[$this ,'checkoutPage'] );
        $this->options = get_option( 'delivering_time_options' ,true );
    }


    public function isSetIndex( $index ){
        $deliveing_option = $this->options ;
        if ( isset( $deliveing_option[$index] ) ){
            return $deliveing_option[$index];
        }
        return '';
    }

    public static function getProductsInCart()
    {
        $products = [];
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        if ( !empty( $items ) ){
            foreach ( $items as $item ){
                $post_id   = $item['product_id'];
                $post_meta = (int) get_post_meta( $post_id  ,'hold_deliver_time' ,true );
                $products [ $post_id ] = $post_meta;
            }
        } 
        return $products;
    }

    public static function disableDay( $products ,$time  )
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


    public function checkoutPage()
    {
        $products = self::getProductsInCart();

        $disable_time_1 = self::disableDay( $products ,1 );
        $disable_time_2 = self::disableDay( $products ,2 );
        $disable_time_3 = self::disableDay( $products ,3 );
        $disable_time_4 = self::disableDay( $products ,4 );
        $disable_time_5 = self::disableDay( $products ,5 );

        $vocations = explode( ","  ,  $this->isSetIndex('vocations_manual') );

        $re_morning_active   = $this->isSetIndex('morning_active');
        $re_afternoon_active = $this->isSetIndex('afternoon_active');
        $re_evening_active   = $this->isSetIndex('evening_active');
        $re_slider_input     = $this->isSetIndex('slider_input');
        $hours               = date_i18n('H:i');

        $time_1 = '';
        $time_2 = '';
        $time_3 = '';
        $days   = 0;

        if( $hours > $this->isSetIndex('limit_morning')  ) {
           $time_1 = "disabled" ;
        }
        if( $hours >= $this->isSetIndex('limit_afternoon')  ) {
           $time_2 = "disabled" ;
        }
        if( $hours >= $this->isSetIndex('limit_evening') ) {
           $time_3 = "disabled" ;
        }

        $disable_today = true;
        if( ($re_morning_active   == 0   ||  $time_1 == 'disabled') &&
            ($re_afternoon_active == 0   ||  $time_2 == 'disabled') &&
            ($re_evening_active   == 0   ||  $time_3 == 'disabled') ) {
                 $disable_today = false;
        }


        $date_1 = jdate("Y/m/d", '', '' , '', 'en');
        $date_2 = jdate("Y/m/d", strtotime("+1 day") , '' , '' ,'en');
        $date_3 = jdate("Y/m/d", strtotime("+2 day") , '' , '' ,'en');
        $date_4 = jdate("Y/m/d", strtotime("+3 day") , '' , '' ,'en');
        $date_5 = jdate("Y/m/d", strtotime("+4 day") , '' , '' ,'en');

        $capacity_1= true ;
        $capacity_1_main = get_option( $date_1 );
        if( !empty( $capacity_1_main ) ){
            $capacity_1_main = explode("|", $capacity_1_main );
            $capacity_1_main = self::indexChecker( $capacity_1_main , 0) + self::indexChecker( $capacity_1_main , 1) + self::indexChecker( $capacity_1_main , 2);
            if($capacity_1_main >= (int) $this->isSetIndex('limit_one') ){
                $capacity_1 = false;
            }
        }

        $capacity_2= true ;
        $capacity_2_main = get_option( $date_2 );

        if( !empty( $capacity_2_main ) ){
            $capacity_2_main = explode("|",$capacity_2_main);
            $capacity_2_main =  self::indexChecker( $capacity_2_main , 0) + self::indexChecker( $capacity_2_main , 1) + self::indexChecker( $capacity_2_main , 2);
            if($capacity_2_main >= (int) $this->isSetIndex('limit_two') ){
                $capacity_2 = false;
            }
        }

        $capacity_3 = true ;
        $capacity_3_main = get_option( $date_3 );
        if ( !empty( $capacity_3_main )){
            $capacity_3_main = explode("|",$capacity_3_main);
            $capacity_3_main = self::indexChecker( $capacity_3_main , 0) + self::indexChecker( $capacity_3_main , 1) + self::indexChecker( $capacity_3_main , 2);
            if($capacity_3_main >= (int) $this->isSetIndex('limit_three') ){
                $capacity_3 = false;
            }
        }

        $capacity_4 = true ;
        $capacity_4_main = get_option( $date_4 );
        if ( !empty( $capacity_4_main ) ){
            $capacity_4_main = explode("|",$capacity_4_main);
            $capacity_4_main =  self::indexChecker( $capacity_4_main , 0) + self::indexChecker( $capacity_4_main , 1) + self::indexChecker( $capacity_4_main , 2);
            if($capacity_4_main >= (int) $this->isSetIndex('limit_four') ){
                $capacity_4 = false;
            }
        }

        $capacity_5 = true ;
        $capacity_5_main = get_option( $date_5 );
        if ( !empty( $capacity_5_main) ){
            $capacity_5_main = explode("|",$capacity_5_main);
            $capacity_5_main =  self::indexChecker( $capacity_5_main , 0) + self::indexChecker( $capacity_5_main , 1) + self::indexChecker( $capacity_5_main , 2);
            if($capacity_5_main >= (int) $this->isSetIndex('limit_five') ){
                $capacity_5 = false;
            }
        }


        $first_time  = $this->isSetIndex('first_time_start').'_'.$this->isSetIndex('first_time_end');
        $second_time = $this->isSetIndex('second_time_start').'_'.$this->isSetIndex('second_time_end');
        $third_time  = $this->isSetIndex('third_time_start').'_'.$this->isSetIndex('third_time_end');
        ?>
        <section>
            <h3>زمان تحویل</h3>
           <table class="table re_table table-hover table-bordered table-time "
                  data-quantity="<?php $quantity=[]; $i=0; foreach ( WC()->cart->get_cart() as $cart_item ) {
                     $quantity[$i]=  $cart_item['quantity'];
                     $i++;
                  }
                  echo array_sum( $quantity ); ?>" >
              <thead>
                  <tr>
                     <th>روز</th>
                     <th>تاریخ</th>

                     <?php  if( $re_morning_active == 1 ) { ?>
                        <th>
                           <span><?php echo $this->isSetIndex('first_time_start'); ?></span>
                           <span>الی</span>
                           <span><?php echo $this->isSetIndex('first_time_end'); ?></span>
                        </th>
                     <?php } ?>

                     <?php  if( $re_afternoon_active == 1 ) { ?>
                        <th>
                           <span><?php echo $this->isSetIndex('second_time_start'); ?></span>
                           <span>الی</span>
                           <span><?php echo $this->isSetIndex('second_time_end'); ?></span>
                        </th>
                     <?php } ?>

                     <?php  if( $re_evening_active == 1 ) { ?>
                        <th>
                           <span><?php echo $this->isSetIndex('third_time_start'); ?></span>
                           <span>الی</span>
                           <span><?php echo $this->isSetIndex('third_time_end'); ?></span>
                        </th>
                     <?php } ?>
                  </tr>
                  </thead>

                  <tbody>

                  <?php

                  if( !in_array( $date_1  ,$vocations ) &&
                      $disable_time_1 &&
                      $time_3 != "disabled" &&
                      $capacity_1 == true &&
                      $disable_today == true &&
                      $this->isFriday( jdate("w", '', '' , '' ,'en') ) ){
                  ?>

                  <tr>
                     <td>امروز</td>
                     <td><?php echo jdate("Y-m-d"); ?></td>


                 <?php  if( $re_morning_active == 1 ) { ?>
                    <td>
                    <?php  if( $time_1 != 'disabled') { ?>
                       <input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_1; ?>"
                              value="<?php echo $first_time; ?>" data-micro-time="<?php echo date('U' ) + 1; ?>" >
                    <?php }else{
                       echo 'تکمیل شد';
                    }  ?>
                    </td>
                 <?php  } ?>


                 <?php  if( $re_afternoon_active == 1 ) { ?>
                     <td>
                        <?php  if( $time_2 != 'disabled') { ?>
                           <input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_1; ?>"
                                  value="<?php echo $second_time; ?>" data-micro-time="<?php echo date('U' ) + 2; ?>" >
                        <?php }else{
                           echo 'تکمیل شد';
                        } ?>
                     </td>
                 <?php  }  ?>


                 <?php if( $re_evening_active == 1 ) { ?>
                     <td>
                        <?php  if( $time_3 != 'disabled') { ?>
                           <input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_1; ?>"
                                  value="<?php echo $third_time; ?>" data-micro-time="<?php echo date('U' ) + 3; ?>" >
                        <?php }else{
                           echo 'تکمیل شد';
                        } ?>
                     </td>
                 <?php } ?>

                  </tr>
                  <?php $days++; }  ?>


                  <?php if( $days < $re_slider_input &&
                          $disable_time_2 &&
                          !in_array(  $date_2  ,  $vocations  ) &&
                          $capacity_2 == true &&
                          $this->isFriday( jdate("w", strtotime("+1 day"), '' , '' ,'en') ) ){ ?>
                  <tr>
                     <td>فردا</td>
                     <td><?php echo  jdate("Y-m-d", strtotime("+1 day")); ?></td>

                      <?php  if($re_morning_active==1) { ?>
                              <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_2; ?>"
                                         value="<?php echo $first_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+1 day') ) + 1; ?>" ></td>
                      <?php } ?>


                      <?php  if($re_afternoon_active==1) { ?>
                              <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_2; ?>"
                                         value="<?php echo $second_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+1 day') ) + 2; ?>" ></td>
                      <?php } ?>

                      <?php  if($re_evening_active==1) { ?>
                           <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_2; ?>"
                                      value="<?php echo $third_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+1 day') ) + 3; ?>" ></td>
                      <?php } ?>
                  </tr>
                  <?php $days++; }  ?>




            <?php if( $days < $re_slider_input &&
                    $disable_time_3 &&
                    !in_array(  $date_3  ,  $vocations  )  &&
                    $capacity_3 == true &&
                    $this->isFriday( jdate("w", strtotime("+2 day"), '' , '' ,'en') ) ){ ?>
                  <tr>
                     <td><?php echo  jdate("l", strtotime("+2 day")); ?></td>
                     <td><?php echo  jdate("Y-m-d", strtotime("+2 day")); ?></td>

                     <?php  if($re_morning_active==1) { ?>
                           <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_3; ?>"
                                      value="<?php echo $first_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+2 day') ) + 1; ?>"></td>
                     <?php } ?>

                     <?php  if($re_afternoon_active==1) { ?>
                        <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_3; ?>"
                                   value="<?php echo $second_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+2 day') ) + 2; ?>"></td>
                     <?php } ?>

                     <?php  if($re_evening_active==1) { ?>
                        <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_3; ?>"
                                   value="<?php echo $third_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+2 day') ) + 3; ?>"></td>
                     <?php } ?>
                  </tr>
            <?php $days++; } ?>




            <?php if( $days < $re_slider_input &&
                $disable_time_4 &&
                !in_array(  $date_4  ,  $vocations  ) &&
                $capacity_4 == true &&
                $this->isFriday( jdate("w", strtotime("+3 day"), '' , '' ,'en') ) ){ ?>
                  <tr>
                     <td><?php echo  jdate("l", strtotime("+3 day")); ?></td>
                     <td><?php echo  jdate("Y-m-d", strtotime("+3 day")); ?></td>

                     <?php  if($re_morning_active==1) { ?>
                        <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_4; ?>"
                                   value="<?php echo $first_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+3 day') ) + 1; ?>"></td>
                     <?php } ?>

                     <?php  if($re_afternoon_active==1) { ?>
                           <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_4; ?>"
                                      value="<?php echo $second_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+3 day') ) + 2; ?>"></td>
                     <?php } ?>

                     <?php  if($re_evening_active==1) { ?>
                              <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_4; ?>"
                                         value="<?php echo $third_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+3 day') ) + 3; ?>"></td>
                     <?php } ?>
                  </tr>
            <?php $days++; }  ?>



            <?php

            if( $days < $re_slider_input &&
                    $disable_time_5 &&
                    !in_array(  $date_5  ,  $vocations  ) &&
                    $capacity_5 == true &&
                    $this->isFriday( jdate("w", strtotime("+4 day"), '' , '' ,'en') ) ){ ?>
               <tr>
                  <td><?php echo  jdate("l", strtotime("+4 day")); ?></td>
                  <td><?php echo  jdate("Y-m-d", strtotime("+4 day")); ?></td>

                 <?php  if($re_morning_active==1) { ?>
                     <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_5; ?>"
                                value="<?php echo $first_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+4 day') ) + 1; ?>"></td>
                  <?php } ?>

                 <?php  if($re_afternoon_active==1) { ?>
                     <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_5; ?>"
                                value="<?php echo $second_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+4 day') ) + 2; ?>"></td>
                  <?php } ?>

                 <?php  if($re_evening_active==1) { ?>
                     <td><input class="re_input" type="radio" name="date_input" data-date="<?php echo $date_5; ?>"
                                value="<?php echo $third_time; ?>" data-micro-time="<?php echo date('U' ,strtotime('+4 day') ) + 3; ?>"></td>
                 <?php } ?>
               </tr>
            <?php $days++; }  ?>

            <?php
                if($days == 0){
                 ?>
                     <tr>
                         <td colspan="5">لطفا برای خرید در روز های اتی مراجعه فرمایید</td>
                     </tr>
                 <?php
                }
            ?>

           </tbody>
        </table>


        </section>
        <?php

    }

    public function isFriday( $day_of_week ){
        $option = $this->options ;
        if ( isset( $option['off_friday'] ) && $option['off_friday'] == 1 && $day_of_week == 6 ) {
            return false;
        }
        return true;
   }

    public static function indexChecker( $input , $index )
    {
        if( !empty( $input ) && is_array( $input ) && isset( $input[$index] ) ){
            return (int) $input[$index];
        }
        return 0;
    }


    public function enqueues($hook) {


        wp_enqueue_script(
            're_ajax' ,
            RE_ASSETS.'js/main_ajax.js' ,
            ['jquery']
        );
        wp_localize_script(
            're_ajax' ,
            're_data' , [
            'admin_url' => admin_url('admin-ajax.php')
        ]);
        wp_enqueue_style(
            're_public_style',
            RE_ASSETS.'css/public.css',
             [] ,
             time()
        );

    }



    public function thankyouPageWc( $order_id ){
        $order =   wc_get_order( $order_id );

        $dates = $order->get_meta('daypart' , true );
        $explode_date   = explode("|" ,$dates );
        $recorded_count = get_option( 'delivering_time_options' );
        $this_day_count = get_option( $explode_date[1] );
        $this_day_count = explode("|" ,$this_day_count );
        $morning   =  isset( $this_day_count[0] ) ? $this_day_count[0] : 0;
        $afternoon =  isset( $this_day_count[1] ) ? $this_day_count[1] : 0;
        $evening   =  isset( $this_day_count[2] ) ? $this_day_count[2] : 0;
        if( $explode_date[0] == $recorded_count['first_time_start'].'_'.$recorded_count['first_time_end'] ){
            $morning   =  $morning + 1 ;
        }elseif( $explode_date[0] == $recorded_count['second_time_start'].'_'.$recorded_count['second_time_end'] ){
            $afternoon = $afternoon + 1 ;
        }elseif( $explode_date[0] == $recorded_count['third_time_start'].'_'.$recorded_count['third_time_end'] ){
            $evening   =  $evening + 1 ;
        }
        update_option( $explode_date[1] ,$morning.'|'.$afternoon.'|'.$evening );
    }


    public function addNotExistsDate(){
        $date_1 = date("Y/m/d");
        $date_2 = date("Y/m/d", strtotime("+7 day") );
        $date_3 = date("Y/m/d", strtotime("+8 days") );
        $date_4 = date("Y/m/d", strtotime("+9 days") );
        $date_5 = date("Y/m/d", strtotime("+10 days") );

        empty( get_option( $date_1 , true) ) ? add_option( $date_1 , "0|0|0") : null;
        empty( get_option( $date_2 , true) ) ? add_option( $date_2 , "0|0|0") : null;
        empty( get_option( $date_3 , true) ) ? add_option( $date_3 , "0|0|0") : null;
        empty( get_option( $date_4 , true) ) ? add_option( $date_4 , "0|0|0") : null;
        empty( get_option( $date_5 , true) ) ? add_option( $date_5 , "0|0|0") : null;
    }



}








