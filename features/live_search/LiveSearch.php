<?php

namespace Sabadino\features\live_search;

class LiveSearch
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
        if ( !is_admin() ){
            add_shortcode('live-search', [ $this , 'shortcode']);


        }
        add_filter( 'wp_ajax_nopriv_sabadino_search_live', [ $this ,'searchResult'] );
        add_filter( 'wp_ajax_sabadino_search_live'       , [ $this ,'searchResult'] );
    }


    public function shortcode()
    {
        ob_start();
    ?>

        <div class="za-live-search-con" >
            <div class="za-live-search-main" >
                <input type="text" class="za-live-search" id="za-live-search" name="za-live-search" placeholder="جستجوی محصولات">
                <div>
                    <svg  xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="25" height="25"
                         viewBox="0 0 512 512"  xml:space="preserve">
                        <g>
                            <g>
                                <path d="M225.474,0C101.151,0,0,101.151,0,225.474c0,124.33,101.151,225.474,225.474,225.474
                                    c124.33,0,225.474-101.144,225.474-225.474C450.948,101.151,349.804,0,225.474,0z M225.474,409.323
                                    c-101.373,0-183.848-82.475-183.848-183.848S124.101,41.626,225.474,41.626s183.848,82.475,183.848,183.848
                                    S326.847,409.323,225.474,409.323z"/>
                            </g>
                        </g>
                            <g>
                                <g>
                                    <path d="M505.902,476.472L386.574,357.144c-8.131-8.131-21.299-8.131-29.43,0c-8.131,8.124-8.131,21.306,0,29.43l119.328,119.328
                                c4.065,4.065,9.387,6.098,14.715,6.098c5.321,0,10.649-2.033,14.715-6.098C514.033,497.778,514.033,484.596,505.902,476.472z"/>
                                </g>
                            </g>
                     </svg>
                </div>
            </div>
            <div class="za-live-search-result">
                <ul>

                </ul>
            </div>
            <div class="close-search-con">
            </div>
        </div>
    <?php
        $ob_str = ob_get_contents();
        ob_end_clean();
        echo $ob_str;
    }


    public function searchResult()
    {
        if ( isset(  $_POST['keyword']  ) ){

            global $wpdb;
            $keyword   = sanitize_text_field(  $_POST['keyword'] );
            $re_query  = $wpdb->prepare(
                    "SELECT * FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' AND (post_title LIKE '%$keyword%' or ID=%d) ; ",$keyword
            );
            $products  = $wpdb->get_results( $re_query,OBJECT );

            foreach ( $products as $product ){
                 $this->resultUI( $product );
            }
            exit();
        }
    }

    public function resultUI( $item )
    {
        $image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ) , 'single-post-thumbnail'  )[0];
        $product   = wc_get_product( $item->ID  );
        ?>
        <li class="autocomplete-suggestion" data-index="0">
            <a href="<?php echo get_permalink( $product->get_id() ); ?>">
                <div class="right">
                    <div class="suggestion-thumb">
                        <img width="60" height="60" src="<?php echo $image_src; ?>"
                             class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="<?php echo $item->post_title; ?>" >
                    </div>
                </div>
                <div class="left">
                    <h4 class="suggestion-title result-title">
                        <strong><?php echo $item->post_title; ?></strong>
                    </h4>
                    <div class="suggestion-price price">
                        <div class="product_price">

                            <?php if ( $product->get_regular_price() > 0 ) : ?>
                                <div class="other_price">
                                    <div class="other_price_text">
                                        <p>قیمت قصابی</p>
                                    </div>
                                    <div class="other_price_amount">
                                        <p>
                                            <del><?php echo $product->get_regular_price(); ?></del>
                                        </p>
                                        <span>تومان</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ( $product->get_sale_price() > 0 ) : ?>
                                <div class="our_price">
                                    <div class="our_price_text">
                                        <p> قیمت سبدینو </p>
                                    </div>
                                    <div class="our_price_amount">
                                        <p class=""><?php echo $product->get_sale_price(); ?></p>
                                        <span>تومان</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </li>

        <?php

    }







}