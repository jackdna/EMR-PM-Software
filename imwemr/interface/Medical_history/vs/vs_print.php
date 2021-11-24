<?php
ob_start();
set_time_limit(300);
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$_SESSION['callFrom']="medicationTab";
$pid = $_SESSION['patient'];
?>
<style>
	.drsElement {
		position: absolute;
		
	}
	.drsMoveHandle {
		height: 20px;
	}
	.text_11 {
		font-family: Arial, Verdana,  Helvetica, sans-serif;
		font-size: 15px;
		font-weight: normal;
		color: #000000;
	}
	.text_11b {
		font-family: Arial, Verdana,  Helvetica, sans-serif;
		font-size: 15px;
		font-weight:bold;
		color: #000000;
	}
	.text_10 {
		font-family: Arial, Verdana, Helvetica, sans-serif;
		font-size: 14px;
		font-weight: normal;
		color: #000000;
	}
	.text_10b {
		font-family: Arial, Verdana, Helvetica, sans-serif;
		font-size: 14px;
		font-weight:bold;
		color: #000000;
	}
	.text_9b {
		font-family: Arial, Verdana, Helvetica, sans-serif;
		font-size: 13px;
		font-weight:bold;
		color: #000000;
	}
	.text_18 {
		font-family: Arial, Verdana,  Helvetica, sans-serif;
		font-size: 18px;
		font-weight:normal;
		color: #000000;
	}
	.text_18b {
		font-family: Arial, Verdana,  Helvetica, sans-serif;
		font-size: 18px;
		font-weight:bold;
		color: #000000;
	}
	
	.tb_heading{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
		color:#000000;
		background-color:#FE8944;
	}
	
	.lightBlue {
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#EAF4FD;
	}
	.midBlue {
		font-size:14px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#80AFEF;
	}
	.text_orangeb{
		font-weight:bold;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
		color:#CB6B43;
	}
	.lightGreen {
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#ECF1EA;
	}
	.lightorange {
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#CB6B43;
	}
			
</style>
<?php
$pid = $_SESSION['patient'];
$sql = "select date_vital,time_vital,id from vital_sign_master
  		where 
		patient_id='".$pid."' and status='0' order by date_vital desc ";
$rez =@imw_query($sql);				
$num_rows=@imw_num_rows($rez);
$displayRecord='none';				

$sql_select = "SELECT CONCAT_WS(', ', lname, fname ) AS full_name, Date_Format(dob,'%W,  %M %d, %Y') as dob,
	street, street2, city, state , postal_code,	phone_home, phone_biz, phone_contact, phone_cell
	FROM  `patient_data` WHERE id = '$pid' ";
$sql_result = imw_query($sql_select);
$sql_array = imw_fetch_array($sql_result);
$phone_of_patient = "";
if($sql_array["phone_home"]!="")
{
	$phone_of_patient = $sql_array["phone_home"];
}
else if($sql_array["phone_biz"]!="")
{
	$phone_of_patient = $sql_array["phone_biz"];
}
else if($sql_array["phone_contact"]!="")
{
	$phone_of_patient = $sql_array["phone_contact"];
}
else if($sql_array["phone_cell"]!="")
{
	$phone_of_patient = $sql_array["phone_cell"];
}
$full_name = $sql_array["full_name"];
$dob = $sql_array["dob"];

$address = $sql_array["street"];
if($sql_array["street2"] != ''){
	$address .= $sql_array["street2"];
}
$address .= $sql_array["city"].', ';
$address .= $sql_array["state"].' ';
$address .= $sql_array["postal_code"];

function space($num)
{
	for($i=0;$i<$num;$i++)
	{
		echo "&nbsp;";
	}
}
?>
<page backtop="0mm" backbottom="0mm">			

<table width="100%" style="width: 100%; display:<?php echo $displayRecord;?>;" border="0" cellspacing="4" cellpadding="4" class='ContentShown tblBg'>	
	<tr valign="top" height="30" bgcolor="#000000">
		<td align="center" colspan="12" class="text_18b text_orangeb">Vital Sign</td>
	</tr>
	<tr valign="top" style="display:<?php echo $displayRecord;?>;">
		<td class="text_10b lightBlue" colspan="10">
			<table border="0" cellpadding="1" cellspacing="1">
				<tr>
					<td class="text_10b" width="100">ID: <?php echo $pid; ?></td><td class="text_10b"></td>
					<td class="text_10b" width="250"><?php space(2); if($full_name!=""){ ?> Name: <?php echo $full_name; }?></td>
					<td class="text_10b" width="250"><?php space(2); if($dob!=""){?> DOB: <?php echo $dob; }?></td>
					<td class="text_10b" width="300"><?php space(2); if($address!=""){?> Address: <?php echo $address; }?></td>
					<td class="text_10b" width="150"><?php space(2); if($phone_of_patient!=""){?> Phone: <?php echo $phone_of_patient; }?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr valign="top">
		<td class="text_10b midBlue" width="60">Date</td>
		<td class="text_10b midBlue" width="100">B/P - Systolic</td>
		<td class="text_10b midBlue" width="100">B/P - Diastolic</td>
		<td class="text_10b midBlue" width="120">Pulse</td>
		<td class="text_10b midBlue" width="120">Respiration</td>
		<td class="text_10b midBlue" width="100">O2Sat</td>
		<td class="text_10b midBlue" width="80">Temp</td>
		<td class="text_10b midBlue" width="80">Height</td>
		<td class="text_10b midBlue" width="100">Weight</td>
		<td class="text_10b midBlue" width="140">BMI</td>
   </tr>
	<?php
		while($row=imw_fetch_array($rez)){
		$dat_final = get_date_format($row['date_vital']);
		$tim=$row['time_vital'];
		$mas_id=$row['id'];
	?>
	<tr valign="top">
	<td class="text_10b lightBlue"><?php echo $dat_final; ?></td>
	<?php	
		$pat_rec=imw_query("select range_vital,unit,range_status from vital_sign_patient 
								where vital_master_id='$mas_id'");
		$i=1;
		while($vt_rec=imw_fetch_array($pat_rec)){
			$unt="";
			if($vt_rec['range_vital']){
				$unt=' ('.$vt_rec['unit'].')';
			}
			if($i < 10 ){
			?>
				<td class="text_10b lightBlue"><?php echo $vt_rec['range_vital'].$unt;?></td>
			<?php
			}
			$i++;
		}
	?>
	  </tr>
	<?php	
		}
	?>
</table>
</page>
<?php
$headDataALL = ob_get_contents();

if(trim($headDataALL) != ""){
	$print_file_name = "chk_in_print_reciept_".$_SESSION["authId"];
	$file_path = write_html($headDataALL);
	?>
	<html>	
		<title>Print Immunization</title>
		<body>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
			<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
			<script type="text/javascript">
				var file_name = '<?php print $print_file_name; ?>';
				top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
				html_to_pdf('<?php echo $file_path; ?>','l',file_name);
			</script>
		</body>
	</html>
	<?php
}
else{
?>
	<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
			<td align="center">No Result.</td>
		</tr>
	</table>
<?php
}
?>