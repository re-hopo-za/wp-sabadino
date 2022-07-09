<?php




require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();




$table_profile = $wpdb->prefix . "re_aff_profile";

$sql_profile = "CREATE TABLE $table_profile (
  id BIGINT NOT NULL AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  date_register VARCHAR(255) DEFAULT null  NULL,
  aff_token VARCHAR(255) NOT NULL,
  adds_link VARCHAR(500) DEFAULT null  NULL, 
  child_register SMALLINT  DEFAULT 0  NULL ,
  child_purchase SMALLINT  DEFAULT 0  NULL ,
  credit INT  DEFAULT 0  NULL ,
  total_money SMALLINT  DEFAULT 0  NULL ,
  app_target VARCHAR(255)  DEFAULT null  NULL ,
  how_know VARCHAR(255)  DEFAULT null  NULL ,
  cart_number VARCHAR(255)  DEFAULT null  NULL ,
  cart_self_name VARCHAR(255)  DEFAULT null  NULL , 
  order_record TEXT  DEFAULT null  NULL , 
  PRIMARY KEY  (id)
) $charset_collate;";

    maybe_create_table( $table_profile, $sql_profile );






  $table_report = $wpdb->prefix . "re_aff_visits_report";


  $sql_report = "CREATE TABLE $table_report (
  id BIGINT NOT NULL AUTO_INCREMENT,
  aff_user_id BIGINT NOT NULL,
  date_register VARCHAR(255) DEFAULT null  NULL,
  visit_count SMALLINT  NOT NULL,  
  PRIMARY KEY  (id)
) $charset_collate;";

    maybe_create_table( $table_report, $sql_report );






    $table_payment = $wpdb->prefix . "re_aff_payment";


 $sql_payment = "CREATE TABLE $table_payment (
  id BIGINT NOT NULL AUTO_INCREMENT,
  aff_user_id BIGINT NOT NULL,
  deposit_date DATETIME DEFAULT '0000-00-00 00:00:00'  NULL,
  amount SMALLINT NOT NULL,  
  credit INT NOT NULL,  
  PRIMARY KEY  (id)
) $charset_collate;";

   maybe_create_table( $table_payment, $sql_payment );



