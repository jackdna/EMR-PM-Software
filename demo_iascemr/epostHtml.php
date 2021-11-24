<div class="modal fade" id="ePostModal" data-backdrop="false"  >
 	<div class="modal-dialog modal-lg">
 		
        <div class="modal-content">
      		
            <div class="modal-header text-center">
            	
                <div id="ModalTitle" style="position:absolute; text-align:center; width:100%; color:white"></div>
                
                <input type="hidden" value="<?php echo $selected_date?>" id="epost_selected_date" />
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                
                <h4 class="modal-title rob" style="text-align:left; color:white;">
                	
                    <span class="patientName" style="width:305px;"></span>
                    
                    <span id="response" style="float:right; font-size:16px; margin-right:5px; "></span>
                </h4>  
			</div>
    		
            <div class="clearfix margin_adjustment_only"></div>
            
            <div class="modal-body" style="postion:relative">
            	
                <div class="bs-example-tabs">
                	<ul id="myTab" class="nav nav-tabs nav-justified">
                    	<li ><a href="#Epost" data-title="Epost"  data-toggle="tab">Eposts (<b id="epostCount"></b>)</a></li>
                        <li ><a href="#AddEpost" data-title="AddEpost"  data-toggle="tab"><b class="fa fa-plus"></b> Add New</a></li>
                        <li ><a href="#Alert" data-title="Alert" data-toggle="tab"><b class="fa fa-exclamation-triangle"></b> Patient Alert (<b id="alertCount"></b>)</a></li>
					</ul>
				</div>
                
                <div class="clearfix margin_adjustment_only"></div>
                
                <div id="myTabContent" class="tab-content">
                	
                    <Div class="clearfix margin_adjustment_only"></Div>	
                    
                    <div class="tab-pane" id="AddEpost">
                    		
                            <div class="form_inside_modal">
                            	
                                <div class="col-lg-3 visible-lg"></div>
                                <div class="col-md-3 visible-md"></div>
                                
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><form id="addForm">
                            		<div class="form_inner_m eposter">
                                    	
                                        <div class="row" >
                                        	<span id="processing"></span>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        		<label for="select_Epost" class="text-left">Enter ePostIt</label>
											</div>
                                            
                                    		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >
                                            	<select class="selectpicker " id="chart_notes" name="chart_notes[]" multiple data-style="btn-info" data-size="6" data-header="Select Chart Note " title='Select Chart Note' ></select>
                                    		</div>
                                    		
                                            <Div class="clearfix margin_adjustment_only"></Div>
                                    		
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                            	<textarea class="form-control" name="epostText" id="epostText"> </textarea>
											</div>
                                            
                                            <Div class="clearfix margin_adjustment_only"></Div>
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="text-align:right">
                                            	<!-- Hidden Fields -->
                                                <input type="hidden" readonly name="PID" /> <!-- Patient Data ID -->
                                                <input type="hidden" readonly name="PCI" /> <!-- Patient Confirmation ID -->
                                                <input type="hidden" readonly name="SID" /> <!-- Patient Stub ID -->
                                                <input type="hidden" readonly name="request" value="add" /> <!-- Request to add new record -->
                                                <button type="submit" name="submit" id="submitButton" class="btn btn-success"><b class="fa fa-save"></b>&nbsp;Save</button>
                                                <button type="reset" name="resetButton" id="resetButton" class="btn btn-danger"><b class="fa fa-times"></b>&nbsp;Reset</button>
											</div>
                                            
										</div><!-- ROW -->
                            		
                                    </div>
                        		
                                	
                                </form></div>
                        	
                            </div>
					
                    </div><!----- ---------------------  Tab content for Add New Epost----------------------------- -->
                    
                    <div class="tab-pane" id="Epost" >
                    
                    	<div class="form_inside_modal"  style="position:relative"  >
                        
                        </div> <!-- Form Inside Modal -->
                        
					</div>	<!----- ---------------------  Tab Content for Epost Tab ----------------------------- -->
                    
                    
                    
                    <div class="tab-pane" id="Alert" >
                    
                    	<div class="form_inside_modal" style="position:relative" >
                        	
                        </div> <!-- Form Inside Modal -->

					</div>	<!----- ---------------------  Tab content for Alert epost tab ----------------------------- -->
                
                
            	</div><!-- End Overall Tab Content -->
                
                
        	</div> <!-- End Modal Body -->
            
            
            <!--<div class="modal-footer">
            	<a class="btn btn-primary" href="javascript:void(0)">  <b class="fa fa-save"></b>  Save   </a>
                <a data-dismiss="modal" class="btn btn-default" href="javascript:void(0)">   <b class="fa fa-close"></b>  Close  </a>
			</div>--> <!-- End Modal Footer -->
            
     	</div><!-- End Modal Content -->
        
	
    </div> <!-- end Modal Dialogue -->

</div><!-- End Modal -->