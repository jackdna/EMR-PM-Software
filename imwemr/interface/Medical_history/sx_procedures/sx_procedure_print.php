<?php
ob_end_clean();
ob_start();
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
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
	<table style="width: 98%; display:<?php echo $displayRecord;?>;">
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
<table width="100%" border="0" cellspacing="2" cellpadding="5" style="display:<?php echo $displayRecord;?>; ">
	<tr>
		<td>
			<?php
			$query = "select type,title,sites,referredby,if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
								if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
													if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
													if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
													date_format(begdate,'".get_sql_date_format()."')
													))))as begdate1,enddate,comments,id from lists where pid=$pid and (type='5' or type='6') AND allergy_status='Active' ORDER BY begdate Desc, id Desc ";
			$sql = imw_query($query);
			$cnt = imw_num_rows($sql);													
			
			$i = 0;
			while( $row = imw_fetch_assoc($sql) ) 
			{
				$i++;
				{
					$tmpz = "sg_occular".$i;
					$$tmpz = $row["type"];		
					$tmpz = "sg_title".$i;
					$$tmpz = stripslashes($row["title"]);	
					$tmpz = "sg_site".$i;
					$$tmpz = $row["sites"];					
					$tmpz = "sg_referredby".$i;
					$$tmpz = stripslashes($row["referredby"]);		
					$tmpz = "sg_begindate".$i;
					$$tmpz = $row["begdate1"];		
					$tmpz = "sg_enddate".$i;
					$$tmpz = $row["enddate"];		
					$tmpz = "sg_comments".$i;
					$$tmpz = stripslashes($row["comments"]);					
					$tmpz = "sg_id".$i;
					$$tmpz = $row["id"];
				}
			}
			?>
			<table  width="100%"  border="0" cellspacing="4" cellpadding="4" class='ContentShown tblBg' onClick="changeBackground('surgery_table');" id="surgery_table" >
				<tr valign="top" height="30" bgcolor="#000000">
					<td align="center" colspan="6" width="90" class="text_18b text_orangeb">Surgeries</td>
				</tr>
        <tr valign="top" height="30">
        	<td align="center" colspan="6" class="text_10b lightBlue">
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
                
				<tr>
					<td  class="text_10b midBlue" style=" width:50px">Ocular</td>
					<td  class="text_10b midBlue" style=" width:250px">Name</td>
          <td class="text_10b midBlue" style=" width:50px">Site</td>
					<td  class="text_10b midBlue" style=" width:100px">Date of Surgery</td>
					<td  class="text_10b midBlue" style=" width:200px">Physician</td>
					<td  class="text_10b midBlue" style=" width:300px">Comments</td>
				</tr> 
				<!-- code to show first five-->			
				<?php
					$lm = 5;
					$cl = 0;
					for($i=1;$i<=$lm;$i++)
					{				
						if($cl%2 == 0){
							$class = 'bgcolor';
						}
						else{
							$class = '';
						}	
						$sg_occular = "sg_occular".$i;
						$sg_title = "sg_title".$i;	
						$sg_site = "sg_site".$i;					
						$sg_begindate = "sg_begindate".$i;
						$sg_enddate = "sg_enddate".$i;
						$sg_referredby = "sg_referredby".$i;
						$sg_comments = "sg_comments".$i;				
						$sg_id = "sg_id".$i;
						$onclick1 = ( $i != $lm ) ? "" : "<img id=\"icnsg_$i\" src=\"../../images/add_medical_history.gif\" alt=\"Add More\"  onClick=\"showNextSurgery('$i');\" >";
						
						if(trim($$sg_occular) || trim($$sg_title) || trim($$sg_site) || ($$sg_begindate!="" && $$sg_begindate!="0000-00-00") || trim($$sg_referredby) || trim($$sg_comments)) {
				?>
							<tr style="display:<?php echo $disp;?>;" id="tblsg_<?php echo $i;?>" class="<?php echo $class;?>">
								<td class="text_10b lightBlue" style=" width:50px; vertical-align:top;"><?php echo ($$sg_occular == "6")? "Yes" : ""; ?></td>
								<td class="text_10b lightBlue" style=" width:250px; vertical-align:top;"><?php echo $$sg_title;?></td>
                                <td class="text_10b lightBlue" style=" width:50px; vertical-align:top;"><?php echo ($$sg_site == 3)?"OU":(($$sg_site == 2)?"OD":(($$sg_site == 1)?"OS":(($$sg_site == 4)?"PO":''))) ;?></td>					
								<td class="text_10b lightBlue" style=" width:100px; vertical-align:top;"><?php echo ($$sg_begindate!="" && get_number($$sg_begindate)!="0")? $$sg_begindate:"";?></td>
								<td class="text_10b lightBlue" style=" width:200px; vertical-align:top;"><?php echo $$sg_referredby;?></td>
								<td class="text_10b lightBlue" style=" width:300px; vertical-align:top;"><?php echo $$sg_comments;?></td>
							</tr>
				<?php
						}
			$cl++; }
				?>			
				<!-- End Code To show Next Five-->
				<!-- code to show next five-->			
				<?php
					$copSug = 5;
					$lm = 25;
					$show_dummysg=true;
					$col = 1;
					for($i=6;$i<=$lm;$i++)
					{					
						if($col%2 == 0){
							$class = 'bgcolor';
						}
						else{
							$class = '';
						}
						$sg_occular = "sg_occular".$i;
						$sg_title = "sg_title".$i;		
						$sg_site = "sg_site".$i;				
						$sg_begindate = "sg_begindate".$i;
						$sg_enddate = "sg_enddate".$i;
						$sg_referredby = "sg_referredby".$i;
						$sg_comments = "sg_comments".$i;				
						$sg_id = "sg_id".$i;
						$onclick = ( $i < $lm ) ? "onClick=\"showNextSurgery('$i');\"" : "";
						
						if($$sg_id != "")
						{ 
							
							$disp = "block";
							$copSug++;
						}
						else
						{
							$disp = "none";
							if($show_dummysg == true)
						   {
								$disp = "block";
								$copSug++;
								$show_dummysg = false;
						   } 	
						}
						if(trim($$sg_occular) || trim($$sg_title) || trim($$sg_site) || ($$sg_begindate!="" && $$sg_begindate!="0000-00-00") || trim($$sg_referredby) || trim($$sg_comments)) {
				?>
                            <tr style="display:<?php echo $disp;?>;" id="tblsg_<?php echo $i;?>" class="<?php echo $class;?>">
								<td class="text_10b lightBlue" style=" width:50px; vertical-align:top;"><?php echo ($$sg_occular == "6")? "Yes" : ""; ?></td>
								<td class="text_10b lightBlue" style=" width:250px; vertical-align:top;"><?php echo $$sg_title;?></td>
                                <td class="text_10b lightBlue" style=" width:50px; vertical-align:top;"><?php echo ($$sg_site == 3)?"OU":(($$sg_site == 2)?"OD":(($$sg_site == 1)?"OS":(($$sg_site == 4)?"PO":''))) ;?></td>					
								<td class="text_10b lightBlue" style=" width:100px; vertical-align:top;"><?php echo ($$sg_begindate!="" && $$sg_begindate!="0000-00-00")? $$sg_begindate:"";?></td>
								<td class="text_10b lightBlue" style=" width:200px; vertical-align:top;"><?php echo $$sg_referredby;?></td>
								<td class="text_10b lightBlue" style=" width:300px; vertical-align:top;"><?php echo $$sg_comments;?></td>
							</tr>
				<?php				
						}
			$col++; }
					
				?>			
			</table>	
		</td>
	</tr>
</table>	
</page>

<?php
$headDataALL = ob_get_clean();
if(trim($headDataALL) != ""){
	$print_file_name = "sx_procedure_report_".$_SESSION["authId"];
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