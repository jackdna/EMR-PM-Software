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
include_once($GLOBALS['srcdir']."/classes/medical_hx/general_health.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$health = new GeneralHealth($medical->current_tab);
$vs_data = $health->get_vs_data();
extract($health->data);
?>
<style type="text/css">
	.input-group-addon { font-size:11px; padding:3px!important;}
	.popover { min-width:350px!important;max-width:350px!important;}
	.popover-content {min-height:400px; }
</style>
<div id="bloodSugarDivMain" style="display:none; z-index:1000;"></div>
<div id="cholesterolDivMain" style="display:none; z-index:1000;"></div>
<span id="ref_pcp_coman_details_span" class="div_popup white border padd5 hide" style="height:300px; width:245px; overflow-x:hidden; overflow-y:auto;">
	<span class="closeBtn" onClick="hideDetail();"></span>
  <span id="ref_pcp_coman_details_span_inner"></span>
</span>

<div class="col-xs-12">
	
 	<form action="<?php echo $folder;?>/save.php" method="post" name="general_form" id="general_form">
	<input type="hidden" name="ptFormId" id="ptFormId" value="<?php echo $vs_data['Pt_Form_Id']; ?>">
  	<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($health->vocabulary) && count($health->vocabulary) > 0) ? urlencode(serialize($health->vocabulary)) : "");?>">
    <input type="hidden" name="patient_id_genHealth" id="patient_id_genHealth" value="<?php echo $health->patient_id; ?>">
    <input type="hidden" name="preObjBack" id="preObjBack" value="">
    <input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $health->current_tab;?>">
    <input type="hidden" name="next_tab" id="next_tab" value="">
    <input type="hidden" name="next_dir" id="next_dir" value="">
		<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">	
    <div id="divMultiPCPMedHx" class="section mt10 m5" style="display:none; z-index:1000; position:absolute; left:260px; top:15px; height:330px; width:250px;" onMouseDown="drag_move_div(this, event);">
   	</div>
    
    <div class="row">
    	<!-- Primary Care Physician -->
      <div class="col-lg-6 col-md-12 col-sm-12 primcare">
      	<div class=""><div class="head"><span class="valign_mid">Primary Care Physician</span></div>
        <div class="clearfix"></div>
        <?php
						$delete_class = is_refPhy_deleted($gen_medicine['med_doctor_id']) ? 'text-danger' : '';
						if(empty($strPCPPhyMulti) == false){
							if(empty($patPCPMedHx) == false){
								$patPCPMedHx .= "; ".$strPCPPhyMulti;
							}
							else{
								$patPCPMedHx = $strPCPPhyMulti;
							}
						}
      	?>
        <div class="row">
        	<div class="col-sm-6">
          	<div class="form-group">
            	<label class="text_purple pointer" onclick="show_multi_phy(1,3);">Primary Care Provider</label>
            	<?php 
								$popoverHtmlAttr = ($popover) ? 'data-trigger="focus" data-toggle="popover" data-html="true" ' : ''; 
							?>
              <input type="text" name="med_doctor" id="med_doctor" onBlur="refine_data(this);" onKeyUp="top.loadPhysicians(this,'hidd_med_doctor'); chk_change('<?php echo addslashes($patPCPMedHx); ?>',this,event);" class="form-control <?php echo $delete_class; ?>" value="<?php echo $patPCPMedHx;?>" onFocus="top.loadPhysicians(this,'hidd_med_doctor');"  <?php echo $popoverHtmlAttr; ?> data-placement="right" data-title="Primary Care Physicians"  data-content="<?php echo $popover;?>" />
             	<input type="hidden" name="hidd_med_doctor" id="hidd_med_doctor" value="<?php echo $gen_medicine['med_doctor_id'];?>">
           	</div>
        	</div>
          
          <div class="col-sm-6">
          	<div class="form-group">
            	<label for="">Assigned Nurse</label>
          		<select class="selectpicker" data-width="100%" name="assigned_nurse" id="assigned_nurse" onChange="top.chk_change_in_form('',this,'DemoTabDb',event); changeClassCombo(this,'selectpicker');" title="Assigned Nurse">
              	<option value=""></option>
                <?php
									echo $cls_common->drop_down_providers($gen_medicine["assigned_nurse"],'','2');
								?>
            	</select>
          	</div>
        	</div>
     		</div>
        
        <div class="clearfix"></div>
    	</div>
      </div>
      
      <!-- Advance Directive -->
      <div class="col-lg-6 col-md-12 col-sm-12 advancedirect">
      	<div class="head"><span class="valign_mid">Advance Directive</span></div>
        <div class="clearfix"></div>
        <div class="row">
        	<div class="col-sm-5">
          	<label for="">Advance Directive</label><br>
            <?php
							//and form_id='0'
						 	$query = "select * from ".constant("IMEDIC_SCAN_DB").".scans where patient_id = '".$health->patient_id."' And image_form = 'ptInfoMedHxGeneralHealth'";
							$sql = imw_query($query);
							$row = imw_fetch_assoc($sql);
							$file_name = $row['file_path'];
							$ad_path = substr(data_path(),0,-1).$file_name;
							$ad_web_path = substr(data_path(1),0,-1).$file_name;
							$scan_id = $row["scan_id"];
							$image_form = $row["image_form"];
						?>
            <div id="div_ado_option">
              <select class="selectpicker" data-width="100%" data-size="10" onChange="javascript:show_hide('other_ado_option','div_ado_option',this); chk_change('',this,event);" name="ado_option" id="ado_option" title="Advance Directive" >
                <option <?php echo($gen_medicine["ptAdoOption"]=="NA" ? "selected" : ""); ?> value="NA">NA</option>
                <option <?php echo($gen_medicine["ptAdoOption"]=="No" ? "selected" : ""); ?> value="No">No</option>
                <option <?php echo($gen_medicine["ptAdoOption"]=="Living Will" ? "selected" : ""); ?> value="Living Will">Living Will</option>
                <option <?php echo($gen_medicine["ptAdoOption"]=="Power of Attorney" ? "selected" : ""); ?> value="Power of Attorney">Power of Attorney</option>
                <option <?php echo($gen_medicine["ptAdoOption"]=="Other" ? "selected" : ""); ?> value="Other">Other</option>
              </select>
           	</div>
             
            <div class="hidden" id="other_ado_option">
            	<div class="input-group">
              	<input type="text" class="form-control" id="ado_other_txt" name="ado_other_txt" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["ptDescAdoOtherTxt"]); ?>',this,event);" value="<?php echo $gen_medicine["ptDescAdoOtherTxt"]; ?>" />
               	<label class="input-group-addon btn btn-success back_other" data-tab-name="ado_option">
                	<span class="glyphicon glyphicon-arrow-left"></span>
               	</label>
            	</div>
          	</div>        
        	
          </div>
          
          <div class="col-sm-2"><br>
          	<label class="btn btn-success mt5 btn-xs" onClick="ado_scan_fun('scan', 'ptInfoMedHxGeneralHealth')" style="font-size:13px;">
            	<i class="glyphicon glyphicon-print"></i>
          	</label>
            <span id="scnGenHlthId">
            	<?php if( $scan_id <> '' ){ ?>
              <label class="btn btn-success mt5 btn-xs" id="" data-path="<?php echo $ad_web_path;?>" onClick="showpdf('<?php echo $scan_id; ?>','','<?php echo $image_form; ?>')" style="font-size:13px;">
              	<i class="glyphicon glyphicon-open-file"></i>
             	</label>
          	<?php } ?>
            </span>
        	</div>
          
      	</div>
        <div class="clearfix"></div>
			</div>
 			
  	</div>
    
    <div class="clearfix"></div>
    
    <!-- Blood Sugar -->
    <div class="row">
    	<div class="col-sm-12 col-lg-7 bloodsug ">
      
      	<div class="row border">
      
      	<div class="head">
          <div class="row">
            <div class=" col-sm-7 col-md-5 col-lg-6 ">
              <span class="valign_mid">Blood Sugar <label class="glyphicon glyphicon-info-sign pointer font-18" data-toggle="tooltip" data-placement="bottom" title="Info Button" onclick='javascript: var labInfoWin = window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=790.29&mainSearchCriteria.v.cs=2.16.840.1.113883.6.103&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","Lab","height=700,width=1000,top=50,left=50");'></label></span>
            </div>
          
            <div class="col-sm-5 col-md-7 col-lg-6 text-right content_box">
              <button class="btn btn-success" type="button" data-toggle="modal" data-target="#bs_history" >View History</button>
              <button class="btn btn-success" type="button" onClick="javascript:save_new('bs_save');">Add New</button>
            </div>
        	</div>
        </div>
        
        <div class="clearfix"></div>
        
        <?php 
          $query = "select *, Date_Format(creation_date,'%c-%e-%Y') as date,
                              Date_Format(creation_date,'%m-%d-%Y') as createdDate 
                          from patient_blood_sugar where patient_id = '".$health->patient_id."' ORDER BY creation_date DESC LIMIT 1";
          $sql = imw_query($query);
          $this_blood_sugar = imw_fetch_assoc($sql);
          $str_this_bs_date = "";
          
          if($this_blood_sugar["createdDate"] != "")
          {
            $str_this_bs_date = get_date_format($this_blood_sugar["createdDate"],'mm-dd-yyyy');
          }
        ?>
        
        <input type="hidden" value="<?php echo(xss_rem($this_blood_sugar["id"])); ?>" name="this_blood_sugar_id" id="this_blood_sugar_id"/>               
        <div class="col-lg-4 col-md-8 col-sm-8">
          <div class="row">
             <!-- Blood Sugar Date -->
            <div class="col-sm-6 col-lg-6">
              <div class="form-group">
            <label for="this_blood_sugar_date">Date</label>
            <div class="input-group">
              <input type="text" name="this_blood_sugar_date" id="this_blood_sugar_date" onKeyUp="chk_change('<?php echo addslashes($str_this_bs_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($str_this_bs_date); ?>',this,event);" value="<?php echo $str_this_bs_date;?>" onBlur="checkdate(this);" title="<?php echo $GLOBALS['date_format'];?>" class="datepicker form-control" onClick="getDate_and_setToField(this)"/>
              <label class="input-group-addon pointer" for="this_blood_sugar_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
            </div>
          </div>
            </div>
            
             <!-- Blood Sugar (mg/dl) -->
            <div class="col-sm-6 col-md-6">
              <div class="form-group">
                <label for="this_blood_sugar" class="nowrap">Blood Sugar
                <div class="inline visible-lg"><small>(mg/dl)</small></div></label>
                <div class="input-group">
                  <input type="text" name="this_blood_sugar" id="this_blood_sugar" onKeyUp="chk_change('<?php echo addslashes($this_blood_sugar["sugar_value"]); ?>',this,event);" value="<?php echo($this_blood_sugar["sugar_value"]); ?>" size="3" class="form-control" />
                  <label class="input-group-addon btn " data-toggle="modal" data-target="#blood_sugar_graph_modal"><i class="glyphicon glyphicon-signal" aria-hidden="true"></i></label>
                </div>
              </div>
            </div>
            
            
            
          </div>
        </div>
        
        <div class="col-lg-5 col-md-4 col-sm-4">
          <div class="row">
          
             <!-- Blood Sugar HbA1c -->
            <div class="col-sm-3 col-md-2">
              <div class="form-group">
                <label for="">HbA1c</label>
                <input type="text" name="this_blood_sugar_hba1c_val" id="this_blood_sugar_hba1c_val" onKeyUp="chk_change('<?php echo addslashes($this_blood_sugar["hba1c_val"]); ?>',this,event);" value="<?php echo($this_blood_sugar["hba1c_val"]); ?>" size="3" class="form-control" />
              </div>
            </div>
        
            <div class="col-sm-5 col-md-4">
            	<label>&nbsp;</label>
              <select class="selectpicker" data-width="100%" data-title="Select" name="this_blood_sugar_hba1c" id="this_blood_sugar_hba1c" onChange="top.fmain.chk_change('',this,event);" >
                <?php 
                  $blood_sugar_opt_str = '';
                  foreach($gen_medicine['blood_sugar_opt_arr'] as $key => $val){
                    $checked = html_entity_decode($this_blood_sugar["hba1c"])==$val ? "selected" : "";
                    $blood_sugar_opt_str .= '<option value="'.$val.'" '.$checked.'>'.$val.'</option>';
                  }
                  echo $blood_sugar_opt_str;
                ?>	
              </select>	
            </div>
            
            <div class="col-sm-3 col-md-2 text-center">
            	<div class="row">
              	<label for="this_blood_sugar_fasting">Fasting</label>
                <div class="checkbox checkbox-inline">
                  <input type="checkbox" name="this_blood_sugar_fasting" id="this_blood_sugar_fasting" onClick="chk_change('<?php echo addslashes($this_blood_sugar["is_fasting"]==1?"checked":""); ?>',this,event);" <?php echo($this_blood_sugar["is_fasting"]==1?"checked":""); ?> value="1"/>
                  <label for="this_blood_sugar_fasting"></label>
                </div>
             	</div>
            </div>
            
            <div class="col-sm-4 col-md-4">
              <div class="form-group">
                <label for="this_blood_sugar_time" class="nowrap">Time of Day</label>
                <div id="div_this_blood_sugar_time">
                  <select name="this_blood_sugar_time" id="this_blood_sugar_time" class="selectpicker" onChange="javascript:show_hide('other_this_blood_sugar_time','div_this_blood_sugar_time',this); chk_change('',this,event);" title="Time of Day" data-width="100%">
                      <option value="0-" ></option>
                      <option value="1-Morning" <?php echo($this_blood_sugar["time_of_day"]=="Morning"?"selected":""); ?>>Morning</option>
                      <option value="2-Post Breakfast" <?php echo($this_blood_sugar["time_of_day"]=="Post Breakfast"?"selected":""); ?>>Post Breakfast</option>
                      <option value="3-Afternoon" <?php echo($this_blood_sugar["time_of_day"]=="Afternoon"?"selected":""); ?>>Afternoon</option>
                      <option value="4-Post Lunch" <?php echo($this_blood_sugar["time_of_day"]=="Post Lunch"?"selected":""); ?>>Post Lunch</option>
                      <option value="5-Evening" <?php echo($this_blood_sugar["time_of_day"]=="Evening"?"selected":""); ?>>Evening</option>
                      <option value="6-Night" <?php echo($this_blood_sugar["time_of_day"]=="Night"?"selected":""); ?>>Night</option>
                      <option value="7-Post Dinner" <?php echo($this_blood_sugar["time_of_day"]=="Post Dinner"?"selected":""); ?>>Post Dinner</option>
                      <option value="8-Other" <?php echo($this_blood_sugar["time_of_day"]=="Other"?"selected":""); ?>>Other</option>
                  </select>
                </div>
                
                <div id="other_this_blood_sugar_time" class="hidden">
                  <div class="input-group">
                    <input type="text" class="form-control" id="this_blood_sugar_other" name="this_blood_sugar_other" onKeyUp="chk_change('<?php echo addslashes($this_blood_sugar["time_of_day_other"]); ?>',this,event);" value="<?php echo $this_blood_sugar["time_of_day_other"]; ?>" />
                    <label class="input-group-addon btn back_other" data-tab-name="this_blood_sugar_time"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
        
        <div class="clearfix hidden-lg"></div>
        
        <div class="col-lg-3 col-md-12 col-sm-12">
          <div class="row">
            <div class="col-xs-12">
              <label for="">Description</label>
              <textarea name="this_blood_sugar_desc" id="this_blood_sugar_desc" rows="1" cols="10" onKeyUp="chk_change('<?php echo addslashes($this_blood_sugar["description"]); ?>',this,event);" onFocus="if($('#this_blood_sugar_date').val()==''){$('#this_blood_sugar_date').val('<?php echo date("m-d-Y"); ?>');}" class="form-control" ><?php echo($this_blood_sugar["description"]); ?></textarea>
            </div>
          </div>
        </div>
        
        </div>
    	
      </div>
      
      <div class="clearfix hidden-lg"></div>
      
      <div class="col-sm-12 col-lg-5 chole">
      	
      	<?php
					$query = "select *, Date_Format(creation_date,'%c-%e-%Y') as date,
															Date_Format(creation_date,'%m-%d-%Y') as createdDate
													from patient_cholesterol where patient_id = '".$health->patient_id."' ORDER BY creation_date DESC LIMIT 1";
					$sql = imw_query($query);
					$this_cholesterol = imw_fetch_assoc($sql);
					$str_this_c_date = "";
					if($this_cholesterol["createdDate"] != ""){
							$arr_this_c_datetime = explode(" ",$this_cholesterol["creation_date"]);
							$arr_this_c_date = explode("-",$arr_this_c_datetime[0]);
							$str_this_c_date = $arr_this_c_date[1]."-".$arr_this_c_date[2]."-".$arr_this_c_date[0];
							$str_this_c_date = get_date_format($this_cholesterol["createdDate"],'mm-dd-yyyy');
					}
			?>
      	<input type="hidden" value="<?php echo($this_cholesterol["id"]); ?>" name="this_cholesterol_id" id="this_cholesterol_id"/>
     		<div class="row border">
         
          <div class="head">
            <div class="row">
              <div class="col-sm-7 col-md-4 col-lg-6 ">
                <span class="valign_mid nowrap">Cholesterol <label class="glyphicon glyphicon-info-sign pointer font-18" data-toggle="tooltip" data-placement="bottom" title="Info Button" onclick='javascript: var labInfoWin = window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=272.0&mainSearchCriteria.v.cs=2.16.840.1.113883.6.103&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","Lab","height=700,width=1000,top=50,left=50");'></label></span>
              </div>
            
              <div class="col-sm-5 col-md-8 col-lg-6 text-right content_box">
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#ch_history">View History</button>
                <button class="btn btn-success" type="button" onClick="javascript:save_new('c_save');">Add New</button>
              </div>
            </div>
          </div>
     	
      	
          <div class="col-lg-6 col-md-8 col-sm-7">
            <div class="row">
               <!-- Cholesterol Date -->
              <div class="col-sm-5 col-md-7 col-lg-6">
                <div class="form-group">
                  <label for="">Date</label>
                  <div class="input-group">
                    <input type="text" class="form-control datepicker" name="this_cholesterol_date" id="this_cholesterol_date" title="mm-dd-yy" onBlur="checkdate(this);" onClick="getDate_and_setToField(this)" onKeyUp="chk_change('<?php echo addslashes($str_this_c_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($str_this_c_date); ?>',this,event);" value="<?php echo($str_this_c_date);?>"/>
                    <label class="input-group-addon pointer" for="this_cholesterol_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                  </div>
                </div>
              </div>
              
              <!-- Cholesterol Total -->
              <div class="col-sm-5 col-md-3 col-lg-4">
                <div class="form-group">
                  <label for="this_cholesterol_total">Total</label>
                  <div class="input-group">
                    <input type="text" name="this_cholesterol_total" id="this_cholesterol_total" class="form-control" onKeyUp="chk_change('<?php echo addslashes($this_cholesterol["cholesterol_total"]); ?>',this,event);" value="<?php echo($this_cholesterol["cholesterol_total"]);?>" />
                    <label class="input-group-addon btn " data-toggle="modal" data-target="#cholesterol_graph_modal"><i class="glyphicon glyphicon-signal" aria-hidden="true"></i></label>
                  </div>
                </div>
              </div>
              
              <!-- Cholesterol Triglycerides -->
              <div class="col-sm-2 col-lg-2">
                <div class="form-group">
                  <label for="this_cholesterol_triglycerides">Trig.</label>
                  <input type="text" class="form-control" name="this_cholesterol_triglycerides" id="this_cholesterol_triglycerides" onKeyUp="chk_change('<?php echo addslashes($this_cholesterol["cholesterol_triglycerides"]); ?>',this,event);" value="<?php echo($this_cholesterol["cholesterol_triglycerides"]);?>" />
                </div>
              </div>
            </div>
          </div>
      
          <div class="col-lg-3 col-md-4 col-sm-5">
            <div class="row">
            
              <!-- Cholesterol LDL -->
              <div class="col-sm-6 ">
                <div class="form-group">
                  <label for="this_cholesterol_LDL">LDL</label>
                  <div class="input-group">
                    <input type="text" name="this_cholesterol_LDL" class="form-control" id="this_cholesterol_LDL" onKeyUp="chk_change('<?php echo addslashes($this_cholesterol["cholesterol_LDL"]); ?>',this,event);" value="<?php echo($this_cholesterol["cholesterol_LDL"]);?>" />
                    <label class="input-group-addon btn" title="LDL Graph" data-toggle="modal" data-target="#cholesterol_ldl_graph_modal"><i class="glyphicon glyphicon-signal" aria-hidden="true"></i></label>
                  </div>
                </div>
              </div>
              
              <!-- Cholesterol HDL -->
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="this_cholesterol_HDL">HDL</label>
                  <div class="input-group">
                    <input type="text" name="this_cholesterol_HDL" class="form-control" id="this_cholesterol_HDL" onKeyUp="chk_change('<?php echo addslashes($this_cholesterol["cholesterol_HDL"]); ?>',this,event);" value="<?php echo($this_cholesterol["cholesterol_HDL"]);?>" />
                    <label class="input-group-addon btn" title="HDL Graph" data-toggle="modal" data-target="#cholesterol_hdl_graph_modal"><i class="glyphicon glyphicon-signal" aria-hidden="true"></i></label>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
      
      		<div class="clearfix hidden-lg"></div>
    	
      		<!-- Cholesterol Description -->
          <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="row">
              <div class="col-xs-12">
                <label for="this_cholesterol_desc">Description</label>
                <textarea rows="1" cols="10" name="this_cholesterol_desc" id="this_cholesterol_desc" onKeyUp="chk_change('<?php echo addslashes($this_cholesterol["description"]); ?>',this,event);" onFocus="if($('#this_cholesterol_date').val()==''){$('#this_cholesterol_date').val('<?php echo date("m-d-Y"); ?>');}" class="form-control" ><?php echo($this_cholesterol["description"]);?></textarea>
              </div>
            </div>
          </div>
      	
        </div>
      </div>
      
    </div>
    
    <!-- Cholesterol -->
    <div class="row">
    	
      
      
    	
      
      
   	</div>
    
    <!-- Medical Conditions -->
    <div class="row">
    	<div class="col-sm-12 ">
      	<div class="row head">
       		<span class="valign_mid">Medical Conditions</span>
        </div>
     	</div>
      
      <div class="clearfix"></div>
      
    	<div class="col-lg-6 col-md-12 col-sm-12">
      	<div class="medcondbox">
        	<h3>Please mark any condition you have presently or have had in the past</h3>
      		<div class="clearfix"></div>
        	<div class="table-responsive">
          	<table class="table table-striped  table-hover">
            	
              <tr class="hghtd">
              	<?php
									$strCbkMasterPtConYes = $strCbkMasterPtConNo = "";
									if($gen_medicine["cbk_master_pt_con"] == "yes"){
											$strCbkMasterPtConYes = "checked";
									}
									else if($gen_medicine["cbk_master_pt_con"] == "no"){
											$strCbkMasterPtConNo = "checked";
									}
								?>
                <td class="col-xs-3">No Known patient medical condition</td>
                <td class="col-xs-1 ysrado text-center"><label>Yes</label>
                  
               	</td>
                <td class="col-xs-1 norado">
                	<div class="checkbox">
                    <input type="checkbox" <?php echo $strCbkMasterPtConNo; ?> id="cbkMasterPtConN" name='cbkMasterPtCon' value="no" onClick="chk_change('<?php echo addslashes($strCbkMasterPtConNo); ?>',this,event); chkAll('no', this); chkCorrespondingChks(this,'chk_under_control[]',true);">
                    <label for="cbkMasterPtConN">No</label>
                  </div>
               	</td>
                <td class="col-xs-3">&nbsp;</td>
                <td class="col-xs-4">&nbsp;</td>
            	</tr>
              
              <!-- Patient High Blood Pressure -->
              <tr>
                <td>High&nbsp;Blood&nbsp;Pressure</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acya_p1[1]; ?> id="selfHighBp" name="any_conditions_u1[]" value="1" onClick="chk_change('<?php echo addslashes($acya_p1[1]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfHighBpN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatHighBP'),document.getElementById('chk_blood_presher_under_control'),'<?php echo $is_checked_under_control[1]; ?>');">
                  	<label for="selfHighBp"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[1]; ?> id="selfHighBpN" name="any_conditions_u1_n[]" value="1" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[1]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfHighBp'), document.getElementById('tdPatHighBP'), document.getElementById('chk_blood_presher_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfHighBpN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatHighBP" class="<?php echo ($acya_p1[1] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[1]); ?>',this,event);" <?php echo $is_checked_under_control[1];?> name="chk_under_control[]" id="chk_blood_presher_under_control" value="1" />
                        <label for="chk_blood_presher_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strHighBloodPresherTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_high_bp"],"pat",$health->delimiter);?>
                  <input id="txtHighBloodPresher" name="txtHighBloodPresher" onKeyUp="chk_change('<?php echo addslashes($strHighBloodPresherTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strHighBloodPresherTxtPat)); ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Patient Heart Problem -->
              <tr>
                <td>Heart&nbsp;Problem</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php print $acya_p1[2]; ?> id="selfHeart" name="any_conditions_u1[]" value="2" onClick="chk_change('<?php echo addslashes($acya_p1[2]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfHeartN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatHeartProb'),document.getElementById('chk_heart_problem_under_control'),'<?php echo $is_checked_under_control[2]; ?>');">
                  	<label for="selfHeart"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[2]; ?>  id="selfHeartN" name="any_conditions_u1_n[]" value="2" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[2]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfHeart'), document.getElementById('tdPatHeartProb'), document.getElementById('chk_heart_problem_under_control'), '', document.getElementById('cbkMasterPtConN')); ">
                 		<label for="selfHeartN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatHeartProb" class="<?php echo ($acya_p1[2] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[2]); ?>',this,event);" <?php echo $is_checked_under_control[2]; ?> name="chk_under_control[]" id="chk_heart_problem_under_control" value="2" />
                        <label for="chk_heart_problem_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strHeartTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_heart_problem"],"pat",$health->delimiter);?>
                  <input id="txtHeartProblem" name="txtHeartProblem" onKeyUp="chk_change('<?php echo addslashes($strHeartTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strHeartTxtPat)); ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Patient Arthritis -->
              <tr>
                <td>Arthritis</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php print $acya_p1[7]; ?>  id="selfArthritis" name="any_conditions_u1[]" value="7" onClick="chk_change('<?php echo addslashes($acya_p1[7]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfArthritisN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatArthritis'),document.getElementById('chk_arthritis_under_control'),'<?php echo $is_checked_under_control[3]; ?>');">
                  	<label for="selfArthritis"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[7]; ?>  id="selfArthritisN" name="any_conditions_u1_n[]" value="7"onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[7]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfArthritis'), document.getElementById('tdPatArthritis'), document.getElementById('chk_arthritis_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfArthritisN">&nbsp;</label>
               		</div>
                </td>
                <td >
                		<div class="col-xs-4">
                      <div class="checkbox">
                        <input type="checkbox" name='elem_subCondition_u1[]' id="elem_subCondition_u1_71" value="7.1" onClick="chk_change('<?php echo addslashes(in_array("7.1",$elem_subCondition_u1) ? "checked" : ""); ?>',this,event);" <?php echo in_array("7.1",$elem_subCondition_u1) ? "checked" : "" ; ?>>	
                        <label for="elem_subCondition_u1_71">RA</label>
                      </div>   
                    </div>
                    <div class="col-xs-3">  	 
                      <div class="checkbox " style="display:inline-block;">
                        <input type="checkbox" name='elem_subCondition_u1[]' id="elem_subCondition_u1_72" value="7.2" onClick="chk_change('<?php echo addslashes(in_array("7.2",$elem_subCondition_u1) ? "checked" : ""); ?>',this,event);" <?php echo in_array("7.2",$elem_subCondition_u1) ? "checked" : "" ; ?>>
                        <label for="elem_subCondition_u1_72">OA</label>
                      </div> 
                    </div>  
                		<div class="pull-right">
                      <div id="tdPatArthritis" class="<?php echo ($acya_p1[7] == "checked") ? "show" : "hidden";?>">
                        <div class="checkbox ">
                          <input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[3]); ?>',this,event);" <?php echo $is_checked_under_control[3]; ?> name="chk_under_control[]" id="chk_arthritis_under_control" value="3" />
                          <label for="chk_arthritis_under_control">UC</label>
                        </div>
                      </div>    
                    </div>
              	</td>
                <td>
                	<?php $strArthritisTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_arthrities"],"pat",$health->delimiter);?>
                  <input id="txtArthrities" name="txtArthrities" onKeyUp="chk_change('<?php echo addslashes($strArthritisTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strArthritisTxtPat));?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Patient Lung Problem -->
              <tr>
                <td>Lung&nbsp;Problems</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acya_p1[4]; ?> id="selfLung"  name="any_conditions_u1[]" value="4" onClick="chk_change('<?php echo addslashes($acya_p1[4]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfLungN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatLungProb'),document.getElementById('chk_lung_problem_under_control'),'<?php echo $is_checked_under_control[4]; ?>');">
                  	<label for="selfLung"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[4]; ?> id="selfLungN"  name="any_conditions_u1_n[]" value="4" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[4]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfLung'), document.getElementById('tdPatLungProb'), document.getElementById('chk_lung_problem_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfLungN"></label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatLungProb" class="<?php echo ($acya_p1[4] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[4]); ?>',this,event);" <?php echo $is_checked_under_control[4]; ?> name="chk_under_control[]" id="chk_lung_problem_under_control" value="4" />
                        <label for="chk_lung_problem_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strLungProblemTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_lung_problem"],"pat",$health->delimiter);?>
                  <input id="txtLungProblem" name="txtLungProblem" onKeyUp="chk_change('<?php echo addslashes($strLungProblemTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strLungProblemTxtPat)); ?>" type="text" class="form-control" />
                </td>
              </tr>
              
              <!-- Patient Stroke -->
              <tr>
                <td>Stroke</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acya_p1[5]; ?> id="selfStroke" name="any_conditions_u1[]" value="5" onClick="chk_change('<?php echo addslashes($acya_p1[5]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfStrokeN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatStroke'),document.getElementById('chk_stroke_under_control'),'<?php echo $is_checked_under_control[5]; ?>');">
                  	<label for="selfStroke"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[5]; ?> id="selfStrokeN" name="any_conditions_u1_n[]" value="5" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[5]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfStroke'), document.getElementById('tdPatStroke'), document.getElementById('chk_stroke_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfStrokeN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatStroke" class="<?php echo ($acya_p1[5] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[5]); ?>',this,event);" <?php echo $is_checked_under_control[5]; ?> name="chk_under_control[]" id="chk_stroke_under_control" value="5" />
                        <label for="chk_stroke_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strStrokeTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_stroke"],"pat",$health->delimiter);?>
                  <input id="txtStroke" name="txtStroke" onKeyUp="chk_change('<?php echo addslashes($strStrokeTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strStrokeTxtPat)); ?>" type="text" class="form-control" />
                </td>
              </tr>
              
              <!-- Patient Thyroid Problems -->
              <tr>
                <td>Thyroid&nbsp;Problems</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php print $acya_p1[6]; ?> id="selfThyroid" name="any_conditions_u1[]" value="6" onClick="chk_change('<?php echo addslashes($acya_p1[6]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfThyroidN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatThyroidProb'),document.getElementById('chk_thyroid_problem_under_control'),'<?php echo $is_checked_under_control[6]; ?>');">
                  	<label for="selfThyroid"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[6]; ?> id="selfThyroidN" name="any_conditions_u1_n[]" value="6" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[6]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfThyroid'), document.getElementById('tdPatThyroidProb'), document.getElementById('chk_thyroid_problem_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfThyroidN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatThyroidProb" class="<?php echo ($acya_p1[6] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[6]); ?>',this,event);" <?php echo $is_checked_under_control[6]; ?>  name="chk_under_control[]" id="chk_thyroid_problem_under_control" value="6" />
                        <label for="chk_thyroid_problem_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strThyroidProblemsTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_thyroid_problems"],"pat",$health->delimiter);?>
                  <input id="txtThyroidProblems" name="txtThyroidProblems" onKeyUp="chk_change('<?php echo addslashes($strThyroidProblemsTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strThyroidProblemsTxtPat)); ?>" type="text" class="form-control" />
                </td>
              </tr>
              
              <!-- Patient Diabetes -->
              <tr>
                <td>Diabetes</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acya_p1[3]; ?> id="elem_diab_u" name="any_conditions_u1[]" value="3" onClick="chk_change('<?php echo addslashes($acya_p1[3]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('elem_diab_uN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatDiabetes'),document.getElementById('chk_diabetes_under_control'),'<?php echo $is_checked_under_control[7]; ?>');">
                  	<label for="elem_diab_u"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox" >
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[3]; ?> id="elem_diab_uN"  name="any_conditions_u1_n[]" value="3" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[3]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('elem_diab_u'), document.getElementById('tdPatDiabetes'), document.getElementById('chk_diabetes_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="elem_diab_uN">&nbsp;</label>
               		</div>
                </td>
                <td>
                	<?php
										$strDiabetesIdTxtPat =  get_set_pat_rel_values_retrive($gen_medicine["diabetes_values"],'pat',$health->delimiter); 
										$diabetes_values = array();
										$diabetes_values["DM Type 1"] = array("DM Type 1","","DM Type 1");
										$diabetes_values["DM Type 2"] = array("DM Type 2","","DM Type 2");
										$diabetes_values["Diet"] = array("Diet","","Diet");
										$diabetes_values["NIDDM"] = array("NIDDM","","NIDDM");
										$diabetes_values["IDDM"] = array("IDDM","","IDDM");
										$diabetes_values["Other"] = array("Other","","");
									?>
                  <div class="col-xs-8">
                  	<div class="select-container" >
                      <select class="selectpicker" data-width="100%" data-style="btn-warning" name="text_diabetes_id[]" id="text_diabetes_id" title="Select" multiple>
                      <?php	
                        echo $health->get_combo_multi($strDiabetesIdTxtPat,$diabetes_values);
                      ?>
                      </select>
                    </div>
                 	</div>   
                	<div class="pull-right">
                  	<div id="tdPatDiabetes" class="<?php echo ($acya_p1[3] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[7]); ?>',this,event);" <?php echo $is_checked_under_control[7]; ?> name="chk_under_control[]" id="chk_diabetes_under_control" value="7" />
                        <label for="chk_diabetes_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strDiabetesTxtPat = get_set_pat_rel_values_retrive($elem_desc_u,"pat",$health->delimiter);?>
                  <input type='text' name='elem_desc_u' onKeyUp="chk_change('<?php echo addslashes($strDiabetesTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strDiabetesTxtPat));?>" class="form-control">
                </td>
              </tr>

  						<!-- Patient LDL -->
              <tr>
                <td>LDL</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acya_p1[13]; ?> id="selfLDL" name="any_conditions_u1[]" value="13" onClick="chk_change('<?php echo addslashes($acya_p1[13]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfLDLN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatLDL'),document.getElementById('chk_LDL_under_control'),'<?php echo $is_checked_under_control[8]; ?>');">
                  	<label for="selfLDL"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[13]; ?> id="selfLDLN" name="any_conditions_u1_n[]" value="13" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[13]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfLDL'), document.getElementById('tdPatLDL'), document.getElementById('chk_LDL_under_control'), '', document.getElementById('cbkMasterPtConN')); ">
                 		<label for="selfLDLN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatLDL" class="<?php echo ($acya_p1[13] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[8]); ?>',this,event);" <?php echo $is_checked_under_control[8]; ?> name="chk_under_control[]" id="chk_LDL_under_control" value="8">
                        <label for="chk_LDL_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strLDLTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_LDL"],"pat",$health->delimiter);?>
                  <input  id="txtLDL" name="txtLDL" onKeyUp="chk_change('<?php echo addslashes($strLDLTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strLDLTxtPat)); ?>" class="form-control" type="text" />
                </td>
              </tr>
              
							<!-- Patient Ulcers -->
              <tr>
                <td>Ulcers</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php print $acya_p1[8]; ?> id="selfUlcers" name="any_conditions_u1[]" value="8" onClick="chk_change('<?php echo addslashes($acya_p1[8]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfUlcersN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatUlcers'),document.getElementById('chk_Ulcers_under_control'),'<?php echo $is_checked_under_control[9]; ?>');" >
                  	<label for="selfUlcers"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[8]; ?> id="selfUlcersN" name="any_conditions_u1_n[]" value="8" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[8]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfUlcers'), document.getElementById('tdPatUlcers'), document.getElementById('chk_Ulcers_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfUlcersN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatUlcers" class="<?php echo ($acya_p1[8] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[9]); ?>',this,event);" <?php echo $is_checked_under_control[9]; ?> name="chk_under_control[]" id="chk_Ulcers_under_control" value="9" />
                        <label for="chk_Ulcers_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strUlcersTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_ulcers"],"pat",$health->delimiter);?>
                  <input id="txtUlcers" name="txtUlcers" onKeyUp="chk_change('<?php echo addslashes($strUlcersTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strUlcersTxtPat)); ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Patient Cancer -->
              <tr>
                <td>Cancer</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" <?php print $acya_p1[14]; ?> id="selfCancer" name="any_conditions_u1[]" value="14" onClick="chk_change('<?php echo addslashes($acya_p1[14]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfCancerN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatCancer'),document.getElementById('chk_Cancer_under_control'),'<?php echo $is_checked_under_control[11]; ?>');">
                  	<label for="selfCancer"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $arrAnyConditionsYouN[14]; ?> id="selfCancerN" name="any_conditions_u1_n[]" value="14" onClick="chk_change('<?php echo addslashes($arrAnyConditionsYouN[14]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('selfCancer'), document.getElementById('tdPatCancer'), document.getElementById('chk_Cancer_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="selfCancerN">&nbsp;</label>
               		</div>
                </td>
                <td >
                	<div class="pull-right">
                  	<div id="tdPatCancer" class="<?php echo ($acya_p1[14] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[11]); ?>',this,event);" <?php echo $is_checked_under_control[11]; ?> name="chk_under_control[]" id="chk_Cancer_under_control" value="11" >
                        <label for="chk_Cancer_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                </td>
                <td>
                	<?php $strCancerTxtPat = get_set_pat_rel_values_retrive($gen_medicine["desc_cancer"],"pat",$health->delimiter);?>
                  <input id="txtCancer" name="txtCancer" onKeyUp="chk_change('<?php echo addslashes($strCancerTxtPat); ?>',this,event);" value="<?php echo stripslashes(html_entity_decode($strCancerTxtPat)); ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Patient Others -->
              <tr>
                <td style="vertical-align: top!important;">Others</td>
                <td class="text-center" style="vertical-align: top!important;">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo $acob[1]; ?> id="ghSelfOthers" name='any_conditions_others_both[]' value="1" onClick="chk_change('<?php echo addslashes($acob[1]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('ghSelfOthersN'), '', '', document.getElementById('cbkMasterPtConN')); TextBox_hide_by_checkbox(this,document.getElementById('tdPatOthers'),document.getElementById('chk_other_under_control'),'<?php echo $is_checked_under_control[10]; ?>');">
                  	<label for="ghSelfOthers"></label>
               		</div>
                </td>
                <td style="vertical-align: top!important;">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo ($gen_medicine["any_conditions_others_n"] == 1) ? "checked" : ""; ?> id="ghSelfOthersN" name='any_conditions_others_n' value="1" onClick="chk_change('<?php echo ($gen_medicine["any_conditions_others_n"] == 1) ? "checked" : ""; ?>',this,event); unChkCorespondingCbk(this, document.getElementById('ghSelfOthers'), document.getElementById('tdPatOthers'), document.getElementById('chk_other_under_control'), '', document.getElementById('cbkMasterPtConN'));">
                 		<label for="ghSelfOthersN">&nbsp;</label>
               		</div>
                </td>
                <td colspan="2" >
                	
                	<div class="col-xs-2 pd0" style="vertical-align: middle;">
                  	<div id="tdPatOthers" class="<?php echo ($acob[1] == "checked") ? "show" : "hidden";?>">
                    	<div class="checkbox ">
                      	<input type="checkbox" onClick="chk_change('<?php echo addslashes($is_checked_under_control[10]); ?>',this,event);" <?php echo $is_checked_under_control[10]; ?> name="chk_under_control[]" id="chk_other_under_control" value="10" />
                        <label for="chk_other_under_control">UC</label>
                    	</div>
                  	</div>    
                 	</div>
                 	<div class="col-xs-10 pd0">
                	<?php $strOthersTxtPat = get_set_pat_rel_values_retrive($gen_medicine["any_conditions_others"],"pat",$health->delimiter);?>
									<textarea id="txtOthers" onKeyUp="chk_change('<?php echo addslashes($strOthersTxtPat); ?>',this,event);" name="any_conditions_others1" class="form-control" style="height:108px!important;"><?php echo stripslashes(html_entity_decode($strOthersTxtPat)); ?></textarea>
									</div>
									
               	</td>
              </tr>
              
						</table>
       		</div>
				</div>
     	</div>
      
      <?php
				//any conditions relative
				$any_conditions_ralative1_arr = explode(",",$gen_medicine["any_conditions_relative"]);
				foreach($any_conditions_ralative1_arr as $key => $val){	
					if($val != ""){
						$acra_p1[$val] = "checked";
					}
				}
				
				//any conditions others
				/*strip commas*/
				$OtherChkVal = '';
				$dbOtherchkVal = $gen_medicine['any_conditions_others_both'];
				if(strlen($dbOtherchkVal)==3){
					$dbOtherchkVal = substr($dbOtherchkVal,1,1);
					if($dbOtherchkVal=='2'){
						$OtherChkVal=' checked';
					}
				}
				else if(strlen($dbOtherchkVal)==5)
				{
					$dbOtherchkVal = substr($dbOtherchkVal,1,3);
					list($ptOther,$relOther) = explode(',',$dbOtherchkVal);
					if($relOther=='2'){
						$OtherChkVal=' checked';
					}
				}
				else{
					$OtherChkVal = '';
				}
				$acob[2] = $OtherChkVal;
				if($OtherChkVal == ' checked'){
					$anyConditionsOthersBothArrFamilyHx = ",2,";
					$anyConditionsOthersBothArrRel = ",2,";
				}
				//Sub Conditions
				$elem_subCondition_rel_val = get_set_pat_rel_values_retrive($gen_medicine["sub_conditions_you"],'rel',$health->delimiter);
				$rel_elem_subCondition_u1 = explode(",",$elem_subCondition_rel_val);
			?>
                                
			<div class="col-lg-6 col-md-12 col-sm-12">
      	<div class="medcondbox">
        	<h3>Conditions your family/blood relative have presently or have had in the past</h3>
      		<div class="clearfix"></div>
        	<div class="" >
          	<table class="table table-striped table-hover">
            	<?php 
              		$strCbkMasterFamCon = "";
									if($gen_medicine["cbk_master_fam_con"] == "no"){
										$strCbkMasterFamCon = "checked";
									}
							?>
              <tr class="hghtd">
              	<td width="25%" style="max-width:25%;" >No Known Family medical condition</td>
                <td width="6%" class="col-xs-1 ysrado text-center"><label>Yes</label></td>
                <td width="6%" class="norado">
                	<div class="checkbox">
                    <input type='checkbox' <?php echo $strCbkMasterFamCon; ?> id="cbkMasterFamConN" name='cbkMasterFamCon' value="no" onClick="chk_change('<?php echo addslashes($strCbkMasterFamConN); ?>',this,event); chkAll('no', this,'family');">
                    <label for="cbkMasterFamConN">No</label>
                  </div>
               	</td>
                <td width="16%">&nbsp;</td>
                <td width="18%">&nbsp;</td>
                <td width="29%">&nbsp;</td>

            	</tr>
              
              <!-- Family High Blood Pressure -->
              <tr>
                <td>High&nbsp;Blood&nbsp;Pressure</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relHighBp" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[1]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relHighBpN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[1]; ?>  value="1" />
                  	<label for="relHighBp"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relHighBpN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[1]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relHighBp'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[1]; ?> value="1" />
                 		<label for="relHighBpN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescHighBp" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescHighBp[]" id="relDescHighBp" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescHighBp'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescHighBp" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescHighBp" id="rel_other_relDescHighBp" value="<?php echo $other_relDescHighBp;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescHighBp"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strHighBloodPresherTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_high_bp"],"rel",$health->delimiter);?>
                  <input id="relTxtHighBloodPresher" name="relTxtHighBloodPresher" onKeyUp="chk_change('<?php echo addslashes($strHighBloodPresherTxtRel); ?>',this,event);" value="<?php echo $strHighBloodPresherTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Heart Problem -->
              <tr>
                <td>Heart&nbsp;Problem</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relHeart" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[2]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relHeartN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[2]; ?>  value="2" />
                  	<label for="relHeart"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relHeartN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[2]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relHeart'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[2]; ?>  value="2" />
                 		<label for="relHeartN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescHeartProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescHeartProb[]" id="relDescHeartProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescHeartProb'],$health->pat_relation);
                          ?>
                      </select>
                  	</div>
                 	</div>
                  <div id="other_relDescHeartProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescHeartProb" id="rel_other_relDescHeartProb" value="<?php echo $other_relDescHeartProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescHeartProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strHeartProblemTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_heart_problem"],"rel",$health->delimiter);?>
                  <input  id="relTxtHeartProblem" name="relTxtHeartProblem" onKeyUp="chk_change('<?php echo addslashes($strHeartProblemTxtRel); ?>',this,event);" value="<?php echo $strHeartProblemTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Arthritis -->
              <tr>
                <td>Arthritis</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relArthritis" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[7]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relArthritisN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[7]; ?> value="7" />
                  	<label for="relArthritis"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relArthritisN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[7]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relArthritis'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[7]; ?> value="7" >
                 		<label for="relArthritisN">&nbsp;</label>
               		</div>
                </td>
                <td>
                	<div class="row">
                    <div class="col-xs-6">
                      <div class="checkbox">
                        <input type='checkbox' name='rel_elem_subCondition_u1[]' value="7.1" id="rel_elem_subCondition_u1_71" onClick="chk_change('<?php echo addslashes(in_array("7.1",$rel_elem_subCondition_u1) ? "checked" : ""); ?>',this,event);" <?php echo in_array("7.1",$rel_elem_subCondition_u1) ? "checked" : "" ; ?>>
                        <label for="rel_elem_subCondition_u1_71">RA</label>
                      </div>
                    </div>
                  	<div class="col-xs-6">
                      <div class="checkbox">
                        <input type='checkbox' name='rel_elem_subCondition_u1[]' value="7.2" id="rel_elem_subCondition_u1_72" onClick="chk_change('<?php echo addslashes(in_array("7.2",$rel_elem_subCondition_u1) ? "checked" : ""); ?>',this,event);" <?php echo in_array("7.2",$rel_elem_subCondition_u1) ? "checked" : "" ; ?>>
                        <label for="rel_elem_subCondition_u1_72">OA</label>
                      </div>
                    </div>
                  </div>
                </td>
                <td >
                	<div id="div_relDescArthritisProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescArthritisProb[]" id="relDescArthritisProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescArthritisProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescArthritisProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescArthritisProb" id="rel_other_relDescArthritisProb" value="<?php echo $other_relDescArthritisProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescArthritisProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strArthritiesTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_arthrities"],"rel",$health->delimiter);?>
                  <input id="relTxtArthrities" name="relTxtArthrities" onKeyUp="chk_change('<?php echo addslashes($strArthritiesTxtRel); ?>',this,event);" value="<?php echo $strArthritiesTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
          	 	
              <!-- Family Lung Problems -->
              <tr>
                <td>Lung&nbsp;Problems</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relLung" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[4]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relLungN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[4]; ?> value="4" />
                  	<label for="relLung"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relLungN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[4]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relLung'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[4]; ?> value="4" />
                 		<label for="relLungN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescLungProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescLungProb[]" id="relDescLungProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescLungProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescLungProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescLungProb" id="rel_other_relDescLungProb" value="<?php echo $other_relDescLungProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescLungProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strLungProblemTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_lung_problem"],"rel",$health->delimiter);?>
                  <input id="relTxtLungProblem" name="relTxtLungProblem" onKeyUp="chk_change('<?php echo addslashes($strLungProblemTxtRel); ?>',this,event);" value="<?php echo $strLungProblemTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Stroke -->
              <tr>
                <td>Stroke</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relStroke" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[5]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relStrokeN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[5]; ?> value="5" />
                  	<label for="relStroke"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relStrokeN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[5]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relStroke'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[5]; ?> value="5" />
                 		<label for="relStrokeN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescStrokeProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescStrokeProb[]" id="relDescStrokeProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescStrokeProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescStrokeProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescStrokeProb" id="rel_other_relDescStrokeProb" value="<?php echo $other_relDescStrokeProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescStrokeProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strStrokeTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_stroke"],"rel",$health->delimiter);?>
                  <input id="relTxtStroke" name="relTxtStroke" onKeyUp="chk_change('<?php echo addslashes($strStrokeTxtRel); ?>',this,event);" value="<?php echo $strStrokeTxtRel; ?>" class="form-control" type="text" >
                </td>
              </tr>
                
             	<!-- Family Thyroid Problems -->
              <tr>
                <td>Thyroid&nbsp;Problems</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relThyroid" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[6]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relThyroidN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[6]; ?> value="6" />
                  	<label for="relThyroid"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relThyroidN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[6]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relThyroid'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[6]; ?> value="6" />
                 		<label for="relThyroidN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescThyroidProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescThyroidProb[]" id="relDescThyroidProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescThyroidProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescThyroidProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescThyroidProb" id="rel_other_relDescThyroidProb" value="<?php echo $other_relDescThyroidProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescThyroidProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strThyroidProblemsTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_thyroid_problems"],"rel",$health->delimiter);?>
                  <input id="relTxtThyroidProblems" name="relTxtThyroidProblems" onKeyUp="chk_change('<?php echo addslashes($strThyroidProblemsTxtRel); ?>',this,event);" value="<?php echo $strThyroidProblemsTxtRel; ?>"  class="form-control" type="text" />
                </td>
              </tr>
              
             	<!-- Family Diabetes -->
              <tr>
                <td>Diabetes</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="elem_diab_r" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[3]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('elem_diab_rN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[3]; ?> value="3" />
                  	<label for="elem_diab_r"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="elem_diab_rN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[3]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('elem_diab_r'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[3]; ?>  value="3" />
                 		<label for="elem_diab_rN">&nbsp;</label>
               		</div>
                </td>
                <td>
                	 <?php
									 	$strDiabetesIdTxtRel = addslashes(get_set_pat_rel_values_retrive($gen_medicine["diabetes_values"],'rel',$health->delimiter));
										$diabetes_values = array(	"DM Type 1" => array("DM Type 1","","DM Type 1"),
																							"DM Type 2" => array("DM Type 2","","DM Type 2"),
																							"Diet" => array("Diet","","Diet"),
																							"NIDDM" => array("NIDDM","","NIDDM"),
																							"IDDM" => array("IDDM","","IDDM"),
																							"Other" => array("Other","",""));
									?>
                  <div class="select-container" >
                    <select class="selectpicker" name="rel_text_diabetes_id[]" id="rel_text_diabetes_id" data-style="btn-warning" multiple data-width="100%" title="Select">
                    <?php
                      echo $health->get_combo_multi($strDiabetesIdTxtRel,$diabetes_values);
                    ?>
                    </select>
                  </div>
               	</td>
                <td >
                	<div id="div_elem_desc_r" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="elem_desc_r[]" id="elem_desc_r" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['desc_r'],$health->pat_relation);
                          ?>
                      </select>
                  	</div>  
                 	</div>
                  <div id="other_elem_desc_r" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_elem_desc_r" id="rel_other_elem_desc_r" value="<?php echo $other_elem_desc_r;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="elem_desc_r"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strDiabetesTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_u"],"rel",$health->delimiter);?>
                  <input type='text' name='rel_elem_desc_u' onKeyUp="chk_change('<?php echo addslashes($strDiabetesTxtRel); ?>',this,event);" value="<?php echo $strDiabetesTxtRel;?>" size="20" class="form-control" />
                </td>
              </tr>
              
              <!-- Family LDL -->
              <tr>
                <td>LDL</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relLDL" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[13]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relLDLN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[13]; ?> value="13" />
                  	<label for="relLDL"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relLDLN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[13]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relLDL'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[13]; ?> value="13" />
                 		<label for="relLDLN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescLDL" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescLDL[]" id="relDescLDL" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescLDL'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescLDL" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescLDL" id="rel_other_relDescLDL" value="<?php echo $other_relDescLDL;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescLDL"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strLDLTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_LDL"],"rel",$health->delimiter);?>
                  <input id="reltxtLDL" name="reltxtLDL" onKeyUp="chk_change('<?php echo addslashes($strLDLTxtRel); ?>',this,event);" value="<?php echo $strLDLTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Ulcers -->
              <tr>
                <td>Ulcers</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type='checkbox' id="relUlcers" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[8]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relUlcersN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[8]; ?> value="8" />
                  	<label for="relUlcers"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type='checkbox' id="relUlcersN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[8]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relUlcers'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[8]; ?> value="8" />
                 		<label for="relUlcersN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescUlcersProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescUlcersProb[]" id="relDescUlcersProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescUlcersProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescUlcersProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescUlcersProb" id="rel_other_relDescUlcersProb" value="<?php echo $other_relDescUlcersProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescUlcersProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strUlcersTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_ulcers"],"rel",$health->delimiter);?>
                  <input id="relTxtUlcers" name="relTxtUlcers" onKeyUp="chk_change('<?php echo addslashes($strUlcersTxtRel); ?>',this,event);" value="<?php echo $strUlcersTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Cancer -->
              <tr>
                <td>Cancer</td>
                <td class="text-center">
                	<div class="checkbox">
                  	<input type="checkbox" id="relCancer" name='any_conditions_relative1[]' onClick="chk_change('<?php echo addslashes($acra_p1[14]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relCancerN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php print $acra_p1[14]; ?> value="14" />
                  	<label for="relCancer"></label>
               		</div>
                </td>
                <td>
                	<div class="checkbox">
                  	<input type="checkbox" id="relCancerN" name='any_conditions_relative1_n[]' onClick="chk_change('<?php echo addslashes($arrAnyConditionsRelativeN[14]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('relCancer'), '', '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $arrAnyConditionsRelativeN[14]; ?> value="14" />
                 		<label for="relCancerN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td >
                	<div id="div_relDescCancerProb" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="relDescCancerProb[]" id="relDescCancerProb" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['relDescCancerProb'],$health->pat_relation);
                          ?>
                      </select>
                    </div>
                 	</div>
                  <div id="other_relDescCancerProb" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_relDescCancerProb" id="rel_other_relDescCancerProb" value="<?php echo $other_relDescCancerProb;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="relDescCancerProb"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strCancerTxtRel = get_set_pat_rel_values_retrive($gen_medicine["desc_cancer"],"rel",$health->delimiter);?>
                  <input id="relTxtCancer" name="relTxtCancer" onKeyUp="chk_change('<?php echo addslashes($strCancerTxtRel); ?>',this,event);" value="<?php echo $strCancerTxtRel; ?>" class="form-control" type="text" />
                </td>
              </tr>
              
              <!-- Family Others -->
              <tr>
                <td style="vertical-align: top!important;">Others</td>
                <td class="text-center" style="vertical-align: top!important;">
                	<div class="checkbox">
                  	<input type="checkbox" id="ghRelOthers" name='any_conditions_others_rel' onClick="chk_change('<?php echo addslashes($acob[2]); ?>',this,event); unChkCorespondingCbk(this, document.getElementById('ghRelOthersN'), '', '', document.getElementById('cbkMasterFamConN'));" <?php echo $acob[2]; ?> value="2" />
                  	<label for="ghRelOthers"></label>
               		</div>
                </td>
                <td style="vertical-align: top!important;">
                	<div class="checkbox">
                  	<input type="checkbox" <?php echo ($gen_medicine["any_conditions_others_rel_n"] == 1) ? "checked" : ""; ?> id="ghRelOthersN" name='any_conditions_others_rel_n' onClick="chk_change('<?php echo ($gen_medicine["any_conditions_others_rel_n"] == 1) ? "checked" : ""; ?>',this,event); unChkCorespondingCbk(this, document.getElementById('ghRelOthers'), '', '', '', document.getElementById('cbkMasterFamConN'));" value="1" />
                 		<label for="ghRelOthersN">&nbsp;</label>
               		</div>
                </td>
                <td>&nbsp;</td>
                <td style="vertical-align: top!important;" >
                	<div id="div_ghRelDescOthers" >
                  	<div class="select-container" >
                      <select class="selectpicker selectpicker_new" data-width="100%" name="ghRelDescOthers[]" id="ghRelDescOthers" title="Select" multiple 	>
                          <?php
                            echo $health->get_combo_multi($gen_medicine['ghRelDescOthers'],$health->pat_relation);
                          ?>
                      </select>
                      </div>
                 	</div>
                  <div id="other_ghRelDescOthers" class="hidden">
                  	<div class="input-group">
                    	<input type="text" name="rel_other_ghRelDescOthers" id="rel_other_ghRelDescOthers" value="<?php echo $other_ghRelDescOthers;?>" class="form-control">
                      <label class="input-group-addon btn back_other" data-tab-name="ghRelDescOthers"><i class="glyphicon glyphicon-arrow-left"></i></label>
                  	</div>
                	</div>
                </td>
                <td>
                	<?php $strOthersTxtRel = get_set_pat_rel_values_retrive($gen_medicine["any_conditions_others"],"rel",$health->delimiter);?>
                  <textarea id="relTxtOthers" name="rel_any_conditions_others1" class="form-control" style="height:108px!important;" onKeyUp="chk_change('<?php echo addslashes($strOthersTxtRel); ?>',this,event);" ><?php echo $strOthersTxtRel;?></textarea>
                </td>
              </tr>
           	
            </table>
       		</div>
				</div>
     	</div>
      
		</div>
    
    <div class="clearfix"></div>
    
    <div class="row">
    	<!-- Annual colorectal cancer screenings --> 
    	<div class="col-lg-3 col-md-6 col-sm-6">
      	<div class="checkbox">
        	<input type="checkbox" name="chk_annual_colorectal_cancer_screenings" id="chk_annual_colorectal_cancer_screenings" value="1" onClick="chk_change('<?php echo addslashes($gen_medicine["chk_annual_colorectal_cancer_screenings"]==1?"checked":""); ?>',this,event); " <?php echo $gen_medicine["chk_annual_colorectal_cancer_screenings"]==1?"checked":"";?> />
          <label for="chk_annual_colorectal_cancer_screenings">Annual colorectal cancer screenings</label>
      	</div>
    	</div>
      
      <!-- Receiving annual mammogram -->
      <div class="col-lg-3 col-md-6 col-sm-6 ">
      	<div class="checkbox">
        	<input type="checkbox" onClick="chk_change('<?php echo addslashes($gen_medicine["chk_receiving_annual_mammogram"]==1?"checked":""); ?>',this,event);" <?php echo $gen_medicine["chk_receiving_annual_mammogram"]==1?"checked":""; ?> name="chk_receiving_annual_mammogram" id="chk_receiving_annual_mammogram" value="1">
          <label for="chk_receiving_annual_mammogram">Receiving annual mammogram</label>
      	</div>
    	</div>
      
      <!-- Counseling for Nutrition/Diet -->
      <?php 
				$nutrition_counseling_date = get_date_format($gen_medicine["nutrition_counseling_date"]);
				if(get_number($nutrition_counseling_date) == "00000000"){
					$nutrition_counseling_date = '';
				}
			?>
      <div class="col-lg-6 col-md-6 col-sm-6 ">
      	<div class="row">
        	<div class="col-sm-6">
          	<div class="checkbox">
            	<input type="checkbox" name="con_for_nut" id="con_for_nut" value="1" onClick="chk_change('<?php if($gen_medicine["nutrition_counseling"] == 1) echo "checked"; ?>',this,event);" <?php if($gen_medicine["nutrition_counseling"] == 1) echo "checked"; ?> >
             	<label for="con_for_nut">Counseling for Nutrition/Diet</label>
           	</div>   
        	</div>
          <div class="col-sm-6">
          	<div class="input-group">
            	<input type="text" name="con_for_nut_date" id="con_for_nut_date" onKeyUp="chk_change('<?php echo addslashes($nutrition_counseling_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($nutrition_counseling_date); ?>',this,event);" value="<?php echo $nutrition_counseling_date; ?>" onBlur="checkdate(this);" class="datepicker form-control" onClick="getDate_and_setToField(this)"/>
              <label class="input-group-addon btn" for="con_for_nut_date"><i class="glyphicon glyphicon-calendar"></i></label>
            </div>
          </div>    
      	</div>
    	</div>
      
      <div class="clearfix mb5"></div>
      
      <!-- Received flu vaccine -->
      <div class="col-lg-3 col-md-6 col-sm-6 ">
        <div class="row">
          <div class="col-lg-7 col-md-7 col-sm-7">
            <div class="checkbox">
              <input type="checkbox" onClick="chk_change('<?php echo $gen_medicine["chk_received_flu_vaccine"]==1?"checked":""; ?>',this,event);" <?php echo $gen_medicine["chk_received_flu_vaccine"]==1?"checked":""; ?> name="chk_received_flu_vaccine" id="chk_received_flu_vaccine" value="1">
              <label for="chk_received_flu_vaccine">Received flu vaccine</label>
            </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5">
            <select name="received_flu_vaccine_type" id="received_flu_vaccine_type" onChange="top.fmain.chk_change('',this,event);" class="selectpicker " data-width="100%" data-title="Please Select">
              <?php 
                $vacc_opt_str = '';
                foreach($gen_medicine['vaccine_flu_arr'] as $key => $val){
                  $checked = html_entity_decode($gen_medicine["received_flu_vaccine_type"]) == $val ? "selected" : "";
                  $vacc_opt_str .= '<option value="'.$val.'" '.$checked.'>'.$val.'</option>';
                }
                echo $vacc_opt_str;
              ?>
            </select>
          </div>	
        </div>
    	</div>
      
      <!-- High-risk for cardiac events on aspirin prophylaxis -->
      <div class="col-lg-3 col-md-6 col-sm-6  ">
      	<div class="checkbox">
        	<input type="checkbox" onClick="chk_change('<?php echo addslashes($gen_medicine["chk_high_risk_for_cardiac"]==1?"checked":""); ?>',this,event);" <?php echo $gen_medicine["chk_high_risk_for_cardiac"]==1?"checked":""; ?> name="chk_high_risk_for_cardiac" id="chk_high_risk_for_cardiac" value="1">
          <label for="chk_high_risk_for_cardiac">High-risk for cardiac events on aspirin prophylaxis</label>
      	</div>
    	</div>
      
      <!-- Counseling for Physical Activity -->
      <?php 
				$physical_activity_counseling_date = get_date_format($gen_medicine["physical_activity_counseling_date"]);
				if(get_number($physical_activity_counseling_date) == "00000000"){
					$physical_activity_counseling_date = '';
				}
			?>
      <div class="col-lg-6 col-md-6 col-sm-6 ">
      	<div class="row">
        	<div class="col-sm-6">
          	<div class="checkbox">
            	<input type="checkbox" name="con_for_phy" id="con_for_phy" value="1" onClick="chk_change('<?php if($gen_medicine["physical_activity_counseling"] == 1) echo "checked"; ?>',this,event);" <?php if($gen_medicine["physical_activity_counseling"] == 1) print "checked"; ?>>
             	<label for="con_for_phy">Counseling for Physical Activity</label>
           	</div>   
        	</div>
          <div class="col-sm-6">
          	<div class="input-group">
            	<input type="text" name="con_for_phy_date" id="con_for_phy_date" onKeyUp="chk_change('<?php echo addslashes($physical_activity_counseling_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($physical_activity_counseling_date); ?>',this,event);" value="<?php echo $physical_activity_counseling_date; ?>" onBlur="checkdate(this);" class="datepicker form-control" onClick="getDate_and_setToField(this)"/>
              <label class="input-group-addon btn" for="con_for_phy_date"><i class="glyphicon glyphicon-calendar"></i></label>
            </div>
          </div>    
      	</div>
    	</div>
      
      <div class="clearfix mb5"></div>
      
      <!-- Received Pneumococcal Vaccine -->
      <div class="col-lg-3 col-md-6 col-sm-6 ">
        <div class="row">
          <div class="col-lg-7 col-md-7 col-sm-7">
            <div class="checkbox">
              <input type="checkbox" onClick="chk_change('<?php echo $gen_medicine["chk_received_pneumococcal_vaccine"]==1?"checked":""; ?>',this,event);" <?php echo $gen_medicine["chk_received_pneumococcal_vaccine"]==1?"checked":""; ?> name="chk_received_pneumococcal_vaccine" id="chk_received_pneumococcal_vaccine" value="1">
              <label for="chk_received_pneumococcal_vaccine" >Received Pneumococcal&nbsp;Vaccine</label>
            </div>
          </div>	
          <div class="col-lg-5 col-md-5 col-sm-5">
            <select name="pneumococcal_vaccine_type" id="pneumococcal_vaccine_type" onChange="top.fmain.chk_change('',this,event);" class="selectpicker " data-width="100%" data-title="Please Select">
              <?php 
                $pneumococcal_vac_opt_str = '';
                foreach($gen_medicine['pneu_vac_arr'] as $key => $val){
                  $checked = html_entity_decode($gen_medicine["pneumococcal_vaccine_type"])==$val ? "selected" : "";
                  $pneumococcal_vac_opt_str .= '<option value="'.$val.'" '.$checked.'>'.$val.'</option>';
                }
                echo $pneumococcal_vac_opt_str;
              ?>	
            </select>
          </div>	
        </div>
        
      </div>
      
      
      <!-- Falls: Risk Assessment -->
      <div class="col-lg-3 col-md-6 col-sm-6 ">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="checkbox">
              <input type="checkbox" onClick="chk_change('<?php echo addslashes($gen_medicine["chk_fall_risk_assd"]==1?"checked":""); ?>',this,event);" <?php echo $gen_medicine["chk_fall_risk_assd"]==1?"checked":""; ?> name="chk_fall_risk_assd" id="chk_fall_risk_assd" value="1">
              <label for="chk_fall_risk_assd">Falls: Risk Assessment</label>
            </div>		
          </div>	
          <div class="col-lg-6 col-md-6 col-sm-6">
            <select name="fall_risk_ass_type" id="fall_risk_ass_type" onChange="top.fmain.chk_change('',this,event);" class="selectpicker" data-width="100%" data-title="Please Select">
              <?php 
                $falls_risk_asses_str = '';
                foreach($gen_medicine['fall_risk_assess_array'] as $key => $val){
                  $checked = html_entity_decode($gen_medicine["fall_risk_ass_type"]) == $val ? "selected" : "";
                  $falls_risk_asses_str .= '<option value="'.$val.'" '.$checked.'>'.$val.'</opiton>';
                }
                echo $falls_risk_asses_str;
              ?>
            </select>
          </div>	
        </div>
	 	</div>
		
			<!-- General Health and Vital Signs combined fields -->
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="checkbox">
              <input type="checkbox" name="chk_blood_pressure" id="chk_blood_pressure" value="1" onClick="top.fmain.chk_change('<?php if($gen_medicine["chk_blood_pressure"] == 1) print "checked"; ?>',this,event);" <?php if($gen_medicine["chk_blood_pressure"] == 1) print "checked"; ?>>
              <label for="chk_blood_pressure">Blood Pressure:</label>	
            </div>
          </div>	
          <div class="col-lg-3 col-md-3 col-sm-3">
            <span style="vertical-align:sub">B/P&nbsp;-&nbsp;S/D:</span>
          </div>	
          <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="row">
              <div class="col-lg-5 col-md-5 col-sm-5">
                <input type="text" maxlength="4" class="form-control" name="bp_systolic" id="bp_systolic" onKeyUp="isNumeric(this.id)" value="<?php echo $vs_data['BP_sys']; ?>" style="">
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 text-center">
                <span style="vertical-align:sub">/</span>
              </div>
              <div class="col-lg-5 col-md-5 col-sm-5">
                <input type="text" maxlength="4" class="form-control" name="bp_dystolic" id="bp_dystolic" onKeyUp="isNumeric(this.id)" value="<?php echo $vs_data['BP_dys']; ?>" style="">
              </div>
            </div>
          </div>	
        </div>	
      </div>
      
      <div class="col-lg-3 col-md-3 col-sm-3">
        <select class="selectpicker" data-width="100%" data-title="Please Select" name="blood_pressure_type" id="blood_pressure_type" onChange="top.fmain.chk_change('',this,event);">
          <?php 
            $opt_str = '';
            foreach($vs_data['BP_array'] as $key => $val){
              $checked = html_entity_decode($gen_medicine["blood_pressure_type"])==$val ? "selected" : "";
              $opt_str .= '<option value="'.$val.'" '.$checked.'>'.$val.'</option>';
            }
            echo $opt_str;
          ?>
        </select>
      </div>
     	
      <div class="clearfix mb5"></div> 
	
      <div class="col-lg-1 col-md-1">
        <div class="checkbox">
          <input type="checkbox" name="chk_bmi" id="chk_bmi" value="1" onClick="top.fmain.chk_change('<?php if($result1["chk_bmi"] == 1) print "checked"; ?>',this,event);" <?php if($gen_medicine["chk_bmi"] == 1) print "checked"; ?>>
          <label for="chk_bmi">BMI:</label>
        </div>
      </div>
      
      <div class="col-lg-2 col-md-3">
        <label >Height:</label>
        <div class="row">
          <div class="col-lg-6 col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" name="bmi_height" id="bmi_height"  value="<?php echo $vs_data['BMI_height']; ?>" onKeyUp="isNumeric(this.id);calculateBmi(this.id);" onBlur="calculateBmi(this.id);" onChange="calculateBmi(this.id)">
              <label class="input-group-addon" <?php echo show_tooltip('Feet','top'); ?> for="bmi_height">
                <span>ft.</span>
              </label>	
            </div>
          </div>	
          <div class="col-lg-6 col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" name="bmi_height_unit" id="bmi_height_unit"  value="<?php echo $vs_data['BMI_height_unit']; ?>" onKeyUp="isNumeric(this.id);calculateBmi(this.id);" onBlur="calculateBmi(this.id);" onChange="calculateBmi(this.id)">
              <label class="input-group-addon" <?php echo show_tooltip('Inches','top'); ?> for="bmi_height_unit">
                <span>Inch</span>
              </label>	
            </div>
          </div>	
        </div>	
      </div>	
      <div class="col-lg-3 col-md-3">
        <label >Weight:</label>
        <div class="row">
          <div class="col-lg-6 col-md-6">
            <input type="text" class="form-control" name="bmi_weight" id="bmi_weight" value="<?php echo $vs_data['BMI_weight']; ?>" onKeyUp="isNumeric(this.id)" onChange="calculateBmi(this.id)" onBlur=""> 
          </div>	
          <div class="col-lg-6 col-md-6">
            <select name="bmi_weight_unit" id="bmi_weight_unit" class="selectpicker" data-width="100%" data-title="Please Select" onChange="convert_height_weight(this.id);calculateBmi(this.id);" onclick="" onfocus="">
                          <option value="lbs" <?php echo ( 'lbs' == $vs_data['BMI_weight_unit'] ? 'selected' : ''); ?>>lbs</option>
                          <option value="kg" <?php echo ( 'kg' == $vs_data['BMI_weight_unit'] ? 'selected' : ''); ?>>kg</option>  
                      </select>
          </div>	
        </div>
      </div>
      <div class="col-lg-3 col-md-2">
        <label>&nbsp;&nbsp;</label>
        <div class="input-group">
          <input type="text" class="form-control" name="bmi_result" id="bmi_result" value="<?php echo $vs_data['BMI_result']; ?>" onChange="calculateBmi(this.id)">
          <label for="bmi_result" class="input-group-addon">
            <span>kg/sqr. m</span>
          </label>
        </div>
      </div>
      <div class="col-lg-3 col-md-3">
        <label>&nbsp;&nbsp;</label>
        <select name="bmi_type" id="bmi_type" onChange="top.fmain.chk_change('',this,event);" class="selectpicker" data-width="100%" data-title="Please Select">
          <?php
            $bmi_opt_str = '';
            foreach($vs_data['BMI_array'] as $key => $val){
              $checked = html_entity_decode($gen_medicine["bmi_type"]) == $val ? "selected" : "";
              $bmi_opt_str .= '<option ="'.$val.'" '.$checked.'>'.$val.'</option>';	
            }
            echo $bmi_opt_str;
          ?>
        </select>
      </div>	
		
			<div class="clearfix mb5"></div> 
	</div>
    
    
   	<!-- Review Of Systems -->
    <div class="reviwsyst">
    <div class="row">
    	<div class="col-sm-12 ">
      	<div class="row head">
       		<span class="valign_mid">Review Of Systems</span>
          <!-- No known medical condition -->
          <div class="text-center" style="margin-top: -23px;">
          	<div class="checkbox">
              <input type='checkbox' <?php echo (($gen_medicine["cbk_master_ROS"] == 1) ? "checked" : ""); ?> id="cbkMasterROS" name='cbkMasterROS' value="1" onClick="chk_change('<?php echo ($gen_medicine["cbk_master_ROS"] == 1) ? "checked" : ""; ?>',this,event); disableAllROS(this);">
              <label for="cbkMasterROS">No known medical condition</label>
            </div>
       		</div>
        </div>
     	</div>
      
      <div class="clearfix"></div>
      
      <!-- Allergic/Immunologic -->
      <div class="col-lg-6 col-md-12 col-sm-12 ">
     <div class="revsubbox"> 	
     <div class="revsyshead">
        	<div class="head">
						<div class="row">
          		<div class="col-lg-9 col-md-7 col-sm-9">
              	<span class="valign_mid">Allergic/Immunologic</span>
             	</div>
            	<div class="col-lg-3 col-md-5 col-sm-3 content_box">
                <div class="checkbox">
                  <input type='checkbox' <?php echo $negChkBx[7]; ?> name='negChkBx[]' id="negChkBxAllerImmBloLym" value="7" onClick="chk_change('<?php echo addslashes($negChkBx[7]); ?>',this,event); disableItsFields(this.id,'chkBxSeaAller','chkBxHayFever','txtBxAllerImmBloLym');">
                  <label for="negChkBxAllerImmBloLym">Negative</label>
                </div>
           		</div>   
        		</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">  
	<div class="col-sm-6 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_aller[2]); ?>',this,event); unChkNegBox('negChkBxAllerImmBloLym', 'cbkMasterROS');" <?php echo $review_aller[2]; ?> id="chkBxHayFever"  name='review_aller[]' value="2" />
            	<label for="chkBxHayFever">Hay Fever</label>
            </div>
        	</div>
		
          <div class="col-sm-6 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_aller[1]); ?>',this,event); unChkNegBox('negChkBxAllerImmBloLym', 'cbkMasterROS'); " <?php echo $review_aller[1]; ?> id="chkBxSeaAller" name='review_aller[]' value="1" />
            	<label for="chkBxSeaAller">Seasonal Allergies</label>
            </div>
        	</div>
          
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxAllerImmBloLym" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" name="review_aller_others" id="txtBxAllerImmBloLym" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_aller_others"]); ?>',this,event); unChkNegBox('negChkBxAllerImmBloLym', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_aller_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- Cardiovascular -->
      <div class="col-lg-6 col-md-12 col-sm-12 ">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Cardiovascular</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[4]; ?> name='negChkBx[]' id="negChkBxCardio" value="4" onClick="chk_change('<?php echo addslashes($negChkBx[4]); ?>',this,event); disableItsFields(this.id,'chkBxChestPain','chkBxShortBreath','chkBxIrregularRhythm','txtBxCardiovascular','chkBxCongHeartFailure','chkBxHgBldPrsr','chkBxLwBldPrsr','chkBxPcMkrDF');" />
              	<label for="negChkBxCardio">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
        	
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[1]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[1]; ?> id="chkBxChestPain" name='review_card[]' value="1" />
            	<label for="chkBxChestPain">Chest Pain</label>
            </div>
        	</div>
          
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
		
            	<input type="checkbox" onClick="chk_change('<?php echo addslashes($review_card[2]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[2]; ?> id="chkBxCongHeartFailure"  name='review_card[]' value="2" />
            	<label for="chkBxCongHeartFailure">Congestive Heart Failure</label>
		
            </div>
        	</div>
	
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[5]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[5]; ?> id="chkBxHgBldPrsr" name='review_card[]' value="5" />
            	<label for="chkBxHgBldPrsr">High Blood Pressure</label>
            </div>
        	</div>	
          
         
          <div class="clearfix"></div>
	  
	 <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[3]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php print $review_card[3]; ?> id="chkBxIrregularRhythm" name='review_card[]' value="3" />
            	<label for="chkBxIrregularRhythm">Irregular Heart beat</label>
            </div>
        	</div>  
	  
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[6]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[6]; ?> id="chkBxLwBldPrsr" name='review_card[]' value="6" />
            	<label for="chkBxLwBldPrsr">Low Blood Pressure</label>
            </div>
        	</div>  
	  
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[7]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[7]; ?> id="chkBxPcMkrDF" name='review_card[]' value="7" />
            	<label for="chkBxPcMkrDF">Pacemaker/defibrillator</label>
            </div>
        	</div>
		
	  
          <div class="clearfix"></div>  

	<div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_card[4]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" <?php echo $review_card[4]; ?> id="chkBxShortBreath" name='review_card[]' value="4" />
            	<label for="chkBxShortBreath">Shortness of Breath</label>
            </div>
        	</div>


        	<div class="col-sm-2 mb5">
          	<label for="txtBxCardiovascular" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-6 mb5">
          	<input type="text" size="30" name="review_card_others" id="txtBxCardiovascular" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_card_others"]); ?>',this,event); unChkNegBox('negChkBxCardio', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_card_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix"></div>
      
      <!-- Constitutional  -->
      <div class="col-lg-6 col-md-12 col-sm-12 ">
    <div class="revsubbox">  	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Constitutional</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[1]; ?> name='negChkBx[]' id="negChkBxConInt" value="1" onClick="chk_change('<?php echo addslashes($negChkBx[1]); ?>',this,event); disableItsFields(this.id,'chkBxFever','chkBxWeightLoss','chkBxRash','chkBxFatigue','txtBxConstIntOther');" />
              	<label for="negChkBxConInt">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<!--
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_const[4]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" <?php echo $review_const[4]; ?> id="chkBxSkinDisease" name='review_const[]' value="4" />
		<label for="chkBxSkinDisease">Skin Disease</label>
		-->
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_const[5]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" <?php echo $review_const[5]; ?> id="chkBxFatigue" name='review_const[]' value="5" />
		<label for="chkBxFatigue">Fatigue</label>
            </div>
        	</div>
        	
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_const[1]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" <?php print $review_const[1]; ?> id="chkBxFever"  name='review_const[]' value="1" />
							<label for="chkBxFever">Fever</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_const[3]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" <?php echo $review_const[3]; ?> id="chkBxRash"  name='review_const[]' value="3" />
							<label for="chkBxRash">Rash</label>
            </div>
        	</div>	
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_const[2]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" <?php echo $review_const[2];?>  id="chkBxWeightLoss" name='review_const[]' value="2" />
							<label for="chkBxWeightLoss">Weight Loss</label>
            </div>
        	</div>
          
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxConstIntOther" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" name="review_const_others" id="txtBxConstIntOther" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_const_others"]); ?>',this,event); unChkNegBox('negChkBxConInt', 'cbkMasterROS');" class="form-control" value="<?php echo $gen_medicine["review_const_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg "></div>
      
      <!-- Ear, Nose, mouth & throat -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Ear, Nose, mouth & throat</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[2]; ?> name='negChkBx[]' id="negChkBxHeadNeck" value="2" onClick="chk_change('<?php echo addslashes($negChkBx[2]); ?>',this,event); disableItsFields(this.id,'chkBxSinusProblems','chkBxPostNasalDrip','chkBxRunnyNose','chkBxDryMouth','chkBxHearingLoss','txtBxHeadNeckOther');">
              	<label for="negChkBxHeadNeck">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_head[5]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" <?php echo $review_head[5]; ?> id="chkBxHearingLoss" name='review_head[]' value="5">
              <label for="chkBxHearingLoss">Deafness</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_head[4]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" <?php echo $review_head[4]; ?> id="chkBxDryMouth"  name='review_head[]' value="4">
							<label for="chkBxDryMouth">Dry Mouth</label>
            </div>
        	</div>

	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_head[2]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" <?php print $review_head[2]; ?> id="chkBxPostNasalDrip"  name='review_head[]' value="2">
							<label for="chkBxPostNasalDrip">Post Nasal Drips</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_head[3]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" <?php echo $review_head[3]; ?> id="chkBxRunnyNose" name='review_head[]' value="3">
							<label for="chkBxRunnyNose">Runny Nose</label>
            </div>
        	</div>          
          
          <div class="clearfix"></div>
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_head[1]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" <?php echo $review_head[1]; ?> id="chkBxSinusProblems"  name='review_head[]' value="1">
              <label for="chkBxSinusProblems">Sinus Infection</label>
            </div>
        	</div>
                    
        	<div class="col-sm-2 mb5">
          	<label for="txtBxHeadNeckOther" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-7 mb5">
          	<input type="text" name="review_head_others" id="txtBxHeadNeckOther" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_head_others"]); ?>',this,event); unChkNegBox('negChkBxHeadNeck', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_head_others"]; ?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix"></div>
      
      <!-- ENDOCRINE -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">ENDOCRINE</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[13]; ?> name='negChkBx[]' id="negChkBxEndocrine" value="13" onClick="chk_change('<?php echo addslashes($negChkBx[13]); ?>',this,event); disableItsFields(this.id,'chkBxMoodSwings','chkBxPolydipsia','txtBxEndocrineOther','chkBxHyperthyroidism','chkBxHypothyroidism');">
              	<label for="negChkBxEndocrine">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">	
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_endocrine[5]); ?>',this,event); unChkNegBox('negChkBxEndocrine', 'cbkMasterROS');" <?php echo $review_endocrine[5]; ?> id="chkBxHyperthyroidism" name='review_endocrine[]' value="5">
							<label for="chkBxHyperthyroidism">Hyperthyroidism</label>
            </div>
        	</div>

	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_endocrine[4]); ?>',this,event); unChkNegBox('negChkBxEndocrine', 'cbkMasterROS');" <?php echo $review_endocrine[4]; ?> id="chkBxHypothyroidism" name='review_endocrine[]' value="4">
							<label for="chkBxHypothyroidism">Hypothyroidism</label>
            </div>
        	</div>
	  
	 <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_endocrine[3]); ?>',this,event); unChkNegBox('negChkBxEndocrine', 'cbkMasterROS');" <?php echo $review_endocrine[3]; ?> id="chkBxPolydipsia" name='review_endocrine[]' value="3">
							<label for="chkBxPolydipsia">Polydipsia</label>
            </div>
        	</div> 
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_endocrine[1]); ?>',this,event); unChkNegBox('negChkBxEndocrine', 'cbkMasterROS');" <?php echo $review_endocrine[1]; ?> id="chkBxMoodSwings"  name='review_endocrine[]' value="1">
              <label for="chkBxMoodSwings">Mood Swings</label>
            </div>
        	</div>	
	
	<div class="clearfix"></div>
	
        	<div class="col-sm-2 mb5">
          	<label for="txtBxEndocrineOther" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-7 mb5">
          	<input type="text" name="review_endocrine_others" id="txtBxEndocrineOther" class="form-control" onKeyUp="chk_change('<?php echo addslashes($review_endocrine_others); ?>',this,event); unChkNegBox('negChkBxEndocrine', 'cbkMasterROS');" value="<?php echo $review_endocrine_others; ?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- EYES -->
       <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">EYES</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[14]; ?> name='negChkBx[]' id="negChkBxEye" value="14" onClick="chk_change('<?php echo addslashes($negChkBx[14]); ?>',this,event); disableItsFields(this.id,'chkBxVisionLoss','chkBxEyepain','chkBxDoublevision','txtBxEyeOther');">
              	<label for="negChkBxEye">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_eye[3]); ?>',this,event); unChkNegBox('negChkBxEye', 'cbkMasterROS');" <?php echo $review_eye[3]; ?> id="chkBxDoublevision" name='review_eye[]' value="3">
							<label for="chkBxDoublevision">Double vision</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_eye[2]); ?>',this,event); unChkNegBox('negChkBxEye', 'cbkMasterROS');" <?php print $review_eye[2]; ?> id="chkBxEyepain"  name='review_eye[]' value="2">
							<label for="chkBxEyepain">Eye pain</label>
            </div>
        	</div>

	<!--
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_eye[4]); ?>',this,event); unChkNegBox('negChkBxEye', 'cbkMasterROS');" <?php echo $review_eye[4]; ?> id="chkBxEyeHeadache" name='review_eye[]' value="4">
							<label for="chkBxEyeHeadache">Headache</label>
            </div>
        	</div>
	-->
        	
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_eye[1]); ?>',this,event); unChkNegBox('negChkBxEye', 'cbkMasterROS');" <?php echo $review_eye[1]; ?> id="chkBxVisionLoss"  name='review_eye[]' value="1">
              <label for="chkBxVisionLoss">Vision loss</label>
            </div>
        	</div>          	
          
          <div class="clearfix"></div>
                    
        	<div class="col-sm-2 mb5">
          	<label for="txtBxEyeOther" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-7 mb5">
          	<input type="text" name="review_eye_others" id="txtBxEyeOther" class="form-control" onKeyUp="chk_change('<?php echo addslashes($review_eye_others); ?>',this,event); unChkNegBox('negChkBxEye', 'cbkMasterROS');" value="<?php echo $review_eye_others; ?>">
         	</div>
          
        </div></div>
    	</div>
      
     
      
      <div class="clearfix "></div>
      
      <!-- Gastrointestinal -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Gastrointestinal</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[5]; ?> name='negChkBx[]' id="negChkBxGastro" value="5" onClick="chk_change('<?php echo addslashes($negChkBx[5]); ?>',this,event); disableItsFields(this.id,'chkBxVomiting','chkBxUlcers','chkBxDiarrhea','chkBxBloodyStools','txtBxGastro','chkBxHepatitis','chkBxJaundice','chkBxConstipation');" />
              	<label for="negChkBxGastro">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[4]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[4]; ?> id="chkBxBloodyStools" name='review_gastro[]' value="4" />
             	<label for="chkBxBloodyStools">Bloody Stools</label>
            </div>
        	</div>

	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[7]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[7]; ?> id="chkBxConstipation" name='review_gastro[]' value="7" />
             	<label for="chkBxConstipation">Constipation</label>
            </div>
        	</div>	
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[3]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[3]; ?> id="chkBxDiarrhea" name='review_gastro[]' value="3" />
							<label for="chkBxDiarrhea">Diarrhea</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[5]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[5]; ?> id="chkBxHepatitis" name='review_gastro[]' value="5" />
             	<label for="chkBxHepatitis">Hepatitis</label>
            </div> 
	</div>    
	  
	<div class="clearfix"></div>
	
	   <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[6]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[6]; ?> id="chkBxJaundice" name='review_gastro[]' value="6" />
             	<label for="chkBxJaundice">Jaundice</label>
            </div>
	 </div>
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[2]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[2]; ?> id="chkBxUlcers" name='review_gastro[]' value="2" />
							<label for="chkBxUlcers">Ulcers</label>
            </div>
        	</div>	
		
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_gastro[1]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" <?php echo $review_gastro[1]; ?> id="chkBxVomiting" name='review_gastro[]' value="1" />
              <label for="chkBxVomiting">Vomiting</label>
            </div>
        	</div>   
	  
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxGastro" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" name="review_gastro_others" id="txtBxGastro" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_gastro_others"]); ?>',this,event); unChkNegBox('negChkBxGastro', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_gastro_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- Genitourinary -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Genitourinary</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[6]; ?> name='negChkBx[]' id="negChkBxGenito" value="6" onClick="chk_change('<?php echo addslashes($negChkBx[6]); ?>',this,event); disableItsFields(this.id,'chkBxGenitalUlcers','chkBxDischarge','chkBxKidneyStones','chkBxBloodUrine','txtBxGenitourinary');">
              	<label for="negChkBxGenito">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_genit[4]); ?>',this,event); unChkNegBox('negChkBxGenito', 'cbkMasterROS');" <?php echo $review_genit[4]; ?> id="chkBxBloodUrine" name='review_genit[]' value="4">
							<label for="chkBxBloodUrine">Blood in Urine</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_genit[2]); ?>',this,event); unChkNegBox('negChkBxGenito', 'cbkMasterROS');" <?php echo $review_genit[2]; ?> id="chkBxDischarge" name='review_genit[]' value="2" />
              <label for="chkBxDischarge">Discharge</label>
            </div>
        	</div>	
        	
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_genit[1]); ?>',this,event); unChkNegBox('negChkBxGenito', 'cbkMasterROS');" <?php echo $review_genit[1]; ?> id="chkBxGenitalUlcers" name='review_genit[]' value="1">
              <label for="chkBxGenitalUlcers">Genital Ulcers</label>
            </div>
        	</div>
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_genit[3]); ?>',this,event); unChkNegBox('negChkBxGenito', 'cbkMasterROS');" <?php echo $review_genit[3]; ?> id="chkBxKidneyStones" name='review_genit[]' value="3">
							<label for="chkBxKidneyStones">Kidney Stones</label>
            </div>
        	</div>
          
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxGenitourinary" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" size="30" name="review_genit_others" id="txtBxGenitourinary" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_genit_others"]); ?>',this,event); unChkNegBox('negChkBxGenito', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_genit_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix "></div>
      
      <!-- Hemotologic/Lymphatic -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Hemotologic/Lymphatic</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[11]; ?> name='negChkBx[]' id="negChkBxBloodLymph" value="11" onClick="chk_change('<?php echo addslashes($negChkBx[11]); ?>',this,event); disableItsFields(this.id,'chkBxAnemia','chkBxBloodTransfusions','chkBxExcessiveBleeding','chkBxPurpura','txtBxBloodLymph','chkBxInfection');">
              	<label for="negChkBxBloodLymph">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
        	
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_blood_lymph[1]); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" <?php echo $review_blood_lymph[1]; ?> id="chkBxAnemia" name='review_blood_lymph[]' value="1">
              <label for="chkBxAnemia">Anemia</label>
            </div>
        	</div>
          
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_blood_lymph[2]); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" <?php echo $review_blood_lymph[2]; ?> id="chkBxBloodTransfusions" name='review_blood_lymph[]' value="2" />
              <label for="chkBxBloodTransfusions">Blood Transfusions</label>
            </div>
        	</div>
          
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_blood_lymph[3]); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" <?php echo $review_blood_lymph[3]; ?> id="chkBxExcessiveBleeding" name='review_blood_lymph[]' value="3">
							<label for="chkBxExcessiveBleeding">Excessive Bleeding</label>
            </div>
        	</div>
          <div class="clearfix"></div>
	 
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_blood_lymph[5]); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" <?php echo $review_blood_lymph[5]; ?> id="chkBxInfection" name='review_blood_lymph[]' value="5">
							<label for="chkBxInfection">Infection</label>
            </div>
        	</div>
		
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_blood_lymph[4]); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" <?php echo $review_blood_lymph[4]; ?> id="chkBxPurpura" name='review_blood_lymph[]' value="4">
							<label for="chkBxPurpura">Purpura</label>
            </div>
        	</div>
	  
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxBloodLymph" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" size="30" name="review_blood_lymph_others" id="txtBxBloodLymph" class="form-control" onKeyUp="chk_change('<?php echo addslashes($review_blood_lymph_others); ?>',this,event); unChkNegBox('negChkBxBloodLymph', 'cbkMasterROS');" value="<?php echo $review_blood_lymph_others;?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- Integumentary -->
      <div class="col-lg-6 col-md-12 col-sm-12 ">
    <div class="revsubbox">  	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Integumentary</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[9]; ?> name='negChkBx[]' id="negChkBxIntgmntr" value="9" onClick="chk_change('<?php echo addslashes($negChkBx[9]); ?>',this,event); disableItsFields(this.id,'chkBxRashes','chkBxWounds','chkBxBreastLumps','chkBxEczema','txtBxIntgmntrOther','chkBxDermatitis');" />
              	<label for="negChkBxIntgmntr">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	 <div class="col-sm-3 mb5">
          	<div class="checkbox">
		
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_intgmntr[3]); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" <?php echo $review_intgmntr[3]; ?> id="chkBxBreastLumps"  name='review_intgmntr[]' value="3" />
							<label for="chkBxBreastLumps">Breast Lumps</label>
							
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_intgmntr[5]); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" <?php echo $review_intgmntr[5]; ?> id="chkBxDermatitis" name='review_intgmntr[]' value="5" />
		<label for="chkBxDermatitis">Dermatitis</label>
		
		
            </div>
        	</div> 	
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_intgmntr[4]); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" <?php echo $review_intgmntr[4]; ?> id="chkBxEczema" name='review_intgmntr[]' value="4" />
		<label for="chkBxEczema">Eczema</label>
		
		
            </div>
        	</div>
        	
          <div class="col-sm-3 mb5">
          	<div class="checkbox">		
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_intgmntr[1]); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" <?php print $review_intgmntr[1]; ?> id="chkBxRashes"  name='review_intgmntr[]' value="1" />
							<label for="chkBxRashes">Rashes</label>
							
            </div>
        	</div>          
          
          <div class="clearfix"></div>
	  
	 <div class="col-sm-3 mb5">
          	<div class="checkbox">
		
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_intgmntr[2]); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" <?php echo $review_intgmntr[2];?>  id="chkBxWounds" name='review_intgmntr[]' value="2" />
							<label for="chkBxWounds">Wounds</label>
							
            </div>
        	</div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxIntgmntrOther" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-7 mb5">
          	<input type="text" name="review_intgmntr_others" id="txtBxIntgmntrOther" onKeyUp="chk_change('<?php echo addslashes($review_intgmntr_others); ?>',this,event); unChkNegBox('negChkBxIntgmntr', 'cbkMasterROS');" class="form-control" value="<?php echo $review_intgmntr_others;?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix  "></div>
      
      <!-- Musculoskeletal -->
       <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Musculoskeletal</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[12]; ?> name='negChkBx[]' id="negChkBxMusculoskeletal" value="12" onClick="chk_change('<?php echo addslashes($negChkBx[12]); ?>',this,event); disableItsFields(this.id,'chkBxPain','chkBxJointAche','chkBxStiffness','chkBxSwelling','txtBxMusculoskeletal','chkBxParalysisFever');">
              	<label for="negChkBxMusculoskeletal">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	 <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[2]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[2]; ?> id="chkBxJointAche" name='review_musculoskeletal[]' value="2" />
              <label for="chkBxJointAche">Joint Ache</label>
            </div>
        	</div>
        	
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[1]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[1]; ?> id="chkBxPain" name='review_musculoskeletal[]' value="1">
              <label for="chkBxPain">Pain</label>
            </div>
        	</div>
		
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[5]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[5]; ?> id="chkBxParalysisFever" name='review_musculoskeletal[]' value="5">
							<label for="chkBxParalysisFever">Paralysis Fever</label>
            </div>
        	</div>	
          
	<div class="clearfix"></div>
          
          <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[3]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[3]; ?> id="chkBxStiffness" name='review_musculoskeletal[]' value="3">
							<label for="chkBxStiffness">Stiffness</label>
            </div>
        	</div>

	 <div class="col-sm-4 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[4]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[4]; ?> id="chkBxSwelling" name='review_musculoskeletal[]' value="4">
							<label for="chkBxSwelling">Swelling</label>
            </div>
        	</div>
	
	<!--	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_musculoskeletal[6]); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" <?php echo $review_musculoskeletal[6]; ?> id="chkBxMusHeadache" name='review_musculoskeletal[]' value="6">
							<label for="chkBxMusHeadache">Headache</label>
            </div>
        	</div>
	-->		
	  
          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxMusculoskeletal" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" size="30" name="review_musculoskeletal_others" id="txtBxMusculoskeletal" class="form-control" onKeyUp="chk_change('<?php echo addslashes($review_musculoskeletal_others); ?>',this,event); unChkNegBox('negChkBxMusculoskeletal', 'cbkMasterROS');" value="<?php echo $review_musculoskeletal_others;?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- Neurological -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Neurological</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[8]; ?> name='negChkBx[]' id="negChkBxNeurPsycMuscu" value="8" onClick="chk_change('<?php echo addslashes($negChkBx[8]); ?>',this,event); disableItsFields(this.id,'chkBxHeadache','chkBxSeizures','chkBxNumbness','chkBxFaints','txtBxNeurPsycMuscu','chkBxMigraines','chkBxMltSclrs','chkBxStroke','chkBxNurAlzDis','chkBxNurParkDis','chkBxDimentia');">
              	<label for="negChkBxNeurPsycMuscu">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[10]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[10]; ?> id="chkBxNurAlzDis" name='review_neuro[]' value="10">
              <label for="chkBxNurAlzDis">Alzheimer's Disease</label>
            </div>
        	</div>
		
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[12]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[12]; ?> id="chkBxDimentia" name='review_neuro[]' value="12">
              <label for="chkBxDimentia">Dementia</label>
            </div>
        	</div>	
	
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[7]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[7]; ?> id="chkBxFaints" name='review_neuro[]' value="7">
              <label for="chkBxFaints">Faints</label>
            </div>
        	</div>
		
	<div class="clearfix"></div> 	
		
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[1]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[1]; ?> id="chkBxHeadache" name='review_neuro[]' value="1">
              <label for="chkBxHeadache">Headache</label>
            </div>
        	</div>
	  
          <div class="col-sm-4 mb5">
          	<div class="checkbox">		
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[2]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[2]; ?> id="chkBxMigraines" name='review_neuro[]' value="2">
              <label for="chkBxMigraines">Migraines</label>		
            </div>
        	</div>
		
	<div class="col-sm-4 mb5">
          	<div class="checkbox">		
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[9]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[9]; ?> id="chkBxMltSclrs" name='review_neuro[]' value="9">
              <label for="chkBxMltSclrs">Multiple Sclerosis</label>
            </div>
        	</div>
		
	<div class="clearfix"></div>	
	  
	 <div class="col-sm-4 mb5">
          	<div class="checkbox">
		
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[6]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[6]; ?> id="chkBxNumbness" name='review_neuro[]' value="6">
							<label for="chkBxNumbness">Numbness</label>
            </div>
        	</div>
	
	<div class="col-sm-4 mb5">
          	<div class="checkbox">		
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[11]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[11]; ?> id="chkBxNurParkDis" name='review_neuro[]' value="11">
							<label for="chkBxNurParkDis">Parkinson's Disease</label>
            </div>
        	</div>
		
	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[5]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[5]; ?> id="chkBxSeizures" name='review_neuro[]' value="5">
              <label for="chkBxSeizures">Seizures</label>
            </div>
        	</div> 

	<div class="clearfix"></div>  

	<div class="col-sm-4 mb5">
          	<div class="checkbox">
		<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_neuro[8]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" <?php echo $review_neuro[8]; ?> id="chkBxStroke"  name='review_neuro[]' value="8">
							<label for="chkBxStroke">Stroke</label>
            </div>
        	</div>
          
          <div class="col-sm-2 mb5">
          	<label for="txtBxNeurPsycMuscu" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-6 mb5">
          	<input type="text" name="review_neuro_others" id="txtBxNeurPsycMuscu" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_neuro_others"]); ?>',this,event); unChkNegBox('negChkBxNeurPsycMuscu', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_neuro_others"];?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix "></div>
      
       <!-- Psychiatry -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Psychiatry</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[10]; ?> name='negChkBx[]' id="negChkBxPsychiatry" value="10" onClick="chk_change('<?php echo addslashes($negChkBx[10]); ?>',this,event); disableItsFields(this.id,'chkBxDepression','chkBxAnxiety','chkBxParanoia','chkBxSleepPatterns','txtBxPsychiatry','chkBxMntlEmoFac','chkBxMemLoss');">
              	<label for="negChkBxPsychiatry">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[2]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[2]; ?> id="chkBxAnxiety" name='review_psychiatry[]' value="2" />
              <label for="chkBxAnxiety">Anxiety</label>
            </div>
        	</div>
        	
          <div class="col-sm-6 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[1]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[1]; ?> id="chkBxDepression" name='review_psychiatry[]' value="1">
              <label for="chkBxDepression">Depression</label>
            </div>
        	</div>
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[3]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[3]; ?> id="chkBxParanoia" name='review_psychiatry[]' value="3">
							<label for="chkBxParanoia">Paranoia</label>
            </div>
        	</div>

	<div class="clearfix"></div>
	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[8]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[8]; ?> id="chkBxMemLoss" name='review_psychiatry[]' value="8">
							<label for="chkBxMemLoss">Memory Loss</label>
            </div>
	</div>

	<div class="col-sm-6 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[5]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[5]; ?> id="chkBxMntlEmoFac" name='review_psychiatry[]' value="5">
							<label for="chkBxMntlEmoFac">Mental and/or emotional factors</label>
            </div>
	</div> 

	 <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_psychiatry[4]); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" <?php echo $review_psychiatry[4]; ?> id="chkBxSleepPatterns" name='review_psychiatry[]' value="4">
							<label for="chkBxSleepPatterns">Sleep Patterns</label>
            </div>
        	</div>	

          <div class="clearfix"></div>
          
        	<div class="col-sm-2 mb5">
          	<label for="txtBxPsychiatry" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" size="30" name="review_psychiatry_others" id="txtBxPsychiatry" class="form-control" onKeyUp="chk_change('<?php echo addslashes($review_psychiatry_others); ?>',this,event); unChkNegBox('negChkBxPsychiatry', 'cbkMasterROS');" value="<?php echo $review_psychiatry_others;?>">
         	</div>
          
        </div></div>
    	</div>
      
      <div class="clearfix hidden-lg"></div>
      
      <!-- Respiratory -->
      <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="revsubbox">	<div class="revsyshead">
        	<div class="head">
        	<div class="row">
          	<div class="col-lg-9 col-md-7 col-sm-9"><span class="valign_mid">Respiratory</span></div>
            <div class="col-lg-3 col-md-5 col-sm-3 content_box">
            	<div class="checkbox">
              	<input type='checkbox' <?php echo $negChkBx[3]; ?> name='negChkBx[]' id="negChkBxResp" value="3" onClick="chk_change('<?php echo addslashes($negChkBx[3]); ?>',this,event); disableItsFields(this.id,'chkBxCough','chkBxBronchitis','chkBxShortnessBreath','chkBxAsthma','chkBxEmphysema','chkBxCOPD','chkBxTB','txtBxHeadRespiratory');">
              	<label for="negChkBxResp">Negative</label>
              </div>
           	</div>   
        	</div>
        	</div>
       	</div>
        
        <div class="clearfix"></div>
        
        <div class="row">
	
	 <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[4]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[4]; ?> id="chkBxAsthma" name='review_resp[]' value="4">
              <label for="chkBxAsthma">Asthma</label>
            </div>
        	</div>
        	
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[2]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[2]; ?> id="chkBxBronchitis" name='review_resp[]' value="2">
              <label for="chkBxBronchitis">Bronchitis</label>
            </div>
        	</div>
		
	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[6]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[6]; ?> id="chkBxCOPD" name='review_resp[]' value="6">
              <label for="chkBxCOPD">COPD</label>
            </div>
        	</div>

	<div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[1]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[1]; ?> id="chkBxCough" name='review_resp[]' value="1">
              <label for="chkBxCough">Cough</label>
            </div>
        	</div>
		
	<div class="clearfix"></div>	
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[5]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[5]; ?> id="chkBxEmphysema" name='review_resp[]' value="5">
              <label for="chkBxEmphysema">Emphysema</label>
            </div>
        	</div>
          
          <div class="col-sm-6 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[3]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[3]; ?>  id="chkBxShortnessBreath" name='review_resp[]' value="3">
              <label for="chkBxShortnessBreath">Shortness of Breath</label>
            </div>
        	</div>
          
          <div class="col-sm-3 mb5">
          	<div class="checkbox">
            	<input type='checkbox' onClick="chk_change('<?php echo addslashes($review_resp[7]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" <?php echo $review_resp[7]; ?> id="chkBxTB" name='review_resp[]' value="7">
              <label for="chkBxTB">TB</label>
            </div>
        	</div>
          
          <div class="clearfix"></div>
                   
          <div class="col-sm-2 mb5">
          	<label for="txtBxHeadRespiratory" class="pdl_25">Others</label>
         	</div>   
          <div class="col-sm-10 mb5">
          	<input type="text" name="review_resp_others" id="txtBxHeadRespiratory" class="form-control" onKeyUp="chk_change('<?php echo addslashes($gen_medicine["review_resp_others"]); ?>',this,event); unChkNegBox('negChkBxResp', 'cbkMasterROS');" value="<?php echo $gen_medicine["review_resp_others"]; ?>">
         	</div>
          
        </div></div>
    	</div>      
      
      <div class="clearfix "></div>
      
      
	
	</div>    
		</div>
    <?php
			$intSocialNumRow = 0;
			$query = "select social.*, date_format(social.modified_on,'%c/%e/%y') as date,
									Date_Format(offered_cessation_counselling_date,'".get_sql_date_format()."') as offeredCessationCounsellingDate,
									Date_Format(smoke_start_date,'".get_sql_date_format()."') as smokeStartDate,
									Date_Format(smoke_end_date,'".get_sql_date_format()."') as smokeEndDate,
									time_format(social.modified_on,'%l:%i %p') as time,	users.fname, users.mname, users.lname
									from social_history as social left join users on users.id = social.modified_by
									where patient_id = '".$health->patient_id."'";
			$sql = imw_query($query);
			$intSocialNumRow = imw_num_rows($sql);
			$result2 = imw_fetch_assoc($sql);
			
			$dateOfferedCessationCounselling = $result2["offeredCessationCounsellingDate"];
			if(get_number($dateOfferedCessationCounselling) == '00000000')
			{
				$dateOfferedCessationCounselling = '';
			}
			$smoke_start_date = $result2["smokeStartDate"];
			if(get_number($smoke_start_date) == '00000000')
			{
				$smoke_start_date = '';
			}
			$smoke_end_date = $result2["smokeEndDate"];
			if(get_number($smoke_end_date) == '00000000')
			{
				$smoke_end_date = '';
			}
			$alcohal2 = $result2["alcohal"];
			$elem_otherSocial = $result2["otherSocial"];
		?>
    
    
    <!-- Social History -->
    <div class="row mt5">
    	<div class="col-lg-8 col-sm-12 ">
      	<div class="socialbox"><div class="row">
        	
          <div class="col-xs-12">
          	<div class="head">
          	<div class="row">
       				<span>Social History</span>
        		</div>
        		</div>
     			</div>
          <div class="clearfix"></div>
          
          <?php
						$smoke_opt="<option value=''></option>";
						foreach($health->show_smoke_arr as $smoke_key=>$smoke_val){
								$sel="";
								if($smoke_key==$result2['smoking_status_id']){  $sel="selected";}
									$smoke_opt.= "<option value='".$smoke_key."' $sel >".$smoke_val."</option>";
						}
					?>
          <div class="col-xs-5 mb5">
          	<div class="row">
          		<div class="col-xs-7">  
              	<label>Smoke</label>
                <select name="SmokingStatus" id="SmokingStatus" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>" onChange="set_smoke_code(this.value);controlsMode(this); chk_change('',this,event);" >
            			<?php echo $smoke_opt; ?>       
           			</select>
            	</div>
          		<div class="col-xs-5">
              	<label>Snomed Code</label>
                <input type="text" class="form-control" id="smoking_code" name="smoking_code" readonly value="<?php echo $health->show_code_arr[$result2['smoking_status_id']]; ?>">
             	</div>
          	</div>    
          </div>
          <div class="col-xs-2 mb5">
          	<label>Type</label>
            <?php
							/*$other_smoking_source_display = "none";
							if($result2["source_of_smoke"]=="Other")
							{
								$other_smoking_source_display = "inline";
							}*/
							$type_disabled="";
							if(	$result2["smoking_status"]=="Never Smoked" || $result2["smoking_status"]=="Never smoker" )
							{
								$type_disabled = "disabled";	
							}
						?>
            <div id="div_source_of_smoke">
              <select class="selectpicker" name="source_of_smoke" id="source_of_smoke" data-width="100%" title="<?php echo imw_msg('drop_sel');?>" <?php echo $type_disabled; ?> onChange="show_hide('other_source_of_smoke', 'div_source_of_smoke', this); chk_change('',this,event);" >
                <option value=""></option>
                <?php
                  $arrOptions = array("Cigars", "Cigarettes", "Tobacco");
                  sort($arrOptions);
                  array_push($arrOptions,"Other");
                  foreach($arrOptions as $source)
                  {
                    $selected =  ($result2["source_of_smoke"] == $source) ? 'selected' : '';
                ?>
                    <option <?php echo($selected);?> value="<?php echo$source; ?>"><?php echo $source; ?></option>
                <?php } ?>
              </select>
          	</div>
            <div id="other_source_of_smoke" class="hidden">
            	<div class="input-group">
              	<input type="text" class="form-control" id="source_of_smoke_other" name="source_of_smoke_other" <?php echo $type_disabled; ?> onKeyUp="chk_change('<?php echo addslashes($result2["source_of_smoke_other"]); ?>',this,event);" value="<?php echo $result2["source_of_smoke_other"]; ?>" />
                <label class="input-group-addon btn back_other" data-tab-name="source_of_smoke"><i class="glyphicon glyphicon-arrow-left"></i></label>
            	</div>
          	</div>
					</div>
          
         	<div class="col-xs-2 mb5">
          	<label>Frequency</label>
            <input type='text' name='smoke_perday' id="smoke_perday" <?php echo $type_disabled; ?> onKeyUp="chk_change('<?php echo addslashes($result2["smoke_perday"]); ?>',this,event);" value="<?php echo $result2["smoke_perday"]?>" class="form-control" />
          </div>
          
          <div class="col-xs-1 mb5">
          	<label>For</label>
            <input type='text' name='number_of_years_with_smoke' id="number_of_years_with_smoke" <?php echo $type_disabled; ?> onKeyUp="chk_change('<?php echo addslashes($result2["number_of_years_with_smoke"]); ?>',this,event);" value="<?php echo $result2["number_of_years_with_smoke"]; ?>" class="form-control" />
          </div>
          
          <div class="col-xs-2 mb5">
          	<label>Period</label>
            <select name="smoke_years_months" id="smoke_years_months" class="selectpicker" data-width="100%" <?php echo $type_disabled; ?> onChange="chk_change('',this,event);">
            	<?php
								$arrOptionsYearMonth = array("Years", "Months");
								foreach($arrOptionsYearMonth as $v)
								{
									$selected= $result2["smoke_years_months"] == $v ? 'selected' : '';
									echo '<option value="'.$v.'" '.$selected.'>'.ucfirst($v).'</option>';
								}
							?>
          	</select>
          </div>
          
		<div class="clearfix"></div>
		<div class="col-sm-3">
			<label for="smoke_start_date">Start Date</label>
			<div class="input-group">
				<input type="text" name="smoke_start_date" id="smoke_start_date" onKeyUp="chk_change('<?php echo addslashes($smoke_start_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($smoke_start_date); ?>',this,event);" value="<?php
				if ($smoke_start_date) {
					echo $smoke_start_date;
				}
				?>" title="<?php echo $GLOBALS['date_format']; ?>" class="datepicker form-control" onClick="if(!this.value) {getDate_and_setToField(this);}" onBlur="checkdate(this);">
				<label class="input-group-addon btn" for="smoke_start_date"><i class="glyphicon glyphicon-calendar"></i></label>
			</div>
		</div>
		<div class="col-sm-3">
			<label for="smoke_end_date">End Date</label>
			<div class="input-group">
				<input type="text" name="smoke_end_date" id="smoke_end_date" onKeyUp="chk_change('<?php echo addslashes($smoke_end_date); ?>',this,event);" onChange="chk_change('<?php echo addslashes($smoke_end_date); ?>',this,event);" value="<?php
				if ($smoke_end_date) {
					echo $smoke_end_date;
				}
				?>" title="<?php echo $GLOBALS['date_format']; ?>" class="datepicker form-control" onClick="if(!this.value) {getDate_and_setToField(this);}" onBlur="checkdate(this);">
				<label class="input-group-addon btn" for="smoke_end_date"><i class="glyphicon glyphicon-calendar"></i></label>
			</div>
		</div>
		
		<div class="col-sm-3">
			<label for="birth_sex">Birth Sex</label>
			<?php
				$arrGender = gender();
			?>
			<select name="birth_sex" id="birth_sex" class="selectpicker" data-width="100%" data-prev-val="<?php echo addslashes($gen_medicine['birth_sex']); ?>" title="<?php echo imw_msg('drop_sel'); ?>" >
			<?php
				foreach($arrGender as $key => $val)
				{
					$key = trim($key);
					$sel = ($gen_medicine['birth_sex'] == $key ) ? 'selected' : '';
					echo '<option value="'.$key.'" '.$sel.'>'.$key.'</option>';
				}
			?>
			</select>
		</div>
		
		<div class="col-sm-3">
			<label for="birth_sex_date">Status Date</label>
			<div class="input-group">
				<input type="text" name="birth_sex_date" id="birth_sex_date" onKeyUp="chk_change('<?php echo addslashes($gen_medicine['birth_sex_date']); ?>',this,event);" onChange="chk_change('<?php echo addslashes($gen_medicine['birth_sex_date']); ?>',this,event);" value="<?php echo get_date_format($gen_medicine['birth_sex_date']); ?>" title="<?php echo $GLOBALS['date_format']; ?>" class="datepicker form-control" onClick="if(!this.value) {getDate_and_setToField(this);}" onBlur="checkdate(this);">
				<label class="input-group-addon btn" for="birth_sex_date"><i class="glyphicon glyphicon-calendar"></i></label>
			</div>
		</div>
		
		
		<div class="clearfix"></div>
          
          <div class="col-xs-5 col-sm-6 col-md-5 mb5">
          	<div class="row">
          		<div class="col-xs-7 col-sm-6 col-md-7"><br>
              	<div class="checkbox">
                	<input type="checkbox" <?php echo $type_disabled; ?> <?php echo $result2["smoke_counseling"]==1?"checked":""; ?> name="offered_cessation_counseling" id="offered_cessation_counseling" value="1" onClick="chk_change('<?php echo addslashes($result2["smoke_counseling"]==1?"checked":""); ?>',this,event); if(this.checked){dgi('txtDateOfferedCessationCounselling').click();}else{dgi('txtDateOfferedCessationCounselling').value='';} controlsMode(this);" />
                 	<label for="offered_cessation_counseling">Cessation Counseling</label> 
                </div>
              </div>
              
              <div class="col-xs-5 col-sm-6 col-md-5 ">
              	<label for="txtDateOfferedCessationCounselling">Counseling Date</label>
              	<div class="input-group">
                	<input type="text" name="txtDateOfferedCessationCounselling" id="txtDateOfferedCessationCounselling" onKeyUp="chk_change('<?php echo addslashes($dateOfferedCessationCounselling); ?>',this,event);" onChange="chk_change('<?php echo addslashes($dateOfferedCessationCounselling); ?>',this,event);" value="<?php if($dateOfferedCessationCounselling){echo $dateOfferedCessationCounselling;} ?>" title="<?php echo $GLOBALS['date_format'];?>" class="datepicker form-control" onClick="if(!this.value) {getDate_and_setToField(this);}" onBlur="checkdate(this);">
                  <label class="input-group-addon btn" for="txtDateOfferedCessationCounselling"><i class="glyphicon glyphicon-calendar"></i></label>
                </div>
              </div>
          	</div>
          </div>
          
          <div class="col-xs-7 col-sm-6 col-md-7  mb5">
          	<label for="cessationCounselling">Type</label>
            <div id="div_cessationCounselling">
              <select name="cessationCounselling" id="cessationCounselling" class="selectpicker" <?php echo $type_disabled; ?> onChange="show_hide('other_cessationCounselling','div_cessationCounselling',this); chk_change('',this,event);" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
              <?php
                $arrSmoke = array("","Advised patient to Quit","Discussed Smoking and Tobacco Use Cessation Medications","Discussed Smoking and Tobacco Use Cessation Strategies","Other");
                foreach ($arrSmoke as $s)
                {
                  $sel = ($s == $result2["cessation_counselling_option"]) ? 'selected="selected"' : '';
                  echo '<option value="'.$s.'" '.$sel.'>'.ucfirst($s).'</option>';
                }
              ?>
              </select>
           	</div>
            <div id="other_cessationCounselling" class="hidden">
            	<div class="input-group">
              	<input type="text" class="form-control" id="cessationCounsellingOther" name="cessationCounsellingOther" <?php echo $type_disabled; ?> onKeyUp="chk_change('<?php echo addslashes($result2["cessation_counselling_other"]); ?>',this,event);" value="<?php echo $result2["cessation_counselling_other"]; ?>" />
                <label class="input-group-addon btn back_other" data-tab-name="cessationCounselling">
                	<i class="glyphicon glyphicon-arrow-left"></i>
               	</label>
           	</div>     
          </div>
          
      	</div>
        
        	<div class="clearfix"></div>
          
          <!-- Intervention not done for Tobacco Use Cessation Counseling due to -->
          <div class="col-xs-12 mb5">
          	<div class="row">
            	<div class="col-xs-8">
              	<div class="checkbox">
                	<input type="checkbox" <?php echo $result2["intervention_not_performed_status"]=='Yes'?"checked":""; ?> name="interventionNotPerformedStatus" id="interventionNotPerformedStatus" value="Yes" />
                  <label for="interventionNotPerformedStatus">Intervention not done for Tobacco Use Cessation Counseling due to</label>
                </div>
              </div>
              <div class="col-xs-4">
              	<select name="interventionReason" id="interventionReason" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                <?php
									$arrInterVn = array("","Medical Reason","Patient Reason");
									foreach ($arrInterVn as $s)
									{
										$selected = ($s == $result2["intervention_reason_option"]) ? 'selected="selected"' : '';
										echo '<option value="'.$s.'" '.$selected.'>'.ucfirst($s).'</option>';
									}
								?>
                </select>
              </div>
              
          	</div>
          </div>
          
          <div class="clearfix"></div>
          
          <!-- Medication order not done for Tobacco Use Cessation due to -->
          <div class="col-xs-12 mb5">
          	<div class="row">
            	<div class="col-xs-8">
              	<div class="checkbox">
                	<input type="checkbox" <?php echo $result2["med_order_not_performed_status"]=='Yes'?"checked":""; ?> name="medOrderNotPerformedStatus" id="medOrderNotPerformedStatus" value="Yes" />
                  <label for="medOrderNotPerformedStatus">Medication order not done for Tobacco Use Cessation due to</label>
                </div>
              </div>
              <div class="col-xs-4">
              	<select name="medOrderReason" id="medOrderReason" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                <?php
									$arrMedOdr = array("","Medical Reason","Patient Reason");
									foreach ($arrMedOdr as $s)
									{
										$selected = ($s == $result2["med_order_reason_option"]) ? 'selected="selected"' : '';
										echo '<option value="'.$s.'" '.$selected.'>'.ucfirst($s).'</option>';
									}
								?>
                </select>
              </div>
              
          	</div>
          </div>
        
     	</div> </div>
   		</div>
    	
      <div class="col-lg-4 col-sm-12">
      	<div class="famlhxbox"><div class="row">
        	
          <div class="col-xs-12">
          	<div class="head">
          	<div class="row">
       				<span>Family Hx of Smoking</span>
        		</div>
        		</div>
     			</div>
          <div class="clearfix"></div>
          
          <div class="col-xs-3 col-md-2 col-lg-4 mb5"><br>
          	<div class="radio radio-inline ">
            	<input type="radio" name="radio_family_smoke" id="family_smoke_yes" value="1" onClick="chk_change('checked',this,event); controlsMode(this)" checked="checked"/>
              <label for="family_smoke_yes">Yes</label>
           	</div>
            
            <div class="radio radio-inline ">
            	<input type="radio" name="radio_family_smoke" id="family_smoke_no" value="0" onClick="chk_change('<?php echo($result2["family_smoke"]==0 ? "checked" : ""); ?>',this,event); controlsMode(this)" <?php echo($result2["family_smoke"]==0 ? "checked" : ""); ?>/>
              <label for="family_smoke_no">No</label>
            </div>
            
          </div>
          
         	<div class="col-xs-9 col-lg-8 mb5">
          <?php
						$dbArray = explode(",",$result2["smokers_in_relatives"]);
						$arrFamily= get_relationship_array('social_history');
					
                $other_smokers_in_relatives = '';
                $arr_string = $arr_string_tmp = array();
                $arrAllRelVals = $arrOtherVals = array();
                if (preg_match("/\bOther\b/i", $result2["smokers_in_relatives"])) {
                    $arr_string = explode(',', $result2["smokers_in_relatives"]);
                    foreach ($arr_string as $val) {
                        $arr_string_tmp[] = trim($val);
                    }
                    $arrAllRelVals = explode(',', $health->get_combo_multi($result2["smokers_in_relatives"], $arrFamily, 'forString'));
                    $arrOtherVals = array_diff($arr_string_tmp, $arrAllRelVals);
                    $other_smokers_in_relatives = implode(',', $arrOtherVals);
                }
                ?>
         		<label for="smokers_in_relatives">Relation</label>
            <div id="div_smokers_in_relatives">
              <select class="selectpicker selectpicker_new" data-width="100%" name="smokers_in_relatives[]" id="smokers_in_relatives" <?php echo($result2["family_smoke"]=="0" ? "disabled" : ""); ?> title="<?php echo imw_msg('drop_sel'); ?>" multiple onChange="chk_change('',this,event);">
              <?php
                echo $health->get_combo_multi($result2["smokers_in_relatives"],$arrFamily);
              ?>	
              </select>
            </div>
            <div id="other_smokers_in_relatives" class="hidden">
              <div class="input-group">
                <input type="text" name="rel_other_smokers_in_relatives" id="rel_other_smokers_in_relatives" value="<?php echo $other_smokers_in_relatives;?>" class="form-control" />
                <label class="input-group-addon btn back_other" data-tab-name="smokers_in_relatives">
                  <i class="glyphicon glyphicon-arrow-left "></i>
                </label>
              </div>
            </div>    
     			</div> 
          
          <div class="clearfix"></div>
          
          <div class="col-xs-12">
          	<label for="smoke_description">Description</label>
            <textarea name="smoke_description" id="smoke_description" <?php echo($result2["family_smoke"]=="0" ? "disabled" : ""); ?> class="form-control" cols="40" rows="6" style="height:88px!important;"  onKeyUp="chk_change('<?php echo addslashes($result2["smoke_description"]); ?>',this,event);"><?php echo $result2["smoke_description"]; ?></textarea>
          </div>
      	</div></div>
    	</div>    
    </div>   
 	   
   	<!-- Alcohol -->
    <div class="alchbox"><div class="row">
    	<div class="col-xs-12">
      	<div class="row head">
        	<span>Alcohol and Drugs</span>
      	</div>
     	</div>
      <div class="clearfix"></div>
    	
      <div class="col-xs-12 col-sm-4">
      	<div class="row">
          <!-- Alcohol -->  
          <div class="col-xs-12">
            <label for="alcohal">Alcohol</label>
            <div class="checkbox" style="float: right; padding: 0; margin: 0;">
              <input type="hidden" name="use_of_alcohol" value="0">
              <input type="checkbox" id="use_of_alcohol" name="use_of_alcohol" value="1" autocomplete="off" <?php echo ($result2["use_of_alcohol"] == 1) ? 'checked="checked"' : ''; ?>>
              <label for="use_of_alcohol">Do you use alcohol?</label>
            </div>
            <div id="div_alcohal">
              <select class="selectpicker selectpicker_new" data-width="100%" name="alcohal[]" id="alcohal" onChange="controlsMode(this); chk_change('',this,event);" multiple title="<?php echo imw_msg('drop_sel');?>"	 >
                <?php
                  $arrOptions = array("","Never","Beer", "Spirits", "Wine", "Former Drinker", "Other");
                  echo $health->get_combo_multi($result2["alcohal"],$arrOptions);
                ?>
              </select>
            </div>
            <div id="other_alcohal" class="hidden">
              <div class="input-group">
                <input type="text" class="form-control" id="source_of_alcohal_other" name="source_of_alcohal_other" onKeyUp="chk_change('<?php echo addslashes($result2["source_of_alcohal_other"]); ?>',this,event);" value="<?php echo $result2["source_of_alcohal_other"]; ?>" >
                <label class="input-group-addon btn back_other" data-tab-name="alcohal">
                  <i class="glyphicon glyphicon-arrow-left"></i>
                </label>
              </div>
            </div>
          </div>
   		
          <!-- Frequency -->
          <div class="col-xs-12">
            <label for="alcohal_quentity">Frequency</label>
            <div class="row">
              <div class="col-xs-4">
                <input type="text" class="form-control" id="alcohal_quentity" name="alcohal_quentity" onKeyUp="chk_change('<?php echo addslashes($result2["consumption"]); ?>',this,event);" value="<?php echo $result2["consumption"]; ?>">
              </div>
              <div class="col-xs-8">
                <select name="alcohal_time" id="alcohal_time" onChange="chk_change('',this,event);" class="selectpicker" data-width="100%" title="<?php echo imw_msg('drop_sel');?>">
                <option value=""></option>
                <?php
                  $arrOptions = array("Per Day", "Per Week", "Occasionally", "Socially");
                  echo $health->get_combo_multi($result2["alcohal_time"],$arrOptions);
                ?>
                </select>
              </div>
            </div>
          </div>
      	</div>
      </div>
      
      <!-- List Any Drugs -->
      <div class="col-xs-6 col-sm-4">
      	<label for="list_drugs">List any Drugs</label>
        <div class="checkbox" style="float: right; padding: 0; margin: 0;">
          <input type="hidden" name="use_of_drugs" value="0">
          <input type="checkbox" id="use_of_drugs" name="use_of_drugs" value="1" autocomplete="off" <?php echo ($result2["use_of_drugs"] == 1) ? 'checked="checked"' : ''; ?>>
          <label for="use_of_drugs">Do you use any drugs?</label>
        </div>
        <textarea name='list_drugs' id="list_drugs" onKeyUp="chk_change('<?php echo addslashes($result2["list_drugs"]); ?>',this,event);" class="form-control" style="height:76px!important;"><?php echo $result2["list_drugs"]; ?></textarea>
    	</div>
      
      <!-- More Information -->
      <div class="col-xs-6 col-sm-4">
      	<label for="elem_otherSocial">More Information</label>
        <textarea name="elem_otherSocial" id="elem_otherSocial" rows="3" cols="30" class="form-control" onKeyUp="chk_change('<?php echo trim(remLineBrk(addslashes($elem_otherSocial))); ?>',this,event);" style="height:76px!important;"><?php echo $elem_otherSocial;?></textarea>
     	</div>
      
    </div> </div>  
          
   	<?php 
			
			echo $health->print_misc_question('general_health');
			echo "<div class='clearfix'></div>";
			echo $health->print_speacialty_question('general_health');
			
		?>
	 	
    <div class="clearfix">&nbsp;</div> 	
    <input type="hidden" name="hidDataMedicalHistory_General_Health" value="<?php echo ($health->policy_status == 1) ? urlencode($serialized) : ''; ?>">
    <?php
			//--- REVIEWED CODE FOR CREATE ARRAY ---
			$operatorId = $_SESSION['authId'];
			$action = ($gen_medicine['record_count'] == 0) ? 'add' : 'update';
			require_once($GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php'); 
			$OBJReviewMedHx = new CLSReviewMedHx;				
			$arrReview_AD = array();
			$arrReview_AD = $OBJReviewMedHx->getReviewArrayAD($gen_medicine,$operatorId,$action);
			$arrReview_GH = array();
			$arrReview_GH = $OBJReviewMedHx->getReviewArrayGH($gen_medicine,$anyConditionsOthersBothArrGenHealth,$anyConditionsOthersBothArrRel,$this_blood_sugar,$this_cholesterol,$operatorId,$action);
			
			$action = ($intSocialNumRow == 0) ? "add" : "update";
			$arrReview_Social = array();
			$result2['offeredCessationCounsellingDate'] = get_date_format($result2['offeredCessationCounsellingDate'],inter_date_format(),'mm-dd-yyyy');
			$arrReview_Social = $OBJReviewMedHx->getReviewArraySocial($result2,$opreaterId,$action);
			?>			
			<input type="hidden" name="hid_arr_review_GH" value="<?php echo urlencode(serialize($arrReview_GH)); ?>"/>
			<input type="hidden" name="hid_arr_review_AD" value="<?php echo urlencode(serialize($arrReview_AD)); ?>"/>
			<input type="hidden" name="hid_arr_review_Social" value="<?php echo urlencode(serialize($arrReview_Social)); ?>"/>
			<input type="hidden" name="gen_health_page_load_done" id="gen_health_page_load_done" value="no">
 	</form>
</div>

<?php
//--- AUDIT TRAIL FOR VIEW -----
if(isset($_SESSION['Patient_Viewed']) === true and $health->policy_status == 1){
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_GH = array();
	$arrAuditTrailView_GH[0]['Pk_Id'] = $gen_medicine['general_id'];
	$arrAuditTrailView_GH[0]['Table_Name'] = 'general_medicine';
	$arrAuditTrailView_GH[0]['Action'] = 'view';
	$arrAuditTrailView_GH[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_GH[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_GH[0]['IP'] = $ip;
	$arrAuditTrailView_GH[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_GH[0]['URL'] = $URL;
	$arrAuditTrailView_GH[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_GH[0]['OS'] = $os;
	$arrAuditTrailView_GH[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_GH[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_GH[0]['Filed_Label'] = 'Patient General Health Data';
	$arrAuditTrailView_GH[0]['Category_Desc'] = 'general_medicine';
	$arrAuditTrailView_GH[0]['pid'] = $pid;
	$patientViewed = $_SESSION['Patient_Viewed'];

	if(is_array($patientViewed) && $patientViewed["Medical History"]["General_Health"] == 0){
		auditTrail($arrAuditTrailView_GH,$mergedArray);
		$patientViewed["Medical History"]["General_Health"] = 1;			
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}
?>
<?php include 'general_health_modal.php'; ?>

<script type="text/javascript" src="<?php echo $library_path;?>/amcharts/amcharts.js"></script>
<script type="text/javascript" src="<?php echo $library_path;?>/amcharts/serial.js"></script>
<script type="text/javascript" src="<?php echo $library_path;?>/js/general_health.js"></script>
<script>
	var vocabulary_gh = <?php echo json_encode($health->vocabulary); ?>;
	var show_code_arr = <?php echo json_encode($health->show_code_arr); ?>;
	var blood_sugar_opt_str = <?php echo json_encode($blood_sugar_opt_str); ?>;
	
	// Creating Graphs on Load 
	var bs_graph = <?php echo json_encode($health->graph_data('blood_sugar')); ?>;
	var c_graph = <?php echo json_encode($health->graph_data('cholesterol')); ?>;
	var cl_graph = <?php echo json_encode($health->graph_data('cholesterol_ldl')); ?>;
	var ch_graph = <?php echo json_encode($health->graph_data('cholesterol_hdl')); ?>;
	
	line_chart('Blood Sugar',bs_graph,'blood_sugar_graph','date','blood_sugar','0');
	line_chart('Cholesterol',c_graph,'cholesterol_graph','date','cholesterol','0');
	line_chart('Cholesterol LDL',cl_graph,'cholesterol_ldl_graph','date','cholesterol_ldl','0');
	line_chart('Cholesterol HDL',ch_graph,'cholesterol_hdl_graph','date','cholesterol_hdl','0');
	// End of creating graphs 
	
	top.btn_show("GH");
	
	document.getElementById('gen_health_page_load_done').value='yes';
</script>