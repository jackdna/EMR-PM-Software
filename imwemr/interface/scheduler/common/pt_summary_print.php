<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/classes/complete_pt_record.class.php");
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");



$complete_pt_rec_obj = New CPR($_SESSION['patient']);
//------SET TEMP KEY CHECKBOX TO 1 IN CHECKIN SCREEN---------
imw_query("UPDATE patient_data SET temp_key_chk_datetime = '".date('Y-m-d H:i:s')."', temp_key_chk_val = '1', temp_key_chk_opr_id = '".$_SESSION['authId']."' WHERE id = '".$_SESSION['patient']."'");

function facility_provider($pro_id,$appt_date = ''){
	$return_pro_id=$pro_id;
	if($appt_date!=''){
		$appt_date="and sa_app_start_date='".$appt_date."'";
	}
	if($pro_id){
		$qry_users="select id from `users` where user_type='22' and id='".$pro_id."'";
		$res_users=imw_query($qry_users);
		  if(imw_num_rows($res_users)>0){
			  $qry_fac_type = "SELECT `facility_type_provider` FROM `schedule_appointments` WHERE sa_doctor_id='".$pro_id."' $appt_date ORDER BY id DESC Limit 0,1";
			  $res_fac_type = imw_query($qry_fac_type);
			  while($res_fac_type = imw_fetch_assoc($res_fac_type)){
					$pro_id = $res_fac_type['facility_type_provider'];
					$return_pro_id = $pro_id;
			  } 
		  }
	}
	return $return_pro_id;
}

function record_pt_summary_printed(){
	global $complete_pt_rec_obj;
	$schId = intval($_REQUEST['sch_id']);
	$pId = intval($_REQUEST['pid']);
	
	$sch_query = "SELECT sa_app_start_date FROM schedule_appointments WHERE id='$schId' AND sa_patient_id='$pId'";
	$sch_result = imw_query($sch_query);
	$sch_rs = imw_fetch_assoc($sch_result);
	$appt_date= $sch_rs['sa_app_start_date'];
	
	$clch_query = "SELECT form_id FROM chart_left_cc_history WHERE date_of_service='$appt_date' AND patient_id='$pId'";
	$clch_result = imw_query($clch_query);
	$clch_rs = imw_fetch_assoc($clch_result);
	$form_id= $clch_rs['form_id'];
	$complete_pt_rec_obj->setLogOfPtPrintedRec($pId,$form_id,$_SESSION['authId'],$appt_date,'iDoc','pdf');
}

$qry_patient_data = "select * from patient_data where id='".$_SESSION["pid"]."' limit 0,1";
$res_patient_data = imw_query($qry_patient_data);
while($row_patient_data = imw_fetch_array($res_patient_data)){
	$reslutPatientRow = $row_patient_data;
}

function getPhysicianName($id,$appt_date = ''){
	$id = facility_provider($id,$appt_date); //This function used for check facility type provider for front desk
	$user_detail = getUserDetails($id);
	$user_name = core_name_format($user_detail["lname"],$user_detail["fname"]);
//	$user_name = substr($user_detail["fname"],0,1).substr($user_detail["lname"],0,1);
	return $user_name;
}
$patientName = core_name_format($reslutPatientRow["lname"],$reslutPatientRow["fname"], $reslutPatientRow["mname"]);
$age = get_age($reslutPatientRow["DOB"]);
$date_of_birth = get_date_format($reslutPatientRow["DOB"]);

$qry = "select default_group from facility where facility_type = 1";
$facilityDetailRes = imw_query($qry);
if(imw_num_rows($facilityDetailRes)>0){
	$facilityDetail=imw_fetch_array($facilityDetailRes);
	$gro_id = $facilityDetail['default_group'];
	$qry = "select * from groups_new where gro_id = '$gro_id'";
	$groupDetails =@imw_fetch_array(imw_query($qry));
}
//Get User Name
$curr_user_detail = getUserDetails($_SESSION["authId"]);
$curr_user_name = substr($curr_user_detail["fname"],0,1).substr($curr_user_detail["lname"],0,1);;
$user_name = getPhysicianName($_SESSION["authId"]);
//Get Problem List
$qryGetProblemList = "SELECT *, Date_Format(onset_date,'".get_sql_date_format()."') as new_date, TIME_FORMAT(OnsetTime,'%h:%i %p') as new_OnsetTime FROM pt_problem_list
					where pt_id ='".$_REQUEST['pid']."' AND status = 'Active' ORDER BY onset_date, id";
$rsGetProblemList = imw_query($qryGetProblemList);

