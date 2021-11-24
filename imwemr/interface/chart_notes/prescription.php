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

File: prescription.php
Purpose: This file provides Prescription section in work view.
Access Type : Direct
*/
//============GLOBAL FILE INCLUSION=========================
require_once(dirname(__FILE__).'/../../config/globals.php');
//============FUNCTIONS FILES INCLUSION=====================
include_once($GLOBALS['srcdir']."/classes/common_function.php");
include_once($GLOBALS['srcdir']."/classes/Functions.php");

//============VERIFIED PATIENT SESSION AND POP-UP CLOSED WITHOUT SESSION
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

//============OBJECT OF FUNCTIONS.PHP=======================
$objManageData = new ManageData;

//============SESSION VARIABLES=============================
$pid 				= $_SESSION['patient'];
$authUserID 		= $_SESSION['authUserID'];
				
$library_path = $GLOBALS['webroot'].'/library';
$libaryImagePath = "../../library/images/";  //PATH SETTING FOR ALL IMAGES
$_SESSION["encounter"] 	=  "";

//============REGULAR VARIABLES=============================
$val_arr['edit']		= '';
$val_arr['pid']			= $pid;
$val_arr['prescription']= "";

	
if($pid<>"")
{	
//============GET PROVIDER ID==============================
	$chart_signed = "SELECT 
						providerId, patient_id 
					FROM
						`chart_master_table`
					WHERE 
						patient_id=$pid 
					AND 
						delete_status='0' 
					ORDER BY 
						update_date desc limit 0,1
					";
						
	$chart_res = imw_query($chart_signed);
	if(@imw_num_rows($chart_res)>0)
	{
		$chart_rec	=	imw_fetch_array($chart_res);
		$provid	=	$chart_rec['providerId'];
	}
//============GET PATIENT DATA=============================	
	$vquery = "SELECT 
					*
			   FROM 
					patient_data 
			   WHERE 
					id =$pid";
	$vsql = @imw_query($vquery);
	
	if(!$vsql)
		echo ("Error : ".imw_error());
		$rt = @imw_fetch_assoc($vsql);
	
	//=========HEADER PATIENT DETAILS=====================
	$patientIdForHeader = $rt["id"];
	$fn = trim($rt["fname"]);
	$mn = trim($rt["mname"]);
	$lm = trim($rt["lname"]);
	$mn = strtoupper(substr($mn,0,1));
	$patientDetails = $lm.", ".$fn." ".strtoupper(substr($mn,0,1))." - ".$patientIdForHeader;
	
	$unit_array		= array("","mg","%");  // UNIT DATA
	$arrQuantity 	= array("","Tabs","ml","cc"); // QUANTITY DATA
	$substitute_arr = array("Permissible","Not Permissible","Brand"); // SUBSTITUTION DATA
	$use_array		= array("qd","qhs","qAM","qid","bid","tid","qod","__hrs","__Xdaily");	//USE DROPDOWN DATA	
	$form_array 	= array("","tablet","capsule","tsp","ml","units","inhilations","gtts(drops)");
	$interval_array = array("","b.i.d.","t.i.d.","q.i.d.","q.3h","q.4h","q.5h","q.6h","q.8h","q.d.");
	$eye_array 		= array("PO","OU","OS","OD","RLL","RUL","LLL","LUL","O/O","IV","IM","Topical","L/R Ear","Both Ears");
	$route_array 	= array("","Per Oris","Per Rectum","To Skin","To Affected Area","Sublingual", "OS", "OD", "OU", "SQ", "IM", "IV", "Per Nostril");

//============RENEW - ADD THE RECORD========================
	if(isset($_GET["renew_id"]) && !empty($_GET["renew_id"]))
	{
		$pres_id = $_GET["renew_id"];		
	}		
//============GET PRESCRIPTION DATA========================
	$vquery_c = "SELECT
					* 
				FROM 
					prescriptions 
				WHERE
					id='$pres_id'
				";					
	$vsql_c = imw_query($vquery_c);		
	$vrs = imw_fetch_array($vsql_c);					
	
	if ($vrs['start_date'] && getNumber($vrs['start_date']) != "00000000")
	{									
		$tmp_date = $vrs['start_date'];
		$create_date = get_date_format($vrs['start_date']);
	}
	
	$st_date = $create_date;	
	$tdate = get_date_format(date("Y-m-d"));
}

