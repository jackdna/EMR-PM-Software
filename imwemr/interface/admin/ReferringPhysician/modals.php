<!-- Add | Edit PO evalution mapping -->
<div id="addNew_PO" class="modal" role="dialog" style="z-index: 1080; width: 1000px">
	<div class="modal-dialog modal-lg">
		<form name="add_edit_po" id="add_edit_po"  method="post" autocomplete="off" onSubmit="savePOData();return false;">
     		<input type="hidden" name="physician_Reffer_id_for_po" id="physician_Reffer_id_for_po" >
      		<div class="modal-content">
      			<div class="modal-header bg-primary">
				  <button type="button" class="close" data-dismiss="modal">×</button>
				  <h4 class="modal-title">PO Evaluation Mapping</h4>
				</div>
      			 <div class="modal-body pd5" style="height: 500px; overflow:hidden; overflow-y:auto;" id="po_content_body">
      			 	Loading content ...
      			 </div>
      			<div class="modal-footer pd5">
				  <input type="submit" class="btn btn-success" value="Save">
				  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
      		</div>
      	</form>
	</div>
</div>
<!-- Add | Edit Referring Physician -->
<div id="addNew_div" class="modal" role="dialog" >
	<div class="modal-dialog modal_90">
  	<!-- Modal content-->
    <form name="add_edit_frm" id="add_edit_frm"  method="post" autocomplete="off" onSubmit="saveFormData();return false;">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title" id="modal_title" style="width: 300px; float: left">Add Referring Physician</h4>
          <span style="float: right; margin-right: 20px" class="text_purple pointer" onClick="showPO()"><!-- data-toggle="modal" data-target="#addNew_PO"-->PO Evaluation Mapping</span>
        </div>
        
        <div class="modal-body pd5" style="overflow:hidden; overflow-y:auto;">
          
            <input type="hidden" name="physician_Reffer_id" id="physician_Reffer_id" >
            <input type="hidden" name="address_del_id" id="address_del_id">
            <div class="row">
              
              <div class="col-lg-3 col-md-3 col-sm-6">
                <div class="grpbox no-min ">
					<div class="head">
						<span>Referring Physician</span>
					</div>
                  <div class="clearfix"></div>
                  
                  <div class="tblBg">
                    <div class="row">
                      <!-- Title -->
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="Title">Title</label>
                          <select class="selectpicker" name="Title" id="Title" tabindex="1" data-width="100%" >
                            <option value="">Title</option>
                            <option value="Dr.">Dr.</option>
                            <option value="DO">DO</option>
                            <option value="OD">OD</option>				
                            <option value="MD">MD</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Miss">Miss</option>	
                            <option value="Ms">Ms</option>	
                            <option value="PA">PA</option>
							<option value="MD-OPH">MD-OPH</option>
							<option value="MD-PHD">MD-PHD</option>
							<option value="NP">NP</option>
							<option value="FNP">FNP</option>
							<option value="PAC">PAC</option>
							<option value="APRN">APRN</option>
							<option value="CNM">CNM</option>
                          </select>
                        </div>
                      </div>
                      
                      <!-- First Name -->
                      <div class="col-sm-3">
                        <div class="form-group">
							<label for="FirstName">First Name</label>
                          <input type="text" class="form-control" tabindex="2"  name="FirstName" id="FirstName" value="">
                        </div>
                      </div>
                      
                      <!-- Middle Name-->
                      <div class="col-sm-3">
                        <div class="form-group">
						<label for="MiddleName">Middle</label>
                          <input type="text" class="form-control" name="MiddleName" id="MiddleName" tabindex="3" value="">
                        </div>
                      </div>
                      
                      <!-- Last Name -->
                      <div class="col-sm-3">
                        <div class="form-group">
						<label for="LastName">Last Name</label>
                          <input type="text" class="form-control" name="LastName" id="LastName" tabindex="4" value="">
                        </div>
                      </div>
                      
                      <!-- Credentials --> 
                      <div class="col-sm-6">
                        <div class="form-group">
							<label for="credential">Credentials</label>
                          <input type="text" size="13" class="form-control"  tabindex="5" name="credential" id="credential" value="" >
                        </div>
                      </div>
                      
                      <!-- Initial Ref. Date -->
                      <div class="col-sm-6">
                        <div class="form-group">
							<label for="start_date">Initial Ref.Date</label>
							<div class="input-group">
                            <input type="text" class="datepicker form-control" tabindex="8" id="start_date"  name="start_date" value="" onBlur="checkdate(this);" onFocus="getFocusObj(this);" />
                            <label class="input-group-addon pointer" for="start_date">
                              <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
                            </label>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Last Ref. Date -->
                      <div class="col-sm-6">
                        <div class="form-group">
						<label for="end_date">Last Ref.Date</label>
                          <div class="input-group">
                            <input type="text"  class="form-control datepicker" tabindex="8" id="end_date"  name="end_date" value="" onBlur="checkdate(this);" onFocus="getFocusObj(this);" />
                            <label class="input-group-addon pointer" for="end_date">
                              <i class="glyphicon glyphicon-calendar" aria-hidden="true"></i>
                            </label>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Group -->
                      <div class="col-sm-6">
                        <div class="form-group">
						<label for="ref_phy_group">Group</label>
                          <select class="selectpicker" name="ref_phy_group" data-width="100%" title="Group" id="ref_phy_group" ></select>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  
                  <div class="clearfix"></div>
                </div>
              </div>
              
              <div class="col-lg-5 col-md-5 col-sm-12">
                <div class="grpbox no-min" style="max-height:216px; overflow:hidden; overflow-y:auto;">
                	
                  <div class="head">
                  	<span class="">Address & Contacts</span>
                  	<figure class="pull-right text-right"><img src="../../../library/images/addinput.png" alt="Add" onClick="add_new_address();" class="pointer" width="20px" height="auto"></figure>
                  </div>
                  <div class="clearfix"></div>
                  
                  <div class="tblBg">
                    <div class="row">
                      <input type="hidden" id="id_address[0]" name="id_address[0]">
                      <!-- Practice Name -->
                      <div class="col-sm-4">
                        <div class="form-group">
							<label for="PractiseName">Practice Name</label>
							<input type="hidden" id="actbval">
                          <input type="text" class="form-control" name="PractiseName[0]" id="PractiseName" value="" onChange="fill_practice_address();">
                        </div>
                      </div>
                      
                      <!-- Specialty -->
                      <div class="col-sm-4">
                        <div class="form-group">
						<label for="specialty">Specialty</label>
                          <input name="specialty[0]" type="text" class="form-control" id="specialty" value="">												
                        </div>
                      </div>
                      
                      <!-- Street 1 -->
                      <div class="col-sm-4">
                        <div class="form-group">
							<label for="">Street 1</label>
                          <input type="text" class="form-control" tabindex="9" name="Address1[0]" value="" >
                        </div>
                      </div>
                      
                      <!-- Street 2 -->
                      <div class="col-sm-4">
                        <div class="form-group">
						<label for="">Street 2</label>
						<input type="text" class="form-control" tabindex="10" name="Address2[0]" value="" >
                        </div>
                      </div>
                      
                      <!-- Zip -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="ZipCode"><?php getZipPostalLabel(); ?></label>
                          <div class="clearfix"></div>
                          <div class="form-inline zipcod">
                            <input maxlength="<?php echo inter_zip_length();?>" type="text" class="form-control" name="ZipCode[0]" id="ZipCode" size="<?php echo inter_zip_length();?>" tabindex="11"  onBlur="zip_vs_state_R6(this,document.getElementsByName('City[0]'),document.getElementsByName('State[0]'));" value="">
                            <?php if( inter_zip_ext() ){?>
                            <input type="text" maxlength="4" class="form-control" name="zip_ext[0]" id="zip_ext[0]"  size="8" tabindex="11" value="" >
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                      
                      <!-- City -->
                      <div class="col-sm-3">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group"><label for="rcity">City</label>
								  <input type="text" class="form-control" name="City[0]" tabindex="12" size="12" id="rcity" value="" >
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group"><label for="rcity">Country</label>
								  <input type="text" class="form-control" name="country[0]" tabindex="12" size="12" id="rcountry" value="" >
								</div>
							</div>
						</div>
                      </div>
                      
                      <!-- State -->
                      <div class="col-sm-1">
                        <div class="form-group"><label for="rstate"><?php echo ucwords(inter_state_label());?></label>
                          <input type="text" class="form-control" name="State[0]" maxlength="<?php if(inter_state_val() == "abb")echo '2';?>" tabindex="13" size="5" id="rstate" value="" >
                        </div>
                      </div>
  										
                      <!-- Phone -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="">Phone</label>
                          <input type="text" class="form-control" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>','','','form-control mandatory');" tabindex="14" name="physician_phone[0]" value="" >
                        </div>
                      </div>
                    
                    	<!-- Fax -->
                      <div class="col-sm-4">
                        <div class="form-group">
						<label for="">Fax</label>
                        	<input type="text" class="form-control" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>','<?php echo inter_phone_length();?>','fax','form-control mandatory');" tabindex="15" name="physician_fax[0]" value="" >
                      	</div>
                    	</div>
                    
                    	<!-- Email -->
                    	<div class="col-sm-4">
                        <div class="form-group">
							<label for="">Email</label>
                          <input type="text" class="form-control" tabindex="16" name="physician_email[0]" value="" >
                        </div>
                      </div>
                    
                    
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  
                  <div class="clearfix"></div>
                  
                  <div id="addNew_address"></div>
                </div>
              </div>
              
              <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="grpbox no-min ">
                  <div class="head"><span>More Info</span></div>
                  
                  <div class="clearfix"></div>
                  
                  <div class="tblBg">
                    <div class="row">
                      <!-- NPI -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="NPI">NPI</label>
                          <input type="text" class="form-control" name="NPI" id="NPI" tabindex="17" value="" size="18">
                        </div>
                      </div>
                      
                      <!-- Taxonomy -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="TexonomyId">Taxonomy</label>
                          <input type="text" class="form-control" id="TexonomyId" tabindex="18" name="Texonomy" value="" size="18">
                        </div>
                      </div>
                      
                      <!-- MDCR -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="">MDCR / CCN</label>
                          <input type="text" class="form-control" name="MDCR" tabindex="19" value="">
                        </div>
                      </div>
                      
                      <div class="clearfix"></div>
                      
                      <!-- MDCD -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="">MDCD / TIN</label>
                          <input type="text" class="form-control" name="MDCD" tabindex="20" value="" size="18">
                        </div>
                      </div>
                      
                      <!-- Status -->
                      <div class="col-sm-4">
                        <div class="form-group"><label for="delete_status">Status</label>
                          <select tabindex="21" name="delete_status" id="delete_status" class="selectpicker" data-width="100%">
                             <option value="0">Active</option>
                             <option value="1">In-Active</option>
                             <option value="2">Not-Confirmed</option>
                          </select>
                        </div>
                      </div>
                      
                      <!-- Direct Email -->
                      <div class="col-sm-4">
                        <div class="form-group"><label class="text_purple pointer" data-toggle="modal" data-target="#directMultiple"><strong>Direct Email</strong></label>
                          <input type="text" tabindex="22" class="form-control" name="direct_email" value="" id="direct_email" readonly>
                        </div>
                      </div>
                      
                      <!-- Comments -->
                      <div class="col-sm-12">
                        <div class="form-group"><label for="comments">Comments</label>
                          <textarea rows="1" cols="40"  name="comments" id="comments" class="form-control"></textarea>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <div class="clearfix"></div>
                  
                </div>
              </div>
  
            </div>
            
            <div class="clearfix"></div>
            
            <div class="grpbox no-min">
              <div class="head"><span>Login</span></div>
              <div class="clearfix"></div>
              <div class="tblBg">
                <div class="row">
                  <!-- Login -->
                  <div class="col-sm-3">
                    <div class="form-group"><label for="userName">Login</label>
                      <input name="userName" id="userName" type="text" class="form-control" tabindex="22" value="">
                    </div>
                  </div>
                  
                  <!-- Password -->
                  <div class="col-sm-3">
                    <div class="form-group"><label for="password">Password</label>
                      <input name="hid_password" id="hid_password" type="hidden" value="" >
                      <input name="password" id="password" type="password"  class="form-control" tabindex="23"  value="" >
                    </div>
                  </div>
                  
                  <!-- Confirm Password -->
                  <div class="col-sm-3">
                    <div class="form-group"><label for="confirm_password">Confirm Password</label>
                      <input name="confirm_password" id="confirm_password" type="password" class="form-control" tabindex="24" value="">
                    </div>
                  </div>
                  
                  <!-- EMR Priviliges -->
                  <div class="col-sm-3">
					<br />
                    <?php
                      $access_privileges = array('EMR','Front office'); 
                      foreach ($access_privileges as $s1){
                        $id = "access_pri_".preg_replace('/\s/','_',$s1);
                        echo '<div class="checkbox checkbox-inline">';
                        echo "<input tabindex='23' type='checkbox' name='access_pri[]' value='".$s1."' id='".$id."'>";
                        echo '<label for="'.$id.'">'.$s1.'</label>';
                        echo '</div>';
                      }
                    ?>
                  </div>
                  
                  <div class="clearfix"></div>
                  
                  <!-- Refer to Physician --> 
                  <div class="col-sm-3">
                    <div class="form-group"><label for="referedPhysician">Refer to Physician</label>
                      <select name="referedPhysician[]" id="referedPhysician" multiple  tabindex="24" class="selectpicker dropup" title="Refer to Physician" data-size="8" data-width="100%" data-live-search="true" data-actions-box="true" data-dropup-auto="false" >
                      </select>
                    </div>
                  </div>
                  
                  <!-- Facility -->
                  <div class="col-sm-3">
                    <div class="form-group"><label for="default_facility">Facility</label>
                      <select name="default_facility[]" id="default_facility" class="selectpicker dropup" tabindex="25" multiple onChange="getFedralEin();" title="Facility" data-size="8" data-width="100%" data-live-search="true" data-actions-box="true" data-dropup-auto="false" >
                      </select>
                    </div>
                  </div>
                  
                  <!-- Notice Days -->
                  <div class="col-sm-2">
                    <div class="form-group"><label for="noticeDays">Notice Days</label>
                      <select name="noticeDays" id="noticeDays" class="selectpicker" tabindex="26" title="Notice Days" data-width="100%" data-size="5">
                      </select>
                    </div>
                  </div>
                  
                  <!-- MAx Ref. Per Day -->
                  <div class="col-sm-2">
                    <div class="form-group"><label for="maxReferals">Max Ref./Day</label>
                      <input name="maxReferals" type="text" id="maxReferals" tabindex="27"  class="form-control" value="" >
                    </div>
                  </div>
                  
                  <!-- Default Group -->
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label for="default_group">Default Group</label>
                      <select name="default_group" id="default_group" class="selectpicker" tabindex="28" onChange="check_group(this.options[this.selectedIndex].value);" data-width="100%" title="Default Group" data-size="5">
                      <?php
                        $qry = "select * from groups_new where del_status='0'";
                        $res = imw_query($qry);
                        $rows = imw_num_rows($res);
                        $se="";
                        while($row = imw_fetch_array($res)){
                          echo("<option value='".$row['gro_id']."'>".stripslashes($row['name'])."</option>");
                        }
                      ?>
                      </select>
                    </div>
                  </div>
                  
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="clearfix"></div>
            </div>
            
          
        </div>
        
        <div class="modal-footer pd5">
          <input type="submit" class="btn btn-success" value="Save">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </form>
  </div>
</div>


<!-- Change Password Modal -->
<div id="div_chg_password" class="modal" role="dialog" >
	<div class="modal-dialog modal-sm">
  	<!-- Modal content-->
    <form name="frm_chg_password" id="frm_chg_password"  method="post" autocomplete="off" onSubmit="change_password(this);return false;">	
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title" id="modal_title">Change Password</h4>
        </div>
        
        <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
          
              <input type="hidden" name="pkId"  id="pkId" value="">
              <div class="col-xs-12">Password
                <input type="password" name="chg_password" id="chg_password" class="form-control">
              </div>
              
              <div class="clearfix"></div>
              
              <div class="col-xs-12">Confirm Password
                <input type="password" name="chg_confirm_password" id="chg_confirm_password" class="form-control">
              </div>
          
        </div>
        
        <div class="modal-footer">
          <input type="submit" class="btn btn-success" value="Done">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </form>
  </div>
</div>

<!-- Multiple direct Modal -->
<div id="directMultiple" class="modal" role="dialog" >
	<div class="modal-dialog">
    <!-- Modal content-->
    <form name="direct_multiple_add" id="direct_multiple_add"  method="post" autocomplete="off" onSubmit="">	
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title" id="modal_title">Multiple Direct Emails</h4>
        </div>

        <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
          <div class="row"><p class="text-center">Please wait...</p></div>
        </div>

        <div class="modal-footer">
          <input type="button" class="btn btn-success" value="Done" onClick="multipleDirect(this);return false;">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>