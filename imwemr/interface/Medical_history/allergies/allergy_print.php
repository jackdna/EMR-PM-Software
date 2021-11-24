<?php
ob_start();
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
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

<table width="100%" border="0" cellspacing="2" cellpadding="5"  bgcolor="#ECF1EA" style="display:<?php echo $displayRecord;?>; ">
	<tr>
		<td colspan="3">
			<?php
				$sql = "select *, DATE_FORMAT(begdate,'".get_sql_date_format()."') as begdateNew from lists where pid=$pid and type in(3,7) AND allergy_status='Active' ORDER BY id";
				if($_REQUEST["type"]!=""){
					$sql = "select * from lists where pid=$pid and type=".$_REQUEST["type"]." AND allergy_status='Active' ORDER BY id ";
				}
				$rez =@imw_query($sql);				
				$num_rows=@imw_num_rows($rez);				
				for($i=1;$i<=$num_rows;$i++)
				{
					$row = @imw_fetch_array($rez);					
					$ag_obj_id = "ag_obj_id".$i;
					$$ag_obj_id = $row["ag_obj_id"];
					$ag_occular_drug = "ag_occular_drug".$i;
					$$ag_occular_drug = $row["ag_occular_drug"];
					$tmpz = "ag_occular".$i;
					$$tmpz = $row["type"];		
					$tmpz = "ag_title".$i;
					$$tmpz = $row["title"];								
					$tmpz = "ag_begindate".$i;
					$$tmpz = $row["begdateNew"];		
					$tmpz = "ag_enddate".$i;
					$$tmpz = $row["enddate"];		
					$tmpz = "ag_comments".$i;
					$$tmpz = $row["comments"];
					$tmpz = "ag_acute".$i;
					$$tmpz = $row["acute"];
					$tmpz = "ag_chronic".$i;
					$$tmpz = $row["chronic"];
					$tmpz = "ag_reactions".$i;
					$$tmpz = $row["reactions"];
					$tmpz = "ag_id".$i;
					$$tmpz = $row["id"];
					$tmpz = "del_allergy_status".$i;
					$$tmpz = $row["del_allergy_status"];
				}
			?>
			<table width="99%" border="0" cellspacing="4" cellpadding="4"  >
				<tr valign="top" height="30" bgcolor="#000000">
					<td align="center" colspan="7" width="90" class="text_18b text_orangeb">Allergies</td>
				</tr> 
				<tr valign="top" height="30" bgcolor="#000000">
					<td width="110" class="text_10b midBlue">Drug</td>
					<td width="280" class="text_10b midBlue">Name</td>
					<td width="140" class="text_10b midBlue">Begin Date</td>
					<!-- <td width="120" class="text_10b midBlue">End Date</td> -->
			<!--	<td width="140" class="text_10b midBlue">Acute</td>
					<td width="140" class="text_10b midBlue">Chronic</td>   -->
					<td width="470" class="text_10b midBlue">Reactions / Comments</td>
				</tr> 
				<?php
					$lm = 5;
					$cl = 0;
					$bgTrColor="#FFFFFF";
					for($i=1;$i<=$lm;$i++)
					{		
						if($cl%2 == 0){
							$class = 'bgcolor';
							$bgTrColor = "#F3F8F2";
						}
						else{
							$class = '';
						}
						$ag_obj_id = "ag_obj_id".$i;
						$ag_occular_drug = "ag_occular_drug".$i;
						$ag_occular = "ag_occular".$i;
						$ag_title = "ag_title".$i;					
						$ag_begindate = "ag_begindate".$i;
						$ag_enddate = "ag_enddate".$i;
						$ag_acute = "ag_acute".$i;
						$ag_chronic = "ag_chronic".$i;
						$ag_reactions = "ag_reactions".$i;
						$ag_comments = "ag_comments".$i;				
						$ag_id = "ag_id".$i;
						$del_allergy_status = "del_allergy_status".$i;
						$onclick1 = ( $i != $lm ) ? "" : "<img id=\"icnag_$i\" src=\"../../images/add_medical_history.gif\" alt=\"Add More\"  onClick=\"showNextAllergy('$i');\" >";					
						
						//if($$ag_occular == "7"){
							//$$ag_occular_drug = 'fdbATDrugName';
						//}
						
				if(trim($$ag_reactions) || trim($$ag_occular_drug) || trim($$ag_title) || ($$ag_begindate!="" && $$ag_begindate!="0000-00-00") || ($$ag_enddate!="" && $$ag_enddate!="0000-00-00") || $$ag_acute == "acute" || $$ag_chronic == "chronic" || trim($$ag_comments)) {
				?>
				
					<?php
						if($$del_allergy_status != 0){
							$if_deleted = "style='text-decoration:line-through;color:#FF0000;'";
						}else{
							$if_deleted = "";
						}
					?>
						<tr valign="top" height="30" bgcolor="#000000">
							<td class="text_10 lightBlue" <?php echo($if_deleted); ?>>
								<?php
								if($$ag_occular_drug == 'fdbATDrugName') 			{ echo 'Drug';
								}else if($$ag_occular_drug == 'fdbATIngredient') 	{ echo 'Ingredient';
								}else if($$ag_occular_drug == 'fdbATAllergenGroup') { echo 'Allergen';
								}
								?>
							</td>
							<td class="text_9b lightBlue" <?php echo($if_deleted); ?> nowrap><?php echo $$ag_title;?></td>			
							<td class="text_10 lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_begindate;?></td>
				<?php /*    <td class="text_9b lightBlue" <?php  echo($if_deleted); ?>><?php echo ($$ag_acute == "acute") ? "Yes" : "";?></td>
							<td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo ($$ag_chronic == "chronic") ? "Yes" : "";?></td> <?php */ ?>
					<?php /* ?>		<td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_reactions; ?></td>   <?php */ ?>
                            <td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_comments." ".$$ag_reactions;?></td>
						</tr>
				<?php
				}	
	$cl++; }
					$copAg = 5;
					$lm = 25;
					$show_dummyag = true;
					$col = 1;
					for($i=6;$i<=$lm;$i++)
					{				
						if($col%2 == 0){
							$class = 'bgcolor';
							$bgTrColor = "#F3F8F2";
						}
						else{
							$class = '';
						}
						$ag_occular = "ag_occular".$i;
						$ag_title = "ag_title".$i;					
						$ag_begindate = "ag_begindate".$i;
						$ag_enddate = "ag_enddate".$i;
						$ag_acute = "ag_acute".$i;
						$ag_chronic = "ag_chronic".$i;
						$ag_reactions = "ag_reactions".$i;
						$ag_comments = "ag_comments".$i;				
						$ag_id = "ag_id".$i;
						$ag_occular_drug = "ag_occular_drug".$i;
						$del_allergy_status = "del_allergy_status".$i;
						
						$onclick = ( $i < $lm ) ? "onClick=\"showNextAllergy('$i');\"" : "";
						
						if($$ag_id != "")
						{ 
							
							$disp = "block";
							$copAg++;
						}
						else
						{
							$disp = "none";
						 if($show_dummyag == true)
						   {
								$disp = "block";
								$copAg++;
								$show_dummyag = false;
						   }
								
						}
						
						//if($$ag_occular == "7"){
							//$$ag_occular_drug = 'fdbATDrugName';
						//}

					if(trim($$ag_occular_drug) || trim($$ag_title) || ($$ag_begindate!="" && $$ag_begindate!="0000-00-00") || ($$ag_enddate!="" && $$ag_enddate!="0000-00-00") || $$ag_acute == "acute" || $$ag_chronic == "chronic" || trim($$ag_comments)) {
					?>
					<?php
						if($$del_allergy_status != 0){
							$if_deleted = "style='text-decoration:line-through;color:#FF0000;'";
						}else{
							$if_deleted = "";
						}
					?>
						<tr valign="top" height="30" bgcolor="#000000">
							<td class="text_10 lightBlue" <?php echo($if_deleted); ?>>
								<?php
								if($$ag_occular_drug == 'fdbATDrugName') { echo 'Drug';
								}else if($$ag_occular_drug == 'fdbATIngredient') { echo 'Ingredient';
								}if($$ag_occular_drug == 'fdbATAllergenGroup') { echo 'Allergen';
								}
								?>
							</td>
							<td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_title;?></td>
							<td class="text_10 lightBlue" <?php echo($if_deleted); ?>><?php echo ($$ag_begindate!="" && $$ag_begindate!="0000-00-00")? ($$ag_begindate):"";?></td>
							<!-- <td class="text_10 lightBlue" <?php echo($if_deleted); ?>><?php echo ($$ag_enddate!="" && $$ag_enddate!="0000-00-00")? ($$ag_enddate):"";?></td>
							 --><td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo ($$ag_acute == "acute") ? "Yes" : "";?></td>
							<td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo ($$ag_chronic == "chronic") ? "Yes" : "";?></td>
							<td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_reactions; ?></td>
                            <td class="text_9b lightBlue" <?php echo($if_deleted); ?>><?php echo $$ag_comments;?></td>
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
$headDataALL = ob_get_contents();
if(trim($headDataALL) != ""){
	$print_file_name = "allergy_report_".$_SESSION["authId"];
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
