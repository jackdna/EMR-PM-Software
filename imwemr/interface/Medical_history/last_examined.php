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
include_once("../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
$patient_id = $_SESSION['patient'];
$medical = new MedicalHistory($patient_id);
$library_path = $GLOBALS['webroot'].'/library';

//--- delete last examined -------
if($del_examined_id)
{
	$qry = "delete from patient_last_examined where patient_last_examined_id = '".$del_examined_id."'";
	imw_query($qry);
}

//-- get last examined details -------
$sectionNameQry = "";
$objVal = trim($_REQUEST['objVal']);
if(empty($objVal) == false){
	$sectionNameQry = "and section_name  = '".$objVal."'";
}
$qry = "select *,date_format(created_date,'".get_sql_date_format()."') as createdDate, time_format(created_date,'%h:%i %p') as createdTime
		from patient_last_examined where patient_id = '".$medical->patient_id."' and (save_or_review = '2' or save_or_review = '1') ".$sectionNameQry."
		order by created_date desc";
$sql = imw_query($qry);
$cnt = imw_num_rows($sql);
$intCounter = 0;
$j=1;

while($row = imw_fetch_assoc($sql))
{
	$nochangeSinceDate = "";
	$operator_id = $row['operator_id'];
	$patientLastExaminedId = $row['patient_last_examined_id'];
	$section_name = trim($row["section_name"]);
	$sectionComplete = $row["section_complete"];
	$sectionCompleteId = $row["section_complete_id"];
	
	$qry = "select concat(lname,', ',fname) as name, mname from users
										where id = '$operator_id'";
	$sql_1 = imw_query($qry);
	$proRes = imw_fetch_assoc($sql_1);
	$proName = trim(ucwords($proRes['name'].' '.$proRes['mname']));	
	$date_time = $row['createdDate']."  ".$row["createdTime"];
	if((empty($objVal) == false) || ($sectionCompleteId == 0 && $section_name != "complete"))
	{	
		$qryChkPatLastExaminedExits = "select id from patient_last_examined_child where master_pat_last_exam_id = '".$patientLastExaminedId."'";
		$rsChkPatLastExaminedExits = imw_query($qryChkPatLastExaminedExits);
		
		//--- get physician name -----
		
		
		$id = $row['patient_last_examined_id'];
		
		$prev_date = $qryRes[$i+1]['createdDate']."  ".$qryRes[$i+1]["createdTime"];
		$intCounter++;
		
		$examinedData .= $medical->getExaminedData($intCounter,$proName,$section_name,$date_time, $prev_date, $patient_id,$patientLastExaminedId,$nochangeSinceDate,$operator_id);
		
	}
	elseif($section_name == "complete")
	{
		$fields = "*,date_format(created_date,'".get_sql_date_format()."') as createdDate, time_format(created_date,'%h:%i %p') as createdTime";
		$extra = "and (save_or_review = '2' or save_or_review = '1') and section_complete_id = '".$patientLastExaminedId."'";
		$rsGetCompleteReviewed = get_array_records('patient_last_examined','patient_id',$medical->patient_id,$fields,$extra,'created_date','Desc');
		
		$intCounter++;
		$examinedData .= $medical->getExaminedDataForComplete($intCounter,$proName,$rsGetCompleteReviewed,$date_time,$section_name,$operator_id);
	}
}

if($cnt == 0)
{
	$examinedData = '<tr><td class="text-center bg bg-info" colspan="4">No change was observed in Section '.$objVal.'.</td></tr>';
}
?>
<script type="text/javascript">
	window.focus();
	window.onload =function()
	{
		var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
		window.resizeTo(parWidth,670);
		var t = 10;
		var l = parseInt((screen.availWidth - window.outerWidth) / 2)
		window.moveTo(l,t);
	}
		
	function del_entry(id){
		if(confirm("Sure ! you want to delete permanently ?")){
			document.getElementById("del_examined_id").value = id;
			document.examined_frm.submit();
		}
	}
	function showAudit_review(patientId,pk,tableName,categoryDesc,wh, date, prev_date){
		var parWidth = parent.document.body.clientWidth;	
		window.opener.top.popup_win('review_details.php?pId='+patientId+'&tname='+tableName+'&cat='+categoryDesc+'&pk='+pk+'&wh='+wh+'&date='+date+'&prev_date='+prev_date,'medHXAudit','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=520');
	}
	function show_review_detail(id,secName,opId,dateTime){
		var parWidth = document.body.clientWidth;								
		window.opener.top.popup_win('review_details.php?masterId='+id+'&secName='+secName+'&opId='+opId+'&dateTime='+dateTime,'medHXReviwed','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+parWidth+',height=520');
	}
	
	
	
</script>
<html>
<head>
<title>imwemr : Last Examined</title>
	<!-- Bootstrap -->
  <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">  


</head>
<body >
  	<div class="panel panel-primary">
      <div class="panel-heading">Total Last Examined <span class="badge"><?php echo $intCounter; ?></span></div>
      <div class="panel-body popup-panel-body">
      	<table class="table table-bordered table-hover table-striped scroll release-table ">	
          <thead class="header">
          	<tr class="grythead">
            	<th class="col-xs-1">#</th>
              <th class="col-xs-2">Provider Name</th>
              <th class="col-xs-6">Section Name</th>
              <th class="col-xs-3">Date</th>
           	</tr>
         	</thead>
          <tbody>
          	<?php echo $examinedData; ?>
         	</tbody>
      	</table>
     	</div>
  		<footer class="panel-footer">
      	<button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
      </footer>
    </div>
    

</body>
</html>
