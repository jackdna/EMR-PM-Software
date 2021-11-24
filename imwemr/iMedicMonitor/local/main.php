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
File: main.php
Purpose: Main interface of iMedicMonitor  (Normal/old view)
Access Type: Direct File
*/
include_once("../globals.php");
include_once("common_functions.php");
if(!$_SESSION['login_sucess']){
	header("Location:../login.php");
}
$dd_fac_id				= isset($_REQUEST['dd_fac_id']) 		? trim($_REQUEST['dd_fac_id']) 		: 0;
$dd_prov_id				= isset($_REQUEST['dd_prov_id']) 		? trim($_REQUEST['dd_prov_id']) 	: 0;
$_SESSION['local_tz'] 	= isset($_GET['local_tz']) 				? urldecode($_GET['local_tz']) 		: '';
$refresh_mode			= isset($_REQUEST['refresh_mode']) 		? trim($_REQUEST['refresh_mode'])	: 'auto';
$view_mode				= isset($_REQUEST['view_mode']) 		? trim($_REQUEST['view_mode'])		: 'private';
$login_facility 		= isset($_SESSION['login_fac']) 		? $_SESSION['login_fac']			: '';
$act_pro_id				= isset($_REQUEST['act_pro_id']) 		? trim($_REQUEST['act_pro_id']) 	: $dd_prov_id;
$act_fac_id		= isset($_REQUEST['act_fac_id']) 		? trim($_REQUEST['act_fac_id']) 	: $dd_fac_id;

if($dd_fac_id==0 && ($login_facility!='' && $login_facility!='0')){$dd_fac_id=$login_facility;}
if($act_fac_id==0 || empty($act_fac_id)) $act_fac_id = $dd_fac_id;

/******NEW LOGIC TO SELECT USER AUTOMATICALLY***im-5182**/
if($dd_prov_id==0 || empty($dd_prov_id)){
	if($_SESSION['logged_user_type']=='1'){
		$dd_prov_id = $_SESSION['authId'];
	}else if(in_array($_SESSION['logged_user_type'],array('3','13'))){//Technician and Scribe.
		$dd_prov_id = $_SESSION['follow_phy_id'];
	}
}

if($act_pro_id==0 || empty($act_pro_id)) $act_pro_id = $dd_prov_id;
/******NEW LOGIC END*************************************/

//GETTING MASTER DATA
$master_file_content = get_master_data();
$arr_master_data = parse_xml_data($master_file_content);
$arr_Physicians = array();
for($i=0;$i<count($arr_master_data["docs"]["doc"]);$i++){
	$cur_phy_id = $arr_master_data["docs"]["doc"][$i]['id'];
	$cur_phy_nm = $arr_master_data["docs"]["doc"][$i]['name'];
	$arr_Physicians[$cur_phy_id] = $cur_phy_nm;
}
if(count($arr_Physicians)>1){$str_phy_json_arr = json_encode($arr_Physicians);}

$room_array = array();
if(isset($arr_master_data["iMonRooms"]["iMonRooms"]) && count($arr_master_data["iMonRooms"]["iMonRooms"]) > 0){
	if(isset($arr_master_data["iMonRooms"]["iMonRooms"][0])){
		for($f = 0; $f < count($arr_master_data["iMonRooms"]["iMonRooms"]); $f++){
			$room_array[$arr_master_data["iMonRooms"]["iMonRooms"][$f]['room_id']] = $arr_master_data["iMonRooms"]["iMonRooms"][$f];
		}
	}else{
		$room_array[$arr_master_data["iMonRooms"]["iMonRooms"]['room_id']] = $arr_master_data["iMonRooms"]["iMonRooms"];
	}
}

