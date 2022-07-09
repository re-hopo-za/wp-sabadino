<?php


namespace Sabadino\features\product_slider;


class Products
{


    public static $Product_cats  = [];
    public static $Product_tags  = [];

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Products The single instance of the class.
     */

    protected static  $_instance = null;
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    public function __construct()
    {
    }

    public static function products( $args )
    {

        $count    = 10;
        $products = [];
        if (isset( $args['limit'] ) && $args['limit'] > 0  ){
            $count  = $args['posts_per_page'];
        }

        if ( isset( $args['product_kinds'] ) && $args['product_kinds'] != '' ){

            $kind = $args['product_kinds'];
            if ( $kind == 'sabadino_offers' ){
                $products = wc_get_products(
                    [
                        'tag'    => [ 'offer-products'  ],
                        'limit'  =>  $count ,
                        'status' =>  'publish'
                    ]
                );
            }else if ( $kind == 'more_sales' ){
                $products = wc_get_products(
                    [
                        'orderby'   => 'meta_value_num',
                        'meta_key'  => 'total_sales',
                        'order'     => 'DESC',
                        'limit'     => $count ,
                        'status'    => 'publish'
                    ]
                );
            }else if ( $kind == 'more_discount' ){
                $products = wc_get_products(
                    [
                        'limit'     => $count ,
                        'order'     => 'DESC',
                        'orderby'   => 'meta_value_num',
                        'meta_key'  => '_sale_price',
                        'status'    => 'publish'
                    ]
                );
            }else if ( $kind == 'new_products' ){
                $products = wc_get_products(
                    [
                        'order'   => 'DESC',
                        'orderby' => 'ID' ,
                        'limit'   => $count ,
                        'status'  => 'publish'
                    ]
                );
            }else if ( $kind == 'more_rates' ){
                $products = wc_get_products(
                    [
                        'limit'     => $count ,
                        'order'     => 'DESC',
                        'orderby'   => 'meta_value_num',
                        'meta_key'  => '_wc_average_rating',
                        'status'    => 'publish'
                    ]
                );
            }else if ( $kind == 'related_products' ){
                global $post;

                $terms = get_the_terms( $post->ID, 'product_cat' )[0];

                $products = wc_get_products(
                    [
                        'category' => [$terms->slug],
                        'orderby'  => 'ID' ,
                        'limit'    => $count ,
                        'status'   => 'publish'
                    ]
                );
            }


        }elseif ( isset( $args['product_cats'] ) && $args['product_cats'] != '' ){

            $products = wc_get_products(
                [
                    'category' => $args['product_cats'],
                    'orderby' => 'ID' ,
                    'limit'   => $count ,
                    'status'  => 'publish'
                ]
            );

        }elseif ( isset( $args['product_tags'] ) && $args['product_tags'] != '' ){
            $products = wc_get_products(
                [
                    'tag'     => $args['product_tags'],
                    'orderby' => 'ID' ,
                    'limit'   => $count ,
                    'status'  => 'publish'
                ]
            );
        }
        return $products;
    }


    public static function getProductsCats()
    {
        $final_cats = [];
        $cats = get_terms(
            [ 'taxonomy'     => 'product_cat' ,
                'hide_empty' => 0 ,
                'orderby'    => 'ASC',
                'parent'     => 0
            ]);
        if ( !empty( $cats ) ){
            foreach ( $cats as $cat ){
                $final_cats[$cat->slug] = $cat->name;
            }
        }
        return $final_cats;

    }

    public static function getProductsTags()
    {
        $final_tag = [];
        $tags = get_terms(
            [ 'taxonomy'     => 'product_tag' ,
                'hide_empty' => 0 ,
                'orderby'    => 'ASC',
                'parent'     => 0
            ] );
        if ( !empty( $cats ) ){
            foreach ( $tags as $tag ){
                $final_tag[$tag->term_id] = $tag->name;
            }
        }
        return $final_tag;
    }

    public static function allProducts()
    {
        $return = [];
        $args =  array(
            'count'          =>  -1       ,
            'post_type'      => 'product' ,
            'posts_per_page' => 1000
        );
        $products = get_posts( $args );

        foreach ( $products  as $product ){
            $return[$product->ID] = $product->post_title ;
        }
         return $return;
    }

