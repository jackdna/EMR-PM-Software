<?php
/*
File: framesdata_setup.php
Coded in PHP7
Purpose: View/Update: FramesData credentials.
Access Type: Direct access
*/
	require_once("../../config/config.php");
	
	/*Save Data*/
	if( isset($_POST['action']) && $_POST['action']=='saveData' && isset($_POST['rec_id']) && $_POST['rec_id']!='' ){
		
		$sql = "UPDATE `in_frames_data`
				SET
					`frame_user_id`='".$_POST['user_id']."',
					`szip_code`='".$_POST['szip_code']."',
					`bzip_code`='".$_POST['bzip_code']."',
					`modified_by`='".$_SESSION['authId']."',
					`modified_data_time`='".date("Y-m-d h:i:s")."'
				WHERE
					`id`='".$_POST['rec_id']."'";
		imw_query($sql);
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
</head>
<body>
<div align="center" id="day_loading" style="width:100%; display:none;">
	<div style="top:200px; left:530px; position:absolute; z-index:1000;">
		<img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" border="0">
	</div>
</div>
<div>
    <form name="frm" id="frm" action="" method="post" class="mt10">
      <div id="listing_record" style="height:370px; overflow:none;">
        <table class="table_collapse">
          <tr class="listheading">
            <td width="560"><div class="fl">Frames Data Credentials</div>
                <div class="success_msg" style="text-align:center"><?php if($msg!=""){echo $msg;} ?></div>
             </td>
          </tr>
<?php
	$loc_sql = 'SELECT 
				`id`, 
				`frame_user_id`, 
				`szip_code`, 
				`bzip_code` 
			FROM 
				`in_frames_data` LIMIT 1';
	$loc_resp = imw_query($loc_sql);
	if( $loc_resp && imw_num_rows($loc_resp) == 1 ):
		$row = imw_fetch_object($loc_resp);
		imw_free_result($loc_resp);
?>
		<tr>
			<td>
				<table width="100%" border="0">
					<tr class="odd">
						<td width="20%">Frames Data Subscriber ID</td>
						<td width="80%">
							<input autocomplete="off" type="text" name="user_id" id="user_id" value="<?php echo $row->frame_user_id; ?>" style="width:208px; height:22px;" />
							<input type="hidden" name="rec_id" id="rec_id" value="<?php echo $row->id; ?>" />
							<input type="hidden" name="action" id="action" value="saveData" />
						</td>
					</tr>
					<tr class="even">
						<td width="20%">Subscriber shipping zip code</td>
						<td width="80%">
							<input autocomplete="off" type="text" name="szip_code" id="szip_code" value="<?php echo $row->szip_code; ?>" style="width:208px; height:22px;" />
						</td>
					</tr>
					<tr class="odd">
						<td width="20%">Subscriber billing zip code</td>
						<td width="80%">
							<input autocomplete="off" type="text" name="bzip_code" id="bzip_code" value="<?php echo $row->bzip_code; ?>" style="width:208px; height:22px;" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?
	endif;
?>
        </table>
      </div>
    </form>

</div>
<script type="text/javascript">
function submitFrom(){
	document.frm.submit();
}

$(document).ready(function(){
//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>