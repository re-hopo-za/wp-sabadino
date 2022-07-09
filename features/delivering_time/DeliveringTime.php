<?php


namespace Sabadino\features\delivering_time;


class DeliveringTime
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
        self::defines();
        $this->hooks();
    }



    public static function defines()
    {
        define("RE_ADMIN", ZA_FEATURES_PATH . "deliver_time/admin/");
        define("RE_ASSETS", plugin_dir_url(__FILE__) . "assets/");

        DeliveringAdmin::get_instance();
        if( !is_admin() ){
            DeliveringPublic::get_instance();
        }
    }

    public function hooks()
    {
        add_action('woocommerce_before_order_notes', [ $this , 'addOrderMetaField' ] );
        add_action('woocommerce_checkout_process', [ $this , 'processOrderMetaField' ] );
        add_action('woocommerce_checkout_update_order_meta', [ $this , 'updateOrderMetaField' ] );
        add_action('woocommerce_admin_order_data_after_billing_address',[ $this , 'displaOrderMetaFieldInAdmin' ]  ,10 ,1);

        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'extra_fields' ) );
        add_action( 'save_post', array( $this, 'save_fields' ) );
    }


    public function save_fields( $post_id ) {
        if ( isset( $_POST['hold_deliver_time'] ) && !empty( $_POST['hold_deliver_time'] ) ) {
            update_post_meta( $post_id, 'hold_deliver_time', sanitize_text_field( $_POST['hold_deliver_time'] ) );
        }
    }

    public function extra_fields() {
        woocommerce_wp_text_input( array(
            'id'    => 'hold_deliver_time',
            'label' => __( 'زمان ارسال ', 'sabadino' ),
            'placeholder' => 0
        ) );
    }


    public function addOrderMetaField( $checkout )
    {
        woocommerce_form_field('daypart', [
            'type' => 'text',
            'require' => true,
            'class' => ['daypart'],
            'label' => __(' '),

        ],
            $checkout->get_value('daypart')
        );
        woocommerce_form_field('re_micro_time', [
            'type' => 'text',
            'require' => true,
            'class' => ['re_micro_time'],
            'label' => __(' '),

        ],
            $checkout->get_value('re_micro_time')
        );
    }

    public function processOrderMetaField()
    {
        global $woocommerce;
        if ($_POST['daypart'] == "blank")
            wc_add_notice('<strong>Please select a day part under Delivery options</strong>', 'error');
    }

    public function updateOrderMetaField( $order_id )
    {
        if ($_POST['daypart'])
            update_post_meta($order_id, 'daypart', esc_attr($_POST['daypart']));

        if ($_POST['re_micro_time'])
            update_post_meta($order_id, 're_micro_time', esc_attr($_POST['re_micro_time']));
    }

    public function displaOrderMetaFieldInAdmin( $order )
    {
        echo '<p>' . get_post_meta( $order->get_id(), 'daypart', true) . '</p>';
        echo '<p>' . get_post_meta( $order->get_id(), 're_micro_time', true) . '</p>';
    }



}