    public static function getProductStatus()
    {
        return get_post_statuses();
    }

    public static function getProductRating( $product ,$settings )
    {
        $display_rating = isset($settings['display_rating']) && $settings['display_rating'] ? $settings['display_rating'] : 'no';
        if( $display_rating == 'yes' ){
            $return         = '<div class="za-rating">';
            if ( 'no' !== get_option( 'woocommerce_enable_review_rating' ) ) {
                $rating_count = $product->get_rating_count();
                $review_count = $product->get_review_count();
                $average      = $product->get_average_rating();
                $product_id   = $product->get_id();
                $return      .= '<div class="za-rating-icons">';
                if ( 0 == $average ) {
                    $html    = '<div class="star-rating">';
                    $html   .= wc_get_star_rating_html( $average, $rating_count );
                    $html   .= '</div>';
                    $return .= $html;
                }else{
                    $return .= wc_get_rating_html( $average, $rating_count );
                }
                $return .= '</div>';
            }
            $return .= '</div>';
            return   $return;
        }
        return '';
    }

    public static function getProductCats()
    {
        $product_categories_list = array();
        $args = array(
            'taxonomy'   => 'product_cat',
        );
        $args = apply_filters( 'wpce_get_product_cat_args', $args );
        $product_categories = get_terms($args);

        if( !empty($product_categories) ){
            foreach ($product_categories as $cat) {
                $product_categories_list[$cat->slug] = $cat->name;
            }
        }
        return $product_categories_list;
    }

    public static function getProductList()
    {
        $product_lists = array();

        $args = array(
            'numberposts'=> -1,
            'return' => 'ids',
        );
        $products_lists = wc_get_products($args);
        if( is_array($products_lists) && !empty($products_lists) ){
            foreach( $products_lists as $index=>$id ){
                $product_lists[$id] = get_the_title($id);
            }
        }
        return $product_lists;
    }



    public static function getProductsKind()
    {
        return [
            'related_products' => 'محصولات مرتبط' ,
            'new_products'     => 'جدید' ,
            'more_sales'       => 'پرفروش' ,
            'more_discount'    => 'تخفیفدار ترین' ,
            'sabadino_offers'  => 'پیشنهاد سبدینو' ,
            'more_rates'       => 'بیشترین امتیاز' ,
        ];
    }

    public static function getAttachmentAlt( $attachment_id )
    {
        if ( ! $attachment_id ) {
            return '';
        }

        $attachment = get_post( $attachment_id );
        if ( ! $attachment ) {
            return '';
        }

        $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
        if ( ! $alt ) {
            $alt = $attachment->post_excerpt;
            if ( ! $alt ) {
                $alt = $attachment->post_title;
            }
        }
        return trim( strip_tags( $alt ) );
    }

    public static function getDiscountPercentage( $product )
    {
        $regular_price = 0;
        $sale_price    = 0;

        if ( $product->get_type() == 'simple'){
            $regular_price = (float) $product->get_regular_price();
            $sale_price = (float) $product->get_price();
        }elseif( $product->get_type() == 'variable' ){
            $whole_price = self::getVariation( (object) $product ,true );
            $regular_price = isset( $whole_price['regular_price'] ) ? (float) $whole_price['regular_price'] : 0 ;
            $sale_price    = isset( $whole_price['sale_price'] ) ? (float) $whole_price['sale_price'] : 0 ;

        }
        if ( $sale_price > 0 ){
            return round( 100 - ( $sale_price / $regular_price * 100 )  ) ;
        }
        return 0;
    }

    public static function getProductImage( $product )
    {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
        return $image[0];
    }

    public static function getPermalink( $product )
    {
        return get_permalink($product->get_id());
    }

