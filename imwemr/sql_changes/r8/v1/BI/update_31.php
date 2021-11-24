<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("ALTER TABLE `cas_reason_code` ADD `cas_action_type` VARCHAR( 50 ) NOT NULL ,ADD `cas_adjustment_negative` INT( 1 ) NOT NULL ,ADD `cas_update_allowed` INT( 1 ) NOT NULL") or $msg_info[] = imw_error();

imw_query("update cas_reason_code set cas_code=REPLACE(cas_code,'-',' ') where cas_code like '%-%'");
/*paymentswriteoff = 'CO 42', 'CO 45', 'PI 45', 'CO 150', 'CO 172', 'CO 59', 'CO 237', 'CO B10', 'CO B15', 'CO 104', 'CO 144', 'OA 45', 'CO 131', 'CO 187', 'CO 223', 'CO 253', 'PI 253', 'CR 253', 'OA 253', 'CO 35', 'CO 15', 'PR 45', 'PI 59', 'CO 5487'
				     'CO 223', 'CO 253', 'CR 253', 'OA 253', 'PI 253'
payment_deductible = 'PR 1', 'OA 1'
account_payments - Co-Insurance =  'PR 2', 'PR 3'
deniedpayment = 'PR 96', 'PR 49', 'PR 119', 'PR 129', 'PR 11', 'PR 16', 'PR 18', 'PR 20', 'PR 21', 'CO 96', 'CO 97'
account_payments - Adjustment = 'CO 137', 'CO 144'*/

$trans_arr["Write Off"]=array('CO 42','CO 45','PI 45','CO 150','CO 172','CO 59','CO 237','CO B10','CO B15','CO 104','CO 144','OA 45','CO 131','CO 187','CO 223','CO 253','PI 253','CR 253','OA 253','CO 35','CO 15','PR 45','PI 59','CO 5487');
$trans_arr["Deductible"]=array('PR 1','OA 1');
$trans_arr["Co-Insurance"]=array('PR 2','PR 3');
$trans_arr["Denied"]=array('PR 96','PR 49','PR 119','PR 129','PR 11','PR 16','PR 18','PR 20','PR 21','CO 96','CO 97');
$trans_arr["Adjustment"]=array('CO 137');

foreach($trans_arr as $main_trans_key=>$main_trans_val){
	foreach($trans_arr[$main_trans_key] as $trans_key=>$trans_val){
		$up_qry=$up_qry_ins="";
		$trans_val_exp=explode(' ',$trans_val);
		if($main_trans_key=="Write Off" && strtolower($trans_val)!="co 223" && strtolower($trans_val)!="co 253" && strtolower($trans_val)!="cr 253" && strtolower($trans_val)!="oa 253" && strtolower($trans_val)!="pi 253"){
			$up_qry_ins=",cas_update_allowed='1'";
		}
		if(strtolower($trans_val)=="co 144"){
			$up_qry_ins=",cas_adjustment_negative='1',cas_update_allowed='1'";
		}
		$qry = imw_query("SELECT cas_id,cas_action_type FROM cas_reason_code where LOWER(cas_code)='".strtolower($trans_val)."'");
		if(imw_num_rows($qry)>0){
			$row = imw_fetch_array($qry);
			if($row['cas_action_type']==""){
				$up_qry="update cas_reason_code set cas_action_type='$main_trans_key'".$up_qry_ins." where cas_id='".$row['cas_id']."'";
				//echo $main_trans_key.' - '.$trans_val.'<br>';
			}
		}else{
			$qry = imw_query("SELECT cas_id,cas_action_type FROM cas_reason_code where LOWER(cas_code)='".strtolower(trim($trans_val_exp[1]))."'");
			if(imw_num_rows($qry)>0){
				$row = imw_fetch_array($qry);
				if($row['cas_action_type']==""){
					$up_qry="update cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val'".$up_qry_ins." where cas_id='".$row['cas_id']."'";
					//echo $main_trans_key.' - '.$trans_val.'<br>';
				}
			}else{
				if($trans_val=="OA 1"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Deductible Amount'";
				}else if($trans_val=="CO 96"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Non-covered charge(s)'";
				}else if($trans_val=="OA 45" || $trans_val=="PR 45"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Charges exceed our fee schedule or maximum allowable amount'";
				}else if($trans_val=="CR 253" || $trans_val=="OA 253"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Sequestration-reduction in federal payment'";
				}else if($trans_val=="PI 59"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Charges are adjusted based on multiple surgery rules or concurrent anesthesia rules'";
				}else if($trans_val=="CO 223"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Adjustment code for mandated federal, state or local law/regulation that is not already covered by another code and is mandated before a new code can be created'";
				}else if($trans_val=="CO 187"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Consumer Spending Account payments'";
				}else if($trans_val=="CO 172"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='Payment is adjusted when performed/billed by a provider of this specialty'";
				}else if($trans_val=="CO 150"){
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='	Payer deems the information submitted does not support this level of service'";
				}else{
					$up_qry="insert into cas_reason_code set cas_action_type='$main_trans_key',cas_code='$trans_val',cas_desc='$trans_val'";
				}
			}
		}
		if($up_qry!=""){
			//echo $up_qry.'<br>';
			imw_query($up_qry);
		}
	}
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 31 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 31 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 31</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>