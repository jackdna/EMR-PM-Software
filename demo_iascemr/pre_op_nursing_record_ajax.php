<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
		
	//SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
		$hidd_vitalSignBp = trim($_GET['vitalSignBP_main']);
		$hidd_vitalSignP = trim($_GET['vitalSignP_main']);
		$hidd_vitalSignR = trim($_GET['vitalSignR_main']);
		$hidd_vitalSignO2SAT = trim($_GET['vitalSignO2SAT_main']);
		$hidd_vitalSignTemp = trim($_GET['vitalSignTemp_main']);
		$hidd_vitalSignTime = date('Y-m-d H:i:s');

		
		if($hidd_vitalSignBp!='' || $hidd_vitalSignP!='' || $hidd_vitalSignR!='' || $hidd_vitalSignO2SAT!='' || $hidd_vitalSignTemp!='')
		{
			$SaveVSignQry = "insert into `preopnursing_vitalsign_tbl` set 
										vitalSignBp = '$hidd_vitalSignBp',
										vitalSignP = '$hidd_vitalSignP', 
										vitalSignR = '$hidd_vitalSignR',
										vitalSignO2SAT = '$hidd_vitalSignO2SAT',
										vitalSignTemp = '$hidd_vitalSignTemp',
										vitalSignTime='$hidd_vitalSignTime',
										ascId='".$_REQUEST["ascId"]."', 
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
										
			$SaveVitalSignRes = imw_query($SaveVSignQry) or die(imw_error());
		}
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
		
	?>
	
		<?php
			$ViewPreopNurseVitalSignQry = "select * from `preopnursing_vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
			$ViewPreopNurseVitalSignRes = imw_query($ViewPreopNurseVitalSignQry) or die(imw_error()); 
			$ViewPreopNurseVitalSignNumRow = imw_num_rows($ViewPreopNurseVitalSignRes);
			if($ViewPreopNurseVitalSignNumRow>0) {
				$m=1;
				while($ViewPreopNurseVitalSignRow = imw_fetch_array($ViewPreopNurseVitalSignRes)) {
					$vitalsign_id=$ViewPreopNurseVitalSignRow["vitalsign_id"];  
					$vitalSignBp = $ViewPreopNurseVitalSignRow["vitalSignBp"];
					$vitalSignP = $ViewPreopNurseVitalSignRow["vitalSignP"];
					$vitalSignR = $ViewPreopNurseVitalSignRow["vitalSignR"];
					$vitalSignO2SAT = $ViewPreopNurseVitalSignRow["vitalSignO2SAT"];
					$vitalSignTemp = $ViewPreopNurseVitalSignRow["vitalSignTemp"];
					
					if($m%2==0) {
						$bg_color_pre_op_nurse = "#FDFAEB";
					}else {
						$bg_color_pre_op_nurse = "#FFFFFF";
					} 
					
					 $top=$m*24+25;
		?>
          				<div class="inner_safety_wrap" id="BP_div<?php echo $vitalsign_id; ?>">
							<div class="row">
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12 " style="padding:6px 12px;color:#800080;cursor: pointer; font-weight:bold;" onClick="changeBaseLine('<?php echo $vitalsign_id; ?>','<?php echo $vitalSignBp;?>','<?php echo $vitalSignP;?>','<?php echo $vitalSignR;?>','<?php echo $vitalSignO2SAT;?>','<?php echo $vitalSignTemp;?>');" >
										  BP
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style"><?php echo $vitalSignBp;?></span>                                                                   
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" style=" padding:6px 12px;font-weight:bold;">
										  P
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style"><?php echo $vitalSignP;?></span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" for="r" style=" font-weight:bold; padding:6px 12px;">
										  R
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style">
										<?php echo $vitalSignR;?>
									  </span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-3 col-sm-3 col-xs-3 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-3 col-xs-12 col-sm-12" for="O2" style=" font-weight:bold; padding:6px 12px;">
										  O<sub>2</sub>SAT
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12">
									  <span class="form-control no-controle-style">
										<?php echo $vitalSignO2SAT;?>
									  </span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-3">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" for="Temp" style="padding:6px 12px;">
										  Temp
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12">
                                          <span class="form-control no-controle-style">
                                            <?php echo $vitalSignTemp;?>
                                          </span>
                                      </Span>
                                      
								  </Div>
							  </div>
							   <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 text-center">
								  <a href="javascript:void(0)" onClick="delentry(<?php echo $vitalsign_id; ?>);" class="btn btn-danger" style="margin:10% 0">  X </a>
							  </div>
							</div>  
					   </div>
                       
                   
					
                    		<?php		
					$m++;
				} 
			}else {

			}
		?>            
			  

