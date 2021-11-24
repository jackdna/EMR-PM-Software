<?php
	/*
	File: header.php
	Coded in PHP7
	Purpose: Header Information
	Access Type: Include File
	*/
	require_once(dirname('__FILE__')."/config/config.php");
	require_once(dirname('__FILE__')."/library/classes/common_functions.php");
	require_once($GLOBALS['DIR_PATH']."/library/classes/functions.php");
	
	$wn_height = (isset($_GET['wn_height']) && intval($_GET['wn_height'])>0 && !isset($_SESSION['wn_height'])) ? ($_SESSION['wn_height'] = intval($_GET['wn_height'])) : 0;
	if(isset($_REQUEST['patient_session_id']) && $_REQUEST['patient_session_id']!=""){
		$_SESSION['patient_session_id'] = $_REQUEST['patient_session_id'];
		$_SESSION['patient_session_ins_alert'] = 0;
		$_SESSION['currentCaseid'] = "";
		$_SESSION['order_id']="";
	}
	if(isset($_REQUEST['newpt']) || isset($_REQUEST['closept'])){
		unset($_SESSION['patient_session_id']);
		unset($_SESSION['currentCaseid']);
		echo '<script>if (window.location.href.indexOf("?closept") > -1) { window.location.href = window.location.pathname; }</script>';
	}
	if($_SESSION['patient_session_id']!=""){
		$p_name_qry = imw_query("select lname,fname,mname,p_imagename, default_facility,  phone_home, phone_biz, phone_cell, preferr_contact from patient_data where id = '".$_SESSION['patient_session_id']."' ");
		$p_name_row = imw_fetch_assoc($p_name_qry);
		$patient_name_id = $p_name_row['lname'].", ".$p_name_row['fname']." ".$p_name_row['mname']." - ".$_SESSION['patient_session_id'];
		if($p_name_row['preferr_contact']==0){$pt_phone=$p_name_row['phone_home'];$prf_contact='Home: ';}
		elseif($p_name_row['preferr_contact']==1){$pt_phone=$p_name_row['phone_biz'];$prf_contact='Work: ';}
		elseif($p_name_row['preferr_contact']==2){$pt_phone=$p_name_row['phone_cell'];$prf_contact='Mobile: ';}
		
		$p_imagename=$p_name_row['p_imagename'];
		if($p_imagename==""){
			$pt_photo=$GLOBALS['WEB_PATH'].'/images/user_images/default.png';		
		}else{
			//in case of sub domain use newly define global variable
			
			if($GLOBALS['SUB_DOMAIN']){$pic_path=$GLOBALS['IMW_DIR_PATH'].'/data/'.$GLOBALS['SUB_DOMAIN'].$p_imagename;}
			else {
				$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
				$pic_path=$GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3].$p_imagename;
			}
			if(file_exists(realpath($pic_path)) ) {
				if($GLOBALS['SUB_DOMAIN']){
					$pt_photo = $GLOBALS['IMW_WEB_PATH'].'/data/'.$GLOBALS['SUB_DOMAIN'].$p_imagename;
				}else{
					$pt_photo = $GLOBALS['IMW_WEB_PATH'].'/data/'.$r8PracticeName[3].$p_imagename;
				}
			}else{
				$pt_photo = $GLOBALS['IMW_WEB_PATH'].$p_imagename;
			}
		}
	}
	
