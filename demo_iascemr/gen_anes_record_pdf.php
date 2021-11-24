<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
include("common_functions.php");
include("common/commonFunctions.php");
$tablename = "genanesthesiarecord";
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
include_once("new_header_print.php");
$get_http_path=$_REQUEST['get_http_path'];

//

//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
	$patientId = $patient_id = $_REQUEST["patient_id"];
	if(!$patient_id) {
		$patientId = $patient_id = $_SESSION['patient_id'];
	}	
	$pConfId = $_REQUEST["pConfId"];
	if(!$pConfId) {
		$pConfId = $_SESSION['pConfId'];
	}	
	
	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	
	
	$fieldName = "genral_anesthesia_form";
	$pageName = "gen_anes_rec.php?patient_id=$patient_id&pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blank_mainform.php?patient_id=$patient_id&pConfId=$pConfId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId;


//GET COMMON VARIABLES FOR THIS PAGE
	$selectPatientProcedureQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$selectPatientProcedureRes = imw_query($selectPatientProcedureQry) or die(imw_error());
	$selectPatientProcedureNumRow = imw_num_rows($selectPatientProcedureRes);
	if($selectPatientProcedureNumRow>0) {
		$selectPatientProcedureRow = imw_fetch_array($selectPatientProcedureRes);
		$patient_primary_procedure = $selectPatientProcedureRow["patient_primary_procedure"];
		$patient_secondary_procedure = $selectPatientProcedureRow["patient_secondary_procedure"];
		
		$anesthesiologist_id = $selectPatientProcedureRow["anesthesiologist_id"];
		
		$getanesthesiologistNameQry = "select * from users where usersId='$anesthesiologist_id' and user_type='Anesthesiologist'";
		$getanesthesiologistNameRes = imw_query($getanesthesiologistNameQry) or die(imw_error());
		$getanesthesiologistNameRow = imw_fetch_array($getanesthesiologistNameRes); 
		$getanesthesiologistName = $getanesthesiologistNameRow["lname"].", ".$getanesthesiologistNameRow["fname"]." ".$getanesthesiologistNameRow["mname"];
		
		// GETTING NURSE SIGN OR NOT		
			$signatureOfAnestheologist = $getanesthesiologistNameRow["signature"];
		// GETTING NURSE SIGN OR NOT
		
	}
	
	
	
//END GET COMMON VARIABLES FOR THIS PAGE



	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		
		 if($anesthesiaClass=="" || $armsTuckedLeft=="" || $armsTuckedRight==""
		 || $armsArmboardsLeft=="" || $armsArmboardsRight=="" || $eyeTapedLeft==""
		 || $eyeTapedRight=="" || $eyeLubedLeft=="" || $eyeLubedRight=="" || trim($evaluation)==""
		)
		
		{
			$form_status = "not completed";
		}
		
		
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	$chkgenAnesQry = "select * from `genanesthesiarecord` where  confirmation_id = '".$pConfId."'";
	$chkgenAnesRes = imw_query($chkgenAnesQry) or die(imw_error()); 
	$chkgenAnesNumRow = imw_num_rows($chkgenAnesRes);
	
	//echo "<script>location.href='gen_anes_rec.php?formStatus=filled$saveLink';<script>";


//END SAVE RECORD TO DATABASE

//FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION
	function calculate_timeFun($MainTime) {
		global $objManageData;
		$time_split = explode(":",$MainTime);
		if($time_split[0]=='24') { //to correct previously saved records
			$MainTime = "12".":".$time_split[1].":".$time_split[2];
		}
		if($MainTime == "00:00:00") {
			$MainTime = "";
		}else {
			$MainTime = $objManageData->getTmFormat($MainTime);//date('h:iA',strtotime($MainTime));
			//$MainTime = date('h:i A',strtotime($MainTime));
			//$MainTime = substr($MainTime,0,-1);
		}
		return $MainTime;
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

//VIEW RECORD FROM DATABASE
//	if($_POST['SaveRecordForm']==''){	
		$genAnesFormStatus='';
		$ViewgenAnesQry = "select *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat, date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat from `genanesthesiarecord` where  confirmation_id = '".$pConfId."'";
		$ViewgenAnesRes = imw_query($ViewgenAnesQry) or die(imw_error()); 
		$ViewgenAnesNumRow = imw_num_rows($ViewgenAnesRes);
		$ViewgenAnesRow = imw_fetch_array($ViewgenAnesRes); 
		if($ViewgenAnesNumRow>0) {
			$genAnesFormStatus = $ViewgenAnesRow["form_status"];
			$alertOriented = $ViewgenAnesRow["alertOriented"];
			$assistedByTranslator = $ViewgenAnesRow["assistedByTranslator"];
			$bp = $ViewgenAnesRow["bp"];
			$P = $ViewgenAnesRow["P"];
			$rr = $ViewgenAnesRow["rr"];
			$sao = $ViewgenAnesRow["sao"];
			$patientVerified = $ViewgenAnesRow["patientVerified"];
			$BMIvalue = stripslashes(trim($ViewgenAnesRow["BMIvalue"]));
			$anesthesiaClass = $ViewgenAnesRow["anesthesiaClass"];
			$o2n2oavailable = $ViewgenAnesRow["o2n2oavailable"];
			$PatientReassessed = $ViewgenAnesRow["PatientReassessed"];
			$MachineEquipment = $ViewgenAnesRow["MachineEquipment"];
			$reserveTanksChecked = $ViewgenAnesRow["reserveTanksChecked"];
			$positivePressureAvailable = $ViewgenAnesRow["positivePressureAvailable"];
			$maskTubingPresent = $ViewgenAnesRow["maskTubingPresent"];
			$vaporizorFilled = $ViewgenAnesRow["vaporizorFilled"];
			$absorberFunctional = $ViewgenAnesRow["absorberFunctional"];
			$gasEvacuatorFunctional = $ViewgenAnesRow["gasEvacuatorFunctional"];
			$o2AnalyzerFunctional = $ViewgenAnesRow["o2AnalyzerFunctional"];
			$ekgMonitor = $ViewgenAnesRow["ekgMonitor"];
			$endoTubes = $ViewgenAnesRow["endoTubes"];
			$laryngoscopeBlades = $ViewgenAnesRow["laryngoscopeBlades"];
			$others = $ViewgenAnesRow["others"];
			$othersDesc = $ViewgenAnesRow["othersDesc"];
			
			$startTime 	= $ViewgenAnesRow["startTime"];
			$startTime 	= calculate_timeFun($startTime);
			$stopTime 	= $ViewgenAnesRow["stopTime"];
			$stopTime 	= calculate_timeFun($stopTime);
			
			/*
			if($startTime=="00:00:00" || $startTime=="") {
				//$startTime=date("H:i A");
				$startTime="";
			}else {
			
				
			//}
			
			//$stopTime = $ViewgenAnesRow["stopTime"];
			$stopTime = $objManageData->getTmFormat($ViewgenAnesRow["stopTime"]);
			
			if($stopTime=="00:00:00" || $stopTime=="") {
				//$stopTime=date("H:i A");
				$stopTime="";
			}else {
			
				$stopTime=$objManageData->getTmFormat(calculate_timeFun($stopTime)); //CODE TO DISPLAY STOP TIME
			//}
			*/
			
			$mac = $ViewgenAnesRow["mac"];
			$macValue = $ViewgenAnesRow["macValue"];
			$millar = $ViewgenAnesRow["millar"];
			$millarValue = $ViewgenAnesRow["millarValue"];
			$etTube = $ViewgenAnesRow["etTube"];
			$etTubeSize = $ViewgenAnesRow["etTubeSize"];
			$lma = $ViewgenAnesRow["lma"];
			$lmaSize = $ViewgenAnesRow["lmaSize"];
			$mask = $ViewgenAnesRow["mask"];
			
			$teethUnchanged = $ViewgenAnesRow["teethUnchanged"];
			$Monitor_ekg = $ViewgenAnesRow["Monitor_ekg"];
			$Monitor_etco2 = $ViewgenAnesRow["Monitor_etco2"];
			$Monitor_etco2Sat = $ViewgenAnesRow["Monitor_etco2Sat"];
			$Monitor_o2Temp = $ViewgenAnesRow["Monitor_o2Temp"];
			$Monitor_PNS = $ViewgenAnesRow["Monitor_PNS"];
			
			
			$genAnesSignApplet = $ViewgenAnesRow["genAnesSignApplet"];
			if($genAnesSignApplet!='')
			{
				
			 	include("imageSc/imgGd.php");
					$qry="select genAnesSignApplet from genanesthesiarecord where confirmation_id= $pConfId";
					$qryRes = @imw_query($qry);
					$arrayRes = @imw_fetch_array($qryRes);
					$appletData= $arrayRes['genAnesSignApplet'];
					$imgName="bgGridBig.jpg";
					//$pixels = "TUp:138,33;CDr:193,42;CFill:290,229;96:458,400;23:202,126;96:362,254;1.5:154,400;0.5:218,400;0:250,400;ram:330,318;36:138,512;SB:138,544;22:154,558;mg Succinylchol..:41,543;Helo world:41,558;TUp:251,436;Cr:268,452;CDr:284,468;CFill:300,483;TDn:315,500;96:250,578;63:330,594;ST:282,610;100:346,624;66:394,640;1.5:330,384;7.5:378,416;96:458,142;36:314,126;12:186,206;00:602,14;36:138,14;66:138,366;36:602,366;TDn:349,5;TDn:443,356;CFill:203,354;CFill:219,354;CFill:395,3;CFill:604,148;CFill:140,132;CDr:524,355;CDr:139,212;CDr:604,212;CDr:284,3;TUp:333,354;TUp:491,2;TUp:604,243;TUp:140,275;";	
					//drawOnImage($pixels,$imgName,"tess.jpg");	
					drawOnImage2($appletData,$imgName,"new_html2pdf/tess.jpg","","new_html2pdf/");		
				//	}	
			}	
/*******end function************/
			
			$armsTuckedLeft = $ViewgenAnesRow["armsTuckedLeft"];
			$armsTuckedRight = $ViewgenAnesRow["armsTuckedRight"];
			$armsArmboardsLeft = $ViewgenAnesRow["armsArmboardsLeft"];
			$armsArmboardsRight = $ViewgenAnesRow["armsArmboardsRight"];
			$eyeTapedLeft = $ViewgenAnesRow["eyeTapedLeft"];
			//$eyeTapedRight = $ViewgenAnesRow["eyeTapedRight"];
			$eyeLubedLeft = $ViewgenAnesRow["eyeLubedLeft"];
			//$eyeLubedRight = $ViewgenAnesRow["eyeLubedRight"];
		
			$pressurePointsPadded = $ViewgenAnesRow["pressurePointsPadded"];
			$bss = $ViewgenAnesRow["bss"];
			$warning = $ViewgenAnesRow["warning"];
			
			$temp = $ViewgenAnesRow["temp"];
			$StableCardioRespiratory = $ViewgenAnesRow["StableCardioRespiratory"];
			$graphComments = $ViewgenAnesRow["graphComments"];
			$evaluation = $ViewgenAnesRow["evaluation"];
			$comments = $ViewgenAnesRow["comments"];
			$anesthesiologistId = $ViewgenAnesRow["anesthesiologistId"];
			$anesthesiologistSign = $ViewgenAnesRow["anesthesiologistSign"];
			$relivedNurseId = $ViewgenAnesRow["relivedNurseId"];
			
			$signAnesthesia1Id =  $ViewgenAnesRow["signAnesthesia1Id"];
			$signAnesthesia1FirstName =  $ViewgenAnesRow["signAnesthesia1FirstName"];
			$signAnesthesia1MiddleName =  $ViewgenAnesRow["signAnesthesia1MiddleName"];
			$signAnesthesia1LastName =  $ViewgenAnesRow["signAnesthesia1LastName"];
			$signAnesthesia1Name= $ViewgenAnesRow["signAnesthesia1LastName"].','.$ViewgenAnesRow["signAnesthesia1FirstName"];
			$signAnesthesia1Status =  $ViewgenAnesRow["signAnesthesia1Status"];
			
			$signAnesthesia2Id 					=  $ViewgenAnesRow["signAnesthesia2Id"];
			$signAnesthesia2DateTime 	= $ViewgenAnesRow["signAnesthesia2DateTime"];
			$signAnesthesia2DateTimeFormat 	= $ViewgenAnesRow["signAnesthesia2DateTimeFormat"];
			$signAnesthesia2FirstName 	=  $ViewgenAnesRow["signAnesthesia2FirstName"];
			$signAnesthesia2MiddleName 	=  $ViewgenAnesRow["signAnesthesia2MiddleName"];
			$signAnesthesia2LastName 		=  $ViewgenAnesRow["signAnesthesia2LastName"]; 
			$signAnesthesia2Status 			=  $ViewgenAnesRow["signAnesthesia2Status"];
			
			$reliefNurseId					= $ViewgenAnesRow["reliefNurseId"];
			$confirmIPPSC_signin		= $ViewgenAnesRow["confirmIPPSC_signin"];
			$siteMarked 						= $ViewgenAnesRow["siteMarked"];
			$patientAllergies 			= $ViewgenAnesRow["patientAllergies"];
			$difficultAirway				= $ViewgenAnesRow["difficultAirway"];
			$anesthesiaSafety				= $ViewgenAnesRow["anesthesiaSafety"];
			$allMembersTeam 				= $ViewgenAnesRow["allMembersTeam"];
			$riskBloodLoss 					= $ViewgenAnesRow["riskBloodLoss"];
			$bloodLossUnits					=	$ViewgenAnesRow["bloodLossUnits"];
			
			$vitalSignGridStatus					=	$ViewgenAnesRow["vitalSignGridStatus"];
			
			$version_num						=	$ViewgenAnesRow["version_num"];
			
			$ascId = $ViewgenAnesRow["ascId"];
			$confirmation_id = $ViewgenAnesRow["confirmation_id"];
			$patient_id = $ViewgenAnesRow["patient_id"];
			$elem_cnvs_anes_drw_file				= $ViewgenAnesRow["drawing_path"];
			$elem_cnvs_anes_drw_coords				= $ViewgenAnesRow["drawing_coords"];
		//}
	}	
	
	//GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
		$selectPreOpNursingQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingRes = imw_query($selectPreOpNursingQry) or die(imw_error());
		$selectPreOpNursingNumRow = imw_num_rows($selectPreOpNursingRes);
		if($selectPreOpNursingNumRow>0) {
			$selectPreOpNursingRow = imw_fetch_array($selectPreOpNursingRes);
			$patientHeight = stripslashes($selectPreOpNursingRow["patientHeight"]);
			
			if(trim($patientHeight)<>"" || $patientHeight<>"") {
				$patientHeightsplit = explode("'",$patientHeight); 
				$patientHeight = $patientHeightsplit[0]."' ".$patientHeightsplit[1].'"';
				
				$patientHeightInches = ($patientHeightsplit[0]*12)+$patientHeightsplit[1];
			}else {
				$patientHeight = "";
			}
			
			$patientWeight = $selectPreOpNursingRow["patientWeight"];
			if($patientWeight<>"") {
				$patientWeight = $patientWeight." lbs";
			}
			//CODE TO CALCULATE BMI VALUE
				if(!$BMIvalue) {
					if((trim($patientHeight)<>"" || $patientHeight<>"") && $patientWeight<>"") {
						
						$BMIvalueTemp = $patientWeight * 703/($patientHeightInches*$patientHeightInches);
						$BMIvalue = number_format($BMIvalueTemp,2,".","");
					}
				}	
			//END CODE TO CALCULATE BMI VALUE
		}	
	
	//END GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD	
	
//END VIEW RECORD FROM DATABASE

 $table_pdf.=$head_table;
 $table_pdf.="<style>.bdrbtm{vertical-align:middle;}</style>";
 $table_pdf.='<table style="width:744px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
  					<tr>
						<td colspan="4" class="fheader" style="width:744px;" >General Anesthesia Record</td>
					</tr>';
					
					if($version_num > 1) 
					{
					$table_pdf.='
					<tr>
						<td colspan="4" style="width:744px;" >
							<table style="width:744px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:744px;" class="bold bdrtop bgcolor">The following items were verified before Induction of Anesthesia</td>
								</tr>
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:280px;" valign="top">
													<strong>Nurse and anesthesia care provider confirm:&nbsp;</strong>
												</td>
												<td style="width:200px; vertical-align:top;text-align:left;">';
												if($signAnesthesia2Id)
												{
													$table_pdf.='<b>Anethesia Provider:</b> '.$signAnesthesia2LastName.", ".$signAnesthesia2FirstName." ".$signAnesthesia2MiddleName."<br>
														<b>Electronically Signed:&nbsp;</b>Yes"; 
												}else {
													$table_pdf.='__________';
												}	
											
								$table_pdf.='
												</td>
												<td style="width:235px;vertical-align:top;text-align:right; white-space:nowrap">';
												$getReliefNurseName = "";
								$table_pdf.="<b>Relief Nurse / Anesthesia:</b><br>";
												if($reliefNurseId){
													$getReliefNurseAnesName = getUsrNm($reliefNurseId);
													$table_pdf.=$getReliefNurseAnesName[0];   
												}else{
													$table_pdf.='_________';	
												}	
								$table_pdf.='
												</td>
											</tr>';
								if($signAnesthesia2Id)
								{
								$table_pdf.='	
											<tr>
												<td>&nbsp;</td>
												<td colspan="2" style="text-align:left;">
													<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia2DateTime).'
												</td>
											</tr>';
								}
								$table_pdf.='
										</table>
									</td>
								</tr>	
								<tr><td style="border-bottom:dashed 1px #C0C0C0">&nbsp;</td></tr>
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												<td class="bdrbtm" style="width:320px;">Confirmation of: identify, procedure, procedure site and consent(s)</td>
												<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
												if($confirmIPPSC_signin) { $table_pdf.=stripslashes($confirmIPPSC_signin); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
												<td class="bdrbtm" style="text-align:left;width:320px; ">Site marked by person performing the procedure</td>
												<td class="bdrbtm cbold" style="width:50px;"> ';
												if($siteMarked) { $table_pdf.=stripslashes($siteMarked); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												<td class="bdrbtm" style="width:320px;">Patient allergies</td>
												<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
												if($patientAllergies) { $table_pdf.=stripslashes($patientAllergies); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
												<td class="bdrbtm" style="text-align:left;width:320px; ">Difficult airway or aspiration risk?</td>
												<td class="bdrbtm cbold" style="width:50px;"> ';
												if($difficultAirway) { $table_pdf.=stripslashes($difficultAirway); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												<td class="bdrbtm" style="width:320px;">
													<table cellpadding="0" cellspacing="0" style="width:320px;">	
														<tr>
															<td style="width:200px;">Risk of blood loss (>500 ml)</td>
														</tr>
														<tr>
															<td style="width:200px;padding-left:10px; text-align:left;">';
																if($riskBloodLoss=="Yes"){
																	$table_pdf.='<b># of units available:&nbsp;</b>';
																	$table_pdf.=htmlentities(stripslashes($bloodLossUnits)); 
																	if(!$bloodLossUnits){$table_pdf.="____";}
																}else{ $table_pdf.="&nbsp;"; }
																
															$table_pdf.='
															</td>
														</tr>
													</table>
												</td>
												<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
												 if($riskBloodLoss) { $table_pdf.=stripslashes($riskBloodLoss);}else{$table_pdf.="__"; }
												$table_pdf.='</td>
												
												
												<td class="bdrbtm" style="text-align:left;width:320px; ">Anesthesia safety check completed</td>
												<td class="bdrbtm cbold" style="width:50px;"> ';
												if($anesthesiaSafety) { $table_pdf.=stripslashes($anesthesiaSafety); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>	
									<td style="width:740px; border-bottom:dashed 1px #C0C0C0" valign="middle"><b>Briefing:</b></td></tr>
									
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												
												<td class="bdrbtm" style="text-align:left;width:320px; ">All members of the team have discussed care plan and addressed concerns</td>
												<td class="bdrbtm cbold" style="width:50px;"> ';
												if($allMembersTeam) { $table_pdf.=stripslashes($allMembersTeam); }else{$table_pdf.="___"; }
												$table_pdf.='</td>
												<td class="bdrbtm" style="width:320px;">&nbsp;</td>
												<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
												$table_pdf.='&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					';
					}
					
					
	$table_pdf.='				
					<tr>
						<td style="width:150px;" class="bdrtop bdrbtm  pl5 bold">Patient Verified:&nbsp;';
						if($patientVerified!=''){
							$table_pdf.=$patientVerified."&nbsp;&nbsp;";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">Patient Height:&nbsp;';
						if($patientHeight!=''){
							$table_pdf.="<b>".$patientHeight."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">Patient Weight:&nbsp;';
						if($patientWeight!=''){
							$table_pdf.="<b>".$patientWeight."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">BMI:&nbsp;';
						if($BMIvalue!=''){
							$table_pdf.="<b>".$BMIvalue."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:700px;" class="pl5 bdrbtm"><b>Procedure Verified&nbsp;&nbsp;</b>';
						if(trim($patient_primary_procedure)!=''){
							$table_pdf.='Primary Proc.:&nbsp;<b>'.$patient_primary_procedure."</b>&nbsp;&nbsp;&nbsp;";
						}
						if($patient_secondary_procedure!='' && $patient_secondary_procedure!='N/A'){
								$table_pdf.='Secondary Proc:&nbsp;<b>'.$patient_secondary_procedure."</b>";
						}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm bgcolor bold pl5">Allergies</td>
						<td colspan="2" style="width:350px;" class="bdrbtm bgcolor bold pl5">Medication</td>
					</tr>';
					//allergies & meds
					$allrgiesQry=("select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId");
					$allergiesRes = imw_query($allrgiesQry);
					$allergiesnum=@imw_num_rows($allergiesRes);
					$table_pdf.='
					<tr>
						<td colspan="2" style="width:350px;vertical-align:top;">
							<table style="width:350px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
									<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
								</tr>';
								if($allergiesnum>0){
									while($detailsAllergy=@imw_fetch_array($allergiesRes)){
										$table_pdf.='
										<tr>
											<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['allergy_name'])).'</td>
											<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['reaction_name'])).'</td>
										</tr>';
									}				
								}else{
								$table_pdf.='
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
										<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
										<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
										<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
									</tr>
									';	
								}
							$table_pdf.='	
							</table>
						</td>';
						$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
						$medsRes=imw_query($medsQry);
						$medsnum=@imw_num_rows($medsRes);
						$table_pdf.='
						<td colspan="2" style="width:350px;">
							<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:125px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
										</tr>';
								if($medsnum>0){
									while($detailsMeds=imw_fetch_array($medsRes)){	
									$table_pdf.='
										<tr>
											<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_name']).'</td>
											<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_desc']).'</td>
											<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_sig']).'</td>
										</tr>';														
									}
								}else {
									for($q=1;$q<=3;$q++) {
										$table_pdf.='
										<tr>
											<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
										</tr>';
									}
								}
						$table_pdf.='		
							</table>
						</td>
					</tr>
					<tr>
						<td class="pl5 bdrbtm" colspan="4" style="width:700px;"><b>Patient Reassessed:&nbsp;</b>';
							if($PatientReassessed){
								$table_pdf.=$PatientReassessed;
							}else{
								$table_pdf.="____";
							}
						$table_pdf.='
						&nbsp;&nbsp;&nbsp;<b>Machine &amp; Equipment Completed:&nbsp;</b>';
							if($MachineEquipment){
								$table_pdf.=$MachineEquipment;
							}else{
								$table_pdf.="____";
							}
						$table_pdf.='
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Anesthesia Class:&nbsp;</b>';
						if($anesthesiaClass!=''){
							$valueClass="____";;
							if($anesthesiaClass=="one"){
								$valueClass="I";
							}
							elseif($anesthesiaClass=="two"){
								$valueClass="II";
							}
							elseif($anesthesiaClass=="three"){
								$valueClass="III";
						   	}
							if($valueClass){
								$table_pdf.=$valueClass;
							}
						}
						
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:700px;">
							<table style="width:700px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:150px;vertical-align:top;">
										<table style="width:150px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:150px;" class="bdrbtm">
												<b>Start Time:&nbsp;</b>';
												if($startTime && $startTime!="00:00 AM" && $startTime!="00:00 PM"){
													$table_pdf.=$startTime;
												}else{$table_pdf.="_____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:150px;" class="bdrbtm">
												<b>Stop Time:&nbsp;</b>';
												if($stopTime && $stopTime!="00:00 AM" && $stopTime!="00:00 PM"){
													$table_pdf.=$stopTime;
												}else{$table_pdf.="_____";}
												$table_pdf.='
												</td>
											</tr>';
											if($macValue!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
													<b>MAC:&nbsp;</b>';
													if($macValue){
														$table_pdf.=ucwords($macValue);
													}else{$table_pdf.="_____";}
													$table_pdf.='
													</td>
												</tr>';
												
											}
											if($millarValue!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Miller:&nbsp;</b>';
														if($millarValue){
															$table_pdf.=ucwords($millarValue);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($etTubeSize!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>ET Tube Size:&nbsp;</b>';
														if($etTubeSize){
															$table_pdf.=ucwords($etTubeSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($lmaSize!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>LMA Size:&nbsp;</b>';
														if($lmaSize){
															$table_pdf.=ucwords($lmaSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($mask!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>LMA Size:&nbsp;</b>';
														if($lmaSize){
															$table_pdf.=ucwords($lmaSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($mask!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Mask:&nbsp;</b>';
														if($mask){
															$table_pdf.="Yes";
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';	
											}
											if($teethUnchanged!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Teeth Unchanged:</b>';
														if($teethUnchanged){
															$table_pdf.=$teethUnchanged;
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';		
											}
											$table_pdf.='
												<tr>
													<td style="width:150px;" class="bgcolor bold">Monitor</td>
												</tr>';
											if($Monitor_ekg!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>EKG:&nbsp;</b>';
														if($Monitor_ekg){
															$table_pdf.="Yes";
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>ETCO<sub>2</sub>:&nbsp;</b>';
													if($Monitor_etco2){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>O<sub>2</sub>Sat:&nbsp;</b>';
													if($Monitor_etco2Sat){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>Temp:&nbsp;</b>';
													if($Monitor_o2Temp){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;height:20px;">
													<b>PNS:&nbsp;</b>';
													if($Monitor_PNS){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>
										</table>
									</td>
									<td style="width:400px;text-align:center;border:1px solid #C0C0C0;">';
									if($genAnesSignApplet!='' && file_exists('new_html2pdf/tess.jpg')){
										$table_pdf.='<img src="../new_html2pdf/tess.jpg" style="width:380px;height:300px;">';
									}else if(!empty($elem_cnvs_anes_drw_file)){										
										$updir = "admin";
										$img_src = $updir."/".$elem_cnvs_anes_drw_file;
										if(file_exists($img_src)){  $table_pdf.='<img src="../'.$img_src.'" style="width:380px;">';}
									}
									$table_pdf.='
									</td>
									<td style="width:150px;vertical-align:top;padding:0px;">
										<table style="width:170px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:165px;" class="bgcolor bold pl5">Arms</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Trucked:&nbsp;</b>';
												if($armsTuckedLeft!=""){
													$table_pdf.="<b>L</b>&nbsp;Yes&nbsp;&nbsp;";
												}
												if($armsTuckedRight!=""){
													$table_pdf.="<b>R</b>&nbsp;Yes";
												}
											$table_pdf.='</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Armboards:&nbsp;</b>';
												if($armsArmboardsLeft!=""){
													$table_pdf.="<b>L</b>&nbsp;Yes";
												}
												if($armsArmboardsRight!=""){
													$table_pdf.="&nbsp;<b>R</b>&nbsp;Yes";
												}
											$table_pdf.='</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bgcolor bold pl5">Eyes</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Taped:&nbsp;</b>';
												if($eyeTapedLeft!=""){
													$table_pdf.=$eyeTapedLeft;
												}else{$table_pdf.="______";}
											$table_pdf.='</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Lubed:&nbsp;</b>';
												if($eyeLubedLeft!=""){
													$table_pdf.=$eyeLubedLeft;
												}else{$table_pdf.="______";}
											$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:170px;font-size:12px;padding-left:2px;" class="bdrbtm"><b>Pressure Points Padded:&nbsp;</b>';
												if($pressurePointsPadded!=''){
													$table_pdf.="Yes";
												}else{$table_pdf.="_";}
											$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>BSS:&nbsp;</b>';
												if($bss!=''){
													$table_pdf.=$bss;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Warming:&nbsp;</b>';
												if($warning!=''){
													$table_pdf.=$warning;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Device temp:&nbsp;</b>';
												if($temp!=''){
													$table_pdf.=$temp;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="pl5"><b>Graph Comments:</b>';
												if(trim($graphComments)){
													$table_pdf.="<br>".$graphComments;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
										</table>
									</td>									
								</tr>
							</table>
						</td>
					</tr>
					
						';
						// Vital Sign Grid Printing Section - Start
						
							if($vitalSignGridStatus)
							{
									$table_pdf.='<tr>';
									$table_pdf.='<td style="width:700px;" class="bdrbtm" colspan="4" valign="top"><table style="width:700px; " cellpadding="0" cellspacing="0">';
									
									$table_pdf.='<tr>';
									$table_pdf.='<td class="bgcolor bold bdrbtm" colspan="8" style="width:700px; vertical-align:middle; ">&nbsp;&nbsp;Vital Signs</td>';
									$table_pdf.='</tr>';
									
									
									$table_pdf.='<tr>';	
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Time</b></td>';
									$table_pdf.='<td colspan="2" class="bdrbtm" style="width:200px; border-right:1px solid #C0C0C0; vertical-align:middle;">&nbsp;<b>B/P</b></td>';
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Pulse</b></td>';
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>RR</b></td>';
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Temp<sup>O</sup> C</b></td>';
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>EtCO<sub>2</sub></b></td>';
									$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
									$table_pdf.='</tr>';
									$table_pdf.='<tr>';	
									
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
									
									$table_pdf.='</tr>';
										
									
									
									$condArr		=	array();
									$condArr['confirmation_id']	=	$pConfId ;
									$condArr['chartName']				=	'genral_anesthesia_form' ;
									
									$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
						
									$gCounter	=	0;
									if(is_array($gridData) && count($gridData) > 0  )
									{
										foreach($gridData as $gridRow)
										{		
											$gCounter++;
											//$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
											$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
											$fieldValue2= $gridRow->systolic;
											$fieldValue3= $gridRow->diastolic;
											$fieldValue4= $gridRow->pulse;
											$fieldValue5= $gridRow->rr;
											$fieldValue6= $gridRow->temp;
											$fieldValue7= $gridRow->etco2;
											$fieldValue8= $gridRow->osat2;
											
											$table_pdf.='<tr>';	
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue1.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue2.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue3.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue4.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue5.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue6.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue7.'</td>';
											$table_pdf.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
											$table_pdf.='</tr>';
												
										}
									}
									
									for($loop = $gCounter; $loop < 3; $loop++)
									{
											$table_pdf.='<tr>';	
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_pdf.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;</td>';		
											$table_pdf.='</tr>';
									}
									$table_pdf.='</table></td>';
									$table_pdf.='</tr>';
												
							}
							
							// Vital Sign Grid Printing Section - End
							
							
						$table_pdf.='<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm bdrtop"><b>Evaluation:&nbsp;</b>';
						if(trim($evaluation)){
							$table_pdf.=trim($evaluation);
						}else{$table_pdf.="_______";}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm bdrtop"><b>Comments:&nbsp;</b>';
						if(trim($comments)){
							$table_pdf.=trim($comments);
						}else{$table_pdf.="_______";}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm"><b>PACU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BP&nbsp;</b>';
							if($bp!=''){
								$table_pdf.=$bp."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>P</b>&nbsp;";
							if($P!=''){
								$table_pdf.=$P."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>RR</b>&nbsp;";
							if($P!=''){
								$table_pdf.=$rr."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>%SAO<sub>2</sub></b>&nbsp;";
							if($sao!=''){
								$table_pdf.=$sao."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm">';
						$table_pdf.='<b>Stable Cardio Respiratory:&nbsp;</b>';
							if($StableCardioRespiratory!=''){
								$table_pdf.=$StableCardioRespiratory;
							}else{$table_pdf.="___&nbsp;&nbsp;";}	
						$table_pdf.='	
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm pl5">';
							
							if($signAnesthesia1Status=="Yes"){
								$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia1Name;
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewgenAnesRow["signAnesthesia1DateTime"]);
							}else {
								$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>________";
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm pl5">';
						if($relivedNurseId>0){
							$qrnurse="select lname,fname from users where usersId='$relivedNurseId'";
							$qrRes=imw_query($qrnurse) ;	
							$nursenameres=imw_fetch_array($qrRes);
							$nurseName=$nursenameres['lname'].','.$nursenameres['fname'];	
						}	
						$table_pdf.="<b>Relief Nurse:&nbsp;</b>";
						if($relivedNurseId>0){
							$table_pdf.=$nurseName;
						}else{
							$table_pdf.="________";
						}
						$table_pdf.='
						</td>
					</tr>';
					
  				$table_pdf.='</table>';

//die($table_pdf);
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table_pdf);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
?>	

 <form name="printgen_anes" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn()
	{
		document.printgen_anes.submit();
	}
</script>
<?php 
if($genAnesFormStatus=='completed' || $genAnesFormStatus=='not completed') {
?>
<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>