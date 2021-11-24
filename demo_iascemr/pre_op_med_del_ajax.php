<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

$objManageData = new manageData;
	
	$patient_id = $_REQUEST['patient_id'];
	$pConfId 	= $_REQUEST['pConfId'];
	$frmVersion	=	$_REQUEST['frmVersion'];
	
	$containerCSS	=	($frmVersion == '1') ? 'col-md-7 col-lg-7' : 'col-md-12 col-lg-12';
	$displayCSS		=	($frmVersion == '1') ? '' : 'style="display:none"';
	
	if(!$patient_id) {
		$patient_id = $_SESSION['patient_id'];
	}
	if(!$pConfId) {
		$pConfId	= $_SESSION['pConfId'];
	}

	$delId=$_GET['delId'];
	$preDefined=$_GET['medication_name'];
	$strength=$_GET['strength'];
	$directions=$_GET['directions'];
	
	$sqlStr = "DELETE FROM patientpreopmedication_tbl 
				where patientPreOpMediId = '$delId'";
	imw_query($sqlStr);
	
// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	$getPreOpPhyDetails = $objManageData->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfId);	
	if($getPreOpPhyDetails){
		$preOpPhysicianOrdersId = $getPreOpPhyDetails->preOpPhysicianOrdersId;
		$ivSelection = $getPreOpPhyDetails->ivSelection;
		$ivSelectionSide = $getPreOpPhyDetails->ivSelectionSide;
		$honanBallon = $getPreOpPhyDetails->honanBallon;
		$honanBallonTime = $getPreOpPhyDetails->honanBallonTime;
		$preOpOrdersOther = $getPreOpPhyDetails->preOpOrdersOther;		
		$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST['pConfId']);
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

		/*	if($procedureFound=='true'){*/
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
	            if(!$getPreOpPhyDetails){
					
					
										unset($conditionArr);											
										if(count($preOpOrdersArr)>0){
											foreach($preOpOrdersArr as $preDefined){			
												$preOpMediDetails = $objManageData->getRowRecord('preopmedicationorder', 'medicationName', $preDefined);
												$strength = $preOpMediDetails->strength;
												$directions = $preOpMediDetails->directions;
												++$seq;
									?>
                                    <div class="row medicine_row">
                                        <div class="<?=$containerCSS?> col-sm-12 col-xs-12">
                                			
                                            <div class="row" id="row<?php echo $seq; ?>">
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>" />                                                                            </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="" name="directionArr[]" value="<?php echo stripslashes($directions); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                     <div class="row">
                                                        <div class="col-md-8 col-lg-8 col-xs-8 col-sm-8">
                                                            <input class="form-control" type="text" placeholder="" name="timemedsArr[]" value="<?php echo $timemeds; ?>"/>		                                                        </div>	
                                                        <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center">
                                                           <a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$frmVersion?>');">X</a>			                                            			</div>	
                                                     </div>
                                                </div>
                                            </div>
                                        </div>	   
                                    </div>
                                     <?php
								}
							}
					   
					}else if($getPreOpPhyDetails){
						
											if(count($preOpPatientDetails)>0){
												
												?>
                    <input type="hidden" id="bp" name="bp_hidden">
					<?php if(!$medicationStartTimeVal) $medicationStartTimeVal = "";//date('h:i A'); ?>
                    <input type="hidden" name="hourVal" id="hourVal" value="<?php print substr($medicationStartTimeVal,0,2); ?>" >
                    <input type="hidden" name="minuteVal" id="minuteVal" value="<?php print substr($medicationStartTimeVal,3,-2); ?>" >
                    <input type="hidden" name="statusVal" id="statusVal" value="<?php print substr($medicationStartTimeVal,5); ?>" >
										<?php
                                        foreach($preOpPatientDetails as $detailsOfMedication){
                                                $parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
                                                if(trim($detailsOfMedication->medicationName)) {
                                                    $preDefined = $detailsOfMedication->medicationName;
                                                    $strength = $detailsOfMedication->strength;
                                                    $directions = $detailsOfMedication->direction;
                                                    $timemeds = $objManageData->getTmFormat($detailsOfMedication->timemeds);
                                                    $timemeds1=array();
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds1);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds2);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds3);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds4);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds5);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds6);
                                                    
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds7);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds8);
                                                    $timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds9);
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
                                                    $freq = $freq > 6 ? 6 : $freq;
                                                    $minsDir = explode('Q',strtoupper($dir[1]));
                                                    //if(count($minsDir)<=1) $freq = '';
                                                    $min=substr(trim($minsDir[1]),0,-3);
                                                    
                                                
                                                ?>
                                             
                                             <div class="row medicine_row">
                                                <div class="<?=$containerCSS?> col-sm-12 col-xs-12">
                                                    <div class="row" id="row<?php echo $seq; ?>">
                                                        <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                            <input type="hidden" name="parientPreOpMediOrderId[]" id="IDS<?php echo $parientPreOpMediOrderId; ?>" value="<?php echo $parientPreOpMediOrderId; ?>">
                                                            <input class="form-control" type="text" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                                        </div>
                                                        <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                            <input class="form-control" type="text"  name="strengthArr[]" value="<?php echo stripslashes($strength); ?>"/>                                                                 </div>
                                                        <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                            <input class="form-control" type="text" name="directionArr[]" value="<?php echo stripslashes($directions); ?>">
                                                        <input type="hidden" name="feq[]" value="<?php print $freq; ?>" >
                                                        <input type="hidden" name="min[]" value="<?php print $min; ?>" >
                                                        </div>
                                                        <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                             <div class="row">
                                                                <div class="col-md-8 col-lg-8 col-xs-8 col-sm-8" <?=$displayCSS?>>
                                                                    <input class="form-control" type="text" placeholder="" id="starttime<?php echo $k;?>[]" name="timemedsArr[]" value="<?php echo $timemeds;//echo $medicationStartTimeVal; ?>" onClick="if(!this.value) { return displayTimeAmPm('starttime<?php echo $k;?>[]');}" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds";?>');">		                                                        </div>	
                                                                <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center">
                                                                   <a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$frmVersion?>');">X</a>			                                    </div>	
                                                             </div>
                                                        </div>
                                                    </div>
                                                </div>	   
                                                <div class="clearfix visible-sm margin_adjustment_only"></div>
                                                <div class="col-md-5 col-lg-5 col-sm-12 col-xs-12" <?=$displayCSS?>>
                                                    <div class="row">
                                                    <?php if($freq>1 ){ 
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
                                                      <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                                            <input class="form-control" type="text"name="starttimeExtra<?php echo $k;?>[]" id="starttimeExtraId<?php echo $k.$tdNew?>" onClick="if(!this.value){return displayTimeAmPm('starttimeExtraId<?php echo $k.$tdNew?>');}" value="<?php  echo($timemeds1[$tdNew]);//print_r($$timemedsram[0]);?>" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds".$td;?>','<?=$frmVersion?>');" />                                               					</div>
                                                     
                                                    <?php } } ?>        
                                                     </div>
                                                </div>	   
                                            </div>
                                        <?php
												}
                                        }
                                        
                                         ?>
                                    
                                <?php 
                                    }
                                
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
        