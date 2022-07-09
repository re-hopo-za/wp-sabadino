<?php

namespace Sabadino\features\order_delivery;


use Sabadino\features\delivering_time\DeliveringPublic;
use Sabadino\includes\Functions;
use WC_Product_Query;

class Delivery{


    protected static $_instance = null;
    public static function get_instance(){
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {
        add_action( 'init', [$this , 'customStatus'] ,99 );
        add_filter( 'wc_order_statuses', [$this , 'addInStatusList']);
        add_filter( 'bulk_actions-edit-shop_order', [$this , 'addInBulkStatusList'] , 99, 1);
        add_filter( 'page_template', [$this , 'pageTemplates'] );

        add_action("wp_ajax_re_update_product_inventory" , [$this , 'updateProductInventory'] );
        add_action("wp_ajax_re_get_products_status" , [$this , 'packerUpdate'] );
        add_action("wp_ajax_re_delivery_status" , [$this , 'deliveryUpdate'] );

        add_action("wp_ajax_re_change_status_to_packing" ,[$this , 'packerPageUpdate'] );
        add_action("wp_ajax_re_change_p_status_to_processing" ,[$this , 'toProcessing'] );
        add_action('wp_ajax_re_change_p_status_to_delivering' ,[$this , 'toDelivering'] );
        add_action('wp_ajax_re_delivery_again' ,[$this , 'deliveringAgain'] );



        self::defines();
        self::includes();

    }


    public static function registration()
    {
        add_role( 'order_packer' , 'بسته بند سفارشات', array( 'read' => true, 'level_0' => true ) );
        add_role( 'order_deliver', 'تحویل دهنده سفارشات', array( 'read' => true, 'level_0' => true ) );
    }


    public static function defines()
    {
        define('RE_DELIVERING_ASSETS' , plugin_dir_url(__FILE__).'assets/');
        define('RE_ADMIN_DELIVERING'  , plugin_dir_url(__FILE__).'include/');
        define('RE_DELIVERING_ADMIN'  , ZA_FEATURES_PATH.'order_delivery/include/');
    }


    public static function includes()
    {
        if (  Functions::isPage('packer_page')  || Functions::isPage('deliver_page')  ){
            include RE_DELIVERING_ADMIN.'enqueue.php';
            include RE_DELIVERING_ADMIN.'packer-page.php';
            include RE_DELIVERING_ADMIN.'deliver-page.php';
        }
    }

    public function customStatus()
    {
        register_post_status('wc-is-packing', array(
            'label' => _x('بسته بند', 'Order status', 'woocommerce'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list' => true,
            'label_count' => _n_noop('در حال بسته بندی<span class="count">(%s)</span>', 'در حال بسته بندی <span class="count">(%s)</span>')
        ));
        register_post_status('wc-on-deliver', array(
            'label' => _x('تحویل راننده', 'Order status', 'woocommerce'),
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list' => true,
            'label_count' => _n_noop('تحویل راننده<span class="count">(%s)</span>', 'تحویل راننده <span class="count">(%s)</span>')
        ));
    }

    public function addInStatusList( $order_statuses )
    {
        $order_statuses['wc-is-packing'] = _x('بسته بند', 'Order status', 'woocommerce');
        $order_statuses['wc-on-deliver'] = _x('تحویل راننده', 'Order status', 'woocommerce');

        return $order_statuses;
    }

    public function addInBulkStatusList( $actions )
    {
        $actions['mark_is-packing'] = __('بسته بند', 'woocommerce');
        $actions['mark_on-deliver'] = __('تحویل راننده', 'woocommerce');
        return $actions;
    }

    public function updateProductInventory()
    {
        $p_id  = $_POST['p_id'];
        $count = $_POST['count'];

        $product = wc_get_product($p_id);
        $product->set_manage_stock(true);
        $product->set_stock_quantity($count);

        $product->save();
        ?>
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
            'order' => 'ASC',
            'limit' => -1
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
        <?php
        exit();
    }

    public function packerUpdate()
    {
        $order_count = 1;
        $orders_is_processing = wc_get_orders( array(
            'status'       => 'processing' ,
            'orderby'      => 'meta_value_num',
            'meta_key'     => array('re_micro_time' ) ,
            'order'        => 'ASC',
            'limit'        => -1
        )); ?>

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

                    <?php $order_count++; } ?>
            </ul>
        </div>


        <?php
        $order_count_packing = 1;
        $orders_is_packing = wc_get_orders(array('status' => 'is-packing' , 'limit'     => -1 ) );
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
        $orders_is_deliver = wc_get_orders(array('status' => 'on-deliver' , 'limit'     => -1 ) );
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
        <?php
        exit();
    }

    public function deliveryUpdate()
    {

        $p_id   = $_POST['p_id'];
        $status = $_POST['status'];

        $status_text = '';
        if ($status == 1 ){
            $status_text = 'wc-completed';
        }elseif ($status == 2){
            $status_text = 'wc-is-packing';
        }elseif ($status == 3){
            $status_text = 'wc-cancelled';
        }

        global $wpdb;
        $wpdb->update( $wpdb->prefix.'posts', array( 'post_status' => $status_text ), array( 'ID' => $p_id ), array( '%s' ), array( '%d' ) );


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
                                <?php (new DeliveringPublic)->checkoutPage(); ?>
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
        <?php
        exit();
    }

    public function packerPageUpdate(){

        $p_id = $_POST['p_id'];

        global $wpdb;
        $wpdb->update( $wpdb->prefix.'posts', array( 'post_status' => 'wc-is-packing' ), array( 'ID' => $p_id ), array( '%s' ), array( '%d' ) );


        $order_count = 1;
        $orders_is_processing = wc_get_orders( array(
            'status'       => 'processing' ,
            'orderby'   => 'meta_value_num',
            'meta_key'  => array('re_micro_time'  ) ,
            'order'        => 'ASC',
            'limit'     => -1
        )); ?>

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
                                        <p> <?php echo $count_p; ?></p>
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

                    <?php $order_count++; } ?>
            </ul>
        </div>

        <?php
        $order_count_packing = 1;
        $orders_is_packing = wc_get_orders(array('status' => 'is-packing' , 'limit'     => -1 ) );
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
        $orders_is_deliver = wc_get_orders(array('status' => 'on-deliver' , 'limit'     => -1 ) );
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
        <?php
        exit();
    }