$provider_wise_ready4 = ProviderReadyFor('main');
$imm_saved_settings_arr = get_imm_saved_configuration();
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>iMedicMonitor</title>
	<meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
	<link type="text/css" rel="stylesheet" href="../css/jquery.ui.core.css">
	<link type="text/css" rel="stylesheet" href="../css/jquery.ui.theme.css">
	<link type="text/css" rel="stylesheet" href="../css/jquery.multiSelect.css">
	<link type="text/css" rel="stylesheet" href="../css/screen_styles.css?<?php echo filemtime('../css/screen_styles.css');?>">
    <link type="text/css" rel="stylesheet" href="../../library/messi/messi.css">
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery.multiSelect.js"></script>    

	<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="../js/common.js?<?php echo filemtime('../js/common.js');?>"></script>
	<script type="text/javascript" src="../js/script.js?<?php echo filemtime('../js/script.js');?>"></script>
    <script type="text/javascript" src="../js/jstz.min.js"></script>
    <script type="text/javascript" src="../js/jquery1.11.3.min.js"></script>
    <script type="text/javascript">var jq1_11_3 = jQuery.noConflict();</script>
    <script type="text/javascript" src="../js/jquery.contextMenu.js"></script>
    <script type="text/javascript" src="../js/jquery.ui.position.min.js"></script>
    <script type="text/javascript" src="../../library/messi/messi.js"></script>
	<script type="text/javascript" src="../js/common_idoc.js?<?php echo filemtime('../js/common_idoc.js');?>"></script>
	<script type="text/javascript">
	//---used in dilation reverse and forward timers of different views.
	var default_dilation_timer = <?php echo (int)$imm_saved_settings_arr['dilation_time']; ?>;
	if (default_dilation_timer==0) default_dilation_timer = 15;
	
	//---used to manage client and server resources consumed by iMM. Can be set to delayed time to save resources.
	var default_refresh_interval = <?php echo (int)$imm_saved_settings_arr['refresh_interval']; ?>;
	if (default_refresh_interval==0) default_refresh_interval = 10000;
	
	//---used to set if window will reload iMM data even if minimized or not active window.
	var IMM_ALWAYS_REFRESH = '<?php echo $imm_saved_settings_arr['refresh_in_background'];?>';
	if(IMM_ALWAYS_REFRESH=='') IMM_ALWAYS_REFRESH = 'NO';
	
	//---used to manage reload or not reload, of data, with given refresh interval
	var IMM_AUTO_REFRESH_MODE = '<?php echo (int)$imm_saved_settings_arr['auto_refresh'];?>';
	
	//---used to show/noshow appts marked as NO-SHOW.
	var IMM_LIST_NOSHOW_APPTS = <?php echo (int)$imm_saved_settings_arr['show_noshow_patients'];?>;
	
	
	
	var	baseName 	= 'main.php';
	var arr_provs 	= $.parseJSON('<?php echo addslashes($str_phy_json_arr);?>');
	var ser_root	= "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
	var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
	var date_format = "<?php echo inter_date_format();?>";
    var jquery_date_format = 'm-d-Y';
	
	var arrLeftDivs = new Array('box_phy_tat','box_tech_al','box_schp');
	var arrRightDivs = new Array('box_waitingpt','box_wr');
	var allObj; var boxBigged = false; var fixObjH = 114;//heights of fix objects and margin
	var bigbox_height;
	$(document).ready(function(){
		allObj = $.merge(arrLeftDivs,arrRightDivs);
		arDim = innerDim();
		initDisplay(arDim,'main');
		$(window).resize(function() {
			arDim = innerDim();
			if(!boxBigged){
				initDisplay(arDim,'main');
			}
		});
		$('span.icon20_expand').click(function(){BigMe(this);});
		$('span.icon20_expand').parent('div.section_header').dblclick(function(){BigMe($(this).children('span.icon20_expand'));});
		$('.icon20_close').click(function(){window.close();});
		$('.icon20_checked').click(function(){open_co_pop();});
		show_clock();
		//selecting all providers
		var dd_pro = new Array();
		dd_pro["listHeight"] = 300;
		dd_pro["noneSelected"] = "Select All";
		dd_pro["onMouseOut"] = function(){$("#sel_provider").multiSelectOptionsHide();set_active_tabs();};
		$("#sel_provider").multiSelect(dd_pro);
		<?php if($dd_fac_id>0){?>
		$('#sel_facility').val('<?php echo $act_fac_id;?>');
		load_pts('<?php echo $dd_fac_id;?>', '<?php echo $dd_prov_id;?>');
		<?php if($dd_prov_id!=''){?>set_active_tabs('onload');<?php }?>
		<?php }
		echo 'set_refresh_mode();';
		?>
		
	});
	
	/*--TO HOLD TIMER INFO--*/
	var page_loader = new Object; var allTimer =  new Object;
	
	</script>
