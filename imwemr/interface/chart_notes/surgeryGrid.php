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

File: surgeryGrid.php
Purpose: This file provides Surgery section in work view.
Access Type : Direct
*/
	$surgery_i = 0;
	if(!isset($include_file_var)){
		//$include_file_var is used to determine whether this file is included in another file or not so that following files are not included multiple time
		require_once('../../config/globals.php');
		$library_path = $GLOBALS['webroot'].'/library';
		include_once $GLOBALS['srcdir']."/classes/common_function.php";
		include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
		include_once $GLOBALS['srcdir']."/classes/saveFile.php";
		$glaucoma_obj = New ChartGlucoma($_SESSION['patient']);
	}
	
	// --------BEGIN SYNC GLUCOMA SX WITH LIST MEDS---------------
$qry_gld = "SELECT 
		glucoma_main.glucomaId as glucomaId,
		glucoma_main.activate, 
		glucoma_grid.glucomaId as glucoma_id,
		glucoma_grid.id,
		glucoma_grid.medication as title,
		glucoma_grid.dateStart as begdate,
		glucoma_grid.dateStop as enddate,
		glucoma_grid.reason as med_comments,
		glucoma_grid.site as sites,
		glucoma_grid.sig,
		glucoma_grid.list_id,
		glucoma_grid.allergy_status
		FROM glucoma_main 
		JOIN glucoma_grid ON glucoma_grid.glucomaId = glucoma_main.glucomaId 
		WHERE glucoma_main.patientId = '".$glaucoma_obj->pid."'
		AND glucoma_main.activate = '1' 
		AND glucoma_grid.typeGrid = 'Surgery' 
		ORDER BY glucoma_grid.med_type desc,glucoma_grid.medication, glucoma_grid.dateStart DESC,glucoma_grid.dateStart DESC";	
$res_gld = @imw_query($qry_gld);

