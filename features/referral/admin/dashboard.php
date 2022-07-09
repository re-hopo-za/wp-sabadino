<?php






function register_re_referral_submenu_page() {
    add_submenu_page(
        'woocommerce',
        __('Referral' , 'woocommerce'),
        __('Referral' , 'woocommerce'),
        'manage_options',
        'referral' ,
        're_referral_function'
    );
}

add_action('admin_menu', 'register_re_referral_submenu_page',99);

function re_referral_function() {


    ?>

    <div class="tabs">
        <ul class="tabs-list">
            <li class="active"><a href="#tab1">تنظیمات </a></li>
            <li ><a href="#tab2">لیست کاربران </a></li>
            <li ><a href="#tab3">نحوه محاسبه امتیار</a></li>
            <li ><a href="#tab4">نحوه محاسبه جایزه</a></li>
        </ul>




        <div id="tab1" class="tab active">
            <h3>تنظیمات</h3>
            <form  autocomplete="off">
                <div class="re_referral_status">
                    <label for="re_referral_status">تغییر وضعیت</label>
                    فعال
                    <input type="radio" name="re_referral_status" id="re_referral_status" value="1"
                    <?php checked( get_option('re_change_referral_status' , true ), '1' , 'checked') ?> >
                    غیر فعال
                    <input type="radio" name="re_referral_status" id="re_referral_status" value="0"
                        <?php checked( get_option('re_change_referral_status' , true ), '0' , 'checked') ?> >
                </div>
                <div class="re_referral_setting">
                    <button>ذخیره</button>
                </div>
            </form>
        </div>



        <div id="tab2" class="tab">
            <h3>لیست موجودی کاربران</h3>

            <table id="datatable" class=" dataTable uk-table uk-table-hover uk-table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>شناسه</th>
                    <th>تلفن</th>
                    <th>نام</th>
                    <th>مانده از خرید</th>
                    <th>مقدار کد معرف</th>
                    <th>تعیین مقدار</th>
                    <th>ذخیره</th>
                </tr>
                </thead>
                <tbody>
<?php
global $wpdb;
$table_name = $wpdb->prefix . "re_referral";

$retrieve_users = $wpdb->get_results( "SELECT * FROM $table_name ;" );

foreach ($retrieve_users as $user ){?>
        <tr  class="table-row con<?php echo $user->user_id ?>"  >
            <td><?php echo $user->user_id ?></td>
            <td><?php echo $user->phone  ?></td>
            <td><?php echo $user->full_name   ?></td>
            <td><?php echo $user->remaining  ?></td>
            <td><?php echo $user->score  ?></td>
            <td class="input-td"><input type="number" data-userid="input<?php echo $user->user_id ?>" placeholder="<?php echo $user->remaining   ?>"></td>
            <td class="ref-proc">
                <button data-userid="<?php echo $user->user_id ?>">ذخیره</button>


                <div class="slide-ref" id="<?php echo $user->user_id ?>" data-userid="<?php echo $user->user_id ?>" >
                    <div>
                        <div>

                        </div>
                    </div>
                </div>
            </td>
        </tr>

<?php } ?>



                </tbody>


                <tfoot>
                <tr>
                    <th>شناسه</th>
                    <th>تلفن</th>
                    <th>نام</th>
                    <th>مانده از خرید</th>
                    <th>مقدار کد معرف</th>
                    <th>تعیین مقدار</th>
                    <th>ذخیره</th>
                </tr>
                </tfoot>
            </table>

        </div>



        <div id="tab3" class="tab">
            <h3>نحوه محاسبه امتیاز</h3>
<?php   $ref_calculate = unserialize(  get_option('re_ref_set_calculate'  , true )); ?>
            <div class="ref-main-con">
                <div class="ref-con">

                    <div class="re-menu-items">
                        <div>
                            <p class="<?php echo $ref_calculate['status'] == '1' ? 're-ref-active' : '' ?>" data-item_number="#re1" > غیر فعال </p>
                            <p class="<?php echo $ref_calculate['status'] == '2' ? 're-ref-active' : '' ?>" data-item_number="#re2"  >امتیاز به صورت یکنواخت </p>
                            <p class="<?php echo $ref_calculate['status'] == '3' ? 're-ref-active' : '' ?>" data-item_number="#re3"  > امتیاز به صورت متغیر </p>
                        </div>
                    </div>

                    <form class="re-menu-pages" autocomplete="off">

                        <div class="re-ref-off <?php echo $ref_calculate['status'] == '1' ? 're-ref-page-active' : '' ?> " id="re1">
                            <p>غیر فعال</p>
                        </div>

                        <div class="re-ref-absolute <?php echo $ref_calculate['status'] == '2' ? 're-ref-page-active' : '' ?> " id="re2">
                            <div class="re-ref-cal-count">
                                <div>
                                    <p>تعیین تعداد دفعات قابل قبول</p>
                                </div>
                                <div>
                                    <input type="text" id="re-ref-cal-count-input" value="<?php echo $ref_calculate['status']  == '2' ? $ref_calculate['ref_term_count'] : '' ; ?> ">
                                    <label for="re-ref-cal-count-input">
                                        بار
                                    </label>
                                </div>
                            </div>

                            <div class="re-ref-cal-type">
                                <div>
                                    <p>نوع محاسبه امتیاز</p>
                                </div>
                                <div>
                                    <input type="radio" name="re-ref-cal-type-input" id="re-ref-cal-type-input-percent"   value="p"
                                        <?php checked($ref_calculate['ref_term_type'] , 'p' , 'checked="checked"') ?> >
                                    <label for="re-ref-cal-type-input-percent">
                                        درصدی
                                    </label>

                                    <input type="radio" name="re-ref-cal-type-input" id="re-ref-cal-type-input-fixed" value="f"
                                        <?php checked($ref_calculate['ref_term_type'] , 'f' , 'checked="checked"') ?>  >
                                    <label for="re-ref-cal-type-input-fixed">
                                        ثابت
                                    </label>
                                </div>

                            </div>

                            <div class="re-ref-cal-amount">
                                <div>
                                    <p>میزان امتیاز بابت هر بار خرید</p>
                                </div>
                                <div>
                                    <input type="text" id="re-ref-cal-count-input-amount"
                                           value="<?php echo $ref_calculate['status'] == '2' ? $ref_calculate['ref_term_amount'] : '' ; ?>">
                                    <?php
                                    if ($ref_calculate['status'] == '2' ) {
                                        if ($ref_calculate['ref_term_type'] =='p'){
                                            echo '<label for="re-ref-cal-count-input-amount">
                                        درصد
                                                </label>';
                                        }else{
                                            echo '<label for="re-ref-cal-count-input-amount">
                                        تومان
                                                </label>';
                                        }
                                    }else{
                                        echo '<label for="re-ref-cal-count-input-amount">
                                        درصد
                                              </label>';
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>

                        <div class="re-ref-dynamic <?php echo $ref_calculate['status'] == '3' ? 're-ref-page-active' : '' ?> " id="re3">
                            <div class="re-term-con-variable" data-settingval="">

                                <div>
                                    <ul>
                                        <?php
                                        if ( $ref_calculate['data'] ){
                                            foreach ($ref_calculate['data'] as $ref ){
                                                ?>
                                                <li data-termnumber="<?php echo $ref['termnumber']?>" data-termtype="<?php echo $ref['termtype']?>" data-termvalue="<?php echo $ref['termvalue']?>" >
                                                    <span>*</span>
                                                    <p>بار<span><?php echo $ref['termnumber']?></span> </p>
                                                    <p><?php echo $ref['termvalue']?><span><?php echo $ref['termtype']=='percent'? 'درصدی':'تومان'; ?></span> </p>
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

                    </form>

                    <div  class="re-ref-set-new-term" data-setnewterms="0" data-whitchinput="0">
                        <span class="re-ref-close">*</span>
                        <div>
                            <label for="re-set-input-1">تعیین مقدار به درصد</label>
                            <input data-active="0"  type="number" id="re-set-input-1"   class="re-set-input"  placeholder="1123" value="10">
                        </div>
                        <div>
                            <label for="re-set-input-2">تعیین مقدار ثابت به تومان</label>
                            <input data-active="0"   type="number" id="re-set-input-2"   class="re-set-input" placeholder="1231">
                        </div>
                        <div>
                            <p id="re-ref-save-new-term"  > ذخیره</p>
                        </div>
                    </div>

                    <div class="re-ref-save">
                        <p data-re_ref_settings="<?php echo '#re'.$ref_calculate['status']; ?>"> ذخیره</p>
                    </div>
                </div>
            </div>

        </div>



        <form id="tab4" class="tab" autocomplete="off">
            <h3>نحوه محاسبه جایزه</h3>
            <?php $how_cal_gift =  unserialize( get_option('re_ref_cal_gifts' , true ) ); ?>
            <div class="re-how-cal-score">
                <div class="re-how-cal-score-list-bth">
                    <p  class="<?php echo $how_cal_gift['status'] == 1 ? 'ref-cal-score-item' : ''; ?>" id="ref-score-cal-1">تخفیف درصدی</p>
                    <p  class="<?php echo $how_cal_gift['status'] == 2 ? 'ref-cal-score-item' : ''; ?>" id="ref-score-cal-2">تخفیف ثابت</p>
                    <p  class="<?php echo $how_cal_gift['status'] == 3 ? 'ref-cal-score-item' : ''; ?>" id="ref-score-cal-3">محصول رایگان</p>
                </div>
                <div class="re-how-many-get-score">
                    <label for="re-how-cal-get-gift">میزان کسب امتیاز برای بدست آوردن جایزه</label>
                    <input type="text" id="re-how-cal-get-gift" value="<?php echo $how_cal_gift['roof'] >  0 ? $how_cal_gift['roof'] : ''; ?>">
                </div>
                <div class="re-how-cal-score-list-con">

                    <div class="re-how-cal-first-con  ref-score-cal-1 <?php echo $how_cal_gift['status'] == 1 ? 'ref-cal-score-con' : ''; ?>">
                        <div>
                            <div>
                                <label for="re-how-cal-score-percent">مقدار تخفیف درصدی</label>
                                <input type="text" id="re-how-cal-score-percent" value="<?php echo   $how_cal_gift['status']  ==1 ? $how_cal_gift['amount'] : ''; ?>">
                            </div>
                            <div class="re-how-cal-score-save">
                                <p data-whichnput="1" >ذخیره</p>
                            </div>
                        </div>
                    </div>

                    <div class="re-how-cal-second-con ref-score-cal-2 <?php echo $how_cal_gift['status'] == 2 ? 'ref-cal-score-con' : ''; ?>">
                        <div>
                            <div>
                                <label for="re-how-cal-score-fixed">مقدار تخفیف ثابت</label>
                                <input type="text" id="re-how-cal-score-fixed" value="<?php echo  $how_cal_gift['status']  ==2 ? $how_cal_gift['amount'] : ''; ?>">
                            </div>
                            <div class="re-how-cal-score-save">
                                <p data-whichnput="2" >ذخیره</p>
                            </div>
                        </div>
                    </div>

                    <div class="re-how-cal-third-con ref-score-cal-3 <?php echo $how_cal_gift['status'] == 3 ? 'ref-cal-score-con' : ''; ?>">
                        <div>
                            <div>
                                <label for="re-how-cal-score-product">شناسه محصول</label>
                                <input type="text" id="re-how-cal-score-product" value="<?php   echo $how_cal_gift['status']  == 3 ? $how_cal_gift['amount'] : ''; ?>">
                            </div>
                            <div class="re-how-cal-score-save">
                                <p data-whichnput="3" >ذخیره</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </form>






    </div>



    <?php
}