//============GET TYPEAHEAD DATA========================
$selQry="SELECT 
			DISTINCT(pres_key) 
		FROM
			`common_prescription`
		WHERE 
			providerID='$authUserID' 
		OR 
			adminPresc='1'  
		LIMIT 
			0,10000";
		
$res=imw_query($selQry) or die(imw_error());
	while($row=imw_fetch_array($res))
	{
		$stringAll.="'".str_replace("'","",$row["pres_key"])."',";
	}
	$stringAll=substr($stringAll,0,-1); //echo($stringAll);
	
//============GET ERX DATA TO CHECK ERX CONDITION========
$qry = "SELECT 
			Allow_erx_medicare 
		FROM 
			`copay_policies`
		WHERE
			policies_id = 1";
			
$policy_qry	= imw_query($qry);
$policy_res = imw_fetch_array($policy_qry);

//============SAVING DATA INTO DB BASED ON POST DATA======
if(isset($_POST["per_action"]) && !empty($_POST["per_action"]) && ($_POST["per_action"] == 'save' || $_POST["per_action"] == 'print'))
{
	
	$start_convert = $per_action = $refilled_by = $quantity_unit= $ele_SaveClose = "";
	
	$pid			= $_SESSION['patient'];			
	
	//========POST DATA===================================	
	$start_convert	= getDateFormatDB($st_date);
	$per_action		=($_POST["per_action"]);
	$refilled_by	= $_POST["refilled_by"];
	$quantity_unit	= $_POST["quantity_unit"];
	$ele_SaveClose	= $_POST["elem_saveClose"];
	
	//========INSERT QRY==================================
	$insertQry = "INSERT INTO 
					prescriptions 
				SET 
					date_added = current_date, 
				";								

	$insertQry .= "
					patient_id  = '$pid',
					filled_by_id = '".trim($_POST["providerID"])."',
					date_modified = current_date,
					start_date 	= '".$start_convert."',
					drug 	= '".imw_real_escape_string($_POST["drug"])."',
					dosage 	= '".imw_real_escape_string($_POST["dosage"])."',								
					quantity = '".imw_real_escape_string($_POST["quantity"])."',
					size = '".imw_real_escape_string($_POST["size"])."',
					unit  = '".imw_real_escape_string($_POST["unit"])."',								
					quantity_unit = '".imw_real_escape_string($quantity_unit)."',
					substitute = '".imw_real_escape_string($_POST["substitute"])."',
					refills  = '".imw_real_escape_string($_POST["refills"])."',
					eye='".imw_real_escape_string($_POST["eye"])."',
					usage_1='".imw_real_escape_string($_POST["usage"])."',
					usage_2='".imw_real_escape_string($_POST["usage_2"])."',
					re_unit='".imw_real_escape_string($_POST["re_unit"])."',
					note = '".imw_real_escape_string($_POST["note"])."' ,
					refilled_by = '".imw_real_escape_string($refilled_by)."' 
				";
	//print $insertQry;	//exit;				
	$insertSql = imw_query($insertQry);
					if(!$insertQry)
					echo ("Error : ". imw_error());
					
	$insertId = imw_insert_id(); //MYSQL INSERT ID
	
	//PARAMETERS SET TO SEND DATA ONTO PRINTING FILE
	$_SESSION['pres_insertId'] = $insertId;
	$_SESSION['pres_per_action'] = $per_action;
	$_SESSION['pres_ele_save_close'] = $ele_save_close;
	
	$url_header = "prescription.php";
	header("Location:".$url_header); exit;		
}	