$group_qry = imw_query("select groups_new.name from groups_new 
	join facility on facility.default_group=groups_new.gro_id where facility.facility_type='1'");
$get_group = imw_fetch_array($group_qry);
?>
<?php 
	if(isset($_REQUEST['newpt']))
	{
		$default_tab="demographics/index.php?newpt";
	}
	else if($_SESSION['default_tab']=="demographics" || !check_privillage('pos') )
	{
		$default_tab="demographics/index.php";
	}else{
		$_SESSION['default_tab']="patient_interface";
		$default_tab="patient_interface/index.php";
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />

<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->


<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-ui.js?<?php echo constant("cache_version"); ?>"></script>

<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/buttons.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/notes.js"></script>
<?php 
    if(isset($_SESSION['patient_session_id']) && $_SESSION['patient_session_id']!=''){
        $optical_notes=patient_optical_notes_alert($_SESSION['patient_session_id']);
        if($optical_notes){
			$optical_notes=preg_replace("/[\n\r]/","<br>",$optical_notes);
			//$optical_notes=str_replace(PHP_EOL, '<br>', $optical_notes);
            echo '<script>top.falert(String("'.$optical_notes.'"));</script>';
        }
    }
?>
<script type="text/javascript">
var WRP = '<?php echo $GLOBALS['WEB_PATH'];?>';
var CURRENCY_SYMBOL = '<?php currency_symbol(); ?>';
function show_clock(){var C = new Date(); var h = C.getHours(); var m = C.getMinutes(); var s = C.getSeconds(); var dn = "PM"; if (h < 12) dn = "AM"; if (h > 12) h = h-12; if (h == 0) h = 12; if (h <=9 ) h = "0" + h; if (m <=9 ) m = "0" + m; if(s <= 9) s = "0" + s; var tm = h + ":" + m + ":" + s + " " + dn;document.getElementById("tick2").innerHTML = "<span style='font-weight:bold;'>" + tm + "</span>";setTimeout("show_clock()", 1000);}
function page_height(){inHeight = <?php echo $_SESSION['wn_height'];?>-292;document.getElementById("main_page_area").style.height = inHeight+"px";show_clock();}
$(document).ready(function(e){
    page_height();
	a = $("#left_navi ul li a").first().addClass('selected');
	$("#left_navi ul li a").click(function(){
		a = $("#left_navi ul li").find('a');
		$(a).each(function(){$(this).removeClass('selected');});
		$(this).addClass('selected');
	});
	
	<?php if($_SESSION['default_tab']=="demographics"){ ?>
		$('a',$('#left_navi')).removeClass('selected');
		$('#demo_id').addClass('selected');	
	<?php }else if($_SESSION['default_tab']=="patient_interface"){ ?>
		$('a',$('#left_navi')).removeClass('selected');
		$('#pt_interface_id').addClass('selected');	
	<?php } ?>	
	notes_load();
});

function set_new_facility(fac_id)
{
	if(fac_id!="")
	{
		var dataString = 'action=ChangeFacility&fac='+fac_id;
		$.ajax({
			type: "POST",
			url: "library/ajax.php",
			data: dataString,
			cache: false,
			success: function(response)
			{
				$("#faclity").val(response);
			}
		});
	}
}

$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});

var WindowDialog = new function() {
    this.openedWindows = {};

    this.open = function(instanceName,url,win,extra) {
		var handle=window.open(url,win,extra);

        this.openedWindows[instanceName] = handle;

        return handle;
    };

    this.close = function(instanceName) {
        if(this.openedWindows[instanceName])
            this.openedWindows[instanceName].close();
    };

    this.closeAll = function() {
		for(var dialog in this.openedWindows)
            this.openedWindows[dialog].close();
    };
};

//how to use above function for opending,closing window
//WindowDialog.open('windowName', /* arguments you would call in window.open() */);
//WindowDialog.open('anotherName', /* ... */);
// closes the instance you created with the name 'testingAgain'
//WindowDialog.close('testingAgain');

// close all dialogs
//WindowDialog.closeAll();

