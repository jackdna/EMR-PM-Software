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

require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$dirName = 'executed_reports';
if(!is_dir($dirName)) {
	mkdir($dirName);         
}
	
$arrFacilitySel=array();
$arrDoctorSel=array();

$saved=false;
$printFile=false;

$selGroupArr =explode(',', $grp_id);
sort($selGroupArr);
$selSCArr =explode(',', $sc_name);
sort($selSCArr);
$selPhysicianArr =explode(',', $Physician);
sort($selPhysicianArr);
	
// SAVE
if(empty($submit_btn) == false){  

	$searched_criteria = $grp_id_sel.'~~'.$sc_name_sel.'~~'.$Physician_sel.'~~'.$process_sel;

	$arrMonths = explode(',', $month_options_sel);
	$arrDays = explode(',', $day_options_sel);
	$arrWeekdays = explode(',', $weekday_options_sel);
	$arrHours = explode(',', $hour_options_sel);

	$qryPrefix = "Insert INTO ";
	$colPart  = ", status='active' ";
	$where='';

	if(empty($pkId)===false){
		$qryPrefix = "Update ";
		$colPart  ='';
		$where = " WHERE id='".$pkId."'";
	}
		
	$qry= $qryPrefix." reports_crone_jobs SET user_id='".$_SESSION['authId']."',
	schedule_name='".addslashes($schedule_name)."',
	report='".$report_sel."',
	searched_criteria='".$searched_criteria."',
	executionPeriod='".$executionPeriod_sel."',
	executed='0000-00-00 00:00:00',
	next_execution_date='".date('Y-m-d')."',
	next_execution_time='".date('H:i:s')."',
	enteredDate='".date('Y-m-d H:i:s')."',
	hour_options ='".$hour_options_sel."',
	day_options ='".$day_options_sel."',
	month_options ='".$month_options_sel."',
	weekday_options ='".$weekday_options_sel."'" 
	.$colPart.$where;
	$rs = imw_query($qry);
	
	if($rs){
		$saved=true;
	}
}

$content_part='';
// GET SAVED SEARCHES

$qry="Select rse.*, DATE_FORMAT(rse.executed_on, '".get_sql_date_format()." %H:%i:%s') as 'executed_on', rcj.schedule_name, rse.executionPeriod  
FROM reports_schedules_executed as rse JOIN reports_crone_jobs as rcj ON rcj.id=rse.schedule_id ORDER BY rse.executed_on DESC";
$rs= imw_query($qry);
while($res = imw_fetch_array($rs)){
	$printFile=true;
	$pdfLink='';
	$id = $res['id'];

	$report  = ucwords(str_replace('_', ' ', str_replace(',', ', ',$res['executed_reports'])));
	$arrAllSceduleNames[$id] = strtolower($res['schedule_name']);
	$executionPeriod = ucwords(str_replace('_', ' ', $res['executionPeriod']));
	$ext = pathinfo($res['file_path'], PATHINFO_EXTENSION);
	
	
	if(strstr($res['file_path'], '/')){
		$pdfUrl = str_replace(data_path(), data_path(1), $res['file_path']);
		if($ext=='pdf'){
			$pdfLink ='<a class="text_10ab" onclick="Open_Pdf(\''.$pdfUrl.'\');" href="javascript:void(0);"><img src="../../library/images/pdf_icon[1].png" border="0">&nbsp;Click to View PDF</a>';
		}else if($ext=='csv'){
			$pdfLink ='<a class="text_10ab" onClick="download_file(\''.$res['file_path'].'\',\''.$ext.'\');" href="javascript:void(0);"><img src="../../library/images/download_file.gif" height="20px" width="20px" border="0">&nbsp;Click to Download CSV</a>';			
		}else{
			$pdfLink ='<a class="text_10ab" href="javascript:void(0);" onClick="javascript:triggerZipCreation(\''.$res['file_path'].'\', \''.$report.'\');" ><img src="../../library/images/download_file.gif" height="20px" width="20px" border="0">&nbsp;Click to Download CSV</a>';			
		}
	}else{
		$pdfLink ='<font color="#CC0000">'.$res['file_path'].'</font>';
	}
	$counter++;
	$content_part .= '
	<tr style="height:25px" id="tr_'.$id.'">
	<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:50px">'.$counter.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:200px" >'.$res['schedule_name'].'
			<input type="hidden" name="rowAllData'.$id.'" id="rowAllData'.$id.'" value="'.$rowAllData.'">
		</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:200px">'.$report.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:100px">'.$executionPeriod.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:150px">'.$res['executed_on'].'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; padding-left:5px; width:250px">'.$pdfLink.'</td>
		<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:60px;" id="statusTD'.$id.'">
			<a class="text_10ab" onclick="deleteSelectet(\''.$id.'\', \''.$res['file_path'].'\');" href="javascript:void(0);">
				<img style="border: 0px currentColor; border-image: none;" alt="Delete" src="../../library/images/del.png">
			</a>
		</td>
	</tr>';			
}


