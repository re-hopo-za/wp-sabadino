<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */
    
    use Sabadino\features\product_slider\Products;
    
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    global $product;
    global $post;
    $other_name = 'قیمت قصابی';
    $terms = get_the_terms( $post->ID, 'product_cat' );
    foreach ( $terms as $term ) {
        if ( $term->slug == 'fruits-vegetables' ){
            $other_name = 'قیمت فروشگاه';
        } 
    }
    
    if( $product->is_on_sale()) {
        echo
            '<div class="product_price">
                      <div  class="other_price">
                          <div class="other_price_text">
                             <p> '.$other_name.' </p>
                          </div>
                          
                          <div class="other_price_amount">
                            <p ><del>'. Products::getPrices( $product ) .'</span> 
                          </div> 
                      </div> 
                      <div  class="our_price"> 
                          <div class="our_price_text">
                              <p> قیمت سبدینو </p> 
                          </div>
                          <div class="our_price_amount">
                              <p class="">'. Products::getPrices( $product , false) .'</span> 
                          </div>
                      </div>
                   </div>';
    }else{
        echo '  
                    <div class="our_price_normal"> 
                          <div class="our_price_normal_text">
                              <p> قیمت</p> 
                          </div>
                          <div class="our_price_normal_amount">
                              <p class=""> '. Products::getPrices( $product  ) .' </span>  
                          </div>
                    </div> ';
    }
    




