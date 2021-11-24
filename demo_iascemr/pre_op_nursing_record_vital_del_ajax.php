<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
			$delId=$_GET['delId'];
			$row = $_GET['row'];
			
			$SaveVitalSignQry = "delete from `preopnursing_vitalsign_tbl` where 
								vitalsign_id ='".$delId."'";
										
			$SaveVitalSignRes = imw_query($SaveVitalSignQry) or die(imw_error());
	
			//IF SELECTED 'BASELINE VITAL SIGNS' (AT THE HEADER) IS DELETED THEN DELETE ITS ID FROM preopnursingrecord ALSO
			$chkPreOpNurseVitalSignIdQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."' AND preopnursing_vitalsign_id ='".$delId."'";
			$chkPreOpNurseVitalSignIdRes = imw_query($chkPreOpNurseVitalSignIdQry) or die(imw_error()); 
			$chkPreOpNurseVitalSignIdNumRow = imw_num_rows($chkPreOpNurseVitalSignIdRes);
			if($chkPreOpNurseVitalSignIdNumRow>0) {
				//UPDATE preopnursing_vitalsign_id IN preopnursingrecord to EMPTY
				$updatePreOpNurseVitalSignIdQry = "UPDATE `preopnursingrecord` SET preopnursing_vitalsign_id ='' WHERE 
													confirmation_id ='".$_REQUEST["pConfId"]."'";
											
				$updatePreOpNurseVitalSignIdRes = imw_query($updatePreOpNurseVitalSignIdQry) or die(imw_error());
				//END UPDATE preopnursing_vitalsign_id IN preopnursingrecord to EMPTY 
			}
			//END IF SELECTED 'BASELINE VITAL SIGNS' (AT THE HEADER) IS DELETED THEN DELETE ITS ID FROM preopnursingrecord ALSO
	
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
			}
			
			else {
			

			}
			
		?>            
			  
	
