<link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-colorpicker.css" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-formhelpers-colorpicker.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/patient_info.js"></script>
<div id="group_form" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg" style="width:95%;">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">x</button>
        <h4 class="modal-title" id="modal_title">
        	<?php 
						echo (($templateArray['Other_Information']['GroupDetails'] <> '') ? 'Edit' : 'Add'). ' Group';
         	?> 
        </h4>
     	</div>
      <div class="modal-body" style="overflow:hidden; overflow-y:auto;">
      	<form name="groups" method="post">
          <input type="hidden" name="act" value="edit_group" />
          <input type="hidden" name="txtSave" id="txtSave" value="1" />
          <input type="hidden" name="alreadyInstitution" id="alreadyInstitution" value="<?php echo $templateArray['Other_Information']['AlreadyInstitution'];?>" >
            <input type="hidden" name="alreadyInstitutionGrpId" id="alreadyInstitutionGrpId" value="<?php echo $templateArray['Other_Information']['AlreadyInstitutionGrpId'];?>}" >
            <input type="hidden" name="txtgroup_institution" id="txtgroup_institution" value="<?php echo $templateArray['Other_Information']['GroupInstitution'];?>" >
          <input type="hidden" name="gro_id" id="gro_id" value="<?php echo $templateArray['Other_Information']['GroupDetails'];?>" />	
          <input type="hidden" name="hiddZipCodeValid" id="hiddZipCodeValid" value="">
          <input type="hidden" name="oldName" id="oldName" value="<?php echo $templateArray['Other_Information']['GroupName'];?>">
          <input type="hidden" name="allowInstitution" id="allowInstitution" value="">
            <div class="row">
            	<div class="col-lg-3 col-md-6 col-sm-6" onClick="change_layout_color('groupInfo_table');" id="groupInfo_table">
              	<div class="grpbox">
                	<div class="head"><span>Group Information</span></div>
                  <div class="clearfix"></div>
                  <div class="tblBg">
                  	<div class="row">
                    	
                      <!-- Name -->
                    	<div class="col-sm-5 ">
                      	<div class="form-group">
                        	<label for="name">Name</label>
                          <input type="text" name="name" id="name" onBlur="changeClass(this);" tabindex="1" size="20" value="<?php echo $templateArray['Group_Info']['Name'];?>" class="form-control" >
                      	</div>
                    	</div>
                      
                      <!-- Institute || Anesthesia -->
                      <div class="col-sm-7">
                      	<div class="row"><br>
                        	<div class="col-sm-6">
                          	<div class="checkbox">
                            	<input type="checkbox" name="group_institution" <?php echo $templateArray['Group_Info']['GroupInstitutionChecked'];?> tabindex="2" id="group_institution">
                              <label for="group_institution">Institution</label>
                            </div>
                         	</div>
                          <div class="col-sm-6">
                            <div class="row">
								<div class="col-sm-3">
									<div class="checkbox">
										<input type="checkbox" name="group_anesthesia" <?php echo $templateArray['Group_Info']['GroupAnesthesiaChecked'];?> id="group_anesthesia"/>	
										<label for="group_anesthesia"></label>
									</div>
								</div>	
								<div class="col-sm-9">
									 <input type="hidden" name="optional_anes_npi" value="<?php echo $templateArray['Group_Info']['optional_anes_npi'];?>"/>
									<label class="text_purple pointer" onclick="show_anes_npi(event,this);" style="vertical-align:sub">Anesthesia</label>
								</div>	
							</div>
                        	</div>
                      	</div>
                     	</div>
                    	
                      <div class="clearfix"></div>
                      <!-- Secondary ID -->     
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="sec_id">Secondary ID</label>
                          <input type="text" name="sec_id" id="sec_id" size="15" tabindex="3" value="<?php echo $templateArray['Group_Info']['GroupDetailSecondaryId'];?>" class="form-control">
                        </div>
                      </div>
                      
                      <!-- NPI -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="group_NPI" onClick="display_npi_div();" class="text_purple pointer">NPI</label>
                          <input type="text" onBlur="changeClass(this);" name="group_NPI" id="group_NPI" size="20" tabindex="4" maxlength="10" value="<?php echo $templateArray['Group_Info']['GroupNPI'];?>" class="form-control">
                        </div>
                      </div>
                    	
                      <!-- Federal EIN -->
                      <div class="col-sm-6">
                      	<div class="form-group">
                          <label for="">Federal EIN</label>
                          <input type="text" onBlur="changeClass(this);" name="group_Federal_EIN" id="group_Federal_EIN" tabindex="5" value="<?php echo $templateArray['Group_Info']['GroupFederalEIN'];?>" class="form-control">
                        </div>
                      </div>
                      
                      <!-- Color -->
                      <div class="col-sm-6">
                    		<div class="form-group">
                        	<label for="">Color</label>
                  				<div class="bfh-colorpicker" data-name="group_color" data-color="<?php echo $templateArray['Group_Info']['GroupColor'];?>"></div>
                      	</div>
                    	</div>
            				</div>
            				<div class="clearfix"></div>
            			</div>
            			<div class="clearfix"></div>
            		</div>
            	</div>
              
              <div class="col-lg-3 col-md-6 col-sm-6" onClick="change_layout_color('mailingAdd_table');" id="mailingAdd_table">
                <div class="grpbox">
                	<div class="head"><span>Mailing Address</span></div>
                  <div class="clearfix"></div>
                  
                  <div class="tblBg">
                  	<div class="row">
                    	
                      <!-- Street 1-->
                      <div class="col-sm-6">
                      	<div class="form-group">
                        	<label>Street 1</label>
                        	<input type="text" onBlur="changeClass(this);" name="group_Address1" tabindex="7" value="<?php echo $templateArray['Mailing']['GroupAddress1'];?>" class="form-control">
                      	</div>
                     	</div>
                      
                      <!-- Street 2-->
                      <div class="col-sm-6">
                      	<div class="form-group">
                        	<label>Street 2</label>
                          <input type="text"  name="group_Address2" tabindex="8" value="<?php echo $templateArray['Mailing']['GroupAddress2'];?>" class="form-control">
                      	</div>
                     	</div>
                      
                      <!-- Zip -->
                      <div class="col-sm-5">
                      	<div class="form-group">
                          <label for=""><?php getZipPostalLabel(); ?></label>
                          <div class="clearfix"></div>
						  <?php
							$form_cls = 'form-inline zipcod';
							if(!$templateArray['zip_ext_status']){
								$form_cls = '';
							}
						  ?>
                          <div class="<?php echo $form_cls; ?>">
                            <input maxlength="<?php echo $templateArray['zip_size'];?>" type="text" onBlur="changeClass(this);" name="group_Zip" id="code" size="<?php echo $templateArray['zip_size'];?>" tabindex="9" onChange="zip_vs_state(this.value,'add_groups');" value="<?php echo $templateArray['Mailing']['GroupZip'];?>" class="form-control">
                            <?php if( $templateArray['zip_ext_status']) { ?>  	
                           	<input type="text" onBlur="changeClass(this);" name="zip_ext" maxlength="4" id="zip_ext" tabindex="9" value="<?php echo $templateArray['Mailing']['GroupZip_Ext'];?>" class="form-control">
                            <?php } ?>
                          </div>
                       	</div>
                   		</div>
                      
                      <!-- City -->
                      <div class="col-sm-4">
                				<div class="form-group">
                    			<label for="">City</label>
                    			<input type="text" onBlur="changeClass(this);" name="group_City" id="city" tabindex="10" value="<?php echo $templateArray['Mailing']['GroupCity'];?>" class="form-control">
                  			</div>
               				</div>
                      
                      <!-- State -->
                      <div class="col-sm-3">
                      	<div class="form-group">
                          <label for="">State</label>
                          <input type="text" onBlur="changeClass(this);" name="group_State" id="state" tabindex="11" value="<?php echo $templateArray['Mailing']['GroupState'];?>" class="form-control">
                        </div>
                      </div>
                      
                      <!-- Medicare Receiver ID -->
                      <div class="col-sm-4">
                      	<div class="form-group">
                          <label for="MedicareReceiverId" title="Medicare&nbsp;Receiver&nbsp;Id">Medicare&nbsp;Rec.&nbsp;Id</label>
                          <input type="text" size="23" class="form-control" tabindex="12" name="MedicareReceiverId" id="MedicareReceiverId" value="<?php echo $templateArray['Mailing']['MedicareReceiverId'];?>" />
                        </div>
                      </div>
                      
                      <!-- Medicare Submitter Id -->
                      <div class="col-sm-4">
                      	<div class="form-group">
                    			<label for="MedicareSubmitterId" title="Medicare&nbsp;Submitter&nbsp;Id">Medicare&nbsp;Sub.&nbsp;Id</label>
                    			<input type="text" class="form-control" tabindex="13" name="MedicareSubmitterId" id="MedicareSubmitterId" value="<?php echo $templateArray['Mailing']['MedicareSubmitterId'];?>" />
                       	</div>
                     	</div>
                      <!-- THCIC submitter ID -->
                      <div class="col-sm-4">
                      	<div class="form-group" id="THCICSubmitterId_col" <?php echo ($templateArray['Mailing']['GroupState']!='TX' || $templateArray['Mailing']['GroupState']=='')?'style="display:none;"':'' ?>>
                    			<label for="THCICSubmitterId" title="THCIC&nbsp;Submitter&nbsp;Id">THCIC sub. Id</label>
                    			<input type="text" class="form-control" tabindex="13" name="THCICSubmitterId" id="THCICSubmitterId" value="<?php echo $templateArray['Mailing']['THCICSubmitterId'];?>" />
                       	</div>
                     	</div>
                			
                   	</div>
                    <div class="clearfix"></div>
                	</div>
                  
                  <div class="clearfix"></div>
              	</div>
            	</div>
              
            	<div class="col-lg-2 col-md-4 col-sm-6" onClick="change_layout_color('contacts_table');" id="contacts_table">
              	<div class="grpbox">
                	<div class="head"><span>Contacts</span></div>
                  <div class="clearfix"></div>
                  
                  <div class="tblBg">
                  	<!-- Contact Name -->
                    <div class="form-group">
                    	<label>Contact Name</label>
                      <input type="text" onBlur="changeClass(this);" name="Contact_Name" id="Contact_Name" tabindex="14" value="<?php echo $templateArray['Contacts']['ContactName'];?>" class="form-control">
                   	</div>
                 		
                    <div class="clearfix"></div>
                    
                    <!-- Email Address -->
                    <div class="form-group">
                    	<label>Email Address</label>
                      <input type="text" name="group_Email" id="group_Email" tabindex="15" value="<?php echo $templateArray['Contacts']['GroupEmail'];?>" class="form-control" onKeyPress="javascript:search_email(event, 'div_email_section', 820, 200)">
                      <div name="div_email_section" id="div_email_section" style="width:auto; display:none; position:absolute; z-index:100; margin-top:0;">
                        <select size="4" onKeyPress="select_option(event, this, 'group_Email')" onClick="select_option_with_mouse(this, 'group_Email')" class="list-group pd0 margin_0">
                          <option class="list-group-item pd3 " selected value="@aol.com">aol.com</option>
                          <option class="list-group-item pd3" value="@msn.com">msn.com</option>
                          <option class="list-group-item pd3" value="@gmail.com">gmail.com</option>
                          <option class="list-group-item pd3" value="@hotmail.com">hotmail.com</option>
                        </select>
                      </div>
                  	</div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                    	<!-- Telephone -->
                    	<div class="col-sm-5">
                      	<div class="form-group">
                        	<label>Telephone</label>
                          <input type="text" onBlur="changeClass(this);" name="group_Telephone" id="group_Telephone" onChange="set_phone_format_js(this,'<?php echo $templateArray['phone_format'];?>');" tabindex="16" value="<?php echo $templateArray['Contacts']['GroupTelephone'];?>" class="form-control">
                      	</div>
                    	</div>
                      
                      <!-- Tel Ext. -->
                      <div class="col-sm-3">
                      	<div class="form-group">
                        	<label>Ext.</label>
                        	<input type="text" onBlur="changeClass(this);" name="group_Telephone_ext" id="group_Telephone_ext" tabindex="16" value="<?php echo $templateArray['Contacts']['GroupTelephone_ext'];?>" class="form-control">
                       	</div>
                     	</div>
                      
                      <!-- Fax -->
                      <div class="col-sm-4">
                      	<div class="form-group">
                        	<label>Fax</label>
                        	<input type="text" name="group_Fax" id="group_Fax" size="10" onChange="set_phone_format_js(this,'<?php echo $templateArray['phone_format'];?>');" tabindex="17" value="<?php echo $templateArray['Contacts']['GroupFax'];?>" class="form-control">
                        </div>
                     	</div>
            				</div>
                	</div>
                  
                  <div class="clearfix"></div>
              	</div>
            	</div>
              
              <div class="col-lg-2 col-md-4 col-sm-6" onClick="change_layout_color('HouseInfo');" id="HouseInfo">
              	<div class="grpbox">
                	<div class="head"><span>Clearing House Information</span></div>
                  <div class="clearfix"></div>
                  <div class="tblBg">
                  	<!-- Receiver Id -->
                    <div class="form-group">
                    	<label>Receiver Id</label>
                      <input type="text" size="20"  class="form-control" tabindex="18" name="ReceiverId" id="ReceiverId" value="<?php echo $templateArray['House_Info']['RecId'];?>" />
                  	</div>
                    
                    <div class="clearfix"></div>
                    
                    <!-- Submitter Id-->
                    <div class="form-group">
                    	<label>Submitter Id</label>
                      <input type="text"  size="20"  class="form-control" tabindex="19" name="submitterId" id="submitterId" value="<?php echo $templateArray['House_Info']['SubId'];?>" />
                  	</div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="form-group">
                    	<label>Site Id</label>
              				<input type="text" size="20" name="site_id" id="site_id" class="form-control" tabindex="20" value="<?php echo $templateArray['House_Info']['SiteId'];?>">
                  	</div>
                	</div>
                  
                  <div class="clearfix"></div>
             		</div>
            	</div>
              
            	<div class="col-lg-2 col-md-4 col-sm-6" onClick="change_layout_color('accessTable');" id="accessTable">
            		<div class="grpbox">
                	<div class="head"><span>Access</span></div>
                  <div class="clearfix"></div>
                  <div class="tblBg">
                  
                  	<!-- Clearing House User Id -->
                  	<div class="form-group">
						<label>Clearing House User Id</label>
                      <input type="text" size="20" class="form-control" tabindex="21" name="EmdeonUserId" id="EmdeonUserId" value="<?php echo $templateArray['Access']['UserId'];?>" />
                   	</div>
                 		
                    <div class="clearfix"></div>
                    
                    <!-- Clearing House Password -->
                    <div class="form-group">
						<label>Clearing House Password</label>
                    	<input size="20" type="password" class="form-control" tabindex="22" name="EmdeonPassword" id="EmdeonPassword" value="<?php echo $templateArray['Access']['UserPwd'];?>" />
                   	</div>
                    
                    <div class="clearfix"></div>
                    
                    <!-- TID -->
                    <div class="form-group">
						<label>TID</label>
                    	<input size="20" type="text" class="form-control" tabindex="22" name="prod_tid" id="prod_tid" value="<?php echo $templateArray['Access']['prod_tid'];?>" />
                   	</div>
                	</div>
                  <div class="clearfix"></div>
              	</div>
            	</div>
            
            </div>
            
            <div class="clearfix"></div>
            
            <div class="row" id="remitte_address" onClick="change_layout_color('remitte_address');">
              	<div class="remadd ">
				<div class="col-sm-2">
					<div class="remadhead">
						Remittance Address	
					</div>
				</div>
                
                  	<!-- Street 1 -->
                    <div class="col-sm-2">
                    	<label for="">Street 1</label>
                      <input type="text" onBlur="changeClass(this);" name="rem_address1" tabindex="23" class="form-control" value="<?php echo $templateArray['Remittance']['rem_address1'];?>">
                   	</div>
                		
                    <!-- Street 2 -->
                   	<div class="col-sm-2">
                    	<label for="">Street 2</label>
                      <input type="text" name="rem_address2" tabindex="24" class="form-control" value="<?php echo $templateArray['Remittance']['rem_address2'];?>">
                   	</div>
                    
                    
                   
					
					<div class="col-sm-3">
						<div class="row">
							<!-- Zip -->  
							<div class="col-sm-6">
								<label for=""><?php getZipPostalLabel(); ?></label>
							  <div class="clearfix"></div>
							  <?php 
								$frm_modal_cls = 'form-inline zipcode form_extension';
								if(!$templateArray['zip_ext_status']){
									$frm_modal_cls = '';
								}
							  ?>
							  <div class="<?php echo $frm_modal_cls; ?>">
								<input maxlength="<?php echo $templateArray['zip_size'];?>" type="text" onBlur="changeClass(this);" name="rem_zip" id="rem_zip" tabindex="25" onChange="zip_vs_state(this.value,'add_rem_groups');" class="form-control" value="<?php echo $templateArray['Remittance']['rem_zip'];?>">
								<?php if( $templateArray['zip_ext_status']){?>
														<input type="text" onBlur="changeClass(this);" name="rem_zip_ext" maxlength="4" id="rem_zip_ext" tabindex="26" class="form-control" value="<?php echo $templateArray['Remittance']['rem_zip_ext'];?>">
								<?php } ?>
								</div>
							</div>
							
							
							 <!-- City & State -->
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-8">
										<label for="">City</label>
										<input type="text" onBlur="changeClass(this);" name="rem_city" id="rem_city" tabindex="27" class="form-control" value="<?php echo $templateArray['Remittance']['rem_city'];?>">
									</div>	
									<div class="col-sm-4">
										<label for=""><?php echo ucfirst($templateArray['state_label']);?></label>
										<input type="text" onBlur="changeClass(this);" name="rem_state" id="rem_state" tabindex="28" class="form-control" value="<?php echo $templateArray['Remittance']['rem_state'];?>">
									</div>	
								</div>
							</div>
							
						</div>	
					</div>
					
					<!-- Telephone -->
                    <div class="col-sm-2">
                    	<label for="">Telephone</label>
						<div class="row">
							<div class="col-sm-8">
								<input type="text" onBlur="changeClass(this);" name="rem_telephone" id="rem_telephone" onChange="set_phone_format_js(this,'<?php echo $templateArray['phone_format'];?>');" tabindex="29" class="form-control" value="<?php echo $templateArray['Remittance']['rem_telephone'];?>">
							</div>
							<div class="col-sm-4">
								<input type="text" onBlur="changeClass(this);" name="rem_telephone_ext" id="rem_telephone_ext" tabindex="30" class="form-control" value="<?php echo $templateArray['Remittance']['rem_telephone_ext'];?>">
							</div>
						</div>
                  	</div>
                    
                    <!-- Fax -->
                    <div class="col-sm-1">
                    	<label for="">Fax</label>
                      <input type="text" name="rem_fax" id="rem_fax" size="10" onChange="set_phone_format_js(this,'<?php echo $templateArray['phone_format'];?>');" tabindex="31" class="form-control" value="<?php echo $templateArray['Remittance']['rem_fax'];?>">
                  	</div>
           	</div>
           	</div>
            
            <div class="clearfix"></div>
            
            <!-- Notices -->
            <div class="row">
				<div class="col-sm-12 pt10">
					<textarea  id="loginLegalNotices" name="loginLegalNotices"><?php echo $templateArray['notice'];?></textarea>
				</div>
           	</div>
      	</form>
      </div>
      
      <div class="modal-footer">	
      </div>
      
    </div>
  </div>
</div>