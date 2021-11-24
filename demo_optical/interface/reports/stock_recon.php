<?php
/*
File: stock_recon.php
Purpose: Stock Reconciliation Report Searching Criteria
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optical</title>
<?php include 'report_includes.php';?>
<script>
$(document).ready(function(){
	$("#operators").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
	$('#reason').multiSelect({noneSelected:'Select All'});
});

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({
		changeMonth: true,changeYear: true,
		dateFormat: 'mm-dd-yy',
		onSelect: function() {
		$(this).change();
		}
	});
});

function submitForm(){
	top.main_iframe.loading('block');
	var postFrmObj = window.frames["frame_result"].stockreconcileResult;
	postFrmObj.faclity.value = $("#faclity").selectedValuesString();
	postFrmObj.operators.value = $("#operators").selectedValuesString();
	postFrmObj.reason.value = $("#reason").selectedValuesString();
	/*postFrmObj.med_type.value = $("#med_type").selectedValuesString();*/
	postFrmObj.date_from.value = $("#date_from").val();
	postFrmObj.date_to.value = $("#date_to").val();
	postFrmObj.submit();
}
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/stock_rec_report.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('report_popup',url, "report_printing","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
	} catch(e) {
		location.target = "_blank";
		location.href = url;
	}
}
</script>
</head>

<body>
<div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
  <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto;">
    <div class="listheading border_top_left border_top_left">Report - Stock Reconcile</div>
    <form name="stockForm" id="stockForm" method="post" action="" style="margin:0px;">
      <table class="table_collapse m2 rptDropDown">
        <tr>
          <td style="width:180px"><select name="faclity" id="faclity" style="width:140px;">
              <option value="">Select Facility</option>
              <?php $fac_name_qry = imw_query("select id, loc_name from in_location where del_status='0' and loc_name!='' order by loc_name asc");
							  while($fac_name_row = imw_fetch_array($fac_name_qry)) {
								  $sel="";
								  if($fac_name_row['id']==$_SESSION['pro_fac_id'])
								  {
									$sel="selected";
								  } 
							  ?>
              <option value="<?php echo $fac_name_row['id']; ?>" <?php echo $sel; ?>><?php echo $fac_name_row['loc_name']; ?></option>
              <?php } ?>
            </select>
            <div class="label">Facility</div></td>
          <td  style="width:180px"><select name="operators" id="operators" style="width:140px">
              <option value="">- Select -</option>
              <?php 
						  $userOptions=''; 
						  $userRs = imw_query("select id,fname,lname from users where delete_status='0'  order by lname,fname ");
						  while($userRes=imw_fetch_array($userRs)){
							$userName='';
							if($userRes['fname']!='' || $userRes['lname']!=''){
								$userName=$userRes['lname'].', '.$userRes['fname'];
							}
							$userOptions.='<option value="'.$userRes['id'].'" >'.$userName.'</option>';
                          }
						  echo $userOptions;?>
            </select>
            <div class="label">Operator</div></td>
            <td  style="width:180px"><select name="reason" id="reason" style="width:140px">
              <option value="">- Select -</option>
              <?php 
						  $resaonRs = imw_query("select id,reason_name from in_reason where del_status='0'  order by reason_name ");
						  while($reasonRes=imw_fetch_array($resaonRs)){
							echo  $reasonRes['reason_name'];
							$reasonOptions.='<option value="'.$reasonRes['id'].'" >'.$reasonRes['reason_name'].'</option>';
                          }
						  echo $reasonOptions;?>
            </select>
            <div class="label">Reason</div></td>
        <td nowrap="nowrap" style="width:100px"><input type="text"  class="date-pick" size="9" name="date_from" id="date_from" style="height: 21px; background-size:17px 21px;" />
            <div class="label">From</div></td>
          <td  nowrap="nowrap" style="padding-left: 10px; text-align:left"><input type="text" size="9" class="date-pick" name="date_to" id="date_to" style="height: 21px; background-size:17px 21px;" />
            <div class="label">To</div></td>
            </tr>
        </table>
    </form>
  </div>
  <iframe src="stock_rec_report.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
</div>
</body>
</html>