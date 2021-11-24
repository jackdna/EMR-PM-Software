<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf width_table table-striped padding_0">
        	<tbody>
		
        		<?php 
						
						/*
						*
						*	Getting Pre op Medications Orders 
						*
						*/
						
						$laserTempDetails			=		$objManageData->getRowRecord('laser_procedure_template', 'laser_templateID', $laserDetails->laser_templateID);
						$preOpOrdMed				= 		$laserTempDetails->laser_preop_medication;
						$preOpOrdMed_exp 		=		explode(",",$preOpOrdMed);
						$preOpOrdMed_exp		=		array_filter($preOpOrdMed_exp);
						$preOpOrdMed_tmp		=		implode("','",$preOpOrdMed_exp);
						
						$preOpOrdMed_id =	$preOpOrdMed_med = $preOpOrdMed_sgt = $preOpOrdMed_dir	= array();
													
						if(count($laserTempDetails)>0)
						{
								if($preOpOrdMed_exp)
								{
									$counter	=	0	;
									foreach($preOpOrdMed_exp as $preOpOrdMed_data)
									{
										if($preOpOrdMed_data) 
										{
											$preOpOrdMed_detail	 = $objManageData->getRowRecord('preopmedicationorder', 'preOpMedicationOrderId', $preOpOrdMed_data,'medicationName');
											if(trim($preOpOrdMed_detail->medicationName)) 
											{
													$counter++;
													$preOpOrdMed_id[$counter] 		= $preOpOrdMed_detail->preOpMedicationOrderId;
													$preOpOrdMed_med[$counter]	=	$preOpOrdMed_detail->medicationName;
													$preOpOrdMed_sgt[$counter]	=	$preOpOrdMed_detail->strength;
													$preOpOrdMed_dir[$counter]		=	$preOpOrdMed_detail->directions;		
													
											}
										}
									}
								}	
						
						
						
						$tabIndex		=	0;
						for($preOpOrdMed_counter = 1 ; $preOpOrdMed_counter <= 20 ; $preOpOrdMed_counter++)
						{ 
								$readMode		=	'';
								//$isDisabled		=	'disabled="disabled"';
								$keyPressEvent = 'onkeyup="javascript:document.getElementById(\'preOpOrdMed_id'.$preOpOrdMed_counter.'\').value=\'\';"';
								/*if($laserDetails->laser_chk_preop_medication=="on"){
									$isDisabled	=	'';
								}*/
								if(trim($preOpOrdMed_med[$preOpOrdMed_counter])) 
								{
										$readMode 			=	'readonly="readonly"';
										$keyPressEvent	=	'';
								}
								
				?> 
									<input type="hidden" name="preOpOrdMed_id[]" id="preOpOrdMed_id<?php echo $preOpOrdMed_counter;?>" value="<?php echo $preOpOrdMed_id[$preOpOrdMed_counter];?>"  />
                                    <input type="hidden" name="preOpOrdMed_cat[]" id="preOpOrdMed_cat<?php echo $preOpOrdMed_counter;?>" value=""  />
                                    <tr>
                                    		<td class="padding_0" style="width:33% !important">
                                            	<input type="text" name="preOpOrdMed_med[]" id="preOpOrdMed_med<?php echo $preOpOrdMed_counter;?>"   <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($preOpOrdMed_med[$preOpOrdMed_counter]); ?>" tabindex="<?=++$tabIndex?>"  />
                                          	</td>
                                            
                                            <td class="padding_0" style="width:33% !important">
                                            	<input type="text" name="preOpOrdMed_sgt[]" id="preOpOrdMed_sgt<?php echo $preOpOrdMed_counter;?>"   <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($preOpOrdMed_sgt[$preOpOrdMed_counter]); ?>" tabindex="<?=++$tabIndex?>"  />
                                          	</td>
                                            
                                            <td class="padding_0" style="width:33% !important">
                                            	<input type="text" name="preOpOrdMed_dir[]"   id="preOpOrdMed_dir<?php echo $preOpOrdMed_counter;?>"     <?php echo $keyPressEvent;?> class="form-control" style="width:100%; float:left; border-radius:0;" value="<?php echo stripslashes($preOpOrdMed_dir[$preOpOrdMed_counter]); ?>" tabindex="<?=++$tabIndex?>"  />
                                          	</td>
                                            <?php
													
												if($preOpOrdMed_id[$preOpOrdMed_counter]) 
												{
											?>
                                            		<td class="padding_0" >
                                               			<img src="../images/close.jpg" style="cursor:pointer; float:right" alt="delete" data-profile-id="<?php echo $laserDetails->laser_templateID; ?>" data-record-id="<?php echo $preOpOrdMed_id[$preOpOrdMed_counter];?>" data-record-type="preMed" class="removeMedOrder" >
                                                   	</td>
                                           	<?php
												}
											?>
                                         	</td>
                            			                
									</tr>
             	<?php 
						} 
						
						}
				?>

</table>