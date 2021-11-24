								<div class="row pd10">
                                    <div class="col-sm-6">
                                        <h2>Future Appointments</h2>
                                        <div class="clearfix"></div>	
                                        <div class="futappo"> <?php echo $objTests->getFutureApp($patient_id);?></div>
                                        <!--<div class="nofut">No Future Appointments</div>	-->
                                    </div>
                                
                                    <div class="col-sm-6">
                                        <h2>Interpreted By</h2>
                                        <div class="form-group">
                                            <?php if($elem_physician){
												$getPersonnal3 = $objTests->getPersonnal3($elem_physician);
											}else if($elem_phyName){
												$getPersonnal3 = $objTests->getPersonnal3($elem_phyName);
											}else{$getPersonnal3 = '';}
											?>
                                            <input type="text" id="phyName" name="phyName" value="<?php echo $getPersonnal3 ;?>" class="form-control" readonly>
                                            <input type="hidden" id="elem_physician" name="elem_physician" value="<?php echo $elem_physician; ?>">
                                            <input type="hidden" id="elem_phyName" name="elem_phyName" value="<?php echo $elem_phyName;?>">
                                        </div>
                                        <div class="form-group">
                                            <?php $sigUserType = $userType;
                                                require_once(dirname(__FILE__)."/test_signature.php");
                                            ?>
                                        </div>
                                    </div>
                                </div>