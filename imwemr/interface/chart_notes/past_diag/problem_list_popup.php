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

require_once(dirname(__FILE__).'/../../../config/globals.php');

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['srcdir']."/classes/medical_hx/problem_list.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
set_time_limit(300);
$problem_obj = new ProblemLst($medical->current_tab);
$pid = $problem_obj->patient_id;

//Vocabulary Array
$arr_info_alert = $problem_obj->problem_vocabulary;

//--- GET LOGGED PROVIDER STATUS ---
$current_user_id = $_SESSION["authId"];
$tmp = getUserFirstName($current_user_id,2);
$operatorName = $tmp[1];


// Saving problem list data
if(isset($_REQUEST['do_action']) && $_REQUEST['do_action'] == 'save'){
	$save_status = $problem_obj->save_prob_list_rec($_REQUEST);
	if(trim($save_status) != '' && $save_status > 0){
		$buttons_to_show = xss_rem($_REQUEST["buttons_to_show"]);
		?>
		<script>
			top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
			window.location.href = top.JS_WEB_ROOT_PATH+'/interface/chart_notes/past_diag/problem_list_popup.php';
		</script><?php
	}
}


?>
<!DOCTYPE html>
<style type="text/css">
	#div_disable{
		position:absolute;
		width:100%;
		height:<?php echo ($_SESSION['wn_height']-265).'px'; ?>;
		text-align:center;
		z-index:1001;
		background-color:#fff;
		opacity:0.6;
		display:none;
	}
	.tbl_even tr:nth-of-type(odd){background-color: #d4cdcd;padding: 1%;}
</style>
<?php
//Show Options
$elem_selList = "Active";

//In URL
if(isset($_GET["sopt"]) && !empty($_GET["sopt"])){
	$elem_selList = $_GET["sopt"];
}
$elem_selList;

$patient_name = $problem_obj->get_patient_name($problem_obj->patient_id,1);
$current_date = date("m-d-Y");

//Get Values in Array
$arrProblemList = $problem_obj->get_prob_list_array($elem_selList);
$flagCreateRow = true;

//Vocabulary Array
$arr_info_alert = $problem_obj->problem_vocabulary;

if(count($arrProblemList) > 0){
	$sql_qry = imw_query("select id from pt_problem_list where pt_id = '$pid'");
	while($row = imw_fetch_array($sql_qry)){
		$pkIdAuditTrail .= $row['id']."-";
		if($pkIdAuditTrailID == ""){		
			$pkIdAuditTrailID = $row['id'];
		}
	}
}

$sessionHeightInMH3= $GLOBALS['gl_browser_name']=='ipad' ? $_SESSION["wn_height"] - 125 : $_SESSION['wn_height']-330;
?>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Medical History:: imwemr ::</title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Selctpicker CSS -->
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]--> 
  	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script>
		var vocabulary_arr = '<?php echo json_encode($arr_info_alert); ?>';
		var JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot'];?>';
		//top.fmain = top;
		var global_date_format = "<?php echo phpDateFormat(); ?>";
		var callFrom = "<?php echo $_GET['callFrom']; ?>";
	</script>
    <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
    <!-- jQuery's Date Time Picker -->
    <script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
    <!-- Bootstrap -->
    <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
    
    <!-- Bootstrap Selectpicker -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
    <!-- Bootstrap typeHead -->
    <script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
	
    <script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript"></script>
    <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet">
    <script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/medical_hx.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/med_problem_list.js"></script>
</head>
<body>
<div id="div_disable" style="display:none;"></div>
<!-- Modal Box -->
<div class="commom_wrapper">
	<div id="div_umls" class="modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Problem Results</h4>	
				</div>
				<div class="modal-body">
						
				</div>	
				<div class="modal-footer">
					<input type="button" name="close" value="Close" id="close_btn" class="btn btn-danger" data-dismiss="modal">
				</div>	
			</div>
		</div>
	</div>
