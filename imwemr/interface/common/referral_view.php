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

require_once '../../config/globals.php';
require_once($GLOBALS['srcdir']."/classes/cls_common_function.php");
$OBJCommonFunction = new CLSCommonFunction;

$library_path = $GLOBALS['webroot'].'/library';
if($ref_type == 1){
	$ins_data_id = $_SESSION['insIdPri'];
	$ins_type = "Primary";
}
else if($ref_type == 2){
	$ins_data_id = $_SESSION['insIdSec'];
	$ins_type = "Secondary";
}
else if($ref_type == 3){
	$ins_data_id = $_SESSION['insIdTer'];
	$ins_type = "Tertiary";
}	
	
$ins_caseid	= $_SESSION["currentCaseid"];	

$rc_updated_flag = 0;	
if(trim($_REQUEST['sbtExpRef']) != "")
{
		$pri_ref_phy_id_arr = $_REQUEST['ref1_phyId'];
		if(!is_array($pri_ref_phy_id_arr))
		{
			$pri_ref_phy_id_arr = array($pri_ref_phy_id_arr);	
		}
		foreach($pri_ref_phy_id_arr as $key => $ref1_phyId_str)
		{
			if(trim($ref1_phyId_str) == "") { continue;	}
			$ref_id_pri = $_REQUEST['ref_id_pri'][$key];		
			$ref1_phy_str = $_REQUEST['ref1_phy'][$key];
			$reffral_no1_str = $_REQUEST['reffral_no1'][$key];	
			$reff1_date_str = $_REQUEST['reff1_date'][$key];	
			$end1_date_str = $_REQUEST['end1_date'][$key];	
			$eff1_date_str = $_REQUEST['eff1_date'][$key];
			
			$no_ref1_str = $_REQUEST['no_ref1'][$key];
			
			$note1_str = $_REQUEST['note1'][$key];
		
			if(trim($ref1_phyId_str) == ""){
				if($ref1_phy_str){
					$Reffer_physician_arr = preg_split('/,/',$ref1_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);
					//$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref1_phyId = $ref_phy_res_id[0][physician_Reffer_id];
				}
			}	
			else{
				$ref1_phyId = $_REQUEST[$ref1_phyId_str];
			}
			
			if($reff1_date_str){
				$reff1_date_str = getDateFormatDB($reff1_date_str);
			}
			if($eff1_date_str){
				$eff1_date_str = getDateFormatDB($eff1_date_str);
			}
			if($end1_date_str){
				$end1_date_str = getDateFormatDB($end1_date_str);
			}
			$no_ref_arr = explode('/',$no_ref1_str);
			$_REQUEST['priNoRef'] = trim($no_ref_arr[0]); 
			$_REQUEST['priUsedRef'] = trim($no_ref_arr[1]); 
			$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
			$reff_used = $no_ref_arr[1];
			
			if($ref_id_pri){			
				$qry  = "update patient_reff set patient_id = ".$_SESSION['patient'].",
						reff_phy_id = '$ref1_phyId_str', reff_by = '".addslashes($ref1_phy_str)."',	no_of_reffs = '$no_reff',
						md = '$mode1', reffral_no = '$reffral_no1_str', reff_date = '$reff1_date_str',reff_used = '$reff_used',
						effective_date = '$eff1_date_str', end_date = '$end1_date_str', 
						insCaseid = '$ins_caseid',
						note = '$note1_str',ins_data_id = '$ins_data_id',reff_type = '".$ref_type."'
						where reff_id = $ref_id_pri";					
				$qryId = imw_query($qry);
				$rc_updated_flag = 1;
			}							
		}				


	// Refferal Auth

	$auth_id_arr = $_REQUEST['a_id'];
	if(!is_array($auth_id_arr))
	{
		$auth_id_arr = array($auth_id_arr);	
	}
	foreach($auth_id_arr as $key => $auth_str)
	{
		if(trim($auth_str) == "") { continue; }
		$auth_id = $_REQUEST['a_id'][$key];
		$auth_provider = $_REQUEST['auth_provider'][$key];		
		$auth_eff1_date = $_REQUEST['auth_eff1_date'][$key];
		$auth_end1_date = $_REQUEST['auth_end1_date'][$key];
		$auth_no_ref1 = $_REQUEST['auth_no_ref1'][$key];
		$auth_name1 = $_REQUEST['auth_name1'][$key];
		$AuthAmount1 = $_REQUEST['AuthAmount1'][$key];
		$auth_note1 = $_REQUEST['auth_note1'][$key];

		if($auth_eff1_date){
			$auth_eff1_date = getDateFormatDB($auth_eff1_date);
		}
		if($auth_end1_date){
			$auth_end1_date = getDateFormatDB($auth_end1_date);
		}

		$auth_no_ref1_arr = explode('/',$auth_no_ref1);
		$_REQUEST['auth_priNoRef'] = trim($auth_no_ref1_arr[0]); 
		$_REQUEST['auth_priUsedRef'] = trim($auth_no_ref1_arr[1]); 
		$auth_no_reff = trim($auth_no_ref1_arr[0]) - trim($auth_no_ref1_arr[1]);
		$auth_reff_used = $auth_no_ref1_arr[1];

		if($auth_id){
			$qry  = "UPDATE patient_auth SET
					auth_provider = '$auth_provider',
					auth_date = '$auth_eff1_date',
					end_date = '$auth_end1_date',
					no_of_reffs = '$auth_no_reff',
					reff_used = '$auth_reff_used',
					auth_comment = '$auth_note1',
					auth_name = '$auth_name1',
					AuthAmount = '$AuthAmount1'
					where patient_id = ".$_SESSION['patient']." AND a_id = ".$auth_id." ";					
			$qryId = imw_query($qry);
			$rc_updated_flag = 1;
		}				
	}
}

