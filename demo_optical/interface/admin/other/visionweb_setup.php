<?php
	/*
	File: misce.php
	Purpose: View/Update: Alternating Setting
	Access Type: Direct access
	*/
	ini_set('max_execution_time', 0);
	ini_set("memory_limit","3072M");
	require_once("../../../config/config.php");
	$msg_stat = "none";
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST) && $_REQUEST['vw_loc_id']>0){
		
		//---- BEGIN UPDATE RELATED ITEMS PRICE RANGE -----------
		$msg = '';
		$sel_row=imw_query("SELECT id FROM in_vision_web where vw_loc_id='".$_REQUEST['vw_loc_id']."'");
		if(imw_num_rows($sel_row)==0){
			$qry = "insert into in_vision_web SET vw_user = '".$_REQUEST['vw_user']."',vw_pass='".$_REQUEST['vw_pass']."',
			vw_ref_id='".$_REQUEST['vw_ref_id']."',modified_date='$entered_date', modified_time='$entered_time', 
			modified_by='$operator_id',vw_loc_id = '".$_REQUEST['vw_loc_id']."'";
		}else{
			$qry = "UPDATE in_vision_web SET vw_user = '".$_REQUEST['vw_user']."',vw_pass='".$_REQUEST['vw_pass']."',
			vw_ref_id='".$_REQUEST['vw_ref_id']."',modified_date='$entered_date', modified_time='$entered_time', 
			modified_by='$operator_id' WHERE  vw_loc_id = '".$_REQUEST['vw_loc_id']."'";
		}

		imw_query($qry);
		$msgCode = "1";
	}
	switch($msgCode){
		case "1":
		$msg = "Record updated successfully";
		break;
		default:
		$msg = '';
		break;
	}
	if($_REQUEST['vw_loc_id']>0){
		$vw_loc_id=$_REQUEST['vw_loc_id'];
	}else{
		$vw_loc_id=$_SESSION['pro_fac_id'];
	}
	$sql = "SELECT * FROM in_vision_web where vw_loc_id='$vw_loc_id' and vw_loc_id>0";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">

var locArray = [];
var respStatus = [];
var response = '';
var alertMessage = '';
var tempSupplierLocStatus = '';

function refresh_data(){
	document.getElementById('day_loading').style.display='block';

	/*Locations Array*/
    $('#vw_loc_id>option:not(:first-child)').each(function(){
        var key = $.trim($(this).val());
        var value = $.trim($(this).text());
        locArray[key] = value;
    });

	/*At this step We are Updating the Suppliers list Only. Lens Attributes will be updated with individual call to Supplier id*/
	var dataString = 'method=getUserProfileByLogin';
	$.ajax({
		type: "POST",
		url: "vw_refresh_data.php",
		data: dataString,
		cache: false,
        complete: function(a, b)
        {
			var refreshCallArr=[];
			var cntr=0;
            response = a.responseText;
            try
            {
                response = $.parseJSON(response);
            }
            catch(err)
            {
                top.falert('Unable to Complete Sync.');
                $('#day_loading').css('display', 'none');
				console.log(a.responseText);
                return false;
            }

            if( response.length === 0)
            {
                top.falert('Unable to Complete Sync.');
                $('#day_loading').css('display', 'none');
				console.log(a.responseText);
                return false;
            }

            alertMessage = '<strong style="font-size: 20px;">Sync Status:</strong><br /><br />';

            /*Loop through each set of locations*/
            $.each(response, function (key, obj) {

                var supplierId = key.split(',');
                var location = obj.locIds.split(',');
				 $.each(obj.suppliers, function(i, v){
					refreshCallArr[cntr] = new Array(4)
					refreshCallArr[cntr]['locId']=supplierId[0];
					refreshCallArr[cntr]['supplierLoc']=i;
					refreshCallArr[cntr]['completeLocId']=key;
					refreshCallArr[cntr]['locName']=v;
					cntr++;
				});
			 });
			
			 /*Sync data for each Suupplier Id*/
			try
			{
				tempSupplierLocStatus = '';
				//$.each(refreshCallArr, function(i, v){
				if(refreshCallArr[0])refreshCall(refreshCallArr[0]['locId'],refreshCallArr[0]['supplierLoc'], refreshCallArr[0]['completeLocId'], refreshCallArr[0]['locName'],refreshCallArr,0);
				//});
			}
			catch (err)
			{
				$.each(location, function(index, value){
					alertMessage += '<strong>'+locArray[value]+'</strong>:&nbsp;&nbsp;&nbsp;Unable to Import data.<br/>';
				});
				$('#day_loading').css('display', 'none');
				top.falert(alertMessage);
			}
        }
	});
	return true;
}

