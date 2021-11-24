<?php
/*
File: day_report.php
Coded in PHP7
Purpose: Day Report Searching Criteria
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

$arrSearchDates = changeDateSelection();

$curDate=date('m-d-Y');

$frameId='';
$typeRs = imw_query("select * from in_module_type where ORDER BY module_type_name asc");
while($typeRes=imw_fetch_array($typeRs)){
 if(strtolower($typeRes['module_type_name'])=='frame'){ $frameId=$typeRes['id']; }
 $typeOptions.='<option value="'.$typeRes['id'].'">'.$typeRes['module_type_name'].'</option>'; 
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<?php include 'report_includes.php';?>

<script type="text/javascript">
var frameId='<?php echo $frameId;?>';
$(document).ready(function(){
	$("#operators").multiSelect({noneSelected:'Select All'});
	$("#status").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
});

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true, dateFormat: 'mm-dd-yy'});
});

function submitForm(){
	top.main_iframe.loading('block');
	var curFrmObj = document.searchForm;
	var postFrmObj = window.frames["frame_result"].searchFormResult;
	
	postFrmObj.operators.value = $('#operators').selectedValuesString();
	postFrmObj.status.value = $('#status').selectedValuesString();
	postFrmObj.facility.value = $('#faclity').selectedValuesString();
	postFrmObj.iportal_orders.value = $('#iportal_orders').val();

	if(curFrmObj.dayReport.value == 'Daily'){
		curFrmObj.date_from.value = '<?php echo $curDate;?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Weekly'){	
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['WEEK_DATE'];?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Monthly'){
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['MONTH_DATE'];?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Quarterly'){
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['QUARTER_DATE_START'];?>';
		curFrmObj.date_to.value = '<?php echo $arrSearchDates['QUARTER_DATE_END'];?>';
	}
	postFrmObj.date_from.value = curFrmObj.date_from.value;
	postFrmObj.date_to.value = curFrmObj.date_to.value;	

	postFrmObj.submit();
}
</script>

<script>

function printcsv()
{

	var $rows = ($("#frame_result").contents().find('table tr'));
	var tmpColDelim = String.fromCharCode(11);
	var tmpRowDelim = String.fromCharCode(0);
	var colDelim = '","';
	var rowDelim = '"\r\n"';
	var csv = '"';
	var filename = 'day_report.csv';

	csv += formatRows($rows.map(grabRow)) + '"';
	
	function formatRows(rows){
		    return rows.get().join(tmpRowDelim)
		        .split(tmpRowDelim).join(rowDelim)
		        .split(tmpColDelim).join(colDelim);
	}
	
	function grabRow(i,row){
	     
	    var $row = $(row);
	    var $cols = $row.find('td'); 
	    if(!$cols.length) $cols = $row.find('th');  
	    return $cols.map(grabCol)
	                .get().join(tmpColDelim);
	}
	function grabCol(j,col){
	    var $col = $(col),
	        $text = $col.text();
	        $text = $text.trim();
	    return $text.replace('"', '""'); 
	}



	var blob = new Blob([csv]);
    var needsClick = true;
    var filename = 'export.csv';
    if ( window.webkitURL ) {
        console.log('CHROME');
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("click", null, null);

        $("<a>", {
            download: filename,
            href: webkitURL.createObjectURL(blob)
        }).get(0).dispatchEvent(evt);

    } else {

        if (document.createEvent) {
            console.log('FIREFOX');
            var link = document.createElement("a");
            link.setAttribute("href", encodeURI("data:text/csv;charset=utf-8," + csv));
            link.setAttribute("download", filename);
            var e = document.createEvent('MouseEvents');
            e.initEvent('click', true, true);
            needsClick = link.dispatchEvent(e);           
        } 

        if(needsClick) {
            console.log('IE');
            window.navigator.msSaveBlob(blob, filename); 
        }
    }


	var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);		

    downloadLink = document.createElement("a");
    downloadLink.download = 'export.csv';
    downloadLink.href = csvData;
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
		
}



function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/day_report_result.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('report_popup',url, "report_printing","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
	} catch(e) {
		location.target = "_blank";
		location.href = url;
	}
}

function DateOptions(val){
	switch(val){
		case 'Date':
		{
			$('#dateFieldControler').hide();
			$('#dateFields').show();
			$('#monthly_vals').hide();
			break;
		}
		default:
		{
			$('#dateFields').hide();
			$('#monthly_vals').hide();
			$('#dateFieldControler').show();
			if(val=='x') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			else if(val=='m') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			break;
		}
	}
}

jQ(document).unbind('keydown').bind('keydown', function (event) {
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
</script>

</head>
<body> 
    <div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto; height:80px;">
           <div class="listheading border_top_left border_top_left">Day Report</div>
           <form name="searchForm" id="searchForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr style="height:43px">
                  <td style="width:220px">
                   	<div id="dateFieldControler">
                        <select class="input_text_10" name="dayReport" id="dayReport" onChange="DateOptions(this.value);" style="width:205px;">
                            <option value="Daily" <?php if($dayReport=='Daily') echo 'selected';?> >Daily</option>
                            <option value="Weekly" <?php if($dayReport=='Weekly') echo 'selected';?>>Weekly</option>
                            <option value="Monthly" <?php if($dayReport=='Monthly') echo 'selected';?>>Monthly</option>
                            <option value="Quarterly" <?php if($dayReport=='Quarterly') echo 'selected';?>>Quarterly</option>
                            <option value="Date" <?php if($dayReport=='Date') echo 'selected';?>>Date Range</option>
                        </select>
                        <div class="label">Order Generated Date</div>
                    </div>
                    <table cellpadding="0" cellspacing="0" border="0" id="dateFields" class="hide">
                        <tr>
                            <td width="auto" nowrap="nowrap">
                                <input type="text"  class="date-pick" name="date_from" id="date_from" value="<?php echo date('m-d-Y');?>" style="width:70px" />
                                <div class="label">From</div>
                            </td>
                            <td width="110" nowrap="nowrap">
                                <img src="../../images/icon_back.png" align="right" border="0" style="cursor:pointer; margin-top:5px;" onClick="DateOptions('x');" />
                                &nbsp;<input type="text" class="date-pick" name="date_to" id="date_to" value="<?php echo date('m-d-Y');?>" style="width:70px"/>
                                <div class="label">To</div>
                            </td>
                        </tr>
                     </table>
                   </td>
                  <td style="width:210px">
                      <select name="operators" id="operators" style="width:180px">
                        <option value="">- Select -</option>
						<?php 
						  $userOptions=''; 
						  $userRs = imw_query("select id,fname,lname from users where delete_status='0' order by lname,fname");
						  while($userRes=imw_fetch_array($userRs)){
							$userName='';
							if($userRes['fname']!='' || $userRes['lname']!=''){
								$userName=$userRes['lname'].', '.$userRes['fname'];
							}
							$userOptions.='<option value="'.$userRes['id'].'" >'.$userName.'</option>';
                          }
						  echo $userOptions;?>
                      </select>
                      <div class="label">Operator(By order generated)</div>
                  </td>
				  <td style="width:210px">
				  	<select name="faclity" id="faclity" style="width:180px;">
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
                    <div class="label">Facility(From order generated)</div>
                  </td> 
				  <td style="width:210px">		
		              <select name="status" id="status" style="width:180px">
                      	<option value="">- All -</option>
                        <option value="stock_added">Stock</option>
                        <option value="pending">Orders Pending</option>
                        <option value="ordered">Orders Ordered</option>
                        <option value="received">Orders Received</option>
						<option value="notified">Orders Notified</option>
                        <option value="dispensed">Orders Dispensed</option>
						<option value="cancelled">Orders Cancelled</option>
						<option value="remake">Orders Remake</option>
						<option value="reorder">Orders Reorder</option>
						<option value="returned">Orders Returned</option>
                      </select>
                      <div class="label">Stock/Current Order Status</div>
          		 
                  </td>
                  <td style="width:auto">
		             <select name="iportal_orders" id="iportal_orders" style="width:120px">
                     	<option value="">All Orders</option>
                        <option value="iportal_orders">iPortal Orders</option>
                     </select>
                     <div class="label">All/iPortal Orders</div>
                  </td>
                </tr>
            </table>
       </form>
        </div>
        <iframe src="day_report_result.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>