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

// Removed REMOTE_SYNC FUNCTIONALITY 
if(isset($_REQUEST['ajaxReq']) && empty($_REQUEST['ajaxReq']) == false){
	require_once("../../config/globals.php");
	require_once("../../library/patient_must_loaded.php");
	require_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
	$medical = new MedicalHistory($_REQUEST['showpage']);
}

$last_examine = $medical->last_examine_detail();
$last_examine_date = date(phpDateFormat(), strtotime(str_replace('-', '/', $last_examine['createdDate'])));
$examine_date_time =  ($last_examine['total'] > 0 ) ? $last_examine_date.' at '.$last_examine['createdTime'].' '.$last_examine['phy_name'] : '';
?>
<div class="row" id="ptMedDrop">
	
  <div class="col-xs-12 pt10"><div class="row"><h2>Patient Medical History</h2></div></div>
	
  <div class="col-xs-12">
  	<select class="selectpicker" onChange="last_exm_all('<?php echo $medical->patient_id;?>',this.value)" name="selLastReviwed" id="selLastReviwed" data-width="60%">
          <option value="no">Medical Reviewed Hx</option>
          <option value="">All</option>
          <option value="Ocular Hx">Ocular Hx</option>
          <option value="General Health">General Health</option>
          <option value="Medications">Medications</option>
          <option value="Sx/Procedure">Sx/Procedure</option>
          <option value="Allergies">Allergies</option>
          <option value="Immunizations">Immunizations</option>
          <option value="Lab">Lab</option>
          <option value="Radiology">Radiology</option>
          <option value="General Health - Advanced Directive">Advanced Directive</option>
		</select>
     <span>
    	<input type="button" value="Reviewed" class="btn btn-success" id="reviewed_all" name="reviewed_all" onClick="<?php if(core_check_privilege(array("priv_vo_clinical")) == true){ ?> view_only_acc_call(0); <?php }else{ ?> reviewed_save_all(this,'<?php echo $medical->patient_id;?>','<?php echo $_SESSION['authId'];?>')<?php } ?>" />
    </span>
    <span class="mt5 mb10 show" ><?php echo $examine_date_time; ?></span>
    
   
  </div>

</div>

<div class="clearfix"></div>	

