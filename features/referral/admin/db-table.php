<?php



require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();



//$wpdb->show_errors(true);
$table_profile = $wpdb->prefix . "re_referral";

$sql_profile = "CREATE TABLE $table_profile (
  id BIGINT NOT NULL AUTO_INCREMENT,
  parent_id BIGINT DEFAULT null NULL ,
  user_id BIGINT NOT NULL ,
  phone VARCHAR(50) DEFAULT null NULL , 
  date_register VARCHAR(255) DEFAULT null  NULL,
  self_referral SMALLINT DEFAULT null NULL,
  parent_referal SMALLINT  DEFAULT null NULL,
  score INT DEFAULT 0  NULL, 
  score_ids  VARCHAR(500) DEFAULT 0  NULL, 
  remaining INT DEFAULT 0  NULL , 
  purchase_ids INT  DEFAULT 0  NULL , 
  payment_record TEXT DEFAULT null NULL ,
  PRIMARY KEY  (id)
) $charset_collate;";

maybe_create_table( $table_profile, $sql_profile );

//$wpdb->print_error();





