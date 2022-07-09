<?php


 if ( defined('ABSPATH' ) )  exit();

include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';


include plugin_dir_path(__FILE__).'convert-to-word.php';
header('Content-Type: text/html; charset=utf-8');
 $p_id   = $_GET['p_id'];

 $order =  wc_get_order($p_id);

$time = $order->get_date_created()->date('Y-m-d H:i:s');
$payment_meth = '';
$pay_meth = $order->get_payment_method();
 if ($pay_meth ==='place' || $pay_meth ==='cod'){
     $payment_meth = 'پرداخت در محل';
 }elseif ($pay_meth === 'portal'){
     $payment_meth = 'پرداخت اینترنتی' ;
 }else{
     $payment_meth = $order->get_payment_method();
 }

 ?>
<head>
    <link href="<?php echo plugin_dir_url(__FILE__).'media.css'; ?>" media="print" rel="stylesheet" />
    <title> <?php  echo $order->get_id();  ?>  فاکتور   </title>
</head>

<div class="main_invoice"  >

    <div class="header" >
      <div class="logo">
          <img src="https://sabadino.com/wp-content/uploads/2020/10/sabadino-logo-e1602588374775.png" alt="">
      </div>
      <div class="site-name">
          <h2>سبدینو</h2>
      </div>
        <div class="order-date">
          <p> : تاریخ ثبت سفارش </p>
            <span><?php echo jdate('Y-m-d H:i:s' , strtotime($time)) ?></span>
        </div>
    </div>

    <div class="body"  >
        <h4>مشخصات خریدار</h4>
     <div>
         <p class="family"> نام خانوادگی : <span><?php echo  $order->get_user()->first_name." ".$order->get_user()->last_name; ?></span></p>
         <p class="invoice-number"> شماره فاکتور : <span><?php echo $order->get_id(); ?>#</span></p>
         <p class="delivery-date"> تاریخ تحویل سفارش : <span ><?php
                 $delivery_date = explode('|' , $order->get_meta('daypart' , true ) );
                 echo str_replace('_' ,'  الی  ' , $delivery_date[0] )."</span> <span dir='ltr'>".$delivery_date[1] ?></span></p>
         <p class="address"> آدرس : <span><?php echo $order->get_billing_address_1(); ?></span></p>
         <p class="phone"> شماره تماس : <span><?php echo $order->get_billing_phone();  ?></span></p>
         <p class="phone"> نحوه پرداخت  : <span><?php echo $payment_meth;  ?></span></p>
     </div>
    </div>

    <div class="footer" >
        <h4>مشخصات سفارش</h4>
        <table>
            <thead>
                 <tr>
                     <th>نام کالا</th>
                     <th>تعداد</th>
                     <th>مبلغ واحد</th>
                     <th>مبلغ کل</th>
                 </tr>
            </thead>
            <tbody>
            <?php
            $product_price = 0 ;
            foreach ($order->get_items() as $item){
                $data = $item->get_data();
                $count = $data['quantity'];
                $product_price +=   $data['subtotal'] ;

                ?>
                <tr>
                    <td><?php echo $data['name']; ?></td>
                    <td><?php echo  $count  ; ?></td>
                    <td class="int"><?php echo number_format( $data['subtotal'] / $count ); ?></td>
                    <td class="int"><?php echo number_format($data['subtotal']  );   ?> </td>
                </tr>
           <?php } ?>
                <tr>
                    <td colspan="3">جمع مبلغ</td>
                    <td class="int"><?php echo number_format($product_price); ?></td>
                </tr>

                <tr>
                    <td colspan="3">مبلغ تخفیف</td>
                    <td class="int dis"><?php echo number_format($order->get_total_discount()); ?></td>
                </tr>

                <tr>
                    <td colspan="3">جمع کل</td>
                    <td colspan="1" class="int total"><?php echo number_format($order->get_total())  ?></td>
                </tr>
                <tr class="last-row">
                    <td colspan="4">
                        <?php
                        $number = new Number2Word;
                        echo $number->numberToWords( $order->get_total() );?>
                        تومان
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="customer_note">
          <pre dir="ltr">
                <?php echo get_user_meta( $order->get_user() , 'description' );   
                 echo !empty( $order->get_customer_order_notes() ) ? $order->get_customer_note() : '';  ?>
			</pre>
        </div>
        <div>
            <p>
                بزرگراه ستاری بلوار فردوس شرق ساختمان بلوط پلاک 428 طبقه دوم واحد 23
            </p>
            <p>
                021 666 86 338
            </p>
        </div>
    </div>

