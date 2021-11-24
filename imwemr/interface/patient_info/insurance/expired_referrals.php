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

require_once '../../../config/globals.php';
require_once('../../../library/classes/cls_common_function.php');		
	
$OBJCommonFunction = new CLSCommonFunction;	
$library_path = $GLOBALS['webroot'].'/library';
$ins_caseid	= $_SESSION["currentCaseid"];
$ins_provider = trim($_REQUEST['ins_provider']);	
$ins_type = trim($_REQUEST['ins_type']);
$ins_data_id = trim($_REQUEST['ins_data_id']);
$i_key = 1; $s_name = 'pri';
if($ins_type == 'secondary')  { $i_key = 2; $s_name = 'sec'; }
else if($ins_type == 'tertiary') { $i_key = 3; $s_name = 'ter'; }

$patient_reff_type = array('primary'=>1,'secondary'=>2,'tertiary'=>3);
	
$rc_updated_flag = 0;	
if(trim($_REQUEST['sbtExpRef']) != "")
{
	$ref_phy_id_arr = $_REQUEST['ref'.$i_key.'_phyId'];
	if(!is_array($ref_phy_id_arr))
		{
			$ref_phy_id_arr = array($ref_phy_id_arr);	
		}
		foreach($ref_phy_id_arr as $key => $ref_phyId_str)
		{
			if(trim($ref_phyId_str) == "")
			{
				continue;	
			}
			$ref_id	= $_REQUEST['ref_id_'.$s_name][$key];
			$ref_phy_str = $_REQUEST['ref'.$i_key.'_phy'][$key];
			$reffral_no_str = $_REQUEST['reffral_no'.$i_key][$key];	
			$reff_date_str = $_REQUEST['reff'.$i_key.'_date'][$key];	
			$end_date_str = $_REQUEST['end'.$i_key.'_date'][$key];	
			$eff_date_str = $_REQUEST['eff'.$i_key.'_date'][$key];
			$no_ref_str = $_REQUEST['no_ref'.$i_key][$key];
			$note_str = $_REQUEST['note'.$i_key][$key];
		
			if(trim($ref_phyId_str) == "")
			{
				if($ref_phy_str)
				{
					$Reffer_physician_arr = preg_split('/,/',$ref_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);
					$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref_phyId = $ref_phy_res_id[0][physician_Reffer_id];
				}
			}	
			else{
				$ref_phyId = $_REQUEST[$ref_phyId_str];
			}
			
			if($reff_date_str){
				$reff_date_str = getDateFormatDB($reff_date_str);
			}
			if($eff_date_str){
				$eff_date_str = getDateFormatDB($eff_date_str);
			}
			if($end_date_str){
				$end_date_str = getDateFormatDB($end_date_str);
			}
			$no_ref_arr = explode('/',$no_ref_str);
			$_REQUEST[$s_name.'NoRef'] = trim($no_ref_arr[0]); 
			$_REQUEST[$s_name.'UsedRef'] = trim($no_ref_arr[1]); 
			$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
			$reff_used = $no_ref_arr[1];
			
			if($ref_id)
			{	
				$mode = $_REQUES['mode'.$i_key];
				$fileName = $_REQUES['fileName'.$i_key];		
				$query = "update patient_reff set patient_id = ".$_SESSION['patient'].",
										reff_phy_id = '".$ref_phyId_str."', reff_by = '".addslashes($ref_phy_str)."',	no_of_reffs = '".$no_reff."',
										md = '".$mode."', reffral_no = '".$reffral_no_str."', reff_date = '".$reff_date_str."',reff_used = '".$reff_used."',
										effective_date = '".$eff_date_str."', end_date = '".$end_date_str."',ins_provider = ".$ins_provider.",
										upload_document = '".$fileName."', insCaseid = '".$ins_caseid."',
										note = '".$note_str."',ins_data_id = '".$ins_data_id."',reff_type = '".$patient_reff_type[$ins_type]."'
										where reff_id = ".$ref_id." ";					
				$sql = imw_query($query) ;
				$rc_updated_flag = 1;
			}							
		}	
		
		
}

