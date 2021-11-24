<?php
ob_end_clean();
ob_start();
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$_SESSION['callFrom']="medicationTab";
$pid = $_SESSION['patient'];
$displayRecord='none';
?>
<style>
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
		font-size:12px;
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
<table width="100%" border="0" cellspacing="2" cellpadding="5" class='ContentShown' bgcolor="#ECF1EA" style="display:<?php echo $displayRecord;?>; ">
	<tr>
		<td colspan="3">
		<?php
			$query = "select * from lists where pid='$pid' and (type='1' or type='4') AND allergy_status='Active' ORDER BY id";
			$sql = imw_query($query);
			$num_rows=imw_num_rows($sql);				
			for($i=1;$i<=$num_rows;$i++)
			{
				$row = imw_fetch_array($sql);
				if($row != false){
                    if($row["referredby"]) {
                        $query1 = "SELECT CONCAT_WS(', ', lname, fname ) AS ref_by_name, id FROM users WHERE id = '".$row["referredby"]."' ";
                        $sql1 = imw_query($query1);
                        $referredby = imw_fetch_assoc($sql1);
                    }
					$tmpz = "md_occular".$i;
					$$tmpz = $row["type"];		
					$tmpz = "md_medication".$i;
					$$tmpz = stripslashes($row["title"]);
					$tmpz = "md_dosage".$i;
					$$tmpz = $row["destination"];		
					$tmpz = "md_prescribedby".$i;
					$$tmpz = $referredby["ref_by_name"];		
					$tmpz = "md_begindate".$i;
					$$tmpz = $row["begdate"];		
					$tmpz = "md_enddate".$i;
					$$tmpz = $row["enddate"];		
					$tmpz = "md_comments".$i;
					$$tmpz = $row["comments"];					
					$tmpz = "med_id".$i;
					$$tmpz = $row["id"];

						$tmpz = "md_qty".$i;
						$$tmpz = $row["qty"];

						$tmpz = "md_sig".$i;
						$$tmpz = $row["sig"];

						$tmpz = "md_refills".$i;
						$$tmpz = $row["refills"];
				}
			}
		?>	
		 <table width="100%" border="0" cellspacing="4" cellpadding="4" class='ContentShown tblBg' onClick="changeBackground('medication_table');" id="medication_table">
			<tr valign="top" height="30" bgcolor="#000000">
				<td align="center" colspan="9" width="90" class="text_18b text_orangeb">Medication</td>
			</tr>
            <tr valign="top" height="30">
                <td align="center" colspan="9" class="text_10b lightBlue"><table border="0" cellpadding="0" cellspacing="2">
            <tr>
                <td class="text_10b">ID: <?php echo $pid; ?></td><td class="text_10b"></td>
                <td class="text_10b"><?php space(5); if($full_name!=""){ ?> Name: <?php echo $full_name; }?></td>
                <td class="text_10b"><?php space(5); if($dob!=""){?> DOB: <?php echo $dob; }?></td>
                <td class="text_10b"><?php space(5); if($address!=""){?> Address: <?php echo $address; }?></td>
                <td class="text_10b"><?php space(5); if($phone_of_patient!=""){?> Phone: <?php echo $phone_of_patient; }?></td>
            </tr>
        </table></td>
            </tr>
			<tr>
				<td width="70" class="text_10b midBlue" align="left">Ocular</td>
				<td width="200" class="text_10b midBlue" align="left">Name</td>
				<td width="160" class="text_10b midBlue" align="left">Dosage</td>
				<td width="70" class="text_10b midBlue" align="left">Sig.</td>
				<td width="70" class="text_10b midBlue" align="left">Qty</td>
				<td width="70" class="text_10b midBlue" align="left">Refills</td>
				
				<!-- <td width="140" class="text_10b midBlue" align="left">Strength</td> --><!-- Dosage</td>-->
				<td width="110" class="text_10b midBlue" align="left">Prescribed By</td>
				<td width="120" class="text_10b midBlue" align="left">Begin Date</td>
				<td width="120" class="text_10b midBlue" align="left">EndDate</td>
			</tr> 
			<!-- code to show first five-->			
			<?php
				$lm = 5;
				$cl = 0;
				for($i=1;$i<=$lm;$i++){		
					if($cl%2 == 0){
						$class = 'bgcolor';
					}
					else{
						$class = '';
					}
					$md_occular = "md_occular".$i;
					$md_medication = "md_medication".$i;					
					$md_dosage = "md_dosage".$i;
					$md_prescribedby = "md_prescribedby".$i;
					$md_begindate = "md_begindate".$i;
					$md_enddate = "md_enddate".$i;
					$md_comments = "md_comments".$i;
					$med_id = "med_id".$i;
					
					$md_qty = "md_qty".$i;
					$md_sig = "md_sig".$i;
					$md_refills = "md_refills".$i;
						
					$onclick1 = ( $i != $lm ) ? "" : "<img id=\"icn_$i\" src=\"../../images/add_medical_history.gif\" alt=\"Add More\"  onClick=\"showNext('$i');\" >";
			
					if(trim($$md_occular) || trim($$md_medication) || trim($$md_dosage) || trim($$md_prescribedby) || ($$md_begindate!="" && $$md_begindate!="0000-00-00") || ($$md_enddate!="" && $$md_enddate!="0000-00-00") || trim($$md_comments)) {
					?>
					
						<tr height="30" class="<?php echo $class;?>">
							<td class="text_10b lightBlue"><?php echo ($$md_occular == "4")? "Yes" : ""; ?></td>
							<td class="text_10b lightBlue"><?php echo $$md_medication;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_dosage; ?></td>
							<td class="text_10b lightBlue"><?php echo $$md_sig;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_qty;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_refills;?></td>
							<!-- <td class="text_10b lightBlue"><?php //echo $$md_dosage;?></td> -->
							<td class="text_10b lightBlue"><?php echo $$md_prescribedby;?></td>
							<td class="text_10b lightBlue"><?php echo ($$md_begindate!="" && $$md_begindate!="0000-00-00")? get_date_format($$md_begindate):"";?></td>
							<td class="text_10b lightBlue"><?php echo ($$md_enddate!="" && $$md_enddate!="0000-00-00")? get_date_format($$md_enddate):"";?></td>
						</tr>
					<?php
					}	
		$cl++ ; }
			?>			
			<!-- End Code To show Next Five-->
			
			<!-- code to show next five-->			
			<?php
				$cop = 5;
				$lm = 25;
				$show_dummy=true;
				$col = 1;
				for($i=6;$i<=$lm;$i++){
					if($col%2 == 0){
						$class = 'bgcolor';
					}
					else{
						$class = '';
					}
					$md_occular = "md_occular".$i;
					$md_medication = "md_medication".$i;					
					$md_dosage = "md_dosage".$i;
					$md_prescribedby = "md_prescribedby".$i;
					$md_begindate = "md_begindate".$i;
					$md_enddate = "md_enddate".$i;
					$md_comments = "md_comments".$i;
					$med_id = "med_id".$i;
					
						$md_qty = "md_qty".$i;
						$md_sig = "md_sig".$i;
						$md_refills = "md_refills".$i;
						
					$onclick = ( $i < $lm ) ? "onClick=\"showNext('$i');\"" : "";
					if($$med_id != ""){ 
						$disp = "block";
						$cop++;
					}
					else{
					    $disp = "none";
					   if($show_dummy == true){
					   		$disp = "block";
							$cop++;
							$show_dummy = false;
						}
					}
					if(trim($$md_occular) || trim($$md_medication) || trim($$md_dosage) || trim($$md_prescribedby) || ($$md_begindate!="" && $$md_begindate!="0000-00-00") || ($$md_enddate!="" && $$md_enddate!="0000-00-00") || trim($$md_comments)) {
			?>
						<tr height="30" class="<?php echo $class;?>">
							<td class="text_10b lightBlue"><?php echo ($$md_occular == "4")? "Yes" : ""; ?></td>
							<td class="text_10b lightBlue"><?php echo $$md_medication;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_dosage; ?></td>
							<td class="text_10b lightBlue"><?php echo $$md_sig;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_qty;?></td>
							<td class="text_10b lightBlue"><?php echo $$md_refills;?></td>
							<!-- <td class="text_10b lightBlue"><?php //echo $$md_dosage;?></td> -->
							<td class="text_10b lightBlue"><?php echo $$md_prescribedby;?></td>
							<td class="text_10b lightBlue"><?php echo ($$md_begindate!="" && $$md_begindate!="0000-00-00")? get_date_format($$md_begindate):"";?></td>
							<td class="text_10b lightBlue"><?php echo ($$md_enddate!="" && $$md_enddate!="0000-00-00")? get_date_format($$md_enddate):"";?></td>
							<!--<td class="text_10b lightBlue"><?php echo $$md_comments;?></td>-->
						</tr>
			<?php				
					}
		$col++ ; }
				
			?>			
		    </table>		 
		</td>
	</tr>
</table>	
</page>

<?php
$headDataALL = ob_get_clean();
if(trim($headDataALL) != ""){
	$print_file_name = "medication_report_".$_SESSION["authId"];
	$file_path = write_html($headDataALL);
	?>
	<html>	
		<title>Print Medication</title>
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