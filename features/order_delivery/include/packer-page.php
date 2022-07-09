<?php



add_shortcode('orders_woocommerce' , function ($attr , $content){


    wp_enqueue_script('re_delivering_script' );
    wp_enqueue_style( 're_delivering_style' );






    if ( current_user_can('order_packer') || current_user_can('administrator')  ){

        if (is_page()){
            ?>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Document</title>
            </head>
            <body>


            <div class="order_list_con">
                <div class="tabs">
                    <button class="tab-1 active" id="1" data-tabs=".tab-1">لیست سفارشات</button>
                    <button class="tab-2" id="2" data-tabs=".tab-2">لیست موجودی</button>
                </div>

                <div class="tabs-con">
                    <div class="tab-1 order_item_con">
                        <?php
                        $order_count = 1;
                        $orders_is_processing = wc_get_orders( array(
                            'status'    => 'wc-processing' ,
                            'orderby'   => 'meta_value_num',
                            'meta_key'  => array('re_micro_time') ,
                            'order'     => 'ASC',
                            'limit'     => -1
                        ));
                        ?>

                        <div class="first_item">
                            <p>تعداد <?php echo count($orders_is_processing); ?></p>
                            <h2>دریافت شده</h2>
                            <ul class="orders-list">
                                <?php

                                foreach ( $orders_is_processing as $item_process  ) {

                                    $date_explode = explode('|' , get_post_meta($item_process->get_id() , 'daypart' , true) );

                                    ?>

                                    <li>
                                        <span><?php echo $order_count; ?></span>

                                        <div class="details-con">

                                            <div>
                                                <span>#<?php echo $item_process->get_id();?></span>
                                            </div>
                                            <div>
                                                <span><?php echo jdate('l' , strtotime($date_explode[1]) ) ; ?></span>
                                                <span><?php echo str_replace('_' , ' الی ' , $date_explode[0]  ); ?></span>
                                                <span><?php echo $date_explode[1] ; ?></span>
                                            </div>
                                        </div>

                                        <div  class="product-list">

                                            <ul>
                                                <span>نام</span>
                                                <span>تعداد</span>
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

                                        </div>
                                        <div class="order-content-processing ">

                                            <div class="order_count">
                                                <p>تعداد کل محصول :<span><?php echo $sum_p ; ?></span></p>
                                            </div>

                                            <div class="print">
                                                <a class="print_invoice" target="_blank" title="document-26"  href="<?php echo RE_ADMIN_DELIVERING."print-page.php?p_id=".$item_process->get_id(); ?>" >Print</a>
                                            </div>

                                            <div class="change-status">
                                                <p class="change_p_status_to_packing" data-product="<?php echo $item_process->get_id();?>"> > </p>
                                            </div>

                                        </div>


                                    </li>

                                    <?php $order_count++;  } ?>
                            </ul>
                        </div>




                        <?php
                        $order_count_packing = 1;
                        $orders_is_packing = wc_get_orders(array('status' => 'wc-is-packing' , 'limit' => -1
                        ) );
                        ?>

                        <div class="first_item">
                            <p>تعداد <?php echo count($orders_is_packing); ?></p>
                            <h2>در حال بسته بندی</h2>
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
                                        </div>

                                        <div  class="product-list">

                                            <ul>
                                                <span>نام</span>
                                                <span>تعداد</span>
                                                <?php
                                                $count_p_packing = 1;
                                                $sum_p   = 0;

                                                foreach ($item_packing->get_items() as  $p_item_packing){ ?>
                                                    <li>
                                                        <p> <?php echo $count_p_packing?></p>
                                                        <p> <?php echo $p_item_packing->get_name(); ?> </p>
                                                        <p> <?php echo  $p_item_packing->get_quantity(); ?> <span>عدد</span></p>
                                                    </li>
                                                    <?php $count_p_packing++;  $sum_p+=$p_item_packing->get_quantity();  } ?>
                                            </ul>

                                        </div>
                                        <div class="order-content-packing">

                                            <div class="order_count">
                                                <p>تعداد کل محصول :<span><?php echo $sum_p; ?></span></p>
                                            </div>

                                            <div class="print">
                                                <a class="print_invoice" target="_blank" title="document-26"  href="<?php echo RE_ADMIN_DELIVERING."print-page.php?p_id=".$item_packing->get_id(); ?>" >Print</a>
                                            </div>

                                            <div class="change-status">
                                                <p class="change_p_status_to_delivering" data-product="<?php echo $item_packing->get_id();?>"> > </p>
                                            </div>

                                            <div class="change-status">
                                                <p class="change_p_status_to_processing" data-product="<?php echo $item_packing->get_id();?>"> < </p>
                                            </div>

                                        </div>


                                    </li>

                                    <?php $order_count_packing++; } ?>
                            </ul>
                        </div>





                        <?php $order_count_deliver = 1;
                        $orders_is_deliver = wc_get_orders(array('status' => 'on-deliver', 'limit' => -1 ) );
                        ?>

                        <div class="first_item">
                            <p>تعداد <?php echo  count( $orders_is_deliver ); ?></p>
                            <h2>تحویل راننده</h2>
                            <ul class="orders-list">
                                <?php

                                foreach ($orders_is_deliver as $item_deliver  ) {
                                    $date_explode_deliver = explode('|' , get_post_meta($item_deliver->get_id() , 'daypart' , true) );
                                    ?>

                                    <li>
                                        <span><?php echo $order_count_deliver; ?></span>
                                        <div class="details-con">

                                            <div>
                                                <span>#<?php echo $item_deliver->get_id();?></span>
                                            </div>
                                            <div>
                                                <span><?php echo jdate('l' , strtotime($date_explode_deliver[1]) ) ; ?></span>
                                                <span><?php echo str_replace('_' , ' الی ' , $date_explode_deliver[0]  ); ?></span>
                                                <span><?php echo $date_explode_deliver[1] ; ?></span>
                                            </div>

                                        </div>

                                        <div  class="product-list">

                                            <ul>
                                                <span>نام</span>
                                                <span>تعداد</span>
                                                <?php
                                                $count_p_deliver = 1;
                                                $sum_p   = 0;

                                                foreach ($item_deliver->get_items() as  $p_item_deliver){ ?>
                                                    <li>
                                                        <p> <?php echo $count_p_deliver?></p>
                                                        <p> <?php echo $p_item_deliver->get_name(); ?> </p>
                                                        <p> <?php echo  $p_item_deliver->get_quantity(); ?> <span>عدد</span></p>
                                                    </li>
                                                    <?php $count_p_deliver++; $sum_p+=$p_item_deliver->get_quantity();  } ?>
                                            </ul>

                                        </div>
                                        <div class="order-content-delivering">

                                            <div class="order_count">
                                                <p>تعداد کل محصول :<span><?php echo $sum_p; ?></span></p>
                                            </div>

                                        </div>


                                    </li>

                                    <?php $order_count_deliver++; } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-2">
                        <div class="main-stock-con">
                            <h1>به روز رسانی موجودی</h1>
                            <table>
                                <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th>شناسه</th>
                                    <th>نام</th>
                                    <th>موجودی</th>
                                    <th>به روز رسانی موجودی</th>
                                </tr>
                                </thead>


                                <tbody>
                                <?php
                                $query = new WC_Product_Query( array(
                                    'orderby'   => 'meta_value_num',
                                    'meta_key'  => '_stock',
                                    'order'     => 'ASC',
                                    'limit'     => -1 ,
                                    'status' => 'publish'
                                ));
                                $query = $query->get_products();
                                $number = 1;
                                foreach ($query  as $item) {

                                    ?>
                                    <tr>
                                        <td class="number-con"> <?php echo $number; ?> </td>
                                        <td class="id-con"> <?php echo $item->get_id() ?> </td>
                                        <td class="name-con"> <?php echo $item->get_name() ?> </td>
                                        <td class="count-con"> <?php echo $item->get_stock_quantity() ?> </td>
                                        <td>
                                            <div>
                                                <input type="number"   class="p-count  p-stock-<?php echo $item->get_id() ?>" value="<?php echo $item->get_stock_quantity() ?> ">
                                            </div>
                                            <div>
                                                <button class="btn-update" data-pid="<?php echo $item->get_id() ?>">به روز رسانی</button>
                                            </div>
                                        </td>

                                    </tr>
                                    <?php $number++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div class="loader">
                <div class="loadingio-spinner-disk-afj302ypqb">
                    <div class="ldio-vn8bwgjykoh">
                        <div><div></div><div></div></div></div></div>
            </div>

            </body>
            </html>

            <?php
        }
    }else{
        wp_redirect('/');
    }


});







