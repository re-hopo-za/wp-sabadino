<?php


namespace Sabadino\features\product_slider\templates;


use Sabadino\features\product_slider\Cart;
use Sabadino\features\product_slider\Products;

class Templates
{


    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Templates The single instance of the class.
     */

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public static function templateOne( $products ,$args ){
        $items ='';

        if ( !empty( $products ) ){
            foreach ( $products as $product ){
                $attachment_image = '';
                if( isset( $args['display_image']) && ( $args['display_image'] == 'yes' )   ){
                    $attachment_image ='  
                    <a href="'.Products::getPermalink( $product ).'" >
                     '.Products::getAttachmentImage( $product , $args ).'
                    </a>';
                }
                $discount = '';
                $dis = Products::getDiscountPercentage( $product );
                if ( $dis > 0 ){
                    $discount =
                        '  <span class="product-label">%'.Products::getDiscountPercentage( $product ).' 
                          تخفیف 
                           </span>
                        ';
                }

                $items .= '<div class="item slick-slide" >
                <div class="slide-product">
                    <div class="product-grid-item">
                        <div class="za-product-header-section">
                             <a href="'.Products::getPermalink( $product ).'" > 
                                '. $discount .'
                             </a>
                        </div>
                        <div class="za-product-image-section">
                            <div class="product-image"> 
                                '.$attachment_image.'
                            </div>
                            <div class="za-product-title">
                                <h6>'. $product->get_name().'</h6>
                            </div>
                        </div> 
                        '.Products::getPrice( $product ,$args ).'
                        <div class="za-product-buttons-section">
                            '.\Sabadino\features\cart\Cart::addToCart( $product ).'
                        </div>
                    </div>
                </div>
            </div>';

            }
        }
        return $items;
    }



}