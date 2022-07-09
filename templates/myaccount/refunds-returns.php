<?php




$user_id = get_current_user_id();
$table_name = 'wp_re_introduce_code';
global  $wpdb;



$self_user = $wpdb->get_results("SELECT * FROM {$table_name} WHERE self_user_id={$user_id} ;");

if(get_current_user_id() > 0 ) {


      if($self_user[0]->is_seller === 0 or $self_user[0]->is_seller == false) {
         ?>
         <div class="referral-container">
            <div class="first-div">
               <div>
                  <div>
                     <p>امتیاز شما</p>
                     <span class="dashicons dashicons-awards"></span>
                     <p><?php echo get_user_meta($user_id, 're_score_amount', true); ?></p>
                  </div>
               </div>
            </div>

            <div class="second-div">
               <div>
                  <div>
                     <p>کد معرف شما</p>
                     <span class="dashicons dashicons-clipboard"></span>
                     <p><?php echo get_user_meta($user_id, 're_self_introduce_code', true); ?></p>
                  </div>
               </div>
            </div>

            <div class="third-div">
               <div>
                  <div>
                     <p>کد معرف ثبت شده</p>
                     <span class="dashicons dashicons-clipboard"></span>
                     <p><?php echo get_user_meta($user_id, 're_introduce_code', true); ?></p>
                  </div>
               </div>
            </div>


            <div class="forth-div">
               <div>
                  <div>
                     <p>تعداد زیر مجموعه</p>
                     <span class="dashicons dashicons-buddicons-buddypress-logo"></span>
                     <p><?php echo $self_user[0]->child_count ?></p>
                  </div>
               </div>
            </div>

            <div>

               <button onclick="copyToClipboard('#re_url')">گپی</button>
               <p dir="ltr" id="re_url">https://galshop.ir/referral?rc=<?php echo get_user_meta($user_id, 're_self_introduce_code', true); ?></p>
            </div>
         </div>


         <?php
      } else {


      }
   }else{
      ?>
  <div>
     <p>لطفا وارد شوید</p>
  </div>
  <?php
   }


 ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
    }


</script>