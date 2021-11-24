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
require_once("../globals.php");
//include_once("common_functions.php");
if(!$_SESSION['login_sucess']){header("Location:../login.php");}
require_once("class.imm.logic.php");

$dd_fac_id		= isset($_REQUEST['dd_fac_id']) 		? trim($_REQUEST['dd_fac_id']) 		: '';
$dd_prov_id		= isset($_REQUEST['dd_prov_id']) 		? trim($_REQUEST['dd_prov_id']) 	: '';
$dd_pro_id		= isset($_REQUEST['dd_pro_id']) 		? trim($_REQUEST['dd_pro_id']) 		: '';
$view_mode		= isset($_REQUEST['view_mode']) 		? trim($_REQUEST['view_mode']) 		: '';
$act_pro_id		= isset($_REQUEST['act_pro_id']) 		? trim($_REQUEST['act_pro_id']) 	: $dd_prov_id;
$act_fac_id		= isset($_REQUEST['act_fac_id']) 		? trim($_REQUEST['act_fac_id']) 	: $dd_fac_id;
//$local_tz		= urldecode($_GET['local_tz']);
$login_facility = $_SESSION['login_fac'];
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

$iMMLogic = new iMMLogic($dd_fac_id,$dd_prov_id);

$arrPhysicians = $iMMLogic->getAllProvs();
if(count($arrPhysicians)>1){$str_phy_json_arr = json_encode($arrPhysicians);}

$room_array = $iMMLogic->getAllRooms(false);
$provider_wise_ready4 = $iMMLogic->ProviderReadyFor('main');

$iMMColList = $iMMLogic->getImmExtendedColList();
$imm_saved_settings_arr = $iMMLogic->get_imm_saved_configuration();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>iMedicMonitor</title>
<meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
<link type="text/css" rel="stylesheet" href="../css/jquery.ui.core.css">
<link type="text/css" rel="stylesheet" href="../css/jquery.ui.theme.css">
<link type="text/css" rel="stylesheet" href="../css/jquery.multiSelect.css">
<link type="text/css" rel="stylesheet" href="../css/screen_styles_extended.css?<?php echo filemtime('../css/screen_styles_extended.css');?>">
<link type="text/css" rel="stylesheet" href="../../library/messi/messi.css">
<style type="text/css">
.icon16,.icon20_done{display:inline-block; margin-right:2px;}
.icon16_readyfor,.icon16_readyforP,.icon16_readyforT{background-position:bottom left; width:22px; border-right:1px solid #ccc;}
.icon20_done{height:12px;}
.priority{display:none;}
#active_patients thead tr th, tbody tr td{text-align:center;}
tbody tr td{word-wrap: break-word; word-break: break-all; white-space: normal;}

#active_patients_header {
    position: absolute;
    display: none;
    background-color: white;
    z-index: 2;
}
#box_extended_ptlist .div_group_container {
	position: relative;
}
</style>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.multiSelect.js"></script>    
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../js/common.js?<?php echo filemtime('../js/common.js');?>"></script>
<script type="text/javascript" src="../js/extended.js?<?php echo filemtime('../js/extended.js');?>"></script>
<script type="text/javascript" src="../js/jstz.min.js"></script>
<script type="text/javascript" src="../../library/messi/messi.js"></script>
<script type="text/javascript" src="../js/common_idoc.js?<?php echo filemtime('../js/common_idoc.js');?>"></script>
<script type="text/javascript" src="../js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="../js/jquery1.11.3.min.js"></script>
<script type="text/javascript">var jq1_11_3 = jQuery.noConflict();</script>
<script type="text/javascript" src="../js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="../js/jquery.ui.position.min.js"></script>
<script type="text/javascript" src="../js/elapseMe.js?<?php echo filemtime('../js/elapseMe.js');?>"></script>
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
var Cols2Show = $.parseJSON('<?php echo addslashes(json_encode($iMMColList));?>');
var arr_provs = $.parseJSON('<?php echo addslashes($str_phy_json_arr);?>');
var ser_root	= "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
var date_format = "<?php echo inter_date_format();?>";
var jquery_date_format = 'm-d-Y';
var fixObjH = 114;//heights of fix objects and margin
var arDim1 = innerDim();
var temp_timer;
$(document).ready(function(){
	set_ar_header();
	arDim = innerDim();
    initDisplayExtendedView(arDim,'extended');
    $(window).resize(function() {
		temp_timer = setTimeout(function(){ clearInterval(temp_timer);resizeToMinimum(1500,500);switchView('extended');},500);
		//window.location.reload();
    });
    show_clock();
	$('.icon20_close').click(function(){window.close();});
	$('.icon20_checked').click(function(){open_co_pop();});
	//selecting all providers
	var dd_pro = new Array();
	dd_pro["listHeight"] = 300;
	dd_pro["noneSelected"] = "Select All";
	dd_pro["onMouseOut"] = function(){$("#sel_provider").multiSelectOptionsHide();set_active_tabs();};
	$("#sel_provider").multiSelect(dd_pro);
	<?php if($dd_fac_id>0){?>
		$('#sel_facility').val('<?php echo $act_fac_id;?>');
		if (typeof(Storage) !== "undefined") {
			if(localStorage.chk_showchkout=='true') $("#chk_showchkout").prop('checked',true);
			else $("#chk_showchkout").prop('checked',false);
		}
		load_pts('<?php echo $dd_fac_id;?>', '<?php echo $act_pro_id;?>');
		<?php if($dd_prov_id!=''){?>set_active_tabs('onload');<?php }?>
	<?php }?>
	
	
});

