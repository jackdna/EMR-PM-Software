<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;

		$_GET['preColor'] = "#".$_GET['preColor'];
	//SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
		//if($_POST['hidd_saveVitalSign']=='yes'){
			$hidd_vitalSignBp = $_GET['vitalSignBP_main'];
			$hidd_vitalSignP = $_GET['vitalSignP_main'];
			$hidd_vitalSignR = $_GET['vitalSignR_main'];
			$hidd_vitalSignO2SAT = $_GET['vitalSignO2SAT_main']; 
			$hidd_vitalSignTime1 = $_GET['vitalSignTime_main']; 
			$hidd_vitalSignTemp = $_GET['vitalSignTemp_main']; 
			//vitalSignO2SAT_main
	//vitalSignTime saved in database
	 
	     /*  
		   $time_splitvitalSign = explode(" ",$hidd_vitalSignTime1);
	       
		if($time_splitvitalSign[1]=="PM" || $time_splitvitalSign[1]=="pm") {
			
			$time_splitvitalSigntime = explode(":",$time_splitvitalSign[0]);
			$hidd_vitalSignTimeIncr=$time_splitvitalSigntime[0]+12;
			$hidd_vitalSignTime = $hidd_vitalSignTimeIncr.":".$time_splitvitalSigntime[1].":00";
			
		}elseif($time_splitvitalSign[1]=="AM" || $time_splitvitalSign[1]=="am") {
		    $time_splitvitalSigntime = explode(":",$time_splitvitalSign[0]);
			$hidd_vitalSignTime=$time_splitvitalSigntime[0].":".$time_splitvitalSigntime[1].":00";
		}*/
		$hidd_vitalSignTime = $objManageData->setTmFormat($hidd_vitalSignTime1);
	//vitalSignTime saved in database 
			
			if($hidd_vitalSignBp!='' || $hidd_vitalSignP!='' || $hidd_vitalSignR!='' || $hidd_vitalSignO2SAT!='')
			{
			$SaveVSignQry = "insert into `vitalsign_tbl` set 
										vitalSignBp = '$hidd_vitalSignBp',
										vitalSignP = '$hidd_vitalSignP', 
										vitalSignR = '$hidd_vitalSignR',
										vitalSignO2SAT = '$hidd_vitalSignO2SAT',
										vitalSignTime = '$hidd_vitalSignTime', 
										vitalSignTemp = '$hidd_vitalSignTemp', 
										ascId='".$_REQUEST["ascId"]."', 
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
										
			$SaveVitalSignRes = imw_query($SaveVSignQry) or die(imw_error());
			}
		//}
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl		
		
	?>
		   <!-- <tr bgcolor="<?php //echo $bg_color_post_op_nurse;?>" class="alignLeft" id="vital_sign_main_id">  -->
		  	
			<!-- <td > --> 
		<?php
			$ViewPostopNurseVitalSignQry = "select * from `vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
			$ViewPostopNurseVitalSignRes = imw_query($ViewPostopNurseVitalSignQry) or die(imw_error()); 
			$ViewPostopNurseVitalSignNumRow = imw_num_rows($ViewPostopNurseVitalSignRes);
			if($ViewPostopNurseVitalSignNumRow>0) {
				$k=1;
				while($ViewPostopNurseVitalSignRow = imw_fetch_array($ViewPostopNurseVitalSignRes)) {
					$vitalsign_id=$ViewPostopNurseVitalSignRow["vitalsign_id"];
					$vitalSignBp = $ViewPostopNurseVitalSignRow["vitalSignBp"];
					$vitalSignP = $ViewPostopNurseVitalSignRow["vitalSignP"];
					$vitalSignR = $ViewPostopNurseVitalSignRow["vitalSignR"];
					$vitalSignO2SAT = $ViewPostopNurseVitalSignRow["vitalSignO2SAT"];
					//$vitalSignTime = $ViewPostopNurseVitalSignRow["vitalSignTime"];
					$vitalSignTime = $objManageData->getTmFormat($ViewPostopNurseVitalSignRow["vitalSignTime"]);
					$vitalSignTemp = $ViewPostopNurseVitalSignRow["vitalSignTemp"];

					//CODE TO SET VITAL SIGN TIME
						/*
						$time_split2 = explode(":",$vitalSignTime);
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
						$vitalSignTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
						*/
					//END CODE TO SET VITAL SIGN TIME		
			
					if($k%2==0) {
						$bg_color_post_op_nurse = "#FDFAEB";
					}else {
						$bg_color_post_op_nurse = "#FFFFFF";
					} 
					 $top=$k*24+25;
		?>
                <div class="inner_safety_wrap" id="id=vs_<?php echo $vitalsign_id; ?>">
					<div class="row">
						  <!--<div class="col-md-2 visible-md"></div>
						  <div class="col-md-2 visible-lg"></div>-->
						<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    	<div class="rown">
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                            <Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">BP</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5 text-center">
                                    <span class="inner_span"><?php echo $vitalSignBp;?></span>                 
                                </span>
                            </Div>
                        </div>
                       	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 plr5">
                        	<div class="rown">
                          	
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 plr5"> 
                            	<Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">P</label>
                                <Span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5 text-center">
                                  <span class="inner_span"><?php echo $vitalSignP;?></span>             
                                </Span>
                              </Div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 plr5">   
                              <Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">R</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                  <span class="inner_span"><?php echo $vitalSignR;?></span>    
                                </span>
                            	</Div>
                           	</div> 
                            
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-5 plr5">
                            	<Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">O<sub>2</sub>SAT</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                  <span class="inner_span"><?php echo $vitalSignO2SAT;?></span>
                                </span>
                              </Div>
                           	</div>
                            
                         	</div>   
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                            <Div class="rown nowrap">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">Time</label>
                                <Span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                    <span class="inner_span"><?php echo $vitalSignTime;?></span>
                                </Span>
                            </Div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                        	<Div class="rown">
                            <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">Temp</label>
                            <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                              <span class="inner_span"><?php echo $vitalSignTemp;?></span>    
                            </span>
                          </Div>
                       	</div>
                        
                        <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 plr5 text-center">
                        	 <a href="javascript:void(0)" class="btn btn-danger" style="margin:0" onClick="delentry(<?php echo $vitalsign_id; ?>);">  X </a>

                       </div>
                   		</div>    
                    </Div>
					</div>
				</div>
                
		<?php		
				$k++;
				} 
			}else {
		?>
				
				
		<?php	
			}
		?>