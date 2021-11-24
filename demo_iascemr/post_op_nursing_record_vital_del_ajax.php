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
$objManageData 		= new manageData;		
			$delId=$_GET['delId'];
			$row = $_GET['row'];
			
			$SaveVitalSignQry = "delete from `vitalsign_tbl` where 
								vitalsign_id =$delId";
										
			$SaveVitalSignRes = imw_query($SaveVitalSignQry) or die(imw_error());
	?>
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
				
			}
		?>