<div class="row mt5" id="listMedHx">
            <?php
							$history_eye = $history = $temp_val = '';
							$arrPtOcular = $medical->getPtOcularInfo();
							$history = $arrPtOcular["eye_history"];		
							$temp_val = (strlen($history) > $medical->max_chars) ? substr($history,0,$medical->max_showc)."..." : $history;
							$history_eye = stripslashes(html_entity_decode($temp_val));
							
							$problem_eye_1 = $problem_eye_2 = $temp_val = '';
							if( count($arrPtOcular["eye_problem"]) > 0 || strlen($arrPtOcular["eye_problems_other"])>0)
							{
								$problem = "";
								foreach($arrPtOcular["eye_problem"] as $key => $val)
								{
									if(count($val) > 0)
									{
										$temp_val = (strlen($val) > $medical->max_chars) ? substr($val,0,$medical->max_showc)."..." : $val;
										$problem_eye_1 .= '<li>'.stripslashes(html_entity_decode($temp_val)).'</li>';
                 	}
								}
								
								//-------eye problem other--------------
								$eye_problem_other = $arrPtOcular["eye_problems_other"];
								if($eye_problem_other!="" || !empty($eye_problem_other) || $eye_problem_other!=NULL)
								{
									$temp_val = (strlen($eye_problem_other) > 20) ? substr($eye_problem_other,0,20)."..." : $eye_problem_other ;
									$problem_eye_2 = '<li>'.stripslashes(html_entity_decode($temp_val)).'</li>	';
              	}
           		}
							
							$any_condition = $any_condition_other = $temp_val = "";
							if( count($arrPtOcular["you_rel"]) > 0 || strlen($arrPtOcular["OtherDesc"])>0)
							{
								foreach($arrPtOcular["you_rel"] as $key => $val)
								{
									if(count($val) > 0)
									{
										$temp_val = (strlen($val)>$medical->max_chars) ? substr($val,0,$medical->max_showc)."..." : $val;
										$any_condition .= '<li>'.stripslashes(html_entity_decode($temp_val)).'</li>';
                 	}
               	}		
								
								//-------Any Condition other--------------
								$temp_val = $arrPtOcular["OtherDesc"];
								if($temp_val <> "" || !empty($temp_val) || $temp_val!=NULL)
								{
									$temp_val = (strlen($temp_val) > 20) ? substr($temp_val,0,20)."..." : $temp_val;
									$any_condition_other	=	'<li>'.stripslashes(html_entity_decode($temp_val)).'</li>';
								}
								//------- Any Condition other --------------		
							}	
							
							$dataOcuMscllnous = '';
							$ocuMiscArr = $medical->getPtOcularMisc();
							if( is_array($ocuMiscArr) && count($ocuMiscArr) > 0 )
							{
								$dataOcuMscllnous.= '<li class="sub_li"><b>Miscellaneous</b></li>';
								$dataOcuMscllnous.= '<ul>';
								foreach( $ocuMiscArr as $key => $data)
								{
									$v = ($data['val']) ? '&nbsp;:&nbsp;'.$data['val'] : '';
									$dataOcuMscllnous.= '<li>'.$data['label'].$v.'</li>';
								}
								$dataOcuMscllnous.= '</ul>';
							}
						?>
            <ul>
            	<li class="pointer" id="Medical_Hx" onclick="redirect_page('ocular');">Ocular</li>
            	<li id="tdOcEyeHistory" class="sub_li"><b><?php echo $history_eye;?></b></li>
              <li class="sub_li"><b>Eye Problems</b>
              <?php if($problem_eye_1 <> '' || $problem_eye_2 <> '') { ?>
              	<ul id="tdOcEyeProb">
              	<?php echo $problem_eye_1;?>
                <?php echo $problem_eye_2;?>
               	</ul>
             	<?php } ?>
              </li>
              <li class="sub_li"><b>Any Conditions</b></li>
              <?php if($any_condition <> '' || $any_condition_other <> '') { ?>
              	<ul id="tdOcAnyCond">
                	<?php echo $any_condition;?>
                  <?php echo $any_condition_other;?>
               	</ul>
             	<?php } ?>
              </li>
              <?php echo $dataOcuMscllnous; ?>
           	</ul>
        	
      
      
     	<!-- Start Rendering Blood Sugar History -->   
			<?php	
        $query = "select id, sugar_value, hba1c, date_format(creation_date,'".get_sql_date_format()."') as createdDate
                              from patient_blood_sugar where patient_id = '".$medical->patient_id."' ORDER BY creation_date DESC LIMIT 1;";
        $sql = imw_query($query);
        $row = imw_fetch_object($sql);
        
        $blood_sugar_date = trim($row->createdDate);
        $blood_sugar_value = trim($row->sugar_value);
        $hba1c = $row->hba1c;
        
        $blood_sugar =  (($blood_sugar_date != "" && $blood_sugar_value != "") ? $blood_sugar_date." - ".$blood_sugar_value." mg/dl" : '');
        $hba	=  (($hba1c <> '' ) ? "HbA1c - ".$hba1c : '');
     	?>
      <ul>
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('general_health');">Blood Sugar</li>
        <?php if($blood_sugar && $hba) { ?>
        
        	<ul>
          	<?php if($blood_sugar) { ?><li><?php echo $blood_sugar; ?></li><?php } ?>
            <?php if($hba) { ?><li><?php echo $hba; ?></li><?php } ?>
        	</ul>
      	
      	<?php } ?>
    	</ul>    
      
      <!-- End Rendering Blood Sugar History -->   
  		
      
      <!-- Start Rendering Cholesterol History -->
     	<?php
			 	$query = "Select id, cholesterol_total, cholesterol_triglycerides, cholesterol_LDL, cholesterol_HDL,
													date_format(creation_date,'".get_sql_date_format()."') as creationDate
                         	from patient_cholesterol where patient_id = '".$medical->patient_id."' order by creation_date desc LIMIT 1;";
				$sql = imw_query($query);
				$row = imw_fetch_object($sql);
				
				$cholesterol_date = $row->creationDate;
        $cholesterol_total = $row->cholesterol_total;
        $cholesterol_tri = $row->cholesterol_triglycerides;
        $cholesterol_ldl = $row->cholesterol_LDL;
        $cholesterol_hdl = $row->cholesterol_HDL;
				
				$cholesterol = $cholesterol_date." ".$cholesterol_total." ".$cholesterol_tri." ".$cholesterol_ldl." ".$cholesterol_hdl;
				$cholesterol = trim($cholesterol);
			?>
                
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('general_health');" >Cholesterol</li>
        <?php if($cholesterol) { ?>
        <ul><li><?php echo $cholesterol; ?></li></ul>
        <?php } ?>
    	</ul>
  		<!-- End Rendering Cholesterol History -->
  		
      <!-- Start Rendering General Health History -->
      <?php
				$arrPtInfo = $medical->getPtGenHealthInfo();
				$arrShowGH = array_merge((array)$arrPtInfo["AnyCond"]["You"],(array)$arrPtInfo["AnyCond"]["Relatives"]);
				//	Removes duplicate values from an array
				$arrShowGH = array_unique($arrShowGH);
				sort($arrShowGH);
				$general_health = "";
				if(count($arrShowGH) > 0 )
				{		
					foreach($arrShowGH as $key => $val)
					{
						$val = (strlen($val)>$medical->max_chars) ? substr($val,0,$medical->max_showc)."..." : $val;
						if($val=="Diabetes" && trim($arrPtInfo["diabetes_values"])!="")
						{
							$diabetes_values = $arrPtInfo["diabetes_values"];
							if(strlen($diabetes_values)>9)
							{
								$diabetes_values = substr($diabetes_values,0,9)."...";
							}
							$val.= " - ".$diabetes_values;
						}
						$general_health .= '<li>'.stripslashes(html_entity_decode($val)).'</li>';
					}
				}
				
				$arrShowGH = explode(",",$arrPtInfo['str_annaual']);
				if(count($arrShowGH) > 0 )
				{		
					foreach($arrShowGH as $key => $val)
					{
						if( $val )
							$general_health .= '<li>'.stripslashes(html_entity_decode($val)).'</li>';
					}
				}
					
				$dataGHMscllnous = '';
				$ghMiscArr = $medical->getPtOcularMisc('general_health');
				if( is_array($ghMiscArr) && count($ghMiscArr) > 0 )
				{
					$dataGHMscllnous.= '<li class="sub_li"><b>Miscellaneous</b></li>';
					$dataGHMscllnous.= '<ul>';
					foreach( $ghMiscArr as $key => $data)
					{
						$v = ($data['val']) ? '&nbsp;:&nbsp;'.$data['val'] : '';
						$dataGHMscllnous.= '<li>'.$data['label'].$v.'</li>';
					}
					$dataGHMscllnous.= '</ul>';
				}

			?>
      
      <ul>
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('general_health');">General Health</li>
        <?php if(	$general_health) { ?>
        <ul id="tdGHMedicalCond"><?php echo $general_health; ?></ul>
        <?php } ?>
        <?php echo $dataGHMscllnous; ?>
    	</ul>
      <!-- End Rendering General Health History -->
      
      
      <!-- Start Rendering Review Of Systems History -->
      <?php 
				$ros = "";	$ros_neg="";
				$ros_toggle = "";
				$flg_neg_ros=0; $flg_pos_ros=0; $nm_neg_ros="";
				
				if(count($arrPtInfo["ROS"]) > 0 )
				{	
					
					foreach($arrPtInfo["ROS"] as $key => $val)
					{
						if(count($val) > 0)
						{
							$key = (strlen($key)>$medical->max_chars) ? substr($key,0,$medical->max_showc)."...":$key;
							if($key != "negChkBx")
							{
								$ros .= '<li>'.stripslashes(html_entity_decode($key))."</li>";
								$flg_pos_ros+=1;
							}
							elseif($key == "negChkBx")
							{
								
								foreach($arrPtInfo["ROS"]["negChkBx"] as $negChkBxKey => $negChkBxVal)
								{
									$negChkBxValOrg = $negChkBxVal;
									$negChkBxVal = (strlen($negChkBxVal)>$medical->max_chars)?substr($negChkBxVal,0,$medical->max_showc)."...":$negChkBxVal;
									$ros_neg .= '<li style="color:green!important; font-weight:600;" >'."Negative&nbsp;".$negChkBxVal."</li>";
									$flg_neg_ros+=1;									
									$nm_neg_ros = str_replace($negChkBxValOrg."<br/>","", $nm_neg_ros);
								}
								
							}
						}elseif($key != "negChkBx"){
							$nm_neg_ros = $nm_neg_ros.$key."<br/>";
						}
					}
					//
					
					if(!empty($ros) && $flg_pos_ros<14 && !empty($flg_neg_ros) ){
						$ros .= '<li style="color:green!important; font-weight:600;" >All recorded systems are negative except as noted above.</li>';
					}else if(!empty($flg_neg_ros)){
						$ros = $ros_neg;
						//$ros .= '<li style="color:green!important; font-weight:600;" >All systems negative.</li>';
					}
					$flg_dn_ros = $flg_pos_ros+$flg_neg_ros;
					$clr_ros = ($flg_dn_ros>9) ? "text-success" : "text-warning";
					$spn_ros = "<span class=\"badge\" >".($flg_dn_ros)." / 14</span>";
				}
				
				
			?>
     	<ul>
      	<li class="pointer <?php echo $clr_ros; ?>" id="Medical_Hx_ROS" onclick="redirect_page('general_health');" data-toggle="tooltip" data-html="true" title="<?php echo $nm_neg_ros; ?>" >Review of Systems <?php echo $spn_ros; ?></li>
				<?php if($ros) { ?>
        <ul id="tdGHROS"><?php echo $ros; ?></ul>
        <?php } ?>	
			</ul>
      <!-- End Rendering Review Of Systems History -->   
      
      
      <!-- Start Rendering Social History -->
     	<?php
				
				$query = "select smoking_status, number_of_years_with_smoke, source_of_smoke, source_of_smoke_other, smoke_perday, alcohal, source_of_alcohal_other, list_drugs, otherSocial,smoke_years_months, smoke_counseling, offered_cessation_counselling_date, cessation_counselling_option, cessation_counselling_other, intervention_not_performed_status, intervention_reason_option, med_order_not_performed_status, med_order_reason_option from social_history where patient_id = '".$medical->patient_id."'";
				$sql = imw_query($query);
				$row = imw_fetch_assoc($sql);
				$row = array_map('trim',$row);
				$smoking_status_exp=explode('/',$row["smoking_status"]);
				$smoking_status=$smoking_status_exp[0];
				if($row["smoking_status"] != "Never smoker" && $row["smoking_status"] != "")
				{
					$smoke_source = $row["source_of_smoke"];
					if($smoke_source == "Other")
					{
						$smoke_source = $row["source_of_smoke_other"]; 
						$smoke_source = (strlen($smoke_source)>10) ? substr($smoke_source,0,10)."..." : $smoke_source;
					}
				}
				
				$smoke_per_day = $row["smoke_perday"];
				$smoke_per_day = ($smoke_per_day) ? $smoke_per_day : '';
				
				$number_of_years_with_smoke = $row["number_of_years_with_smoke"];
				$number_of_years_with_smoke = ($number_of_years_with_smoke) ? $number_of_years_with_smoke : '';
				
				$smoke_years_months = $row["smoke_years_months"];
				
				$smoke_str = (($row["smoking_status"]) ? $smoking_status : "").(($smoke_source) ? " of ".$smoke_source : "")." ".(($smoke_per_day) ? $smoke_per_day." Per Day" : "").(($number_of_years_with_smoke) ? " for ".$number_of_years_with_smoke.(($smoke_years_months ) ? " ".$smoke_years_months  : "") : "");
				$smoke_str = 	stripslashes(html_entity_decode(trim($smoke_str)));
				
				// *** alcohal ***
				$alcohal = $row["alcohal"];
				if($alcohal == "Other")
				{
					$alcohal = $row["source_of_alcohal_other"];
					$alcohal = (strlen($alcohal)>13) ? substr($alcohal,0,13)."..." : $alcohal;
				}
				$alcohal_str = stripslashes(html_entity_decode($alcohal));
				
				// *** List Drugs ***
				$list_drugs = $row["list_drugs"];
				$list_drugs = (strlen($list_drugs)>21) ? substr($list_drugs,0,21)."..." : $list_drugs ;
				
				$list_drugs_str = stripslashes(html_entity_decode($list_drugs));
				
				// *** More Information ***
				$other_social = $row["otherSocial"];
				$other_social = (strlen($other_social)>20) ? substr($other_social,0,20)."..." : $other_social ;
				
				$other_social_str = stripslashes(html_entity_decode($other_social));

				$cessationStr = '';
				if( $row['offered_cessation_counselling_date']) {
					$cDate = get_date_format($row['offered_cessation_counselling_date']);
					$cType = ($row['cessation_counselling_option'] == 'Other') ? $row['cessation_counselling_other'] : $row['cessation_counselling_option'];
					$cessationStr .= $cType." ".($cDate ? 'On '.$cDate : '');
					trim($cessationStr);
				}
				
				$interventionStr = '';
				if( $row['intervention_not_performed_status']) {
					$interventionStr .= "Intervention not done for Tobacco Use Cessation Counseling due to ".$row['intervention_reason_option'];
				}

				$medOrderStr = '';
				if( $row['med_order_not_performed_status']) {
					$medOrderStr .= "Medication order not done for Tobacco Use Cessation due to ".$row['med_order_reason_option'];
				}
			?>
      
      <ul>
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('general_health');">Social</li>
        <ul>
					<?php if($smoke_str) { ?><li id="tdGHSocialSmoking"><b>Smoke:</b> <?php echo $smoke_str; ?></li><?php } ?>
					<?php if($cessationStr) { ?><li id="tdGHSocialCessation"><b>Cessation Counseling:</b> <?php echo $cessationStr; ?></li><?php } ?>
					<?php if($interventionStr) { ?><li id="tdGHSocialIntervention" ><?php echo $interventionStr; ?></li><?php } ?>
        	<?php if($medOrderStr) { ?><li id="tdGHSocialMedOrder" ><?php echo $medOrderStr; ?></li><?php } ?>	
        	<?php if($alcohal_str) { ?><li id="tdGHSocialAlchohal"><b>Alcohol:</b> <?php echo $alcohal_str; ?></li><?php } ?>
         	<?php if($list_drugs_str) { ?><li id="tdGHSocialDrugList" ><?php echo $list_drugs_str; ?></li><?php } ?>
          <?php if($other_social_str) { ?><li id="tdGHSocialOther" ><?php echo $other_social_str; ?></li><?php } ?>
        </ul>
    	</ul>
  		<!-- End Rendering Social History -->
      
      <?php
				/* ***Collecting Data For Ocular MEdication || Sx Procedures || Allergies */
				$strNoMedication = $strNoSurgery = $strNoAllergy = $strNoImm = "";
				$query = "select id, title, type, ag_occular_drug from lists where pid = '".$medical->patient_id."' and allergy_status = 'Active' order by(id)";
				$sql = imw_query($query);
				$ocularData = '';
       	$ocularSxData = '';
        $ocularAllData = '';
        $drugAllData = '';
        $medicationData = '';
        $sxProcData = '';
								
				while($row = imw_fetch_assoc($sql))
				{
					$title = ucfirst($row['title']);
					if(strlen($title) > $medical->max_chars){
          	$title = substr($title,0,$medical->max_showc).'...';
         	}
					$type = $row['type'];
					$ag_occular_drug = $row['ag_occular_drug'];
					
					$title = stripslashes(html_entity_decode($title));
					$title = '<li>'.$title.'</li>';
					switch ($type):
						case 1:
							//--- GET MEDICATION DATA -----
							$medicationData .= $title;
						break;
            case 3:
							//--- GET OCULAR ALLERGY DATA -----
							$ocularAllData .= $title;
						break;
						case 4:
							//--- GET OCULAR MEDICATION DATA -----
							$ocularData .= $title;
						break;
						case 5:
							//--- GET SX/PROCEDURE DATA -----
           		$sxProcData .= $title;
						break;
						case 6:
							//--- GET OCULAR SX DATA -----
							$ocularSxData .= $title;
						break;
						case 7:
							//--- GET DRUG ALLERGY DATA -----
							$drugAllData .= $title;
						break;
					endswitch;
				}
			?>
       
      <!-- Start Rendering Ocular Medication History -->
      <?php
				$ocular_med = ''; 
				if(empty($ocularData) == false)
				{
					$ocular_med = $ocularData;
				}
				else
				{
					$strNoMedication = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
					if($strNoMedication == "checked")
					{
						$ocular_med = '<li>No Known Medication</li>';
					}
					else if(empty($medicationData) == true)
					{
						$ocular_med = '<li>Not Reviewed</li>';
					}
				}
			
			?>
      
      <ul>
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('medication');">Ocular Medication</li>
        <?php if($ocular_med) { ?>
        <ul id="tdOcMed"> <?php echo $ocular_med; ?></ul>
        <?php } ?>
      </ul>
  		<!-- End Rendering Ocular Medication History -->
      
      
      <!-- Start Rendering Ocular SX Procedures History -->
      <?php
				$ocular_sx = '';
				if(empty($ocularSxData) == false)
				{
					$ocular_sx = $ocularSxData;
				}
				else
				{
					$strNoSurgery = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
					if($strNoSurgery == "checked")
					{
						$ocular_sx = '<li>No Known Surgeries</a></li>';
					}
					else if(empty($sxProcData) == true)
					{
						$ocular_sx = '<li>Not Reviewed</a></li>';
					}
				}
				
				if($ocularAllData)
				{
					$ocular_sx .= $ocularAllData;
				}
			
			?>
      
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('sx_procedures');">Ocular Sx/Procedures</li>
        <?php if($ocular_sx) { ?>
        <ul id="tdSxData"><?php echo $ocular_sx; ?></ul>
        <?php } ?>
     	</ul>
  		<!-- End Rendering Ocular SX Procedures History -->
      
  		
  		<!-- Start Rendering Allergies Reviewed History -->
      <?php
				$allergies_rev = '';
				if(empty($drugAllData) == false)
				{
					$allergies_rev = $drugAllData;
				}
				else
				{
					$strNoAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
					if($strNoAllergy == "checked")
					{
						$allergies_rev = '<li>NKDA</li>';
					}
					else
					{
						$allergies_rev = '<li>Not Reviewed</li>';
					}
				}
			?>
      
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('allergies');">Allergies Reviewed</li>
        <ul id="tdAllergyData"><?php echo $allergies_rev; ?></ul>
     	</ul>
      <!-- End Rendering Allergies Reviewed History -->
      
      
      <!-- Start Rendering Medication History -->
      <?php
				$medication = '';
				if(empty($medicationData) == false)
				{
					$medication = $medicationData;
				}
				else
				{
					$strNoMedication = commonNoMedicalHistoryAddEdit($moduleName="Medication",$moduleValue="",$mod="get");
					if($strNoMedication == "checked")
					{
						$medication = '<li>No Known Medications</li>';
					}
				}
			?>   
       
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('medication');">Medication</li>
        <?php if($medication) { ?>
        <ul id="tdMed"><?php	echo $medication; ?></ul>
        <?php } ?>
    	</ul>
      
      <!-- End Rendering Medication History -->
      
      
      <!-- Start Rendering Sx/Procedures History -->
      <?php
				$sx_proc = '';
				if(empty($sxProcData) == false)
				{
					$sx_proc = $sxProcData;
				}
				else
				{
					$strNoSurgery = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
					if($strNoMedication == "checked")
					{
						$sx_proc = '<li><a href="javacript:void(0);">No Known Surgeries</a></li>';
					}
				}
			?> 
         
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('sx_procedures');">Sx/Procedures</li>
        <?php if($sx_proc) { ?>
        <ul id="tdSxProcData"><?php	echo $sx_proc; ?></ul>
        <?php } ?>
    	</ul>
      <!-- End Rendering Sx/Procedures History -->
       
       
      <!-- Start Rendering Immunizations History -->
      <?php
				$immunization = ''; $immunization_toggle = '';
				$query = "select date_format(administered_date,'".get_sql_date_format()."') as administered_date,
                                immunization_id,id from immunizations  
                                where patient_id = '".$medical->patient_id."' and status = 'Given'";
				$sql = imw_query($query);
				while($row = imw_fetch_assoc($sql))
				{
					$administered_date = $row['administered_date'];
					if($administered_date == '00-00-0000')
					{
						$administered_date = '';
					}
					$name = $row['immunization_id'];
					$im_id = $row["id"];
					if(strlen($name) > $medical->max_chars)
					{
						$name = substr($name,0,$medical->max_chars).'...';
					}
					$immunization .=  '<li>'.$administered_date.' '.$name.'</li>';
				}
				
				if(!$immunization)
				{
					$strNoImm = commonNoMedicalHistoryAddEdit($moduleName="Immunizations",$moduleValue="",$mod="get");
					if($strNoImm == "checked"){
						$immunization .= '<li>No Immunizations</li>';
					}
					
				}
			?>
      
      <ul >
      	<li class="pointer" id="Medical_Hx" onclick="redirect_page('immunizations');">Immunizations</li>
        <?php if($immunization) { ?>
        <ul><?php	echo $immunization; ?></ul>
        <?php } ?>
    	</ul>
      <!-- End Rendering Immunizations History --> 
      
</div>