if($ins_type != "" && $ins_data_id != "")
{
		$req_qry = "SELECT pat_ref.*,TRIM(CONCAT(reff.LastName,', ',reff.FirstName,' ',reff.MiddleName,if(reff.MiddleName!='',' ',''),reff.Title)) as refphy
									FROM `patient_reff` pat_ref
									LEFT JOIN refferphysician reff 
									ON pat_ref.reff_phy_id = reff.physician_Reffer_id 
									WHERE pat_ref.reff_type = '".$patient_reff_type[$ins_type]."' 
									AND ((pat_ref.end_date < current_date() && pat_ref.end_date != '0000-00-00')
										  OR
										  (pat_ref.no_of_reffs = 0 && pat_ref.reff_used > 0)		
										) 
									AND pat_ref.ins_data_id = '$ins_data_id' and del_status = 0 
									order by pat_ref.reff_id desc";	
		$exp_ref_obj = imw_query($req_qry);														
	}
	$global_date_format = 'Y-m-d';//phpDateFormat();
?>
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<!-- Messi CSS -->
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet">
<!-- Application Common CSS -->
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<!-- Insurance Page CSS -->
<link href="<?php echo $library_path; ?>/css/insurance.css" rel="stylesheet">
<!-- Messi Plugin for fancy alerts CSS -->
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<!-- DateTime Picker CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<!-- jQuery's Date Time Picker -->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
<!-- Bootstrap -->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<!-- Bootstrap Selectpicker -->
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<!-- Bootstrap typeHead -->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>


<!-- Application Common JS -->
<script type="text/javascript" sec="<?php echo $library_path; ?>/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<script>
	// Default JS Variables 
	var web_root = '<?php echo $GLOBALS['webroot']; ?>';
	var mandatory = <?php echo json_encode($defaults['mandatory_fld']); ?>;
	var mandatory_fld = <?php echo json_encode($mandatory_flds); ?>;
	var vocabulary = <?php echo json_encode($vocabulary); ?>;
	var patient_info = <?php echo json_encode($patientDetail); ?>;
	var phone_format = '<?php echo $GLOBALS['phone_format'] ?>';
	var operator = '<?php echo $defaults['operator_name']; ?>';
	var change_flag, _this, $_this = false;
	var js_today_date = '<?php echo get_date_format(date('Y-m-d')); ?>';
	var js_alert_msg = '<?php echo trim($data_obj->js_alert_msg); ?>';
	if(js_alert_msg) { top.fAlert(js_alert_msg); }
</script>

