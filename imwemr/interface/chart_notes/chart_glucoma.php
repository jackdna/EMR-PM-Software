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

File: chart_glucoma.php
Purpose: This file is used for Glucoma Flow Sheet.
Access Type : Direct
*/

require_once('../../config/globals.php');
extract($_REQUEST);
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';
include_once $GLOBALS['srcdir']."/classes/common_function.php";
include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
include_once $GLOBALS['srcdir']."/classes/SaveFile.php";
include_once $GLOBALS['srcdir']."/classes/work_view/PtIop.php";
include_once $GLOBALS['srcdir']."/classes/work_view/wv_functions.php";

$pid = $_SESSION['patient'];
$auth_id = $_SESSION['authId'];

$glaucoma_obj = New ChartGlucoma($pid);

//Returns Graph data on ajax request
if(isset($_REQUEST['get_charts']) && isset($_REQUEST['ajax_request'])){
	//$graph_data = $glaucoma_obj->get_graph_data($_REQUEST);
	//echo json_encode($graph_data);
	$oic = new PtIop($pid);
	$oic->getGraph();	
	exit();
}

$patientStatus = $glaucoma_obj->getPatientStatus($glaucoma_obj->pid);

// Default Date of Activation
$elem_dateActivation = get_date_format(date("Y-m-d"));
$elem_activate = "-1";	

if(isset($_GET["mode"]) && !empty($_GET["mode"])){
	$elem_activate = $_GET["mode"];   
}

// Transfer Chart Notes Reading
$glaucoma_obj->getChartNotesReading($glaucoma_obj->pid);

//if Past Glucoma Record exists	
$sql = "SELECT dateActivation,activate
		FROM glucoma_main 
		WHERE patientId = '".$glaucoma_obj->pid."' 
		AND activate = '1' ";
$row = sqlQuery($sql);
if($row != false){
	$tmp = $glaucoma_obj->checkWrongDate($row["dateActivation"]);
	$elem_dateActivation = (!empty($tmp)) ? get_date_format($row["dateActivation"],'mm-dd-yyyy') : $elem_dateActivation  ;
	$elem_activate = $row["activate"];
}

//Patient Info
$patientInfo = $glaucoma_obj->getPatientInfo($glaucoma_obj->pid);

//Menu Option    
$arrMenuVfNfa = array();
$arrTmp = array("Empty", "Normal","Border Line", "PS", "Abnormal","Increase Abnormal","Decrease Abnormal","No Change Abnormal","Stable");
foreach($arrTmp as $key => $var){
	$varTmp =  ($var == "Empty") ? "" : $var;
	$arrMenuVfNfa[] = array($glaucoma_obj->refineMenuValue($var),$arrEmpty,$varTmp);            
}


