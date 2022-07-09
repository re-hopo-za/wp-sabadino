<?php

namespace Sabadino\features\delivering_time;


class DeliveringAdmin
{



    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public $options = [];
    public function __construct()
    {
        $this->defineOptions();
        add_action('admin_menu', [$this ,'addAdminPage']);
        add_action( 'admin_enqueue_scripts', [$this ,'enqueues'] );


    }

    public function defineOptions()
    {

        if ( !get_option( 'delivering_time_options' , true ) ){

            $deliveing_option = [
                'cancel_date'      => '' ,
                'vocations_manual' => '' ,
                'limit_one'        => '' ,
                'limit_two'        => '' ,
                'limit_three'      => '' ,
                'limit_four'       => '' ,
                'morning_active'   => '' ,
                'afternoon_active' => '' ,
                'evening_active'   => '' ,
                'limit_morning'    => '' ,
                'limit_afternoon'  => '' ,
                'limit_evening'    => '' ,
                'slider_input'     => '' ,
                'off_friday'       => ''
            ];
            update_option( 'delivering_time_options' ,$deliveing_option );
            $this->options = $deliveing_option;
        }else{
            $this->options = get_option( 'delivering_time_options' ,true );
        }




    }
 
    public function addAdminPage() {
        add_submenu_page(
            'woocommerce',
            __( 'انتخاب زمان ارسال', 're-pardis' ),
            __( 'انتخاب زمان ارسال','re-pardis' ),
            'manage_options',
            're_delivery',
            [$this ,'adminPageCallback']
        );
    }
 
    public function isSetIndex( $index ){
        $deliveing_option = $this->options ;
        if ( isset( $deliveing_option[$index] ) ){
            return $deliveing_option[$index];
        }
        return '';
    }
    
        
    
 