<!-- Insurance JS -->
<script type="text/javascript" src="<?php echo $library_path; ?>/js/patient_info.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/insurance.js"></script>
<div style="background-color:white; width:100%;margin:auto;">	
  	<div class="adminbox no-border">
    		
      <div class="col-sm-12">
        <div class="head">
          <h4><?php echo ucfirst($ins_type); ?> Expired Referral</h4>
        </div>
      </div>
      
      <div class="clearfix"></div>
			
      <?php if($rc_updated_flag == 1){ ?>
      <div class="alert alert-success"> Referral Updated Successfully </div>
      <?php } ?>
      
			<?php 
				if(imw_num_rows($exp_ref_obj) > 0)
				{ 
			?>
      <form name="insuranceReff" id="insuranceReff" enctype="multipart/form-data" method="post">
      	<div class="tblBg" id="<?php echo $ins_type; ?>ReffCont" style="max-height:500px; " >
        <?php			
					$request_iterator = 0;
					while($referral_row = imw_fetch_assoc($exp_ref_obj))
					{
						$request_iterator ++;
						$cur_reff_id = $referral_row['reff_id'];
						$cur_reff_phy_id = $referral_row['reff_phy_id'];
						$cur_reff_date = $referral_row['reff_date'];
						$cur_effective_date = $referral_row['effective_date'];
						$cur_end_date = $referral_row['end_date'];
						$cur_no_of_reffs = $referral_row['no_of_reffs'];
						$cur_reff_used = $referral_row['reff_used'];
						$cur_reffral_no = $referral_row['reffral_no'];
						$cur_note = $referral_row['note'];
						$target_img = '<img onclick="del_reff_ins_act(this,'.$cur_reff_id.');" style="cursor:pointer;" src="../../../library/images/close1.png" alt="Delete Referral" title="Delete Referral" />';			
			
						if($cur_reff_phy_id != 0){
							$cur_reff_by = $OBJCommonFunction->get_ref_phy_name($cur_reff_phy_id);                
						}
			
						if($cur_reff_date == '0000-00-00' || $cur_reff_date == ''){
							$cur_reff_date = '';
						}else{
							$cur_reff_date = get_date_format($cur_reff_date);
						}
			
						if($cur_effective_date == '0000-00-00' || $cur_effective_date == ''){
							$cur_effective_date = '';
						}else{
							$cur_effective_date = get_date_format($cur_effective_date);
						}
			
						if($cur_end_date == '0000-00-00' || $cur_end_date == ''){
							$cur_end_date = '';
						}else{
							$cur_end_date = get_date_format($cur_end_date);
						}
					?>
        			
              <div class="col-sm-12 table_grid margin-top-5" id="<?php echo $ins_type; ?>_refferal_<?php echo $request_iterator; ?>">	 	
                <div class="row">	
                  <div class="col-sm-12 margin-top-5">
                    <div class="row">
                      <div class="col-sm-7">
                        <label class="sub-heading">Referral</label>
                      </div>
                      <div class="col-sm-5 text-right">
                        <?php echo $target_img; ?>
                        
                      </div>
                    </div>
                  </div>
              
              		<div class="clearfix"></div>
                 
                  <div class="col-sm-12">
                    <?php $_SESSION['ref_id_pri'] = $cur_reff_id; ?>
                    <input type="hidden" name="ref<?php echo $i_key;?>_phyId[]" id="ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>" value="<?php echo $cur_reff_phy_id; ?>" />
                    <input type="hidden" name="ref_id_<?php echo $s_name;?>[]" id="ref_id_<?php echo $s_name;?><?php echo $request_iterator; ?>" value="<?php echo $cur_reff_id; ?>" />
                  
                    <div class="row">
                    
                      <div class="col-sm-3">
                        <label>Ref. Physician</label><br>
                        <?php
                            $strRefPhy = "";
                            $strRefPhy = trim(stripslashes($cur_reff_by));
                      	?>
  											
                        <div class="input-group">
                          <input type="text" name="ref<?php echo $i_key;?>_phy[]" id="ref<?php echo $i_key;?>_phy<?php echo $request_iterator; ?>" value="<?php echo trim(stripslashes($strRefPhy)); ?>"  class="form-control" data-search-by="" data-action="search_physician" data-text-box="ref<?php echo $i_key;?>_phy<?php echo $request_iterator; ?>" data-id-box="ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>" size="25" onKeyUp="top.chk_change_in_form('<?php echo addslashes($strRefPhy); ?>',this,'InsTabDb',event); chk_change('<?php echo trim(addslashes($cur_reff_by)); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="javascript: document.insuranceCaseFrm.ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>.value = '' ;chk_change('<?php echo trim(addslashes($cur_reff_by)); ?>',this.value,event); save_data(event);" onFocus="get_focus_obj(this);loadPhysicians(this,'ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>');">
                          <label class="input-group-addon btn search_physician" data-source="ref<?php echo $i_key;?>_phy<?php echo $request_iterator; ?>"><i class="glyphicon glyphicon-search"></i></label>
                      	</div>   
                        
                      </div>
                      
                      <div class="col-sm-3">
                      	<label>Start Date</label><br>
                      	<div class="input-group">
                        	<input class="datepicker form-control" type="text" name="eff<?php echo $i_key;?>_date[]" id="eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_effective_date); ?>" size="11" onBlur="checkdate(this); lost_focus(this,'form-control');"  maxlength="10" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_effective_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_effective_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                        	<label for="eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                      	</div>    
                      </div>
                      
                      <div class="col-sm-3">
                      	<label>End Date</label><br>
                      	<div class="input-group">
                          <input type="text" class="datepicker form-control" name="end<?php echo $i_key;?>_date[]" id="end<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_end_date); ?>" size="11" onBlur="checkdate(this); chkFuture('eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>',this); lost_focus(this,'form-control');"  onChange="checkdate(this);"  maxlength="10" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_end_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_end_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" />
                          <label for="end<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                       	</div>   
                      </div>
                      
                      <div class="col-sm-3">
                        <label>Visits</label><br>
                        <?php
                          if($cur_no_of_reffs + $cur_reff_used == '0' && $InsPriRefVisits==1){
                            $class="form-control mandatory-chk mandatory";
                          } 
                          else{
                            $class="form-control";
                          }
                          if($cur_no_of_reffs + $cur_reff_used=='0'){
                            $value="";
                          }
                          else{
                            $value = $cur_no_of_reffs + $cur_reff_used .'/'.$cur_reff_used;
                          }
                        ?>
                        <input type="hidden" name="<?php echo $s_name; ?>NoRef[]" id="<?php echo $s_name; ?>NoRef<?php echo $request_iterator; ?>" value="<?php echo $cur_no_of_reffs; ?>"/>
                        <input type="hidden" name="<?php echo $s_name; ?>UsedRef[]" id="<?php echo $s_name; ?>UsedRef<?php echo $request_iterator; ?>" value="<?php echo $cur_reff_used; ?>"/>
                        <input type="text"  name="no_ref<?php echo $i_key;?>[]" id="no_ref<?php echo $i_key;?><?php echo $request_iterator; ?>" value="<?php echo stripslashes($value); ?>" size="3" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($value); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($value); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                      </div>
                      
                    </div>
                  </div>
                
            			<div class="clearfix"></div>
              
                  <div class="col-sm-12">
                    <div class="row">
                      
                      <div class="col-sm-3">
                        <label>Referral#</label><br>
                        <input type="text" name="reffral_no<?php echo $i_key;?>[]" id="reffral_no<?php echo $i_key;?><?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reffral_no); ?>" size="11" class="form-control " onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_reffral_no); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_reffral_no); ?>',this.value,event);" onBlur="lost_focus(this,'form-control');" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                      </div>
                      
                      <div class="col-sm-3">
                      	<label>Ref. Date</label><br>
                      	<div class="input-group">
                          <input type="text" name="reff<?php echo $i_key;?>_date[]" id="reff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reff_date); ?>" size="11" onBlur="checkdate(this); lost_focus(this,'form-control');"  maxlength="10" class="form-control datepicker" onKeyUp="top.chk_change_in_form('<?php echo addslashes($cur_reff_date); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($cur_reff_date); ?>',this.value,event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);">
                      	  <label for="reff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                      	</div>    
                      </div>
                      
                      <div class="col-sm-6">
                        <label>Notes</label><br>
                        <?php
                          $strNotesRef = "";
                          $strNotesRef = ucwords($cur_note);
                        ?>
                        <textarea style="height:34px;" name="note<?php echo $i_key;?>[]" id="note<?php echo $i_key;?><?php echo $request_iterator; ?>" cols="40" rows="1" class="form-control" onKeyUp="top.chk_change_in_form('<?php echo addslashes($strNotesRef); ?>',this,'InsTabDb',event); chk_change('<?php echo addslashes($strNotesRef); ?>',this.value,event);" onKeyPress="save_data(event);" onBlur="lost_focus(this,'form-control');" onFocus="get_focus_obj(this);"><?php echo stripslashes($strNotesRef); ?></textarea>
                      </div>
                      
                    </div>  
                  </div>
             			
                  <div class="clearfix">&nbsp;</div>
                </div>
              </div>
       		<?php 
								if($request_iterator == '')
								{
									$request_iterator = 2;			
								}
								else
								{
									$request_iterator++;	
								}
						}
				?>
        </div>
        <div class="clearfix">&nbsp;</div>
        <div class="clearfix" >&nbsp;</div>
		<div id="exp_reff_opts" class="text-center">
		<div id="module_buttons" class="ad_modal_footer">
		 <input type="submit" value="Save" name="sbtExpRef" class="btn btn-success" /> &nbsp; 
         	<input type="button" value="Close" onclick="window.close()" class="btn btn-danger" />
		</div>
       	</div>
    	</form>
     	<?php		
				}
				else
				{
					echo '<div class="alert alert-danger">No Expired Referral Exists</label>';	
				}
			?>
      
	</div>
</div>

<!-- 
	Start Search Physician
-->
<div id="search_physician_result" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">x</button>
        <h4 class="modal-title col-xs-4 col-sm-3" id="modal_title">Select Physician</h4>
        <div class="col-xs-7 col-sm-8 form-inline">
        <select class="selectpicker col-xs-4" id="search_by" title="Search By">
          	<option value="LastName" selected="selected">Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="Address1">Street Address</option>
                        <option value="PractiseName">Practice Name</option>
						<option value="physician_phone">Phone Number</option>
						<option value="physician_fax">Fax Number</option>
       	</select>&nbsp;For&nbsp;
        <span class="col-xs-7 input-group">
        	<input type="text" id="phy_ajax" class="form-control" title="Search Physician" placeholder="Search Physician" data-action="search_physician"data-text-box="" data-id-box="" />
          <label class="input-group-addon btn search_physician" id="phy_ajax_btn" title="Click to Search" data-source="phy_ajax">
          	<span class="glyphicon glyphicon-search"></span>
        	</label>
       	</span>
        </div>
     	</div>
      
      <div class="modal-body" style="max-height:350px; overflow:hidden; overflow-y:auto;">
      	<div class="loader"></div>
      </div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- End Search Physician -->