//Get Allergies
$getAllergies = "select type,title,begdate,acute,allergy_status,chronic,reactions,ag_occular_drug,comments,
			date_format(begdate,'".get_sql_date_format()."') as DateStart from lists where pid = '".$_REQUEST['pid']."' 
			and type in(3,7) and allergy_status = 'Active' order by id";
$rsGetAllergies = imw_query($getAllergies);

//Get medication List
$getMedication = "select type,title,sig,sites,allergy_status,referredby,destination,begdate,enddate,comments,med_comments,
			date_format(begdate,'".get_sql_date_format()."') as DateStart from lists where pid='".$_REQUEST['pid']."' and  
			allergy_status = 'Active' and type in (1,4) order by id";
$rsGetMedication = imw_query($getMedication);

//Get Detail Of Future Appointments of The Patient  sch_id
$qry_cur_appt = "select sa_app_end_date, sa_app_endtime from schedule_appointments where id = '".$_REQUEST['sch_id']."' limit 0,1";
$res_cur_appt = imw_query($qry_cur_appt);
$row_cur_appt = imw_fetch_assoc($res_cur_appt);

$qry_fur_appt = "select *, date_format(sa_app_start_date,'".get_sql_date_format()."') AS sa_app_start_date,sa_app_start_date as appt_date from schedule_appointments where ((sa_app_start_date = '".$row_cur_appt['sa_app_end_date']."' AND sa_app_starttime > '".$row_cur_appt['sa_app_endtime']."') OR (sa_app_start_date > '".$row_cur_appt['sa_app_end_date']."')) AND sa_patient_id='".$_SESSION["pid"]."' AND sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_start_date,sa_app_starttime  limit 0,3";
$res_fur_appt = imw_query($qry_fur_appt);

$qry_slot_procedure = "select id, proc from slot_procedures";
$res_slot_procedure = imw_query($qry_slot_procedure);
while($row_slot_procedure = imw_fetch_assoc($res_slot_procedure)){
	$arr_procedure[$row_slot_procedure["id"]] = $row_slot_procedure["proc"];
}
$qry_facility = "select id, name from facility";
$res_facility = imw_query($qry_facility);
while($row_facility = imw_fetch_assoc($res_facility)){
	$arr_ficility[$row_facility["id"]] = $row_facility["name"];
}

record_pt_summary_printed();
ob_start();	

?>
<page backtop="5mm" backbottom="5mm">