$str_id = (isset($_SESSION["pres_insertId"]) && $_SESSION["pres_insertId"] != "") ? $_SESSION["pres_insertId"] : "";  //MYSQL INSERT ID
$str_action = (isset($_SESSION["pres_per_action"]) && $_SESSION["pres_per_action"] != "") ? $_SESSION["pres_per_action"] : ""; //ACTION OF SAVE OR PRINT
$str_save_close = (isset($_SESSION["pres_ele_save_close"]) && $_SESSION["pres_ele_save_close"] != "") ? $_SESSION["pres_ele_save_close"] : ""; //SAVE CLOSE POP UP DEFAULT 0
//========SAVING WORKS ENDS HERE=======================================

//========GET PRESCRIPTION INFORMATION CALL BY checkinfo FUNCTION BELOW
//========CALL BY checkinfo FUNCTION BELOW==============================
if(isset($_GET["medication"]) && $_GET["medication"] != "")
{	
	$pres_key=trim($_GET['medication']);
	
	$qryPresInfo ="	SELECT 
						*
					FROM 
						`common_prescription`
					WHERE 
						pres_key='$pres_key'
					AND 
						(
							providerID='$authUserID' 
						OR 
							adminPresc='1'
						)
					ORDER BY adminPresc 
				 "; 
		  
				$resPresInfo=@imw_query($qryPresInfo);
				if(@imw_num_rows($resPresInfo)>0)	{
					
					$rowPresInfo=@imw_fetch_array($resPresInfo);
					
					$qty1		=	$rowPresInfo['qty'];
					$drug1		=	$rowPresInfo['drug'];
					$key1		=	$rowPresInfo['pres_key'];
					$dosage1	=	$rowPresInfo['dosage'];
					$refill1	=	$rowPresInfo['refill'];
					$eye1		=	$rowPresInfo['eye'];
					$usage1		=	$rowPresInfo['usage_1'];
					$usage_22	=	$rowPresInfo['usage_2'];
					$d_unit1	=	$rowPresInfo['dosage_unit'];
					$direction1	=	$rowPresInfo['direction'];
					$quantity_unit1	=	$rowPresInfo['qty_unit'];
					$substitute 	=	$rowPresInfo['substitute'];
					
					$medicine	=	$drug1."-".$dosage1."-".$d_unit1."-".$qty1."-".$quantity_unit1."-".$direction1."-".$eye1."-".$usage_22."-".$usage1."-".$refill1."-".$substitute;
					echo $medicine;
				}
				unset($_GET);
}
?>
<html>
<head>
<title>Welcome to the Clinic</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Prescription</title>
<!----------BOOTSTRAP FILES INCLUSION----------->
<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<!----------REGULAR CSS FILES INCLUSION--------->
<link href="<?php echo $library_path; ?>/css/core.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/workview.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/wv_landing.css" rel="stylesheet" type="text/css">
<!----------MESSI PLUGIN FOR FANCY ALERTS CSS--->
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">

