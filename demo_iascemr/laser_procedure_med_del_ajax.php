<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

$objManageData = new manageData;

	$patient_id = $_SESSION['patient_id'];
	//$ascId = $_SESSION['ascId'];
	$pConfId = $_SESSION['pConfId'];
	$delId=$_GET['delId'];
	$preDefined=$_GET['medication_name'];
	$strength=$_GET['strength'];
	$directions=$_GET['directions'];
	
	$sqlStr = "DELETE FROM laserprocedure_medication_tbl  
				where patientlaserproMediId = '$delId'";
	imw_query($sqlStr);
	
/*	
	$sqldel=imw_query("select * from  patientpreopmedication_tbl where patient_confirmation_id='$pConfId'");
	//echo "select * from  patientpreopmedication_tbl where patient_confirmation_id='$pConfId'";
		while($resDel=imw_fetch_array($sqldel))
	{
	$preDefined=$resDel['medicationName'];
	$strength=$resDel['strength'];
	$directions=$resDel['direction']; 
*/	
	
// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	$getPreOpPhyDetails = $objManageData->getRowRecord('laser_procedure_patient_table ', 'confirmation_id', $pConfId);	
	if($getPreOpPhyDetails){
		$preOpPhysicianOrdersId = $getPreOpPhyDetails->laser_procedureRecordID;
		//$ivSelection = $getPreOpPhyDetails->ivSelection;
		//$ivSelectionSide = $getPreOpPhyDetails->ivSelectionSide;
		//$honanBallon = $getPreOpPhyDetails->honanBallon;
		//$honanBallonTime = $getPreOpPhyDetails->honanBallonTime;
		$preOpOrdersOther = $getPreOpPhyDetails->laser_other_pre_medication;		
		$preOpPatientDetails = $objManageData->getArrayRecords('laserprocedure_medication_tbl', 'laserproOrderId', $preOpPhysicianOrdersId);
	}else{
		//GETTING SURGEON PROFILE TO SHOW FIEST VIEW "$surgeonId"
			unset($conditionArr);
			$conditionArr['surgeonId'] = $surgeonId;
			$conditionArr['del_status'] = '';
			$profilesDetail = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			if($profilesDetail){
				foreach($profilesDetail as $profile){
					$surgeonProfileId = $profile->surgeonProfileId;
					$proceduresList = $profile->procedures;
					$preOpOrder = $profile->preOpOrders;
					if(strpos($proceduresList, ", ")){
						$proceduresArray = explode(", ", $proceduresList);
						if(in_array(trim($patient_primary_procedure), $proceduresArray)){
							$procedureFound = 'true';
							break;
						}
					}else{
						if(trim($patient_primary_procedure)==trim($proceduresList)){
							$procedureFound = 'true';
							break;
						}
						$proceduresArray[] = $proceduresList;
					}
				}
			}	

			/*if($procedureFound=='true'){*/
				$profileIDToShow = $surgeonProfileId;
			/*}else{
				// SHOW DEFAULT PROFILE
					unset($conditionArr);
					$conditionArr['surgeonId'] = $surgeonId;
					$conditionArr['defaultProfile'] = '1';
					$defaultProfilesDetail = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
					if(count($defaultProfilesDetail)>0){
						foreach($defaultProfilesDetail as $profileDetails){
							$profileIDToShow = $profileDetails->surgeonProfileId;
						}
					}
				// SHOW DEFAULT PROFILE
			}*/
			
			// PROFILE TO DISPLAY
				unset($conditionArr);
				$conditionArr['surgeonProfileId'] = $profileIDToShow;
				$showProfileDetails = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			// PROFILE TO DISPLAY
			
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			if(count($showProfileDetails)>0){
				foreach($showProfileDetails as $profile){
					$proceduresList = $profile->procedures;
					$preOpOrders = $profile->preOpOrders;
					if(strpos($preOpOrders, ", ")){
						$preOpOrdersArr = explode(", ", $preOpOrders);
					}else{
						$preOpOrdersArr[] = $preOpOrders;
					}
				}
			}
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			
		//GETTING SURGEON PROFILE TO SHOW FIEST VIEW "$surgeonId"
	}
// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT	
	?>					
<!-- <td colspan="5" id="preOpPhysicianShowAjaxId"> -->
	                                <div  style="position:relative;height:150px;border:2px;overflow:auto;">
                                  <table border="0" cellpadding="0" cellspacing="0" width="460">
                                    <?php
										if(!$getPreOpPhyDetails){
											unset($conditionArr);											
											if(count($preOpOrdersArr)>0){
												foreach($preOpOrdersArr as $preDefined){			
													$preOpMediDetails = $objManageData->getRowRecord('preopmedicationorder', 'medicationName', $preDefined);
													$strength = $preOpMediDetails->strength;
													$directions = $preOpMediDetails->directions;
													++$seq;
													?>
													
                                    <tr id="DD<?php echo $seq; ?>">
                                      <td class="text_10"></td>
                                      <td class="text_10" ><input size="25" type="text" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"></td>
                                      <td class="text_10" ><input size="25" type="text" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>"></td>
                                      <td class="text_10" ><input size="25" type="text" name="directionArr[]" value="<?php echo stripslashes($directions); ?>"></td>
                                      <td class="text_10" ><input size="8" maxlength="8" type="text" name="timemedsArr[]" value="<?php echo $timemeds; ?>"></td>
                                      <td class="text_10b"><img src="images/tpixel.gif" width="7"><img src="images/close.jpg" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>');"> </td>
                                    </tr>
                                    <?php
												}
											}
										}else if($getPreOpPhyDetails){
										?>
                                    <?php
											if(count($preOpPatientDetails)>0){
												foreach($preOpPatientDetails as $detailsOfMedication){
													$parientPreOpMediOrderId = $detailsOfMedication->patientlaserproMediId;
													$preDefined = $detailsOfMedication->medicationName;
													$strength = $detailsOfMedication->strength;
													$directions = $detailsOfMedication->direction;
													$timemeds = $detailsOfMedication->timemeds;
													$timemeds1=array();
													$timemeds1[] = $detailsOfMedication->timemeds1;
													$timemeds1[] = $detailsOfMedication->timemeds2;
													$timemeds1[] = $detailsOfMedication->timemeds3;
													$timemeds1[] = $detailsOfMedication->timemeds4;
													$timemeds1[] = $detailsOfMedication->timemeds5;
													$timemeds1[] = $detailsOfMedication->timemeds6;
													$timemeds1[] = $detailsOfMedication->timemeds7;
													$timemeds1[] = $detailsOfMedication->timemeds8;
													$timemeds1[] = $detailsOfMedication->timemeds9;
													
													$qry=imw_query ("select medicationStartTime from laser_procedure_patient_table  where confirmation_id=$pConfId"); 
	                                          		list($medicationStartTimeVal) = imw_fetch_array($qry);
													/*convert got time from db
													 $Time=$timemeds; 
													$time_split2 = explode(":",$Time);
													if($time_split2[0]>12) {
													$am_pm2 = "PM";
													}else {
													$am_pm2 = "AM";
													}
													if($time_split2[0]>=13) {
													$time_split2[0] = $time_split2[0]-12;
													if(strlen($time_split2[0]) == 1) {
													$time_split2[0] = "0".$time_split2[0];
													}
													}else {
													//DO NOTHNING
														}
													// $MedsTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
													
													
													//End convert got time from db*/
													//$MedsTime=$timemeds;

													++$k;
												if($k==1)
												{
												 $disptr='block';
												}	
												else
												{
												$disptr='none';
												}
												
												$dir = explode('X',strtoupper($directions));
												$freq = substr(trim($dir[1]),0,1);
												$freq = $freq > 5 ? 5 : $freq;
												$minsDir = explode('Q',strtoupper($dir[1]));
												//if(count($minsDir)<=1) $freq = '';
												$min=substr(trim($minsDir[1]),0,-3);
												
												?>
											<tr height="25"  style="display:<?php echo $disptr;?>">
											<td width="1"></td>
											<td width="115" align="center" class="text_10b">Medication</td>
											<td width="70" align="center" class="text_10b">Strength</td>
											<td width="165" align="center" class="text_10b">Directions</td>
											<td width="203" align="left" class="text_10b">
												<?php 
													if(!$medicationStartTimeVal) $medicationStartTimeVal = date('h:i A'); 
													$hourVals = substr($medicationStartTimeVal,0,2);
													$minuteVals = substr($medicationStartTimeVal,3,-2);
													$statusVals = substr($medicationStartTimeVal,5);
												?>
												<input type="hidden" id="bp" name="bp_hidden">
												<input type="hidden" name="hourVal" value="<?php print $hourVals; ?>" >
												<input type="hidden" name="minuteVal" value="<?php print $minuteVals;?>" >
												<input type="hidden" name="statusVal" value="<?php print $statusVals; ?>" >
												<!-- <input type="text" size="8" name="startTimeVal[]" id="bp_temp3" onKeyUp="displayText3=this.value" onclick="getShowNewPos(250,350,'flag3');clearVal_c();" value="<?php  echo $medicationStartTimeVal; ?>" > -->
											</td>
											<td style="cursor:pointer;"><img src="images/tpixel.gif" width="7"><!-- <img src="images/arrow1.png" align="absmiddle" onClick="startTimeSet(); save_physician_time();"> --></td>
											<td colspan="4"></td>
											</tr>	
                                   <tr id="deltr<?php echo $k; ?>">
                                      <input type="hidden" name="parientPreOpMediOrderId[]" id="IDS<?php echo $parientPreOpMediOrderId; ?>" value="<?php echo $parientPreOpMediOrderId; ?>">
                                      <td class="text_10"></td>
                                      <td class="text_10" id="deltd1<?php echo $k; ?>"><input size="20" type="text" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"></td>
                                      <td class="text_10" id="deltd2<?php echo $k; ?>"><input size="10" type="text" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>"></td>
                                      <td class="text_10" id="deltd3<?php echo $k; ?>">
									  	<input size="25" type="text" name="directionArr[]" value="<?php echo stripslashes($directions); ?>">
										<input type="hidden" name="feq[]" value="<?php print $freq; ?>" >
										<input type="hidden" name="min[]" value="<?php print $min; ?>" >
										</td>
									  <td class="text_10" id="deltd5<?php echo $k; ?>"><input size="8" id="starttime<?php echo $k;?>[]" maxlength="8" type="text" name="timemedsArr[]" value="<?php echo $timemeds;//echo $medicationStartTimeVal; ?>" onClick="return displayTimeAmPm('starttime<?php echo $k;?>[]');" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds";?>');"></td>
                                      <td width="90" align="center" class="text_10b" id="deltd4<?php echo $k; ?>" > <img src="images/tpixel.gif" width="7"><img src="images/close.jpg" align="absmiddle"  onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>');"><img src="images/tpixel.gif" width="7"></td>
									  <?php
													
													
									if($freq>1 )
										{
										for($td=1;$td<$freq;$td++){
										
										$timeStatusArr = explode(':',$medicationStartTimeVal);
										$minsInt = $min * $td;
										$timeVar = $minsInt + substr($timeStatusArr[1],0,2);
										$timeStatus = substr($timeStatusArr[1],3);
										if($timeVar>=60)
										{
											$timeStatusArr[0]=$timeStatusArr[0]+1;
											$timeVar= $timeVar - 60;
										}
										if($timeStatusArr[0]>12)
										{
											$timeStatusArr[0]= $timeStatusArr[0]-12;
										}
										if($timeVar < 10)
										{
											$timeVar= '0'.$timeVar;
										}
										if($timeStatusArr[0]!='')
										{
										$tdTime = $timeStatusArr[0].':'.$timeVar.''.$timeStatus;
										}
										else
										{
										$tdTime=' ';
										}
										$tdNew = $td-1;
									?>
									  <td><img src="tpixel.gif" width="1"><input type="text" size="8" name="starttimeExtra<?php echo $k;?>[]" id="starttimeExtraId<?php echo $k.$tdNew?>" onClick="return displayTimeAmPm('starttimeExtraId<?php echo $k.$tdNew?>');" value="<?php echo($timemeds1[$tdNew]);//print_r($tdTime);?>" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds".$td;?>');"></td>
									<!--  <td><input type="text" size="8" name="time2" id="id2<?php echo $k; ?>" value="<?php echo $k; ?>"></td>
									  <td><input type="text" size="8" name="time3" id="id3<?php echo $k; ?>" value="<?php echo $k; ?>"></td>
									  <td><input type="text" size="8" name="time4" id="id4<?php echo $k; ?>"value="<?php echo $k; ?>"></td>	
									  <td><input type="text" size="8" name="time5" id="id5<?php echo $k; ?>" value="<?php echo $k; ?>"></td>
								   --><?php } } ?>
								   </tr>
										
										<?php
												
												}
											}
											?>
                                    <?php
										}
										?>
										<script language="javascript">
										function startTimeSet(){
											var direction = document.getElementsByName("directionArr[]");
											var freq = document.getElementsByName("feq[]");
											var mins = document.getElementsByName("min[]");
											var mins2 = 0;
											var mainDiv = '';
											var changeMin = '';											
											var curHour2 = 0;										
											var startTimeValObj = document.getElementsByName("startTimeVal[]");
											var timeVal = startTimeValObj(0).value;
											var d=1;
																						
											for(i=0;i<timeVal.length;i++){
												if(timeVal.charAt(i) == ':'){
													break;
												}
											}
											var startMin = ++i;
											var mainHour = timeVal.substr(0,--i);
											var curHour = '';
											var mainMins = timeVal.substr(startMin,2);
											//alert(startMin)
											var curMin = '';
											for(i=0;i<direction.length;i++,d++){
												var status = timeVal.substr(timeVal.length-2);
												
												if(status == 'AM'){
													changeStatus = 'PM';
												}
												else{
													changeStatus = 'AM';
												}
												curHour = mainHour;
												curMin = mainMins;
												mainDiv = freq(i).value;
												mins2 = mins(i).value;
												var fillTd = document.getElementsByName("starttime"+d+"[]");
												changeMin = curMin;
												curHour2 = curHour;
												for(u=0;u<fillTd.length;u++){
													if(u > 0){
														changeMin = parseInt(changeMin) + parseInt(mins2);
													}													
													if(changeMin >= 60){
														changeMin = changeMin - 60;
														curHour2 = ++curHour;														
													}													
													if(mainHour ==12 && curMin >00)
													{
													changeStatus=status;
													}
													else
													{
													if(u > 0){
														if(curHour2 >= 12){
															status = changeStatus;
														}
													}
													}
													if(curHour2 > 12){
														curHour2 = curHour2 - 12 ;
													}
													if(eval(curHour2) < 10){
														if(curHour2.length){
															curHour2 = "0"+curHour2.substr(1,2);
														}
														else{
															curHour2 = "0"+curHour2;
														}
													}													
													if(changeMin.length < 2 || changeMin < 10){
														if(changeMin.length){
															changeMin = "0"+changeMin.substr(1,2);
														}
														else{
															changeMin = "0"+changeMin;
														}
													}
													fillTimeVal = curHour2+":"+changeMin+" "+status;
													fillTd(u).value = fillTimeVal;
													if(u == 0){
														startTimeValObj(0).value = fillTimeVal;	
													}
												}
											}
										}										
										
										</script>
                                  </table>
                                </div>
								</td>
					   		</tr>