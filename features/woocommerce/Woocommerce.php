<?php

namespace Sabadino\features\woocommerce;


use Sabadino\includes\Functions;
use Sabadino\features\product_slider\Products;
use WC_Query;

class Woocommerce
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


        add_filter( 'woo_wallet_nav_menu_items' ,[$this , 'wooWalletUnsetUnusedItem'] ,10 ,1 );

        add_action( "woocommerce_review_order_after_cart_contents",[$this , "addOrderDetails"]); //tr
        add_action( "woocommerce_cart_totals_before_order_total",[$this , "addOrderDetails"]); //tr

        add_action( 'add_meta_boxes', [ $this, 'add' ] );
        add_action( 'save_post'     , [ $this, 'save' ]);

        add_action( 'wp_head'    , [$this , 'orderStep'] );
        add_action( 'wp_footer'  , [$this , 'mobileMenu'], 130 );

        add_action( 'wp_footer'  , [$this , 'widgetCart'] );

        add_filter( 'woocommerce_checkout_fields' ,[$this , 'checkoutFields'] );
        add_filter( 'woocommerce_locate_template',[$this , 'woocommerecTemplates'], 20, 2 );
        add_shortcode( 'min-cart-widget-on-header', [$this , 'miniCart'] );


        add_action( 'product_cat_add_form_fields' , [$this , 'addFieldInAddCat'], 10, 2 );
        add_action( 'product_cat_edit_form_fields', [$this , 'addFieldInEditCat'], 10, 2 );
        add_action( 'edit_term'    , [$this   , 'createCat'], 10, 3 );
        add_action( 'created_terms' , [$this   , 'createCat'], 10, 3 );
        add_action( 'woocommerce_after_shop_loop', [$this , 'showExtraCat'], 5 );


        add_filter('woocommerce_my_account_my_orders_columns', [$this , 'addCustomColumnsInOrders']);
        add_action('woocommerce_my_account_my_orders_column_order-delivery',[$this , 'showDeliveryTimeInOrder']);

        add_filter( 'woocommerce_account_menu_items',  [$this , 'removeItemsFromMyAccount'] );
        add_action( 'woocommerce_check_cart_items', [$this , 'limitCart'] );

        add_action('woocommerce_thankyou',  [$this , 'onSaveOrder'] ,11 ,1 );
        add_action('woocommerce_thankyou',  [$this , 'sendDeliverSms'] ,11 ,1 );
        add_action('woocommerce_order_status_changed', [$this , 'statusChanged'] ,99 ,4 );

        add_filter('woocommerce_product_data_tabs' ,[$this ,'extraProductSettingSection']  , 20 );
        add_action('woocommerce_product_data_panels' , [$this ,'addNewProductPanelInProducts'] );
        add_action('woocommerce_process_product_meta' ,[$this ,'saveExtraDataInProducPage'] , 20 ,2);
        add_filter( 'woocommerce_billing_fields'      , [$this ,'billingCustomFields'] ,99 ,1);
        add_filter( 'woocommerce_sale_flash' , [$this ,'salesFlash'] ,10 ,3);
       

        add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false');
      
 
      


      
      
 }

      public function salesFlash( $text, $post, $product ) {
         
            $regular_price = 0;
            $sale_price    = 0;
            $final_price   = 0;
            if ( $product->get_type() == 'simple'){
            $regular_price = (float) $product->get_regular_price();
            $sale_price = (float) $product->get_price();
            }elseif( $product->get_type() == 'variable' ){
            $whole_price = Products::getVariation( (object) $product ,true );
            $regular_price = isset( $whole_price['regular_price'] ) ? (float) $whole_price['regular_price'] : 0 ;
            $sale_price    = isset( $whole_price['sale_price'] ) ? (float) $whole_price['sale_price'] : 0 ;

            }
            if ( $sale_price > 0 ){
                $final_price = round( 100 - ( $sale_price / $regular_price * 100 )  ) ;
            }

            return '<span class="onsale"> %'.$final_price.' تخفیف </span>';

         
    }

    public function billingCustomFields( $fields ) {
        $fields ['billing_email']['required']    = false;
        return $fields;
    }


    public function woocommerecTemplates( $template, $template_name ){

        $TEMPLATE_PATH = ZA_ROOT_PATH.'templates/';
        if ( $template_name == 'myaccount/form-edit-account.php' ){
            return $TEMPLATE_PATH.'myaccount/form-edit-account.php';
        }
        elseif ( $template_name == 'cart/cart.php') {
            return $TEMPLATE_PATH.'cart/cart.php';
        }
        elseif ( $template_name == 'cart/mini-cart.php') {
            return $TEMPLATE_PATH.'cart/mini-cart.php';
        }
        elseif ( $template_name == 'loop/add-to-cart.php') {
            return $TEMPLATE_PATH.'loop/add-to-cart.php';
        }
        elseif ( $template_name == 'loop/rating.php') {
            return $TEMPLATE_PATH.'loop/rating.php';
        }
        elseif ( $template_name == 'cart/cart-item-data.php') {
            return $TEMPLATE_PATH.'cart/cart-item-data.php';
        }
        elseif ( $template_name == 'loop/price.php') {
            return $TEMPLATE_PATH.'loop/price.php';
        }
        elseif ( $template_name == 'loop/sale-flash.php') {
            return $TEMPLATE_PATH.'loop/sale-flash.php';
        }
        elseif ( $template_name == 'loop/title.php') {
            return $TEMPLATE_PATH.'loop/title.php';
        }
        elseif ( $template_name == 'loop/orderby.php') {
            return $TEMPLATE_PATH.'loop/orderby.php';
        }
        elseif ( $template_name == 'cart/cross-sells.php') {
            return $TEMPLATE_PATH.'woocommerce/cart/cross-sells.php';
        }
        elseif ( $template_name == 'cart/cart-totals.php') {
            return $TEMPLATE_PATH.'cart/cart-totals.php';
        }
        elseif ( $template_name == 'global/quantity-input.php') {
            return $TEMPLATE_PATH.'global/quantity-input.php';
        }
        elseif ( $template_name == 'checkout/form-checkout.php') {
            return $TEMPLATE_PATH.'checkout/form-checkout.php';
        }
        elseif ( $template_name == 'checkout/thankyou.php') {
            return $TEMPLATE_PATH.'checkout/thankyou.php';
        }
 

        elseif ( $template_name == 'single-product/add-to-cart/simple.php') {
            return $TEMPLATE_PATH.'single-product/add-to-cart/simple.php';
        }
        elseif ( $template_name == 'single-product/add-to-cart/variable.php') {
            return $TEMPLATE_PATH.'single-product/add-to-cart/variable.php';
        }

        else{
            return $template;
        }
    }



    public function addOrderDetails(){
        ?>

            <tr class="re-additional-field">
                <td class="re-td-send">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="45" height="45" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g transform="matrix(-1,0,0,1,512,0)">
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M476.158,231.363l-13.259-53.035c3.625-0.77,6.345-3.986,6.345-7.839v-8.551c0-18.566-15.105-33.67-33.67-33.67h-60.392    V110.63c0-9.136-7.432-16.568-16.568-16.568H50.772c-9.136,0-16.568,7.432-16.568,16.568V256c0,4.427,3.589,8.017,8.017,8.017    c4.427,0,8.017-3.589,8.017-8.017V110.63c0-0.295,0.239-0.534,0.534-0.534h307.841c0.295,0,0.534,0.239,0.534,0.534v145.372    c0,4.427,3.589,8.017,8.017,8.017c4.427,0,8.017-3.589,8.017-8.017v-9.088h94.569c0.008,0,0.014,0.002,0.021,0.002    c0.008,0,0.015-0.001,0.022-0.001c11.637,0.008,21.518,7.646,24.912,18.171h-24.928c-4.427,0-8.017,3.589-8.017,8.017v17.102    c0,13.851,11.268,25.119,25.119,25.119h9.086v35.273h-20.962c-6.886-19.883-25.787-34.205-47.982-34.205    s-41.097,14.322-47.982,34.205h-3.86v-60.393c0-4.427-3.589-8.017-8.017-8.017c-4.427,0-8.017,3.589-8.017,8.017v60.391H192.817    c-6.886-19.883-25.787-34.205-47.982-34.205s-41.097,14.322-47.982,34.205H50.772c-0.295,0-0.534-0.239-0.534-0.534v-17.637    h34.739c4.427,0,8.017-3.589,8.017-8.017s-3.589-8.017-8.017-8.017H8.017c-4.427,0-8.017,3.589-8.017,8.017    s3.589,8.017,8.017,8.017h26.188v17.637c0,9.136,7.432,16.568,16.568,16.568h43.304c-0.002,0.178-0.014,0.355-0.014,0.534    c0,27.996,22.777,50.772,50.772,50.772s50.772-22.776,50.772-50.772c0-0.18-0.012-0.356-0.014-0.534h180.67    c-0.002,0.178-0.014,0.355-0.014,0.534c0,27.996,22.777,50.772,50.772,50.772c27.995,0,50.772-22.776,50.772-50.772    c0-0.18-0.012-0.356-0.014-0.534h26.203c4.427,0,8.017-3.589,8.017-8.017v-85.511C512,251.989,496.423,234.448,476.158,231.363z     M375.182,144.301h60.392c9.725,0,17.637,7.912,17.637,17.637v0.534h-78.029V144.301z M375.182,230.881v-52.376h71.235    l13.094,52.376H375.182z M144.835,401.904c-19.155,0-34.739-15.583-34.739-34.739s15.584-34.739,34.739-34.739    c19.155,0,34.739,15.583,34.739,34.739S163.99,401.904,144.835,401.904z M427.023,401.904c-19.155,0-34.739-15.583-34.739-34.739    s15.584-34.739,34.739-34.739c19.155,0,34.739,15.583,34.739,34.739S446.178,401.904,427.023,401.904z M495.967,299.29h-9.086    c-5.01,0-9.086-4.076-9.086-9.086v-9.086h18.171V299.29z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M144.835,350.597c-9.136,0-16.568,7.432-16.568,16.568c0,9.136,7.432,16.568,16.568,16.568    c9.136,0,16.568-7.432,16.568-16.568C161.403,358.029,153.971,350.597,144.835,350.597z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M427.023,350.597c-9.136,0-16.568,7.432-16.568,16.568c0,9.136,7.432,16.568,16.568,16.568    c9.136,0,16.568-7.432,16.568-16.568C443.591,358.029,436.159,350.597,427.023,350.597z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M332.96,316.393H213.244c-4.427,0-8.017,3.589-8.017,8.017s3.589,8.017,8.017,8.017H332.96    c4.427,0,8.017-3.589,8.017-8.017S337.388,316.393,332.96,316.393z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M127.733,282.188H25.119c-4.427,0-8.017,3.589-8.017,8.017s3.589,8.017,8.017,8.017h102.614    c4.427,0,8.017-3.589,8.017-8.017S132.16,282.188,127.733,282.188z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M278.771,173.37c-3.13-3.13-8.207-3.13-11.337,0.001l-71.292,71.291l-37.087-37.087c-3.131-3.131-8.207-3.131-11.337,0    c-3.131,3.131-3.131,8.206,0,11.337l42.756,42.756c1.565,1.566,3.617,2.348,5.668,2.348s4.104-0.782,5.668-2.348l76.96-76.96    C281.901,181.576,281.901,176.501,278.771,173.37z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                        </g>
                 </svg>
                 <p>ارسال</p>
                 </td>
                 <td class="re-td-free">
                     <?php
                     if(
                         !empty(WC()->session->get('cart_totals'))
                         &&
                         is_array(WC()->session->get('cart_totals'))
                         &&
                         array_key_exists('shipping_total',WC()->session->get('cart_totals'))
                     ){
                         echo wc_price(WC()->session->get('cart_totals')['shipping_total']);
                     }
                     ?>
                 </td>
             </tr>
            <tr class="re-additional-field">
                <td class="re-td-save">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="45" height="45" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M85.072,454.931c-1.859-1.861-4.439-2.93-7.069-2.93s-5.21,1.069-7.07,2.93c-1.86,1.861-2.93,4.44-2.93,7.07    s1.069,5.21,2.93,7.069c1.86,1.86,4.44,2.931,7.07,2.931s5.21-1.07,7.069-2.931c1.86-1.859,2.931-4.439,2.931-7.069    S86.933,456.791,85.072,454.931z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M469.524,182.938c-1.86-1.861-4.43-2.93-7.07-2.93c-2.63,0-5.21,1.069-7.07,2.93c-1.859,1.86-2.93,4.44-2.93,7.07    s1.07,5.21,2.93,7.069c1.86,1.86,4.44,2.931,7.07,2.931c2.64,0,5.21-1.07,7.07-2.931c1.869-1.859,2.939-4.439,2.939-7.069    S471.393,184.798,469.524,182.938z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>
                            <g xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M509.065,2.929C507.189,1.054,504.645,0,501.992,0L255.998,0.013c-5.522,0-9.999,4.478-9.999,10V38.61l-94.789,25.399    c-5.335,1.43-8.501,6.913-7.071,12.247l49.127,183.342l-42.499,42.499c-5.409-7.898-14.491-13.092-24.764-13.092H30.006    c-16.542,0-29.999,13.458-29.999,29.999v162.996C0.007,498.542,13.464,512,30.006,512h95.998c14.053,0,25.875-9.716,29.115-22.78    l11.89,10.369c9.179,8.004,20.939,12.412,33.118,12.412h301.867c5.522,0,10-4.478,10-10V10    C511.992,7.348,510.94,4.804,509.065,2.929z M136.002,482.001c0,5.513-4.486,10-10,10H30.005c-5.514,0-10-4.486-10-10V319.005    c0-5.514,4.486-10,10-10h37.999V424.2c0,5.522,4.478,10,10,10s10-4.478,10-10V309.005h37.999c5.514,0,10,4.486,10,10V482.001z     M166.045,80.739l79.954-21.424V96.37l-6.702,1.796c-2.563,0.687-4.746,2.362-6.072,4.659s-1.686,5.026-0.999,7.588    c3.843,14.341-4.698,29.134-19.039,32.977c-2.565,0.688-4.752,2.366-6.077,4.668c-1.325,2.301-1.682,5.035-0.989,7.599    l38.979,144.338h-20.07l-10.343-40.464c-0.329-1.288-0.905-2.475-1.676-3.507L166.045,80.739z M245.999,142.229v84.381    l-18.239-67.535C235.379,155.141,241.614,149.255,245.999,142.229z M389.663,492H200.125V492c-7.345,0-14.438-2.658-19.974-7.485    l-24.149-21.061V325.147l43.658-43.658l7.918,30.98c1.132,4.427,5.119,7.523,9.688,7.523l196.604,0.012c7.72,0,14,6.28,14,14    c0,7.72-6.28,14-14,14H313.13c-5.522,0-10,4.478-10,10c0,5.522,4.478,10,10,10h132.04c7.72,0,14,6.28,14,14c0,7.72-6.28,14-14,14    H313.13c-5.522,0-10,4.478-10,10c0,5.522,4.478,10,10,10h110.643c7.72,0,14,6.28,14,14c0,7.72-6.28,14-14,14H313.13    c-5.522,0-10,4.478-10,10c0,5.522,4.478,10,10,10h76.533c7.72,0,14,6.28,14,14C403.662,485.72,397.382,492,389.663,492z     M491.994,492h-0.001h-71.359c1.939-4.273,3.028-9.01,3.028-14s-1.089-9.727-3.028-14h3.139c18.747,0,33.999-15.252,33.999-33.999    c0-5.468-1.305-10.635-3.609-15.217c14.396-3.954,25.005-17.149,25.005-32.782c0-7.584-2.498-14.595-6.711-20.255V235.007    c0-5.522-4.478-10-10-10c-5.522,0-10,4.478-10,10v113.792c-2.35-0.515-4.787-0.795-7.289-0.795h-0.328    c1.939-4.273,3.028-9.01,3.028-14c0-18.748-15.252-33.999-33.999-33.999h-16.075c17.069-7.32,29.057-24.286,29.057-44.005    c0-26.389-21.468-47.858-47.857-47.858c-26.388,0-47.857,21.469-47.857,47.858c0,19.719,11.989,36.685,29.057,44.005h-54.663    V109.863c17.864-3.893,31.96-17.988,35.852-35.853h75.221c3.892,17.865,17.988,31.96,35.852,35.853v31.09c0,5.522,4.478,10,10,10    s10-4.478,10-10v-40.018c0-5.522-4.478-10-10-10c-14.847,0-26.924-12.079-26.924-26.925c0-5.522-4.478-10-10-10h-93.076    c-5.522,0-10,4.478-10,10c0,14.847-12.078,26.925-26.924,26.925c-5.522,0-10,4.478-10,10v199.069H266V20.011L491.994,20V492z     M378.996,283.858c-15.361,0-27.857-12.497-27.857-27.857s12.497-27.858,27.857-27.858S406.853,240.64,406.853,256    S394.357,283.858,378.996,283.858z" fill="#4eb53e" data-original="#000000" style="" class=""/>
                                </g>
                            </g>

                        </g>
                    </svg>
                    <p>تخفیف</p>
                </td>
                <td class="re-td-money"><?php echo $this->za_discount_total_cart(); ?></td>
            </tr>


        <?php
    }

    public function za_discount_total_cart() {
        global $woocommerce;
        $discount_total=0;
        foreach($woocommerce->cart->get_cart() as $cart_item_key => $values){
            $_product = $values['data'];
            if( $_product->is_on_sale() && $_product->get_sale_price() > 0 &&  $_product->get_regular_price() > 0){
                $regular_price  = $_product->get_regular_price();
                $sale_price     = $_product->get_sale_price();
                $discount       = ( $regular_price - $sale_price) * $values['quantity'];
                $discount_total += $discount;
            }
        }
        return wc_price( $discount_total + $woocommerce->cart->discount_cart );
    }




    public function checkoutFields( $fields ) {
        unset($fields['billing']['billing_company']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_state']);

        return $fields;
    }



    public function add() {
        $screens = [ 'product' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'hamfy_meta_box_id',
                __('اولویت بندی محصولات '),
                [ self::class, 'html' ] ,
                $screen ,
                'side'
            );
        }
    }


    public static function html( $post ) {
        $support = get_post_meta($post->ID, '_product_show_order', true );
        ?>
        <div class="za-meta-box">
            <div class="za-support">
                <label for="za_support_days_field"><?php echo 'تعیین اولویت محصول ';  ?></label>
                <input type="number" name="za_support_days_field" placeholder="<?php echo  $support; ?>" id="za_support_days_field" value="<?php echo $support; ?>" >
            </div>
        </div>

        <style>
            .za-meta-box  {
                padding:10px;
                border:1px solid #eee;
            }
            .za-meta-box>div:nth-child(1)  {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .za-meta-box input[type="number" ]  {
                background-color: #2321;
                width: 40%;
                margin: 5px;
                text-align: center
            }
            .za-meta-box  label{
                display: block;
                font-size: 12px;
                font-weight: bold!important;;
            }
            .za-meta-box>div:not(:nth-child(1)) {
                display: flex;
                padding: 0 10px;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .za-meta-box>div:not(:nth-child(1)) input{
                position: relative;
                left: 25px;
            }


        </style>
        <?php
    }


    public static function save( int $post_id ) {
        if (!is_admin()) return;
        if (!current_user_can('edit_post', $post_id )) return;

        if ( isset($_POST['za_support_days_field']) ) {
            update_post_meta(
                (int) $post_id,
                '_product_show_order',
                (int)$_POST['za_support_days_field']
            );
        }
    }

    public function miniCart()
    {
        ?>
        <div class="sabadino-shopping-cart cart-widget-opener" title="<?php echo esc_attr__( 'Shopping cart', 'woocommerce' ); ?>">
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" onclick="return false;">
                <div class="sabadino-cart-icon wd-tools-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve" width="60px" height="60px">
                        <g>
                            <g>
                                <path d="M46.83,24.38c0.43-0.98-0.29-2.08-1.36-2.08c-5.65,0-10.86,0-16.06,0c-0.75,0-1.55,0.15-2.23-0.07
                                    c-0.13-0.04-0.25-0.1-0.37-0.18c-0.74-0.48-0.87-1.53-0.28-2.19c0.21-0.24,0.42-0.41,0.63-0.41c7.49-0.1,14.98-0.11,22.47-0.06
                                    c1.63,0.01,2.13,1.01,1.5,2.49c-2.36,5.51-4.68,11.04-7.18,16.5c-0.31,0.68-1.56,1.31-2.39,1.32c-6.37,0.11-12.74,0-19.1,0.1
                                    c-1.9,0.03-2.34-1.01-2.74-2.48c-1.88-6.87-3.88-13.71-5.68-20.6c-0.54-2.08-1.47-3.09-3.66-2.75c-0.64,0.1-1.38,0.17-1.94-0.07
                                    c-0.14-0.06-0.27-0.14-0.4-0.24c-0.73-0.57-0.78-1.7-0.06-2.28c0.14-0.11,0.28-0.19,0.42-0.2c1.95-0.17,3.97-0.29,5.87,0.04
                                    c0.75,0.13,1.61,1.4,1.87,2.31c1.98,6.84,3.93,13.7,5.67,20.6c0.57,2.27,1.58,2.96,3.86,2.87c4.49-0.19,8.99-0.12,13.48-0.02
                                    c1.65,0.04,2.52-0.5,3.15-2.06C43.69,31.47,45.2,28.09,46.83,24.38z"/>
                                <path d="M49.23,45.39c0.01,0.13,0.01,0.26-0.01,0.39c-0.36,2.01-1.45,3.28-3.66,3.21c-2.3-0.07-3.34-1.55-3.4-3.68
                                    c-0.06-1.94,1.77-3.67,3.64-3.48C47.91,42.05,49.02,43.31,49.23,45.39z"/>
                                <path d="M19.82,41.8c0.13-0.02,0.26-0.02,0.39-0.01c2.08,0.25,3.34,1.41,3.41,3.51c0.07,2.15-1.1,3.52-3.32,3.7
                                    c-2.05,0.17-3.91-1.54-3.78-3.56C16.66,43.38,17.8,42.13,19.82,41.8z"/>
                            </g>
                        </g>
                    </svg>
                    <span class="sabadino-cart-number"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?> </span>
                </div>
                <span class="sabadino-cart-totals wd-tools-text">
                    <span class="sabadino-cart-subtotal"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                </span>
            </a>
        </div>
        <?php
    }


    public static function checkoutSteps() {
        if( isset( $_GET['key'] ) && is_wc_endpoint_url( 'order-received' ) ) {
            echo do_shortcode('[elementor-template id="201"]' );
        }
        ?>
        <div class="sabadino-checkout-steps">
            <ul>
                <li class="step-cart">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
                        <span><img class="step-cart <?php echo (is_cart()) ? 'step-active' : 'step-inactive'; ?>" src="<?php echo ZA_ASSETS_URL; ?>images/cart.png"></span>
                    </a>
                </li>
                <div class="line-step-card">

                </div>
                <li class="step-checkout">
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
                        <span><img class="step-checkout <?php echo (is_checkout() && ! is_order_received_page()) ? 'step-active' : 'step-inactive'; ?>" src="<?php echo ZA_ASSETS_URL; ?>images/checkout.png"></span>
                    </a>
                </li>
                <div class="line-step-card">

                </div>
                <li class="step-complete">
                    <span><img class="step-complete <?php echo (is_order_received_page()) ? 'step-active' : 'step-inactive'; ?>" src="<?php echo ZA_ASSETS_URL; ?>images/complete.png"></span>
                </li>
            </ul>
        </div>
        <?php
    }

    public static function orderStep()
    {
       if ( is_cart() || is_checkout()   ){
           self::checkoutSteps();
       }
    }




    public static function sabadinoShopPageLink( $keep_query = false, $taxonomy = '' ) {
        // Base Link decided by current page
        if ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) || is_shop() ) {
            $link = get_permalink( wc_get_page_id( 'shop' ) );
        } elseif( is_product_category() ) {
            $link = get_term_link( get_query_var('product_cat'), 'product_cat' );
        } elseif( is_product_tag() ) {
            $link = get_term_link( get_query_var('product_tag'), 'product_tag' );
        } else {
            $queried_object = get_queried_object();
            $link           = get_term_link( $queried_object->slug, $queried_object->taxonomy );
        }

        if( $keep_query ) {

            // Min/Max
            if ( isset( $_GET['min_price'] ) ) {
                $link = add_query_arg( 'min_price', wc_clean( $_GET['min_price'] ), $link );
            }

            if ( isset( $_GET['max_price'] ) ) {
                $link = add_query_arg( 'max_price', wc_clean( $_GET['max_price'] ), $link );
            }

            // Orderby
            if ( isset( $_GET['orderby'] ) ) {
                $link = add_query_arg( 'orderby', wc_clean( $_GET['orderby'] ), $link );
            }

            /**
             * Search Arg.
             * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
             */
            if ( get_search_query() ) {
                $link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
            }

            // Post Type Arg
            if ( isset( $_GET['post_type'] ) ) {
                $link = add_query_arg( 'post_type', wc_clean( $_GET['post_type'] ), $link );
            }

            // Min Rating Arg
            if ( isset( $_GET['min_rating'] ) ) {
                $link = add_query_arg( 'min_rating', wc_clean( $_GET['min_rating'] ), $link );
            }

            // All current filters
            if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
                foreach ( $_chosen_attributes as $name => $data ) {
                    if ( $name === $taxonomy ) {
                        continue;
                    }
                    $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
                    if ( ! empty( $data['terms'] ) ) {
                        $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                    }
                    if ( 'or' == $data['query_type'] ) {
                        $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                    }
                }
            }
        }

        if ( is_string( $link ) ) {
            return $link;
        } else {
            return '';
        }
    }


    public static function getCurrentPageNumber()
    {
        if( ! class_exists('WC_Session_Handler') ) return;
        $s = WC()->session;
        if ( is_null( $s ) ) return 12;

        if ( isset( $_REQUEST['per_page'] ) && ! empty( $_REQUEST['per_page'] ) ) :
            return intval( $_REQUEST['per_page'] );
        elseif ( $s->__isset( 'shop_per_page' ) ) :
            $val = $s->__get( 'shop_per_page' );
            if( ! empty( $val ) )
                return intval( $s->__get( 'shop_per_page' ) );
        endif;
        return 12;
    }


    public static function widgetCart() {
        ?>
        <div class="cart-widget-side">
            <div class="widget-heading">
                <h3 class="widget-title"> سبد خرید </h3>
                <a href="#" class="close-side-widget wd-cross-button wd-with-text-left" onclick="return false"></a>
            </div>
            <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
        </div>
        <?php
    }


    public static function mobileMenu()
    {
        echo '<div class="woodmart-close-side"></div>';

        //if ( wp_is_mobile() ){
            echo '<div class="mobile-nav slide-from-left"> <div class="mobile-menu-tab mobile-pages-menu active">';

            wp_nav_menu(
                [
                    'menu' => 'main-mobile' ,
                    'menu_class' => 'site-mobile-menu',
                    'walker' =>  SABADINO_Mega_Menu_Walker::get_instance()
                ]
            );
            echo '</div>';

            if( is_active_sidebar( 'mobile-menu-widgets' ) ): ?>
                <div class="widgetarea-mobile">
                    <?php dynamic_sidebar( 'mobile-menu-widgets' ); ?>
                </div>
            <?php endif;
            echo '</div>';
       //}
    }




    public function addFieldInAddCat() {
        ?>

        <div class="form-field">
            <label for="seconddesc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label>

            <?php
            $settings = array(
                'textarea_name' => 'sabadino_extra_desc_cat',
                'quicktags' => array( 'buttons' => 'em,strong,link' ),
                'tinymce' => array(
                    'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                    'theme_advanced_buttons2' => '',
                ),
                'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
            );

            wp_editor( '', 'sabadino_extra_desc_cat', $settings );
            ?>

            <p class="description"><?php echo __( 'This is the description that goes BELOW products on the Tag page', 'woocommerce' ); ?></p>
        </div>
        <?php
    }



    public function addFieldInEditCat( $term ) {

        $second_desc = htmlspecialchars_decode( get_term_meta( $term->term_id ,'sabadino_extra_desc_cat' ,true ) );

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="second-desc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label></th>
            <td>
                <?php

                $settings = array(
                    'textarea_name' => 'sabadino_extra_desc_cat',
                    'quicktags' => array( 'buttons' => 'em,strong,link' ),
                    'tinymce' => array(
                        'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                        'theme_advanced_buttons2' => '',
                    ),
                    'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
                );

                wp_editor( $second_desc, 'sabadino_extra_desc_cat', $settings );
                ?>
            </td>
        </tr>
        <?php
    }


    public function createCat( $term_id, $tt_id = '', $taxonomy = '' ) {
        if ( isset( $_POST['sabadino_extra_desc_cat'] ) && 'product_cat' === $taxonomy ) {
            update_term_meta( $term_id, 'sabadino_extra_desc_cat', esc_attr( $_POST['sabadino_extra_desc_cat'] ) );
        }
    }


    public function showExtraCat()
    {
        if ( is_product_taxonomy() ) {
            $term = get_queried_object();
            if ($term && !empty( get_term_meta($term->term_id, 'sabadino_extra_desc_cat', true))) {
                echo '<div class="term-description sabadino-extra-description">' . wc_format_content( htmlspecialchars_decode(get_term_meta( $term->term_id, 'sabadino_extra_desc_cat', true ) ) ) . '</div>';
            }
        }
    }



    public function addCustomColumnsInOrders( $columns )
    {
        $new_columns = [];
        foreach ( $columns as $key => $name ) {
            $new_columns[$key] = $name;
            if ('order-total' === $key) {
                $new_columns['order-delivery'] = __('زمان ارسال', 'woocommerce');
            }
        }
        return $new_columns;
    }


    public function showDeliveryTimeInOrder( $order )
    {
        echo str_replace( '_' , ' الی ' , str_replace( '|' ,' - ' ,$order->get_meta('daypart' , true ) ) );
    }




    public function removeItemsFromMyAccount( $items ) {

        if ( isset(  $items['downloads']  ) ){
            unset( $items['downloads'] );
        }
        return $items;
    }

    public function wooWalletUnsetUnusedItem( $items )
    {
        if ( isset( $items['top_up'] ) ){
            unset( $items['top_up'] );
        }
        if ( isset( $items['transfer'] ) ){
            unset( $items['transfer'] );
        }
        return $items;
    }



    public function limitCart() {
        $minimum_cart_total =(int) get_option( 're_minimum_purchase_amount' , true );
        if( is_cart() || is_checkout() ) {
            $total = WC()->cart->subtotal;
            if( $total <= $minimum_cart_total  ) {
                wc_add_notice( sprintf( '<strong> حداقل میزان خرید  %s %s است</strong>'  .'<br />مبلغ فعلی سبد شما   %s %s میباشد',
                    number_format($minimum_cart_total) ,
                    'تومان' ,
                    number_format($total) ,
                    'تومان'
                ),
              'error' );
            }
        }
    }


    public function onSaveOrder( $order_id )
    {
        $order  = wc_get_order( $order_id );
        $date   = $order->get_meta('daypart' , true );
        $unix   = $order->get_meta('re_micro_time' , true );
        $option =  get_option( 'delivering_time_options' );
        if ( empty( $date )){
            update_post_meta( $order_id , 'daypart' ,
                jdate("Y/m/d", strtotime("+1 day") , '' , '' ,'en').'_'.
                $option['first_time_start'].'_'.$option['first_time_end']
            );
        }
        if ( empty( $unix )){
            update_post_meta( $order_id , 're_micro_time' ,
                date('U' ,strtotime('+1 day' ) + 1 )
            );
        }
    }

    public static function sendDeliverSms( $order_id )
    {
        $order       = wc_get_order( $order_id );
        $userData    = get_user_by('id' , get_current_user_id() );
        $productName = [];


        foreach ($order->get_items() as $item_id => $item_data) {
            $productName[] = "- ".$item_data->get_name()." * ".$item_data->get_quantity()."
";
        }
        $total = Functions::convertNumbers( number_format($order->get_total()) );
        $pName = implode(" ", $productName);
        $text = sprintf('سلام %s  
سفارش شما به شماره %s ثبت شد
آیتم های سفارش : %s 
مبلغ سفارش : %s  تومان
زمان ارسال %s', $userData->first_name , $order->get_id() , $pName , $total ,$order->get_meta('daypart' , true )  );



        $text_admin = sprintf('سفارش  %s به شماره %s ثبت شد
آیتم های سفارش : %s 
مبلغ سفارش : %s  تومان
از اپلیکیشن در محل
زمان ارسال : %s' , $userData->first_name , $order->get_id() , $pName , $total  ,$order->get_meta('daypart' , true ) );


        Functions::sendSmsByService( $text , get_user_meta( get_current_user_id(), 'billing_phone', true) );
        Functions::sendSmsByService( $text_admin ,'09128381226' );

    }



    public function statusChanged( $order_id ,$old_status ,$new_status ,$order )
    {
       
      if($new_status != 'processing' ){
                $user = get_user_by('id' , $order->get_customer_id() );
        $name = $user->first_name;
        $txt  = sprintf('%s عزیز وضعیت سفارش شما از %s به %s  تغییر یافت 
sabadino' ,$name ,Functions::statusTranslater( $old_status ) ,Functions::statusTranslater( $new_status ) );
        Functions::sendSmsByService( $txt ,get_user_meta( $user->ID ,'billing_phone' ,true )  );
      }

    }






    public function extraProductSettingSection($product_data_tabs){
        $product_data_tabs[''] = array(
            'label'    => __( 'ضریب', 'woocommerce' ),
            'target'   => 'assembly_product_data',
            'class'    => array( 'assembly-product-data' )
        );
        return $product_data_tabs;
    }

    public function addNewProductPanelInProducts()
    {
        ?>
        <div id="assembly_product_data" class="panel woocommerce_options_panel">
            <p>ضریب محصول </p>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id'          => '_factor_count',
                    'label'       => __( 'تعیین مقدار ضریب', 'woocommerce' ),
                    'placeholder' => _x( 'ضریب', 'placeholder', 'woocommerce' ),
                    'description' => __( 'برای تعیین مقدار ضریب در هر بار اضافه کردن به سبد', 'woocommerce' ),
                )
            );
            ?>
        </div>
        <?php
    }


    public function saveExtraDataInProducPage( $product_id , $post ){
        if (isset($_POST['_factor_count'])){
            update_post_meta(
                $product_id ,
                '_factor_count' ,
                wc_clean($_POST['_factor_count'])
            );
        }
    }



}