</div>	
<div class="mainwhtbox">
<div class="row">
	<form action="problem_list_popup.php?do_action=save" method="post" name="problem_list_form" id="problem_list_form">
			<input type="hidden" id="mode" name="mode">
			<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $medical->current_tab;?>">
			<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($arr_info_alert) && count($arr_info_alert) > 0) ? urlencode(serialize($arr_info_alert)) : "");?>">
			<input type="hidden" name="preObjBack" id="preObjBack" value="">
			<input type="hidden" name="next_tab" id="next_tab" value="">
			<input type="hidden" name="next_dir" id="next_dir" value="">
			<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
			<input type="hidden" name="list_patient_id" value="<?php echo $pid;?>">
			<input type="hidden" name="current_user_id[]" id="current_user_id_0" value="<?php echo($current_user_id); ?>"/>
			<input type="hidden" name="list_operator_name[]" id="operator_name_0" value="<?php echo($operatorNameEDIT=="")?$operatorName:$operatorNameEDIT; ?>"/>
			<input type="hidden" id="hiden_val_field" name="id[]" value="<?php echo($arrProblemListID[0]["id"]); ?>"/>
		<div class="col-sm-12">
			<div class="row">
				<div class="cols-m-12 purple_bar">
					<div class="row">
						<div class="col-sm-4">
							<label>Patient problem list</label>
						</div>
						<div class="col-sm-4">
							<?php echo $problem_obj->get_patient_name($problem_obj->patient_id); ?>
						</div>
						<div class="col-sm-4">
							<?php 
								$url = $GLOBALS['webroot'].'/interface/chart_notes/past_diag/problem_list_popup.php?sopt=';
							?>
							<select class="selectpicker" name="elem_selList" onChange="setProListOpts(this.value,'<?php echo $url; ?>');" data-width="100%" data-container="#selectpicker_div">
								<option value="All" <?php echo ($elem_selList == "All") ? "selected" : ""; ?>>All</option>
								<option value="Active" <?php echo ($elem_selList == "Active") ? "selected" : ""; ?>>Active</option>
								<option value="Inactive" <?php echo ($elem_selList == "Inactive") ? "selected" : ""; ?>>Inactive</option>
								<option value="Resolved" <?php echo ($elem_selList == "Resolved") ? "selected" : ""; ?>>Resolved</option>
								<option value="Unobserved" <?php echo ($elem_selList == "Unobserved") ? "selected" : ""; ?>>Unobserved</option>
								<option value="External" <?php echo ($elem_selList == "External") ? "selected" : ""; ?>>External</option>
							</select>
						</div>
					</div>	
				</div>	
			</div>
			<div id="selectpicker_div" style="position:absolute"></div>
			<div class="row" style="overflow-x:hidden;height:400px;overflow-y:scroll">
				<table class="table table-striped table-bordered table-condensed">
					<tr class="grythead">
						<td>Onset Date</td>	
						<td>Problem</td>
						<td>Status</td>	
						<td>Operator</td>		
						<td></td>	
					</tr>
					<tr>
						<td colspan="5">
							<?php 
								$arrOpts = array("Active","Inactive","Resolved", "Unobserved","External","Other");
	$str = "";
	$c=1;
	$flagCreateRow = true;
	do{
		
		//
		$elem_problemName = $elem_probListId = $elem_onsetDate = "";
		$elem_comments = $elem_status = $elem_oprator = "";
		$elem_onsetDate = $crDate;
		$elem_opratorName = $crUserName;
		$elem_opratorId = $crUserId;
		if($elem_selList != "All"){
			$elem_status = $elem_selList;
		}
		
		if(count($arrProblemList) > 0){
			$arrTmp = array_pop($arrProblemList);
			$elem_problemName = $arrTmp["problem_name"];
			$elem_probListId = $arrTmp["id"];
			$elem_onsetDate = get_date_Format($arrTmp["onset_dates"],inter_date_format());
			$elem_comments = $arrTmp["comments"];
			$elem_status = $arrTmp["status"];	
			$elem_opratorId = $arrTmp["user_id"];
			$tmp = getUserFirstName($elem_opratorId,2);
			$elem_opratorName = $tmp[1];
			
		}else{
			$flagCreateRow = false;			
		}
		
		if(trim($elem_problemName) != "" || $c==1){
			
			$fun ="";
			if($c == 1){
				$fun .= "<span class=\"spnAdd hand_cur\" onclick=\"adjustRows(1);\"></span><br/>";
			}
			
			$fun .= "<span class=\"spnDel hand_cur\" onclick=\"adjustRows(0,".$c.");\"></span>";
				
			$str .= "<tr id=\"elem_tblPL_".$c."\">".				
					"<td >".
					"<div class='input-group'>
						<input type=\"text\" id=\"elem_onsetDate_".$c."\" name=\"list_date[]\" value=\"".$elem_onsetDate."\" class=\" date-pick form-control begin_date_med\" onchange=\"checkdate(this);\">
						<label for=\"elem_onsetDate_".$c."\" class='input-group-addon'>
							<span class='glyphicon glyphicon-calendar'></span>	
						</label>	
					</div>".
					"</td>".
	
					"<td>".
						"<textarea name=\"list_problem[]\" rows=\"1\"  
						class=\"form-control\" onchange=\"check4DxDesc_new(this);\"
						>".$elem_problemName."</textarea>".
						"<input type=\"hidden\" name=\"elem_probListId[]\" value=\"".$elem_probListId."\">".				
					"</td>
					<td id=\"tdStatus_".$c."\">	";
					if(!empty($elem_status) && (!in_array($elem_status,$arrOpts))){	
						$selDis = "style=\"display:none;\"";
						$spDis = "style=\"display:block;\"";				
						$elem_statusOther = $elem_status;
						$elem_status = "Other";
					}else{
						$selDis = "style=\"display:block;\"";
						$spDis = "style=\"display:none;\"";
						$elem_statusOther = ""; 
					}
	
					$str .=	"<div id='elem_status".$c."'><select  name=\"list_status[]\" class=\"selectpicker\" onchange=\"toggle_other_dropdown('elem_sp".$c."', 'elem_status".$c."',this.value)\" ".$selDis." data-width='100%'>";
						
						foreach($arrOpts as $key => $val){
							$sel = ($val == $elem_status) ? "selected" : "";
							$str .= "<option value=\"".$val."\" ".$sel.">".$val."</option>";
						}
	
					$str .=	"</select></div>";
	
					$str .= "
					<div id=\"elem_sp".$c."\" class='col-sm-12 hide'>
						<div class='input-group'>
							<input type=\"text\" id='elem_statusOther".$c."' name=\"list_status_other[]\" value=\"".$elem_statusOther."\" class=\"form-control\" >
							<div class='input-group-addon' onclick=\"toggle_other_dropdown('elem_status".$c."', 'elem_sp".$c."','Other')\">
								<span class='glyphicon glyphicon glyphicon-arrow-left'></span>
							</div>	
						</div>
					</div>";
			
			$str .=	"</td>".
					"<td>".
						"<input type=\"text\" name=\"elem_opratorName[]\" value=\"".$elem_opratorName."\" 
								class=\"form-control\" readonly=\"readonly\" >".
						"<input type=\"hidden\" name=\"elem_operator[]\" value=\"".$elem_opratorId."\" >".
					"</td>".
					"<td>".$fun.
					"<input type=\"hidden\" name=\"elem_iter[]\" value=\"".$c."\" >".
					"</td>".
					"</tr>";
			$c++;
		}

	}while($flagCreateRow == true);
	echo $str;
							?>
						</td>
					</tr>
				</table>
			</div>
			<div class="row">
				<div class="col-sm-12 text-center">
					<input type="submit" class="btn btn-success" value="Done">	
					<input type="button" class="btn btn-danger" value="Close" onClick="top.window.close();">	
				</div>	
			</div>	
		</div>
	</form>
	
	<script>
		$(function(){
			$('.selectpicker').selectpicker();
			$('.date-pick').datetimepicker({timepicker:false,format:global_date_format,autoclose: true,scrollInput:false});
		});
	</script>
</div>
</div>
</body>
</html>