<!----------JQUERY (NECESSARY FOR BOOTSTRAP'S JAVASCRIPT PLUGINS)--->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<!----------BOOTSTRAP--------------------------->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
<!----------BOOTSTRAP SELECTPICKER------------->
<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
<!----------COMMON JS FILE--------------------->
<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
<!----------FANCY ALERT JS FILE---------------->
<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
<!----------TYPEAHEAD JS FILE------------------>
<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
<script>
//============MEDICATION TYPEAHEAD DATA SET======
var medicationTypeHeadArr = new Array(<?php echo $stringAll;?>);

//==========ALERTS, ACTION ON PRINT AND CLOSE POP UP WORK===
<?php
if($str_id != "" && $str_action != "")
{
?>
	$( document ).ready(function() {
		var recordId = "<?php echo $str_id;?>";
		var perAction = "<?php echo $str_action;?>";
		var alertMsg = "<?php echo $str_message;?>";
		var eleSaveClose = "<?php echo $str_save_close;?>";
		
		if(eleSaveClose == 1)
		{
			window.close();	
		}	
		if(perAction == 'print')
		{
			var parWidth = parent.document.body.clientWidth;
			var parHeight = parent.document.body.clientHeight;
			window.open('print_patient_prescription.php?printType=3&preId='+recordId,'printPatientPrescription','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
			top.fAlert("Record printed, please check print.");
		}
		else if(perAction == 'save')
		{
			top.fAlert("Record save successfully.");
		}		
	})
<?php
	//SESSION PARAMETERS UNSET WHICH WAS USED TO FORWARD DATA PRINTING FILE
	unset($_SESSION['pres_insertId']);
	unset($_SESSION['pres_per_action']);
	unset($_SESSION['pres_ele_save_close']);
}
?>

function restart1()
{
	document.prescribe.st_date.value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
	mywindow.close();
}

function GetXmlHttpObject2()
{ 
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	else if(!xmlhttp&&typeof XMLHttpRequest!="undefined") 
	{

		objXMLHttp=new XMLHttpRequest();
	}

	return objXMLHttp
}
//===TYPEAHEAD WORK CONDITIONAL + OTHER JS WORKS STARTS HERE======
function checkPrescKey(srch)
{
	var ret = false;
	if(typeof medicationTypeHeadArr != "undefined")
	{
		var lenHS = medicationTypeHeadArr.length;
		for(var i=0;i<lenHS;i++)
		{
			if(trim(srch).toLowerCase() == trim(medicationTypeHeadArr[i]).toLowerCase())
			{
				ret = true;
				break;
			}
		}
	}
	return ret;
}

function checkinfo(val)
{
	xmlHttp=GetXmlHttpObject2();
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request")
			return
		} 
		if(val!="")
		{
			if(checkPrescKey(val))
			{
				url="prescription.php?medication="+val;
				xmlHttp.onreadystatechange=checkdruginfo
				xmlHttp.open("GET",url,true);
				xmlHttp.send(null);
				window.status = "Processing....";
			}
			else
			{
			   window.status = "Invalid Key.";
			}
		}
		else
		{

		}
		
}
//==========CHECK DRUG INFORMATION============================
function checkdruginfo()
{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		var result=xmlHttp.responseText;						
		if(trim(result).length > 0)
		{								
			var result2=result.split("-");
			document.prescribe.drug.value=trim(result2[0]);
			document.prescribe.size.value=trim(result2[1]);
			document.prescribe.dosage.value=trim(result2[2]);
			document.prescribe.quantity.value=trim(result2[3]);
			document.prescribe.quantity_unit.value=trim(result2[4]);
			document.prescribe.unit.value=trim(result2[5]);
			document.prescribe.eye.value=trim(result2[6]);
			document.prescribe.usage_2.value=trim(result2[7]);
			document.prescribe.usage.value=trim(result2[8]);
			document.prescribe.refills.value=trim(result2[9]);
			//document.getElementById("refills").value=trim(result2[9]);								
			document.getElementById("usa1").style.display = (document.prescribe.usage_2.value.length > 0) ? 'block' : 'none' ;
			document.prescribe.substitute.value=trim(result2[10]);
			window.status = "Key exists.";
		}
		else
		{
			window.status = "Key does not exists.";
			/*
			document.prescribe.drug.value='';
			document.prescribe.size.value='';
			document.prescribe.dosage.value='';
			document.prescribe.quantity.value='';
			document.prescribe.quantity_unit.value='';
			document.prescribe.unit.value='';
			document.prescribe.eye.value='';
			document.prescribe.usage_2.value='';
			document.prescribe.usage.value='';
			document.getElementById("refills").value=0;
			document.getElementById("usa1").style.display = 'none';
			document.prescribe.substitute.value=1;
			*/
		}
			
	}
	else
	{
		
	}
}
//==========RESET OR RELOAD THE FORM==================
function pres_reset()
{
	document.prescribe.reset();
}
//==========SUBMIT OR SAVE THE FORM==================
function pres_save()
{   
		document.prescribe.per_action.value="save";
		document.prescribe.submit();
}
//==========SAVE AND PRINT===========================
function pres_print_save_record()
{   
	
	document.prescribe.per_action.value="print";
	document.prescribe.submit();
}
//==========STARTING DATA VALIDATION================
function checkdata()
{
	if(prescribe.st_date.value=='' || prescribe.st_date.value== '00-00-0000')
	{
		msg = msg + ' Starting Date is required\n';
		prescribe.st_date.style.backgroundColor=changecolor;
	}
	else
	{
		validate_dt_dob(prescribe.st_date);
	}
	if(msg == '')
	{
		prescribe.st_date.submit();
	}
	else
	{
		fAlert (msg);
		return false;
	}
}
//==========OPEN ERX WINDOW IF PATIENT IS REGISTERED WITH ERX
function open_erx()
{
	var parentWid = parent.document.body.clientWidth;
	var parenthei = parent.document.body.clientHeight;
	var url="erx_patient_selection.php?loadmodule=prescription";
	window.open(url,'erx_window','scrollbars=1,resizable=1,width='+parentWid+',height='+parenthei+'');
}