    public static function getAttachmentImage( $product ,$args )
    {
        $thumbnail_id = $product->get_image_id();
        if( $thumbnail_id ){
            return sprintf( '<img src="%s" title="%s" alt="%s"%s />',
                esc_attr( wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), [600,600] )[0] ,
                    get_the_title( $thumbnail_id )  ),
                get_the_title( $thumbnail_id ), self::getAttachmentAlt( $product ) , '' );
        }
        return '';
    }

    public static function getPrice( $product ,$productType )
    {
        $other_name = 'قیمت قصابی';
        if ( empty( $productType['product_kinds'] ) && !empty( $productType['product_cats'] ) ){
            $other_name = 'قیمت فروشگاه';
        }

        if( $product->is_on_sale()) {
            $price =
               '<div class="product_price">
                  <div  class="other_price">
                      <div class="other_price_text">
                         <p>'.$other_name.'</p>
                      </div> 
                      <div class="other_price_amount">
                        <p><del>'. self::getPrices( $product ) .'</span> 
                      </div> 
                  </div>
                  <div  class="our_price"> 
                      <div class="our_price_text">
                          <p> قیمت سبدینو </p> 
                      </div>
                      <div class="our_price_amount">
                          <p class="">'. self::getPrices( $product , false) .'</span> 
                      </div>
                  </div>
               </div>';
        }else{
            $price = '  
                <div class="our_price_normal"> 
                      <div class="our_price_normal_text">
                          <p> قیمت</p> 
                      </div>
                      <div class="our_price_normal_amount">
                          <p class=""> '. self::getPrices( $product  ) .' </span>  
                      </div>
                </div> ';
        }
        return $price;
    }

    public static function getVariation( $product , $first = false )
    {
        if( empty( $product ) ) return [];

        if ( $first ){
            $variations = $product->get_available_variations();
            if ( isset( $variations[0] ) ){
                $variation_data = $variations[0] ;
                return [
                    'sale_price'    => $variation_data['display_price'] ,
                    'regular_price' => $variation_data['display_regular_price'] ,
                    'description'   => $variation_data['variation_description'] ,
                    'in_stock'      => $variation_data['is_in_stock']
                ];
            }else{
//                return [
//                    'sale_price'    => $product-> ,
//                    'regular_price' => $variation_data['display_regular_price'] ,
//                    'description'   => $variation_data['variation_description'] ,
//                    'in_stock'      => $variation_data['is_in_stock']
//                ];
            }


        }
        $return = [];
        $variations_data = $product->get_available_variations();
        if( !empty( $variations_data ) && is_array( $variations_data ) ) {
            foreach ( $variations_data as $variation ){
                $return[]= [
                    'sale_price'    => $variation['display_price'] ,
                    'regular_price' => $variation['display_regular_price'] ,
                    'description'   => $variation['variation_description'] ,
                    'in_stock'      => $variation['is_in_stock']
                ];
            }
        }
        return $return;
    }

    public static function getPrices( $product , $regular = true   )
    {
        $regular_price = $sales_price = 0;
        $currency_symbol = get_woocommerce_currency_symbol();
        if ( $product->get_type() == 'simple' && !empty( $product->get_regular_price() ) ){
            if ( !empty( $product->get_sale_price() ) ){
                $sales_price   =  number_format( $product->get_sale_price()).'</p><span>'.$currency_symbol;
            }
            $regular_price =  number_format( $product->get_regular_price()).'</p><span>'.$currency_symbol;
        }elseif( $product->get_type() == 'variable' ){
            $whole_price = self::getVariation( (object) $product ,true );
            if ( isset( $whole_price['sale_price'] ) && $whole_price['sale_price'] > 0 ){
                $sales_price   =  number_format( self::getVariation( (object) $product ,true )['sale_price'] ).'</p><span>'.$currency_symbol;
            }
            $regular_price =  number_format( isset( $whole_price['regular_price'] ) ? $whole_price['regular_price'] : 0 ) .'</p><span>'.$currency_symbol;
        }
        return $regular ? $regular_price : $sales_price;
    }


    public static function getStock( $product )
    {
        $product_stock = 0;
        if ( $product->get_manage_stock() == true ){
            $product_stock = $product->get_stock_quantity();
        }elseif ( $product->get_manage_stock() == false and $product->get_stock_status() =='instock' ){
            $product_stock = 99999;
        }elseif($product->get_manage_stock()== false and $product->get_stock_status() =='outofstock') {
            $product_stock = 0 ;
        }
        return $product_stock;
    }


}