while($row_gld = imw_fetch_assoc($res_gld)){
	
	$qry_lst="select * from lists where pid='".$glaucoma_obj->pid."' AND (type='6') AND allergy_status='Active' AND title = '".$row_gld['title']."'";
	$res_lst = @imw_query($qry_lst);
	if(imw_num_rows($res_lst)<=0){
		$row_lst = imw_fetch_assoc($res_lst);
		$sql = "INSERT INTO lists
			(
			id, date, title, begdate, enddate, med_comments, type, sites, sig, allergy_status, pid, compliant)
			VALUES
			(
			NULL,'".date('Y-m-d H:i:s')."' ,
			'".$row_gld['title']."','".$row_gld['begdate']."',
			'".$row_gld['enddate']."','".$row_gld['med_comments']."',
			'6','".$row_gld['sites']."','".$row_gld['sig']."',
			'".$row_gld['allergy_status']."','".$glaucoma_obj->pid."','1'
			) ";
		imw_query($sql);
	}
}

	//
	$arrSearch = array();
	// Ocu Surgery
	$sql = "select id,title,type,begdate,
								if((DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0') && (YEAR(STR_TO_DATE(begdate,'%Y-%m-%d'))='0000') && (MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0'),'', 
								 if((DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0') && (MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0'),date_format(STR_TO_DATE(begdate,'%Y-%m-%d'), '%Y'), 
										if(MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' OR MONTH(STR_TO_DATE(begdate,'%Y-%m-%d'))='0',date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%Y'), 
											 if(DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='00' or DAY(STR_TO_DATE(begdate,'%Y-%m-%d'))='0',date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%m-%Y'), date_format(STR_TO_DATE(begdate,'%Y-%m-%d'),'%m-%d-%Y') ))))as begdate1 
								 ,referredby,comments,sites from lists where pid='".$glaucoma_obj->pid."' AND type='6' and allergy_status != 'Deleted' order by STR_TO_DATE(begdate, '%Y-%m-%d') desc,id desc";
	$rez = sqlStatement($sql);
	for($j=0;$row=sqlFetchArray($rez);$j++)
	{
		$tmpSurgery = $row["title"];
		$tmpsite = $row["sites"];
		$tmpDtSt = (!empty($row["begdate1"]) && (preg_replace('/[^0-9]/','',$row["begdate1"]) != "00000000")) ? ($row["begdate1"]) : "" ;
		$tmpReason = $row["comments"];
		
		//if(!in_array(strtolower($tmpSurgery), $arrSearch)){
			$elem_listId = "elem_listId_".($j+1);
			$$elem_listId = $row["id"];
			$elem_surgeryId = "elem_surgery_".($surgery_i+1);
			$$elem_surgeryId = $tmpSurgery;
			$elem_siteId = "elem_site_".($surgery_i+1);
			$$elem_siteId = $tmpsite;
			$elem_dateStartId = "elem_dateStart_".($surgery_i+1);
			$$elem_dateStartId = $tmpDtSt;
			$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".($surgery_i+1);
			$$elem_reasonDiscontinuedId = $tmpReason;
			$arrSearch[] = strtolower($$elem_surgeryId);
			$surgery_i++;
		//}	
	}	
	
	$countR = $surgery_i; // Total Records
?>	
<!DOCTYPE html>
<html>
<head>
<?php if(!isset($include_file_var)){ ?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Surgery Grid</title>
	<!-- Bootstrap -->
	<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
	<!-- Bootstrap Selctpicker CSS -->
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
	<!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<!-- DateTime Picker CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/gfs.css"/>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]--> 
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
	<!-- jQuery's Date Time Picker -->
	<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
	<!-- Bootstrap -->
	<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
	
	<!-- Bootstrap Selectpicker -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/amcharts/amcharts.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/amcharts/serial.js" type="text/javascript"></script>
	<style>
		.process_loader {
			border: 16px solid #f3f3f3;
			border-radius: 50%;
			border-top: 16px solid #3498db;
			width: 80px;
			height: 80px;
			-webkit-animation: spin 2s linear infinite;
			animation: spin 2s linear infinite;
			display: inline-block;
		}
		.adminbox{min-height:inherit}
		.adminbox label{overflow:initial;}
		.adminbox .panel-body{padding:5px}
		.adminbox div:nth-child(odd) {padding-right: 1%;}
		.od{color:blue;}
		.os{color:green;}
		.ou{color:#9900cc;}
		.checkbox label::after{padding-top:0px}
	</style>
	<?php } ?>
</head>	
<script>
function checkDateFormatTest(obj)
{	
	checkdate(obj)
}
function surgery_addRow()
{
	var objTbl = document.getElementById("tblSurgery");
	//objTbl.style.backgroundColor='#d9e4f2';
	var lastRow = objTbl.rows.length;	
	var iteration = lastRow+1;
	var row = objTbl.insertRow(lastRow);
	//FirstCell
	var cellFirst = row.insertCell(0);
	var elemSurgery = document.createElement("input");
	elemSurgery.type = "text";
	elemSurgery.name = "elem_surgery_"+iteration;
	elemSurgery.id = "elem_surgery_"+iteration;
	//elemSurgery.size = 15;
	elemSurgery.value = "";
	elemSurgery.className = "form-control";
	cellFirst.appendChild(elemSurgery);
	cellFirst.style.paddingLeft='5px';
	//Second Cell
	var cellSec = row.insertCell(1);
	var elemDateStart = document.createElement("input");
	elemDateStart.type = "text";
	elemDateStart.name = "elem_site_"+iteration;
	elemDateStart.id = "elem_site_"+iteration;
	//elemDateStart.size = 2;
	elemDateStart.className = "form-control";
	elemDateStart.value = "";
	elemDateStart.onblur = checkDateFormatTest;	 
	cellSec.appendChild(elemDateStart);
	cellSec.style.paddingLeft='5px';
	//Third Cell
	var cellSec = row.insertCell(2);
	var elemDateStart = document.createElement("input");
	elemDateStart.type = "text";
	elemDateStart.name = "elem_dateStart_"+iteration;
	elemDateStart.id = "elem_dateStart_"+iteration;
	//elemDateStart.size = 10;
	elemDateStart.className = "form-control";
	elemDateStart.value = "";
	elemDateStart.onblur = checkDateFormatTest;	 
	cellSec.appendChild(elemDateStart);
	cellSec.style.paddingLeft='5px';
	
    //Forth Cell
	var cellForth = row.insertCell(3);
	var elemReason = document.createElement("textarea");	
	elemReason.name = "elem_reasonDiscontinued_"+iteration;
	elemReason.id = "elem_reasonDiscontinued_"+iteration;
	elemReason.rows = 1; //rows=\"1\" cols=\"32\"
	//elemReason.cols = 44;
	elemReason.value = "";
	elemReason.className = "form-control";
	cellForth.appendChild(elemReason);	
	cellForth.style.paddingLeft='5px';
}
function surgery_removeRow()
{
  var tbl = document.getElementById('tblSurgery');
  var lastRow = tbl.rows.length;
  if (lastRow > 2) tbl.deleteRow(lastRow - 1);
}

</script>

<body style="background:#fff">
<!-- Surgery -->	
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
<div style="width:582px;height:136px;overflow:scroll;">
<?php } ?>
	<form name="frmSurgery">
	<table id="tblSurgery" class="table table-bordered table-striped">
		<tr class="grythead">
			<th>Name</th>	
			<th>Site</th>	
			<th>Date</th>	
			<th>Comments</th>	
		</tr>
		<?php
		$lm=4;
		$lm = ($countR > $lm)? $countR : $lm;
		for($i=0;$i<$lm;$i++)
		{
			$elem_listId = "elem_listId_".($i+1);
			$elem_list_id = $$elem_listId;
			$elem_surgeryId = "elem_surgery_".($i+1);
			$elem_surgery = $$elem_surgeryId;
			$elem_siteId = "elem_site_".($i+1);
			$elem_site_int = $$elem_siteId;
			$elem_dateStartId = "elem_dateStart_".($i+1);
			$elem_dateStart = $$elem_dateStartId;
			$elem_dateStoppedId = "elem_dateStopped_".($i+1);
			$elem_dateStopped = $$elem_dateStoppedId;
			$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".($i+1);
			$elem_reasonDiscontinued = $$elem_reasonDiscontinuedId;
			$elem_gridId = "elem_grid_".($i+1);
			$elem_grid = $$elem_gridId;		
			$surg_sites="";
			if($elem_site_int==1){
				//OS
				$surg_sites="OS";
			}
			if($elem_site_int==2){
				//OD
				$surg_sites="OD";
			}
			if($elem_site_int==3){
				//OU
				$surg_sites="OU";
			}
			if($elem_site_int==4){
				//PO
				$surg_sites="PO";
			}	

			if(!empty($elem_dateStart) && $elem_dateStart != '0000-00-00') {
				$elem_dateStart = date(phpDateFormat(), strtotime(str_replace('-', '/', $elem_dateStart)));
			}
			
			echo "<tr>";
			echo "<td><textarea name=\"".$elem_surgeryId."\" rows=\"1\" class=\"form-control\">".$elem_surgery."</textarea></td>";
			echo "<td ><input type=\"text\" name=\"".$elem_siteId."\" value=\"".$surg_sites."\"  class=\"form-control\"></td>";
			echo "<td><input type=\"text\" name=\"".$elem_dateStartId."\" value=\"".$elem_dateStart."\" onblur=\"checkDateFormatTest(this)\" class=\"form-control\"></td>";			
			echo "<td><textarea name=\"".$elem_reasonDiscontinuedId."\" rows=\"1\" class=\"form-control\">".$elem_reasonDiscontinued."</textarea></td>";
			echo "</tr>";	
			echo "<input type=\"hidden\" name=\"".$elem_gridId."\" value=\"".$elem_grid."\">";	
			echo "<input type=\"hidden\" name=\"".$elem_listId."\" id=\"".$elem_listId."\" value=\"".$elem_list_id."\">";
		}
	?>	
	</table>
	</form>
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
</div>
<?php } ?>
<!-- Surgery -->
</body>
</html>
