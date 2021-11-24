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
//this ajax is being called from landing page without pt loaded
if(isset($_REQUEST['ajax_request'])){
	$load_dt = $_REQUEST['load_dt'];
	$ins_case="";
	if($_REQUEST['id']=='')
	{$ins_case="1";
		$qry_init = "INSERT INTO patient_notes SET patient_id=".$_REQUEST['pat_id'].", provider_id = '".$_REQUEST["prov_id"]."', patient_note = '".addslashes($_REQUEST["notes"])."', note_date = '".$load_dt."', add_date = '".date('Y-m-d H:i:s')."'";
	}else {
		if($_REQUEST['action']=='del')
		{
			$qry_init = "DELETE from patient_notes WHERE id=".$_REQUEST['id'];
		}else {
			$qry_init = "UPDATE patient_notes SET patient_note = '".addslashes($_REQUEST["notes"])."' WHERE id=".$_REQUEST['id'];
		}
	}echo $qry_init;
	imw_query($qry_init);
	if($_REQUEST['id']=='' && $ins_case=="1" && imw_insert_id()>0){$return=imw_insert_id();}else if($_REQUEST['id']){$return=$_REQUEST['id'];}
	if($return){echo $return;die();}
	$return = "0";
	$qry = "SELECT count(id) as tot_notes FROM patient_notes WHERE patient_id = ".$_REQUEST['pat_id']." AND provider_id = '".$_REQUEST["prov_id"]."' AND note_date = '".$load_dt."'";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		$arr = imw_fetch_array($res);
		$return = $arr["tot_notes"];
	}
	echo $return;
	exit();
}


//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
//For saving Pt. notes

if(isset($_REQUEST["load_dt"]) && $_REQUEST["load_dt"]!=""){
	if(inter_date_format() == "mm-dd-yyyy"){
		list($mn, $dt, $yr) = explode("/", $_REQUEST["load_dt"]);
		$disp_date = $mn."/".$dt."/".$yr;
		$sch_date = $yr."-".$mn."-".$dt;
	}else if(inter_date_format() == "dd-mm-yyyy"){
		list($dt, $mn, $yr) = explode("/", $_REQUEST["load_dt"]);
		$disp_date = $dt."/".$mn."/".$yr;
		$sch_date = $yr."-".$mn."-".$dt;
	}
}else{
	$_REQUEST["load_dt"] = get_date_format(date('Y-m-d'),'','','','/');
	if(inter_date_format() == "mm-dd-yyyy"){
		list($mn, $dt, $yr) = explode("/", $_REQUEST["load_dt"]);
		$disp_date = $mn."/".$dt."/".$yr;
		$sch_date = $yr."-".$mn."-".$dt;
	}else if(inter_date_format() == "dd-mm-yyyy"){
		list($dt, $mn, $yr) = explode("/", $_REQUEST["load_dt"]);
		$disp_date = $dt."/".$mn."/".$yr;
		$sch_date = $yr."-".$mn."-".$dt;
	}
}

$library_path = $GLOBALS['webroot'].'/library';
$cur_patid = $_REQUEST['cur_patid'];

$disp_date = date('m-d-y');
if(isset($_REQUEST['load_dt']) && $_REQUEST['load_dt'] != ''){
	$disp_date = $_REQUEST['load_dt'];
}

//Getting pt details of cur_pat 
$pt_sql = imw_query('select CONCAT(fname," ",lname) as pt_name from patient_data where id = '.$cur_patid.'');
$pt_details = imw_fetch_assoc($pt_sql);
$pt_name = $pt_details['pt_name'];



//Getting user details
$qry0 = "SELECT fname, lname, mname FROM users WHERE id = '".$_SESSION["authId"]."'";
$res0 = imw_query($qry0);
if(imw_num_rows($res0) > 0){
	$arr0 = imw_fetch_array($res0);
	$disp_prov = core_name_format($arr0["lname"], $arr0["fname"], $arr0["mname"]);
}