function refreshCall(locId, supplierLoc, completeLocId, locName,refreshCallArr,arrCounter)
{
    var dataString = 'method=getCatalogBySupplier&location='+locId+'&supplier='+supplierLoc+'&locName='+locName;
    $.ajax({
        type: "POST",
        url: "vw_refresh_data.php",
        data: dataString,
        cache: false,
        async: true,
        complete: function(a, b)
        {
            try
            {
				
                //var responseText = (a.responseText == '') ? 'Unable to import data.' : a.responseText;
				//fix for ecl server where ajax is returing without response data bu actually call being complete on php end successfully
				var responseText = (a.responseText == '') ? 'Record imported successfully' : a.responseText;
				
                tempSupplierLocStatus += '<strong style="font-size: 14px;">'+supplierLoc+' - '+locName+'</strong>:&nbsp;&nbsp;&nbsp;' + responseText + '<br/>';

                delete response[completeLocId].suppliers[supplierLoc];

                if (
                    Object.keys(response).length > 0 &&
                    Object.keys(response[completeLocId]).length > 0 &&
                    Object.keys(response[completeLocId].suppliers).length <= 0
                )
                {
                    response[completeLocId].locIds = response[completeLocId].locIds.split(',');
                    alertMessage += '<strong style="font-size: 18px;">';

                    $(response[completeLocId].locIds).each(function (i, v) {
                        alertMessage += locArray[v]+', ';
                    });
                    alertMessage = $.trim(alertMessage);
                    alertMessage = alertMessage.slice(0, -1);
                    alertMessage += '</strong><br/>';
                    alertMessage += tempSupplierLocStatus+'<br />';

                    delete response[completeLocId];
                    tempSupplierLocStatus = '';
                }

                if (Object.keys(response).length <= 0) {
                    $('#day_loading').css('display', 'none');
                    top.falert(alertMessage);
                }
				
				
				arrCounter++;
				if(refreshCallArr[arrCounter])refreshCall(refreshCallArr[arrCounter]['locId'],refreshCallArr[arrCounter]['supplierLoc'], refreshCallArr[arrCounter]['completeLocId'], refreshCallArr[arrCounter]['locName'],refreshCallArr,arrCounter);
            }
            catch (err)
            {
                //console.log(err.message);
                if ( response[completeLocId] !== null && typeof(response[completeLocId]) !== 'undefined' && Object.keys(response[completeLocId]).length > 0)
                {
                    response[completeLocId].locIds = response[completeLocId].locIds.split(',');
                    $(response[completeLocId].locIds).each(function (i, v) {
                        alertMessage += '<strong>' + locArray[v] + '</strong>:&nbsp;&nbsp;&nbsp;Unable to Import data.<br/>';
                    });
                    delete response[completeLocId];
                }

                if (Object.keys(response).length <= 0) {
                    $('#day_loading').css('display', 'none');
                    top.falert(alertMessage);
                }
            }

        }
    });
}

function sel_loc_data(val){
	document.getElementById('day_loading').style.display='block';
	window.location.href='visionweb_setup.php?vw_loc_id='+val;
}
</script>
</head>
<body>
<div align="center" id="day_loading" style="width:100%; display:none;">
	<div style="top:200px; left:330px; position:absolute; z-index:1000;">
		<img src="../../../images/loading_image.gif" border="0"><br>
		<span style="width:100%;">Keep Calm! It may take few minutes to an hour.</span>
	
	</div>
</div>
<div>
    <form name="frm" id="frm" action="" method="post" class="mt10">
      <div id="listing_record" style="height:370px; overflow:auto;">
        <table class="table_collapse">
          <tr class="listheading">
            <td width="560"><div class="fl">VisionWeb Settings</div>
                <div class="success_msg" style="text-align:center"><?php if($msg!=""){echo $msg;} ?></div>
             </td>
          </tr>
          
          <tr>
            <td><table width="100%" border="0">
			  <tr class="even">
                <td width="18%" class="even">Location</td>
                <td width="82%" class="even">
                <select name="vw_loc_id" id="vw_loc_id" style="width:215px; height:30px;" onChange="sel_loc_data(this.value);">
					<option value="">Select Location</option>
					<?php 
						$fac_name_qry = imw_query("select id, loc_name, hq from in_location where del_status='0' and loc_name!='' order by loc_name asc");
						while($fac_name_row = imw_fetch_array($fac_name_qry)) { 
					?>
						<option value="<?php echo $fac_name_row['id']; ?>" <?php if($fac_name_row['id']==$vw_loc_id){echo 'selected=selected';}else{echo '';}?>>
							<?php echo $fac_name_row['loc_name']; ?>
						</option>
					<?php } ?>
				</select>
              </tr>
              <tr class="even">
                <td width="18%" class="even">User Name </td>
                <td width="82%" class="even">
                <input type="text" name="vw_user" class="textbox" style="width:210px;" value="<?php echo $row['vw_user'];?>"> 
              </tr>
			  <tr class="odd">
                <td width="18%">Password </td>
                <td width="82%">
                <input type="password" name="vw_pass" class="textbox" style="width:210px;" value="<?php echo $row['vw_pass'];?>"> 
              </tr>
             <!-- <tr class="even">
                <td width="18%" class="even">Ref Id </td>
                <td width="82%" class="even">
                <input type="text" name="vw_ref_id" class="textbox" style="width:210px;" value="<?php echo $row['vw_ref_id'];?>">
              </tr>-->
			   <input type="hidden" name="vw_ref_id" class="textbox" style="width:210px;" value="ROIMEDICWARE">
            </table></td>
          </tr>
         
        </table>
      </div>
    </form>

</div>
<script type="text/javascript">
function submitFrom(){
	document.frm.submit();
}
$(document).ready(function()
{
//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","Import","top.main_iframe.admin_iframe.refresh_data();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>