<style type="text/css">
span.span_priority{float:right;}
.icon16_readyfor,.icon16_readyforP,.icon16_readyforT{padding-bottom:10px;}
</style>
</head>
<body scroll="no">
<input type="hidden" id="active_pro" name="active_pro" value="<?php echo $act_pro_id;?>">
<input type="hidden" id="active_fac" name="active_fac" value="<?php echo $act_fac_id;?>">
<div id="topbar">
	<table class="table_collapse w100per"<?php echo $GLOBALS['gl_browser_name']=='ipad' ? ' style="font-size:80%;"' : '';?>>
		<tr>
			<td style="width:50px;">Facility</td>
			<td style="width:<?php echo $GLOBALS['gl_browser_name']=='ipad' ? 150 : 240;?>px;">
			<select name="sel_facility" id="sel_facility" style="width:85%;" onChange="javascript:load_pts(this.value, 'get');">
				<option value="">Select Facility</option>
				<?php
				if(isset($arr_master_data["facs"]["fac"]) && count($arr_master_data["facs"]["fac"]) > 0){
					if(isset($arr_master_data["facs"]["fac"][0])){				
						for($f = 0; $f < count($arr_master_data["facs"]["fac"]); $f++){
							?>
					<option value="<?php echo $arr_master_data["facs"]["fac"][$f]["id"];?>"><?php echo $arr_master_data["facs"]["fac"][$f]["name"];?></option>
							<?php 
						}
					}else{
						?>
					<option value="<?php echo $arr_master_data["facs"]["fac"]["id"];?>"><?php echo $arr_master_data["facs"]["fac"]["name"];?></option>
						<?php 
					}
				}
				?>
			</select>
			</td>
			<td style="width:55px;">Provider</td>
            <td style="width:<?php echo $GLOBALS['gl_browser_name']=='ipad' ? 130 : 215;?>px;">
			<select name="sel_provider" id="sel_provider" style="width:85%;" onChange="javascript:load_pts('get', this.value);" multiple>
				<option value="">Select Physician</option>
				<?php
				if(isset($arr_master_data["docs"]["doc"]) && count($arr_master_data["docs"]["doc"]) > 0){
					$arr_sel_pro_ids = explode(',',$dd_prov_id);
					if(isset($arr_master_data["docs"]["doc"][0])){
						for($f = 0; $f < count($arr_master_data["docs"]["doc"]); $f++){
							$pro_selected='';
							if(in_array($arr_master_data["docs"]["doc"][$f]["id"],$arr_sel_pro_ids)){$pro_selected=' selected';}
												?>
					<option<?php echo $pro_selected;?> value="<?php echo $arr_master_data["docs"]["doc"][$f]["id"];?>"><?php echo $arr_master_data["docs"]["doc"][$f]["name"];?></option>
							<?php 
											}
					}else{
						$pro_selected='';
						if(in_array($arr_master_data["docs"]["doc"]["id"],$arr_sel_pro_ids)){$pro_selected=' selected';}
						?>
					<option<?php echo $pro_selected;?> value="<?php echo $arr_master_data["docs"]["doc"]["id"];?>"><?php echo $arr_master_data["docs"]["doc"]["name"];?></option>
						<?php
					}
				}
				?>
			</select>
			</td>
			<td style="width:auto;" class="textb alignCenter">
			<?php echo date("M d, Y");?>
			<span class="alignCenter ml10 mt5" id="div_now">
				&nbsp;
			</span>
			</td>
            <td style="width:120px; text-align:center;">Mode: <input type="button" id="btn_refresh_mode" value="Auto" class="btn_normal" title="Click to switch refresh mode" onClick="set_refresh_mode('toggle');"><input type="hidden" name="refresh_mode" id="refresh_mode" value="<?php echo $refresh_mode;?>"></td>

			<td style="width:180px; text-align:center;">View: <input type="button" id="btn_view_mode" value="Private" class="btn_normal" title="Click to switch view mode" onClick="set_view_mode();"><input type="hidden" name="view_mode" id="view_mode" value="<?php echo $view_mode;?>"></td>
			<td style="width:150px;">
				<span class="icon20 icon20_close fr ml5" title="Close"></span>
				<span class="icon20 icon20_checked fr" title="View Checked-out Patients"></span>
                <select class="btn_normal" name="changeView" id="changeView" onChange="switchView(this.value);" style="width:70px;">
               	    <option value="normal" selected>Normal view</option>
                    <option value="room">Room view</option>
                    <option value="extended">Extended view</option>
                </select>
			</td>
		</tr>
	</table>