</div>







<script>
    // window.print();
</script>
<style >


    @page {
        size: 7in 9.25in;   /* auto is the initial value */
        margin: 0;  /* this affects the margin in the printer settings */
    }
    .customer_note{
        padding: 10px 10px 0 0;
        line-height: 1.5;
    }
    .main_invoice{
        width: 800px;
        margin: 0 auto;
        border: 2px solid #999;
        padding: 20px;
    }

    .main_invoice .header{
        height: 70px;
        border-bottom: 1px solid #ccc;
    }

    .main_invoice .header .logo{
        width: 30%;
        float: right;
    }
    .main_invoice .header .logo img{
        width:50px;
        float: right;
        margin-right: 30px;
    }

    .main_invoice .header .site-name{
        width:40%;
        float: right;
        text-align: center;
    }
    .main_invoice .header .site-name h2{
        height: 100%;
        margin: 0;
        font-size:40px;
        text-shadow: 0 0 10px  #eee;
    }

    .main_invoice .header .order-date{
        width: 30%;
        padding-top: 1px;
    }

    .main_invoice .header .order-date p{
        font-size: 14px;
        width: 38%;
        float: right;
    }
    .main_invoice .header .order-date span{
        color: #444;
        width: 62%;
        font-size: 14px;
        line-height: 45px;
        font-weight: bold;
        display: block;
        float: right;

    }

    .body h4{
        width: 100%;
        text-align: center;
        padding: 25px 0 5px 0;
        margin: 0;
        color: #5c5c5c;
        font-size:23px;
        text-shadow: 0 0 10px  #eee;
    }

    .body div{
        width: 90%;
        margin:0 auto;
        border:1px solid #ccc;
        padding:10px;
        direction: rtl;
    }
    .body div p{
        width: 45%;
        display: inline-block;
        margin:0 auto;
        color: #999;
        border-right:2px solid #555;
        padding:10px;
        margin:1px;
    }
    .body div p span{
        color: #222;
    }
    .body div p>span:nth-child(2){
        margin-right:20px;
        font-weight: bold;
    }
    .footer{
        width: 93%;
        margin:0 auto;
        direction: rtl;
    }
    .footer h4{
        width: 100%;
        text-align: center;
        padding: 23px 0 5px 0;
        margin: 0;
        color: #5c5c5c;
        font-size:23px;
        text-shadow: 0 0 10px  #eee;
    }
    table {
        width: 100%;
        border: 1px solid #ccc;
    }

    table th{
        background-color: #555 !important;
        text-align: center;
        color: #fff;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    table td{
        text-align: center;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }


    .last-row td{
        background-color: #555 !important;
        color: #fff;
        font-weight: bold;
        word-spacing: 3px;
        font-size: 17px;
    }

    table td.int{
        font-weight: bold;
        font-size: 15px;
        color: #333;
    }

    table td.total{
        font-weight: bold;
        font-size: 15px;
        color: #fff;
        background-color: #555 !important;
    }


    .footer div{
        height: 40px;
        margin-top: 50px;
        box-shadow: 0 0 5px #eee;
        border-right:3px solid #999;
    }

    .footer div p:first-of-type{
        width: 75%;
        float: right;
        margin: 0;
        padding: 10px;
        font-size: 17px

    }
    .footer div p:last-of-type{
        width: 20%;
        float: left;
        margin: 0;
        padding: 10px 0;
        font-size: 17px;
        direction: ltr;
        text-align: center;
    }

    @media print {
        body {-webkit-print-color-adjust: exact;}
    }








</style>
<?php

    function get_private_order_notes( $order_id){
        global $wpdb;
        $order_note =[];

        $table_comment = $wpdb->comments;
        $table_comment_meta = $wpdb->commentmeta;
        $results = $wpdb->get_results("
        SELECT wc_comment.* FROM {$table_comment} AS wc_comment 

        WHERE  wc_comment.comment_post_ID = $order_id AND wc_comment.comment_type LIKE 'order_note'  
    ");
        var_dump($wpdb);
        foreach($results as $note){
            $order_note[]  = array(
                'note_id'      => $note->comment_ID,
                'note_date'    => $note->comment_date,
                'note_author'  => $note->comment_author,
                'note_content' => $note->comment_content,
                'all' => $note
            );
        }
        return $order_note;
    }