// OpenReleaseNote - open Release Notes Window
/*function OpenReleaseNote(){
	var ReleaseWindow = window.open('library/release_note.php','Release_Notes_Optical','width=820,height=575');
	ReleaseWindow.document.body.focus();
}*/
$(function(){
	$(".release-note").on('click',function(){
		o = $('#div_version_release_doc');
		o.load('library/release_note.php?rand='+Math.random(),'',function(){
			ww = $(window).width();
			dw = o.width();
			o.css("position","absolute");
			o.css("top", Math.max(0, (($(window).height() - o.outerHeight()) / 2) + $(window).scrollTop()) + "px");
			o.css("left", Math.max(0, (($(window).width() - o.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
			o.fadeIn();
		});	
	});
    $("#board").draggable();
	$("#note_close").on('click',function(){
		$("#board").hide();
	});
	$("#sticky_notes").on('click',function(){
		$("#board").show();
	});
	$("#note_save").on('click',function(){
		notes_add();
	});
});
</script>
</head>
<body onUnload="WindowDialog.closeAll()">
<div id="modal-window" style="position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; z-index: 1050; overflow: auto; display:none">
<div class="modal-box modal-size-normal" style="position: absolute; top: 50%; left: 50%; margin-top: -102.5px; margin-left: -280px;">
<div class="modal-inner"><div class="modal-title">
<h3 id="modal-window-title">imwemr</h3>
<a class="modal-close-btn" onClick="closeMe()"></a></div>
<div id="modal-window-detail" class="modal-text">detail will be here</div>
<div class="modal-buttons">
<a class="modal-btn" onClick="closeMe()">Cancel</a>
<a class="modal-btn btn-light-blue" onClick="submitMe();">Confirm</a>
</div></div></div></div>
<!--code related to sticky notes starts here-->
	<div id="board" class="note">
		<div class="note_header">Manage Notes 
		<select name="disp_note" id="disp_note" onChange="notes_load();">
		<?php if($_SESSION['patient_session_id']){ ?>
		<option value="<?php echo $_SESSION['patient_session_id'];?>"><?php echo $patient_name_id; ?></option>
		<? }?>
		<option value="All">All Notes</option>
		</select>
		<span class="loading" id="note_loader">loading...</span>
		<span class="close" id="note_close">X</span>
		</div>
		<div class="body">
			<div style="width: 100%" class="form">
				<table class="table_collapse">
					<tr>
						<td><textarea class="cnt" placeholder="Enter note description here" style="height: 30px;" id="new_sticky_note"></textarea></td>
						<?php if($_SESSION['patient_session_id']){ ?>
						<td style="width:150px; text-align: right">
						<select name="note_pt" id="note_pt">
						<option value="">Select Patient</option>
						<option value="<?php echo $_SESSION['patient_session_id'];?>"><?php echo $patient_name_id; ?></option>
						</select></td><? }?>
						<td style="width:30px; text-align: right">
						<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" name="save" id="note_save" title="Save Note" alt="Save Note" /></td>
					</tr>
				</table>
			</div>
			<div style="width: 100%" class="listing" id="note_listing">
			</div>
		</div>
	</div>
<!--code related to sticky notes end here-->
	
<div class="container">
<div class="header">
<table class="table_collapse table_cell_padd5" border="0">
	<tr>
    	<td>
            <table class="fr table_collapse_autoW" border="0">
                <tr>
                   <!-- <td style="width:15px;"><img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/red_flag.png" /></td>
                    <td style="width:15px;"><img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/expl_sign.png" /></td>-->
					<td style="width:350px; font-weight:bold; text-align:left;" class="user_name"><?php echo stripcslashes($get_group['name']); ?>
					<span class="sccont pointer" id="sticky_notes" title="Notes">0</span>
					</td>
                    <td style="width:auto; text-align:left;" class="user_name">
					<?php echo $_SESSION['authProviderName'];?></td>
					<td style="width:auto; text-align:left;" class="user_name">
						<?php $fac_name_qry = imw_query("select id, loc_name from in_location where id='".$_SESSION['pro_fac_id']."'");
							  $fac_name_row = imw_fetch_array($fac_name_qry);
						?>
						<input type="text" name="faclity" value="<?php echo $fac_name_row['loc_name']; ?>" readonly style="width:120px;" />
					</td>
                   <!-- <td style="width:100;"><a href="#"><img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/switch_user.png" border="0" /></a></td>-->
                    <td style="width:35px;"><a href="<?php echo $GLOBALS['WEB_PATH']."/login/?act=logout" ?>"><img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/logout.png" border="0" title="Logout" /></a></td>
                </tr>
            </table>

            <table class="fr table_collapse_autoW" style="clear:right;" border="0">
                <tr>
                    <!--<td style="width:15px;"><a href="#"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/head_icon1.png" /></a></td>
                    <td style="width:15px;"><a href="#"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/head_icon2.png" /></a></td>
                    <td style="width:15px;"><a href="#"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/head_icon3.png" /></a></td>
                    <td style="width:15px;"><a href="#"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/head_icon4.png" /></a></td>
                    <td style="width:15px;"><a href="#"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/head_icon5.png" /></a></td>-->
                    <td style="width:auto;"><?php include dirname('__FILE__')."/search/patient_search.php"; ?></a></td>
                </tr>
            </table>
			
			<?php
			 if($_SESSION['patient_session_id']!=""){ ?>
			 <img src="<?php echo $pt_photo; ?>" align="top" class="pt_photo" />
			 <span class="user_name" style="padding:15px 0px 0px 15px; display:inline-block;">
			 <?php echo $patient_name_id; ?><br>
			<?php 
			if($pt_phone){
			echo $prf_contact.stripslashes(core_phone_format($pt_phone)); 
			}
			?>
			</span>
			<?php
				/*Show Patient Status*/
				$sql = 'SELECT `account_status`.`status_name`, `patient_data`.`pat_account_status` FROM `patient_data` LEFT JOIN `account_status` on(`patient_data`.`pat_account_status`=`account_status`.`id`) WHERE `patient_data`.`id`='.$_SESSION['patient_session_id'];
				$resp = imw_query($sql);
				if($resp && imw_num_rows($resp) > 0)
				{
					$resp = imw_fetch_assoc($resp);
					$resp['status_name'] = ($resp['pat_account_status']=='0')?'Active':$resp['status_name'];
					
					$resp1 = splitLongString($resp['status_name'], 7, '~~~~~');
					$resp1 = explode('~~~~~', $resp1);
					$resp1 = (count($resp1) > 1) ? $resp1[0].'...':$resp1[0];
					print '<span class="user_name" style="padding:15px 0px 0px 15px; display:inline-block; vertical-align: top;" title="'.$resp['status_name'].'">A/C Status: '.$resp1.'</span>';
				}
			} ?>
        </td>
    </tr>
</table>
</div>