</div>

<div id="scroll_bar" style="overflow:hidden;">
	<marquee style="width:50%;" class="fr" scrollamount="1" scrolldelay="20" truespeed>checked-in patients here....</marquee>
    <div style="width:50%;" id="prov_tabs_div"></div>
</div>
<div id="processing_image" style="text-align:center; background-color:#FFF; opacity:30%;position:absolute; height:900px; width:100%;"><img src="../css/images/processing.gif"></div>
<div class="m5">
	<div id="box_waitingpt" class="section fr">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Waiting Patients/Test Active List</div>
		<div class="tableCon">
		<table id="waiting_patients" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:5%;">Status</th>
		   <th style="width:13%;">Appt.</th>
		   <th style="width:13%;">CI Time</th>
		   <th style="width:auto;">Patient Name</th>
		   <th style="width:12%;" class="private">Procedure</th>
		   <th style="width:10%;">Provider</th>
		   <th style="width:14%;">Tech</th>
		   <th style="width:19%;" class="private">Message</th>
		   <th style="width:70px;" class="private">Timer</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>
	
	<div id="box_phy_tat" class="section">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Physician Active List</div>
		<div class="tableCon">
		<table id="ready_for_doc_pts" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:13%;">CI Time</th>
		   <th style="width:auto;">Patient Name</th>
		   <th style="width:12%;" class="private">Procedure</th>
		   <th style="width:10%;">Room#</th>
		   <th style="width:14%;">Provider</th>
           <th style="width:14%;">Scribe</th>
		   <th style="width:19%;" class="private">Message</th>
		   <th style="width:70px;" class="private">Timer</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>
	
	<div id="box_tech_al" class="section">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Tech/Active List</div>
		<div class="tableCon">
		<table id="tech_active_list" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:13%;">CI Time</th>
		   <th style="width:auto">Patient Name</th>
		   <th style="width:12%;" class="private">Procedure</th>
		   <th style="width:10%;">Room#</th>
		   <th style="width:14%;">Provider</th>
		   <th style="width:14%;">Tech</th>
		   <th style="width:19%;" class="private">Message</th>
		   <th style="width:70px;" class="private">Timer</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>
	
	<div id="box_wr" class="section fr">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Waiting Room (Checked-in Patients)</div>
	 	<div class="tableCon">
		<table id="checked_in_pts" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:13%;">CI Time</th>
		   <th style="width:auto;">Patient Name</th>
		   <th style="width:12%;" class="private">Procedure</th>
		   <th style="width:14%;">Provider</th>
		   <th style="width:19%;" class="private">Message</th>
		   <th style="width:70px;" class="private">Timer</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>
	
	<div id="box_schp" class="section fl">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Scheduled Patients</div>
		<div class="tableCon">
	 	<table id="scheduled_pts" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:12%;">Appt</th>
		   <th style="width:auto;">Patient Name</th>
		   <th style="width:12%;" class="private">Procedure</th>
		   <th style="width:15%;">Provider</th>
		   <th style="width:20%;" class="private">Message</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>
