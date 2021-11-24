<?php 
session_start();
include("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

//START CODE TO SAVE SX PLANNING COMMENTS
$multiPatientInWaitingIdArr = array();
$multiPatientInWaitingId 	= $_REQUEST["multiPatientInWaitingId"];
$saveRecord = false;
if($_REQUEST["hiddSave"]=="yes" && $multiPatientInWaitingId) {
	$multiPatientInWaitingId 	= "0,".$multiPatientInWaitingId;
	$chkQry 					= "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl WHERE patient_in_waiting_id IN($multiPatientInWaitingId)";
	$chkRes						=	imw_query($chkQry) or die( $chkQry.' Error found at line no. '.(__LINE__).': '.imw_error()) ;
	if(imw_num_rows($chkRes)>0) {
		while($chkRow = imw_fetch_object($chkRes)) {
			$saveWaitingId 					= $chkRow->patient_in_waiting_id;	
			$saveSxPlanningComments 		= $_REQUEST["sxPlanningComments".$saveWaitingId];
			if(trim($saveSxPlanningComments)!="") {
				$updtSxPlanningCommentsQry 	= "UPDATE patient_in_waiting_tbl SET sx_planning_comments = '".addslashes($saveSxPlanningComments)."' WHERE patient_in_waiting_id = '".$saveWaitingId."' ";
				$updtSxPlanningCommentsRes	=	imw_query($updtSxPlanningCommentsQry) or die( $updtSxPlanningCommentsQry.' Error found at line no. '.(__LINE__).': '.imw_error()) ;
				
			}
			$saveRecord=true;
		}
	}
}
//END CODE TO SAVE SX PLANNING COMMENTS
$printSxPlanningSheet = "no";
include_once('patient_license_sheet_template.php');

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PDF Management</title>
<style>
	table { border-collapse : collapse; text-align:left; width:100%; background-color:#FAF9F7; }	
	td { border:0px; }
	.mainTable { border:0px; width:1000px; font-size:14px; vertical-align:top;  margin-top:5px; margin-left:5px; }
	.font_12 { font-size:12px;}
	.font_14 { font-size:14px; }
	.font_16 { font-size:16px;}
	.font_30 { font-size:30px; }
	.height_10 { height:10px; min-height:10px; }
	.shadow { box-shadow:2px 2px 5px #ddd}
	.bordered { border:solid 1px #9FBFCC; }
	.borderRight{ border-right : solid 1px #9FBFCC; }
	
</style>
<script language="javascript">
window.focus();
function frmSubmit(){
	document.getElementById("divAjaxLoadId").style.display='inline-block';
	document.getElementById("divAjaxLoadId").innerHTML='<img src="images/ajax-loader5.gif" width="80" height="80">';
	document.patient_license_sheet.submit();
}
function frmShowAlert(){
	document.getElementById('alert_div_content2').style.display="inline-block";
	document.getElementById('alert_div_confirm1').innerHTML="Selected Planning sheet(s) saved successfully";
	document.getElementById('sx_planning_Ok_confirm').setAttribute('onClick',"page_refresh1()");
}
function page_refresh1()
{
	document.getElementById("divAjaxLoadId").style.display='none';
	document.getElementById('alert_div_content2').style.display="none";
	//top.location.reload();
}
function print_license_sheet(){
	document.forms.print_patient_license_sheet.submit();
}
</script>
<?php
$spec= "
</head>
<body>";

include("common/link_new_file.php");
?>
<span id='divAjaxLoadId' style="position:absolute; top:50px; left:400px; display:none;"></span>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr valign="top" height="20" bgcolor="#F8F9F7" class="text_orangeb"  >
        <td  class="text_10b alignCenter"  style="padding-left:5px; background-image:url(<?php echo $bgHeadingImage;?>);" >Sx Planning Sheet</td>
    </tr>
</table>
<form name="print_patient_license_sheet" action="print_patient_license_sheet.php" method="post" target="_blank">
	<input type="hidden" name="multiPatientInWaitingId" id="multiPatientInWaitingId" value="<?php echo $_REQUEST["multiPatientInWaitingId"];?>">
</form>
	
<form name="patient_license_sheet" action="patient_license_sheet.php" method="post">
	<input type="hidden" name="hiddSave" id="hiddSave" value="yes">
	<input type="hidden" name="multiPatientInWaitingId" id="multiPatientInWaitingId" value="<?php echo $_REQUEST["multiPatientInWaitingId"];?>">
    
    <div id="sx_plan_div" style="height:590px; overflow:auto; overflow-x:hidden; border-bottom:1px solid #9FBFCC;">
		<?php
        $Html		=	'' ;
        include_once('patient_license_sheet_common.php');
        if($Html) {
            echo $Html;
        }
        else {?>
            <table cellpadding="0" cellspacing="0" width="100%">
                    <tr valign="top" height="20" bgcolor="#F8F9F7" class="text_10b"  style="font-size:11px; ">
                            <td  align="center">No Record Found</td>
                    </tr>
            </table>
        <?php
        }?>
    </div>
    <?php    
	if($Html){?>
		<table class="text_10 table_collapse alignCenter" style="border:none;" >
			<tr>
				<td class="text_10b alignCenter" style="height:8px; padding-top:10px;">
					<div>
						<a href="#" onClick="frmSubmit();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img id="saveBtn" src="images/save.jpg" style="border:none;" alt="Save" /></a>
						<a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;"  alt="Close" onClick="window.close();"/></a>
						<a href="#" onClick="print_license_sheet();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('printBtn','','images/print_hover1.jpg',1)"><img id="printBtn" src="images/print.jpg" style="border:none;" alt="Print" /></a>                   
                    </div>
				</td>
			</tr>
		</table>
	<?php
	}
    ?>
    <div id="alert_div_content2">
      <p id="alert_div_confirm"></p>
      <p id="alert_div_confirm1"></p>
      <a href="#" class="io_Sync_Ok io_sync_ok_1" id="sx_planning_Ok_confirm"></a>
    </div>
	<?php
    if($saveRecord==true) {
		echo '<script>frmShowAlert();</script>';
	}?>
</form>
</body>
</head>
</html>