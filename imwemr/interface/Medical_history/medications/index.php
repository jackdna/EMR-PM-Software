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
include_once($GLOBALS['srcdir']."/classes/medical_hx/medications.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
if(isERPPortalEnabled()) {
  include_once($GLOBALS['srcdir']."/erp_portal/rabbitmq_exchange.php");
  $OBJRabbitmqExchange = new Rabbitmq_exchange();
}
$medication = new Medications($medical->current_tab);
$medication->del_medication($_REQUEST['mode'],$_REQUEST['del_id'],$_REQUEST['callFrom'],$_REQUEST['subcall']);
$medication->data = $medication->load_medications($_REQUEST);
extract($medication->data);
echo '<script>top.show_loading_image("show");</script>';
//pre($medical_data);

if(isDssEnable()) {
  $medication->getPatientMedList();
}
?>
<?php if(isset($_GET['callFrom']) && $_GET['callFrom'] == 'WV'){ ?>
<style>
/*****extra*****/
.process_loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 80px;
  height: 80px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  display:inline-block;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

#div_loading_image{position:absolute; top:100px; z-index:1000; text-align:center; width:100%;}
#div_loading_image .loading_container{
  display:inline-block;
  width:30%;
}
#div_loading_image .loading_container #div_loading_text{
  padding:10px; font-size:16px; font-weight:bold; font-family:Verdana, Geneva, sans-serif;
  background: #fff; /* For browsers that do not support gradients */
  background: -webkit-radial-gradient(#fff, #fff, transparent, transparent); /* Safari 5.1 to 6.0 */
  background: -o-radial-gradient(#fff, #fff, transparent, transparent); /* For Opera 11.6 to 12.0 */
  background: -moz-radial-gradient(#fff, #fff,transparent, transparent); /* For Firefox 3.6 to 15 */
  background: radial-gradient(#fff, #fff,transparent, transparent); /* Standard syntax */ 
}  
</style>
<?php } else { ?>
<style>
  #div_loading_image{z-index:99999!important; }
</style>
<?php } ?>

<div class="col-xs-12">

	<div id="divMedicineAllergie" style="position:absolute;top:200px;left:200px;visibility:hidden; z-index:1000;"></div>

  <form name="medications_form" id="medications_form" action="<?php echo $folder;?>/save.php" method="post">
  	<input type="hidden" name="info_alert" id="info_alert" value="<?php echo urlencode(serialize($medication->vocabulary)); ?>">
    <input type="hidden" name="preObjBack" id="preObjBack" value=""/>
    <input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $medication->current_tab;?>">
    <input type="hidden" name="next_tab" id="next_tab" value=""/>
    <input type="hidden" name="next_dir" id="next_dir" value=""/>
    <input type="hidden" name="buttons_to_show" id="buttons_to_show" value=""/>
    <input type="hidden" name="change_data" id="change_data" value="">
    <input type="hidden" name="hidMedIdVizChange" id="hidMedIdVizChange" value="">
    <input type="hidden" name="hidTotMedRow" id="hidTotMedRow" value="">
    <input type="hidden" name="callFrom" id="callFrom" value="<?php echo $_REQUEST["callFrom"];?>" />
    <input type="hidden" name="subcallFrom" id="subcallFrom" value="<?php echo $_REQUEST["subcall"]; ?>" />
    <input type="hidden" name="divH" id="divH" value="<?php echo $_REQUEST['divH'];?>" />
    <input type="hidden" name="cnt" id="cnt" value="" />
    <input type="hidden" name="prv_frmid" id="prv_frmid" value="<?php echo $_REQUEST["prv_frmid"]; ?>" />
   	
    <div>
    	
      <div <?php echo ($_REQUEST['callFrom'] == 'WV' ? 'style=" min-height:'.($_REQUEST["divH"]-30).'px; max-height:'.($_REQUEST["divH"]-30).'px;overflow:hidden; overflow-y:auto;"' : ''); ?> >
      
    	<div class="oculartop">
      	<div class="row">
        
        	<div class="col-lg-2 col-md-2 col-sm-12">
          	<div class="eyetst text-left" >
            <div class="checkbox checkbox-default margin_0">
            	<?php 
								$chk_change = ($_REQUEST["callFrom"] <> 'WV') ? 'chk_change(\''.$no_medication.'\',this,event);' : '';	
							?>
            	<input type="checkbox" name="commonNoMedications" id="commonNoMedications" value="NoMedications" <?php if(!$disableNoMed) { echo $no_medication; }?> <?php echo ($disableNoMed ? 'disabled' : '');?> onChange="<?php echo $chk_change;?> med_change_val(); setTxtArea(this.id); statusOfAllInputs();" >
              <label for="commonNoMedications"><b>No Medications</b></label>
           	</div>
            </div>
         	</div>
         	
          <div class="col-lg-10 col-md-10 col-sm-12 pt10">
          	<div class="row">
            
            	<div class="col-lg-5 col-md-5 col-sm-12 ">
								<div class="row">
									<div class="col-sm-3 text-right simple_hd"><span class="head valign_mid">Comments</span></div>	
									<div class="col-sm-9">
										<div class="row">
										<textarea class="form-control" name="comments" id="comments" rows="1" <?php if($no_medication <> 'checked') { echo "disabled"; } ?> ><?php if($disableNoMed == '0') { echo $comments; } ?></textarea>
										</div>
									</div>	
								</div>
            	</div>
                
              <div class="col-lg-4 col-md-4 col-sm-12 form-inline">
								<div class="row">
									<?php /*if($_REQUEST["subcall"] <> 'grid'):*/ ?>
									<div class="col-sm-2 text-right simple_hd">
										<span class="head valign_mid">FILTER</span>
									</div>
									<div class="col-sm-7">
										 <select name="searchby[]" id="searchby" class="selectpicker" multiple data-width="100%" title="Filter" data-actions-box="true" >
											<?php 
													if(is_array($searchOptions) && count($searchOptions) > 0 )
													{
														foreach($searchOptions as $key => $val)
														{
															if(empty($val)) continue;
															$sel = array_key_exists($key,$arrSearchSel) ? 'selected' : '';
															echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';	
														}
													}
											?>	
										</select>
									</div>
									<?php /*endif;*/ ?>
									<?php if($erx_patient_id > 0 && $Allow_erx_medicare == 'Yes'): ?>
									<div class="col-sm-3 text-center">
											<input type="button" name="btn" id="btn" onClick="open_dur();" value="DUR" class="btn btn-primary" />
									</div>
									<?php endif; ?>
								</div>
							</div>
            
            	<div class="col-lg-3 col-md-3 col-sm-12 ">
								
									<select name="sel_columns" id="sel_columns" data-page="medication" class="selectpicker" multiple data-width="100%" title="Select Columns" data-actions-box="true" >
										<?php
											foreach( $medication->default_columns as $key => $cStatus) {
												$selected = (in_array($key,$medication->columns)) ? 'selected' : '';
												echo '<option value="'.$key.'" '.$selected.' >'.str_replace("-"," ",$key).'</option>';
											}
										?>
									</select>
								
							</div>
            </div>
         	</div>
          
       	</div>
     	</div>
      
      <div class="clearfix"></div>
      
			<?php
        $i = 0;
        foreach($medical_data as $MEDICAL_DATA_MOD => $MEDICAL_DATA_VAL1)
        {
            $mod = $MEDICAL_DATA_MOD;
						
            if($MEDICAL_DATA_MOD == 'OCU')
            {
              $width = "150"; $width1 = "100";
              if($_REQUEST["callFrom"] == 'WV')
              {
                $width = "100px"; $width1 = "90px";
              }
              $modname = "Ocular Name";
            }
            else if($MEDICAL_DATA_MOD == 'SYS')
            {
              $width = "210"; $width1 = "200";
              $modname = "Systemic Name";
            }
            
            
            if($MEDICAL_DATA_MOD == 'OCU' || $MEDICAL_DATA_MOD == 'SYS')
            {
      ?>			<div class="table-responsive modules" id="mo">
      					<div id="select-container" style="position:absolute;"></div>
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr class="grythead">
                    	<td rowspan="2" align="center" width="170"><?php echo $modname; ?></td>
                      <td rowspan="2" align="center" width="60">Dosage</td>
                      <?php if($MEDICAL_DATA_MOD == 'OCU'): ?>
                      <td colspan="4" align="center" width="80">Site</td>
                      <?php endif; ?>
                      <td rowspan="2" align="center" width="<?php echo $width;?>" >Sig.</td>
                      <?php if($MEDICAL_DATA_MOD == 'SYS'): ?>
                      <td rowspan="2"="4" align="center" width="100">Route</td>
                      <?php endif; ?>
                      <td colspan="2" align="center" width="40" >Compliant</td>
                      <td rowspan="2" align="center" width="120" class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns) ? 'hide' : ''); ?>">Begin Date</td>
                      <td rowspan="2" align="center" width="60" class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns) ? 'hide' : ''); ?>">Time</td>
                      <td rowspan="2" align="center" width="120" class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns) ? 'hide' : ''); ?>">End Date</td>
                      <td rowspan="2" align="center" width="60" class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns) ? 'hide' : ''); ?>" >Time</td>
                     	<?php if( $mod == "OCU" ): ?>
                      <td rowspan="2" align="center" width="120" class="LastTakenDate <?php echo (!in_array('Last-Taken-Date',$medication->columns) ? 'hide' : ''); ?>">Last Taken Date</td>
                      <?php endif; ?>
                      <td rowspan="2" align="center" width="<?php echo $width1;?>">Comments</td>
                      <td rowspan="2" align="center" width="50" class="OrderedBy <?php echo (!in_array('Ordered-By',$medication->columns) ? 'hide' : ''); ?>">Ordered by </td>
                      <td rowspan="2" align="center" width="50">Status</td>
                      <td rowspan="2" align="center" width="100">RxNorm Code</td>
                      <td rowspan="2" align="center" width="30" <?php echo show_tooltip('Ocular','top'); ?>><img src="<?php echo $library_path; ?>/images/vision_problem.png" style="border:outset 1px #EFEFEF;" alt="VP"></td>
                      <?php if(isDssEnable()){ ?>
                        <td rowspan="2" align="center" width="50" class="Eligibility <?php //echo (!in_array('Eligibility',$medication->columns) ? 'hide' : ''); ?>">Service Eligibility<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top" title="Is this Medication is service connected eligibility?" data-container="body"></span></td>
                      <?php } ?>
                      <td rowspan="2" align="center" width="50" class="Refusal <?php echo (!in_array('Refusal',$medication->columns) ? 'hide' : ''); ?>">Refusal</td>
                     	<td rowspan="2" align="center" width="30" class="Hx <?php echo (!in_array('Hx',$medication->columns) ? 'hide' : ''); ?>">Hx</td>
                     	<td rowspan="2" align="center" width="30">Del</td>
                    </tr>
                  
                    <tr class="grythead">
                      <?php if($MEDICAL_DATA_MOD == 'OCU'): ?>
                      <td align="center" width="20">OU</td>
                      <td align="center" width="20">OD</td>
                      <td align="center" width="20">OS </td>
                      <td align="center" width="20">PO</td>
                      <?php endif;?>
                      <td align="center" width="20">Yes</td>
                      <td align="center" width="20">No</td>
                    </tr>
                  </thead>
                  <tbody id="medication_table_<?php echo strtolower($mod);?>">
                    <?php
											
                      foreach( $MEDICAL_DATA_VAL1 as $MEDICAL_DATA_KEY => $MEDICAL_DATA_VAL)
                      {
												$MEDICAL_DATA_KEY = $i;
												if( !$MEDICAL_DATA_VAL['DEL_STATUS'])
                        {
                          if($MEDICAL_DATA_MOD == 'OCU')	$type = "4";
                          else if($MEDICAL_DATA_MOD == 'SYS')	$type = "1";
                    ?>
                    
                    			<tr id="tbl_md_row_<?php echo $i;?>" <?php echo ($MEDICAL_DATA_VAL['DIS_STATUS']) ? 'class="bg bg-orange"' : '';?> >
                      <!-- Title Column -->
                      <td>
                        <input type="hidden" name="med_type<?php echo $MEDICAL_DATA_KEY;?>" id="med_type<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $type;?>">
                        <input type="hidden" name="med_tw_ddi<?php echo $MEDICAL_DATA_KEY;?>" id="med_tw_ddi<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['MED_TW_DDI']; ?>" />
                        <input type="hidden" name="med_tw_id<?php echo $MEDICAL_DATA_KEY;?>" id="med_tw_id<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['MED_TW_ID']; ?>" />

                        <input type="hidden" name="med_id<?php echo $MEDICAL_DATA_KEY;?>" id="med_id<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $MEDICAL_DATA_VAL['MED_ID'];?>">
                        <!-- onKeyDown="indexEntCheck();" -->
                        <?php if($MEDICAL_DATA_VAL['MED_ID']) { ?>
                        <div class="input-group">
                        <?php } ?>
                        	<input type="text" class="form-control" style=" <?php echo $MEDICAL_DATA_VAL['STYLE'];?>" id="textTitle<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_medication<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $MEDICAL_DATA_VAL['MED_TITLE'];?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') { ?>chk_change('<?php addslashes($MEDICAL_DATA_VAL['MED_TITLE']);?>',this,event);<?php } ?> insertMedIdVizChange('<?php addslashes($MEDICAL_DATA_VAL['MED_TITLE']);?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));"  onMouseDown="addNewMedicine(event,this);" oncontextmenu="return false" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php addslashes($MEDICAL_DATA_VAL['MED_TITLE']);?>',this,event);<?php } ?> setCBKOcular(this,'md_occular<?php echo $MEDICAL_DATA_KEY;?>'); med_change_val();" onfocus="check_umls(this,<?php echo $MEDICAL_DATA_KEY;?>)">
                          <?php if($MEDICAL_DATA_VAL['MED_TITLE'] <> ''): ?>
                        	<label class="input-group-addon btn" data-toggle="tooltip" data-placement="bottom" title="Info Button">
                          	<i class="glyphicon glyphicon-info-sign" id='info_prob_<?php echo $i; ?>' onclick='javascript: var medInfoWin = window.open("http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=<?php echo $MEDICAL_DATA_VAL['medInfoButtonCode'];?>&mainSearchCriteria.v.cs=2.16.840.1.113883.6.88&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","ProblemList","height=700,width=1000,top=50,left=50,scrollbars=yes");medInfoWin.focus();'></i>
                         	</label>
                          <?php endif; ?>
                       	<?php if($MEDICAL_DATA_VAL['MED_ID']) { ?>
                       	</div>   
                        <?php } ?>
                     	</td>
                      <!-- Dosage -->
                      <td>
                        <input type="text" id="md_dosage<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_dosage<?php echo $MEDICAL_DATA_KEY;?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_DEST']); ?>',this,event);<?php }?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_DEST']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" value="<?php echo $MEDICAL_DATA_VAL['MED_DEST']; ?>" class="form-control" onKeyDown="indexEnt();" onChange="med_change_val();">
                      </td>
                      
                      <!-- Sites -->
                      <?php if($mod == "OCU"): ?>
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="md_occular<?php echo $MEDICAL_DATA_KEY;?>" id="md_ou<?php echo $MEDICAL_DATA_KEY;?>" value="3" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_SITE'] == '3') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('md_od<?php echo $MEDICAL_DATA_KEY;?>', 'md_os<?php echo $MEDICAL_DATA_KEY;?>', 'md_po<?php echo $MEDICAL_DATA_KEY;?>')" >
                          <label for="md_ou<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="md_occular<?php echo $MEDICAL_DATA_KEY;?>" id="md_od<?php echo $MEDICAL_DATA_KEY;?>" value="2" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_SITE'] == '2') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event);<?php } ?>insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('md_os<?php echo $MEDICAL_DATA_KEY;?>', 'md_ou<?php echo $MEDICAL_DATA_KEY;?>', 'md_po<?php echo $MEDICAL_DATA_KEY;?>');" >
                          <label for="md_od<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="md_occular<?php echo $MEDICAL_DATA_KEY;?>" id="md_os<?php echo $MEDICAL_DATA_KEY;?>" value="1" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_SITE'] == '1') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('md_od<?php echo $MEDICAL_DATA_KEY;?>', 'md_ou<?php echo $MEDICAL_DATA_KEY;?>', 'md_po<?php echo $MEDICAL_DATA_KEY;?>');" >
                          <label for="md_os<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="md_occular<?php echo $MEDICAL_DATA_KEY;?>" id="md_po<?php echo $MEDICAL_DATA_KEY;?>" value="4" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_SITE'] == '4') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SITE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('md_od<?php echo $MEDICAL_DATA_KEY;?>', 'md_ou<?php echo $MEDICAL_DATA_KEY;?>', 'md_os<?php echo $MEDICAL_DATA_KEY;?>');" />
                          <label for="md_po<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>   
                      </td>
                      <?php endif; ?>
                      
                      <!-- Sig. -->
                      <td>
                        <textarea class="form-control" id="md_sig<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_sig<?php echo $MEDICAL_DATA_KEY;?>" onKeyDown="indexEnt();" onChange="med_change_val();" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SIG']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SIG']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" rows="1" onMouseDown="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SIG']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_SIG']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" title="<?php echo $MEDICAL_DATA_VAL['MED_SIG'];?>"><?php echo $MEDICAL_DATA_VAL['MED_SIG'];?></textarea>	
                      </td>
                      <!-- Route -->
                      <?php if($mod == "SYS"): ?>
                      <td>
                      	<select class="form-control minimal" data-width="70px" data-size="10" name="md_route<?php echo $MEDICAL_DATA_KEY;?>" id="md_route<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?> med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" title="Select" data-container="#select-container" >
                          <?php echo $MEDICAL_DATA_VAL['MED_ROUTE']; ?>
                        </select>
                    	</td>
                      <?php endif; ?>
                      
                      <!-- Compliant YES -->
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="compliant<?php echo $MEDICAL_DATA_KEY;?>" id="comp_yes<?php echo $MEDICAL_DATA_KEY;?>" value="1" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_COMPLIANT'] == '1') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMPLIANT']); ?>',this,event);<?php } ?>insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMPLIANT']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('comp_no<?php echo $MEDICAL_DATA_KEY;?>');" >
                          <label for="comp_yes<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>
                      </td>
                      
                      <!-- Compliant No -->
                      <td class="text-center">
                        <div class="radio">
                          <input type="radio" name="compliant<?php echo $MEDICAL_DATA_KEY;?>" id="comp_no<?php echo $MEDICAL_DATA_KEY;?>" value="0" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" <?php if($MEDICAL_DATA_VAL['MED_COMPLIANT'] == '0') echo 'checked'; ?> onClick="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMPLIANT']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMPLIANT']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); chkBoxSetting('comp_yes<?php echo $MEDICAL_DATA_KEY;?>');" >
                          <label for="comp_no<?php echo $MEDICAL_DATA_KEY;?>" ></label>
                        </div>
                      </td>
                      
                      <!-- Begin Date -->
                      <td class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns) ? 'hide' : ''); ?>" >
                      	<div class="input-group">
                        	<input type="text" class="datepicker form-control" id="md_begindate<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_begindate<?php echo $MEDICAL_DATA_KEY;?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event);<?php } ?>insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); med_change_val();" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); med_change_val();"  value="<?php echo $MEDICAL_DATA_VAL['MED_BEG_DATE']; ?>" onKeyDown="indexEnt();" maxlength="10" title="<?php echo inter_date_format(); ?>" onBlur="checkdate(this); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_BEG_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); med_change_val();" >
                          <label for="md_begindate<?php echo $MEDICAL_DATA_KEY;?>" class="input-group-addon btn">
                          	<i class="glyphicon glyphicon-calendar "></i>
                          </label>
                      	</div>    
                      </td>
                      
                      <td class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns) ? 'hide' : ''); ?>" >
                      	
                        	<input type="text" class="form-control" name="md_begtime<?php echo $MEDICAL_DATA_KEY; ?>" id="md_begtime<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['MED_BEG_TIME']; ?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));">
                      	  
                      </td>
                      
                      <!-- End Date -->
                     	<td class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns) ? 'hide' : ''); ?>" >
                      	<div class="input-group">
                        	<input type="text" class="datepicker form-control" id="md_enddate<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_enddate<?php echo $MEDICAL_DATA_KEY;?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" onChange="insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event);<?php } ?> med_change_val();" value="<?php echo $MEDICAL_DATA_VAL['MED_END_DATE']; ?>" maxlength="10" onKeyDown="indexEnt();"  title="<?php echo inter_date_format();?>" onBlur="checkdate(this); insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_END_DATE']); ?>',this,event);<?php } ?> med_change_val();" >
                          <label for="md_enddate<?php echo $MEDICAL_DATA_KEY;?>" class="input-group-addon btn">
                          	<i class="glyphicon glyphicon-calendar "></i>
                          </label>
                      	</div>    
                      </td>
                      
                      <td class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns) ? 'hide' : ''); ?>" >
                        	<input type="text" class="form-control" name="md_endtime<?php echo $MEDICAL_DATA_KEY; ?>" id="md_endtime<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['MED_END_TIME']; ?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));">
                      	 
                      </td>
                     
                      <!-- Last Taken Date -->
                      <?php if( $mod == "OCU"): ?>
                      <td class="LastTakenDate <?php echo (!in_array('Last-Taken-Date',$medication->columns) ? 'hide' : ''); ?>" >
                      	<div class="input-group">
                        	<input type="text" class="datetimepicker form-control" id="md_lasttakendate<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="md_lasttakendate<?php echo $MEDICAL_DATA_KEY;?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" onChange="insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event);<?php } ?> med_change_val();" value="<?php echo $MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']; ?>" onKeyDown="indexEnt();"  title="<?php echo inter_date_format(); ?>" onBlur="checkdatetime(this); insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_LASTTAKEN_DATE']); ?>',this,event);<?php } ?> med_change_val();" >
                          <label for="md_lasttakendate<?php echo $MEDICAL_DATA_KEY;?>" class="input-group-addon btn">
                          	<i class="glyphicon glyphicon-calendar "></i>
                          </label>
                     		</div>     
                      </td>
                      <?php endif; ?>
                      
                      <!-- Comments -->
                      <td>
                        <textarea id="med_comments<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="med_comments<?php echo $MEDICAL_DATA_KEY;?>" onKeyUp="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMMENTS']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMMENTS']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" class="form-control" onKeyDown="indexEnt();" onChange="med_change_val();" rows="1" onMouseDown="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMMENTS']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_COMMENTS']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));"><?php echo $MEDICAL_DATA_VAL['MED_COMMENTS']; ?></textarea> 	
                      </td>
                      
                      <!-- Ordered By -->
                      <td class="OrderedBy <?php echo (!in_array('Ordered-By',$medication->columns) ? 'hide' : ''); ?>" >
                        <select class="form-control minimal" data-width="70px" data-size="10" name="md_prescribedby<?php echo $MEDICAL_DATA_KEY;?>" id="md_prescribedby<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?> med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" title="Select" data-container="#select-container" >
                          <?php echo $MEDICAL_DATA_VAL['MED_PREFERED_BY']; ?>
                        </select>
                      </td>
                      
                      <!-- Status -->
                      <td>
                        <select class="form-control minimal" data-width="90px" name="cbMedicationStatus<?php echo $MEDICAL_DATA_KEY;?>" id="cbMedicationStatus<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" data-container="#select-container">
                          <?php echo $MEDICAL_DATA_VAL['STATUS']; ?>
                        </select>
                      </td>
                      
                 			<!-- Rx Nome Code -->
                      <td>
                        <input type="hidden" id="ccda_code_hidd<?php echo $MEDICAL_DATA_KEY;?>" name="ccda_code_hidd<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $MEDICAL_DATA_VAL['ccda_code'];?>">
                        <input type="text" id="ccda_code<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="ccda_code<?php echo $MEDICAL_DATA_KEY;?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_ccda_code']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_ccda_code']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); med_change_val();"  class="form-control" onKeyDown="indexEnt();" value="<?php echo $MEDICAL_DATA_VAL['medInfoButtonCode']; ?>">
                        <input type="hidden" id="fdb_id<?php echo $MEDICAL_DATA_KEY;?>" tabindex="<?php echo $MEDICAL_DATA_KEY;?>" name="fdb_id<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $MEDICAL_DATA_VAL['MED_fdb_id']; ?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('<?php echo addslashes($MEDICAL_DATA_VAL['MED_fdb_id']); ?>',this,event);<?php } ?> insertMedIdVizChange('<?php echo addslashes($MEDICAL_DATA_VAL['MED_fdb_id']); ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>')); med_change_val();">	
                      </td>
                      
                      <!-- Vision Problem -->
                      <td class="text-center">
                        <div class="checkbox">
                          <input type="checkbox" id="ocular_med_chkbox_<?php echo $MEDICAL_DATA_KEY;?>" name="ocular_med_chkbox_<?php echo $MEDICAL_DATA_KEY;?>" title="Ocular" <?php if($type == '4') echo 'checked'; ?> value="1" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change(<?php echo ($type == '4' ? "'true'" :"'false'");?>,this,event);<?php } ?> insertMedIdVizChange(<?php echo ($type == '4' ? "'true'" : "'false'");?>,this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));">
                          <label for="ocular_med_chkbox_<?php echo $MEDICAL_DATA_KEY;?>"></label>
                        </div>
                      </td>
                        <?php if(isDssEnable()){ ?>
                            <td class="text-center Eligibility <?php //echo (!in_array('Eligibility',$medication->columns) ? 'hide' : ''); ?>" >
                                <!-- <div class="checkbox">
                                    <input type="checkbox" class="" name="service_eligibility<?php echo $MEDICAL_DATA_KEY; ?>" id="service_eligibility<?php echo $MEDICAL_DATA_KEY; ?>" <?php if ($MEDICAL_DATA_VAL['service_eligibility'] == 1) echo 'checked'; ?> onChange="dss_value_change(this);<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('<?php if ($MEDICAL_DATA_VAL['service_eligibility'] == 1) echo 'checked'; ?>',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" value="<?php echo $MEDICAL_DATA_VAL['service_eligibility']; ?>">
                                    <label for="service_eligibility<?php echo $MEDICAL_DATA_KEY; ?>">&nbsp;</label>
                                </div> -->
                                <span class="glyphicon glyphicon-question-sign" onclick="top.dssLoadServiceConnectedOpt('medication','<?php echo $MEDICAL_DATA_VAL['MED_ID'];?>')"></span>
                            </td>
                        <?php } ?>
											<!-- Refusal -->
											<td class="text-center Refusal <?php echo (!in_array('Refusal',$medication->columns) ? 'hide' : ''); ?>" >
												<div class="checkbox">
													<input type="checkbox" class="checkbox" name="refusal<?php echo $MEDICAL_DATA_KEY; ?>" id="refusal<?php echo $MEDICAL_DATA_KEY; ?>" <?php if ($MEDICAL_DATA_VAL['REFUSAL'] == 1) echo 'checked'; ?> onChange="check_refusal(<?php echo $MEDICAL_DATA_KEY; ?>); <?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));" value="<?php echo $MEDICAL_DATA_VAL['REFUSAL']; ?>">
													<label for="refusal<?php echo $MEDICAL_DATA_KEY; ?>">&nbsp;</label>
												</div>
											</td>

											<input type="hidden" name="refusal_reason<?php echo $MEDICAL_DATA_KEY; ?>" id="refusal_reason<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['REFUSAL_REASON']; ?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));">
											<input type="hidden" name="refusal_snomed<?php echo $MEDICAL_DATA_KEY; ?>" id="refusal_snomed<?php echo $MEDICAL_DATA_KEY; ?>" value="<?php echo $MEDICAL_DATA_VAL['REFUSAL_SNOMED']; ?>" onChange="<?php if( $_REQUEST["callFrom"] <> 'WV') {  ?>chk_change('',this,event);<?php } ?>med_change_val(); insertMedIdVizChange('',this,event, document.getElementById('med_id<?php echo $MEDICAL_DATA_KEY;?>'));">	
											
											<!-- History -->
                     	<td class="Hx <?php echo (!in_array('Hx',$medication->columns) ? 'hide' : ''); ?>" >
                        <img src="<?php echo $library_path; ?>/images/search.png" alt="Hx" class="pointer" onClick="med_history_records('','<?php echo $MEDICAL_DATA_VAL['MED_ID'] ?>')" />
                    	</td>
											
											<!-- Delete -->
                      <td>
                      <?php
												
												$cnt1 = count($medical_data['OCU']);
												$cnt2 = count($medical_data['SYS']);
												$totalCnt = $cnt1+$cnt2 ;
												
												if($MEDICAL_DATA_KEY == $cnt1-1 || $MEDICAL_DATA_KEY ==  $totalCnt-1)
												{
													echo '<span class="glyphicon glyphicon-plus" id="add_row_'.$i.'" class="pointer" alt="Add More" onClick="addNewRow(\''.$i.'\',\''.$type.'\');"></span>';
												}
												else
												{
													echo '<span class="glyphicon glyphicon-remove pointer" alt="Delete Row" onClick="removeTableRow(\''.$MEDICAL_DATA_VAL['MED_ID'].'\',\''.$MEDICAL_DATA_KEY.'\');"></span>';
												}
											?>
                     	</td>
                    </tr>
                    
										<?php
                        }
                        else
                        {
                    ?>			
                    			<tr id="tbl_md_row_<?php echo $i;?>" <?php echo ($MEDICAL_DATA_VAL['DIS_STATUS']) ? 'class="bg bg-orange"': 'style="text-decoration:line-through; color:#f00;"';?> >
                      <!-- Title Column -->
                      <td>
                        <input type="hidden" name="med_id<?php echo $MEDICAL_DATA_KEY;?>" id="med_id<?php echo $MEDICAL_DATA_KEY;?>" value="<?php echo $MEDICAL_DATA_VAL['MED_ID'];?>">
                            <?php echo $MEDICAL_DATA_VAL['MED_TITLE']; ?>
                      </td>
                      <!-- Dosage -->
                      <td><?php echo $MEDICAL_DATA_VAL['MED_DEST']; ?></td>
                      
                      <!-- Sites -->
                      <?php if($mod == "OCU"): ?>
                      <td colspan="4">&nbsp;</td>
                      <?php endif; ?>
                      
                      <!-- Sig. -->
                      <td><?php echo $MEDICAL_DATA_VAL['MED_SIG']; ?></td>
                      
                      <!-- Route -->
                      <?php if( $mod == "SYS"){ ?>
                      <td><?php echo $MEDICAL_DATA_VAL['MED_ROUTE_VAL']; ?></td>
                      <?php } ?>  
                      <!-- Compliant YES / NO -->
                      <td colspan="2">
                        <?php
                        	echo ($MEDICAL_DATA_VAL['MED_COMPLIANT'] == '1' || $MEDICAL_DATA_VAL['MED_COMPLIANT'] == '') ? 'Yes' : 'No';
												?>	
                      </td>
                      
                      <!-- Begin Date -->
                      <td class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns)?'hide':'');?>"><?php echo $MEDICAL_DATA_VAL['MED_BEG_DATE']; ?></td>
                      <td class="BeginDateTime <?php echo (!in_array('Begin-Date-Time',$medication->columns)?'hide':'');?>"><?php echo $MEDICAL_DATA_VAL['MED_BEG_TIME']; ?></td>
                      
                      <!-- End Date -->
                      <td class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns)?'hide':'');?>"><?php echo $MEDICAL_DATA_VAL['MED_END_DATE']; ?></td>
                      <td class="EndDateTime <?php echo (!in_array('End-Date-Time',$medication->columns)?'hide':'');?>"><?php echo $MEDICAL_DATA_VAL['MED_END_TIME']; ?></td>
                      
                      <!-- Last Taken Date -->
                      <?php if( $mod == "OCU"): ?>
                      <td class="LastTakenDate <?php echo (!in_array('Last-Taken-Date',$medication->columns)?'hide':'');?>">&nbsp;</td>
                      <?php endif; ?>
                      
                      <!-- Comments -->
                      <td>&nbsp;</td>
                      
                      <!-- Ordered By -->
                      <td class="OrderedBy <?php echo (!in_array('Ordered-By',$medication->columns)?'hide':'');?>">&nbsp;</td>
                      
                      <!-- Status -->
                      <td>Deleted</td>
                      
                      <!-- Rx Nome Code -->
                      <td>&nbsp;</td>
                      
                      <!-- Vision Problem -->
                      <td class="text-center">&nbsp;</td>

                      <!-- Refusal -->
                      <td class="Refusal <?php echo (!in_array('Refusal',$medication->columns)?'hide':'');?>">&nbsp;</td>
                      
                      <!-- History -->
                      <td class="Hx <?php echo (!in_array('Hx',$medication->columns)?'hide':'');?>">
                        <img src="<?php echo $library_path; ?>/images/search.png" alt="Hx" class="pointer" onClick="med_history_records('','<?php echo $MEDICAL_DATA_VAL['MED_ID'] ?>')">
                        
                      </td>
                      
                      <!-- Delete -->
                      <td>&nbsp;</td>
                    </tr>							
                       
                    <?php		
                        }
						//--- AUDIT TRAIL VARIABLES RAVI MANTRA -- 
						if(empty($MEDICAL_DATA_VAL['MED_TITLE']) == false){
							$pkIdAuditTrail .= $MEDICAL_DATA_VAL['MED_ID']."-";
							if(empty($pkIdAuditTrailID) == true){		
								$pkIdAuditTrailID = $MEDICAL_DATA_VAL['MED_ID'];
							}
						}
											
                       	$i++;
                      }
                    ?>	
                    
                  </tbody>
                </table>
              </div>
              <div class="clearfix"></div>
      <?php
            }
      		 
        }
      ?>
		<div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="modal_title">Refusal Reason</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" name="refusal_row" id="refusal_row" value="" >
            <input type="hidden" name="rowID" id="rowID" value="" >
            <div class="form-group">
              <label for="usrname">Refusal Reason</label>
              <textarea type="text" class="form-control" id="refusal_reason" name="refusal_reason"></textarea>
            </div>
            <div class="form-group">
              <label for="psw">Refusal Snomed</label>
              <input type="text" class="form-control" id="refusal_snomed" name="refusal_snomed">
            </div>
          </div>
          <div id="module_buttons" class="ad_modal_footer modal-footer">
            <button type="button" class="btn btn-success" onclick="check_refusal_values();">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<!-- TW medication display Modal -->
    <div class="modal fade" id="medsModal" role="dialog">
			<div class="modal-dialog modal-lg">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">TW Medication Records</h4>
					</div>
					<div class="modal-body" id="twModalContent">
						
				</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="button" class="btn btn-success" onclick="top.fmain.medications_form.submit();">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