    public function adminPageCallback()
     {
         ?>
         <h1>تنظیمات و شخصی سازی</h1>
         <form action="" method="post"  class="limit">
             <section>
                 <div>
                     <label for="re_vocation_manual"> تعطیلات اضافی</label>
                     <textarea dir="ltr" style="text-align:left" name="re_vocation_manual"
                               id="re_vocation_manual" class="red_comma" lang="en"><?php echo $this->isSetIndex('vocations_manual'); ?></textarea>
                 </div>
    
    
                 <div class="limit-con">
                     <h2>تعیین محدودیت</h2>
                     <div class="<?php echo $this->isSetIndex('morning_active') == 0 ? 'deactivate-hours' : 'activate-hours'; ?>" >
                         <label for="re_limit_morning">
                             محدودیت برای شیفت صبح
                             <input style="text-align:left" type="text" name="re_limit_morning" id="re_limit_morning"
                                    value="<?php echo  $this->isSetIndex('limit_morning'); ?>">
                         </label>
                         <label for="morning_active">
                             فعال
                             <input type="radio" class="re_days_status"  name="re_morning_active" value="1" <?php checked(1 == $this->isSetIndex('morning_active') )?> id="morning_active">
                             غیر فعال
                             <input type="radio" class="re_days_status"   name="re_morning_active" value="0" <?php checked(0 == $this->isSetIndex('morning_active') ) ?> id="morning_active">
                         </label>
                     </div>
    
                     <div class="<?php echo $this->isSetIndex('afternoon_active') == 0 ? 'deactivate-hours' : 'activate-hours'; ?>">
                         <label for="re_limit_afternoon">
                             محدودیت برای شیفت ظهر
                             <input style="text-align:left" type="text" name="re_limit_afternoon" id="re_limit_afternoon"
                                    value="<?php echo $this->isSetIndex('limit_afternoon'); ?>">
                         </label>
                         <label for="afternoon_active">
                             فعال
                             <input type="radio" class="re_days_status"   name="re_afternoon_active" value="1" <?php checked(1 == $this->isSetIndex('afternoon_active') ) ?>  id="afternoon_active">
                             غیر فعال
                             <input type="radio" class="re_days_status"   name="re_afternoon_active" value="0" <?php checked(0 == $this->isSetIndex('afternoon_active') ) ?>   id="afternoon_active">
                         </label>
                     </div>
    
                     <div class="<?php echo $this->isSetIndex('evening_active') == 0 ? 'deactivate-hours' : 'activate-hours'; ?>">
                         <label for="re_limit_evening">
                             محدودیت برای شیفت عصر
                             <input style="text-align:left" type="text" name="re_limit_evening" id="re_limit_evening"
                                    value="<?php echo $this->isSetIndex('limit_evening' ); ?>">
                         </label>
                         <label for="evening_active">
                             فعال
                             <input type="radio" class="re_days_status" name="re_evening_active" value="1"  <?php checked(1 == $this->isSetIndex('evening_active') ) ?> id="evening_active">
                             غیر فعال
                             <input type="radio" class="re_days_status" name="re_evening_active" value="0"  <?php checked(0 == $this->isSetIndex('evening_active') ) ?>  id="evening_active">
                         </label>
                     </div>
                 </div>
    
    
    
    
                 <div class="times-con">
                     <h2>تعیین ساعت ها</h2>
                     <div class="morning_active <?php echo $this->isSetIndex('morning_active') == 0 ?'deactivate-hours' : ''; ?>"   >
                         <p>زمان اول</p>
                         <label for="">
                             <span>شروع</span>
                             <input type="text"   placeholder="23:00" name="re_first_time_start"
                                    value="<?php echo $this->isSetIndex('first_time_start'); ?>">
                         </label>
    
                         <label for="">
                             <span>اتمام</span>
                             <input type="text"   placeholder="23:00" name="re_first_time_end"
                                    value="<?php echo $this->isSetIndex('first_time_end'); ?>">
                         </label>
                     </div>
    
    
    
                     <div class="afternoon_active <?php echo $this->isSetIndex('afternoon_active') == 0 ? 'deactivate-hours' : ''; ?> " >
                         <p>زمان دوم</p>
                         <label for="">
                             <span>شروع</span>
                             <input type="text"   placeholder="23:00" name="re_second_time_start"
                                    value="<?php echo $this->isSetIndex('second_time_start'); ?>">
                         </label>
                         <label for="">
                             <span>اتمام</span>
                             <input type="text"   placeholder="23:00" name="re_second_time_end"
                                    value="<?php echo $this->isSetIndex('second_time_end'); ?>">
                         </label>
                     </div>
    
    
                     <div class="evening_active <?php echo $this->isSetIndex('evening_active') == 0 ? 'deactivate-hours' : ''; ?>">
                         <p>زمان سوم</p>
                         <label for="">
                             <span>شروع</span>
                             <input type="text" placeholder="23:00" name="re_third_time_start"
                                    value="<?php echo $this->isSetIndex('third_time_start'); ?>">
                         </label>
                         <label for="">
                             <span>اتمام</span>
                             <input type="text" placeholder="23:00" name="re_third_time_end"
                                    value="<?php echo $this->isSetIndex('third_time_end'); ?>">
                         </label>
                     </div>
                 </div>
    
             </section>

    
    
    
             <section>
                 <div>
                     <input style="text-align:left" type="number" name="re_limit_one" id="re_limit_one"
                            value="<?php echo $this->isSetIndex('limit_one'); ?>">
                     <label for="re_limit_one" >
                         محدودیت برای امروز
                         <span dir="ltr"><?php echo jdate("Y-m-d"); ?></span>
                     </label>
                 </div>
    
                 <div>
                     <input style="text-align:left" type="number" name="re_limit_two" id="re_limit_two"
                            value="<?php echo $this->isSetIndex('limit_two'); ?>">
                     <label for="re_limit_two">
                         محدودیت برای روز
                         <?php echo  jdate("l", strtotime("+1 day")); ?>
                         <span dir="ltr"><?php echo jdate("Y-m-d", strtotime("+1 day")); ?></span></label>
                 </div>
    
                 <div>
                     <input style="text-align:left" type="number" name="re_limit_three" id="re_limit_three"
                            value="<?php echo $this->isSetIndex('limit_three'); ?>">
                     <label for="re_limit_three">
                         محدودیت برای روز
                         <?php echo jdate("l", strtotime("+2 day")); ?>
                         <span dir="ltr"><?php echo jdate("Y-m-d", strtotime("+2 day")); ?></span>
                     </label>
                 </div>
    
                 <div>
                     <input style="text-align:left" type="number" name="re_limit_four" id="re_limit_four"
                            value="<?php echo $this->isSetIndex('limit_four'); ?>">
                     <label for="re_limit_four">
                         محدودیت برای روز
                         <?php echo  jdate("l", strtotime("+3 day")); ?>
                         <span dir="ltr"><?php echo  jdate("Y-m-d", strtotime("+3 day")); ?></span>
                     </label>
                 </div>
    
                 <div>
                     <input style="text-align:left" type="number" name="re_limit_five" id="re_limit_five"
                            value="<?php echo $this->isSetIndex('limit_five'); ?>">
                     <label for="re_limit_five">
                         محدودیت برای روز
                         <?php echo  jdate("l", strtotime("+4 day")); ?>
                         <span dir="ltr"><?php echo  jdate("Y-m-d", strtotime("+4 day")); ?></span>
                     </label>
                 </div>
    
             </section>
    

             <hr>
             <section class="re-save-con">
                 <div>
                     <label for="re_clear_old_date">حذف تاریخ های گذشته
                         <input type="checkbox" name="re_clear_old_date" id="re_clear_old_date" value="1">
                     </label>

                     <label for="re_off_friday"> بستن روزهای جمعه
                         <input type="checkbox" name="re_off_friday" id="re_off_friday" value="1" <?php  echo $this->isSetIndex('off_friday') == 1 ? 'checked' : '';   ?>>
                     </label>
    
                     <label style="color:darkred" for="re_clear_all_order">پاکسازی تعداد سفارشهای ذخیره شده
                         <input type="checkbox" name="re_clear_all_order" id="re_clear_all_order" value="1">
                     </label>
    
                 </div>
    
                 <div class="slidecontainer">
                     <label for="dateRange">
                         انتخاب تعداد روز
                         <input type="range" min="1" max="5" value="<?php echo $this->isSetIndex('slider_input'); ?>" name="re_slider_input"  class="slider" id="dateRange">
                         <span id="datePrint"><?php echo $this->isSetIndex('slider_input'); ?></span>
                     </label>
                 </div>
    
                 <div>
                     <input type="submit" name="re_submit" value="ذخیره">
                 </div>
    
             </section>
         </form>
    
         <?php
         $this->saveAdminForm();
    
     }


