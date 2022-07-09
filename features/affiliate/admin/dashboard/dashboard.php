<?php



function re_affiliate_submenu() {
    add_submenu_page(
        'woocommerce',
        __('Affiliate' , 'woocommerce'),
        __('Affiliate' , 'woocommerce'),
        'manage_options',
        'affiliate-manager',
        're_affiliate_manager'
    );
}

function re_affiliate_manager() {
    ?>
    <div class="wrap re-aff">
        <h3>همکاری در فروش</h3>

        <div>
            <?php
            $aff_calculate = unserialize(  get_option('re_aff_set_calculate'  ,true ));
            ?>
            <div class="aff-menu-items">
                <div class="aff-menu-1">
                    <p>نحوه محاسبه امتیاز</p>
                </div>
                <div class="aff-menu-2">
                    <p>تنظیملات برنامه</p>
                </div>
                <div class="aff-menu-3">
                    <p>تنظیمات ظاهری</p>
                </div>
                <div class="aff-menu-4">
                    <p>گزارش گیری</p>
                </div>
            </div>

            <form class="aff-menu-con"  autocomplete="off">

                <div id="aff-menu-1">
                    <h4>نحوه محاسبه امتیاز</h4>
                    <div class="re-input-con">
                        <div>
                            <label for="re_aff_calc_disable">غیر فعال</label>
                            <input type="radio" name="re_aff_calc_disable" id="re_aff_calc_disable" value="0" <?php checked($aff_calculate['status'] , 'o' , 'checked="checked"') ?>>
                        </div>
                        <div>
                            <label for="re_aff_calc_static">درامد به صورت یکنواخت</label>
                            <input type="radio"   name="re_aff_calc_disable" id="re_aff_calc_static" value="1" <?php checked($aff_calculate['status'] , 'a' , 'checked="checked"') ?>>
                        </div>
                        <div>
                            <label for="re_aff_calc_dynamic">درامد به صورت متغیر </label>
                            <input type="radio"  name="re_aff_calc_disable" id="re_aff_calc_dynamic" value="2" <?php checked($aff_calculate['status'] , 'f' , 'checked="checked"') ?>>
                        </div>
                    </div>


                    <div class="re-window-con">
                        <div class="re-aff-op-con">

                            <div class="re-options-con-0">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                            <g>
                                <g>
                                    <g>
                                        <path d="M256,0C114.615,0,0,114.615,0,256s114.615,256,256,256s256-114.615,256-256S397.385,0,256,0z M256,480
                                            C132.288,480,32,379.712,32,256S132.288,32,256,32s224,100.288,224,224S379.712,480,256,480z"/>
                                        <circle cx="176" cy="176" r="32"/>
                                        <circle cx="336" cy="176" r="32"/>
                                        <path d="M256,240c-79.529,0-144,64.471-144,144h32c0-61.856,50.144-112,112-112s112,50.144,112,112h32
                                            C400,304.471,335.529,240,256,240z"/>
                                    </g>
                                </g>
                            </g>
                            </svg>
                            </div>

                            <div class="re-options-con-1">


                                <div class="re-terms-count-con">
                                    <div>
                                        <label for="re-terms-count">تعیین تعداد دفعات قابل قبول</label>
                                    </div>
                                    <div>
                                        <input type="number" id="re-terms-count"  value="<?php echo $aff_calculate['status']  =='a' ? $aff_calculate['term_count'] : 0 ; ?>"  >
                                        <span>بار</span>

                                    </div>
                                </div>

                                <div class="re-payment-type">
                                    <div>
                                        <label for="re-payment-type">نوع محاسبه پرداختی</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="re-payment-type" id="re-payment-type-1" placeholder="0" value="0"
                                            <?php checked($aff_calculate['term_type'] , 'p' , 'checked="checked"') ?> > <span>درصدی</span>
                                        <input type="radio" name="re-payment-type" id="re-payment-type-2" placeholder="1" value="1"
                                            <?php checked($aff_calculate['term_type'] , 'f' , 'checked="checked"') ?> > <span>ثابت</span>
                                    </div>
                                </div>

                                <div class="re-terms-amount">
                                    <div>
                                        <label for="re-terms-percent">میزان درامد بابت هر بار خرید</label>
                                    </div>
                                    <div>
                                        <input type="number" id="re-terms-percent" value="<?php echo $aff_calculate['status'] == 'a' ? $aff_calculate['term_value'] : 0 ; ?>">
                                        <span class="re-amount"><?php
                                            if ($aff_calculate['status'] == 'a' ) {
                                                if ($aff_calculate['term_type'] =='p'){
                                                    echo 'درصد';
                                                }else{
                                                    echo 'تومان';
                                                }
                                            }else{
                                                echo 'درصد';
                                            }
                                            ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="re-options-con-2">

                                <div  class="re-set-new-term" data-setnewterms="0" data-whitchinput="0"><span class="aff-close">*</span>
                                    <div>
                                        <label for="re-set-input-1">تعیین مقدار به درصد</label>
                                        <input data-active="0"  type="number" id="re-set-input-1"   class="re-set-input"  placeholder="1123" value="10">
                                    </div>
                                    <div>
                                        <label for="re-set-input-2">تعیین مقدار ثابت به تومان</label>
                                        <input data-active="0"   type="number" id="re-set-input-2"   class="re-set-input" placeholder="1231">
                                    </div>
                                    <div>
                                        <a id="re-save-new-term" href="javascript:void(0)"> ذخیره</a>
                                    </div>
                                </div>
                                <div class="re-term-con-variable" data-settingval="">

                                    <div>
                                        <ul>
                                            <?php
                                            if ( isset( $aff_calculate['data'] ) ){
                                                foreach ($aff_calculate['data'] as $af ){
                                                    ?>
                                                    <li data-termnumber="<?php echo $af['termnumber']?>" data-termtype="<?php echo $af['termtype']?>" data-termvalue="<?php echo $af['termvalue']?>" >
                                                        <span>*</span>
                                                        <p>بار<span><?php echo $af['termnumber']?></span> </p>
                                                        <p><?php echo $af['termvalue']?><span><?php echo $af['termtype']=='percent'? 'درصدی':'تومان'; ?></span> </p>
                                                    </li>
                                            <?php } } ?>
                                        </ul>

                                        <div class="add">
                                            <p>+</p>
                                            <p>افزودن</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="re_save_aff_setting">
                        <button id="re_save_aff_setting">
                            ذخیره
                        </button>
                    </div>
                </div>

                <div id="aff-menu-2">
                    <?php
                    $aff_settings = unserialize(  get_option('re_affiliate_settings'  ,  false ));
                    ?>

                    <h4>تنظیمات برنامه</h4>
                    <div class="re_calculate_income">
                        <div>
                           <div class="re_calculate_income_item">
                                <h5>نحوه محاسبه درامد برای سفارشات</h5>
                                <div>
                                    <input type="checkbox"  id="re_exclude_coupon" <?php checked($aff_settings['re_exclude_coupon'] , "true" , 'checked="checked"'); ?> >
                                    <label for="re_exclude_coupon">بجز سفارشات دارای کوپن تخفیف</label>
                                </div>
                            </div>

                            <div class="re_calculate_income_item_last">
                                <h5>زمان محاسبه درامد از فروش</h5>
                                <div>
                                    <input type="radio"  id="re_cal_income_save_order" name="re_when_cal_income" value="0" <?php checked($aff_settings['re_cal_income_save_order'] , 0 , 'checked="checked"'); ?> >
                                    <label for="re_cal_income_save_order">هنگام ثبت سفارش</label>
                                </div>
                                <div>
                                    <input type="radio"  id="re_cal_income_get_order" name="re_when_cal_income" value="1" <?php checked($aff_settings['re_cal_income_save_order'] , 1 , 'checked="checked"'); ?> >
                                    <label for="re_cal_income_get_order">هنگام تکمیل سفارش</label>
                                </div>
                            </div>
                            <div class="re_exclude_user_change_aff">
                                <h5>محدودیت کاربران</h5>
                                <div>
                                    <input type="checkbox"  id="re_exclude_user_change_aff" name="re_exclude_user_change_aff" value="0" <?php checked($aff_settings['re_exclude_user_change_aff'] , "true" , 'checked="checked"'); ?> >
                                    <label for="re_exclude_user_change_aff">بجز کاربرانی که تبدیل به همکار فروش شده اند</label>
                                </div>
                            </div>

                        </div>

                        <div class="re_calculate_income_pro">
                            <div>
                                <h5> محدودیت سبد </h5>
                                <div>
                                    <input type="text"  id="re_minimal_cart" value="<?php  echo $aff_settings['re_minimal_cart'] != '' ? $aff_settings['re_minimal_cart']  : '' ;?>" >
                                    <label for="re_minimal_cart">حداقل مقدار خرید</label>
                                </div>
                                <div>
                                    <input type="text"  id="re_maximal_cart" value="<?php  echo $aff_settings['re_maximal_cart'] != '' ? $aff_settings['re_maximal_cart']  : '' ;?>"   >
                                    <label for="re_maximal_cart">حداکثر مقدار خرید</label>
                                </div>
                            </div>
                        </div>


                        <div>
                            <button  class="re_save_settings">
                                ذخیره
                            </button>
                        </div>
                    </div>
                </div>

                <div id="aff-menu-3">
                    <?php
                    $visual_settings = unserialize(  get_option('re_visual_aff_set_setting'  ,  false ));
                    ?>
                    <h4>تنظیمات ظاهری</h4>

                    <div class="re_visual_con_setting">
                            <h5>شخصی سازی پیشخوان</h5>
                            <div  class="re_visual_users_count">
                                <input type="checkbox"  id="re_visual_users_count" <?php checked( $visual_settings['re_visual_users_count'] , "true" , 'checked="checked"'); ?> >
                                <label for="re_visual_users_count">نمایش تعداد کاربران ثبت نام کرده</label>
                            </div>
                            <div  class="re_visual_purchase_count">
                                <input type="checkbox"  id="re_visual_purchase_count" <?php checked( $visual_settings['re_visual_purchase_count'] , "true" , 'checked="checked"'); ?> >
                                <label for="re_visual_purchase_count">نمایش تعداد خریدهای انجام شده</label>
                            </div>
                            <div  class="re_visual_chart">
                                <input type="checkbox"  id="re_visual_chart" <?php checked( $visual_settings['re_visual_chart'] , "true" , 'checked="checked"'); ?> >
                                <label for="re_visual_chart"> نمایش آمار بازدید کنندگان</label>
                            </div>
                            <div  class="re_visual_users_profile">
                                <input type="checkbox"  id="re_visual_users_profile" <?php checked( $visual_settings['re_visual_users_profile'] , "true" , 'checked="checked"'); ?> >
                                <label for="re_visual_users_profile">نمایش مشخصات پروفایل</label>
                            </div>

                    </div>

                    <div class="re_visual_con_setting_btn">
                        <button  class="re_save_visual_settings">
                            ذخیره
                        </button>
                    </div>

                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r120/three.min.js"></script>
                <div id="aff-menu-4">
<!--                    <div id="tree-container">-->
<!---->
<!--                    </div>-->
                    <div id="tree-container"></div>
                </div>

            </form>



        </div>
    </div>




    <?php

}
add_action('admin_menu', 're_affiliate_submenu',99);