<style>
.text_b_w{
		font-size:14px;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:14px;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:14px;
	font-weight:bold;
	color:#000;
	background-color:#CCC;
	padding:3px 0px 3px 0px;
	vertical-align:middle;
}
.tb_headingHeader{
	font-size:14px;
	
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.text_lable{
		font-size:14px;
		
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:14px;
		
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:14px;
		
		color:#0000CC;
		font-weight:bold;
	}
.text_green{
		font-size:14px;
		
		color:#006600;
		font-weight:bold;
}
.imgCon{width:325px;height:auto;}
.border{
	border:1px solid #C0C0C0;
}
.bdrbtm{
	border-bottom:1px solid #C0C0C0;
	height:13px;	
	vertical-align:top;
	padding-top:2px;
	padding-left:3px;
}
.bdrtop{
	border-top:1px solid #C0C0C0;
	height:15px;
	vertical-align:top;	
}
.pdl5{
	padding-left:10px;
		
}
.bdrright{
	border-right:1px solid #C0C0C0;
}
</style>		

<page_footer>
<table style="width: 100%;">
	<tr>
		<td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td>
	</tr>
</table>
</page_footer>		
		<table style="width:750px;" cellpadding="0" cellspacing="0">
        	<tr>
	    	    <td class="tb_headingHeader" style="width:750px;">
        	        <table style="width:750px;" cellpadding="0" cellspacing="0" border="0">
        	            <tr>
        	                <td style="width:200px; text-align:left;"><?php if(!empty($patientName)){ print $patientName."-".$reslutPatientRow['id']; }?></td>
           	             <td style="width:200px; text-align:center;"><?php print $reslutPatientRow['sex'];print("&nbsp;($age)&nbsp;".$date_of_birth);?>&nbsp; </td>
           	             <td style="width:350px; text-align:right;">Created By:&nbsp;<?php echo $curr_user_name;?>&nbsp;on&nbsp;<?php echo get_date_format(date("Y-m-d")).' '.date("H:i:s");?></td>
           	         </tr>
                	</table>
            	</td>
             </tr>  
             <tr>
                    	<td>
                        	<table style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width:40%" align="left"> 
                                            <table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
                                                <tr>
                                                    <td style="width:100%" class="text_lable"><?php if(!empty($patientName)){ print $patientName."-".$reslutPatientRow['id']; }?> </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php print $reslutPatientRow['sex'];print("&nbsp;($age)&nbsp;".$date_of_birth);?>&nbsp; </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"> <?php  print $reslutPatientRow['street']."&nbsp;";?>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php  print $reslutPatientRow['street2']; ?>&nbsp; </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php print ($reslutPatientRow['city'])?$reslutPatientRow['city'].",":""; print "&nbsp;".$reslutPatientRow['state']."&nbsp;".$reslutPatientRow['postal_code']; ?>&nbsp; </td>
                                                </tr>
                                
                                            </table>
                                            
                                        </td>
                                            
                                    <?php echo('<TD style="width:20%"  valign="top">&nbsp;</TD>'); ?>
                                            <td style="width:40%" align="right">
                                            <table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
                                                <tr>
                                                    <td style="width:100%" class="text_lable"><?php print $groupDetails['name']; ?> </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php print ucwords($groupDetails['group_Address1']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php print ucwords($groupDetails['group_Address2']);?>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value"><?php print $groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip']; ?>  </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="width:100%" class="text_value">Ph.:&nbsp;<?php print $groupDetails['group_Telephone']; ?> </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%" class="text_value">Fax:&nbsp;<?php print $groupDetails['group_Fax']; ?> </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                        </td>
                    </tr> 
        </table>
		
               		 <?php
                 	  if (imw_num_rows($rsGetProblemList) > 0) {	
   				    ?>
                    <table style="width:750px;"  class="border" cellpadding="0" cellspacing="0">	
								<tr>
									<td style="color:#000; width:750px" colspan="3" class="tb_heading">Problem list:&nbsp;</td>				
								</tr>
								<tr>
									<td style="width:450px;" class="text_lable bdrbtm" nowrap valign="top">Patient Problem - Dx Code</td>
									<td style="width:150px;" class="text_lable bdrbtm" nowrap valign="top">Date Diagnosed</td>
                                    <td style="width:100px;" class="text_lable bdrbtm" nowrap valign="top">Status</td>
								</tr>
								<?php 	
								while($rowProblemList = imw_fetch_assoc($rsGetProblemList)){
								?>
									<tr>
										<td class="text_value bdrbtm" valign="top" style="width:450px;">
                                        <?php echo core_refine_user_input($rowProblemList["problem_name"]); ?>&nbsp;
										</td>			
										<td style="width:150px;" class="text_value bdrbtm" valign="top">
											<?php if($rowProblemList["new_date"]!="00-00-0000"){echo($rowProblemList["new_date"]);} ?>&nbsp;
										</td>
                                        <td style="width:100px;" class="text_value bdrbtm" valign="top">
											<?php echo ($rowProblemList["status"]); ?>&nbsp;
										</td>
									
									</tr>				
									<?php								
								}
								
								?>
                                 </table>
					<?php }	?>
                    <?php if (imw_num_rows($rsGetAllergies) > 0){ ?>
                    <table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:750px;" colspan="5" class="tb_heading">Allergies:</td>				
							</tr>
							<tr>
								<td style="width:100px;" class="text_lable bdrbtm" nowrap valign="top">Type</td>
								<td style="width:200px;" class="text_lable bdrbtm" nowrap valign="top">Name</td>		
								<td style="width:200px;" class="text_lable bdrbtm" nowrap valign="top">Reactions / Comments</td>
								<td style="width:100px;" class="text_lable bdrbtm" nowrap valign="top">Date Started</td>		
								<td style="width:100px;" class="text_lable bdrbtm" nowrap valign="top">Status</td>		
							</tr>
						<?php
						while($row = imw_fetch_assoc($rsGetAllergies)){
							$typeAllergy="";
							if($row["ag_occular_drug"] == 'fdbATDrugName'){
									$typeAllergy = 'Drug';
								}
								if($row["ag_occular_drug"] == 'fdbATIngredient'){
									$typeAllergy = 'Ingredient';
								}
								if($row["ag_occular_drug"] == 'fdbATAllergenGroup'){
									$typeAllergy = 'Allergen';
								}
							?>
								<tr>
									<td style="width:100px;" class="text_value bdrbtm" valign="top"><?php echo $typeAllergy; ?>&nbsp;</td>			
									<td style="width:200px;" class="text_value bdrbtm" valign="top"><?php echo $row["title"]; ?>&nbsp;</td>
									<td style="width:200px;" class="text_value bdrbtm" valign="top"><?php echo ucwords($row["comments"]); ?>&nbsp;</td>	
									<td style="width:100px;" class="text_value bdrbtm" valign="top">
										<?php if($row["DateStart"]!="00/00/00" && $row["DateStart"]!="00-00-0000"){echo $row["DateStart"];} ?>&nbsp;
									</td>
									<td style="width:100px;" class="text_value bdrbtm" valign="top"><?php print $row["allergy_status"];?>&nbsp;</td>	
								</tr>
							<?php								
							}
						?>
                        </table>
                        <?php
                        }
					
					?>

                   <?php
					if(imw_num_rows($rsGetMedication) > 0){
					?>
                    <table style="width:750px;" class="border" cellpadding="0" cellspacing="0">
							<tr>
								<td  colspan="7" class="tb_heading">Medication list:</td>				
							</tr>	
							<tr>
								<td class="text_lable bdrbtm" nowrap valign="top">Name</td>
								<td class="text_lable bdrbtm" nowrap valign="top">Dosage</td>
                                <td class="text_lable bdrbtm" nowrap valign="top">Site</td>
                                <td class="text_lable bdrbtm" nowrap valign="top">Sig</td>
								<td class="text_lable bdrbtm" nowrap valign="top">Comments</td>
                                <td class="text_lable bdrbtm" nowrap valign="top">Date Started</td>
                                <td class="text_lable bdrbtm" nowrap valign="top">Status</td>		
							</tr>
							<?php 	
							$o=$s=0;
							$ocularMed=$systemicMed="";
							while($row = imw_fetch_assoc($rsGetMedication)){
								if($row['type']==4){
									$o++;
								$sites = "";
								if($row["sites"] == "1"){
									$sites = "OS";
								}
								else if($row["sites"] == "2"){
									$sites = "OD";
								}
								else if($row["sites"] == "3"){
									$sites = "OU";
								}
								else if($row["sites"] == "4"){
									$sites = "PO";
								}	
							if($o==1){	
								$ocularMed.='
                        	    <tr>
                            		<td colspan="7" style="padding-left:5px;" class="tb_subheading bdrbtm">Ocular</td>
                            	</tr>';
                         	}
							
								$ocularMed.='
								<tr>		
									<td class="text_value bdrbtm pdl5" valign="top" style="width:180px;">'.$row["title"].'</td>			
									<td class="text_value bdrbtm" valign="top" style="width:65px;">'.$row["destination"].'</td>			
									<td class="text_value bdrbtm" valign="top" style="width:40px;">'.$sites.'</td>
                                    <td class="text_value bdrbtm" valign="top" style="width:120px;">'.$row["sig"].'</td>			
									<td class="text_value bdrbtm" valign="top" style="width:125px;">'.$row["med_comments"].'</td>
                                    <td class="text_value bdrbtm" valign="top" style="width:100px;">';
									 if($row["DateStart"]!="00/00/00" && $row["DateStart"]!="00-00-0000"){
										 $ocularMed.=$row["DateStart"];
									 } 
									$ocularMed.='
                                    </td>			
								    <td class="text_value bdrbtm" valign="top" style="width:50px;">'.$row["allergy_status"].'</td>
								</tr>';				
															
								}
								if($row['type']=="1"){
								
									$s++;
								$sites = "";
								if($row["sites"] == "1"){
									$sites = "OS";
								}
								else if($row["sites"] == "2"){
									$sites = "OD";
								}
								else if($row["sites"] == "3"){
									$sites = "OU";
								}
								else if($row["sites"] == "4"){
									$sites = "PO";
								}	
							if($s==1){	
								$systemicMed.='
								 <tr>
                            		<td colspan="7" style="padding-left:5px;" class="tb_subheading bdrbtm">Systemic</td>
                            	</tr>';
							}
							$systemicMed.='
							<tr>		
								<td class="text_value bdrbtm pdl5" valign="top" style="width:180px;">'.$row["title"].'</td>			
								<td class="text_value bdrbtm" valign="top" style="width:65px;">'.$row["destination"].'</td>			
								<td class="text_value bdrbtm" valign="top" style="width:40px;">'.$sites.'</td>
                                <td class="text_value bdrbtm" valign="top" style="width:120px;">'.$row["sig"].'</td>			
								<td class="text_value bdrbtm" valign="top" style="width:125px;">'.$row["med_comments"].'</td>
                                <td class="text_value bdrbtm" valign="top" style="width:100px;">';
								if($row["DateStart"]!="00/00/00" && $row["DateStart"]!="00-00-0000"){
									$systemicMed.=$row["DateStart"];
								} 
								$systemicMed.='
								</td>			
								    <td class="text_value bdrbtm" valign="top" style="width:50px;">'. $row["allergy_status"].'</td>
								</tr>';
								}	
							}
							echo $ocularMed;
							echo $systemicMed;
							?>
                            </table>
					<?php } 
					if(imw_num_rows($res_fur_appt) > 0){	?>
                    
                    
						<table style="width:750px;"  class="border" cellpadding="0" cellspacing="0">	
							<tr>
								<td style="width:750px;" colspan="6" class="tb_heading">Future Appointments:</td>				
							</tr>	
							<tr>
								<td style="width:200px;" class="text_lable bdrbtm" nowrap valign="top">Physician</td>
								<td style="width:150px;" class="text_lable bdrbtm" nowrap valign="top">Procedure</td>
								<td style="width:100px;"  class="text_lable bdrbtm" nowrap valign="top">Date</td>
                                <td style="width:100px;"  class="text_lable bdrbtm" nowrap valign="top">Time</td>
                                <td style="width:150px;"  class="text_lable bdrbtm" nowrap valign="top">Facility</td>
							</tr>
							<?php 	
							while($row = imw_fetch_assoc($res_fur_appt)){
							?>
								<tr>		
									<td style="width:200px;" class="text_value bdrbtm" valign="top"><?php echo getPhysicianName($row["sa_doctor_id"],$row["appt_date"]); ?>&nbsp;</td>			
									<td style="width:150px;" class="text_value bdrbtm" valign="top"><?php echo $arr_procedure[$row["procedureid"]]; ?>&nbsp;</td>				
                                    <td style="width:100px;" class="text_value bdrbtm" valign="top"><?php echo $row["sa_app_start_date"]; ?>&nbsp;</td>			
									<td style="width:100px;" class="text_value bdrbtm" valign="top"><?php echo core_time_format($row["sa_app_starttime"]); ?>&nbsp;</td>
                                    <td style="width:150px;" class="text_value bdrbtm" valign="top"><?php echo $arr_ficility[$row["sa_facility_id"]]; ?>&nbsp;</td>
								</tr>				
							<?php								
							}
							?>
							
						</table>
                       
                        
			<?php
            }	$iportal_instructions_detail = $iportal_status="";
				$reqInsDetFacQry = "SELECT iportal_instructions_detail,dis_iportal from facility where facility_type=1";
				$reqInsDetFacRes = imw_query($reqInsDetFacQry);
				if(imw_num_rows($reqInsDetFacRes)) {
					$reqInsDetFacRow = imw_fetch_array($reqInsDetFacRes);
					$iportal_instructions_detail = $reqInsDetFacRow["iportal_instructions_detail"];
					$iportal_status = $reqInsDetFacRow["dis_iportal"];
				}
			$reqQry = "SELECT temp_key FROM patient_data WHERE temp_key!='' and (username='' OR username IS NULL)  and id = '".$_SESSION["pid"]."'";
			$result_obj = imw_query($reqQry);
			if($iportal_status!=1){
				$result_data = imw_fetch_assoc($result_obj);
			?>
             <table style="width:750px; font-size:14px;"  class="border" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" class="tb_heading" style="width:750px;"> For login to Patient Portal </td>
                </tr>
        <?php if($result_data["temp_key"] && imw_num_rows($result_obj)>0){ ?> 
                <tr>
                    <td class="text_value bdrbtm"  style="width:73px; font-weight:bold; vertical-align:middle; height:18px;">Temp Key:</td>
					<td class="text_value bdrbtm"  style=" width:630px;vertical-align:middle; height:18px;"><?php echo $result_data["temp_key"]; ?></td>
                </tr>
        <?php } ?>
                <tr>
                    <td class="text_value bdrbtm"  style="width:73px;font-weight:bold;vertical-align:top; height:18px;">Instructions:</td>
					<td class="text_value bdrbtm"  style=" width:630px;vertical-align:middle; height:18px;"><?php echo stripslashes(nl2br($iportal_instructions_detail)); ?></td>
                </tr>                
            </table>
            	<?php } ?>
				
</page>
<?php
$patient_print_data = ob_get_contents();
ob_end_clean();
$print_file_name = "chk_in_print_pt_visit_".$_SESSION["authId"];
$file_path = write_html($patient_print_data);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	var file_name = '<?php print $print_file_name; ?>';
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	html_to_pdf('<?php echo $file_path; ?>','p',file_name);
	window.close();
</script>