    public function saveAdminForm()
    {
        if( isset( $_POST['re_submit'] ) ) {
            $deliveing_option = [];
            $deliveing_option['vocations_manual'] = isset($_POST['re_vocation_manual']) ? $_POST['re_vocation_manual'] : '';
            $deliveing_option['limit_one'] = isset($_POST['re_limit_one']) ? $_POST['re_limit_one'] : '';
            $deliveing_option['limit_two'] = isset($_POST['re_limit_two']) ? $_POST['re_limit_two'] : '';
            $deliveing_option['limit_three'] = isset($_POST['re_limit_three']) ? $_POST['re_limit_three'] : '';
            $deliveing_option['limit_four'] = isset($_POST['re_limit_four']) ? $_POST['re_limit_four'] : '';
            $deliveing_option['limit_five'] = isset($_POST['re_limit_five']) ? $_POST['re_limit_five'] : '';
            $deliveing_option['limit_morning'] = isset($_POST['re_limit_morning']) ? $_POST['re_limit_morning'] : '';
            $deliveing_option['limit_afternoon'] = isset($_POST['re_limit_afternoon']) ? $_POST['re_limit_afternoon'] : '';
            $deliveing_option['limit_evening'] = isset($_POST['re_limit_evening']) ? $_POST['re_limit_evening'] : '';
            $deliveing_option['first_time_start'] = isset($_POST['re_first_time_start']) ? $_POST['re_first_time_start'] : '';
            $deliveing_option['first_time_end'] = isset($_POST['re_first_time_end']) ? $_POST['re_first_time_end'] : '';
            $deliveing_option['second_time_start'] = isset($_POST['re_second_time_start']) ? $_POST['re_second_time_start'] : '';
            $deliveing_option['second_time_end'] = isset($_POST['re_second_time_end']) ? $_POST['re_second_time_end'] : '';
            $deliveing_option['third_time_start'] = isset($_POST['re_third_time_start']) ? $_POST['re_third_time_start'] : '';
            $deliveing_option['third_time_end'] = isset($_POST['re_third_time_end']) ? $_POST['re_third_time_end'] : '';
            $deliveing_option['morning_active'] = isset($_POST['re_morning_active']) ? (int) $_POST['re_morning_active'] : '';
            $deliveing_option['afternoon_active'] = isset($_POST['re_afternoon_active']) ? (int) $_POST['re_afternoon_active'] : '';
            $deliveing_option['evening_active'] = isset($_POST['re_evening_active']) ? (int) $_POST['re_evening_active'] : '';
            $deliveing_option['slider_input'] = isset($_POST['re_slider_input']) ? $_POST['re_slider_input'] : '';
            $deliveing_option['off_friday'] =  $_POST['re_off_friday'] == 1 ? $_POST['re_off_friday'] : 0;

            update_option( 'delivering_time_options' ,$deliveing_option );

            if (isset($_POST['re_clear_old_date']) && $_POST['re_clear_old_date'] == 1) {

                global $wpdb;
                $table = $wpdb->prefix . "options";
                $date = jdate("Y/m/d", strtotime("-2 day"), '', '', 'en');
                $years = jdate("Y", '', '', '', 'en');
                $wpdb->get_results("DELETE FROM $table WHERE option_name < '$date'  AND option_name LIKE '$years%'  ");
            }


            if (isset($_POST['re_clear_all_order']) == 1) {
                global $wpdb;
                $table = $wpdb->prefix . "options";
                $years_all = jdate("Y", '', '', '', 'en');
                $date_all = jdate("Y/m/d", strtotime("+6 day"), '', '', 'en');
                $wpdb->get_results(" DELETE   FROM  $table  WHERE option_name < '$date_all'  AND option_name LIKE '$years_all%'  "); 
            }
          header("Refresh:0");
        }
    }


    public function enqueues($hook) {

        wp_enqueue_script(
                'delivering_script',
                RE_ASSETS.'js/date_ajax.js' ,
                ['jquery'] ,
                time()
        );

        wp_localize_script(
                'delivering_script',
                'ajax_object',
                [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]
       );
        wp_enqueue_style(
                're_admin_style' ,
                RE_ASSETS.'css/admin.css' ,
                [] ,
                time()
        );

    }


}