if($ref_type != "" && $ins_data_id != "")
{
	$req_qry = "SELECT pat_ref.*,TRIM(CONCAT(reff.LastName,', ',reff.FirstName,' ',reff.MiddleName,if(reff.MiddleName!='',' ',''),reff.Title)) as refphy, 
								if(pat_ref.end_date < current_date() and pat_ref.end_date != '0000-00-00',1,
									if(pat_ref.no_of_reffs = 0 AND pat_ref.reff_used >0,1,0)
								) as exp_status,
								insurance_companies.in_house_code as in_house_code  
								FROM patient_reff pat_ref
								LEFT JOIN refferphysician reff 
								ON pat_ref.reff_phy_id = reff.physician_Reffer_id 
								left join insurance_companies on insurance_companies.id = pat_ref.ins_provider 								
								left join insurance_data on insurance_data.id =  pat_ref.ins_data_id 									
								WHERE pat_ref.reff_type = '".$ref_type."' and insurance_data.ins_caseid = '$ins_caseid' and 									
								pat_ref.ins_data_id = '$ins_data_id' and pat_ref.del_status = 0 
								order by pat_ref.reff_id desc";	
	$exp_ref_obj = imw_query($req_qry) or die(imw_error());	

	$ref_auth_query = "SELECT 
			pat_auth.*,
			TRIM(CONCAT(reff.LastName,', ',reff.FirstName,' ',reff.MiddleName,if(reff.MiddleName!='',' ',''),reff.Title)) as refphy, 
			if(pat_auth.end_date < current_date() and pat_auth.end_date != '0000-00-00',1, if(pat_auth.no_of_reffs = 0 AND pat_auth.reff_used >0,1,0)) as exp_status
			,insurance_companies.in_house_code as in_house_code  
		FROM patient_auth pat_auth
		LEFT JOIN refferphysician reff ON pat_auth.auth_provider = reff.physician_Reffer_id 
		left join insurance_companies on insurance_companies.id = pat_auth.ins_provider
		left join insurance_data on insurance_data.id =  pat_auth.ins_data_id 									
		WHERE 
				pat_auth.ins_type = '".$ref_type."' and 
				insurance_data.ins_caseid = '$ins_caseid' and
				pat_auth.ins_data_id = '$ins_data_id' and 
				pat_auth.auth_status = 0 
				order by pat_auth.a_id desc";

	$ref_auth_result = imw_query($ref_auth_query) or die(imw_error());	
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>imwemr</title>
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
	window.focus;
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
	</head>
	<body>
<div style="background-color:white; width:100%;margin:auto;">	
  	<div class="adminbox no-border pdnon">
    		
      <div class="col-sm-12">
       	<div class="row">
       		<div class="head purple_bar">
          	<h4 style="color:white;"><?php echo ucfirst($ins_type); ?> Referral</h4>
        	</div>
				</div>
      </div>
      
      <div class="clearfix"></div>
			
      <?php if($rc_updated_flag == 1){ ?>
      	<div class="alert alert-success"> Referral Updated Successfully </div>
				<script>
					if( window.opener) window.opener.update_iconbar();
					else top.update_iconbar();
					
					if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href ); }
				</script>
      <?php } ?>
      
				<?php if(imw_num_rows($exp_ref_obj) > 0) { ?>
    		<table class="table table-bordered table-striped" id="<?php echo $ins_type; ?>ReffCont">
     		<thead>
       		<tr>
						<th class="col-xs-1">Insurance</t>
						<th class="col-xs-1">Ref. Physician</th>
						<th class="col-xs-1">Start Date</th>
						<th class="col-xs-1">End Date</th>
						<th class="col-xs-1">Visits</th>
						<th class="col-xs-1">Referral#</th>
						<th class="col-xs-1">Ref. Date</th>
						<th class="col-xs-3">Notes</th>
						<th class="col-xs-1">Status</th>
						<th class="col-xs-1"></th>
						
					</tr>
				</thead>			
				<tbody>		
    		
     		<form name="insuranceReff" id="insuranceReff" enctype="multipart/form-data" method="post">
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
						$target_img = '<img onclick="del_reff_ins_act(this,'.$cur_reff_id.');" style="cursor:pointer;" src="../../library/images/close1.png" alt="Delete Referral" title="Delete Referral" />';
						
			
						if($cur_reff_phy_id != 0){
							$curReffArr = get_reffphysician_detail ($cur_reff_phy_id,'array');
							$cur_reff_by = $curReffArr['full_name'];                
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
        			<tr>
        				<td valign="top" style="padding-top:5px;font-weight:bold;width:80px;"><?php echo $referral_row['in_house_code']; ?></td>
              	<td>
               			<input type="hidden" name="ins_provider" value="<?php echo $ins_provider; ?>" />
                   	<input type="hidden" name="ins_type" value="<?php echo $ins_type; ?>" />
                    <input type="hidden" name="ins_data_id" value="<?php echo $ins_data_id; ?>" />
                                                      
                    <input type="hidden" name="ref_id_pri[]" id="ref_id_pri<?php echo $request_iterator; ?>" value="<?php print $cur_reff_id; ?>" />
                    <?php $_SESSION['ref_id_pri'] = $cur_reff_id; ?>
                    <input type="hidden" name="ref1_phyId[]" id="ref1_phyId<?php echo $request_iterator; ?>" value="<?php print $cur_reff_phy_id; ?>" />
                    <?php
                        $strRefPhyPri = "";
                        $strRefPhyPri = trim(stripslashes($cur_reff_by));
                    ?>
                    <input style="width:145px;" type="text" name="ref1_phy[]" id="ref1_phy<?php echo $request_iterator; ?>" value="<?php echo trim(stripslashes($strRefPhyPri)); ?>" class="form-control" onKeyUp="top.loadPhysicians(this,'ref1_phyId<?php echo $request_iterator; ?>');" onKeyPress="javascript: document.insuranceReff.ref1_phyId<?php echo $request_iterator; ?>.value = '' ;" />
               			     
                </td>
                <td>
                	<div class="input-group">
                		<input class="datepicker form-control" type="text" name="eff1_date[]" id="eff1_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_effective_date); ?>" maxlength="10" />
                		<label for="eff1_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div> 		
               	</td>
               	<td>
               		<div class="input-group">
                		<input class="datepicker form-control" type="text" name="end1_date[]" id="end1_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_end_date); ?>" size="11" />
                		<label for="end1_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>		
               	</td>
                <td>
                    <?php 
                        if($cur_no_of_reffs + $cur_reff_used=='0'){
                            $value="";
                        }
                        else{
                            $value=$cur_no_of_reffs + $cur_reff_used .'/'.$cur_reff_used;
                        }
                    ?>
                    <input type="hidden" class="priNoRef" name="priNoRef[]" id="priNoRef<?php echo $request_iterator; ?>" value="<?php echo $cur_no_of_reffs; ?>"/>
                    <input type="hidden" class="priUsedRef" name="priUsedRef[]" id="priUsedRef<?php echo $request_iterator; ?>" value="<?php echo $cur_reff_used; ?>"/>
                    <input type="text" name="no_ref1[]" id="no_ref1<?php echo $request_iterator; ?>" value="<?php echo stripslashes($value); ?>" size="3" class="form-control" />
               	</td>
                <td>
                   <input type="text" name="reffral_no1[]" id="reffral_no1<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reffral_no); ?>" size="11" class="form-control"  />
              	</td>
                <td>
                	<div class="input-group">
                		<input type="text" name="reff1_date[]" id="reff1_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reff_date); ?>" size="11"  maxlength="10" class="form-control datepicker" />
                		<label for="reff1_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
               	</td>
                <td>
                	<?php $strPriNotesRef = "";$strPriNotesRef = ucwords($cur_note); ?>
                 	<textarea name="note1[]" id="note1<?php echo $request_iterator; ?>" cols="40" rows="1" class="form-control" /><?php echo stripslashes($strPriNotesRef); ?></textarea>
              	</td>
              	<td class="text-center" ><?php if($referral_row['exp_status']==1){ echo 'Expired'; }else{ echo 'Active'; } ?></td>
               	<td class="text-center">
               		<?php if($cur_reff_id > 0){ ?>
                  	<span class="btn btn-success btn-xs">
                  		<img src="../../library/images/scanner.png" width="20" alt="Referral scan document" style="border:none;" onClick="openScanDocument('<?php echo $cur_reff_id; ?>','<?php echo strtolower($ins_type);?>_reff','$ins_data_id');">
                  	</span>
                  	<?php } ?>
                  	<?php echo $target_img; ?> 
                </td>                
            </tr>
         	<?php } ?>
         	
       </tbody> 
				</table>	  	
      	<div class="clearfix">&nbsp;</div>





		<!-- Refferal Auth -->
		<hr/>
	      <div class="col-sm-12">
	       	<div class="row">
	       		<div class="head purple_bar">
	          	<h4 style="color:white;"><?php echo ucfirst($ins_type); ?> Auth</h4>
	        	</div>
					</div>
	      </div>
	      
	      <div class="clearfix"></div>

			<?php if(imw_num_rows($ref_auth_result) > 0) { ?>
			<table class="table table-bordered table-striped" id="<?php echo $ins_type; ?>ReffCont">
	     		<thead>
		       		<tr>
						<th class="col-xs-1">Insurance</t>
						<th class="col-xs-1">Auth Provider</th>
						<th class="col-xs-1">Start Date</th>
						<th class="col-xs-1">End Date</th>
						<th class="col-xs-1">Visits</th>
						<th class="col-xs-1">Authorization#</th>
						<th class="col-xs-1">Auth Amount</th>
						<th class="col-xs-3">Notes</th>
						<th class="col-xs-1">Status</th>
						<th class="col-xs-1"></th>
					</tr>
				</thead>			
				<tbody>		
	     		<?php			
						$request_iterator = 0;
						while($referral_row = imw_fetch_assoc($ref_auth_result))
						{
							$request_iterator ++;
							$cur_reff_id = $referral_row['a_id'];
							$cur_auth_provider = $referral_row['auth_provider'];
							$cur_reff_date = $referral_row['auth_date'];
							$cur_end_date = $referral_row['end_date'];
							$cur_no_of_reffs = $referral_row['no_of_reffs'];
							$cur_reff_used = $referral_row['reff_used'];
							$auth_name = $referral_row['auth_name'];
							$cur_note = $referral_row['auth_comment'];
							$auth_amount = $referral_row['AuthAmount'];
							$ins_provider = $referral_row['ins_provider'];
							$target_img = '<img onclick="delete_auth_info_for_popup('.$cur_reff_id.');" style="cursor:pointer;" src="../../library/images/close1.png" alt="Delete Referral" title="Delete Referral" />';
							
				
							if($cur_reff_date == '0000-00-00' || $cur_reff_date == ''){
								$cur_reff_date = '';
							}else{
								$cur_reff_date = get_date_format($cur_reff_date);
							}
				
							if($cur_end_date == '0000-00-00' || $cur_end_date == ''){
								$cur_end_date = '';
							}else{
								$cur_end_date = get_date_format($cur_end_date);
							}
						?>
	        			<tr id='auth_row_id_<?php echo $cur_reff_id; ?>'>
	        				<td valign="top" style="padding-top:5px;font-weight:bold;width:80px;">
	        					<?php echo $referral_row['in_house_code']; ?>
	    					</td>
	              	<td>
	              		<input type="hidden" name="a_id[]" value="<?php echo $cur_reff_id; ?>" />
						<select class="form-control minimal" name="auth_provider[]" id="auth_provider<?php echo $request_iterator; ?>">
							<option value="">Select Provider</option>
							<?php
								$data = $OBJCommonFunction->drop_down_providers('','','1', true);
								foreach ($data as $key => $value) {
									$selected = ($key == $cur_auth_provider) ? 'selected="selected"' : '';
									echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
								}
							?>
						</select>            		
	                </td>
	                <td>
	                	<div class="input-group">
	                		<input class="datepicker form-control" type="text" name="auth_eff1_date[]" id="auth_eff1_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reff_date); ?>" maxlength="10" />
	                		<label for="auth_eff1_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
										</div> 		
	               	</td>
	               	<td>
	               		<div class="input-group">
	                		<input class="datepicker form-control" type="text" name="auth_end1_date[]" id="auth_end1_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_end_date); ?>" size="11" />
	                		<label for="auth_end1_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
										</div>		
	               	</td>
	                <td>
	                    <?php 
	                        if($cur_no_of_reffs + $cur_reff_used=='0'){
	                            $value="";
	                        }
	                        else{
	                            $value=$cur_no_of_reffs + $cur_reff_used .'/'.$cur_reff_used;
	                        }
	                    ?>
	                    <input type="text" name="auth_no_ref1[]" id="auth_no_ref1<?php echo $request_iterator; ?>" value="<?php echo stripslashes($value); ?>" size="3" class="form-control" />
	               	</td>
	                <td>
	                   <input type="text" name="auth_name1[]" id="auth_name1<?php echo $request_iterator; ?>" value="<?php echo stripslashes($auth_name); ?>" size="11" class="form-control"  />
	              	</td>
					<td>
	                   <input type="text" name="AuthAmount1[]" id="AuthAmount<?php echo $request_iterator; ?>" value="<?php echo stripslashes($auth_amount); ?>" class="form-control"  />
	              	</td>
	                <td>
	                	<?php $strPriNotesRef = "";$strPriNotesRef = ucwords($cur_note); ?>
	                 	<textarea name="auth_note1[]" id="auth_note1<?php echo $request_iterator; ?>" cols="40" rows="1" class="form-control" /><?php echo stripslashes($strPriNotesRef); ?></textarea>
	              	</td>
	              	<td class="text-center" ><?php if($referral_row['exp_status']==1){ echo 'Expired'; }else{ echo 'Active'; } ?></td>
	               	<td class="text-center">
	                  	<?php echo $target_img; ?> 
	                </td>                
	            </tr>
	         	<?php } ?>
	         	
	       		</tbody> 
			</table>	  	
			<?php } ?>

	      <div class="clearfix"></div>


				<div id="exp_reff_opts" class="text-center">
						<div id="module_buttons" class="ad_modal_footer">
							<input type="submit" value="Save" name="sbtExpRef" class="btn btn-success" /> &nbsp; 
							<input type="button" value="Close" onclick="window.close()" class="btn btn-danger" />
						</div>
				</div>
    	</form>
     	<?php		
				} else { echo '<div class="alert alert-danger">No Referral Exists</label>';	 } 
			?>
	</div>


</div>
</body>
</html>