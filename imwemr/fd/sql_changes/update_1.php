<?php 
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
$curr_dt=date('Y-m-d H:i:s');			  
if(imw_num_rows(imw_query("select * from account_trans"))==0){
	imw_query("CREATE TABLE IF NOT EXISTS account_trans(
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `patient_id` int(11) NOT NULL,
				  `encounter_id` int(11) NOT NULL,
				  `charge_list_id` int(11) NOT NULL,
				  `charge_list_detail_id` int(11) NOT NULL,
				  `copay_chld_id` int(11) NOT NULL,
				  `payment_by` varchar(250) NOT NULL,
				  `payment_method` varchar(250) NOT NULL,
				  `check_number` varchar(20) NOT NULL,
				  `cc_type` varchar(250) NOT NULL,
				  `cc_number` varchar(20) NOT NULL,
				  `cc_exp_date` varchar(20) NOT NULL,
				  `ins_id` int(11) NOT NULL,
				  `payment_amount` double(12,2) NOT NULL,
				  `payment_date` date NOT NULL,
				  `operator_id` int(11) NOT NULL,
				  `entered_date` datetime NOT NULL COMMENT 'Trans Entered Date time',
				  `payment_code_id` int(11) NOT NULL,
				  `del_status` int(2) NOT NULL,
				  `del_operator_id` int(12) NOT NULL COMMENT 'operator deleted transaction',
				  `del_date_time` datetime NOT NULL COMMENT 'transaction deleted date time',
				  `modified_date` datetime NOT NULL,
				  `modified_by` int(11) NOT NULL,
				  `payment_type` varchar(250) NOT NULL,
				  `batch_id` int(11) NOT NULL,
				  `cap_main_id` int(11) NOT NULL,
				  `era_amt` float(12,2) NOT NULL,
				  `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `parent_id` int(11) NOT NULL,
				   PRIMARY KEY (`id`)
				   ) ENGINE=MyISAM");
	
	imw_query("ALTER TABLE `account_payments` ADD `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
	imw_query("ALTER TABLE `paymentswriteoff` ADD `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
	imw_query("ALTER TABLE `patient_chargesheet_payment_info` ADD `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
	imw_query("ALTER TABLE `patient_charges_detail_payment_info` ADD `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
	imw_query("ALTER TABLE `creditapplied` ADD `date_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
	imw_query("ALTER TABLE `copay_policies` ADD `financial_dashboard` DATETIME NOT NULL");
	imw_query("UPDATE copay_policies SET financial_dashboard = '$curr_dt'"); 
	$qry=imw_query("select * from account_payments");	
	while($row=imw_fetch_array($qry)){
		$id=$row['id'];
		$patient_id=$row['patient_id'];
		$encounter_id=$row['encounter_id'];
		$charge_list_id=$row['charge_list_id'];
		$charge_list_detail_id=$row['charge_list_detail_id'];
		$copay_chld_id=$row['copay_chld_id'];
		$payment_by=$row['payment_by'];
		$payment_method=$row['payment_method'];
		$check_number=$row['check_number'];
		$cc_type=$row['cc_type'];
		$cc_number=$row['cc_number'];
		$cc_exp_date=$row['cc_exp_date'];
		$ins_id=$row['ins_id'];
		$payment_amount=$row['payment_amount'];
		$payment_date=$row['payment_date'];
		$operator_id=$row['operator_id'];
		$entered_date=$row['entered_date'];
		$payment_code_id=$row['payment_code_id'];
		$del_status=$row['del_status'];
		$del_operator_id=$row['del_operator_id'];
		$del_date_time=$row['del_date_time'];
		$modified_date=$row['modified_date'];
		$modified_by=$row['modified_by'];
		$payment_type=$row['payment_type'];
		$batch_id=$row['batch_id'];
		
		$rs=imw_query("INSERT INTO account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
						charge_list_detail_id='$charge_list_detail_id',copay_chld_id='$copay_chld_id',payment_by='$payment_by',payment_method='$payment_method',
						check_number='$check_number',cc_type='$cc_type',cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$ins_id',payment_amount='$payment_amount',
						payment_date='$payment_date',operator_id='$operator_id',entered_date='$entered_date',payment_code_id='$payment_code_id',del_status='$del_status',
						del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',payment_type='$payment_type',
						batch_id='$batch_id',parent_id='$id'");
	}
}
echo 'Query Executed Successfuly';

?>