$options = '';	$patInList =0;
if($cur_patid!=''){	
	$qry1 = "select id, fname, mname, lname from patient_data where id = ".$cur_patid;
	$res1 = imw_query($qry1);
	if(imw_num_rows($res1) > 0){
		$arr1 = imw_fetch_array($res1);
		$patName = core_name_format($arr1["lname"], $arr1["fname"], $arr1["mname"]);
		$options .= "<option value=\"".$arr1["id"]."\" selected >".$patName." - ".$arr1["id"]."</option> ";
	}
}

if(!isset($_REQUEST['mode'])){
	$_REQUEST['mode'] = 'sel';
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>To Do</title>
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
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		
	</head>
	<body>
		<div class="mainwhtbox">
			<div class="row">
				<div class="col-sm-12 purple_bar">
					<div class="row">
						<div class="col-sm-4 text-left">
							<label>Add / Update Comments/Reminder</label>	
						</div>
						<div class="col-sm-4 text-center">
							<label><?php echo $disp_prov; ?></label>
						</div>	
						<div class="col-sm-4 text-right">
							<label><?php echo $disp_date; ?></label>
						</div>	
					</div>	
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12 pt10">
							<div id="homeDropDownSCH" class="row">
								<div class="col-sm-12" id="search_patient">
									<div class="row">
										<div class="col-sm-3">
											<input type="hidden" name="pat_id" id="pat_id" value="<?php echo $cur_patid ?>" autocomplete="off">
											<input type="text" class="form-control" name="txt_for" id="txt_for" placeholder="Search patient..." value="<?php echo $pt_name; ?>" onKeyPress="{if (event.keyCode==13)return get_pt_details();}">
										</div>
										<div class="col-sm-3">
											<div class="input-group">
												<input type="text" id="sel_by" name="sel_by" value="<?php echo $elem_status; ?>" readonly class="form-control">
												<div style="white-space:nowrap">
													<div class="dropdown">
														<a id="dLabel" role="button" data-toggle="dropdown" class="btn" data-target="#"  style="padding:10px 12px;"><span class="caret"></span></a>
														<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu" id="main_search_dd"></ul>
													</div>
													<input type='hidden' id="btn_sub" name="btn_sub" value='Search'>
													<input type="hidden" name="from" value="<?php echo ($fax)?$_REQUEST['from']:''; ?>">
													<input type="hidden" name="fieldKey" value="<?php echo $faxfieldKey; ?>">
													<button id="save_butt" type="button" class="btn tsearch" onClick="get_pt_details();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
												</div>	
											</div>
										</div>
									</div>
								</div>  
							</div>	
						</div>
						<div class="col-sm-12 pt10">
							<label>Comments</label>
							<input type="hidden" id="id" name="id" value="<?php if(trim($_REQUEST['ins_id'])){echo trim($_REQUEST['ins_id']);} ?>" />
							<input type="hidden" id="prov_id" name="prov_id" value="<?php echo $_SESSION["authId"];?>" />
							<input type="hidden" id="load_dt" name="load_dt" value="<?php echo date("Y-m-d");?>" />
							<textarea id="new_pat_note" name="new_pat_note" style="overflow:auto;resize: none;" cols="60" rows="2" class="form-control"><?php if(trim($_REQUEST['notes'])){echo $_REQUEST['notes'];} ?></textarea>	
						</div>	
					</div>	
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-7 pt10 text-right" id="module_buttons">
							<input type="button" id="new_button" class="btn btn-primary hide" value="New" onClick="javascript:new_patient_notes(); save_patient_notes('list', '<?php echo $_REQUEST['mode'];?>', document.getElementById('id').value);" />
							<input type="button" class="btn btn-success" value="Done" onClick="javascript:save_patient_notes('list', '<?php echo $_REQUEST['mode'];?>', document.getElementById('id').value);" />
							<input type="button" class="btn btn-danger" value="Cancel" onClick="window.close();" />
						</div>
						<?php 
							$disp_title = 'View All Notes';
							$mode = 'all';
							if($_REQUEST['mode'] == 'all'){
								$disp_title = 'Today\'s Notes';
								$mode = 'sel';
							}
						?>
						<div class="col-sm-5 text-right">
							<br />
							<label class="pointer text_purple" onClick="reloadPage('<?php echo $mode; ?>','<?php echo $disp_date; ?>')"><?php echo $disp_title; ?></label>
						</div>	
					</div>	
				</div>
				<div class="col-sm-12 pt10">
					<div class="row" style="max-height:300px;overflow-y:scroll">
						<table class="table table-bordered table-striped table-hover">
							<tr class="grythead">
								<th class="col-sm-3">Date</th>	
								<th class="col-sm-3">Patient Name - ID</th>	
								<th class="col-sm-3">Comment/Reminder</th>	
								<th class="col-sm-1">Action</th>	
							</tr>
						<?php
							$a=0;$qryPart = '';$pt_notes_arr = array();
							if($_REQUEST['mode']!='all') { 
								$qryPart = "AND pn.note_date = '".$sch_date."'";
							}
							$sel_sch = imw_query("select pn.id, pn.patient_id, pn.provider_id, pn.patient_note, DATE_FORMAT(pn.note_date, '".get_sql_date_format('','','/')."') as note_date, pd.fname, pd.mname, pd.lname from patient_notes pn LEFT JOIN patient_data pd ON pn.patient_id = pd.id WHERE pn.provider_id= ".$_SESSION["authId"]." ".$qryPart." ORDER BY pn.id DESC");
							if(imw_num_rows($sel_sch) > 0){
								while($this_pat = imw_fetch_array($sel_sch)){
								//patient name
								$pat_name = core_name_format($this_pat["lname"], $this_pat["fname"], $this_pat["mname"]);
							?>
								<tr class="pointer">
									<td onClick="javascript:editPatientNote('<?php echo $this_pat['id'];?>','<?php echo $pat_name." - ".$this_pat["patient_id"];?>');"><?php echo $this_pat["note_date"];?></td>
									<td onClick="javascript:editPatientNote('<?php echo $this_pat['id'];?>','<?php echo $pat_name." - ".$this_pat["patient_id"];?>');"><?php echo $pat_name." - ".$this_pat["patient_id"];?></td>
									<td onClick="javascript:editPatientNote('<?php echo $this_pat['id'];?>','<?php echo $pat_name." - ".$this_pat["patient_id"];?>');"><?php echo stripslashes($this_pat["patient_note"]);?></td>
									<td onClick="javascript:delete_patient_note('list', '<?php echo $_REQUEST['mode'];?>', 'del', '<?php echo $this_pat['id'];?>');" class="text-center">
										<img src="<?php echo $GLOBALS['webroot'].'/library/images/closerd.png' ?>">
										<input type="hidden" id="pat_note<?php echo $this_pat['id'];?>" name="pat_note<?php echo $this_pat['id'];?>" value="<?php echo stripslashes($this_pat["patient_note"]);?>">
									</td>
								</tr>
						<?php	} 	
							}else{
								echo '<tr><td colspan="4" class="text-center">No record</td></tr>';
							}
						?>	
						</table>
					</div>	
				</div>	
			</div>	
		</div>
	</body>
	<script>
		jquery_date_format = '<?php echo jQueryIntDateFormat();?>';
		function reloadPage(mode,date){
			window.location='patient_notes.php?load_dt='+date+'&mode='+mode;	
		}
		
		function editPatientNote(id,patName){
			$("#id").val(id);
			$("#new_pat_note").val($("#pat_note"+id).val());
			if($("#new_button").hasClass('hide') === true){
				$("#new_button").removeClass('hide')
			}
			$('#txt_for').val(patName).prop('disabled',true);
			$('#save_butt').prop('disabled',true);
			$('#dLabel').css('pointer-events','none');
		}
		
		function new_patient_notes(){
			$("#id").val('');
			//$("#new_pat_note").val('');
			if($("#new_button").hasClass('hide') === false){
				$("#new_button").addClass('hide')
			}
			$('#txt_for').val('').prop('disabled',false);
			$('#save_butt').prop('disabled',false);
			$('#dLabel').removeAttr('style');
		}
		
		function get_dropdown(icon_name){
			$.ajax({
				url:'<?php echo $GLOBALS['webroot']; ?>/interface/core/ajax_handler.php?task='+icon_name+'',
				success:function(response){
					var result = JSON.parse(response);
					$('#main_search_dd').html(result.recent_search);
				}
			});
		}
		
		function delete_patient_note(mode, pageMode, action, id, cnfrm){	 // mode is 'list'; pageMode is 'all/sel'
			var pat_id = $("#pat_id").val();
			if(typeof(cnfrm)=="undefined"){
				top.fancyConfirm('Are you sure to delete this note?', '', 'delete_patient_note("'+mode+'", "'+pageMode+'", "'+action+'", "'+id+'", true)');
				return;
			}
			else if(cnfrm==true){
				var load_dt = $("#load_dt").val();
				$.ajax({ 
					url: "patient_notes.php?ajax_request=yes&id="+id+"&action="+action,
					success: function(resp){
						window.location = 'patient_notes.php?cur_patid='+pat_id;	
					}
				});
			}else {
				return false;
			}
		}
		
		//Opens pt search popup
		function get_pt_details(){
			var height=500;
			var seach_for = $('#txt_for').val();
			var seacrh_by = $('#sel_by').val();
			if(seacrh_by == ''){
				seacrh_by = 'Active';
			}
			if(seach_for == ''){
				fAlert('Please provide some input to proceed');
				$('#txt_for').focus();
				return false;
			}
			window.open( window.opener.top.JS_WEB_ROOT_PATH+ "/interface/scheduler/search_patient_popup.php?btn_enter="+seacrh_by+"&btn_sub="+seach_for+"&call_from=pdfSplitter&sel_by="+seacrh_by+"","pdfPatientSearchWindow","width=800,height="+height+",top=90,left=10,scrollbars=yes");
		}
		
		//gets pt data values
		function setpatient(pid,fname,mname,lname,suffix,ph,pm,pb,ps,pd,pstreet,pcity,pstate,pzip,ttl,tmp,p1,p2,p3,p4,sel_follow,sel_follow_val,c1,c2,c3){
			var pt_name = fname;
			if(mname != ''){
				pt_name += ' '+mname;
			}
			if(lname != ''){
				pt_name += ' '+lname;
			}	
			var pt_id = pid;
			$('#txt_for').val(pt_name);
			$('#pat_id').val(pt_id);
		}
		
		function save_patient_notes(mode, pageMode, id){
			
			var pat_id = $("#pat_id").val();
			var prov_id = $("#prov_id").val();
			var load_dt = $("#load_dt").val();
			var notes = $("#new_pat_note").val();
			var data = "ajax_request=yes&id="+id+"&pat_id="+pat_id+"&prov_id="+prov_id+"&load_dt="+load_dt+"&notes="+escape(notes);
			$.ajax({ 
				url: "patient_notes.php",
				data:data,
				type:'POST',
				success: function(resp){
					if(resp > 0){
						$("#flag_"+pat_id).css("display", "block");
					}else{
						$("#flag_"+pat_id).css("display", "none");
					}
					if(mode=='list') {
						window.location='patient_notes.php?cur_patid='+pat_id;
					}else {
						hide_patient_notes();
					}
				}
			});
		}
		
		$(document).ready(function(){
			window.resizeTo(900,500);
			get_dropdown('get_icon_bar_status');
			$('body').on('click','#main_search_dd li a:lt(11)',function(){
				var fv = $(this).text();
				if(typeof(fv)!='undefined' && fv!='Advance') 
				{
					$('#sel_by').val(fv);
					$('#findByShow').val(fv);
					if($(this).hasClass('noclose') === false){
						$('ul#main_search_dd').trigger('click');
					}
				}
			});
			
			$('body').on('click','#main_search_dd li a:gt(11)',function(){
				$('#pat_srh_id').val('');
				var fv = $(this).text();
				var pt_id = $(this).attr('pt_id');
				if(typeof(pt_id)=='undefined'){
					$('#sel_by').val(fv).attr('title',fv);
				}
				else{
					var pt_name = fv.split('-');
						$("#txt_for").val(pt_name[0]);
						$('#sel_by').val('Active');
						document.frm_sel.submit();	
				}
				$('.dropdown-submenu > .dropdown-menu').css('display','none');
			});
			$('#patient').val("<?php echo $patName;?>");
			$('#pat_id').val('<?php echo $cur_patid;?>');
		});
		
		
	</script>	
</html>	