/*--TO HOLD TIMER INFO--*/
var page_loader = new Object; var allTimer =  new Object;

function saveAllAptChkState(t){
	if (typeof(Storage) !== "undefined"){
		if($(t).prop('checked')) localStorage.chk_showchkout = 'true';
		else localStorage.chk_showchkout = 'false';
	}
}

function set_ar_header(){
	if($("#active_patients").outerWidth()!=null){
		var tbl_width = $("#active_patients").outerWidth();
		var tableOffset = $("#active_patients").position().top;
		var $header = $("#active_patients > thead").clone(true);
		var $fixedHeader = $("#active_patients_header").append($header);
		$("#active_patients_header").attr("class", $("#active_patients").attr("class"));
		$("#active_patients").css({'width':parseInt(tbl_width-26)+"px"});

		if($("#active_patients_header tr").length==2){
			$("#active_patients_header, #active_patients").css({'width':tbl_width+"px"});
		}else{
			$("#active_patients").css({'width':parseInt(tbl_width-26)+"px"});
		}

		var tr_in = $("#active_patients_header tr").length-1;
		//console.log($("#active_patients tr").length);
		var td = $("#active_patients tr").eq(tr_in).find("td");
		var th = $("#active_patients_header tr").eq(tr_in).find("td");
		td.each(function(indx){
			//console.log(this.innerText, $(this).outerWidth(), th.eq(indx).html());
			var tw = $(this).outerWidth();
			th.eq(indx).css({"width":tw+"px"});
		});
	}
	
	$("#box_extended_ptlist .div_group_container").bind("scroll", function() {
		var offset = $(this).scrollTop();
		if (offset >= tableOffset ) {
			$fixedHeader.css({'top':offset}).show();//
		}
		else if (offset < tableOffset) {
			$fixedHeader.hide();
		}
	});

}