//==========CHANGE SAVE IMAGE ICON ON ONCE CLICK TO STOPPED MULTISAVING OF RECORDS
function changeSaveImage(val)
{  
	if(document.getElementById("drugLookUp").value == '')
	{ 
		top.fAlert('Please enter medication name.');
	}
	else if (val == 'save')
	{	
		document.getElementById('save').removeAttribute("onclick");
		pres_save();
	}
	else if (val == 'print')
	{
		document.getElementById('print').removeAttribute("onclick");
		pres_print_save_record();
	}	
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<!----------MAIN DIV------------------------>
<!----------FORMATTNG WORK STARTS----------->
<div class="whtbox">	
<!----------HEADING BAR------------------------>
	<div class="purple_bar">
		<div class="row">
			<div class="col-sm-5">Prescription</div>
			<div class="col-sm-7"><?php echo $patientDetails; ?></div>
		</div>
	</div>
	<div class="presmain">
		<div class="row" id="precs">
			<form name="prescribe" method="post" action="prescription.php" onSubmit="return checkdata();">
			<input type="hidden" name="pres_id" value="<?=$pres_id?>">
			<input type="hidden" name="refilled_by" value="<?php echo $_GET["refilledBy"];?>">
			<input type="hidden" name="elem_saveClose" value="0">	
			<div class="col-sm-5">
				<div class="presleft">
				<div class="row">
					<div class="col-sm-3">Starting Date</div>
					<div class="col-sm-9"> 
						<input type="" class="form-control" name="st_date"  onBlur="checkdate(this);" readonly size='13' maxlength=10 value="<? if($st_date=="" || getNumber($st_date)=="00000000" ||$st_date=="--" ){ echo($tdate);} else {echo($st_date);} ?>" class="date-pick txt_10">
					</div>
					<div class="col-sm-3">Provider</div>
					<div class="col-sm-9 ">
						<div class="row">
						<div class="col-sm-5">	
						<select class="form-control minimal" name="providerID">
							<?php
								//===========GET PHYSICIAN DETAILS=================
								$provQry = "SELECT 
												* 
											FROM
												`users` 
											WHERE 
												user_type = '1' 
											AND 
												delete_status='0' 
											ORDER By
												fname,lname,mname
											";
								$provSql = imw_query($provQry);
								while($provRt = imw_fetch_assoc($provSql))
								{
									if($provRt['lname'] == 'Administrator')
									{
							?>
									<option value='<?php echo $provRt['id'];?>'  <?php if($provRt['id'] == $provid) echo ("selected"); ?> >-- Select Provider --</option>
							<?php
									}
									else
									{
									
							?>
									<option value='<?php echo $provRt['id'];?>'  <?php if($provRt['id'] == $provid) echo ("selected"); ?> >
										<?php echo $provRt['fname'].' '.$provRt['lname'].' '.$provRt['mname'];?>
									</option>
							<?php 
									} 
								} 
							?>
						</select>
						</div>
						<div class="col-sm-7 mt-3">	
					<?php	//=========GET ERX DATA TO VERIFY ERX CONDITIONS============
							$patId = $_SESSION['patient'];
							$qry = "SELECT 
										erx_patient_id
									FROM
										`patient_data`
									WHERE 
										id = '$patId'
									";
							$qryRes = imw_query($qry);
							$res = imw_fetch_assoc($qryRes);	
							$erx_patient_id = $res['erx_patient_id'];
							if($policy_res['Allow_erx_medicare'] == 'Yes' && $erx_patient_id != 'null' && $erx_patient_id != '')
							{
						?>
							<button type="button" id="btNewERXPad" class="btn btn-success btn-sm"  onClick="open_erx()">New e/Rx Pad</button>
						<?php 
							}
						?></div>
						</div>
					</div>
					<div class="col-sm-3">Patient Name</div>
					<div class="col-sm-9">
						<?php
							$patientName = $rt['title'].' ';
							//===========CHANGE PATIENT NAME FORMAT===========
							$patientNameArr = array();
							$patientNameArr['LAST_NAME'] = $rt['lname'];
							$patientNameArr['FIRST_NAME'] = $rt['fname'];
							$patientNameArr['MIDDLE_NAME'] = $rt['mname'];									
							$patientName .= $objManageData->__changeNameFormat($patientNameArr);
							echo trim($patientName);
						?>
					</div>
					<div class="col-sm-3">Medication Name</div>
					<div class="col-sm-9  ">
						<div class="row">
							<div class="col-sm-5">
								<input type="input" class="form-control" size="20" name="drug" value="<?=$vrs['drug']?>" id="drugLookUp" <?php if($_SESSION["sess_privileges"]["priv_admin"] == 1){?> onChange="checkinfo(this.value);" <?php }?> /> 
							</div>
							<div class="col-sm-7 mt-7">	
								<!--BELOW BUTTON IS COMMENTED DUE TO DONOT FOUND WHAT EXACTLY IT WORKS BECAUSE ITS ASSOCIATED FILES NOT EXISTS IN R7
								<button type="button" class="btn btn-success btn-sm drugbut dff_button" onClick="drugPopup=window.open('../../eRx/controller.php?prescription&lookup','drugPopup','width=400,height=50,menubar=no,titlebar=no,left=400,top=400'); drugPopup.opener=self; return true;">Drug Lookup</button>
								-->
							</div>
						</div>
					</div>
					<div class="col-sm-3">Strength</div>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="size" maxlength="10" value="<?=$vrs['size']?>" >
					</div>
					<div class="col-sm-5">
						<select name="dosage" class="form-control minimal">
							<?php
								$i=0;
								foreach($unit_array as $theunit)
								{
							?>
								<option value="<?=$theunit?>" <?php if($theunit == $vrs['dosage']) echo ('selected'); ?> ><? echo $theunit?></option>
							<?php
								$i++;
								}
							?>
						</select>
					</div>
					<div class="col-sm-3">Quantity</div>
					<div class="col-sm-4 "> 
						<input type="text" class="form-control" name="quantity" size="1" maxlength="10" VALUE="<?=$vrs['quantity']?>" />
					</div>
					<div class="col-sm-5"> 
						<select class="form-control minimal" name="quantity_unit">
							<?php
							foreach($arrQuantity as $theunit => $val)
							{
							?>
								<option value="<?php echo $val?>" <?php if($val == $vrs['quantity_unit']) echo ('selected'); ?> ><?php echo $val;?></option>
							<?php
							}
							?>
						</select>
					</div>
					<div class="col-sm-3">Direction</div>
					<div class="col-sm-4 form-inline ">
						<input type="text" name="unit" size="11" maxlength="10" class="form-control direct" VALUE="<?=$vrs['unit']?>"> In  
					</div>
					
					<div class="col-sm-5 form-inline">
						<select class="form-control minimal" name="eye" >
						<?php
							$i=0;
							foreach($eye_array as $theeye)
							{

						?>
							<option value="<?=$i?>" <?php if($i==$vrs['eye']) echo ('selected'); ?> ><?=$theeye?></option>
						<?php

							$i++;
							}
						?>
						</select> 
						<input type="text" name="usage_2" id="usa1" size="5" maxlength="10" class="form-control" VALUE="<?=$vrs['usage_2']?>" style="display:none;"/>
						<select name="usage"  class="form-control minimal" onChange="set_jtype(this.options[this.selectedIndex].value)">
						<?php
							$i=0;
							foreach($use_array as $theuse)
							{
						?>
								<option value="<?=$i?>" <?php if($i==$vrs['usage_1']) echo ('selected'); ?> ><?=$theuse?></option>
						<?php
								$i++;
							}
						?>
						</select>
					</div>
					<div class="col-sm-3">Refill</div>
					<div class="col-sm-4 refill">
						<select name="refills" class="form-control minimal">
						<?php
							for($i=0;$i<=20;$i++)
							{ 
						?>
							<option value="<?php echo($i);?>" <?php if($i==$vrs['refills']) echo ('selected'); ?> ><?php echo($i);?></option>
						<?php 
							}
						?>
						</select>
					</div>
					<div class="col-sm-5">
						<div class="row"><div class="col-sm-4">Substitution</div>  
							<div class="col-sm-5 pr-5 text-right">
								<select name="substitute" class="form-control minimal ">
								<?php
									$i=0;
									foreach($substitute_arr as $theunit)
									{
										?>
										<option value="<?=$i?>" <?php if($i==$vrs['substitute']) echo ('selected'); ?> ><?=$theunit?></option>
										<?php
										$i++;
									}
								?>	
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-3">Notes</div>
					<div class="col-sm-9">
						<textarea name="note" class="form-control fldalgn" cols="40" rows="5" wrap="virtual"><?=$vrs['note']?></textarea>
					</div> 
				</div>
				<div class="text-center mt-5" > <br>
					<img src="<?php echo $libaryImagePath; ?>prec_save.png" onClick="changeSaveImage('save')" style="cursor:hand;" title="Save" alt="Save" name="save" id="save"> 
					<img src="<?php echo $libaryImagePath; ?>pres_print.png" onClick="changeSaveImage('print')" style="cursor:hand;" title="Save and Print" alt="Print" id="print"> 
					<img src="<?php echo $libaryImagePath; ?>pres_reload.png" onClick="pres_reset()" style="cursor:hand;" title="Reload" alt="Reload">
				</div>
				</div>
			</div>
			 <input type="hidden" value="" name="per_action" id="per_action">
			</form>
			<div class="col-sm-7">
				<!--------ALLERGIES SECTION------->
				<div class="presalerg">
					<h2>Allergies</h2>
					<iframe class="form-control" name="iframe_allergies" style="width:100%;height:135px!important;" width="100%" height="120" scrolling="yes" frameborder="0" src="prescription_allergies.php"></iframe>
				</div>
				<!--------MEDICATION SECTION------>
				<div class="clearfix"></div>
				<div class="presamedica">
					<h2>Medication</h2>
					<iframe class="form-control" name="iframe_medication" style="width:100%;height:135px!important;" width="100%" height="120" scrolling="yes" frameborder="0" src="prescription_medication.php"></iframe>
				</div>
			</div>
		</div>
		<!--------PRESCRIPTION HISTORY SECTION---->
		<div class="row" id="history">
			<iframe class="form-control" name="pres_vi" style="width:100%;height:325px!important;" scrolling="yes" frameborder="0" src="p_pres_med.php">
			</iframe>
		</div>
	</div>
</div>
<!--------FOOTER SECTION---->
<div>
	<footer class="footer text-center" style="paddding-bottom:10px;width:100%; position:fixed; bottom:20px;">
		<center>
			<div>
				<input type="button" class="btn btn-danger" style="text-align:center!important;padding: 8px 15px;" id="butIdCancel" title="Cancel" value="Cancel" onclick="window.close();" autocomplete="off">
			</div>
		</center>		
	</footer>
</div>			
</body>
</html>
<script>
//=========TYPEAHEAD DATA ADDED TO MEDICATION FIELD==========
$("#drugLookUp").typeahead({'source':medicationTypeHeadArr});
</script>