<!-- End TW medication display Modal -->

   		<input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $i;?>">
      </div>
      	 
      <?php if($_REQUEST["callFrom"] == 'WV'): ?>
      	<div class="panel-footer ad_modal_footer" id="module_buttons">
      		<input type="button" id="btSaveMedication" name="btSaveMedication" class="btn btn-success" value="Done" onClick="fn_ocu_site_chk()"/>
          <input type="button" id="btClose" name="btClose" class="btn btn-danger" value="Cancel" onClick="window.close();" />
	  <input type="hidden" id="prv_frmid" name="prv_frmid"  value="<?php echo $_REQUEST["prv_frmid"]; ?>"  />
       	</div>
     	<?php endif; ?>
   	
    </div>	
</form>
  
<!-- Admin Medication Modal -->
<div id="admin_medications_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Medications Type Ahead
        	<span class="form-inline" style="margin-left:20px;">
          	<input type="hidden" name="hidd_click_field_id" id="hidd_click_field_id" />
            <input type="text" name="medicineName" placeholder="Add Medicine" class="form-control" id="medicineName" data-action="admin_medicine" data-sub-action="insert" data-record-id="0" onKeyUp="filter_table(this.value,'admin_med_table');" style="width:300px;" />
            <button type="button" class="btn btn-success" id="medicineNameSave">Save</button>
        	</span>
        </h4>
     	</div>
      
      <div class="modal-body" id="admin_medications_data" style=" min-height:400px; max-height:400px; overflow:hidden; overflow-y:scroll; ">
      	<?php 
					echo $medication->admin_medicine_data();
				?>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Med External Records Modal -->