    public function toProcessing()
        {
            $p_id = $_POST['p_id'];
            global $wpdb;
            $wpdb->update( $wpdb->prefix.'posts', array( 'post_status' => 'wc-processing' ), array( 'ID' => $p_id ), array( '%s' ), array( '%d' ) );


            $order_count = 1;
            $orders_is_processing = wc_get_orders( array(
                'status'       => 'processing' ,
                'orderby'   => 'meta_value_num',
                'meta_key'  => array('re_micro_time'   ) ,
                'order'        => 'ASC',
                'limit'     => -1
            )); ?>

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

                        <?php $order_count++; } ?>
                </ul>
            </div>

            <?php
            $order_count_packing = 1;
            $orders_is_packing = wc_get_orders(array('status' => 'is-packing' , 'limit'     => -1 ) );
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
            $orders_is_deliver = wc_get_orders(array('status' => 'on-deliver'  , 'limit'     => -1 ) );
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
            <?php
            exit();
        }

    public function toDelivering()
        {
            $p_id = $_POST['p_id'];
            global $wpdb;
            $wpdb->update( $wpdb->prefix.'posts', array( 'post_status' => 'wc-on-deliver' ), array( 'ID' => $p_id ), array( '%s' ), array( '%d' ) );


            $order_count = 1;
            $orders_is_processing = wc_get_orders( array(
                'status'       => 'processing' ,
                'orderby'   => 'meta_value_num',
                'meta_key'  => array('re_micro_time'  ) ,
                'order'        => 'ASC',
                'limit'     => -1
            )); ?>

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

                        <?php $order_count++; } ?>
                </ul>
            </div>

            <?php
            $order_count_packing = 1;
            $orders_is_packing = wc_get_orders(array('status' => 'is-packing' , 'limit'     => -1 ) );
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
            $orders_is_deliver = wc_get_orders(array('status' => 'on-deliver' , 'limit'     => -1 ) );
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
            <?php
            exit();
        }

    public function deliveringAgain()
    {
        $p_id          = $_POST['p_id'];
        $deliver_again = $_POST['date'];

        $order  = wc_get_order($p_id);
        $order->update_meta_data('daypart' , $deliver_again );
        $order->set_status('is-packing');
        $order->save();


        $order_count = 1;
        $orders_is_processing = wc_get_orders( array(
            'status'    => 'on-deliver' ,
            'orderby'   => 'meta_value_num',
            'meta_key'  => array('re_micro_time') ,
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
                                <p>وضعیت :<span><?php echo $item_process->get_payment_method() ==='place' ? '<span style="color:crimson">پرداخت در محل</span>': '<span style="color:chartreuse ">پرداخت اینترنتی</span>'  ; ?></span></p>
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
                                <?php (new DeliveringPublic)->checkoutPage();  ?>
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
        <?php
        exit();

    }

    public function pageTemplates( $page_template )
    {
        if ( is_page( 'packer_page' ) ) {
            $page_template = RE_DELIVERING_ADMIN . '../order-status-template.php';
        }
        if ( is_page( 'deliver_page' ) ) {
            $page_template = RE_DELIVERING_ADMIN . '../delivery-template.php';
        }
        return $page_template;
    }


}

