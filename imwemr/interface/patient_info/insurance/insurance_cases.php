<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php if($patient_id){ ?>         		

<div class="insurcase">
 	
  <div class="inshead">
    <h2><figure><img src="<?php echo $library_path  ?>/images/insuricon.png" alt=""/></figure>Insurance Case</h2>
  </div>
	<div class="clearfix"></div>
  
	<div >
  	 
    <div class="form-group">
    	<label for="">Choose Case</label>
      <?php 
				$ins_prev_cases = $data_obj->insurance_prev_cases($case_status);
			?>
      <select class="selectpicker" name="choose_prevcase" id="choose_prevcase" onChange="return choose_form();" data-width="100%" data-size="5" title="<?php echo imw_msg('drop_sel');?>">
        <option value="" selected><?php echo imw_msg('drop_sel');?></option>
        <?php
					$adminInsCaseTypeIdOfpatient = "";
					$ins_case_drop_down = '';
					$exists_case_id = array();
					$copy_to_ins_case = '';
					for($i=0;$i<count($ins_prev_cases);$i++)
					{
						$ins_caseid = $ins_prev_cases[$i]['ins_caseid'];
						$ins_case_type = $ins_prev_cases[$i]['ins_case_type'];
						$case_name = $ins_prev_cases[$i]['case_name'];
						$case_id = $ins_prev_cases[$i]['case_id'];
						
						array_push($exists_case_id,$case_id);
						
						$sel = ($_SESSION['currentCaseid'] == $ins_caseid) ? 'selected' : '';	
						if(($_SESSION['currentCaseid'] == $ins_caseid)) 
							$adminInsCaseTypeIdOfpatient = $case_id;
								
						
						if(strtolower($case_name) != 'self pay'){
							$copy_to_ins_case .= <<<DATA
								<option value="$ins_caseid" $sel>$case_name</option>
DATA;
							$ins_case_drop_down .= <<<DATA
								<option value="$ins_caseid" $sel>$case_name</option>
DATA;
						}
						echo '<option value="'.$ins_caseid.'" '.$sel.'>'.$case_name.' - '.$ins_caseid.'</option>';
					}
				?>
      </select>
      <input type="hidden" id="adminInsCaseTypeId" name="adminInsCaseTypeId" value="<?php echo $adminInsCaseTypeIdOfpatient; ?>"/>
      <input type="hidden" id="closeOpenBtVal" name="closeOpenBtVal" value="<?php echo $_REQUEST['closeOpenBtVal']; ?>" class="closebut" />
            
  	</div>
    
    <!-- Rendering NEW/DEL/Closed/Opened Cases Button -->
    <div class="clearfix text-center">
    	<input class="btn btn-info" type="button" name="newcase" id="newcase" onClick="return choose_newform(this.value);" value="New" />
      <input class="btn btn-success" type="button"  name="delcase" id="delcase" onClick="return choose_newform(this.value);" value="Delete" />
      <?php
				$case_type	=	($case_status == 'Close')	?		'Opened' :	'Closed';
				$caseTitle=	'Click to see '.(strtolower($case_type)).' cases';
			?>
      <input class="btn btn-danger" type="button" name="closecase" title="<?=$caseTitle?>" id="closecase" onClick="choose_newform(this.value);" value="<?php echo $case_type; ?> Cases" />
  	</div>
    <!-- Rendering NEW/DEL/Closed/Opened Cases Button -->
    
    <!-- Case Type DropDown -->
    <div class="form-group">
      <label for="">Case Type</label>
      <?php 
				$exists_case_id_str = join(',',$exists_case_id);
				$ins_case_type = $data_obj->insurance_case_types($exists_case_id_str);
			?>	
			<select class="selectpicker" name="inscasetype" id="inscasetype" data-width="100%" title="<?php echo imw_msg('drop_sel');?>" onchange="top.chk_change_in_form('',this,'InsTabDb',event);">
      	<option value="" selected><?php echo imw_msg('drop_sel'); ?></option>
        <?php
					for($i=0;$i<count($ins_case_type);$i++)
					{
						$select = $ins_case_type[$i]['case_id'] == $caseType ? 'selected' : '';	
						$case_id = $ins_case_type[$i]['case_id'];
						$case_name = $ins_case_type[$i]['case_name'];
						echo "<option value='".$case_id."' ".$select.">".$case_name."</option>";
					}
				?>							
			</select>
    </div>
    <!-- Case Type DropDown -->
    
 	</div>
  
  <div class="row">
    
    <!-- Readonly Field Case ID -->
    <div class="col-sm-6">
      <div class="form-group">
        <label for="">Case ID</label>
        <input type="text" class="form-control" name="case_id" id="case_id" value="<?php echo $Caseid;?>" readonly size="11" placeholder="">
      </div>
    </div>
  	
    <!-- Readonly Field Responsible PArty -->
    <div class="col-sm-6">
      <div class="form-group">
        <label for="">Resp. Party</label>
        <input type="text" name="case_resp_party" id="case_resp_party" readonly value="<?php echo $resName;?>" class="form-control" placeholder="" >
      </div>
    </div>
    
    <!-- Case Start Date -->
    <div class="col-sm-6">
      <div class="form-group">
        <label for="">Start Date</label>
        <div class="input-group">
          <input type="text" class="form-control datepicker" name="case_startdate" id="case_startdate" placeholder="" size="14" value="<?php echo $start_date; ?>" maxlength=10 title='<?php echo inter_date_format();?>' onBlur="check_date(this);" onkeyup="top.chk_change_in_form('<?php echo addslashes($start_date); ?>',this,'InsTabDb',event);">
          <label class="input-group-addon btn" for="case_startdate"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
        </div>
      </div>
    </div>
    
    <!-- Case End Date -->
    <div class="col-sm-6">
      <div class="form-group">
        <label for="">End Date</label>
        <div class="input-group">
          <input name="case_enddate" id="case_enddate" type="text" size="14" value="<?php echo $end_date; ?>" maxlength="10" class="datepicker form-control" title='<?php echo inter_date_format();?>' onBlur="checkdate(this);" onkeyup="top.chk_change_in_form('<?php echo addslashes($end_date); ?>',this,'InsTabDb',event);" placeholder="">
          <label class="input-group-addon btn" for="case_enddate"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
        </div>
      </div>
    </div>
    
    
    <!-- Case Status DropDown || Update Button -->
    <div class="col-sm-12">
      <div class="form-group">
        <label for="">Status</label>
        
        <div class="form-inline status">
          
          <select name="case_status" id="case_status" class="selectpicker" data-width="50%" onchange="top.chk_change_in_form('',this,'InsTabDb',event);">
            <option value="Open" <?php if($case_status == "Open"){echo("selected");}?>> Open </option>
            <option value="Close" <?php if($case_status == "Close" && $_SESSION['currentCaseid']) {echo("selected");}?>>Close</option>
          </select>
          
          <!-- Update Button -->
          <input type="button" class="btn btn-success" id="openCase" name="openCase" value="<?php echo $submitcap; ?>" onclick="<?php if(core_check_privilege(array("priv_vo_pt_info")) == true){ ?> view_only_acc_call(1); return false; <?php }else{ ?>return saveCase(this.value);<?php } ?>" >
       	</div>
      </div>
    </div>
  
    <div class="col-sm-12">
      <div class="form-group">
        <?php
          $audit_cases_str = "";
					if($data['policy_status'] == 1){
            //  Audit Functionality
						include_once("audit_cases.php");
          }
        ?>
      </div>
    </div>
    
  </div>
  
  <input type="hidden" id="hidDataInsCase" name="hidDataInsCase" value="<?php echo $audit_cases_str; ?>">
  
</div>

	<?php	if($insurance_case_type == '1') { ?>
  <br>
  <div class="accordion-box">
  	<div class="accordion">
    	<dl>
      	
        <dt>
        	<a href="#side_accordion1" aria-expanded="false" aria-controls="side_accordion1" class="accordion-title accordionTitle js-accordionTrigger">Primary&nbsp;</a>
      	</dt>
        
        <dd class="accordion-content accordionItem is-collapsed" id="side_accordion1" aria-hidden="true">
        
        		<?php
							$temp_data = ''; $i = 0;
							if($inactivePriInsComp){
								$qry = imw_query("select provider,referal_required,actInsComp from insurance_data where id = '".$inactivePriInsComp."'");
								$priRes = imw_fetch_object($qry);
							}
							if($priRes->referal_required == 'Yes')
							{
								$qry = "SELECT no_of_reffs,effective_date,reff_used 								
														FROM `patient_reff` WHERE reff_type = 1 and del_status = 0 
														and patient_id = '".$patientDetail->id."' and ins_provider = '".$priRes->provider."' 
														and  (end_date >= current_date() or end_date = '0000-00-00') 
														and insCaseid = '".$_SESSION['currentCaseid']."' AND ins_data_id = '".$inactivePriInsComp."'
														order by reff_id desc";
								$sql = imw_query($qry);
								while($row = imw_fetch_assoc($sql))
								{
									$i++;
									$class = "" ;
									$priAval = '-';
									$reff_used = '-';
									$no_of_reffs = '-';
									
									if($row['no_of_reffs'] > 0)
									{
										$priAval = $row['no_of_reffs'];		
										$reff_used = $row['reff_used'];
										$no_of_reffs = $priAval + $reff_used;
										if($priRes->actInsComp == 1 && $priAval > 1){
											$class = "text-success";
										}
										else if($priAval == 1 && $priRes->actInsComp == 1){
											$class = "text-warning";
										}
										else{
											$class = "text-danger";
										}								
									}
									$temp_data .='
										<tr class="font-weight-bold '.$class.'">
											<td data-label="Available">'.$priAval.'</td>
											<td data-label="Used">'.$reff_used.'</td>
											<td data-label="Total">'.$no_of_reffs.'</td>
										</tr>
									';
							
								}
							}
							if($i == 0)
							{
								$temp_data = '<tr class=" font-weight-bold"><th colspan="3">'.imw_msg('no_rec').'</th></tr>';
							}
					
						?>
            
            <table class="table table-bordered table-hover table-striped scroll release-table">
                <thead class="header">
                  <tr class="grythead">
                    <th class="col-sm-4">Available</th>
                    <th class="col-sm-4">Used</th>
                    <th class="col-sm-4">Total</th>
                  </tr>
                </thead>
                <tbody>
                	<?php echo $temp_data; ?>
                </tbody>
          	</table>
                  
       	</dd>
        	
        
        <dt>
        	<a href="#side_accordion2" aria-expanded="false" aria-controls="side_accordion2" class="accordion-title accordionTitle js-accordionTrigger">Secondary&nbsp;</a>
       	</dt>
        
        <dd class="accordion-content accordionItem is-collapsed" id="side_accordion2" aria-hidden="true">
        
        	<?php
						$temp_data2 = '';$k = 0;
						if($inactiveSecInsComp){
							$qry = imw_query("select provider,referal_required,actInsComp from insurance_data where id = '".$inactiveSecInsComp."'");
							$secRes = imw_fetch_object($qry);
						}
						if($secRes->referal_required == 'Yes')
						{
							$qry = "SELECT no_of_reffs,effective_date,reff_used 						 		
									FROM patient_reff WHERE reff_type = 2 and del_status = 0 
									and patient_id = '".$patientDetail->id."' and ins_provider = '".$secRes->provider."'
									and (end_date >= current_date() or end_date = '0000-00-00') 
									and insCaseid = '".$_SESSION['currentCaseid']."' and ins_data_id = '".$inactiveSecInsComp."'
									order by reff_id desc";
							$qryId = imw_query($qry);
							
							while($reffRes2 = imw_fetch_assoc($qryId))
							{
									$k++;
									$class2 = '';
									$secAval = '-';
									$reff_used2 = '-';
									$no_of_reffs2 = '-';
								
								if($reffRes2[$i]['no_of_reffs'] > 0)
								{
									$secAval = $reffRes2[$i]['no_of_reffs'];		
									$reff_used2 = $reffRes2[$i]['reff_used'];
									$no_of_reffs2 = $secAval + $reff_used2;
									if($secRes->actInsComp == 1 && $secAval > 1){
										$class2 = "text-success";
									}
									else if($secAval == 1 && $secRes->actInsComp == 1){
										$class2 = "text-warning";
									}
									else{
										$class2 = "text-danger";
									}											
								}
								
								$temp_data2 .='
									<tr class=" font-weight-bold '.$class2.'">
										<th data-label="Available">'.$secAval.'</th>
										<th data-label="Used">'.$reff_used2.'</th>
										<th data-label="Total">'.$no_of_reffs2.'</th>
									</tr>
								';
								
							}
						}
						if($k == 0)
						{
							$temp_data2 = '<tr class=" font-weight-bold"><th colspan="3">'.imw_msg('no_rec').'</th></tr>';
						}
					?>
          
					<table class="table table-bordered table-hover table-striped scroll release-table">
                <thead class="header">
                  <tr class="grythead">
                    <th class="col-sm-4">Available</th>
                    <th class="col-sm-4">Used</th>
                    <th class="col-sm-4">Total</th>
                  </tr>
                </thead>
                <tbody>
                	<?php echo $temp_data2; ?>
                </tbody>
          	</table>
       	
        </dd>
        
        
        <dt>
        	<a href="#side_accordion3" aria-expanded="false" aria-controls="side_accordion2" class="accordion-title accordionTitle js-accordionTrigger">Tertiary&nbsp;</a>
       	</dt>
        
        <dd class="accordion-content accordionItem is-collapsed" id="side_accordion3" aria-hidden="true">
        	
					<?php
						$temp_data3 = ''; $m = 0;
						if($inactiveTerInsComp){
							$qry = imw_query("select provider,referal_required,actInsComp from insurance_data where id = '".$inactiveTerInsComp."'");
							$terRes = imw_fetch_object($qry);
						}
						if($terRes->referal_required == 'Yes')
						{
							$qry = "SELECT no_of_reffs,effective_date,reff_used 
									FROM patient_reff WHERE reff_type = 3 and del_status = 0 
									and patient_id = '".$patientDetail->id."' AND ins_provider = '".$terRes->provider."'
									and (end_date >= current_date() or end_date = '0000-00-00') 
									and insCaseid = '".$_SESSION['currentCaseid']."' and ins_data_id = '".$inactiveTerInsComp."'
									order by reff_id desc";
							$qryId = imw_query($qry);
							while($reffRes3 = imw_fetch_assoc($qryId))
							{
									$m++;
									$class3 = '';
									$terAval = '-';
									$reff_used3 = '-';
									$no_of_reffs3 = '-';
						
									if($reffRes3[$i]['no_of_reffs'] > 0)
									{
										$terAval = $reffRes3[$i]['no_of_reffs'];		
										$reff_used3 = $reffRes3[$i]['reff_used'];
										$no_of_reffs3 = $terAval + $reff_used3;
										if($terRes->actInsComp == 1 && $terAval > 1){
											$class3 = " text-success";
										}
										else if($terAval == 1 && $terRes->actInsComp == 1){
											$class3 = " text-warning";
										}
										else{
											$class3 = "text-danger";
										}										
									}
									
									$temp_data3 .='
										<tr class="font-weight-bold '.$class3.'">
											<td align="center" data-label="Available">'.$terAval.'</td>
											<td align="center" data-label="Used">'.$reff_used3.'</td>
											<td align="center" data-label="Total">'.$no_of_reffs3.'</td>
										</tr>
									';
								}
							
						}
						
						if($m == 0)
						{
							$temp_data3 = '<tr class="font-weight-bold"><th colspan="3">'.imw_msg('no_rec').'</th></tr>';
						}
					?>
          
          <table class="table table-bordered table-hover table-striped scroll release-table">
              <thead class="header">
                <tr class="grythead">
                  <th class="col-sm-4">Available</th>
                  <th class="col-sm-4">Used</th>
                  <th class="col-sm-4">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $temp_data3; ?>
              </tbody>
          </table>
       	
        </dd>
        
    	</dl>
  	</div>
	</div>        
  
  <?php } ?>
  
<?php } ?>