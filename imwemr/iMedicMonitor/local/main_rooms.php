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
Purpose: Main interface of iMedicMonitor (Room View) 
Access Type: Direct File
*/
include_once("../globals.php");
include_once("common_functions.php");
if(!$_SESSION['login_sucess']){
	header("Location:../login.php");
}

$dd_fac_id				= isset($_REQUEST['dd_fac_id']) 		? trim($_REQUEST['dd_fac_id']) 		: 0;
$dd_prov_id				= isset($_REQUEST['dd_prov_id']) 		? trim($_REQUEST['dd_prov_id']) 	: 0;
$dd_pro_id				= isset($_REQUEST['dd_pro_id']) 		? trim($_REQUEST['dd_pro_id']) 		: 0;
$_SESSION['local_tz'] 	= isset($_GET['local_tz']) 				? urldecode($_GET['local_tz']) 		: '';
$refresh_mode			= isset($_REQUEST['refresh_mode']) 		? trim($_REQUEST['refresh_mode'])	: 'auto';
$view_mode				= isset($_REQUEST['view_mode']) 		? trim($_REQUEST['view_mode'])		: 'private';
$login_facility 		= isset($_SESSION['login_fac']) 		? $_SESSION['login_fac']			: '';
$act_pro_id				= isset($_REQUEST['act_pro_id']) 		? trim($_REQUEST['act_pro_id']) 	: $dd_pro_id;
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

/*---ROOM GROUPS WORK---*/
$colors 	= ProviderColors();		/*--GET COLORS--*/
$profiles 	= iMonProfiles();		/*--GET PROFILES--*/
if($dd_pro_id==0){foreach($profiles as $pro_id=>$pro_rs){if($pro_rs['default']==1){$dd_pro_id=$pro_id;break;}}}


$facilities_array = array();
if(isset($arr_master_data["facs"]["fac"]) && count($arr_master_data["facs"]["fac"]) > 0){
	if(isset($arr_master_data["facs"]["fac"][0])){
		for($f = 0; $f < count($arr_master_data["facs"]["fac"]); $f++){
			$facilities_array[$arr_master_data["facs"]["fac"][$f]['id']] = $arr_master_data["facs"]["fac"][$f];
		}
	}else{
		$facilities_array[$arr_master_data["facs"]["fac"]['id']] = $arr_master_data["facs"]["fac"];
	}
}