<div id="med_external_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">External Records
        	
        </h4>
     	</div>
      <div class="modal-body" id="med_external_data" style=" min-height:400px; max-height:400px; overflow:hidden; overflow-y:scroll; ">
      	<?php 
					echo $medication->med_external();
				?>
     	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
  
<!-- Med History Records Modal -->
<div id="med_history_modal" class="modal" role="dialog">
	<div class="modal-dialog">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Changes History
        	
        </h4>
     	</div>
      <div class="modal-body" id="med_history_data" style=" min-height:400px; max-height:400px; overflow:hidden; overflow-y:scroll; ">
     	</div>
       <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>  

<!-- Check For UMLS Modal -->
<div id="med_umls_fdb_modal" class="modal" role="dialog">
	<div class="modal-dialog">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Changes History
        	
        </h4>
     	</div>
      <div class="modal-body" style="min-height:450px; max-height:450px; overflow:hidden; overflow-y:scroll; ">
      	<div class="row">
        	<div class="col-xs-12 ">
          	<div class="headinghd"><h4>UMLS Results</h4></div>
          </div>
          <div class="col-xs-12" id="umls_content" style="max-height:150px; min-height:150px; overflow:hidden; overflow-y:auto;"></div>
        </div>
        
        <div class="row mt20">
        	<div class="col-xs-12 ">
          	<div class="headinghd"><h4>FDB Results</h4></div>
          </div>
          <div class="col-xs-12" id="fdb_content" style="max-height:150px; min-height:150px; overflow:hidden; overflow-y:auto;"></div>
        </div>
     	</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<?php
		$pid = $_SESSION["patient"];
		//--- GET POLICY STATUS FOR AUDIT TRAIL ----
		$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

				
		#---------------------------
		# Audit trail for view only
		#---------------------------

		if($policyStatus == 1 and $pkIdAuditTrailID != '' and isset($_SESSION['Patient_Viewed']) === true){
			$opreaterId = $_SESSION["authId"];											 
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$arrAuditTrailView_Medication = array();
			$arrAuditTrailView_Medication[0]['Pk_Id'] = $pkIdAuditTrailID;
			$arrAuditTrailView_Medication[0]['Table_Name'] = 'lists';
			$arrAuditTrailView_Medication[0]['Action'] = 'view';
			$arrAuditTrailView_Medication[0]['Operater_Id'] = $_SESSION['authId'];
			$arrAuditTrailView_Medication[0]['Operater_Type'] = getOperaterType($opreaterId);
			$arrAuditTrailView_Medication[0]['IP'] = $ip;
			$arrAuditTrailView_Medication[0]['MAC_Address'] = $_REQUEST['macaddrs'];
			$arrAuditTrailView_Medication[0]['URL'] = $URL;
			$arrAuditTrailView_Medication[0]['Browser_Type'] = $browserName;
			$arrAuditTrailView_Medication[0]['OS'] = $os;
			$arrAuditTrailView_Medication[0]['Machine_Name'] = $machineName;
			$arrAuditTrailView_Medication[0]['Category'] = 'patient_info-medical_history';
			$arrAuditTrailView_Medication[0]['Filed_Label'] = 'Medication Data';
			$arrAuditTrailView_Medication[0]['Category_Desc'] = 'medication';
			$arrAuditTrailView_Medication[0]['Old_Value'] = $pkIdAuditTrail;
			$arrAuditTrailView_Medication[0]['pid'] = $pid;
		
			$patientViewed = $_SESSION['Patient_Viewed'];
			if(is_array($patientViewed) && $patientViewed["Medical History"]["Medications"] == 0){
				auditTrail($arrAuditTrailView_Medication,$mergedArray,0,0,0);
				$patientViewed["Medical History"]["Medications"] = 1;
				$_SESSION['Patient_Viewed'] = $patientViewed;
			}
		}
		
		// *** CLS Alerts Calls ***
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel")
		{
			require_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");	
			$OBJPatSpecificAlert = new CLSAlerts();		
			$alertToDisplayAt="admin_specific_chart_note_med_hx";	
			echo $getAdminAlert = $OBJPatSpecificAlert->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");	
			$alertToDisplayAt="patient_specific_chart_note_med_hx";	
			echo $getPatSpecificAlert = $OBJPatSpecificAlert->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");	
			echo $autoSetDivLeftMargin = $OBJPatSpecificAlert->autoSetDivLeftMargin("140","265");
			echo $autoSetDivTopMargin = $OBJPatSpecificAlert->autoSetDivTopMargin("250","30");	
			echo $writeJS = $OBJPatSpecificAlert->writeJS();
		}
		
	?>
    <script>var isDssEnable='<?php echo isDssEnable(); ?>';</script>
  <script type="text/javascript" src="<?php echo $library_path;?>/js/medications.js"></script>
  
  <script>
		document.getElementById('cnt').value = '<?php echo $i;?>';
		/*ty_med_title = <?php echo json_encode($medicationTitleArr); ?>;
		ty_ccda_code = <?php echo json_encode($medication_ccdacode_Arr); ?>;
		ty_doses_code = <?php echo json_encode($medication_doses_Arr); ?>;
		ty_sig_code = <?php echo json_encode($medication_sig_Arr); ?>;
		ty_fbd_id = <?php echo json_encode($fdb_id_arr); ?>;	*/
		var phyOptions = <?php echo json_encode($MEDICAL_DATA_VAL['MED_PREFERED_BY']); ?>;
		var routeOptions = <?php echo json_encode($MEDICAL_DATA_VAL['MED_ROUTE']); ?>;
		var callFrom = document.getElementById('callFrom').value;
		
		$(function(){
			
			$("#commonNoMedications").triggerHandler('change');
			$('body').on('click','#medicineNameSave',function(){
				
				var _this = $(this);
				var vf_obj = $("#medicineName"); 
				var v = vf_obj.val().trim();
				
				if(v === ''){
					top.fAlert('Please type medicine name');
					return false;
				}
				
				var p = vf_obj.data();
				var d = '';
				$.each(p,function(i,v){  d	+= '&' + i + '=' + v; });
				d += '&mName='+v;
				d = d.substr(1);
				
				var url = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php';
				
				$.ajax({
					url : url,
					type:'POST',
					dataType:"json",
					beforeSend: function(){
						_this.html('<i class="glyphicon glyphicon-repeat slow-right-spinner"></i>&nbsp;Please wait...')
					},
					complete: function(){ _this.html('Save'); },
					data : d,
					success: function(r)
					{
						if(r.result === 2)
							top.fAlert('Record already exists');
						else if(r.result === 1)
						{
							if(r.action == 'update')
							{
								var g = '';
								vf_obj.data('sub-action','insert').data('record-id',0);
								vf_obj.val(g);
								$("#medicineNameSave").html('Save');
							}
							$("#"+$("#hidd_click_field_id").val()).val(v);
							$("#admin_medications_data").html(r.data);
							$("#admin_medications_modal").modal('hide');
							document.oncontextmenu = new Function("return true");
							
							medicine_typeahead('refresh');
						}
						else
						{
							top.fAlert('Something went wrong....Please try again...!!!');
						}
					}
				});
				
			});
			
			$('body').on('click','.set-medicine',function(){
				var m = $(this).data('medicine-name');
				var f = $("#hidd_click_field_id").val();
				var tmp = $("#"+f).val();
				$("#"+f).val(m);
				chk_change(tmp,$("#"+f)[0]);
				med_change_val();
				$("#admin_medications_modal").modal('hide');
				document.oncontextmenu = new Function("return true");	
			});
			
			$('body').on('click','.edit-medicine',function(){
				var vf_obj = $("#medicineName");
				var r = $(this).data('record-id');
				var m = $(this).data('medicine-name');
				vf_obj.data('sub-action','update').data('record-id',r).val(m);
				$("#medicineNameSave").html('Update');
				vf_obj.focus();
			});
			
			$('body').on('click','.del-medicine',function(){
				var r = 	$(this).data('record-id');
				r = parseInt(r);
				
				if(r > 0 ) {
					var func = 'top.fmain.del_admin_medicine('+r+')';
					if( callFrom == 'WV' ) var func = 'top.del_admin_medicine('+r+')'
					top.fancyConfirm('<?php echo imw_msg('del_rec');?>','',func,false);
				}
				
				return false;	
			});
			
			if(callFrom != 'WV')	top.btn_show("MED");
			top.show_loading_image("hide");
			medicine_typeahead('refresh');	
			
		});
		

