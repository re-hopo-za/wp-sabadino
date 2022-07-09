<?php

namespace Sabadino\features\mobile_app;

class Dashboard
{


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
        add_action('admin_menu', [$this ,'re_mobile_app_submenu_page'],99);
        add_action('admin_enqueue_scripts' , [$this , 'enqueues' ]);
    }


    public function re_mobile_app_submenu_page() {
        add_submenu_page(
            'woocommerce',
            __('Mobile App' , 'woocommerce'),
            __('Mobile App' , 'woocommerce'),
            'manage_options',
            'mobile_app' ,
            [$this ,'re_mobile_app_function']
        );
    }

    public function re_mobile_app_function() {
        ?>
        <h1>تنظیمات برنامه</h1>
        <div class="re_mobile_con">
            <div class="re_sidebar_con">
                <ul>
                    <li class="re_mobile_menus_active" id="0">  تنظیمات اسلایدر </li>
                    <li id="1">  تنظیمات بنر </li>
                    <li id="2">  تنظیمات برنامه </li>
                </ul>
            </div>
            <div class="re_body">
                <div>



                    <div class="re_items_0 re_mobile_body_active">
                        <h2>تنظیمات اسلایدر </h2>
                        <ul>
                            <?php
                            $sliders = get_option( 're_and_app_slider' , true );
                            if ( !empty( $sliders ) && is_array( $sliders ) ) {
                                foreach ( $sliders as $item ){
                                    ?>
                                    <li data-href="<?php echo $item['slider']; ?>" >
                                        <div class="re_slider_img_con">
                                            <img src="<?php echo $item['slider']; ?>" alt="">
                                        </div>
                                        <div class="re_slider_remove_con">
                                            <button> حذف </button>
                                        </div>
                                    </li>
                                <?php }  } ?>
                        </ul>

                        <div class="re_inputs_slider_con">
                            <div>
                                <div>
                                    <input type="text" placeholder="لینک تصویر را وارد کنید">
                                    <button class="re_upload_slider_image">
                                        <a href="#" class="misha-upl">آپلود تصویر</a>
                                        <input type="hidden" name="misha-img" value="">
                                    </button>
                                </div>
                            </div>
                            <div class="add_slider_con" >
                                <button>
                                    افزودن
                                </button>
                            </div>
                        </div>

                    </div>



                    <div class="re_items_1">
                        <h2>تنظیمات بنر</h2>
                        <ul>
                            <?php
                            $baners = get_option( 're_and_app_banner' , true );
                            if ( !empty( $baners ) && is_array( $baners ) ) {
                                foreach ( $baners as $item ){
                                    ?>
                                    <li data-href="<?php echo $item['banner']; ?>">
                                        <div class="re_banner_img_con">
                                            <img src="<?php echo $item['banner']; ?>" alt="">
                                        </div>
                                        <div class="re_banner_remove_con">
                                            <button> حذف </button>
                                        </div>
                                    </li>
                                <?php } } ?>
                        </ul>

                        <div class="re_inputs_banner_con">
                            <div>
                                <div>
                                    <input type="text" placeholder="لینک تصویر را وارد کنید">
                                    <button class="re_upload_banner_image">
                                        <a href="#" class="misha-upl-banner">آپلود تصویر</a>
                                        <input type="hidden" name="misha-img-banner" value="">
                                    </button>
                                </div>
                            </div>
                            <div class="add_banner_con" >
                                <button>
                                    افزودن
                                </button>
                            </div>
                        </div>

                    </div>



                    <div class="re_items_2">
                        <h2>تنظیمات برنامه</h2>
                        <form  autocomplete="off">
                            <ul>
                                <li class="app_discount_section">
                                    <label for="discount_timer_input">انتخاب زمان</label>
                                    <div class="date">
                                        <input type="text" value="<?php
                                        $app_options = get_option( 're_set_discount_time' , true );
                                        if ( !empty( $app_options ) ){
                                            echo explode('|' ,$app_options   )[0];
                                        }else{
                                            echo '';
                                        } ?>" id="discount_timer_input_date">
                                    </div>
                                    <div>
                                        <input type="text" value="<?php
                                        if ( !empty( $app_options ) ){
                                            echo explode('|' ,$app_options   )[1];
                                        }else{
                                            echo '';
                                        }  ?>"  id="discount_timer_time">
                                    </div>
                                    <div>
                                        <button>
                                            ذخیره
                                        </button>
                                    </div>
                                </li>

                                <li class="minimum_purchase_amount">
                                    <label for="minimum_purchase_amount">حداقل سبد</label>
                                    <div class="date">
                                        <input type="text" value="<?php echo get_option( 're_minimum_purchase_amount' , true  );  ?>" id="minimum_purchase_amount">
                                    </div>
                                    <div>
                                        <button>
                                            ذخیره
                                        </button>
                                    </div>
                                </li>


                                <li class="display_product_count">
                                    <label for="display_product_count">تعداد محصولات قابل نمایش</label>
                                    <div class="date">
                                        <input type="text" value="<?php echo get_option( 're_display_product_count' , true );  ?>" id="display_product_count">
                                    </div>
                                    <div>
                                        <button>
                                            ذخیره
                                        </button>
                                    </div>
                                </li>

                            </ul>
                        </form>

                    </div>

                </div>
            </div>

        </div>
        <?php
    }


    public function enqueues()
    {
        wp_enqueue_script('re_admin_page_js', RE_MOBILE_ASSETS . '/admin/mobile_admin.js', array('jquery'), time());
        wp_localize_script('re_admin_page_js', 're_back_app_objects', array(
            'admin_url' => admin_url('admin-ajax.php'),
        ));
        wp_enqueue_style('re_admin_page_css', RE_MOBILE_ASSETS . '/admin/mobile_admin.css', null, time());
    }



}






