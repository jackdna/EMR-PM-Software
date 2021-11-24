<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>IDX_INVISION_RCO MATCHING</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../../../interface/themes/default/common1.css">
</head>

<body>
<big><b>Displaying CSV records. Matched with already existing records in table "idx_invision_rco".</b></big>
<div class="section padd10" style="width:300px;">
  <div class="fl mr5" style="width:12px; height:13px; background-color:#000;"></div> Matching Records.<br>
  <div class="fl mr5" style="width:12px; height:13px; background-color:#ff0000;"></div> Not Found Records.<br>
  <div class="fl mr5" style="width:12px; height:13px; background-color:#0000ff;"></div> Manual Checking Required.<br>
  <b>White Text in Grey Background is Database record which we already have in Mapping table. This is provided to compare with blue record.</b>
</div><BR>
<?php 
$msg = '';$error = array(); 
$arr_Exceptions = array(); 
$counters = array();
$msg_info=array();
$csvFile = "IDX_insurance table update_05_2019.csv";
$fp = fopen($csvFile,"r");
echo '<table style="table-collapse:collapse; border:1px solid #ccc;">
	<tr><th>Invision Plan Code</th><th>Invision Plan Description</th><th>IDX Plan Description</th><th>IDX FSC</th></tr>';
$data = fgetcsv($fp);
while($data = fgetcsv($fp)){
	$inv_plan_code		= ucwords(trim($data[0]));
	$inv_plan_desc		= ucwords(trim($data[1]));
	$idx_plan_desc		= ucwords(trim($data[2]));
	$idx_plan_code		= ucwords(trim($data[3]));

	$q2="SELECT * FROM idx_invision_rco WHERE LOWER(invision_plan_code)='".strtolower($inv_plan_code)."' AND status='0'";// AND  LOWER(invision_plan_description)='".addslashes(strtolower($inv_plan_desc))."' AND  LOWER(IDX_description)='".strtolower($idx_plan_desc)."' AND  LOWER(IDX_FSC)='".addslashes(strtolower($idx_plan_code))."'";
	$res2=imw_query($q2);
	if($res2){
		$foundRows = imw_num_rows($res2);
		if($foundRows==0){
			$inv_plan_desc		= addslashes($inv_plan_desc);
			$idx_plan_desc		= addslashes($idx_plan_desc);
			$q_ins = "INSERT INTO idx_invision_rco (invision_plan_code,invision_plan_description,IDX_description,IDX_FSC) VALUES ('$inv_plan_code','$inv_plan_desc','$idx_plan_desc','$idx_plan_code')";
			$res_ins = imw_query($q_ins);
			if(!$res_ins) echo imw_error().'<hr>';
			else $counters['inserted'] += 1;
			echo "<tr style='color:#ff0000; font-weight:bold;'>
					<td>$inv_plan_code</td>
					<td>$inv_plan_desc</td>
					<td>$idx_plan_desc</td>
					<td>$idx_plan_code</td>
				  </tr>";
				  
			$counters['notfound'] = $counters['notfound']+1;			
		}else{
			//found record. matching exactly.
			while($rs2 = imw_fetch_assoc($res2)){
				$db_record_id			= $rs2['id'];
				$db_inv_plan_code		= $rs2['invision_plan_code'];
				$db_inv_plan_desc		= $rs2['invision_plan_description'];
				$db_idx_plan_desc		= $rs2['IDX_description'];
				$db_idx_plan_code		= $rs2['IDX_FSC'];
				if((strtolower($inv_plan_desc) != strtolower($db_inv_plan_desc)) || (strtolower($idx_plan_desc) != strtolower($db_idx_plan_desc)) || (strtolower($idx_plan_code) != strtolower($db_idx_plan_code))){
					/****MARK THIS SEMI-UNMATCHED RECORD AS IN-ACTIVE****/
					imw_query("UPDATE idx_invision_rco SET status='1' WHERE id='".$db_record_id."'");
					
			echo "<tr style='color:#0000ff; font-weight:bold;'>
					<td>$inv_plan_code</td>
					<td>$inv_plan_desc</td>
					<td>$idx_plan_desc</td>
					<td>$idx_plan_code</td>
				  </tr>
				  <tr style='color:#fff; background-color:#666; font-weight:bold;'>
					<td>$db_inv_plan_code</td>
					<td>$db_inv_plan_desc</td>
					<td>$db_idx_plan_desc</td>
					<td>$db_idx_plan_code</td>
				  <tr/>
				  <tr><td colspan=4 style='height:10px;'></td></tr>";
				  
					$counters['manual_check'] = $counters['manual_check']+1;
				}else{
				/*
				echo "<tr style='color:#000;'>
					<td>$inv_plan_code</td>
					<td>$inv_plan_desc</td>
					<td>$idx_plan_desc</td>
					<td>$idx_plan_code</td>
				  <tr>";*/
					$counters['found'] = $counters['found']+1;	
				}
			}
		}

	}else{
		$error[] = imw_error();	
	}
}
echo "</table>";
//pre($counters);

?>
<br><br>
<b>INSERTED RECORD COUNT: </b><?php echo $counters['inserted'];?><br>
<b>Already Existing: </b><?php echo $counters['found'];?><br>
<!--<b>Total Not Found: </b><?php echo $counters['notfound'];?><br>-->
<b>Marked In-active: </b><?php echo $counters['manual_check'];?><br>

</body>
</html>