</script>
</head>
<body scroll="no">
<input type="hidden" id="active_pro" name="active_pro" value="<?php echo $act_pro_id;?>">
<input type="hidden" id="active_fac" name="active_fac" value="<?php echo $act_fac_id;?>">
<input type="hidden" id="view_mode" name="view_mode" value="<?php echo $view_mode;?>">
<input type="hidden" id="sort_by" name="sort_by" value="">
<input type="hidden" id="sort_order" name="sort_order" value="">
<div id="topbar">
	<table class="table_collapse w100per"<?php echo $GLOBALS['gl_browser_name']=='ipad' ? ' style="font-size:80%;"' : '';?>>
		<tr>
			<td style="width:60px;">Facility</td>
			<td style="width:<?php echo $GLOBALS['gl_browser_name']=='ipad' ? 150 : 240;?>px;">
			<select name="sel_facility" id="sel_facility" style="width:85%;" onChange="load_pts(this.value, 'get');">
				<option value="">Select Facility</option>
				<?php echo $iMMLogic->getDDFacs($act_fac_id);?>
			</select>
            <input type="hidden" name="hidd_sel_provider" id="hidd_sel_provider" value="<?php echo $dd_prov_id;?>">
			</td>
            
            <td style="width:65px;">Provider</td>
			<td style="width:160px;">
			<select name="sel_provider" id="sel_provider" style="width:85%;" onChange="javascript:load_pts('get', this.value);" multiple>
				<option value="">Select Physician</option>
				<?php echo $iMMLogic->getDDProvs($dd_prov_id);?>
			</select>
			</td>
			<td style="width:auto; text-align:center;" class="textb">
				<?php echo date("M d, Y");?>
                <span class="alignCenter ml10 mt5" id="div_now">&nbsp;</span>
            </td>
            <td style="width:10px;"><input type="checkbox" class="form-control" id="chk_showchkout" value="1" onClick="saveAllAptChkState(this);getSchData(this);"></td>
            <td style="width:200px;" class="alignLeft"><label for="chk_showchkout"> Show all appointments</label></td>
			<td style="width:170px;">
				<span class="icon20 icon20_close fr ml5" title="Close iMedicMonitor"></span>
				<span class="icon20 icon20_checked fr" title="View Checked-out Patients"></span>
                <select class="btn_normal" name="changeView" id="changeView" onChange="switchView(this.value);" style="width:85px;">
               	    <option value="normal">Normal view</option>
                    <option value="room">Room view</option>
                    <option value="extended" selected>Extended view</option>
                </select>
			</td>
		</tr>
	</table>
</div>

<div id="scroll_bar" style="overflow:hidden;">
	<marquee style="width:50%;" class="fr" scrollamount="1" scrolldelay="20" truespeed>checked-in patients here....</marquee>
    <div style="width:50%;" id="prov_tabs_div"></div>
    </div>

<div class="m5" style="overflow:hidden;">
	<div id="box_extended_ptlist" class="section">
		<div class="section_header">Active Patient List</div>
        
        <div id="processing_image" style="text-align:center; background-color:#FFF; opacity:30%;position:absolute; height:900px; width:100%;"><img src="../css/images/processing.gif"></div>
        	
        <div class="div_group_container">
        	<table id="active_patients_header" class="w100per table_collapse" style="top:0; display:table;">
             <thead>
             </thead>
            </table> 
            

            <table id="active_patients" class="w100per table_collapse">
             <thead>
              <tr>
               <th style="width:60px;">#</th>
<?php if($iMMColList[1]==1){?>               <th style="width:150px;">Patient Name</th><?php }?>
<?php if($iMMColList[2]==1){?>               <th style="width:auto; cursor:pointer" title="Click to Sort" onClick="imon_sortby(this,'appt_type')">Appt. Type</th><?php }?>
<?php if($iMMColList[3]==1){?>               <th style="width:105px; cursor:pointer" title="Click to Sort" onClick="imon_sortby(this,'doctor')">Dr. Initials</th><?php }?>
<?php if($iMMColList[4]==1){?>               <th style="width:95px;">Appt. Time</th><?php }?>
<?php if($iMMColList[5]==1){?>               <th style="width:95px;">Arrival</th><?php }?>
<?php if($iMMColList[6]==1){?>               <th style="width:95px;">Check-In</th><?php }?>
<?php if($iMMColList[7]==1){?>               <th style="width:95px;">Front-Desk<br>Time</th><?php }?>
<?php if($iMMColList[8]==1){?>				 <th style="width:50px;">FSh<br>Y/N</th><?php }?>
<?php if($iMMColList[9]==1){?>				 <th style="width:90px;">Appt/Arrival<br>to Now</th><?php }?>
<?php if($iMMColList[10]==1){?>				 <th style="width:90px;">Check-In<br>to Now</th><?php }?>
<?php if($iMMColList[11]==1){?>               <th style="width:6%;">Work-Up<br>with Tech</th><?php }?>
<?php if($iMMColList[12]==1){?>               <th style="width:120px;">Tech Room</th><?php }?>
<?php if($iMMColList[13]==1){?>               <th style="width:100px;">Subwait Time</th><?php }?>
<?php if($iMMColList[14]==1){?>               <th style="width:120px;">Total Tech<br>Work-Up Time</th><?php }?>
<?php if($iMMColList[15]==1){?>               <th style="width:95px;">Dilation<br>Time</th><?php }?>
<?php if($iMMColList[16]==1){?>               <th style="width:95px;">Total<br>Subwait Time</th><?php }?>
<?php if($iMMColList[17]==1){?>               <th style="width:95px;">Doctor<br>Start Time</th><?php }?>
<?php if($iMMColList[18]==1){?>               <th style="width:auto;">Doctor Room</th><?php }?>
<?php if($iMMColList[19]==1){?>               <th style="width:95px;">Doctor<br>End Time</th><?php }?>
<?php if($iMMColList[20]==1){?>               <th style="width:95px;">Doctor in<br>Room Time</th><?php }?>
<?php if($iMMColList[21]==1){?>               <th style="width:80px;">Checked Out</th><?php }?>
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

