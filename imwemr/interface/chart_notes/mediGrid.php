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
	if(!isset($include_file_var)){
		//$include_file_var is used to determine whether this file is included in another file or not so that following files are not included multiple time
		require_once('../../config/globals.php');
		$library_path = $GLOBALS['webroot'].'/library';
		include_once $GLOBALS['srcdir']."/classes/common_function.php";
		include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
		include_once $GLOBALS['srcdir']."/classes/SaveFile.php";
		$glaucoma_obj = New ChartGlucoma($_SESSION['patient']);
	}
	if(isset($_REQUEST['show_type'])){
		$show_type = $_REQUEST['show_type'];
	}
	$chk_allergy_status = $_REQUEST['chk_allergy_status'];
	if(empty($show_type) === true){
		$show_type=4;
	}
	
	
	function get_glaucoma_med(){
		$sql = "SELECT medicine_name FROM medicine_data WHERE glucoma='1' AND del_status = '0'";
		$res = imw_query($sql);
		while($row = imw_fetch_assoc($res)){
			$arrGlTypeHead[] = "'".$row['medicine_name']."'";
		}
		return $arrGlTypeHead;
	}
	function get_ocu_gl_med(){
		$sql = "SELECT medicine_name FROM medicine_data WHERE (glucoma='1' OR ocular = '1') AND del_status = '0'";
		$res = imw_query($sql);
		while($row = imw_fetch_assoc($res)){
			$arrGlOcuTypeHead[] = "'".$row['medicine_name']."'";
		}
		return $arrGlOcuTypeHead;
	}
	$strGlTypeHead = implode(',',get_glaucoma_med());
	$strGlOcuTypeHead = implode(',',get_ocu_gl_med());
	
	function is_glaucoma_med($med){
		$sql = "SELECT count(*) AS counter FROM medicine_data WHERE glucoma='1' AND medicine_name= '".$med."' AND del_status = '0'";
		$res = imw_query($sql);
		$row = imw_fetch_assoc($res);
		if($row['counter']>0) return 1;
		else return 0;
	}
	
// --------BEGIN SYNC GLUCOMA MEDS WITH LIST MEDS---------------
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
		AND glucoma_grid.typeGrid = 'Medication' 
		ORDER BY glucoma_grid.med_type desc,glucoma_grid.medication, glucoma_grid.dateStart DESC,glucoma_grid.dateStart DESC";	
$res_gld = @imw_query($qry_gld);

while($row_gld = imw_fetch_assoc($res_gld)){
	$arrBeginDate = $arrEndDate = array();
	if(strstr($row_gld['begdate'],'-') !== false){
		$arrBeginDate = explode("-",$row_gld['begdate']);
	}else if(strstr($row_gld['begdate'],'/') !== false){
		$arrBeginDate = explode("/",$row_gld['begdate']);
	}else{
		$arrBeginDate[0] = $row_gld['begdate'];
	}
	if(count($arrBeginDate) == 1){
		$row_gld['begdate'] = $arrBeginDate[0]."-00-00";
	}else if(count($arrBeginDate) == 2){
		$row_gld['begdate'] = $arrBeginDate[1]."-".$arrBeginDate[0]."-00";
	}
	else if(count($arrBeginDate) == 3){
		$row_gld['begdate'] = $arrBeginDate[2]."-".$arrBeginDate[1]."-".$arrBeginDate[0];
	}
	
	
	if(strstr($row_gld['enddate'],'-') !== false){
		$arrEndDate = explode("-",$row_gld['enddate']);
	}else if(strstr($row_gld['enddate'],'/') !== false){
		$arrEndDate = explode("/",$row_gld['enddate']);
	}else{
		$arrEndDate[0] = $row_gld['enddate'];
	}
	
	if(count($arrEndDate) == 1){
		$row_gld['enddate'] = $arrEndDate[0]."-00-00";
	}else if(count($arrEndDate) == 2){
		$row_gld['enddate'] = $arrEndDate[1]."-".$arrEndDate[0]."-00";
	}
	else if(count($arrEndDate) == 3){
		$row_gld['enddate'] = $arrEndDate[2]."-".$arrEndDate[1]."-".$arrEndDate[0];
	}
	
	$qry_lst="select * from lists where pid='".$glaucoma_obj->pid."' AND (type='4') AND allergy_status='".$row_gld['allergy_status']."' AND title = '".$row_gld['title']."'";
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
			'4','".$row_gld['sites']."','".$row_gld['sig']."',
			'".$row_gld['allergy_status']."','".$_SESSION['patient']."','1'
			) ";
		imw_query($sql);
	}
	
}

