<?php

namespace Sabadino\features\orders_on_excell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Sabadino\includes\Functions;

class ExportOrders
{


    protected static $_instance = null;
    public static function get_instance(){
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        self::defines();
        add_action('admin_menu', [$this , 'wcSubmenu'],99 );
        add_action("wp_ajax_re_get_orders_as_excel" ,[$this , 'preparingOrdersData'] );
        add_action('admin_enqueue_scripts' , [ $this , 'enqueue' ], 99 );
    }


    public static function defines()
    {
        define('RE_EXPORT_ASSETS' , plugin_dir_url(__FILE__).'assets/');
        define('RE_ADMIN_EXPORT'  , plugin_dir_url(__FILE__).'include/');
        define('RE_EXPORT_ADMIN'  , ZA_FEATURES_PATH.'orders_on_excell/');
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'za_export_excel' ,
            RE_EXPORT_ASSETS.'export-excel.js',
            array(),
            ZA_SABADINO_SCRIPTS_VERSION ,
            false
        );

        wp_localize_script(
            'za_export_excel' ,
            'za_export_excel_object',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ) ,
                'nonce'    => wp_create_nonce('za-main-nonce'),
            ]
        );
    }

    public function wcSubmenu()
    {
        add_submenu_page(
            'woocommerce',
            'استخراج سفارشات',
            'استخراج سفارشات',
            'manage_options',
            'wc-export-as-excell-submenu',
            [$this , 'register_my_custom_submenu_page'] );

    }
    public function register_my_custom_submenu_page() {
        ?>
        <div class="export-excel">
            <div class="tab-3">
                <div class="separation-con">
                    <?php
                    $date = jdate( 'Y/m/d'  );
                    $today_orders = wc_get_orders( array(
                        'limit'        => -1,
                        'orderby'      => 'date',
                        'order'        => 'DESC',
                        'status'       => 'wc-is-packing'
                       // 'meta_key'     => 'daypart',
                      //  'meta_compare' => 'LIKE',
                      // 'meta_value'   => $date,
                    ));
                    $product_separation = [];
                    if (!empty( $today_orders ) ){
                        foreach ($today_orders as $o_item ){ 
                            foreach ( $o_item->get_items() as $p_item ){  
                                if ( isset( $product_separation[ $p_item->get_product_id() ] ) ){
                                    $product_separation[ $p_item->get_product_id() ]['count']= $product_separation[ $p_item->get_product_id()  ]['count'] + $p_item->get_quantity();
                                }else{
                                    $product = wc_get_product((int) $p_item->get_product_id() ); 
                                    $cover   = '';
                                    if ( !empty( $product ) ){
                                        $cover = wp_get_attachment_url( $product->get_image_id() );
 
                                    }
                                  

                                    $product_separation[ $p_item->get_product_id() ] =
                                        [ 'id' => $p_item->get_product_id() , 'count' => $p_item->get_quantity() ,'name' => $p_item->get_name() ,'img' =>$cover ];
                                }
                            }
                        }
                        ?>
                        <table>
                            <thead>
                            <tr>
                                <th> ردیف </th>
                                <th> تصویر </th>
                                <th> نام </th>
                                <th> شناسه </th>
                                <th> مقدار </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach ( $product_separation as $product ){
                                ?>
                                <tr>
                                    <td><p><?php echo $i; ?></p></td>
                                    <td><p><img src="<?php echo $product['img']; ?>" alt=""></p></td>
                                    <td><p><?php echo $product['name']; ?></p></td>
                                    <td><p><?php echo $product['id']; ?></p></td>
                                    <td><p><?php echo $product['count']; ?> عدد </p></td>
                                </tr>
                                <?php $i++; } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <form action="<?php echo RE_EXPORT_ASSETS.'tmp/'.self::preparingOrdersData(); ?>">
                <input type="submit" value="دانلود سفارشات">
            </form>
        </div>
        <?php
        self::style();
    }

    public function preparingOrdersData(){

        $orders = [];
        $date      = jdate( 'Y/m/d'  );
        $getOrders = wc_get_orders( array(
            'limit'        => -1,
            'orderby'      => 'date',
            'order'        => 'DESC',
 //           'meta_key'     => 'daypart',
 //           'meta_compare' => 'LIKE',
  //          'meta_value'   => $date ,
            'status'       => 'wc-is-packing'
        ));

         if ( !empty( $getOrders ) ){
            $row = 1;
            foreach( $getOrders as $order ){
                $orders[$order->get_id()]['row']   = $row;
                $orders[$order->get_id()]['send_time'] = get_post_meta( $order->get_id() ,'daypart' ,true );
                $orders[$order->get_id()]['name']  = $order->get_billing_first_name().' '.$order->get_billing_last_name();
                $orders[$order->get_id()]['id']    = $order->get_id();
                $orders[$order->get_id()]['phone'] = $order->get_billing_phone();

                foreach ($order->get_items() as $products ){
                    $orders[$order->get_id()]['products'][$products->get_product_id()] = [
                        'name'     => $products->get_name() ,
                        'quantity' => $products->get_quantity()
                    ];
                    $p_ob = wc_get_product( (int) $products->get_product_id() );
                    if ( $p_ob  ){
                        $orders[$order->get_id()]['products'][$products->get_product_id()]['price'] = $p_ob->get_price();
                    }else{
                        $orders[$order->get_id()]['products'][$products->get_product_id()]['price'] = 'بدون قیمت';
                    }
                }
                $orders[$order->get_id()]['address'] = $order->get_billing_address_1();
                $orders[$order->get_id()]['total'] = $order->get_total();
                $orders[$order->get_id()]['pay_type'] = Functions::getPaymentType( $order );
                $orders[$order->get_id()]['description'] = $order->get_customer_note();
                $row++;
            }

        }

        $titles      = ['ردیف' ,'زمان ارسال' ,'نام مشتری' ,'شماره فاکتور' ,'تلفن' ,'کالا'  ,'وزن' ,'فی' ,'آدرس' ,'مبلغ کل' ,'نحوه پرداخت' ,'توضیحات' ];
        $writer      = '';
        $spreadsheet = new Spreadsheet();
        $alphabets = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $title_row = 0;
        foreach ( $titles as $title ) {
            try {
                $spreadsheet->setActiveSheetIndex(0 )
                    ->setRightToLeft(true)
                    ->setCellValue($alphabets[$title_row].'1',$title);
            } catch (Exception $e) {
                error_log($e->getMessage());
                continue;
            }
            $title_row++;
        }
        try {
            $spreadsheet->setActiveSheetIndex(0)
                ->setRightToLeft(true)
                ->getStyle( $alphabets[0] . '1' . ':' . $alphabets[9] . '1' )
                ->getFill()
                ->setFillType('solid' )
                ->getStartColor()
                ->setRGB('F28A8C');
        } catch (Exception $e) {
        }
        $j = 3;
        foreach ( $orders as $item  ){
            $i = 0;
            $j_merge_loop  = $j;
            $j_merge_first = $j;

            foreach ( $item as $k => $v ){
                $i_merge_first = $i;
                $spreadsheet->getActiveSheet()->getColumnDimension( $alphabets[$i_merge_first] )->setWidth(30 );

                if ( $k == 'products' ){
                    foreach ( $v as $p_k => $p_v ) {
                        try {
                            $spreadsheet->setActiveSheetIndex(0 )
                                ->setRightToLeft(true)
                                ->setCellValue($alphabets[$i_merge_first].$j_merge_loop, $p_v['name'] );
                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            continue;
                        }
                        try {
                            $spreadsheet->setActiveSheetIndex(0 )
                                ->setRightToLeft(true)
                                ->setCellValue($alphabets[$i_merge_first+1].$j_merge_loop, $p_v['quantity'] );

                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            continue;
                        }
                        try {
                            $spreadsheet->setActiveSheetIndex(0 )
                                ->setRightToLeft(true)
                                ->setCellValue($alphabets[$i_merge_first+2].$j_merge_loop, $p_v['price']);

                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            continue;
                        }
                        $j_merge_loop++;
                    }

                    $j = $j_merge_first ;
                    $i = $i+3;
                }else{
                    try {
                        $spreadsheet->setActiveSheetIndex(0 )
                            ->setRightToLeft(true)
                            ->setCellValue($alphabets[$i].$j, $v )
                            ->mergeCells($alphabets[$i].$j.':'.$alphabets[$i].($j+count($item['products']) -1 ));

                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        continue;
                    }
                    $i++;
                }

            }
            $j=$j_merge_loop+1;
        }

        try {
            $spreadsheet->getDefaultStyle()->getFont()->setSize(12 );
            $spreadsheet->getDefaultStyle()->getFont()->getColor()->setRGB('444444');
            $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal('center');
            $spreadsheet->getDefaultStyle()->getAlignment()->setVertical('center');

        }catch (Exception $e) {
        }
        try {
            $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
        }
        $rand      = wp_generate_password(12,false,false );
        $file_name = $rand.time() . '.xlsx';
        $file_url  = RE_EXPORT_ADMIN.'assets/tmp/' .$file_name;

        $writer->save( $file_url );
        return $file_name;

    }


    public static function style() :void
    {
        ?>
            <style>
                .export-excel{
                    display:flex;
                    flex-direction:column;
                    align-items:flex-end;
                    width:90%;
                    margin:0 auto;
                }
                .export-excel .tab-3{
                    width:100%;
                }
                .separation-con{
                    background-color:#fff;
                    width:95%;
                    padding:20px;
                    margin: 40px auto 0;
                }

                .separation-con table{
                    width:100%;
                }
                .separation-con td ,
                .separation-con th{
                    text-align:center;
                    padding:0;
                    min-height:60px;
                    height:60px;
                }
                .separation-con td{
                    border-top:1px solid #eee;
                }
                .separation-con th{
                    padding-top:3px;
                    background-color:#ddd;
                    color:#000;
                }
                .separation-con table p{
                    display:flex;
                    justify-content:center;
                    align-items:center;
                    height:100%;
                    font-size:17px;
                }
                .separation-con td img{
                    width:60px!important;
                    height:60px!important;
                }
                .export-excel form{
                    width:100%;
                    margin:0 auto;
                    display:flex;
                    flex-direction:row-reverse;
                }
                .export-excel form input{
                    padding:10px;
                    margin-left:5px;
                    margin-top:50px;
                    background-color:green;
                    border:1px solid green;
                    color:#fff;
                    cursor:pointer;
                    transition:0.3s ease-in-out;
                }
                .export-excel form input:hover{
                    background-color:#fff;
                    color:green;
                }
            </style>

        <?php
    }


}