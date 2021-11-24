<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


//CHECK VERSION OF MYSQL. IF OLD VERSION THEN NO MORE THAN ONE TIMESTAMP TYPE OF COLUMNS CREATED IN A TABLE 
//SO FIRST MODIFYING OLD TIMESTAMP COLUMN WHICH IS USED FOR FINANCIAL DASHBOARD SO THAT NEW COLUMN CAN BE CREATED IN THOSE TABLES.
$mysql_ver=mysqli_get_server_info($conLink);
if($mysql_ver<="5.6.1"){
	imw_query("ALTER TABLE  `account_payments` CHANGE  `date_timestamp`  `date_timestamp` DATETIME NOT NULL");
	imw_query("ALTER TABLE  `paymentswriteoff` CHANGE  `date_timestamp`  `date_timestamp` DATETIME NOT NULL");
	imw_query("ALTER TABLE  `patient_chargesheet_payment_info` CHANGE  `date_timestamp`  `date_timestamp` DATETIME NOT NULL");
	imw_query("ALTER TABLE  `patient_charges_detail_payment_info` CHANGE  `date_timestamp`  `date_timestamp` DATETIME NOT NULL");
	imw_query("ALTER TABLE  `creditapplied` CHANGE  `date_timestamp`  `date_timestamp` DATETIME NOT NULL");
}