$sql_chack="SELECT glucomaId,activate FROM glucoma_main WHERE patientId = '".$glaucoma_obj->pid."' order by glucomaId DESC LIMIT 0,1";
$res_chack=imw_query($sql_chack);
if(imw_num_rows($res_chack)>0){
	$row_check = imw_fetch_assoc($res_chack);
	$avtivate_=$row_check['activate'];
	if($avtivate_==0){
		$elem_activate=0;
		$_GET["mode"]="";
	}
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Glaucoma Flow Sheet</title>
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
	</head>
	<script>
		date_format = "<?php echo inter_date_format();?>";
        var jquery_date_format = window.opener.top.global_date_format; //'m-d-Y';
	//Calendar funcs
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	
	function y2k(number)
	{
		return (number < 1000)? number+1900 : number;
	}
	function padout(number)
	{
		return (number < 10) ? '0' + number : number;
	}
	function restart(id)
	{
		gebi(id).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
		mywindow.close();
		if(typeof gebi(id).onchange == "function"){
			gebi(id).onchange();
		}		
	}	
	function setDateActivation(obj)
	{		
		var objDateActivate = top.iframeIntialTop.document.frmGlucoma.elem_dateActivation;
		objDateActivate.value = objDateActivate.defaultValue;  
		if(checkDate_v3(obj))
		{
			objDateActivate.value = obj.value;			
			//objDateActivate.onchange();
		}		
	}  		
	function displayAddNewForm(obj,forceOpen)
	{
		var objElemAddNew = gebi("elem_addNew");
		var objDiv = gebi("conAddNewTable");	
		var objFrm = document.frmAddNew;
		var objiFrameLog = gebi("tdFrameLog");	// top.iframeLog; //tdFrameLog	
		var dt = new Date();
		var hrs = dt.getHours();
		var mint = dt.getMinutes();
		var tStamp = dt.getTime();		
		hrs = (hrs == 0)? 12 : hrs;
		mint = (mint < 9) ? "0"+mint : mint;
		time = (hrs > 12) ? "PM" : "AM";	
		hrs = (hrs > 12)? hrs - 12 : hrs;			
		hrs = (hrs < 9)? "0"+hrs : hrs;			
		objFrm.elem_time.value = hrs +":"+ mint +" "+ time;		
		objFrm.elem_timeStamp.value = tStamp;
		obj.value = (forceOpen == true) ? "Edit" : "Add New";				
		objElemAddNew.value = ((objElemAddNew.value == "1") && (forceOpen != true)) ? "0" : "1";
		objDiv.style.display = (objElemAddNew.value == "1") ? "block" : "none"; 
		objiFrameLog.height =(objElemAddNew.value == "1") ? "152" : "205" ;
		obj.className = (objElemAddNew.value == "1") ? "btnPressed" : "btn" ;
        if(objElemAddNew.value == "0"){
           objElemAddNew.form.reset();
           var arrDiv = new Array('elem_vfOdSummaryTmp','elem_vfOsSummaryTmp','elem_nfaOdSummaryTmp','elem_nfaOsSummaryTmp');
           for(x in arrDiv)
           {
             var oTmp = gebi(arrDiv[x]);
             oTmp.innerHTML = "";   
           }
        }    
        obj.blur(); 
		//top.fAlert(objiFrameLog.height)
	}    
	 function checkFormDefaultVals(objFrm,checkArr)
	  {
		var flag = false;
		var objElems = objFrm.elements;
		if(objElems == null) return false;
		var len = 0;
		if(objFrm.name.indexOf("frmEditLog") != -1 ){
			len = objElems.length;
		}
		else
		{
			len = (checkArr != false ) ? checkArr.length : objElems.length;
		}
		Outer:
		for(var i=0;i<len;i++)
		{
			//top.fAlert(checkArr[i]);
			var objTmp;
			if(objFrm.name.indexOf("frmEditLog") != -1 )
			{
				if((objElems[i].name.indexOf("elem_treatmentChange_") != -1) || (objElems[i].name.indexOf("elem_medication_") != -1) || (objElems[i].name.indexOf("elem_show_data_") != -1) || (objElems[i].name.indexOf("elem_highlight_data_") != -1) ){
					objTmp = objElems[i] ;
				}
				else{
					continue;	
				}
			}
			else
			{
				objTmp = ( checkArr != false ) ?  objElems[checkArr[i]] : objElems[i] ;
			}
			if(objTmp == null){
				//top.fAlert(checkArr[i]+"\n"+objTmp)
				continue;
			}
			var eName = objTmp.name;
			var eValue = objTmp.value;
			var eType =  objTmp.type;
			if(typeof eType == "undefined"){
				if(objTmp.length > 0)
				{
					eType = objTmp[0].type;
				}
			}
				switch(eType)
				{
					case "text":
					case "textarea":
					case "hidden":
						
						//--
						if(objTmp.name == "sig_datacd_app_od" || objTmp.name == "sig_datacd_app_os"){					
							
							if(objFrm.elements[objTmp.name+"_1"].value!=eValue){
								flag = true;
								break;							
							}
							
						}else{
						//--
							if(objTmp.defaultValue != objTmp.value){								
								flag = true;
								break;
							}
						}
					break;
					case "select-one":
						var objOptions = objTmp.options;
						var lenOp = objOptions.length;						
						var ftmp = 0;
						for(var j=0;j<lenOp;j++){
							if(objOptions[j].defaultSelected){
								if(objTmp.selectedIndex != j)
								{
									flag = true;
									break Outer;
								}
								ftmp = 1;
							}
						}

						if(ftmp == 0 && objTmp.selectedIndex > 0){
							flag = true;
						}

					break;
					case "checkbox":
						if(objTmp.length > 0)
						{
							var lenChk = objTmp.length;
							for(var k=0;k<lenChk;k++)
							{
								if(objTmp[k].checked != objTmp[k].defaultChecked)
								{
									flag = true;
									break Outer;
								}	
							}
						}
						else
						{
							if(objTmp.checked != objTmp.defaultChecked)
							{
								flag = true;
								break Outer;
							}
						}	
					break;
					case "radio":
						if(objTmp.length > 0)
						{
							var lenChk = objTmp.length;
							for(var k=0;k<lenChk;k++)
							{
								if(objTmp[k].checked != objTmp[k].defaultChecked)
								{
									flag = true;
									break Outer;
								}	
							}
						}
						else
						{
							if(objTmp.checked != objTmp.defaultChecked)
							{
								flag = true;
								break Outer;
							}
						}	
					break;
				}
			
		}
		//top.fAlert(objFrm.name+"\n"+flag);
		return flag;
	  }

function isInitialChanged()
{
	var objFrmInitial = top.iframeIntialTop.document.frmGlucoma; //Initial
	var arrNamesInitial = new Array("elem_dateActivation","elem_activate","elem_dateDiagnosisMain","elem_diagnosisOd",
									"elem_diagnosisOs","elem_dateHighTaOdMain","elem_highTaOdOd",
									"elem_highTaOdOs","elem_dateHighTaOsMain","elem_highTaOsOd","elem_highTaOsOs",
									"elem_dateHighTxOdMain","elem_highTxOdOd",
									"elem_highTxOdOs","elem_dateHighTxOsMain","elem_highTxOsOd","elem_highTxOsOs",
									"elem_dateCd","elem_cdOd","elem_cdOs","elem_datePachy",
									"elem_pachyOdReads","elem_pachyOdAvg","elem_pachyOdCorr",
									"elem_pachyOsReads","elem_pachyOsAvg","elem_pachyOsCorr","elem_dateDiskPhoto",
									"elem_diskPhotoOd","elem_diskPhotoOs","elem_dateGonio","elem_gonioOd","elem_gonioOs",
									"elem_dateVf","elem_vfOdSummary","elem_vfOsSummary",
									"elem_dateNfa","elem_nfaOdSummary","elem_nfaOsSummary",
									"elem_riskFactors","elem_warnings","elem_cd_app_od","elem_cd_app_os",
									"elem_notes","elem_trgtIopOd","elem_trgtIopOs",
									"elem_vfOdSummaryOther","elem_vfOsSummaryOther",
									"elem_nfaOdSummaryOther","elem_nfaOsSummaryOther",
									"sig_datacd_app_od",
									"sig_datacd_app_os"
									);
	var flagInitial = checkFormDefaultVals(objFrmInitial,arrNamesInitial);
	return flagInitial;
}

function isAddNewChanged()
{
	var objFrmAddNew = document.frmAddNew; //Add New Form
	//"elem_tpOd",	"elem_tpOs",
	var arrNamesAddNew = new Array("elem_cdOd","elem_cdOs","elem_taOd","elem_taOs","elem_vfOdSummary","elem_vfOsSummary",
						"elem_gonioOd","elem_gonioOs","elem_tc","elem_medication","elem_show_data","elem_highlight_data","elem_cee","elem_txOd",
						"elem_txOs","elem_nfaOdSummary","elem_nfaOsSummary","elem_diskFundusOd","elem_diskFundusOs");
	var flagAddNew = checkFormDefaultVals(objFrmAddNew,arrNamesAddNew);
	return flagAddNew;
}

function isLogChanged()
{
	var objFrmLog = top.iframeLog.document.frmEditLog; //Log
	var arrNamesLog = new Array("elem_treatmentChange_","elem_medication_","elem_show_data_","elem_highlight_data_");
	var flagLog = checkFormDefaultVals(objFrmLog,arrNamesLog);
	return flagLog;
}

function isMediGridChanged()
{
	var objFrmMediGrid = top.iframeMedication_4.document.frmMedi; //MediGrid
	var flagMediGrid = checkFormDefaultVals(objFrmMediGrid,false);
	return flagMediGrid;
}

function isSysGridChanged(){}
function isSurgeryChanged(){
	var objFrmSurgery = top.iframeSurgery.document.frmSurgery; //Surgery
	var flagSurgery = checkFormDefaultVals(objFrmSurgery,false);	
	return flagSurgery;
}

function show_gfs_procedures(){
	var char_win = "width=1280, scrollbar=yes, status = yes";
	var URL = "procedure_glucoma_flow_sheet.php";
	var win = window.open(URL, "_blank", char_win);	
}
	
    function saveGlucoma(opPrint){  
		<?php // if View Only Access 
		if($elem_per_vo == "1"){	
		echo "return;"; 
		}
		?>
		var activate = getActivate();		
		if(activate == -1){
			if(opPrint != "-1"){ 
			top.fAlert("Please Activate Glucoma Diag. Hx.");
			}
			return ;			
		}
		arrMeds1 = top.iframeMedication_4.validateForm();
		arrMeds = arrMeds1;
		
		if(arrMeds.length>0){
			top.fAlert("Please enter site for :<br />"+arrMeds.join("<br />"));
			return false;
		}
		var objGlucoma = top.iframeIntialTop.document.frmGlucoma;
		var objMedication = top.iframeMedication_4.document.frmMedi;
		var objSurgery = top.iframeSurgery.document.frmSurgery;
		var objSaveFrm = document.frmSave;		
		var objAddNewFrm = document.frmAddNew;
		var objEditLogFrm = top.iframeLog.document.frmEditLog; 
		
		var tmpFlag = isInitialChanged();
		var tmpFlagMediGrid = isMediGridChanged();
		var tmpFlagSysGrid = isSysGridChanged();
		var tmpFlagSurgery = isSurgeryChanged();

		var isGlucomaNew = trim(objGlucoma.elem_glucomaId.value);
		if(isGlucomaNew.length == 0){
			tmpFlag = true;
		}
		
		if((tmpFlag == true) || (tmpFlagMediGrid == true) || /*(tmpFlagMediDisGrid == true) ||*/ (tmpFlagSysGrid == true) || (tmpFlagSurgery == true) ){
		
		objSaveFrm.elem_initialChanged.value = (tmpFlag==true) ? "1" : "0";
		objSaveFrm.elem_glucomaId.value = objGlucoma.elem_glucomaId.value;
		objSaveFrm.elem_dateActivation.value = objGlucoma.elem_dateActivation.value;
		objSaveFrm.elem_activate.value = objGlucoma.elem_activate.value;
		
		objSaveFrm.diagnosis_description.value = objGlucoma.diagnosis_description.value;
		objSaveFrm.elem_diagnosisOd.value = top.iframeIntialTop.$("#elem_diagnosisOd").val();
		objSaveFrm.elem_diagnosisOs.value = top.iframeIntialTop.$("#elem_diagnosisOs").val();
		
		objSaveFrm.elem_dateHighTmaxOd.value = objGlucoma.elem_dateHighTmaxOd.value;
		objSaveFrm.elem_highTmaxOdOd.value = objGlucoma.elem_highTmaxOdOd.value;
		objSaveFrm.elem_dateHighTmaxOs.value = objGlucoma.elem_dateHighTmaxOs.value;
		objSaveFrm.elem_highTmaxOsOs.value = objGlucoma.elem_highTmaxOsOs.value;
		
		objSaveFrm.elem_dateVf.value = (objGlucoma.elem_dateVf.type == "select-one" ) ? objGlucoma.elem_dateVf.options[objGlucoma.elem_dateVf.selectedIndex].text : objGlucoma.elem_dateVf.value;
		objSaveFrm.elem_vfOdSummary.value = objGlucoma.elem_vfOdSummary.value;
		objSaveFrm.elem_vfOsSummary.value = objGlucoma.elem_vfOsSummary.value;
		objSaveFrm.elem_vfOdSummaryOther.value = objGlucoma.elem_vfOdSummaryOther.value;
		objSaveFrm.elem_vfOsSummaryOther.value = objGlucoma.elem_vfOsSummaryOther.value;
		
		objSaveFrm.elem_dateNfa.value = (objGlucoma.elem_dateNfa.type == "select-one") ? objGlucoma.elem_dateNfa.options[objGlucoma.elem_dateNfa.selectedIndex].text :objGlucoma.elem_dateNfa.value;
		objSaveFrm.elem_nfaOdSummary.value = objGlucoma.elem_nfaOdSummary.value;
		objSaveFrm.elem_nfaOsSummary.value = objGlucoma.elem_nfaOsSummary.value;
		objSaveFrm.elem_nfaOdSummaryOther.value = objGlucoma.elem_nfaOdSummaryOther.value;
		objSaveFrm.elem_nfaOsSummaryOther.value = objGlucoma.elem_nfaOsSummaryOther.value;
		
		objSaveFrm.elem_dateGonio.value = (objGlucoma.elem_dateGonio.type == "select-one") ? objGlucoma.elem_dateGonio.options[objGlucoma.elem_dateGonio.selectedIndex].text:objGlucoma.elem_dateGonio.value;
		objSaveFrm.elem_gonioOd.value = objGlucoma.elem_gonioOd.value;
		objSaveFrm.elem_gonioOs.value = objGlucoma.elem_gonioOs.value;
		
		objSaveFrm.elem_datePachy.value = (objGlucoma.elem_datePachy.type == "select-one") ? objGlucoma.elem_datePachy.options[objGlucoma.elem_datePachy.selectedIndex].text: objGlucoma.elem_datePachy.value;
		objSaveFrm.elem_pachyOdReads.value = objGlucoma.elem_pachyOdReads.value;
		objSaveFrm.elem_pachyOdAvg.value = objGlucoma.elem_pachyOdAvg.value;
		objSaveFrm.elem_pachyOdCorr.value = objGlucoma.elem_pachyOdCorr.value;
		objSaveFrm.elem_pachyOsReads.value = objGlucoma.elem_pachyOsReads.value;
		objSaveFrm.elem_pachyOsAvg.value = objGlucoma.elem_pachyOsAvg.value;
		objSaveFrm.elem_pachyOsCorr.value = objGlucoma.elem_pachyOsCorr.value;
		
		objSaveFrm.elem_dateCd.value =  (objGlucoma.elem_dateCd.type == "select-one") ? objGlucoma.elem_dateCd.options[objGlucoma.elem_dateCd.selectedIndex].text :objGlucoma.elem_dateCd.value;
		objSaveFrm.elem_cdOd.value = objGlucoma.elem_cdOd.value;
		objSaveFrm.elem_cdOs.value = objGlucoma.elem_cdOs.value;
		
		stag_od_array = new Array();
		  top.iframeIntialTop.$('#staging_code_od option:selected').each(function() {
			stag_od_array.push($(this).val());
		  });
		  var staging_code_od_val = stag_od_array.join(',');
		  
		  stag_os_array = new Array();
		  top.iframeIntialTop.$('#staging_code_os option:selected').each(function() {
			stag_os_array.push($(this).val());
		  });
		  var staging_code_os_val = stag_os_array.join(',');
		
		objSaveFrm.staging_code_od.value = staging_code_od_val;
		objSaveFrm.staging_code_os.value = staging_code_os_val;
				
		var str = "";
		var len = objGlucoma.elem_riskFactors.length;
		for(i=0;i<len;i++){
			if(objGlucoma.elem_riskFactors[i].checked == true){
				str += objGlucoma.elem_riskFactors[i].value+",";
			}
			else
			{
				if(objGlucoma.elem_riskFactors[i].tagName == "SELECT")
				{
					str += objGlucoma.elem_riskFactors[i].value+",";	
				}
			}
		}
		
		race_val_arr = new Array();
		  top.iframeIntialTop.$('#elem_riskFactors_race option:selected').each(function() {
		   race_val_arr.push($(this).val());
		  });
		  var race_value_str = race_val_arr.join(',');
		
		var riskFactors_elems_val = race_value_str;
		str += riskFactors_elems_val+",";

		objSaveFrm.elem_riskFactors.value = str;
		var str = "";
		var len = objGlucoma.elem_warnings.length;
		for(i=0;i<len;i++){
			if(objGlucoma.elem_warnings[i].checked == true){
				str += objGlucoma.elem_warnings[i].value+",";
			}
		}		
		objSaveFrm.elem_warnings.value = str;
		objSaveFrm.elem_cdAppOd.value = objGlucoma.elem_cd_app_od.value;
		objSaveFrm.elem_cdAppOs.value = objGlucoma.elem_cd_app_os.value;		
		
		objSaveFrm.sig_datacd_app_od.value = objGlucoma.sig_datacd_app_od.value;
		objSaveFrm.sig_imgcd_app_od.value = objGlucoma.sig_imgcd_app_od.value;
		objSaveFrm.sig_datacd_app_os.value = objGlucoma.sig_datacd_app_os.value;
		objSaveFrm.sig_imgcd_app_os.value = objGlucoma.sig_imgcd_app_os.value;		
		
		objSaveFrm.elem_notes.value = objGlucoma.elem_notes.value;
		
		objSaveFrm.elem_trgtIopOd.value = (objGlucoma.elem_trgtIopOd) ? objGlucoma.elem_trgtIopOd.value : "";	
		objSaveFrm.elem_trgtIopOs.value = (objGlucoma.elem_trgtIopOs) ? objGlucoma.elem_trgtIopOs.value : "";	
		
		
		//Medication Grid
		var valueSap = "!#!";
		var fieldSap = "~*~";
		var strMedi = "";
		var len = objMedication.elements.length;
		for(i=0;i<len;i++)
		{
			var name = objMedication.elements[i].name;
			var val = objMedication.elements[i].value;	
			if(objMedication.elements[i]){
				if(objMedication.elements[i].type=='radio'){
					if(objMedication.elements[i].checked==true){
						strMedi += name + valueSap + val + fieldSap ;	
					}
				}else{
					strMedi += name + valueSap + val + fieldSap ;
				}
			}
		}
		
		//var len = objMedicationDis.elements.length;
		len = 0;
		for(i=0;i<len;i++)
		{
			var name = objMedicationDis.elements[i].name;
			var val = objMedicationDis.elements[i].value;	
			if(objMedicationDis.elements[i]){
				if(objMedicationDis.elements[i].type=='radio'){
					if(objMedicationDis.elements[i].checked==true){
						strMedi += name + valueSap + val + fieldSap ;	
					}
				}else{
					strMedi += name + valueSap + val + fieldSap ;
				}
			}
		}
		objSaveFrm.elem_medicationGrid.value = strMedi;
		objSaveFrm.elem_mediGridChanged.value = (tmpFlagMediGrid==true/* || tmpFlagMediDisGrid==true*/) ? "1" : "0";
		//Systemic Grid
		var valueSap = "!#!";
		var fieldSap = "~*~";
		var strSys = "";
		
		//Surgery Grid
		var valueSap = "!#!";
		var fieldSap = "~*~";
		var strSur = "";
		var len = objSurgery.elements.length;
		for(i=0;i<len;i++)
		{
			var name = objSurgery.elements[i].name;
			var val = objSurgery.elements[i].value;								
			strSur += name + valueSap + val + fieldSap ;
		}
		objSaveFrm.elem_surgeryGrid.value = strSur;
		objSaveFrm.elem_surgeryChanged.value = (tmpFlagSurgery==true) ? "1" : "0";
		
		}
		
		//Edit Log
		var valueSap = "!#!";
		var fieldSap = "~*~";
		var strLog = "";
		var tmpFlagLog = isLogChanged();
		if(tmpFlagLog == true){
			var len = objEditLogFrm.elements.length;
			for(i=0;i<len;i++)
			{
				var name = objEditLogFrm.elements[i].name;
				var val = objEditLogFrm.elements[i].value;
				if(name.indexOf("elem_treatmentChange") != -1){				
					val = (objEditLogFrm.elements[i].checked == true) ? "1" : "0";							
				}
				strLog += name + valueSap + val + fieldSap ;
			}		
			objSaveFrm.elem_log.value = strLog;
			objSaveFrm.elem_logChanged.value = "1";
		}
		
		// Add New Form
		var tmpFlagAddNew = isAddNewChanged();
		if((objAddNewFrm.elem_addNew.value == "1"))
		{		
			objSaveFrm.elem_addNewChanged.value = "1";
			objSaveFrm.elem_Add_addNew.value = objAddNewFrm.elem_addNew.value;
			objSaveFrm.elem_Add_date.value = top.iframeLog.document.getElementById('elem_date').value;
			objSaveFrm.elem_Add_va_od_summary.value = top.iframeLog.document.getElementById('elem_va_od_summary').value;
			objSaveFrm.elem_Add_va_os_summary.value = top.iframeLog.document.getElementById('elem_va_os_summary').value;
			objSaveFrm.elem_Add_taOd.value = top.iframeLog.document.getElementById('elem_ta_od_summary').value;
			objSaveFrm.elem_Add_taOs.value = top.iframeLog.document.getElementById('elem_ta_os_summary').value;
			objSaveFrm.elem_Add_txOd.value =  top.iframeLog.document.getElementById('elem_tx_od_summary').value;
			objSaveFrm.elem_Add_txOs.value =  top.iframeLog.document.getElementById('elem_tx_os_summary').value;
			objSaveFrm.elem_Add_tpOd.value =  top.iframeLog.document.getElementById('elem_tp_od_summary').value;
			objSaveFrm.elem_Add_tpOs.value =  top.iframeLog.document.getElementById('elem_tp_os_summary').value;
			objSaveFrm.elem_Add_glucoma_med.value =  top.iframeLog.document.getElementById('elem_glucoma_med').value;
			objSaveFrm.elem_Add_gonio_od_summary.value =  top.iframeLog.document.getElementById('elem_gonio_od_summary').value;
			objSaveFrm.elem_Add_gonio_os_summary.value =  top.iframeLog.document.getElementById('elem_gonio_os_summary').value;
			objSaveFrm.elem_Add_fundus_od_cd_ratio.value =  top.iframeLog.document.getElementById('elem_fundus_od_cd_ratio').value;
			objSaveFrm.elem_Add_fundus_os_cd_ratio.value =  top.iframeLog.document.getElementById('elem_fundus_os_cd_ratio').value;
			objSaveFrm.elem_Add_fundus_od_summary.value =  top.iframeLog.document.getElementById('elem_fundus_od_summary').value;
			objSaveFrm.elem_Add_fundus_os_summary.value =  top.iframeLog.document.getElementById('elem_fundus_os_summary').value;
			objSaveFrm.elem_Add_test_data.value =  top.iframeLog.document.getElementById('elem_test_data').value;
			objSaveFrm.elem_Add_assessment.value =  top.iframeLog.document.getElementById('elem_assessment').value;
			objSaveFrm.elem_Add_plan.value =  top.iframeLog.document.getElementById('elem_plan').value;
			objSaveFrm.elem_Add_medication.value =  top.iframeLog.document.getElementById('elem_medication').value;
			if(top.iframeLog.document.getElementById('elem_show_data')){
				objSaveFrm.elem_Add_show_data.value =  top.iframeLog.document.getElementById('elem_show_data').value;
			}
			if(top.iframeLog.document.getElementById('elem_highlight_data')){
				objSaveFrm.elem_Add_highlight_data.value =  top.iframeLog.document.getElementById('elem_highlight_data').value;
			}
			
			objSaveFrm.elem_Add_id.value = objAddNewFrm.elem_id.value;
			objSaveFrm.elem_Add_ocular_med.value =  top.iframeLog.document.getElementById('elem_ocular_med').value;
		}
		
		//print
		var objPrint = objSaveFrm.elem_print;
		objPrint.value = (opPrint == "1") ? "1" : "0";
		if(opPrint == "-1"){ 
			savingGlucomaForm(document.frmSave); 
		}else{			
			//Submit 
			objSaveFrm.submit();
		}
	}
	
	function getActivate(){	
		return top.iframeIntialTop.document.frmGlucoma.elem_activate.value;
	}
	
	function setActivate(val)
	{		
		<?php // if View Only Access 
		if($elem_per_vo == "1"){
		echo "return;"; 
		}
		?>
		var objActivate = $("#btn_activate");
		var objDeactivate = $("#btn_deactivate");
		if(val == "0"){
			if(objDeactivate.hasClass('btn-primary') === true){
				objDeactivate.removeClass('btn-primary');
			}
			objDeactivate.addClass('btn-default');
			
			if(objActivate.hasClass('btn-default') === true){
				objActivate.removeClass('btn-default');
			}
			objActivate.addClass('btn-primary');
			objDeactivate.prop('disabled',true);
			objActivate.prop('disabled',false);
		}
		else
		{
			if(objActivate.hasClass('btn-primary') === true){
				objActivate.removeClass('btn-primary');
			}
			objActivate.addClass('btn-default');
			
			if(objDeactivate.hasClass('btn-default') === true){
				objDeactivate.removeClass('btn-default');
			}
			objDeactivate.addClass('btn-primary');
			objActivate.prop('disabled',true);
			objDeactivate.prop('disabled',false);
		}
		
		var elem_activate_obj = gebi("elem_activate");
		elem_activate_obj.value=val;
		var objFrm = top.iframeIntialTop.document.frmGlucoma;
		var objFrmLogActivate = top.iframeLog.document.frmActivate;
		objFrm.elem_activate.value = val;
		objFrmLogActivate.elem_activate.value = val;
        
		if(val == "1"){
		objFrm.submit();
		objFrmLogActivate.submit();
		//setDocumentEvent("Remove");
		}		
	}
	
    // Events when Not activated         
    function alert4Activate()
    {
       top.fAlert("Please Activate Glucoma Hx.");
      // setDocumentEvent("Remove");         
    }    
    function setDocumentEvent(val){}    
    function getMenuOptionShow(str)
    {
      var ret ="";
      if(trim(str).length > 0)
      {
         switch(str)
         {
           case "Normal":
            ret = "NL";
           break;
           case "Border Line":
            ret = "BL";
           break;          
           case "Increase Abnormal":
            ret = "<span class=\"spechar\">&#8593;</span>Abn"; //\u2191
           break;
           case "Decrease Abnormal":
            ret = "<span class=\"spechar\">&#8595;</span>Abn"; //\u2193
           break;
           case "No Change Abnormal":
            ret = "<span class=\"spechar1\">&#916;</span>NC";  //\u0394
           break;
           case "Abnormal":
            ret = "Abn";
           break; 
	   case "PS":
	    ret = "Rel"; 
	   break;
	   case "Stable":
	    ret = "St";
	   break
         }   
      }
      return ret;      
    }
    function resetMenuOption(objElem)
    {     
      var value = objElem.value;
      var name = objElem.name;
      var conName = name.concat("Tmp");     
      var conObj = gebi(conName);    
      if(conObj != null)
      {
        conObj.innerHTML = getMenuOptionShow(value);    
        //conObj.value = getMenuOptionShow(value);    
      }
      //objElem.value = getMenuOptionShow(value);      
    }
   function clearText(objElem)
   {	 
	if(objElem.value == "Medication & Recommendation:")
	{
		objElem.value="";
	}
	else if(objElem.value.length > 0)
	{
		objElem.select();	
	}	
   } 
   
   function checkingAddNew(){	
	saveGlucoma("-1");
   }
 
 function checkGluacomaChange()
 {
	var flagAddNew = isAddNewChanged();
	var flagInitial = isInitialChanged();
	var flagLog = isLogChanged();
	var flagMediGrid = isMediGridChanged();
	//var flagMediDisGrid = isMediDisGridChanged();
	var flagSysGrid = isSysGridChanged();
	var flagSurgery = isSurgeryChanged();
	
	if(flagAddNew || flagInitial || flagLog || flagMediGrid /*|| flagMediDisGrid */|| flagSysGrid || flagSurgery )
	{
		return true;
	}
	else
	{
		return false;
	}
 }

function showGFSGraph(wh){
	
	var oDiv = gebi("div_imgGrph");
	var oImg = top.iframeLog.gebi("imgGrph_"+wh);
	if( oImg ){
		//oImg.style.display = "block";		
		
		if(wh == "all"){
			
			var o1 = top.iframeLog.gebi("elem_grph_cdod");
			var o2 = top.iframeLog.gebi("elem_grph_cdos");		
			
			o1 = (o1 && (o1.value != "")) ? "checked" : "disabled";
			o2 = (o2 && (o2.value != "")) ? "checked" : "disabled";
			
			var o3 = top.iframeLog.gebi("elem_grph_taod");
			var o4 = top.iframeLog.gebi("elem_grph_taos");					
			o3 = (o3 && (o3.value != "")) ? "checked" : "disabled";
			o4 = (o4 && (o4.value != "")) ? "checked" : "disabled";			
			
			var o5 = top.iframeLog.gebi("elem_grph_txod");
			var o6 = top.iframeLog.gebi("elem_grph_txos");					
			o5 = (o5 && (o5.value != "")) ? "checked" : "disabled";
			o6 = (o6 && (o6.value != "")) ? "checked" : "disabled";
			
			
			oDiv.innerHTML = "<table>"+
						"<tr><td id=\"imgGrphCustom\" width=\"600\" height=\"230\">"+oImg.outerHTML+"</td></tr>"+						
						"<tr><td>"+
							"<span style=\"width:60px;\"><input type=\"checkbox\" id=\"elem_grph_cdOd\" name=\"elem_grph_cdOd\" value=\"1\" "+o1+" onclick=\"customGrphImg()\">CD OD</span>"+
							"<span><input type=\"checkbox\" id=\"elem_grph_cdOs\" name=\"elem_grph_cdOs\" value=\"1\" "+o2+" onclick=\"customGrphImg()\">CD OS</span>"+
						"</td></tr>"+						
						"<tr><td>"+
							"<span style=\"width:60px;\"><input type=\"checkbox\" id=\"elem_grph_taOd\" name=\"elem_grph_taOd\" value=\"1\" "+o3+" onclick=\"customGrphImg()\">TA OD</span>"+
							"<span><input type=\"checkbox\" id=\"elem_grph_taOs\" name=\"elem_grph_taOs\" value=\"1\" "+o4+" onclick=\"customGrphImg()\">TA OS</span>"+
						"</td></tr>"+
						"<tr><td>"+
							"<span style=\"width:60px;\"><input type=\"checkbox\" id=\"elem_grph_txOd\" name=\"elem_grph_txOd\" value=\"1\" "+o5+" onclick=\"customGrphImg()\">TX OD</span>"+
							"<span><input type=\"checkbox\" id=\"elem_grph_txOs\" name=\"elem_grph_txOs\" value=\"1\" "+o6+" onclick=\"customGrphImg()\">TX OS</span>"+
						"</td></tr>"+
						
						"<tr><td align=\"center\">"+							
							"<span style=\"width:200px;cursor:pointer;\" onclick=\"clickHandler()\"><b>Close</b></span>"+
						"</td></tr>"+
						
						"</table>";
		}else{
			oDiv.innerHTML = oImg.outerHTML;
		}
	}
	
	var oImg = gebi("imgGrph_"+wh);
	if( oImg ){		
		oImg.style.display="block";
		oDiv.style.display="block";
	}
	stopClickBubble();
}

function showGFSGraphAM(){
	document.getElementById('icoGrph_all_am_main').style.display='block';
}

function clickHandler(){
	//close pop up;
	var oDiv = gebi("div_imgGrph");
	if(oDiv){ oDiv.style.display="none"; }
}

function getToolTip(id){
	if(id>0){
		var curPos = getPosition4();
		$('#allerg_show_div').fadeIn();
		document.getElementById('allerg_show_div').style.pixelTop = curPos.y;
		document.getElementById('allerg_show_div').style.pixelLeft = curPos.x+10;
	}else{
		$('#allerg_show_div').fadeOut();
	}
}
function show_sys_fun(val){
	if(val>0){
		$('#systemic_show_div').fadeIn();
	}else{
		$('#systemic_show_div').fadeOut();
	}
}
$(document).ready(function(){
	 $('.date-pick').datetimepicker({timepicker:false,format:jquery_date_format,autoclose: true,scrollInput:false});
});

function staging_div_view(status)
{
	status = $.trim(status);
	if(status == "show")
	{
		$('#staging_code_info').css({'display':'block'});		
	}
	else
	{
		$('#staging_code_info').css({'display':'none'});		
	}	
}

function vf_gl_div_view(status)
{
	status = $.trim(status);
	if(status == "show")
	{
		$('#vf_gl_abbreviations').css({'display':'block'});		
	}
	else
	{
		$('#vf_gl_abbreviations').css({'display':'none'});		
	}	
}
    
	<?php
		if(isset($_GET["op"]) && ($_GET["op"] == "i")){
	?>
		top.window.status="Glaucoma is saved.";
	<?php	
		}
		
	?>
	
	
		function add_new_col(obj,act){
			if(act==0){
				top.fAlert("Please Activate Glucoma Diag. Hx.");
				return false;
			}
			for(i=0;i<12;i++){
				var col_id= 'add_row_'+i;
				if(top.iframeLog.document.getElementById(col_id)){
					if(top.iframeLog.document.getElementById(col_id).style.display=='none'){
						top.iframeLog.document.getElementById(col_id).style.display='block';
						document.getElementById('elem_addNew').value=1;
						top.iframeLog.show_up_cols_auto_click();
					}else{
						document.getElementById('elem_addNew').value=0;
						top.iframeLog.document.getElementById(col_id).style.display='none';
					}
				}
			}
		}
	
		function iframeLoaded() {
			var iFrameID = document.getElementById('iframeIntialTop');
			if(iFrameID) {
				iFrameID.height = "";
				iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
			}
			//set_other_iframe_height();	
		  }
		  
		function set_other_iframe_height(){
			var iframe_height = new Array();
			var iFrameID = document.getElementById('iframeIntialTop');
			if(iFrameID) {
				iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight;
				$('.grid_sections').each(function(id,elem){
					iframe_height.push($(elem).height());
				});
				var top_height = iframe_height.reduce(function(total,num){
					return total + num;
				});
				
				var top_bar_height = $('.mainwhtbox > .row.purple_bar').height();
				var final_height = parseInt((iFrameID.height - top_height - top_bar_height - 20) / 2);
				var iframe_med_grid = document.getElementById('iframeMedication_4');
				if(iframe_med_grid){
					iframe_med_grid.height = final_height + "px";
				}
				var iframe_surgery_grid = document.getElementById('iframeSurgery');
				if(iframe_surgery_grid){
					iframe_surgery_grid.height = final_height + "px";
				}
			}
		}
		
		function IOP_showGraphsAm(val){
			var u="<?php echo $GLOBALS['rootdir']; ?>/chart_notes/chart_glucoma.php?get_charts=yes&ajax_request=yes";
			var p="elem_opts=All";
			$.post(u,p,function(data){
				var ARR_result = JSON.parse(data);
				var line_pay_graph_var_arr_js = ARR_result['line_pay_graph_var_detail'];
				var line_payment_tot_arr_js = ARR_result['line_payment_tot_detail'];
				if(line_pay_graph_var_arr_js && line_payment_tot_arr_js){
				line_chart('serial','div_imgGrph',line_payment_tot_arr_js,line_pay_graph_var_arr_js,'90');
				$('#iop_graph_modal').modal('show');
				}
				//document.getElementById('IOPGraphChartAmMain').style.display='block';
			});
		}

		function line_chart(chart_type,div_id,data_arr,data_graph_arr,labelRotation){
			if(typeof(labelRotation)=='undefined' || labelRotation==''){ var labelRotation=0};
			var chartData = JSON.parse(data_arr);
			var chartData_Graph = JSON.parse(data_graph_arr);
			 
			var chart = AmCharts.makeChart(div_id, {
				"type": chart_type,
				"categoryField": "category",
				"startDuration": 0,
				"theme": "light",
				"fontSize": 12,
				"categoryAxis": {
					"gridPosition": "start",
					"labelRotation": labelRotation,
				},
				"trendLines": [],
				"graphs": chartData_Graph,
				"guides": [],
				"valueAxes": [{
					"unit": "",
					"unitPosition": "left",
				}],
				"allLabels": [],
				"balloon": {},
				"legend": {
					"useGraphSettings": true
				},
				"titles": [{"text": "IOP"}],
				"dataProvider": chartData,
				
			} );
		}
	</script>
	<body>
		<div class="container-fluid">
			<div class="mainwhtbox pd10" >
				<div class="row">
        	<div class="col-sm-12 purple_bar">
            <div class="col-sm-2">
              <label>Glaucoma Flow Sheet</label>
            </div>
            <div class="col-sm-4 text-center">
              <label><?php echo $patientInfo; ?></label>
            </div>
						<div class="col-sm-6 form-inline text-right">
							<div class="row">
                <div class="col-sm-2">
                  <?php $glIndctr = ($glaucoma_obj->hasPtGLProc($glaucoma_obj->pid)) ? 'btn btn-success' : 'btn btn-default'; ?>
                  <button class="<?php echo $glIndctr; ?>" type="button" onClick="show_gfs_procedures();">Procedures</button>
                </div>
                <div class="col-sm-5">
                  <div class="row">
                    <div class="col-sm-5">
                      <label for="elem_dateActivationMain"><small>Date of Activation: </small></label>
                    </div>
                    <div class="col-sm-7">
                      <div class="input-group pointer">
                        <input type='text' name='elem_dateActivation' id="elem_dateActivationMain"  class="form-control date-pick" onBlur="checkdate(this);" maxlength="10" value="<?php echo $elem_dateActivation;?>">
                        <label for="elem_dateActivationMain" class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </label>	
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-5">
                  <div class="form-group">
                    <input type="button" class="btn <?php echo ($elem_activate == "1") ? "btn-default" : "btn-primary";?>" id="btn_activate" name="btn_activate" value="Activate" onClick="setActivate(1);saveGlucoma();" <?php echo ($elem_activate == "1") ? "disabled" : "";?>>&nbsp;
                    <input type="button" class="btn <?php echo ($elem_activate == "0") ? "btn-default" : "btn-primary";?>" id="btn_deactivate" name="btn_deactivate" value="Deactivate" onClick="setActivate(0);saveGlucoma(9);" <?php echo ($elem_activate == "0") ? "disabled" : "";?>>&nbsp;
                    <input type="hidden" id="elem_activate" name="elem_activate" value="<?php echo $elem_activate;?>">&nbsp;
                    <?php 
                      if(($patientStatus == "Active") && ($elem_per_vo != "1")){
                        echo '<button type="button" class="btn btn-primary" onClick="saveGlucoma(1);"><span class="glyphicon glyphicon-print"></span></button>';
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>	
					</div>
      	</div>
            
				<div class="clearfix"></div>
        
				<div class="row">
					<div class="col-sm-7 pt10">
						<iframe name="iframeIntialTop" id="iframeIntialTop" width="100%" height="100%" src="initialTop.php?mode=<?php echo $_REQUEST['mode']; ?>" scrolling="no" frameborder="0" onload="iframeLoaded();"></iframe>
					</div>
					<div class="col-sm-5 pt10">
						<?php 
						//Modal data
						//Allergy
							$checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get"); 
							$i=0;
							$show_allergy_modal = '';
							if($checkAllergy!=""){
								$allergy_btn_class = "btn-success";
							}else{
								$allergy_btn_class = "btn-danger";
								$sql_med = imw_query("select title,comments from lists where pid='".$glaucoma_obj->pid."' and allergy_status='Active' and type='7' and ag_occular_drug='fdbATDrugName'");
								while($row_med = imw_fetch_array($sql_med)){
									$i++;
									$show_allergy_modal.="<tr><td>".$i."</td><td>".$row_med['title']."</td><td>".$row_med['comments']."</td></tr>";
								}
							}
						//Systemic meds
							$show_systemic_modal = '';
							$systemic_btn_class = 'btn-success';
							$check_data = imw_query("select * from lists where pid='".$glaucoma_obj->pid."' AND type='1' AND allergy_status!='Deleted' AND allergy_status!='Discontinue' AND allergy_status!='Stop' ORDER BY begdate DESC");	
							if(imw_num_rows($check_data) > 0){
								$k=0;
								while($row = imw_fetch_array($check_data)){
									$k++;
									$sys_begdate = (!empty($row["begdate"]) && ($row["begdate"] != '0000-00-00')) ? get_date_format($row["begdate"]) : "" ;
									$sys_enddate = (!empty($row["enddate"]) && ($row["enddate"] != '0000-00-00')) ? get_date_format($row["enddate"]) : "" ;
									$show_systemic_modal.="<tr><td>".$k."</td><td>".$row['title']."</td><td>".$sys_begdate."</td><td>".$sys_enddate."</td></tr>";
								}
								$systemic_btn_class = 'btn-danger';
							}
						?>
						<div class="whtbox">
							<div class="head">
								<div class="row">
									<div class="col-sm-6">
										<button type="button" class="btn <?php echo $allergy_btn_class; ?>" data-toggle="modal" data-target="#allerg_show_div">Allergies</button>
									</div>
									<div class="col-sm-6 text-right">
										<button type="button" class="btn <?php echo $systemic_btn_class; ?>" data-toggle="modal" data-target="#systemic_show_div">Systemic Meds</button>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="head">
             		<div class="row">
									<div class="col-sm-9">
										Medication Grid</h4>	
									</div>
									<div class="col-sm-3 text-right">
                  	<span class="glyphicon glyphicon-plus mlr5 pointer" onClick="top.iframeMedication_4.medigrid_addRow(4)"></span>
                    <span class="glyphicon glyphicon-remove mlr5 pointer" onClick="top.iframeMedication_4.medigrid_removeRow(4)"></span>
									</div>	
								</div>	
							</div>
							<div class="clearfix mb10"></div>
							<div class="row">
								<div class="col-sm-12">
									<iframe id="iframeMedication_4" name="iframeMedication_4" width="100%" height="100%" src="mediGrid.php?show_type=4" scrolling="yes" frameborder="0" onload="resizeIframe(this);"></iframe>
								</div>
							</div>
							<div class="clearfix mb10"></div>
							<div class="head ">
								<div class="row">
									<div class="col-sm-9">Surgery</div>
									<div class="col-sm-3 text-right">
                  	<span class="glyphicon glyphicon-plus mlr5 pointer" onClick="top.iframeSurgery.surgery_addRow(4)"></span>
                    <span class="glyphicon glyphicon-remove mlr5 pointer" onClick="top.iframeSurgery.surgery_removeRow(4)"></span>
                    
									</div>	
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="row">
								<div class="col-sm-12">
									 <iframe id="iframeSurgery" name="iframeSurgery" width="100%" height="95" src="surgeryGrid.php" scrolling="yes" frameborder="0" onload="resizeIframe(this);"></iframe>
								</div>
							</div>	
						</div>	
					</div>
				</div>
			</div>
      
      <div class="mainwhtbox mt10 pt0" >	
				
					<div class="head">
						<div class="row">
							<div class="col-sm-8">Log</div>
							<div class="col-sm-4 text-right">
								<input type="button" name="btnAddNew" value="Add New" class="btn btn-primary" onClick="return add_new_col(this,'<?php echo $elem_activate; ?>');">
							</div>	
						</div>	
					</div>
          
					<div class="clearfix"></div>	
          
					<div class="row">
						<div class="col-sm-12" id="tdFrameLog">
							<iframe name="iframeLog" width="<?php echo($GLOBALS['gl_browser_name']=='ipad')?"1385px":"100%"?>" src="glucomaLog.php<?php echo (!empty($_GET["mode"])) ? "?mode=".$_GET["mode"] : "" ;?>" scrolling="yes" frameborder="0" style="<?php echo($GLOBALS['gl_browser_name']=='ipad')?"height:100%;":"100%"?>" onload="resizeIframe(this);"></iframe>	
						</div>
						<!-- Add new form -->
						<div class="col-sm-12 hide">
							<form name="frmAddNew" style="margin:0px; " method="post">
								<input type="hidden" name="elem_addNew" id="elem_addNew" value="0">
								<input type="hidden" name="elem_id" value="0">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td height="1" bgcolor="Gray"></td>
									</tr>							
									<tr>
										<td align="right" bgcolor="#9AADE0">
										</td>
									</tr>
								</table>						
							</form>
							
							<form name="frmSave" action="saveGlucoma.php" method="post">
								<!-- Glucoma Main --> 									
								<input type="hidden" name="elem_patientId" value="<?php echo $glaucoma_obj->pid;?>">
								<?php
									$arrTmp = array('elem_glucomaId','elem_dateActivation','elem_activate',
												  'diagnosis_description','elem_diagnosisOd','elem_diagnosisOs',
												  'staging_code_od','staging_code_os',
												  'elem_dateHighTmaxOd','elem_highTmaxOdOd','elem_dateHighTmaxOs','elem_highTmaxOsOs',
												  'elem_dateHighTaOd','elem_highTaOdOd','elem_highTaOdOs',
												  'elem_dateHighTaOs','elem_highTaOsOd','elem_highTaOsOs',
												  'elem_dateHighTxOd','elem_highTxOdOd','elem_highTxOdOs',
												  'elem_dateHighTxOs','elem_highTxOsOd','elem_highTxOsOs',					  
												  'elem_dateVf','elem_vfOdSummary','elem_vfOsSummary',			
												  'elem_vfOd','elem_vfOs','elem_nfaOd',
												  'elem_nfaOs','elem_dateNfa','elem_nfaOdSummary',
												  'elem_nfaOsSummary','elem_dateGonio','elem_gonioOd',
												  'elem_gonioOs','elem_datePachy','elem_pachyOdReads',
												  'elem_pachyOdAvg','elem_pachyOdCorr','elem_pachyOsReads',
												  'elem_pachyOsAvg','elem_pachyOsCorr','elem_dateDiskPhoto',
												  'elem_diskPhotoOd','elem_diskPhotoOs','elem_dateCd',
												  'elem_cdOd','elem_cdOs','elem_riskFactors',
												  'elem_warnings','elem_cdAppOd','elem_cdAppOs',
												  'elem_trgtIopOd','elem_trgtIopOs',
												  'elem_notes','elem_dateCee','elem_cee','elem_ceeNotes',
												  'elem_vfOdSummaryOther','elem_vfOsSummaryOther',	
												  'elem_nfaOdSummaryOther','elem_nfaOsSummaryOther',
												  'sig_datacd_app_od', 'sig_imgcd_app_od',
												  'sig_datacd_app_os', 'sig_imgcd_app_os');
									
									foreach($arrTmp as $key => $val)
									{
										echo "<input type=\"hidden\" name=\"".$val."\" value=\"\">";      
									}             
								?>                   
								<!-- Glucoma Main --> 
								
								<!-- Edit -->
								<input type="hidden" name="elem_log" value="">
								<!-- Edit -->
								
								<!-- Medication-->
								<input type="hidden" name="elem_medicationGrid" value="">
								<!-- Medication-->
								<!-- Systemic-->
								<input type="hidden" name="elem_systemicGrid" value="">
								<!-- Medication-->
								<!-- Surgery-->
								<input type="hidden" name="elem_surgeryGrid" value="">
								<!-- Surgery-->
								<!-- Add New -->
								<?php
									$arrAddNewElem = array('elem_Add_addNew','elem_Add_date','elem_Add_taOd','elem_Add_taOs','elem_Add_va_od_summary',
									'elem_Add_va_os_summary','elem_Add_txOd','elem_Add_txOs','elem_Add_tpOd',
									'elem_Add_tpOs','elem_Add_glucoma_med','elem_Add_gonio_od_summary',
									'elem_Add_gonio_os_summary','elem_Add_sle_od_summary','elem_Add_sle_os_summary','elem_Add_fundus_od_cd_ratio','elem_Add_fundus_os_cd_ratio',
									'elem_Add_fundus_od_summary','elem_Add_fundus_os_summary','elem_Add_test_data','elem_Add_assessment','elem_Add_plan','elem_Add_medication','elem_Add_show_data','elem_Add_highlight_data',
									'elem_Add_id','elem_Add_ocular_med');			
									foreach($arrAddNewElem as $key => $val){
										echo "<input type=\"hidden\" name=\"".$val."\" value=\"\">";
									}			
								?>
								<!-- Add New -->
								<!-- Print -->
								<input type="hidden" name="elem_print" value="0">
								<!-- Print -->
								<!-- Save&Close -->
								<input type="hidden" id="elem_saveClose" name="elem_saveClose" value="0">	
								<!-- Save&Close -->
								<!-- Changed -->
								<input type="hidden" name="elem_initialChanged" value="0">
								<input type="hidden" name="elem_mediGridChanged" value="0">
								<input type="hidden" name="elem_sysGridChanged" value="0">
								<input type="hidden" name="elem_surgeryChanged" value="0">
								<input type="hidden" name="elem_logChanged" value="0">
								<input type="hidden" name="elem_addNewChanged" value="0">
								<!-- Changed -->
							</form>
							
							<span id="icoGrph_tx" ></span>
							<span id="icoGrph_ta" ></span>
							<span id="icoGrph_cd" ></span>
						</div>	
					</div>	
				
				
  		</div>	      
      
      <div class="mainwhtbox mt10" >
      	<div class="row pt10">
        	<div class="col-sm-12 text-center ad_modal_footer" id="module_buttons">
          	<input type="button"  class="btn btn-success" id="save"  value="Save" align="bottom" onClick="javascript:saveGlucoma();" />
            <input type="button"  class="btn btn-danger"  value="Cancel" align="bottom"  id="cancel" onClick="javascript:window.close();" />	
        	</div>
       	</div>
    	</div>
		</div>
		
		
		<!-- Modals -->
		<div class="common_wrapper">
			<!-- Abbrevations modal -->
			<div id="vf_gl_abbreviations" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">Abbreviations</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<table class="table table-bordered table-striped table-hover">
									<tr>
										<th>Normal</th>
										<td>NL</td>
										<th>Nonspecific</th>
										<td>Nonspec</td>	
									</tr>
									<tr>
										<th>Nasal Step</th>
										<td>NasalStep</td>
										<th>Arcuate</th>
										<td>A</td>
									</tr>
									<tr>
										<th>Hemifield</th>
										<td>HF</td>
										<th>Paracentral</th>
										<td>PC</td>	
									</tr>
									<tr>
										<th>Into Fixation</th>
										<td>IF</td>
										<th>Central Island _degrees</th>
										<td>CI</td>	
									</tr>
									<tr>
										<th>Stable</th>
										<td>S</td>
										<th>Worse</th>
										<td>W</td>	
									</tr>
									<tr>
										<th>Improved</th>
										<td>I</td>
										<th>Not Improved</th>
										<td>NI</td>	
									</tr>
									<tr>
										<th>Likely worse</th>
										<td>LW</td>
										<th>Probably worse</th>
										<td>PW</td> 	
									</tr>	
								</table>
							</div>
						</div>
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class="btn btn-danger" data-dismiss="modal" type="button">Close</button>
						</div>	
					</div>	
				</div>	
			</div>
			
			<!-- Staging code modal -->
			<div id="staging_code_info" class="modal fade" role="dialog">
				<div class="modal-dialog modal90">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Staging Code Information</h4>	
						</div>
						<div class="modal-body">
							<div class="row">
								<ol type="a">
									<li><b>Mild</b>
										<ol type="i">
											<li>Optic Nerve abnormalities consistent with glaucoma </li>
											<li>But NO visual field abnormalities on any visual field test </li>
											<li>OR abnormalities present only on short-wavelength automated perimetry or frequency doubling perimetry </li>
										</ol>
									</li>
									<li><b>Moderate</b>
										<ol type="i">
											<li>Optic nerve abnormalities consistent with glaucoma</li>
											<li>AND glaucomatous visual field abnormalities in ONE hemifield and </li>
											<li>NOT within 5 degrees of fixation </li>
										</ol>        
									</li>
									<li><b>Severe</b>
										<ol type="i">
											<li>Optic nerve abnormalities consistent with glaucoma </li>
											<li>AND glaucomatous visual field abnormalities in BOTH hemifields </li>
											<li>AND/OR loss within 5 degrees of fixation in at least one hemifield</li>
										</ol>         
									</li>
									<li><b>Indeterminate</b>
										<ol type="i">
											<li>Visual fields not performed yet, </li>
											<li>Patient incapable of visual field testing,</li>
											<li>Unreliable/uninterpretable visual field testing </li>
										</ol>           
									</li>                        
									<li><b>Unspecified</b>
										<ol type="i">
											<li>Stage not recorded in chart</li>
											<li>Remember to document stage in record</li>
										</ol>        
									</li>                                
								</ol>	
							</div>
						</div>
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class="btn btn-danger" data-dismiss="modal" type="button">Close</button>
						</div>
					</div>	
				</div>	
			</div>
			
			<!-- VF Synthesis modal -->
			<div id="vf_gl_synthesis_popup" class="modal fade" role="dialog">
				<div class="modal-dialog modal90">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">VF Synthesis</h4>	
						</div>	
						<div class="modal-body">
							<div class="row">
								<table class="table table-striped table-bordered popup_synthesis_format">
								<?php
									$vf_gl_synthesis_qry = "SELECT date_format(examDate,'".get_sql_date_format()."') as exam_date , synthesis_od,synthesis_os FROM vf_gl WHERE patientId = '".$glaucoma_obj->pid."' order by vf_gl_id DESC";
									$vf_gl_synthesis_qry_obj = imw_query($vf_gl_synthesis_qry);
									if(imw_num_rows($vf_gl_synthesis_qry_obj)>0){
									while($vf_gl_qry_data = imw_fetch_assoc($vf_gl_synthesis_qry_obj)):
								?> 
									<tr>
										<th colspan="2">Exam Date: <?php echo $vf_gl_qry_data["exam_date"]; ?></th>
									</tr>
									<tr>
										<td><b>Synthesis OD:</b></td>
										<td><?php echo $vf_gl_qry_data["synthesis_od"]; ?></td>
									</tr>
									<tr>
										<td><b>Synthesis OS:</b></td>
										<td><?php echo $vf_gl_qry_data["synthesis_os"]; ?></td>
									</tr>                            
								<?php endwhile;
									}else{ ?>
									<tr><td class="text-center">No record</td></tr>
								<?php		
									}
									
								?>		
								</table>	
							</div>
						</div>	
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class=" btn btn-danger" type="button" data-dismiss="modal">Close</button>
						</div>	
					</div>	
				</div>	
			</div>
			
			<!-- RNFL Synthesis modal -->
			<div id="rnfl_gl_synthesis_popup" class="modal fade" role="dialog">
				<div class="modal-dialog modal90">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">RNFL Synthesis</h4>	
						</div>	
						<div class="modal-body">
							<div class="row">
								<table class="table table-striped table-bordered popup_synthesis_format">
								<?php
									$rnfl_gl_synthesis_qry = "SELECT date_format(examDate,'".get_sql_date_format()."') as exam_date, synthesis_od,synthesis_os FROM oct_rnfl WHERE patient_id = '".$glaucoma_obj->pid."' order by oct_rnfl_id DESC";
									$rnfl_gl_synthesis_qry_obj = imw_query($rnfl_gl_synthesis_qry);
									if(imw_num_rows($rnfl_gl_synthesis_qry_obj)>0){
									while($rnfl_gl_qry_data = imw_fetch_assoc($rnfl_gl_synthesis_qry_obj)):
								?>
									<tr>
										<th colspan="2">Exam Date: <?php echo $vf_gl_qry_data["exam_date"]; ?></th>
									</tr>
									<tr>
										<td><b>Synthesis OD:</b></td>
										<td><?php echo $vf_gl_qry_data["synthesis_od"]; ?></td>
									</tr>
									<tr>
										<td><b>Synthesis OS:</b></td>
										<td><?php echo $vf_gl_qry_data["synthesis_os"]; ?></td>
									</tr>                            
								<?php endwhile;
									}else{ ?>
									<tr><td class="text-center">No record</td></tr>
								<?php		
									}
								?>		
								</table>	
							</div>
						</div>	
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class=" btn btn-danger" type="button" data-dismiss="modal">Close</button>
						</div>	
					</div>	
				</div>	
			</div>
			
			<!-- Allergies popup modal -->
			<div id="allerg_show_div" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">Allergy Details</h4>	
						</div>	
						<div class="modal-body">
							<div class="row">
								<table class="table table-bordered table-striped">
									<tr class="grythead">
										<th class="text-nowrap">S. No.</th>
										<th>Name</th>
										<th>Reactions</th>
									</tr>
									<?php 
										echo $show_allergy_modal;
									?>	
								</table>	
							</div>
						</div>	
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>
						</div>	
					</div>	
				</div>	
			</div>
			
			<!-- Systemic meds modal -->
			<div id="systemic_show_div" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">Systemic Med Details</h4>	
						</div>	
						<div class="modal-body">
							<div class="row">
								<table class="table table-bordered table-striped">
									<tr class="grythead">
										<th class="text-nowrap">S. No.</th>
										<th>Medication name</th>
										<th>Started</th>
										<th>Stopped</th>
									</tr>
									<?php 
										echo $show_systemic_modal;
									?>	
								</table>	
							</div>
						</div>	
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>
						</div>	
					</div>	
				</div>	
			</div>	
			
			<!-- IOP Graph modal -->
			<div id="iop_graph_modal" class="modal fade">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">	
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="header-title">IOP Graph</h4>	
						</div>
						<div class="modal-body">
							<div id="div_imgGrph" style="min-height:400px"></div>	
						</div>
						<div class="modal-footer ad_modal_footer" id="module_buttons">
							<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>	
						</div>	
					</div>	
				</div>	
			</div>	
		</div>
        
        
        <div id="imgModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">GFS Large Image View</h4>
                    </div>
                    <div class="modal-body">
                        <p>Nothing Found</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        
        
	</body>
		<script>
			function resizeIframe(obj) {
				if( $(obj).attr('name') == 'iframeMedication_4')
					$(obj).attr( 'height',$(obj).contents().find('form[name="frmMedi"]').outerHeight() + 20 + 'px');
				else if( $(obj).attr('name') == 'iframeLog')
					$(obj).attr( 'height',$(obj).contents().find('div.fixed_table_col').outerHeight() +20+ 'px');	
				else{
					$(obj).attr('height',$(obj).contents().outerHeight() + 'px' );	
				}
			}
	
			$(window).load(function(){
				if(typeof(window.opener.top.innerDim)=='function'){
					var innerDim = window.opener.top.innerDim();
					if(innerDim['w'] > 1600) innerDim['w'] = 1600;
					if(innerDim['h'] > 900) innerDim['h'] = 900;
					window.resizeTo(innerDim['w'],innerDim['h']);
					brows	= get_browser();
					if(brows!='ie') innerDim['h'] = innerDim['h']-35;
					var result_div_height = innerDim['h']-210;
					//$('.mainwhtbox').height(result_div_height+'px');
				}
			});
		</script>
	</html>	