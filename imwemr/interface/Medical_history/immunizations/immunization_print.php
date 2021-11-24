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
$sql = "select * from immunizations  where patient_id='".$pid."' and status='Given'";
$rez =@imw_query($sql);				
$num_rows=@imw_num_rows($rez);
$displayRecord='none';				
?>
<page backtop="0mm" backbottom="0mm">			
<page_footer>
	<table style="width: 100%; display:<?php echo $displayRecord;?>;">
		<tr>
			<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
		</tr>
	</table>
</page_footer>
<?php
	$sql_select = "
		SELECT CONCAT_WS(', ', lname, fname ) AS full_name, Date_Format(dob,'%W,  %M %d, %Y') as dob,
		CONCAT_WS(', ', street, street2, city, state ) AS address,
		phone_home, phone_biz, phone_contact, phone_cell
		FROM  `patient_data` 
		WHERE id = '".$pid."' 
	";
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
	$address = $sql_array["address"];
	function space($num)
	{
		for($i=0;$i<$num;$i++)
		{
			echo "&nbsp;";
		}
	}
?>
<table width="100%" style="width: 100%; display:<?php echo $displayRecord;?>;" border="0" cellspacing="4" cellpadding="4" class='ContentShown tblBg'>	
	<tr valign="top" height="30" bgcolor="#000000">
		<td align="center" colspan="12"  class="text_18b text_orangeb">Immunizations</td>
	</tr>
	<tr valign="top" style="display:block;" id="tblIMU_<?php echo $i;?>" class="">
		<td class="text_10b lightBlue" colspan="4">
			<table border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td class="text_10b">ID: <?php echo $pid; ?></td><td class="text_10b"></td>
					<td class="text_10b"><?php space(5); if($full_name!=""){ ?> Name: <?php echo $full_name; }?></td>
					<td class="text_10b"><?php space(5); if($dob!=""){?> DOB: <?php echo $dob; }?></td>
					<td class="text_10b"><?php space(5); if($address!=""){?> Address: <?php echo $address; }?></td>
					<td class="text_10b"><?php space(5); if($phone_of_patient!=""){?> Phone: <?php echo $phone_of_patient; }?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr valign="top">
		<td class="text_10b midBlue" width="260">Immunizations</td>
		<td class="text_10b midBlue" width="260">Manufacturer Info</td>
		<td class="text_10b midBlue" width="260">Administered</td>
		<td nowrap class="text_10b midBlue" width="260">Reactions & Comments</td>
   	</tr>
		
	<?php
		$cl = 0;
		for($i=1;$i<=$num_rows;$i++) {
			$checkImmunizations="disabled";
			$row = @imw_fetch_array($rez);					
			
			$tmpz = "administered_by_id".$i;
			$$tmpz = $row["administered_by_id"];
			
			$tmpz = "imnzn_id".$i;//foregin key Id From Table immunizatio_admin
			$$tmpz = $row["imnzn_id"];	
			
			$tmpz = "immzn_dose_id".$i;
			$$tmpz = $row["immzn_dose_id"];
			
			$tmpz = "manufacturer".$i;
			$$tmpz = $row["manufacturer"];	
				
			$tmpz = "immunization_id".$i;
			$$tmpz = $row["immunization_id"];								
				
			$tmpz = "administered_date".$i;
			$$tmpz = $row["administered_date"];		
			
			$tmpz = "immzn_note".$i;
			$$tmpz = $row["note"];
			
			$tmpz = "lot_number".$i;
			$$tmpz = $row["lot_number"];		
			
			$tmpz = "imu_autoid".$i;//primary key ID
			$$tmpz = $row["id"];
			
			$tmpz = "immzn_type".$i;
			$$tmpz = $row["immzn_type"];
			
			$tmpz = "immzn_type".$i;
			$$tmpz = $row["immzn_type"];
			 
			$tmpz = "immzn_dose".$i;
			$$tmpz = $row["immzn_dose"];
			
			$tmpz = "immzn_route_site".$i;
			$$tmpz = $row["immzn_route_site"];
			
			$tmpz = "expiration_date".$i;
			$$tmpz = $row["expiration_date"];
			
			$tmpz = "consent_date".$i;
			$$tmpz = $row["consent_date"];
			
			$tmpz = "adverse_reaction".$i;
			$$tmpz = $row["adverse_reaction"];				
						
			
			$administered_by_id="administered_by_id".$i;
			$immunization_id = "immunization_id".$i;					
			$expiration_date = "expiration_date".$i;
			$administered_date = "administered_date".$i;
			$consent_date = "consent_date".$i;
			$manufacturer = "manufacturer".$i;
			$lot_number = "lot_number".$i;
			$immzn_note = "immzn_note".$i;
			$adverse_reaction = "adverse_reaction".$i;
			$immzn_route_site = "immzn_route_site".$i;
			$immzn_type = "immzn_type".$i;
			$immzn_dose = "immzn_dose".$i;
			$imu_autoid = "imu_autoid".$i;
			$imnzn_id  = "imnzn_id".$i;
			$immzn_dose_id  = "immzn_dose_id".$i;
	?>
	<tr valign="top" style="display:block;" id="tblIMU_<?php echo $i;?>" class="">
		<td class="text_10b lightBlue">
			<table border="0" cellpadding="0" cellspacing="0">
				<?php if($$immunization_id) {?><tr><td class="text_10b">Name:</td><td class="text_10"><?php echo $$immunization_id;?></td></tr><?php }?>
				<?php if($$immzn_type) 		{?><tr><td class="text_10b">Type:</td><td class="text_10"><?php echo $$immzn_type;?></td></tr><?php }?>
				<?php if($$immzn_dose) 		{?><tr><td class="text_10b">Dose:</td><td class="text_10"><?php echo $$immzn_dose;?></td></tr><?php }?>
				<?php if($$immzn_route_site){?><tr><td class="text_10b">Route & Site:</td><td class="text_10"><?php echo $$immzn_route_site;?></td></tr><?php }?>
			</table>
		</td>
		<td class="text_10b lightBlue">
			<table border="0" cellpadding="0" cellspacing="0">
				<?php if($$manufacturer){?><tr><td class="text_10b">Name:</td><td class="text_10"><?php echo $$manufacturer;?></td></tr><?php }?>
				<?php if($$lot_number) 	{?><tr><td class="text_10b">Lot#:</td><td class="text_10"><?php echo $$lot_number;?></td></tr><?php }?>
				<?php if($$expiration_date!="" && $$expiration_date!="0000-00-00") {?><tr><td class="text_10b">Expiry Date:</td><td class="text_10"><?php echo ($$expiration_date!="" && $$expiration_date!="0000-00-00")? get_date_format($$expiration_date):"";?></td></tr><?php }?>
			</table>
		</td>
		<td class="text_10b lightBlue">
			<table border="0" cellpadding="0" cellspacing="0">
				<?php if($$administered_by_id){?>
				<tr><td class="text_10b">By:</td>
					<td class="text_10">
						<?php
						//START CODE TO GET ADMINISTRATOR FULL NAME BY ID
						$administeredByFullName='';
						$sqlFullNameQry = "select id, concat(fname,', ',lname) as full_name " .
						"from users " .
						"WHERE id='".$$administered_by_id."'";
						$sqlFullNameRes = imw_query($sqlFullNameQry);
						if(imw_num_rows($sqlFullNameRes)>0) {
							$sqlFullNameRow = imw_fetch_array($sqlFullNameRes);
							$administeredByFullName = $sqlFullNameRow['full_name'];
						}
						echo $administeredByFullName;
						//END CODE TO GET ADMINISTRATOR FULL NAME BY ID
						?>
					</td>
				</tr>
				<?php
				}
				?>
				<?php if($$administered_date!="" && $$administered_date!="0000-00-00"){?><tr><td class="text_10b">Date:</td><td class="text_10"><?php echo ($$administered_date!="" && $$administered_date!="0000-00-00")? get_date_format($$administered_date):"";?></td></tr><?php }?>
			</table>
		</td>
		<td class="text_10b lightBlue">
			<table border="0" cellpadding="0" cellspacing="0">
				<?php if($$consent_date!="" && $$consent_date!="0000-00-00"){?><tr><td class="text_10b">Consent Date:</td><td class="text_10"><?php echo ($$consent_date!="" && $$consent_date!="0000-00-00")? get_date_format($$consent_date):"";?></td></tr><?php }?>
				<?php if($$adverse_reaction){?><tr><td class="text_10b">Reaction:</td><td class="text_10"><?php echo $$adverse_reaction;?></td></tr><?php }?>
				<?php if($$immzn_note){?><tr><td class="text_10b">Comments:</td><td class="text_10"><?php echo $$immzn_note;?></td></tr><?php }?>
			</table>
		</td>
	</tr>
	<?php
	}
	?>
</table>
</page>

<?php
$headDataALL = ob_get_contents();

if(trim($headDataALL) != ""){
	$print_file_name = "immunization_report_".$_SESSION["authId"];
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