if($printFile==true){
	
	if(trim($content_part) != ''){				
		
	}
}
?>
<!DOCTYPE html>
<html>
<title>imwemr</title>
<head>
    <script type="text/javascript">
	function deleteSelectet(pos_id, fileName){
		if(pos_id!=''){
			top.fancyConfirm("Are you sure to delete this record?","", "top.fmain.deleteModifiers('"+pos_id+"', '"+fileName+"')");
		}
	}
	function deleteModifiers(pos_id, fileName) {
		frm_data = 'delId='+pos_id+'&fileName='+fileName+'&task=delete';
		$.ajax({
			type: "POST",
			url: "executed_report_ajax.php",
			data: frm_data,
			success: function(d) {
				if(d=='done'){top.alert_notification_show('Record Deleted');
				$("#tr_"+pos_id).remove();
				}
				else{top.fAlert(d+'Record deletetion failed. Please try again.');}
			}
		});
	}	
	
	function Open_Pdf(fileName){
		window.open(fileName);
	}
    </script>
</head>
<?php if(empty($content_part)===false){?>
<div class="whtbox">
	<div class="table-responsive respotable adminnw">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th style="width:50px; text-align:left;">Sr.</th>
					<th style="width:200px; text-align:left;">Schedule Name</th>
					<th style="width:200px; text-align:left;">Report</th>
					<th style="width:100px; text-align:left;">Report Duration</th>
					<th style="width:150px; text-align:left;">&nbsp;Executed On</th>
					<th style="width:250px; text-align:left;">&nbsp;View Report</th>
					<th style="width:70px; text-align:left;">&nbsp;Action</th>
				</tr>
			</thead>
			<tbody><?php echo $content_part;?></tbody>
		</table>
	</div>
</div>

<?php }else{
	echo '<div class="text-center alert alert-info">No Executed Report Results Exists.</div>';
}
?>

<div id="csvFileDataTable" style="display:none"></div>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="" />
</form>
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 

<script>
top.show_loading_image("hide");
function triggerZipCreation(completePath, report_name){
	top.show_loading_image('show');
	url='get_data.php?mode=getdata&completePath='+completePath;
	$.ajax({
	   url: url,
	   success: function(data) {
		   if(data)
		   {
				var dataArr=data.split('~');
				var is_data=dataArr[0];
				var html_data=dataArr[1];
						
				if(is_data=='1'){
					if(html_data!=''){
						$('#csvFileDataTable').html(html_data);
						var csv_value=$('#csvFileDataTable').table2CSV({delivery:'value'});
						$('#csvFileDataTable').html('');
						$('#csv_text').val(csv_value);
						$('#csv_file_name').val(report_name+'.csv');
						document.csvDownloadForm.submit();
					}
					top.show_loading_image('hide');
				}
			}
	   }
	});	
	

}

function download_file(file_path, extension){
	$('#file').val(file_path);
	$('#file_format').val(extension);
	document.csvDirectDownloadForm.submit();
}

set_header_title('Executed Reports');
</script>
</body>
</html>