</div>

<div id="page_bottom_bar">Copyrights &copy; 2006 - <?php echo date("Y"); ?> imwemr &reg; All rights reserved.</div>
<script type="text/javascript">
/*****CONTEXT MENU JS scring*********/
function attach_new_context_menu(obj11){
	/**************************************************
     * Context-Menu with Sub-Menu
     **************************************************/
	$(obj11).each(function(index, element) {
		curr_element = $(obj11);
        this_row_prov_id = jq1_11_3(this).parent('tr').attr('doc_id');
		this_row_sch_id = jq1_11_3(this).parent('tr').attr('sch_id');
		arr_provWise_ready4	= arr_privider_wise_ready4[this_row_prov_id];
		if(typeof(arr_provWise_ready4)=='undefined'){
			arr_provWise_ready4	= arr_privider_wise_ready4['0'];
		}

		jq1_11_3.contextMenu({
			selector: '#nametd'+this_row_sch_id, 
			callback: function(key,root, options) {
				sch_id = jq1_11_3(this).parent('tr').attr('sch_id');
				sel_opt = key;
				room_priority_change(sel_opt,sch_id); 
			},
			items: {
				"fold0": {
					"name": "Patient Priority", 
					"items": {
						"priority_0": {"name": "Normal"},
						"priority_1": {"name": "Priority 1"},
						"priority_2": {"name": "Priority 2"},
						"priority_3": {"name": "Priority 3"}
					}
				},"sep1": "---------",
				"fold2": {
					"name": "Rooms", 
					"items": {
						<?php if(isset($room_array) && is_array($room_array)){
							$i = 1; $z = count($room_array);
							foreach($room_array as $room_id=>$room_rs){?>
								"room_<?php echo $room_id;?>": {"name": "<?php echo $room_rs['room_no'];?>"}
							<?php
								if($i<$z){?>,<?php }
								$i++;
							}
						}?>
					}
				},"sep2": "---------",
				"fold1": {
					"name": "Ready For...",
					"items": arr_provWise_ready4
				},
				"task_6": {"name": "Done"}
			}
		});
			
    });
}

var json_prowise_ready4 = '<?php echo addslashes(json_encode($provider_wise_ready4));?>';
try{
	var arr_privider_wise_ready4 = JSON.parse(json_prowise_ready4);
}catch(e){alert('Exception Encountered. Contact support team.'+"\n\n"+e.message);}
			
/*function srd(){
	var ReleaseWindowiMedic = window.open('release_note.php','Release Notes','width=820,height=575');
	ReleaseWindowiMedic.document.body.focus();
}*/
</script>
<!--------MESSAGES EDITOR---------->
<div class="msgEditor bg1">
	<textarea class="m5" cols="20" rows="2" style="height:50px; width:250px;"></textarea>
	<input type="hidden" name="hidd_sch_id" id="hidd_sch_id" value="">
	<input type="button" class="btn_ok m5" value="Update" onClick="saveMessages();" /> &nbsp; &nbsp; &nbsp;
	<input type="button" class="btn_normal m5" value="Cancel" onClick="hideEditMessages();" />
</div>


<!--------DILATION TIMER EDITOR---------->
<div class="dilTimerEditor bg1">
	<b>&nbsp;Duration in Minutes</b> (Time remaining to dilate)<br>	
	<input type="text" class="m5" style="width:30px;" name="text_timerMinute" id="text_timerMinute" />
	<input type="hidden" name="hidd_dt_sch_id" id="hidd_dt_sch_id" value="" />
	<input type="button" class="btn_ok m5" value="Update" onClick="saveDilTimer();" /> &nbsp; &nbsp; &nbsp;
	<input type="button" class="btn_normal m5" value="Cancel" onClick="hideDilTimerForm();" />
</div>

<table id="record_drag_container" style="background-color:#FFF;" border="1" bordercolor="#ccc"></table>
</body>
</html>