imw_query("ALTER TABLE copay_policies ADD report_closed_day DATETIME NOT NULL") or $msg_info[] = imw_error();
imw_query("ALTER TABLE patient_charge_list ADD `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE patient_charge_list_details ADD report_date_timestamp TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE defaultwriteoff ADD report_date_timestamp TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE  `account_payments` ADD  `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE  `paymentswriteoff` ADD  `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE  `patient_chargesheet_payment_info` ADD  `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE  `patient_charges_detail_payment_info` ADD  `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE  `creditapplied` ADD  `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();

/*imw_query("ALTER TABLE patient_charge_list CHANGE `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE patient_charge_list_details CHANGE `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE defaultwriteoff CHANGE `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE account_payments CHANGE  `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE paymentswriteoff CHANGE  `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE patient_chargesheet_payment_info CHANGE  `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE patient_charges_detail_payment_info CHANGE  `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();
imw_query("ALTER TABLE creditapplied CHANGE  `report_date_timestamp` `report_date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP") or $msg_info[] = imw_error();*/


imw_query("CREATE TABLE IF NOT EXISTS report_enc_detail (report_enc_detail_id int(11) NOT NULL AUTO_INCREMENT,
  patient_id int(11) NOT NULL,encounter_id int(11) NOT NULL,charge_list_id int(11) NOT NULL,charge_list_detail_id int(11) NOT NULL,case_type_id int(11) NOT NULL,
  date_of_service date NOT NULL,proc_code_id int(11) NOT NULL,pri_prov_id int(11) NOT NULL,sec_prov_id int(11) NOT NULL,facility_id int(11) NOT NULL,
  gro_id int(11) NOT NULL,reff_phy_id int(11) NOT NULL,pri_ins_id int(11) NOT NULL,sec_ins_id int(11) NOT NULL,tri_ins_id int(11) NOT NULL,
  units varchar(10) NOT NULL,charges double(12,2) NOT NULL,total_charges double(12,2) NOT NULL,pri_due double(12,2) NOT NULL,sec_due double(12,2) NOT NULL,
  tri_due double(12,2) NOT NULL,pat_due double(12,2) NOT NULL,mod_id1 int(11) NOT NULL,mod_id2 int(11) NOT NULL,mod_id3 int(11) NOT NULL,
  dx_id1 varchar(10) NOT NULL,dx_id2 varchar(10) NOT NULL,dx_id3 varchar(10) NOT NULL,dx_id4 varchar(10) NOT NULL,dx_id5 varchar(10) NOT NULL,
  dx_id6 varchar(10) NOT NULL,dx_id7 varchar(10) NOT NULL,dx_id8 varchar(10) NOT NULL,dx_id9 varchar(10) NOT NULL,dx_id10 varchar(10) NOT NULL,
  dx_id11 varchar(10) NOT NULL,dx_id12 varchar(10) NOT NULL,approved_amt double(12,2) NOT NULL,write_off double(12,2) NOT NULL,write_off_code_id int(11) NOT NULL,
  write_off_dop date NOT NULL,write_off_dot date NOT NULL,write_off_by int(11) NOT NULL,write_off_opr_id int(11) NOT NULL,proc_balance double(12,2) NOT NULL,
  superbill_id int(11) NOT NULL,sb_proc_id int(11) NOT NULL,entered_date datetime NOT NULL,operator_id int(11) NOT NULL,del_status int(11) NOT NULL,
  trans_del_date datetime NOT NULL,del_operator_id int(11) NOT NULL,last_pri_paid_date date NOT NULL,last_sec_paid_date date NOT NULL,
  last_ter_paid_date date NOT NULL,last_pat_paid_date date NOT NULL,batch_id int(11) NOT NULL,from_sec_due_date date NOT NULL,
  from_ter_due_date date NOT NULL,from_pat_due_date date NOT NULL,primary_provider_id_for_reports int(11) NOT NULL,proc_selfpay int(2) NOT NULL,
  first_posted_date date NOT NULL,first_posted_opr_id int(11) NOT NULL,sch_app_id int(11) NOT NULL,PRIMARY KEY (report_enc_detail_id)) ENGINE=MyISAM  DEFAULT CHARSET=latin1");

imw_query("CREATE TABLE IF NOT EXISTS report_enc_trans (report_trans_id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11) NOT NULL,master_tbl_id int(11) NOT NULL,patient_id int(11) NOT NULL,encounter_id int(11) NOT NULL,
  charge_list_id int(11) NOT NULL,charge_list_detail_id int(11) NOT NULL,trans_by varchar(250) NOT NULL,trans_ins_id int(11) NOT NULL,
  trans_method varchar(250) NOT NULL,check_number varchar(20) NOT NULL,cc_type varchar(250) NOT NULL,cc_number varchar(20) NOT NULL,
  cc_exp_date varchar(20) NOT NULL,trans_type varchar(250) NOT NULL,trans_amount double(12,2) NOT NULL,units int(11) NOT NULL,
  trans_code_id int(11) NOT NULL,batch_id int(11) NOT NULL,cap_main_id int(11) NOT NULL,trans_dot date NOT NULL COMMENT 'Trans Entered Date time',
  trans_dot_time time NOT NULL,trans_dop date NOT NULL,trans_dop_time time NOT NULL,trans_operator_id int(11) NOT NULL,trans_del_date date NOT NULL,
  trans_del_time time NOT NULL,trans_del_operator_id int(11) NOT NULL,era_amt double(12,2) NOT NULL,cas_type varchar(50) NOT NULL,
  cas_code varchar(50) NOT NULL,trans_qry_type varchar(20) NOT NULL,date_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (report_trans_id)) ENGINE=MyISAM  DEFAULT CHARSET=latin1");

imw_query("ALTER TABLE report_enc_detail ADD over_payment DOUBLE( 12, 2 ) NOT NULL ,ADD submitted VARCHAR( 5 ) NOT NULL");

imw_query("ALTER TABLE report_enc_detail ADD lastPayment DOUBLE( 12, 2 ) NOT NULL ,ADD lastPaymentDate DATE NOT NULL");

imw_query("ALTER TABLE `report_enc_detail` ADD `statement_status` INT( 12 ) NOT NULL,ADD `statement_date` DATE NOT NULL, ADD `letter_sent_date` DATE NOT NULL, ADD `collection` ENUM( 'false', 'true' ) NOT NULL DEFAULT 'false',
ADD `collectionAmount` DOUBLE( 12, 2 ) NOT NULL ,ADD `collectionDate` DATE NOT NULL ,ADD `collection_sent` INT( 2 ) NOT NULL ,ADD `letter_sent_id` INT( 11 ) NOT NULL");

imw_query("ALTER TABLE `report_enc_detail` ADD `re_submitted` VARCHAR( 10 ) NOT NULL ,ADD `re_submitted_date` DATE NOT NULL ");

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>Update 1 Failed!</b>";
	$color = "red";
}
else
{
	imw_query("UPDATE copay_policies SET report_closed_day = '2017-09-24 10:10:10'");
	$msg_info[] = "<br><br><b>Release 8:<br>Update 1 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 1 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>