// -------	END SYNC GLUCOMA MEDS WITH LIST MEDS---------------



	// GET MASTER MEDICINE DATA FOR ALERTS
	$medRs = imw_query("Select medicine_name FROM medicine_data WHERE alert=1 AND del_status = '0'");
	$arrMedicines = array();
	while($medRes = imw_fetch_array($medRs)){
		$arrMedicines[$medRes['medicine_name']] = $medRes['medicine_name'];
	}

	$sql_med=imw_query("select medicine_name from medicine_data where glucoma='1' AND del_status = '0'");
	while($row_med=imw_fetch_array($sql_med)){
		$glucoma_med_arr[]=$row_med["medicine_name"];
	}
	
	//
	$arrDiscont = array();
	$arrStop = array();
	$arrSearch = array();
	
	$tot_rec=0;
	
	$check_data="select * from lists where pid='".$glaucoma_obj->pid."' 
				AND type='4' 
				AND allergy_status='Active' 
				AND (enddate >= '".date('Y-m-d')."' OR enddate = '0000-00-00' OR enddate = '' OR enddate IS NULL)
				ORDER BY allergy_status, begdate DESC";
	
	if($_REQUEST['chk_allergy_status'] == "dis_") {
		$check_data="SELECT li.* FROM lists li
					JOIN medicine_data md ON REPLACE(li.title,'* Ocular','') = md.medicine_name
					where pid='".$glaucoma_obj->pid."' AND type='4' AND (allergy_status='Discontinue' OR allergy_status='Stop' OR (allergy_status='Active' AND enddate < '".date('Y-m-d')."' AND enddate != '0000-00-00' AND enddate != '' AND enddate IS NOT NULL))
					AND md.glucoma='1' 
					AND md.del_status = '0'
					ORDER BY allergy_status, begdate DESC";
	}
	$resList =  imw_query($check_data);
	foreach($arrRes1 as $arrList){
		$arrListMed[] = "'".$arrList['title']."'";
	}
	$tot_rec = imw_num_rows($resList);
	$j = 0;
	while($row = @imw_fetch_array($resList)){
		$tmp_med = $row["title"];
		
			$tmp_dtSt = (!empty($row["begdate"]) && ($row["begdate"] != '0000-00-00')) ? get_date_format($row["begdate"]) : "" ;
			$tmp_dtEd = (!empty($row["enddate"]) && ($row["enddate"] != '0000-00-00')) ? get_date_format($row["enddate"]) : "" ;
			$tmp_reason = $row["med_comments"];
			
			$tmp_sts = strtoupper($row["allergy_status"]);
			$tmp_compliant = $row["compliant"];
			$tmp_date = $row["date"];
			$tmp_site = $row["sites"];
			$tmp_sig = $row["sig"];
			$tmp_id = $row["id"];
			
				$elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($j+1);
				$$elem_listId = $tmp_id;	
	
			//if(!in_array(strtolower($tmp_med),$arrSearch)){	
					
				$elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($j+1);
				$$elem_siteId = $tmp_site;	
				$elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($j+1);
				$$elem_sigId = $tmp_sig;	
				$elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($j+1);
				$$elem_medicationId = $tmp_med;				
				$elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateStartId = $tmp_dtSt;
				$elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateStoppedId = $tmp_dtEd;
				$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($j+1);
				$$elem_reasonDiscontinuedId = $tmp_reason;				
				$elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($j+1);
				$$elem_statusId = $tmp_sts;		
				$elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($j+1);
				$$elem_compliantId = $tmp_compliant;
				$elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateId = $tmp_date;		
				
				$arrSearch[] = strtolower($tmp_med);
				$arrTypeHead[$tmp_med] = "'".$tmp_med."'";
				
				$j++;
	}
	
	
	$check_data="SELECT li.* FROM lists li
					JOIN medicine_data md ON REPLACE(li.title,'* Ocular','') = md.medicine_name
					where pid='".$glaucoma_obj->pid."' AND type='4' AND (allergy_status='Discontinue' OR allergy_status='Stop' OR (allergy_status='Active' AND enddate < '".date('Y-m-d')."' AND enddate != '0000-00-00' AND enddate != '' AND enddate IS NOT NULL))
					AND md.glucoma='1' 
					AND md.del_status = '0'
					ORDER BY allergy_status, begdate DESC";
	$resList =  imw_query($check_data);
	foreach($arrRes1 as $arrList){
		$arrListMed[] = "'".$arrList['title']."'";
	}
	$tot_rec = imw_num_rows($resList);
	$ocular_count = $j;
	while($row = @imw_fetch_array($resList)){
		$tmp_med = $row["title"];
			$tmp_dtSt = (!empty($row["begdate"]) && ($row["begdate"] != '0000-00-00')) ? get_date_format($row["begdate"]) : "" ;
			$tmp_dtEd = (!empty($row["enddate"]) && ($row["enddate"] != '0000-00-00')) ? get_date_format($row["enddate"]) : "" ;
			$tmp_reason = $row["med_comments"];
			
			$tmp_sts = strtoupper($row["allergy_status"]);
			$tmp_compliant = $row["compliant"];
			$tmp_date = $row["date"];
			$tmp_site = $row["sites"];
			$tmp_sig = $row["sig"];
			$tmp_id = $row["id"];
			
				$elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($j+1);
				$$elem_listId = $tmp_id;					
				$elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($j+1);
				$$elem_siteId = $tmp_site;	
				$elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($j+1);
				$$elem_sigId = $tmp_sig;	
				$elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($j+1);
				$$elem_medicationId = $tmp_med;				
				$elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateStartId = $tmp_dtSt;
				$elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateStoppedId = $tmp_dtEd;
				$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($j+1);
				$$elem_reasonDiscontinuedId = $tmp_reason;				
				$elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($j+1);
				$$elem_statusId = $tmp_sts;		
				$elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($j+1);
				$$elem_compliantId = $tmp_compliant;
				$elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($j+1);
				$$elem_dateId = $tmp_date;		
				
				$arrSearch[] = strtolower($tmp_med);
				$arrTypeHead[$tmp_med] = "'".$tmp_med."'";
				
				$j++;
	}
	$dis_glaucoma_count = $j;	
	$total_meds = $j;		
	