/*****CONTEXT MENU JS scring*********/
function attach_new_context_menu2(obj11){
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
							foreach($room_array as $room_name=>$room_id){?>
								"room_<?php echo $room_id;?>": {"name": "<?php echo $room_name;?>"}
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

<!-- MESSAGES EDITOR -->
<div class="msgEditor bg1">
	<textarea class="m5" cols="20" rows="2" style="height:50px; width:250px;"></textarea>
	<input type="hidden" name="hidd_sch_id" id="hidd_sch_id" value="">
	<input type="button" class="btn_ok m5" value="Update" onClick="saveMessages();" /> &nbsp; &nbsp; &nbsp;
	<input type="button" class="btn_normal m5" value="Cancel" onClick="hideEditMessages();" />
</div>


<!-- DILATION TIMER EDITOR -->
<div class="dilTimerEditor bg1">
	<b>&nbsp;Duration in Minutes</b> (Time remaining to dilate)<br>	
	<input type="text" class="m5" style="width:30px;" name="text_timerMinute" id="text_timerMinute" />
	<input type="hidden" name="hidd_dt_sch_id" id="hidd_dt_sch_id" value="" />
	<input type="button" class="btn_ok m5" value="Update" onClick="saveDilTimer();" /> &nbsp; &nbsp; &nbsp;
	<input type="button" class="btn_normal m5" value="Cancel" onClick="hideDilTimerForm();" />
</div>
<table id="record_drag_container" style="background-color:#FFF;" border="1" bordercolor="#ccc"></table>
<script type="text/javascript">

/********adding window minimum resizing*********/
function resizeToMinimum(rw,rh){
    w=rw>window.outerWidth?rw:window.outerWidth;
    h=rh>window.outerHeight?rh:window.outerHeight;
    window.resizeTo(w, h);
	
	/***SETTING WINDOW POSTION***/
	brow = get_browser();
	if(brow=='ie'){//Y==vertical; X=horizontal;
		scr_h = screen.height;
		scr_w = screen.width;
		//alert(scr_w+'::'+scr_h+'::'+window.screenY);
		//console.log(window.screenX+'+'+rw+'='+scr_w);
		win_top = window.screenX;
		win_left = window.screenY;
		do_win_move = false;
		if(window.screenX+rw>scr_w){
			win_top = scr_w-rw;
			do_win_move = true;
		}
		if(window.screenY+rh>scr_h){
			win_left = scr_h-rh;
			do_win_move = true;
			    
		}
		if(do_win_move){
			window.moveTo(win_top,win_left);
			window.focus();
		}
	}
	
	doResize=false;
};

var doResize = false;
if(arDim1['w'] < 1500){arDim1['w'] = 1500;doResize=true;}
if(arDim1['h'] < 500){arDim1['h'] = 500;doResize=true;}
if(doResize) {resizeToMinimum(arDim1['w'],arDim1['h']);doResize=false;}
</script>
</body>
</html>