<?php if( is_allscripts()): ?>

/*Saving Data to TW, Track modified/new records*/
var asSearchMeds = {};
$(document).ready(function(){


  $('#medications_form').on('change', 'input[name^="md_medication"]', function(){

    var elemId = $(this).attr('id');
    var elemIndex = elemId.replace(/.*?([0-9]*)$/g, '$1');
    var medValue = $(this).val();
    medValue = $.trim(medValue);

    if(medValue === '')
    {
      if( typeof asSearchMeds[elemIndex] !== undefined)
        delete asSearchMeds[elemIndex];

      return true;
    }
    
    asSearchMeds[elemIndex] = medValue;
  });


  $('body').on('change', 'input.twMedSelection[type="radio"]', function(){

    var radiodId = $(this).attr('id');
    var regex = /\D*?([0-9])_([0-9])/g;
    ids = regex.exec(radiodId);
console.log($('#twMed'+ids[2]));
    $('#textTitle'+ids[1]).val($('#twMed'+ids[2]).text());
    $('#med_tw_ddi'+ids[1]).val($('#twDdi'+ids[2]).text());


    // console.log(ids);

  });

});

/*Search Allscripts and prevent medications form sublimt*/
function searchMedsTW(){

  $('input:hidden[id^="med_tw_id"][value=""]').each(function(index, object){

    elemId = $(object).attr('id');
    elemIndex = elemId.replace(/.*?([0-9]*)$/g, '$1');

    $('#textTitle'+elemIndex).trigger('change');
  });

  if( Object.keys(asSearchMeds).length > 0)
  {
    /*Send search data to ajax handler to query from TW*/
    var url = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/ajax/ajax_handler.php';
    var reqMeds = {action:'search_tw_medication', medNames:asSearchMeds}; /*Requested meds to search*/

    $.ajax({
      url : url,
      type:'POST',
      data: reqMeds,
      complete: function(respObj, status){
        var medicationResp = $.parseJSON(respObj.responseText);
        medicationResp = medicationResp.medsData;

        $('#twModalContent').empty();

        //console.log(medicationResp);
        var tempHtml = '';

        $.each(medicationResp, function(key, vals){
          tempHtml += '<div class="twMedTitle">'+asSearchMeds[key]+'</div>';
          tempHtml += '<div class="twMedsDetails">';
            tempHtml += '<table class="table table-bordered table-hover table-striped scroll release-table">';

              tempHtml += '<thead> ';
                tempHtml += '<tr>';
                  tempHtml += '<td>#</td>';
                  tempHtml += '<td>Mecation</td>';
                  tempHtml += '<td>DDI</td>';
                  tempHtml += '<td>GPI</td>';
                  tempHtml += '<td>MedForm</td>';
                  tempHtml += '<td>MedDictDe</td>';
                  tempHtml += '<td>ControlSubstanceCoce</td>';
                  tempHtml += '<td>KDC</td>';
                  tempHtml += '<td>FormularyStatus</td>';
                tempHtml += '</tr>';
              tempHtml += '</thead><tbody>';

          $.each(vals, function(key1, vals1){
            tempHtml += '<tr>';
              tempHtml += '<td><input class="twMedSelection" type="radio" name="tw'+key+'"" id="key'+key+'_'+key1+'" /></td>';
              tempHtml += '<td id="twMed'+key1+'">'+vals1.Medname+'</td>';
              tempHtml += '<td id="twDdi'+key1+'">'+vals1.DDI+'</td>';
              tempHtml += '<td>'+vals1.GPI+'</td>';
              tempHtml += '<td>'+vals1.MedForm+'</td>';
              tempHtml += '<td>'+vals1.MedDictDE+'</td>';
              tempHtml += '<td>'+vals1.ControlSubstanceCode+'</td>';
              tempHtml += '<td>'+vals1.KDC+'</td>';
              tempHtml += '<td>'+vals1.FormularyStatus+'</td>';
            tempHtml += '</tr>';
          });

          tempHtml += '</tbody></table></div>';
        });
        
        $('#twModalContent').html(tempHtml);
        $('#medsModal').modal('show');
      }
    });

    /*$.each(asSearchMeds, function(key, val){
      console.log(key);
      console.log(val);
      

    });*/
  }
  else
    top.fmain.medications_form.submit();
};
<?php endif; ?>
/*Saving Data to TW, Track modified/new records*/ 


	</script>
</div>