$_SESSION['strTypeHead'] = implode(",", $arrTypeHead);

$countR = $j;
$library_path = $GLOBALS['webroot'].'/library';	
?>	
<!DOCTYPE html>
<html>
<head>
	<?php if(!isset($include_file_var)){ ?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Medical Grid</title>
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
function validateForm(){
	arrMeds = new Array();
	$('input[name^=elem_medication_]').each(function(index, element) {
		cnt = getId(this.id);
		medVal = $(this).val();
		if(medVal != ""){
			siteID = '#elem_site_'+cnt;
			if($(siteID).val() == ""){
				arrMeds[arrMeds.length] = medVal;
			}
		}
	});
	
	$('input[name^=elem_medication_dis]').each(function(index, element) {
		cnt = getId(this.id);
		medVal = $(this).val();
		if(medVal != ""){
			siteID = '#elem_site_dis_'+cnt;
			if($(siteID).val() == ""){
				arrMeds[arrMeds.length] = medVal;
			}
		}
	});
	$.unique(arrMeds);
	return arrMeds;
	if(arrMeds.length>0){
		alert("Please enter site for :\n"+arrMeds);
	}
}

function getId(str){return str.replace(/[^\d.,]+/,'');}
function checkDateFormatTest(obj){
	checkdate(obj);
}
function medigrid_addRow(show_type)
{
	var objTbl = document.getElementById("tblMedication_"+show_type);
	//objTbl.style.backgroundColor='#d9e4f2';
	var lastRow = objTbl.rows.length;
	var iteration = lastRow+1;
	var row = objTbl.insertRow(lastRow);
	//FirstCell
	var index_val=0;
	var cellFirst = row.insertCell(index_val);
	var elemSurgery = document.createElement("input");
	elemSurgery.type = "text";
	elemSurgery.name = elemSurgery.id = "elem_medication_"+show_type+iteration;
	//elemSurgery.style.width='100px';
	elemSurgery.className = "form-control";
	cellFirst.appendChild(elemSurgery);
	cellFirst.style.paddingLeft='5px';
	
	index_val=index_val+1;
	//Second Cell
	
	var cellSec = row.insertCell(index_val);
	var elemSiteStart = document.createElement("input");
	elemSiteStart.type = "text";
	elemSiteStart.name = elemSiteStart.id = "elem_site_"+show_type+iteration;
	//elemSiteStart.style.width='25px';
	elemSiteStart.className = "form-control";
	elemSiteStart.value = "";
	cellSec.appendChild(elemSiteStart);
	cellSec.style.paddingLeft='5px';
	index_val=index_val+1;
	
	//if(show_type==4){
		
		var cellSec = row.insertCell(index_val);
		var elemSigStart = document.createElement("input");
		elemSigStart.type = "text";
		elemSigStart.name = elemSigStart.id = "elem_sig_"+show_type+iteration;
		//elemSigStart.style.width='80px';
		elemSigStart.className = "form-control";
		elemSigStart.value = "";
		cellSec.appendChild(elemSigStart);
		cellSec.style.paddingLeft='5px';
		index_val=index_val+1;
		
		//Second Cell
		var cellSec = row.insertCell(index_val);
		var elemDateStart = document.createElement("input");
		elemDateStart.type = "text";
		elemDateStart.name = elemDateStart.id = "elem_dateStart_"+show_type+iteration;
		//elemDateStart.style.width='60px';
		elemDateStart.className = "form-control";
		elemDateStart.value = "";
		//elemDateStart.onblur = checkDateFormatTest;
		$(elemDateStart).bind("blur",function(){checkDateFormatTest(this);})	
		cellSec.appendChild(elemDateStart);
		cellSec.style.paddingLeft='5px';
		index_val=index_val+1;
	//}
	
	
	//Third Cell
	//if(show_type!=4){
		var cellThird = row.insertCell(index_val);
		var elemDateStopped = document.createElement("input");
		elemDateStopped.type = "text";
		elemDateStopped.name = elemDateStopped.id = "elem_dateStopped_"+show_type+iteration;
		//elemDateStopped.style.width='60px';
		elemDateStopped.className = "form-control";
		elemDateStopped.value = "";
		//elemDateStopped.onblur = checkDateFormatTest;
		$(elemDateStopped).bind("blur",function(){checkDateFormatTest(this);})	
		cellThird.appendChild(elemDateStopped);
		cellThird.style.paddingLeft='5px';
		index_val=index_val+1;
	//}
	//Forth Cell
	var cellForth = row.insertCell(index_val);
	var elemReason = document.createElement("textarea");	
	elemReason.name = elemReason.id = "elem_reasonDiscontinued_"+show_type+iteration;
	elemReason.rows = 1;
	//elemReason.style.width='165px';
	elemReason.className = "form-control";
	cellForth.appendChild(elemReason);	
	cellForth.style.paddingLeft='5px';
	
	arrGlOcuMed = new Array(<?php echo $strGlOcuTypeHead;?>);
	$('#elem_medication_'+show_type+iteration).typeahead({source:arrGlOcuMed});
}
</script>

<body style="background:#fff">
<!-- Medication -->	
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
<div style="width:582px;height:180px;overflow:scroll;">
<?php } ?>
<form name="frmMedi">
	<table id="tblMedication_<?php echo $chk_allergy_status; ?><?php echo $show_type; ?>" class="table table-striped table-bordered">
		<tr class="grythead">
			<th class="text-nowrap">Ocular Med</th>	
			<th>Site</th>	
			<th>Sig</th>	
			<th>Started</th>	
			<th>Stopped</th>	
			<th>Comments</th>	
		</tr>
	<?php
		$lm=8;		
		if($total_meds>$lm){
			$lm=$total_meds+1;
		}else{
			$lm=$lm;
		}
		if($_REQUEST['chk_allergy_status'] == "dis_")
		$readonly = " readonly ";
		else 
		$readonly = "";
		//$lm = ($countR > $lm)? $countR : $lm;		
		for($i=0;$i<$lm;$i++)
		{
			if($i >= $ocular_count && $i < $dis_glaucoma_count){
			$readonly = " readonly ";
			$dis_glaucoma = 1;
			}
			else{
			$readonly = "";
			$dis_glaucoma = 0;
			}
			$elem_dis_glaucoma = "elem_dis_glaucoma_".$chk_allergy_status.$show_type.($i+1);
			$elem_listId = "elem_listId_".$chk_allergy_status.$show_type.($i+1);
			$elem_list_id = $$elem_listId;
			$elem_medicationId = "elem_medication_".$chk_allergy_status.$show_type.($i+1);
			$elem_medication = $$elem_medicationId;
			$elem_dateStartId = "elem_dateStart_".$chk_allergy_status.$show_type.($i+1);
			$elem_dateStart = $$elem_dateStartId;
			$elem_dateStoppedId = "elem_dateStopped_".$chk_allergy_status.$show_type.($i+1);
			$elem_dateStopped = $$elem_dateStoppedId;
			$elem_reasonDiscontinuedId = "elem_reasonDiscontinued_".$chk_allergy_status.$show_type.($i+1);
			$elem_reasonDiscontinued = $$elem_reasonDiscontinuedId;
			$elem_gridId = "elem_grid_".$chk_allergy_status.$show_type.($i+1);
			$elem_grid = $$elem_gridId;
			$elem_statusId = "elem_status_".$chk_allergy_status.$show_type.($i+1);
			$elem_status = $$elem_statusId;
			$elem_compliantId = "elem_compliant_".$chk_allergy_status.$show_type.($i+1);
			$elem_compliant = $$elem_compliantId;
			$elem_dateId = "elem_date_".$chk_allergy_status.$show_type.($i+1);
			$elem_date = $$elem_dateId;
			
			$elem_siteId = "elem_site_".$chk_allergy_status.$show_type.($i+1);
			$elem_site = $$elem_siteId;
			
			$elem_sigId = "elem_sig_".$chk_allergy_status.$show_type.($i+1);
			$elem_sig = $$elem_sigId;

			$cssStyle = "color:#F00!important;";
			if(strtoupper($elem_status) == 'ACTIVE' || strtoupper($elem_status) == 'RENEW'){
				$cssStyle = "color:#390!important;"; //Green
			}
			
			$curDate = date('Y-m-d',strtotime($elem_date));
			if(strtotime($curDate) == strtotime(date('Y-m-d'))){
				$cssStyle = "color:#36F!important;"; //Blue
			}
			
			if($elem_compliant=='0' || (strtoupper($elem_status) != 'ACTIVE' && strtoupper($elem_status) != 'RENEW')){
				$cssStyle = "color:#F00!important;"; // Black
			}
			
			if(strtoupper($elem_status) == 'DISCONTINUE'){
				$trStyle= "background-color:#ff9853;";
			}else{
				$trStyle= "background-color:#D9E4F2;";
			}
			if(is_glaucoma_med($elem_medication)){
				$trStyle = "background-color:#69D200";
			}
			if($elem_medication==""){
				$cssStyle = "color:#000000;";
			}
			$elem_site_val="";
			if($elem_site==1){
				$elem_site_val="OS";
			}
			if($elem_site==2){
				$elem_site_val="OD";
			}
			if($elem_site==3){
				$elem_site_val="OU";
			}
			if($elem_site==4){
				$elem_site_val="PO";
			}
			echo "<tr style=\"$trStyle\">";
				echo "<td>
				<input type=\"hidden\" name=\"".$elem_listId."\" id=\"".$elem_listId."\" value=\"".$elem_list_id."\" $readonly><input type=\"text\" name=\"".$elem_medicationId."\" id=\"".$elem_medicationId."\" value=\"".$elem_medication."\" class=\"form-control\" style=\"".$cssStyle.";\" $readonly></td>";
				echo "<input type=\"hidden\" name=\"".$elem_dis_glaucoma."\" id=\"".$elem_dis_glaucoma."\" value=\"".$dis_glaucoma."\" style=\"$cssStyle\">";
				echo "<td><input type=\"text\" name=\"".$elem_siteId."\" id=\"".$elem_siteId."\" value=\"".$elem_site_val."\" class=\"form-control\"  $readonly></td>";
				//if($chk_allergy_status==""){	
					echo "<td><input type=\"text\" name=\"".$elem_sigId."\" id=\"".$elem_sigId."\" value=\"".$elem_sig."\" class=\"form-control\" $readonly></td>";
					$elem_dateStart_arr=explode("-",$elem_dateStart);
					if(strlen(end($elem_dateStart_arr))==4){
						$elem_dateStart_arr[2]=substr(end($elem_dateStart_arr),2,2);
						$elem_dateStart=implode("-",$elem_dateStart_arr);
					}
					echo "<td><input type=\"text\" name=\"".$elem_dateStartId."\" id=\"".$elem_dateStartId."\" value=\"".$elem_dateStart."\" class=\"form-control\" $readonly></td>";
				//}else{
					$elem_dateStopped_arr=explode("-",$elem_dateStopped);
					if(strlen(end($elem_dateStopped_arr))==4){
						$elem_dateStopped_arr[2]=substr(end($elem_dateStopped_arr),2,2);
						$elem_dateStopped=implode("-",$elem_dateStopped_arr);
					}
					echo "<td><input type=\"text\" name=\"".$elem_dateStoppedId."\" id=\"".$elem_dateStoppedId."\" value=\"".$elem_dateStopped."\"  class=\"form-control\" $readonly></td>";	
				//}
				if($chk_allergy_status!=""){
					$text_cols="45";
				}else{
					$text_cols="31";
				}
				echo "<td><textarea name=\"".$elem_reasonDiscontinuedId."\" id=\"".$elem_reasonDiscontinuedId."\" rows=\"1\" class=\"form-control\" $readonly style='overflow:auto;'>".$elem_reasonDiscontinued."</textarea></td>";
			echo "</tr>";
			echo "<input type=\"hidden\" name=\"".$elem_gridId."\" value=\"".$elem_grid."\" $readonly>";			
		}
	?>	
	</table>	
</form>	
<?php if($GLOBALS['gl_browser_name']=='ipad'){ ?>
</div>
<?php } ?>
<!-- Medication -->
</body>
</html>
<script>
$(function(){
	arrGlOcuMed = new Array(<?php echo $strGlOcuTypeHead;?>);
	$('input[name^=elem_medication_]').each(function(index, element) {
		$(element).typeahead({source:arrGlOcuMed});
	});
	arrGlMed = new Array(<?php echo $strGlTypeHead;?>);
	$('input[name^=elem_medication_dis]').each(function(index, element) {
		$(element).typeahead({source:arrGlMed});
	});
});

function medigrid_removeRow(show_type){
  var tbl = document.getElementById('tblMedication_'+show_type);
  var lastRow = tbl.rows.length;
  limit  = <?php echo (isset($total_meds) && $total_meds>2)?$total_meds:2;?>;
  if (lastRow > limit) tbl.deleteRow(lastRow - 1);
}
//validateForm();
</script>
