<?php



add_shortcode('delivering_woocommerce' , function ($attr , $content) {







    if ( current_user_can('order_deliver') || current_user_can('administrator')  ){


        wp_enqueue_script('re_delivering_script' );
        wp_enqueue_style( 're_delivering_style' );

            ?>
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Document</title>
            </head>
            <body>

            <div class="delivery_list_con">

                <?php
                $order_count = 1;
                $orders_is_processing = wc_get_orders( array(
                    'status'    => 'on-deliver' ,
                    'orderby'   => 'meta_value_num',
                    'meta_key'  => array('re_micro_time' , 'daypart' ) ,
                    'order'     => 'ASC',
                    'limit'     => -1
                ));
                ?>

                <div class="main-item first_item">
                    <p>تعداد <?php echo count($orders_is_processing); ?></p>
                    <h4>دریافت شده</h4>
                    <ul class="orders-list">
                        <?php

                        foreach ( $orders_is_processing as $item_process  ) {
                            $date_explode = explode('|' , get_post_meta($item_process->get_id() , 'daypart' , true) );

                            $map =  get_post_meta($item_process->get_id() , '_order_shipping_location' )[0];
                            ?>

                            <li>
                                <span><?php echo $order_count; ?></span>

                                <div class="details-con">

                                    <div>
                                        <span>#<?php echo $item_process->get_id();?></span>
                                    </div>
                                    <div class="order_map">
                                        <a target="_blank"   href="https://map.ir/lat/<?php echo $map['lat']; ?>/lng/<?php echo $map['lng']; ?>/z/20" > نقشه </a>
                                    </div>
                                    <div>
                                        <span><?php echo jdate('l' , strtotime($date_explode[1]) ) ; ?></span>
                                        <span><?php echo str_replace('_' , ' الی ' , $date_explode[0]  ); ?></span>
                                        <span><?php echo $date_explode[1] ; ?></span>
                                    </div>
                                </div>

                                <div  class="product-list">

                                    <ul>
                                        <?php
                                        $count_p = 1;
                                        $sum_p   = 0;
                                        foreach ($item_process->get_items() as  $p_item_process){ ?>
                                            <li>
                                                <p> <?php echo $count_p ?></p>
                                                <p> <?php echo $p_item_process->get_name(); ?> </p>
                                                <p> <?php echo $p_item_process->get_quantity(); ?> <span>عدد</span></p>
                                            </li>
                                            <?php $count_p++;  $sum_p+=$p_item_process->get_quantity(); } ?>
                                    </ul>
                                    <div>
                                        <p>
                                            <span>آدرس :</span>  <?php echo  $item_process->get_billing_address_1(); ?>
                                        </p>
                                        <p>
                                            <span>توضیحات :</span>   <?php echo get_user_meta( $item_process->get_user()->ID , 'order_note' , true); ?>
                                        </p>
                                        <p>
                                            <span>شماره موبایل :</span>  <?php echo  $item_process->get_billing_phone(); ?>
                                        </p>
                                        <p>
                                            <span>مبلغ کل :</span><?php echo $item_process->get_total(); ?>
                                        </p>
                                    </div>

                                </div>
                                <div class="order-content-processing ">

                                    <div class="order_count">
                                        <p>ت.ک :<span><?php echo $sum_p ; ?></span></p>
                                    </div>
                                    <div class="order_status">
                                        <p>وضعیت :<span><?php echo $item_process->get_payment_method() ==='portal' ? '<span style="color:#1FB100">پرداخت اینترنتی</span>': '<span style="color:#6D0019 ">پرداخت در محل</span>'  ; ?></span></p>
                                    </div>
                                    <div class="total-price">
                                        <select name="change-status" class="change-status-option">
                                            <option value="0">انتخاب وضعیت</option>
                                            <option value="1">تکمیل شد</option>
                                            <option value="2">برگشت به بسته بندی</option>
                                            <option value="3">لغو شد</option>
                                        </select>
                                    </div>

                                    <div class="change-status-delivery">
                                        <a href="javascript:void(0)" id="change-status-delivery" data-product="<?php echo $item_process->get_id();?>"> > </a>
                                    </div>

                                    <div class="deliver_time">
                                        <div class="close">*</div>
                                        <?php (new Sabadino\features\delivering_time\DeliveringPublic)->checkoutPage(); ?>
                                        <p class="save_time_deliver_again" data-product="<?php echo $item_process->get_id();?>">ذخیره</p>
                                    </div>

                                </div>


                            </li>

                            <?php $order_count++; } ?>
                    </ul>
                </div>


                <?php
                $order_count_packing = 1;
                $orders_is_packing = wc_get_orders(array('status' => 'completed' , 'limit'     => -1
                ) );
                ?>
                <div class="fixed-con">
                    <div class="deliver_items">
                        <h4>تکمیل شده</h4>
                        <ul class="orders-list">
                            <?php
                            foreach ($orders_is_packing as $item_packing  ) {
                                $date_explode_packing = explode('|' , get_post_meta($item_packing->get_id() , 'daypart' , true) );
                                ?>
                                <li>
                                    <span><?php echo $order_count_packing; ?></span>

                                    <div class="details-con">

                                        <div>
                                            <span>#<?php echo $item_packing->get_id();?></span>
                                        </div>
                                        <div>
                                            <span><?php echo jdate('l' , strtotime($date_explode_packing[1]) ) ; ?></span>
                                            <span><?php echo str_replace('_' , ' الی ' , $date_explode_packing[0]  ); ?></span>
                                            <span><?php echo $date_explode_packing[1] ; ?></span>
                                        </div>
                                        <div class="order_count">
                                            <p>تعداد کل محصول :<span><?php echo $sum_p; ?></span></p>
                                        </div>
                                    </div>

                                    <?php
                                    $sum_p   = 0;
                                    foreach ($item_packing->get_items() as  $p_item_packing){
                                        $sum_p+=$p_item_packing->get_quantity();
                                    } ?>


                                </li>
                                <?php $order_count_packing++; } ?>

                        </ul>
                    </div>






                    <?php
                    $order_count_packing = 1;
                    $orders_is_packing = wc_get_orders(array('status' => 'is-packing' , 'limit'     => -1
                    ) );
                    ?>

                    <div class="deliver_items">
                        <h4>در حال بسته بندی</h4>
                        <ul class="orders-list">
                            <?php
                            foreach ($orders_is_packing as $item_packing  ) {
                                $date_explode_packing = explode('|' , get_post_meta($item_packing->get_id() , 'daypart' , true) );
                                ?>
                                <li>
                                    <span><?php echo $order_count_packing; ?></span>

                                    <div class="details-con">

                                        <div>
                                            <span>#<?php echo $item_packing->get_id();?></span>
                                        </div>
                                        <div>
                                            <span><?php echo jdate('l' , strtotime($date_explode_packing[1]) ) ; ?></span>
                                            <span><?php echo str_replace('_' , ' الی ' , $date_explode_packing[0]  ); ?></span>
                                            <span><?php echo $date_explode_packing[1] ; ?></span>
                                        </div>
                                        <div class="order_count">
                                            <p>تعداد کل محصول :<span><?php echo $sum_p; ?></span></p>
                                        </div>
                                    </div>

                                    <?php
                                    $sum_p   = 0;
                                    foreach ($item_packing->get_items() as  $p_item_packing){
                                        $sum_p+=$p_item_packing->get_quantity();
                                    } ?>


                                </li>
                                <?php $order_count_packing++; } ?>

                        </ul>
                    </div>






                    <?php
                    $order_count_canceled = 1;
                    $orders_is_canceled = wc_get_orders(array('status' => 'cancelled' , 'limit'     => -1
                    ) );
                    ?>

                    <div class="deliver_items">
                        <h4>لغو شده</h4>
                        <ul class="orders-list">
                            <?php
                            foreach ($orders_is_canceled as $item_canceled  ) {
                                $date_explode_canceled = explode('|' , get_post_meta($item_canceled->get_id() , 'daypart' , true) );
                                ?>
                                <li>
                                    <span><?php echo $order_count_canceled; ?></span>

                                    <div class="details-con">

                                        <div>
                                            <span>#<?php echo $item_canceled->get_id();?></span>
                                        </div>
                                        <div>
                                            <span><?php echo jdate('l' , strtotime($date_explode_canceled[1]) ) ; ?></span>
                                            <span><?php echo str_replace('_' , ' الی ' , $date_explode_canceled[0]  ); ?></span>
                                            <span><?php echo $date_explode_canceled[1] ; ?></span>
                                        </div>
                                        <div class="order_count">
                                            <?php
                                            $sum_can   = 0;
                                            foreach ($item_canceled->get_items() as  $p_item_canceled){
                                                $sum_can+=$p_item_canceled->get_quantity();
                                            } ?>
                                            <p>تعداد کل محصول :<span><?php echo $sum_can; ?></span></p>
                                        </div>
                                    </div>





                                </li>
                                <?php $order_count_canceled++; } ?>

                        </ul>
                    </div>
                </div>


                <div class="loader">
                    <div class="loadingio-spinner-disk-afj302ypqb">
                        <div class="ldio-vn8bwgjykoh">
                            <div><div></div><div></div></div></div></div>
                </div>



            </div>


            </body>
            </html>

            <?php

    }else{
        wp_redirect('/');
    }

});