$groups_array = array();
if(isset($arr_master_data["iMongroup"]["iMongroup"]) && count($arr_master_data["iMongroup"]["iMongroup"]) > 0){
	if(isset($arr_master_data["iMongroup"]["iMongroup"][0])){
		for($f = 0; $f < count($arr_master_data["iMongroup"]["iMongroup"]); $f++){
			$groups_array[$arr_master_data["iMongroup"]["iMongroup"][$f]['id']] = $arr_master_data["iMongroup"]["iMongroup"][$f];
		}
	}else{
		$groups_array[$arr_master_data["iMongroup"]["iMongroup"]['id']] = $arr_master_data["iMongroup"]["iMongroup"];
	}
}

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
<style type="text/css">
.icon16,.icon20_done{display:inline-block; margin-right:2px;}
.icon16_readyfor,.icon16_readyforP,.icon16_readyforT{background-position:bottom left; width:22px; border-right:1px solid #ccc;}
.icon20_done{height:12px;}
.priority{display:none;}
</style>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.multiSelect.js"></script>    
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../js/common.js?<?php echo filemtime('../js/common.js');?>"></script>
<script type="text/javascript" src="../js/script.js?<?php echo filemtime('../js/script.js');?>"></script>
<script type="text/javascript" src="../js/jstz.min.js"></script>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript" src="../js/common_idoc.js?<?php echo filemtime('../js/common_idoc.js');?>"></script>
<script type="text/javascript" src="../js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="../js/jquery1.11.3.min.js"></script>
<script type="text/javascript">var jq1_11_3 = jQuery.noConflict();</script>
<script type="text/javascript" src="../js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="../js/jquery.ui.position.min.js"></script>
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
	
	
	
var	baseName = window.location.pathname.substring(window.location.pathname.lastIndexOf("/")+1);
var arr_provs = $.parseJSON('<?php echo addslashes($str_phy_json_arr);?>');
var ser_root	= "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
var date_format = "<?php echo inter_date_format();?>";
var jquery_date_format = 'm-d-Y';

var arrLeftDivs = new Array('box_schp','box_waitingpt_left');
var arrRightDivs = new Array('box_waitingpt','box_wr');
var allObj; var boxBigged = false; var fixObjH = 114;//heights of fix objects and margin
var bigbox_height;
$(document).ready(function(){
    <?php if(!$profiles){?>fancyModal('<iframe name="iframe1" id="iframe1" src="room_groups.php" frameborder=0 style="height:200px; width:800px;"></iframe>','Room View Settings','820px','200px');<?php }
    else if(count($profiles)==1 && $profiles[$dd_pro_id]['profile_data']==''){?>fancyModal('<iframe name="iframe1" id="iframe1" src="room_groups.php" frameborder=0 style="height:500px; width:800px;"></iframe>','Room View Settings','820px','500px');<?php }?>
    
    allObj = $.merge(arrLeftDivs,arrRightDivs);
    arDim = innerDim();
    initDisplayRoomView(arDim,'main');
    $(window).resize(function() {
        arDim = innerDim();
        if(!boxBigged){
            initDisplayRoomView(arDim,'main');
        }
    });
    $('span.icon20_expand').click(function(){BigMe(this,'roomview');});
  //  $('span.icon20_expand').parent('div.section_header').dblclick(function(){BigMe($(this).children('span.icon20_expand'));});
    $('.icon20_close').click(function(){window.close();});
    $('.icon20_checked').click(function(){open_co_pop();});
    show_clock();
   
    //$('.div_group_head').click(function(){$(this).parent().find('.div_room_container').toggle();});
    
    $('#settings').click(function(){
        top.fancyModal('<iframe src="room_groups.php?dd_pro_id=<?php echo $dd_pro_id;?>" frameborder=0 style="height:500px; width:800px;"></iframe>','Room View Settings','820px','500px');
    });

	<?php if($dd_fac_id>0){?>
		load_pts('<?php echo $act_fac_id;?>', 'get');
	<?php }
	echo 'set_refresh_mode();';
	?>

});

/*--TO HOLD TIMER INFO--*/
var page_loader = new Object; var allTimer =  new Object;

/*Room profile work below*/
function profileChanged(t){
	sel_fac = $('#sel_facility').val();
	sel_prov = $('#hidd_sel_provider').val();
	sel_pro = $('#sel_profiles').val();
	view_mode = $('#view_mode').val();
	window.location.href = 'main_rooms.php?dd_fac_id='+sel_fac+'&dd_prov_id='+sel_prov+'&dd_pro_id='+sel_pro+'&view_mode='+view_mode;
}
</script>
</head>
<body scroll="no">
<input type="hidden" id="active_pro" name="active_pro" value="<?php echo $act_pro_id;?>">
<input type="hidden" id="active_fac" name="active_fac" value="<?php echo $act_fac_id;?>">
<div id="topbar">
	<table class="table_collapse w100per"<?php echo $GLOBALS['gl_browser_name']=='ipad' ? ' style="font-size:80%;"' : '';?>>
		<tr>
			<td style="width:50px;">Facility</td>
			<td style="width:<?php echo $GLOBALS['gl_browser_name']=='ipad' ? 150 : 240;?>px;">
			<select name="sel_facility" id="sel_facility" style="width:85%;" onChange="load_pts(this.value, 'get');">
				<option value="">Select Facility</option>
				<?php foreach($facilities_array as $indx=>$facility_rs){
					$sel_dd_facility = '';
					if($dd_fac_id>0 && $act_fac_id==$facility_rs['id']){$sel_dd_facility=' selected';}
					echo '<option value="'.$facility_rs['id'].'"'.$sel_dd_facility.'>'.$facility_rs['name'].'</option>';
				}            
				?>
			</select>
            <input type="hidden" name="hidd_sel_provider" id="hidd_sel_provider" value="<?php echo $dd_prov_id;?>">
			</td>
            <td style="width:90px; white-space:nowrap">Room Profile</td>
            <td style="width:<?php echo $GLOBALS['gl_browser_name']=='ipad' ? 130 : 215;?>px;"><select name="sel_profiles" id="sel_profiles" style="width:85%;" onChange="profileChanged(this)">
            <option value="">--SELECT--</option>
			<?php foreach($profiles as $indx=>$profile_rs){
				$sel_dd_profile = '';
				if($dd_pro_id>0 && $dd_pro_id==$profile_rs['id']){$sel_dd_profile=' selected';}
				echo '<option value="'.$profile_rs['id'].'"'.$sel_dd_profile.'>'.stripslashes($profile_rs['title']).'</option>';
			}            
			?></select>
            </td>
            <!--<td style="width:55px;">Provider</td>
			<td style="width:215px;">
			<select name="sel_provider" id="sel_provider" style="width:200px;" onChange="javascript:load_pts('get', this.value);">
				<option value="">Select Physician</option>
				<?php
				if(isset($arr_master_data["docs"]["doc"]) && count($arr_master_data["docs"]["doc"]) > 0){
					if(isset($arr_master_data["docs"]["doc"][0])){
						for($f = 0; $f < count($arr_master_data["docs"]["doc"]); $f++){
												?>
					<option value="<?php echo $arr_master_data["docs"]["doc"][$f]["id"];?>"><?php echo $arr_master_data["docs"]["doc"][$f]["name"];?></option>
							<?php 
											}
					}else{
						?>
					<option value="<?php echo $arr_master_data["docs"]["doc"]["id"];?>"><?php echo $arr_master_data["docs"]["doc"]["name"];?></option>
						<?php
					}
				}
				?>
			</select>
			</td>-->
			<td style="width:auto; text-align:right;" class="textb"><?php echo date("M d, Y");?><span class="alignCenter ml10 mt5" id="div_now">&nbsp;</span></td>
            <td style="width:50px;"></td>
            <td style="width:60px;" class="alignCenter"><?php if($_SESSION['show_settings']=='1'){?><img src="../css/images/settings.png" style="cursor:pointer; margin:0px 0px 0px;" id="settings"><?php }?></td>
            <td style="width:100px; text-align:center;">Mode: <input type="button" id="btn_refresh_mode" value="Auto" class="btn_normal" title="Click to switch refresh mode" onClick="set_refresh_mode('toggle');"><input type="hidden" name="refresh_mode" id="refresh_mode" value="<?php echo $refresh_mode;?>">
			<td style="width:110px; text-align:center;">View: <input type="button" id="btn_view_mode" value="Private" class="btn_normal" title="Click to switch view mode" onClick="set_view_mode();"><input type="hidden" name="view_mode" id="view_mode" value="<?php echo $view_mode;?>"></td>
            <td style="width:10px;">&nbsp;</td>
			<td style="width:150px;">
				<span class="icon20 icon20_close fr ml5" title="Close iMedicMonitor"></span>
				<span class="icon20 icon20_checked fr" title="View Checked-out Patients"></span>
                <select class="btn_normal" name="changeView" id="changeView" onChange="switchView(this.value);" style="width:62px;">
               	    <option value="normal">Normal view</option>
                    <option value="room" selected>Room view</option>
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
<div class="m5" style="overflow:hidden;">
	<div id="box_waitingpt" class="section fr">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Waiting Patients/Test Active List</div>
		<div class="tableCon">
		<table id="waiting_patients" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:auto;">Patient Name</th>
		   <th style="width:30%;" class="private">Procedure</th>
		   <th style="width:70px;" class="private">Timer</th>
		  </tr>
		 </thead>
		 <tbody>
		 </tbody>
		</table>
		</div>
	</div>

	<div id="box_waitingpt_left" class="section">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Room View</div>
        <div class="div_group_container">
        <?php
		if($dd_pro_id==0){?>
			<div style="margin:100px 0px 0px;" class="alignCenter">
        		<h2 class="unBold">Room Profile not selected.</h2>
                <h3 class="unBold">Please select a room profile to start with Room View.</h3>
        		<script type="text/javascript">
					$('#sel_profiles').animate({backgroundColor: '#FF0000'}, 'slow',null,function(){$('#sel_profiles').animate({backgroundColor: '#FFFFFF'}, 'slow');});
				</script>
    	    </div>
        <?php	
		}else{
			$curr_profile_rs 		= $profiles[$dd_pro_id];
			$this_profile_groups 	= explode(',',$curr_profile_rs['imon_groups']);
			$this_profile_rooms 	= explode(',',$curr_profile_rs['imon_rooms']);
			$group_wise_rooms_arr 	= json_decode($curr_profile_rs['profile_data'],true);
			$groups_count			= count($this_profile_groups);
			if(isset($this_profile_groups) && $groups_count > 0 && $curr_profile_rs['profile_data']!='' && count($groups_array)>0){
				foreach($this_profile_groups as $profile_group_id){
					if($profile_group_id==0 || $profile_group_id==''){
						echo '<div style="margin:100px 0px 0px;" class="alignCenter">
								<h2 class="unBold">No Rooms added to selected profile.</h2>
								<h3 class="unBold">Please go to <b>Settings</b>, add rooms to this profile.</h3>
							</div>';
						continue;
					}
					$div_group_width='99%';
					if($groups_count==2){$div_group_width='48.5%';}
					else if($groups_count>=3){$div_group_width='32.4%';}
					//else if($groups_count>3){$div_group_width='24.2%';}
					?>
					<div class="div_group" style="width:<?php echo $div_group_width;?>;">
						<div class="div_group_head" title="<?php echo $groups_array[$profile_group_id]["name"];?>"><div class="div_group_text"><?php echo substr($groups_array[$profile_group_id]["name"],0,32);?></div></div>
							<div class="div_room_container">
							<?php $this_grp_rooms = $group_wise_rooms_arr[$profile_group_id];
							//pre($room_array);die;
							foreach($this_grp_rooms as $room_id){
								$curr_room_rs = $room_array[$room_id];?>
								<div class="div_room cls_room_fac<?php echo $curr_room_rs['fac_id'];?>" id="room<?php echo $room_id;?>">
								<div class="section_header"><span class="priority fr">Priority </span><span class="span_room_proc fr private"></span><?php echo $curr_room_rs['room_no'];?></div>
								<div class="tableCon">
									<table class="table_collapse w100per">
										<thead>
											<tr>
												<th class="pt_name_td" style="width:auto; height:0px;"></th>
                                                <th style="width:70px;" class="private"></th>
											</tr>
										</thead>
										<tbody class="room_records">
										</tbody>
									</table>
								</div>
								</div><?php
							}?>						
							</div>
					</div>
		<?php	}
			}else{?>
            	<div style="margin:100px 0px 0px;" class="alignCenter">
                    <h2 class="unBold">No Rooms added to selected profile.</h2>
                    <h3 class="unBold">Please go to <b>Settings</b>, add rooms to this profile.</h3>
                </div>
   <?php    }
		}?>  
    	</div>
	</div>
</div>
<div class="m5" style="overflow:hidden;">	
	<div id="box_wr" class="section fr">
		<div class="section_header"><span class="icon20 icon20_expand fr"></span>Waiting Room (Checked-in Patients)</div>
	 	<div class="tableCon">
		<table id="checked_in_pts" class="w100per table_collapse tablesorter">
		 <thead>
		  <tr>
		   <th style="width:4%;">#</th>
		   <th style="width:13%;">CI Time</th>
		   <th style="width:auto">Patient Name</th>
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
	
	<div id="box_schp" class="section">
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
		   <th style="width:25%;" class="private">Message</th>
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
