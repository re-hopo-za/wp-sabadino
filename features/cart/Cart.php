<?php

namespace Sabadino\features\cart;

use http\Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Sabadino\features\product_slider\Products;
use Sabadino\includes\Functions;

class Cart
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
        self::defines();
        add_action('wp_enqueue_scripts' ,[ $this ,'frontScripts' ] ,99 );

        add_filter( 'wp_ajax_nopriv_mode_theme_update_mini_cart' ,[ $this ,'updateMiniCart'] );
        add_filter( 'wp_ajax_mode_theme_update_mini_cart'        ,[ $this ,'updateMiniCart'] );
        add_filter( 'wp_ajax_re_cart_add'        ,[ $this ,'addToCartProcess'] );
        add_filter( 'wp_ajax_nopriv_re_cart_add' ,[ $this ,'addToCartProcess'] );
        add_filter( 'wp_ajax_re_refresh_cart_icon' ,[ $this ,'refreshCart'] );
        add_filter( 'wp_ajax_nopriv_re_refresh_cart_icon' ,[ $this ,'refreshCart'] );

        add_action( 'wp_footer', [ $this ,'variationSection'] );
    }

    public static function addToCartProcess()
    {
        $product_id = Functions::indexChecker( $_GET , 'product_id' ,0 );
        $quantity   = Functions::indexChecker( $_GET , 'quantity' ,0 );
        $cart       = WC()->instance()->cart;
        $product    = wc_get_product( $product_id );
        if( $product ){
            $parent_id = $product->get_parent_id();
            if ( $parent_id > 0 ){
                foreach ( WC()->cart->get_cart() as $item_key => $item ) {
                    if ( $item['variation_id'] == $product_id ) {
                        WC()->cart->remove_cart_item( $item_key );
                        break;
                    }
                }
                try {
                    WC()->cart->add_to_cart($parent_id, $quantity, $product_id);
                } catch (\Exception $e) {
                    wp_send_json([
                        'total'    => number_format( WC()->cart->get_cart_contents_total() ) ,
                        'count'    => WC()->cart->get_cart_contents_count() ,
                    ] ,500 );
                }
            }else{
                $cart_id = $cart->generate_cart_id($product_id);
                $cart_item_id = $cart->find_product_in_cart($cart_id);

                if($cart_item_id){
                    $cart->set_quantity($cart_item_id, $quantity );
                }else{
                    try {
                        $cart->add_to_cart($product_id , $quantity );
                    }catch (\Exception $e ){
                        wp_send_json([
                            'total'    => number_format( WC()->cart->get_cart_contents_total() ) ,
                            'count'    => WC()->cart->get_cart_contents_count() ,
                        ] ,500 );
                    }
                }
            }
        }
        wp_send_json([
            'total'    => number_format( WC()->cart->get_cart_contents_total() ) ,
            'count'    => WC()->cart->get_cart_contents_count() ,
        ] ,200);



    }



    public static function updateMiniCart()
    {
        echo wc_get_template( 'cart/mini-cart.php' );
        exit();
    }

    public static function refreshCart()
    {
        global $woocommerce;
        wp_send_json( array(
            'total'    => number_format( WC()->cart->get_cart_contents_total() ) ,
            'count'    => WC()->cart->get_cart_contents_count() ,
        ) );
    }



    public static function defines()
    {
        define("RE_CART_ASSETS", plugin_dir_url(__FILE__) . "assets/");
    }

    public static function addToCart( $product )
    {
        if (  $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
            return self::addToCartUI( $product ,self::getCartCount( $product ) );
        }
        return self::outOfStockUI();
    }


    public static function getCartCount( $product )
    {
        $count = 0; 
        if ( $product->is_type( 'variable' ) ){
            if ( !empty( $product->get_children() ) ){
                foreach ( $product->get_children() as $child ) {
                    if (isset( WC()->cart->get_cart_item_quantities()[$child]  )){
                        $count += (WC()->cart->get_cart_item_quantities())[$child];
                    }
                }
            }   
        }else{
            if ( array_key_exists( $product->get_id(), WC()->cart->get_cart_item_quantities() ) ) {
                $count = (WC()->cart->get_cart_item_quantities())[$product->get_id()]; 
            }
        }
        return $count;
    }


    public static function getTypeClass( $product )
    {
        return $product->is_type( 'simple' ) ? 'data-product-type="simple-product"' : 'data-product-type="variable-product"';
    }

    public static function getProductID( $product )
    {
        return 'data-product-id="'.$product->get_id().'"';
    }

    public static function getFactorCount( $product )
    {
        $factor = $product->get_meta('_factor_count' , true );
        return  $factor == null ? 1 : $factor ;
    }
    public static function getFactorCountUI( $product )
    {
        return 'data-factor="'.self::getFactorCount( $product ).'"';
    }

    public static function getStock( $product )
    {
        $product_stock = 0;
        if ( $product->get_manage_stock() == true ){
            $product_stock = $product->get_stock_quantity();
        }elseif ( $product->get_manage_stock() == false and $product->get_stock_status() =='instock' ){
            $product_stock = 99999;
        }
        return $product_stock;
    }

    public static function getStockUI( $product ){
        return 'data-stock="'.self::getStock( $product ).'"';
    }

    public static function getCountUI( $count ){
        return 'data-count="'.$count.'"';
    }



    public static function addToCartUI( $product ,$count )
    {
        $final_html ='<div class="cart-con p-id-'.$product->get_id().'" '.
            self::getTypeClass( $product ).' '.
            self::getProductID( $product ).' '.
            self::getFactorCountUI( $product ).' '.
            self::getStockUI( $product ).' '.
            self::getCountUI( $count ).' >';


        if ( $count > 0 ){
            $mines_icon = $count == 1 ? 'trash' : 'minus';
            $final_html .=
                '<div class="added-cart" >
                    <div class="cart-plus-con" >
                        <img src="'.RE_CART_ASSETS.'plus.png" alt="plus icon">
                    </div>
                    <div class="cart-count-con" >
                        <span class="cart-count"> '. $count .'</span>
                    </div>
                    <div class="cart-mines-con" >
                        <img src="'.RE_CART_ASSETS.$mines_icon.'.png" alt="'.$mines_icon.' icon">
                    </div>
                </div>';
        }else{
            $final_html .=
                '<div class="normal-cart" >
                    <div class="cart-plus-con" >
                        <img src="'.RE_CART_ASSETS.'plus-white.png" alt="plus white icon">
                    </div>
                    <div class="cart-count-con" >
                        <span class="cart-count">اضافه کردن به سبد</span>
                    </div>
                    <div class="cart-mines-con" >
                        <img src="'.RE_CART_ASSETS.'trash.png" alt="trash icon">
                    </div>
                </div>';
        }
        return $final_html .'</div>';
    }

    public static function outOfStockUI()
    {
        return
            '<div class="re-outOfStock">
                <div>
                    <p>ناموجود</p>
                </div>
            </div>';
    }


    public static function getAllVariationsProduct()
    {
        $variables = [];
        $args = [
            'type' => 'variable'
        ];
        $products = wc_get_products( $args );
        if ( !empty( $products ) ){
            foreach ( $products as $product ){
                $variables[$product->get_id()] = self::getVariationList( $product );
            }
        }
        return $variables;
    }


    public static function getVariationList( $product )
    {
        $list       = [];
        $translate  = [];
        $parent_tax = [];
        $total_quantity = 0;
        $var_data   = [
            'image'     => wp_get_attachment_url( $product->get_image_id() ) ,
            'name'      => $product->get_title() ,
            'factor'    => self::getFactorCount( $product ) ,
            'stock'     => self::getStock( $product ) ,
            'list'      => [] ,
            'translate' => $translate
        ];

        foreach ( wc_get_attribute_taxonomies() as $parent ){
            $parent_tax[ 'pa_'.$parent->attribute_name ] = $parent->attribute_label ;
        }
        if ( $product->is_type( 'variable' ) ){
            $variations = $product->get_available_variations();
            foreach ( $variations as $variation ){
                foreach ( $product->get_variation_attributes() as $a_v_key => $a_v_val ) {
                    foreach ( $a_v_val  as $item ) {
                        $translate[$a_v_key][ 'items' ][$item] = get_term_by('slug', $item , $a_v_key  )->name ;
                        $translate[$a_v_key][ 'name' ] = $parent_tax[$a_v_key] ;
                    }
                }
                $quantity = 0 ;
                foreach ( WC()->cart->get_cart() as $cart_item ) {
                    if( in_array( $variation['variation_id'] , [ $cart_item['product_id'], $cart_item['variation_id'] ] ) ){
                        $quantity = $cart_item['quantity'];
                        $total_quantity += $quantity;
                    }
                }
                $list[$variation['variation_id']] = [
                    'attributes'     => array_values( $variation['attributes'] ) ,
                    'regular_price'  => $variation['display_regular_price'] ,
                    'sale_price'     => $variation['display_price'] ,
                    'is_in_stock'    => $variation['is_in_stock']  ,
                    'is_purchasable' => $variation['is_purchasable']  ,
                    'description'    => $variation['variation_description'],
                    'added_cart'     => self::cartItems( $variation['variation_id'] )  ,
                    'quantity_cart'  => $quantity ,
                    'variation_id'   => $variation['variation_id'] ,
                ];
            }
            $var_data['list']      = $list;
            $var_data['translate'] = $translate;
            $var_data['quantity']  = $total_quantity;

        }
        return $var_data;

    }


    public static function cartItems( $productID )
    {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        if ( !empty( $items ) ) {
            foreach ( $items as $cart_item ) {
                if( $productID ==  $cart_item['variation_id'] ){
                    return $cart_item['quantity'];
                }
            }
        }
        return 0;
    }


    public function frontScripts()
    {
        wp_enqueue_script(
            'custom_cart_handler_js' ,
            RE_CART_ASSETS.'custom-cart-handler.js',
            [],
            ZA_SABADINO_SCRIPTS_VERSION ,
            false
        );

        wp_localize_script(
            'custom_cart_handler_js' ,
            'sabadino_object',
            [
                'ajax_url'       => admin_url( 'admin-ajax.php' ) ,
                'home_url'       => home_url()  ,
                'sabadino_nonce' => wp_create_nonce('za-main-nonce'),
                'variables_list' => self::getAllVariationsProduct()
            ]
        );

        wp_enqueue_style(
            'custom_cart_handler_css' ,
            RE_CART_ASSETS.'custom-cart-handler.css',
            [],
            ZA_SABADINO_SCRIPTS_VERSION
        );
    }


    public function variationSection()
    {
        echo
        '<div class="za-variation-sidebar">
            <div class="closer">
                <svg  x="0px" y="0px" id="za-variation-closer"  viewBox="0 0 512.001 512.001" width="25px" height="25px">
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
                <h3>انتخاب متغیر</h3>
            </div>
            <div class="za-variation-root">
                <div class="za-top-section">
                    <div class="za-variation-image">
                        <img src="" alt="produc-image">
                    </div>
                    <div class="za-variation-title">
                        <h5>  </h5>
                    </div>
                    <div class="za-variation-select-box" data-parent-id="">

                    </div>
                </div> 
                <div class="za-variation-price">
                      <div class="za-variation-other-price">
                          <div class="za-variation-other-text">
                             <p>قیمت فروشگاه</p>
                          </div> 
                          <div class="za-variation-other-amount">
                            <b><del> </del></b><span>تومان</span> 
                          </div> 
                      </div>
                      
                      <div class="za-variation-our-price">
                          <div class="za-variation-our-text">
                             <p>قیمت سبدینو</p>
                          </div> 
                          <div class="za-variation-our-amount">
                            <b> </b><span>تومان</span> 
                          </div> 
                       </div>  
                 </div> 
                <div class="za-variation-cart-btn za-product-buttons-section cart-con "  >
                     
                </div> 
            </div>
        </div>';
    }
}