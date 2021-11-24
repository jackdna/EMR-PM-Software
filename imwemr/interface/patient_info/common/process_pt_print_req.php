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

include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
$library_path = $GLOBALS['webroot'].'/library';

$face_check = '';
global $ChartNoteImagesString;
$pid = $_SESSION['patient'];

$cpr = New CmnFunc($pid);

$library_path = $GLOBALS['webroot'].'/library';

if($_REQUEST["glaucoma"]=="1"){
	include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/chart_glucoma_print_inc.php');
	exit();
}

global $ChartNoteImagesString;
$pid = $cpr->patient_id;

$tdate = get_date_format(date("Y-m-d"));

//Get Pt. Data
$pt_data = $cpr->get_pt_data($cpr->patient_id);

// If print then form id
$print_form_id = $_REQUEST['print_form_id'];
if($print_form_id){
	$form_id = $print_form_id;
}

//Get DOS For Chart Notes
$dos_details = $cpr->get_chrt_dos($form_id,$request);
if($dos_details['formIdToPrint']){
	$form_id = $dos_details['formIdToPrint'];
}

//Facesheet check
$dontshowPDF=false;
if(count($_REQUEST['patient_info'])>0){
	$dontshowPDF=true;
	$face_check = true;
}
$face_check = $cpr->chk_facesheet($_REQUEST);

if($face_check == true){
	unset($_REQUEST['patient_info']);
	$_REQUEST['patient_info'][0] = 'face_sheet';
	$border = 0;
}
else{
	$border = 0;
}

if($_REQUEST["chart_nopro"] != '' && ($_REQUEST["glaucoma"] != '' || count($_REQUEST['special_all'])>0 || $face_check)){
$dontshowPDF=true;
}

if($_REQUEST["glaucoma"] != '' || count($_REQUEST['special_all'])>0){
	$dontshowPDF=true;
}

//Get Pt. Image
if($pt_data['p_imagename']){
	$pt_images = $cpr->get_pt_images($pt_data['p_imagename']);
	$patient_img = $pt_images['patient_img'];		//Array 
	$patientImage = $pt_images['patientImage'];		//Single img
	$ChartNoteImagesString = $pt_images['ChartNoteImagesString'];	
}

//Get Opr. details
$qryOPNM = "select id,lname,fname,mname from users where id ='".$_SESSION['authId']."'";
$phyQryRes = imw_query($qryOPNM);
$phyNameArr =imw_fetch_array($phyQryRes);
$phyNameCurrentUser=substr($phyNameArr['fname'],0,1);
$phyNameCurrentUser.=substr($phyNameArr['lname'],0,1);

$opertator_name = strtoupper($phyNameCurrentUser);

//Start code to set log of printed records
if(intval($form_id)==0 && intval($formIdToPrint)>0){
	$cpr->setLogOfPtPrintedRec($pid,$formIdToPrint,$_SESSION['authId'],$databaseDateOfService,'iDoc','pdf');
}else{
	$cpr->setLogOfPtPrintedRec($pid,$form_id,$_SESSION['authId'],$databaseDateOfService,'iDoc','pdf');
}

//Array for variables to be used in JS
$js_php_arr = array();
//Check fax submit
$js_php_arr['fax_submit'] = true;
if(!isset($_REQUEST['faxSubmit']) || intval($_REQUEST['faxSubmit'])==0){
	$js_php_arr['fax_submit'] = false;
}


if(count($_REQUEST['patient_info'])>0){
	$pt_info_str =  implode(",", $_REQUEST['patient_info']);
	$js_php_arr['pt_info_str'] = $pt_info_str;
}

if($_REQUEST["glaucoma"]){
	$js_php_arr['glaucoma_chk'] = ",glaucoma";
}
$js_php_arr['chart_nopro'] = $_REQUEST['chart_nopro'];
$js_php_arr['dontshowPDF'] = $dontshowPDF;
$js_php_arr['face_check']  = $face_check;
$js_php_var = json_encode($js_php_arr);
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Print Complete Patient Record</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/report.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
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
		<script>
			var js_php_var = JSON.parse('<?php echo $js_php_var; ?>');
		</script>
        <script type="text/javascript">
            function GetXmlHttpObject()
            {
                var objXMLHttp = null
                if (window.XMLHttpRequest)
                {
                    objXMLHttp = new XMLHttpRequest()
                } else if (window.ActiveXObject)
                {
                    objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP")
                }
                return objXMLHttp;
            }
            var fcSheet = '<?php if(count($_REQUEST['patient_info'])>0){echo implode(",", $_REQUEST['patient_info']);} ?>';
            var auditCat = '<?php if(count($_REQUEST['patient_info'])>0){echo implode(",", $_REQUEST['patient_info']);} ?>';
            auditCat += '<?php if($_REQUEST["glaucoma"]){echo ",glaucoma";} ?>';	
            window.onafterprint = function(){
                var xmlHttp;				
                xmlHttp = GetXmlHttpObject()				
                if(xmlHttp==null){
                    alert ("Browser does not support HTTP Request");
                    return;
                }

                var url = '../complete_pt_rec/print_audit.php?print_op='+auditCat;
                //alert(url)
                xmlHttp.onreadystatechange = function (){									
                    //alert(xmlHttp.readyState);
                    if(xmlHttp.readyState == 4){
                    //alert(xmlHttp.responseText);						
                        if(xmlHttp.responseText == "DONE"){						
                            return true;
                        }						
                    }
                }
                xmlHttp.open("GET",url,true);
                xmlHttp.send(null);				

            }
            if(fcSheet!='face_sheet') {
                onafterprint();
            }
        </script>
		<style>
		/**---Style Sheet Changes For Printing Only----*/
			.text_9		{ font-family:"verdana"; font-size:10px; color:#000000;}
			.text_9b	{ font-family:"verdana"; font-size:10px; color:#000000; font-weight:bold;}
			.text_9b1	{ font-family:"verdana"; font-size:9px; color:#000000; font-weight:bold;}
			.text_9b2	{ font-family:"verdana"; font-size:9px; color:#000000;}
			.text_10	{ font-family:"verdana"; font-size:11px; color:#000000;}
			.text_10b	{ font-family:"verdana"; font-size:12px; color:#000000; font-weight:bold;  }
			.text		{ font-family:"verdana"; font-size:11px; color:#000000;}	
		/**---Style Sheet Changes For Printing Only----**/
	</style>	
	</head>
	<body topmargin="0" rightmargin="0" leftmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		<?php 
			ob_start();
			$chart_notprintinginclude=false;
			if(is_array($_REQUEST["chart_nopro"])){
				if(count($_REQUEST["chart_nopro"])>0){
				 $chart_notprintinginclude=true;
				}
			}
			$heightTb = 675;
			if($face_check) $heightTb = '100%';
		?>
		<table width="<?php print $heightTb; ?>" border="0" cellspacing="0" rules="none" cellpadding="0">
		<?php
			if($face_check && $dontshowPDF==true){
					$temp_id = "";
					$ptDocsTemplateId = $cpr->check_temp_id_in_pt_doc();
					if($ptDocsTemplateId){
						$temp_id = $ptDocsTemplateId;
						$mode = 'facesheet';
						include_once($GLOBALS['fileroot'].'/interface/chart_notes/scan_docs/load_pt_docs.php');
					?>	
						<script>
							top.$('#div_loading_image').hide();
						</script>
					<?php	
						exit();
					}
				//Getting Past App. Data	
				$arrPastAppData = $cpr->get_past_pt_data();
				
				//Getting Pt. App. details
				if($_REQUEST['apptId']){ $apptId = $_REQUEST['apptId'];}
				$pt_app_details = $cpr->get_pt_app_details($apptId);
				$pt_appt_date = $pt_app_details['pt_appt_date'];
				$pt_appt_time = $pt_app_details['pt_appt_time'];
				$pt_proc_get = $pt_app_details['pt_proc_get'];
			?>
				<tr> 
					<td><b><h1>FACE SHEET</h1></b></td>
					<td><b><?php print $pt_data['title'].' '.$pt_data['patientName']; ?></b></td>
					<td align="center">ID :<b><?php print $pt_data['id']; ?></b></td>
					<td align="center" style="padding-left:30px;"><b><?php print $pt_data['groupDetails'][0]['name']; ?></b></td>
				</tr>
				<tr>
					<td align="left"><?php echo $pt_appt_date." ".$pt_appt_time; ?></td>
					<td>DOB: <b><?php print (trim($pt_data['date_of_birth'])!="" && get_number($pt_data['date_of_birth'])!="00000000") ? $pt_data['date_of_birth']."(".$pt_data['age'].")" : ""; ?></b></td>			
					<td rowspan="6" align="center"><?php print $patientImage; ?></td>
					<td align="center"><b><?php print ucwords($pt_data['groupDetails'][0]['group_Address1']); ?></b></td>						
				</tr>
				<tr> 	
					<td><?php echo $pt_proc_get; ?></td>
					<td>SS#: <b><?php print $pt_data['pt_ss']; ?></b></td>
					<td align="center" class="text_9b"><b><?php print ucwords($pt_data['groupDetails'][0]['group_Address2']); ?></b></td>	
				</tr>
				<tr> 	
					<td><b><?php echo ($arrPastAppData[0]) ? "Past Appt. History" : "" ?></b></td>	
					<td>Sex: <b><?php print $pt_data['sex']; ?></b></td>	
					<td align="center"><b><?php print $pt_data['groupDetails'][0]['group_City'].', '.$pt_data['groupDetails'][0]['group_State'].' '.$pt_data['groupDetails'][0]['group_Zip']; ?></b></td>	
				</tr>
				<tr>
					<td><?php echo $arrPastAppData[0]; ?></td>
					<td></td>
					<td align="center"><b>Ph. # <?php print core_phone_format($pt_data['groupDetails'][0]['group_Telephone']); ?></b></td>
				</tr>
				<tr>
					<td><?php echo $arrPastAppData[1]; ?></td>
					<td></td>
					<td align="center"><b>Fax # <?php print core_phone_format($pt_data['groupDetails'][0]['group_Fax']); ?></b></td>
				</tr>
			<?php
				//exit();
			}else{
				if($chart_notprintinginclude==false && $dontshowPDF==true){ ?>
					<tr>
						<td colspan="8"><hr size="1px" class="text_9b"></td>
					</tr>
					<tr height="25px"> 	
						<?php 
							//---Code to get Provider information of patient
							$providerID = $pt_data['providerID'];
							$provider_details = $cpr->get_pt_provider($form_id,$providerID);
							
							$provider_name = $provider_details["provider_name"];
							$sel_doctorId = $provider_details["doctorId"];
						?>
						<td class="text_10b" align="left" colspan="2">
							<?php print $pt_data['title'].' '.$pt_data['patientName']; ?>&nbsp;
							<?php if($pt_data['facilityPracCode']) print '('.$pt_data['facilityPracCode'].')'; ?></td>
						<td class="text_10b"> SS#:</td>
						<td class="text_9"><?php print $pt_data['pt_ss']; ?></td>
						<td class="text_10b"><?php if($pt_data['date_of_service']) print 'DOS:'; else '&nbsp;'; ?></td>
						<td class="text_9"><?php print $pt_data['date_of_service']; ?></td>
						<td class="text_10b"  align="right" colspan="2"><?php echo (($sel_doctorId!="")?$cpr->showDoctorName($sel_doctorId):$provider_name); ?></td>
					</tr>
					<tr>	
						<td><font class="text_10b">DOB:&nbsp;</font><font class="text_9"><?php print $pt_data['date_of_birth']; ?></font></td>
						<td class="text_9">&nbsp;</td>
						<td class="text_10b">Age: </td>
						<td class="text_9"><?php print $pt_data['age']; ?> Years</td>
						<td class="text_10b">Sex:</td>
						<td class="text_9"><?php print $pt_data['sex']; ?></td>
						<td class="text_10b" align="right">Print Date:</td>
						<td class="text_9" align="right">&nbsp;<?php print get_date_format(date('Y-m-d')); ?></td>
					</tr>
					<tr>
						<td colspan="8" ><hr size="1px" class="text_9b"></td>
					</tr>
				<?php
				}
			} 
		?>	
		</table>
		<?php 
			$headData = ob_get_contents();
			ob_end_clean();
			//CONSULT LETTER IDs	
			$lenclIdsNew=count($_REQUEST["consultLetterToPrint"]);
			if($lenclIdsNew>0){		
				$strCLIdsImplode=implode(', ',$_REQUEST["consultLetterToPrint"]);
			}	
			
			//Get Pt. Info
			if(count($_REQUEST['patient_info']) > 0){
				if($face_check){
					$detail="";
					$detail = $_REQUEST['face_sheet_detail'];
					$dont_print_medical=($_REQUEST["dont_print_medical"]==1)?1:0;
					
					//Audit func. removed in the file below
					include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_faceSheet.php');
					//FACESHEET PRINT RECORD SAVED FROM FRONT DESK FOR IMEDICMONITOR
					$headData = $headData.$patient_print_data;
					if((!empty($_REQUEST['from']) && $_REQUEST['from'] == 'frontDesk') && (!empty($_REQUEST['apptId']) && $_REQUEST['apptId']>0))
					{
						$temp_id = 0;
						$enable_footer='';
						$templateDataArr = array();
						$templateDataArr['patient_id'] = $_SESSION['patient'];
						$templateDataArr['pt_doc_primary_template_id'] = $temp_id;
						$templateDataArr['pt_enable_footer'] = $enable_footer;
						$templateDataArr['template_content'] = $headData;
						/*	REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. */
						$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
						$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
						$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
						$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
						/*--SMART TAG REPLACEMENT END--*/
						$templateDataArr['created_date'] = date('Y-m-d h:i:s');
						$templateDataArr['operator_id'] = $_SESSION['authId'];
						$templateDataArr['template_delete_status'] = 0;
						$templateDataArr['print_from'] = 'scheduler';
						$templateDataArr['appt_id'] = $_REQUEST['apptId'];
						AddRecords($templateDataArr, 'pt_docs_patient_templates');
					}	
					//============END==================================
					$headData = str_replace(array("<newpage>","</newpage>","<PAGE></PAGE>"), array("<PAGE>","</PAGE>",""), $headData);
					$file_location = write_html($headData);
					/*$fp = fopen('../common/html2pdf/pdffile.html','w');
					$putData = fputs($fp,$headData);
					fclose($fp);*/
					
					if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1){
						echo '<script type="text/javascript">window.location="sendfax_chart_summary.php?pdfversion=html2pdf&txtFaxRecipent='.trim($_REQUEST['selectReferringPhy']).'&txtFaxNo='.trim($_REQUEST['send_fax_number']).'&send_fax_subject='.trim($_REQUEST['send_fax_subject']).'&faxConsultMultiId='.$strCLIdsImplode.'&file_location'.$file_location.'";</script>';
						//echo '<input type="hidden" name="txtFaxNo" value="'.trim($_REQUEST['send_fax_number']).'">';
						exit;
					}
				
					$query_staring = array();
					if(count($patient_img)>0){
						foreach($patient_img as $key => $val){
							$query_staring[] = $val;
						}
					}
					$queryStaring = implode(',',$query_staring); ?>
					<form name="printFrm" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST">
						<input type="hidden" name="page" value="1.3" >
						<input type="hidden" name="font_size" value="8.0">
						<input type="hidden" name="onePage" value="false">
						<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
						<?php 
						if($_REQUEST["hidexport_report"]=="Yes"){?>
							<input type="hidden" name="pdf_name" value="<?php print $pt_data['fileNamewith'];?>">
							<input type="hidden" name="saveOption" value="F">
							<input type="hidden" name="encPassword" value="<?php echo($pt_data['encPassword']);?>">
						<?php }
						?>
						<input type="hidden" name="images" value="<?php print $queryStaring; ?>" >
					</form>
					<script type="text/javascript">
						window.focus();
						top.$('#div_loading_image').hide();
						document.printFrm.submit();
					</script>
		<?php	}else{
					if($dontshowPDF==true){
						include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_demographicsAll.php');
						print $patient_print_data;				
					}
				}
			}
			//A-Scan Print
			if(count($_REQUEST['special_all'])>0){ ?>
				<form name="printFrmAS" action="../complete_pt_rec/ascan-pdf-print.php" method="post">
					<input type="hidden" name="formIdToPrint" value="<?php echo($_REQUEST["formIdToPrint"][0]);?>"/>
					<?php
						if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1){
					/*	echo '<script type="text/javascript">window.location="sendfax_chart_summary.php?txtFaxNo='.trim($_REQUEST['send_fax_number']).'";</script>';*/
							echo '<input type="hidden" name="txtFaxNo" value="'.trim($_REQUEST['send_fax_number']).'">';
							echo '<input type="hidden" name="txtFaxRecipent" value="'.trim($_REQUEST['selectReferringPhy']).'">';
						}
						
					?>	
				</form>
				<script type="text/javascript">
					top.$('#div_loading_image').hide();
					document.printFrmAS.submit();
				</script>
	<?php	}
	
		if($chart_notprintinginclude==true &&  $dontshowPDF==false){
			if(is_array($_REQUEST["chart_nopro"])){
				if(!in_array("Include Provider Notes",$_REQUEST["chart_nopro"])){ 
					$AuditEntryFor="chart_notes_without_provider_notes";
				}
				if(in_array("Include Provider Notes",$_REQUEST["chart_nopro"])){ 
					$AuditEntryFor="chart_notes_with_provider_notes";
				}
			}
			if($chart_notprintinginclude === true){
				$reportName="Visit Notes";
				$lenFIds = count($_REQUEST["formIdToPrint"]);
				
				ob_start();
				echo "<page backtop=\"5mm\" backbottom=\"5mm\">";
				if(count($_REQUEST["formIdToPrint"]) > 0){
					$arrFormIds=$_REQUEST["formIdToPrint"];
					//Header for following files 
					include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/visionPrintWithNotes_1.php');
					foreach($arrFormIds as $key=> $val){			
						if(empty($val))continue;
						
						//Set timeout
						set_time_limit(10);
						$zFormId=$val;	
						$arrDosToPrint = $cpr->print_getDosfromId(array($zFormId));
						$strDosToPrint1 = "'".implode("', '", $arrDosToPrint)."'";
						include($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/visionPrintWithNotes.php');
						//Add Empty Page ---
						if($key<$lenFIds-1){ //do not add at end
							echo "<div style=\"height:100%;border:0px solid red;\"></div>";				
						}
						//Add Empty Page ---
					}
				}else{
					// IF PRINT THEN FORM ID
					$print_form_id = $_REQUEST['print_form_id'];
					if($print_form_id!=""){
						$form_id = $print_form_id;
					}		
					include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/visionPrintWithNotes_1.php');
					$zFormId=$form_id;				
					include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/visionPrintWithNotes.php');
				}
				include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/other_print.php');
				
				//Consult Letters
				$lenclIds=count($_REQUEST["consultLetterToPrint"]);
				if($lenclIds>0){		
					$strCLIds="'".implode("', '",$_REQUEST["consultLetterToPrint"])."'";
					$cpr->print_ConsultLetters($pid, '', $strCLIds);			
				}
				
				//Op Notes
				$lenOpNIds=count($_REQUEST["opNoteToPrint"]);
				if($lenOpNIds>0){		
					$strOpNIds="'".implode("', '",$_REQUEST["opNoteToPrint"])."'";
					$cpr->print_OpNotes($pid, '', $strOpNIds);		
				}	

				echo "</page>";
					$patient_workprint_data = ob_get_contents();
					//exit("DONE");
					ob_end_clean();
			}
			$headDataRR=$patient_workprint_data;
			$headDataRR =  str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$headDataRR);
			$file_location = write_html(stripcslashes($headDataRR));
			$ChartNoteImagesStringFinal=implode(",",$ChartNoteImagesString);
			
			/* if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1){
				echo '<script type="text/javascript">
				top.$("#div_loading_image").hide();window.location="sendfax_chart_summary.php?txtFaxRecipent='.trim($_REQUEST['selectReferringPhy']).'&txtFaxNo='.trim($_REQUEST['send_fax_number']).'&send_fax_subject='.trim($_REQUEST['send_fax_subject']).'&faxConsultMultiId='.$strCLIdsImplode.'&file_location='.$file_location.'";</script>';
				///exit();
				$exit_fxSubmit=1;
			} */
			
		/* 	if(!isset($exit_fxSubmit)){ */
				//---------BEGIN MAKE AN ARRAY OF ALL SELECTED TEST IDS FOR PDF MERGING-----------------	
				$arr = array();
				$arr[] = $_REQUEST["printTestRadioVF"];
				$arr[] = $_REQUEST["printTestRadioHRT"];
				$arr[] = $_REQUEST["printTestRadioOCT"];
				$arr[] = $_REQUEST["printTestRadioGDX"];
				$arr[] = $_REQUEST["printTestRadioPachy"];
				$arr[] = $_REQUEST["printTestRadioIVFA"];
				$arr[] = $_REQUEST["printTestRadioICG"];
				$arr[] = $_REQUEST["printTestRadioFundus"];
				$arr[] = $_REQUEST["printTestRadioExternal_Anterior"];
				$arr[] = $_REQUEST["printTestRadioTopography"];
				$arr[] = $_REQUEST["printTestRadioCellCount"];
				$arr[] = $_REQUEST["printTestRadioLaboratories"];
				$arr[] = $_REQUEST["printTestRadioBscan"];
				$arr[] = $_REQUEST["printTestRadioOther"];
				$arr[] = $_REQUEST["printTestRadioVF_GL"];
				$arr[] = $_REQUEST["printTestRadioOCT_RNFL"];
				$arr[] = $_REQUEST["printTestRadioTemplate_Type"];
                $arr[] = $_REQUEST["printTestRadioIOLMaster"];
                //get all custom tests names
                $q_where = " AND status=1 AND test_table='test_custom_patient' ";
                $q_tests = "SELECT * FROM tests_name WHERE del_status=0 ".$q_where." ORDER BY temp_name";
                $res_tests = imw_query($q_tests);
                if($res_tests && imw_num_rows($res_tests)>0){
                    while($rs_test=imw_fetch_assoc($res_tests)){
                        $testNameID = str_replace(array('-','/',' '), '_', $rs_test['temp_name']);
                        if(isset($_REQUEST["printTestRadio".$testNameID]) && $_REQUEST["printTestRadio".$testNameID]!='') {
                            $arr[] = $_REQUEST["printTestRadio".$testNameID];
                        }
                    }
                }     
				$arrTestIds = array();$strAllTestIds = '';
				if(count(array_filter($arr))>0)
				{
					foreach($arr as $attTmp)
					{
						if(count($attTmp)>0)
						$arrTestIds[] = implode(',',$attTmp);
					}
					if(count($arrTestIds)>0)
					{
						$strAllTestIds = implode(",",$arrTestIds);
					}
				}
				else
				{
					$PathForBrowserPDFDownload = "";
					if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1)
					{
						echo '<script type="text/javascript">
						top.$("#div_loading_image").hide();window.location="sendfax_chart_summary.php?txtFaxRecipent='.trim($_REQUEST['selectReferringPhy']).'&txtFaxNo='.trim($_REQUEST['send_fax_number']).'&send_fax_subject='.trim($_REQUEST['send_fax_subject']).'&faxConsultMultiId='.$strCLIdsImplode.'&file_location='.$file_location.'";</script>';
						exit();
						//$exit_fxSubmit=1;
					}
					else
					{
						$PathForBrowserPDFDownload = "?file_location=".$file_location."&pdf_name=new_pdf";
					}		
				}
				$pdfFile = "patient_".$cpr->patient_id."_".date("H_i_s");//die();
				?>
				<form id="printFrm" name="printFrm" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php<?php echo $PathForBrowserPDFDownload; ?>" method="post">
					<input type="hidden" name="page" value="1.3" >
					<input type="hidden" name="op" value="P" >
					<input type="hidden" name="font_size" value="7.5">
					<input type="hidden" name="name" value="<?php print $pdfFile;?>">
					<input type="hidden" name="mergePDF" id="testId" value="<?php echo '../complete_pt_rec/merge_pdf.php';?>">
					<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
					<input type="hidden" name="testIds" id="testIds" value="<?php echo $strAllTestIds;?>">
					
					<?php
					if($_REQUEST['mail2pt']==1)
					{
					?>
					<input type="hidden" name="ptmailid" value="<?php echo $_REQUEST['ptmailid'];?>">
					<input type="hidden" name="ptmailname" value="<?php echo $_REQUEST['ptmailname'];?>">
					<?	
					}
					
					if($_REQUEST['faxSubmit']==1)
					{
					?>
					<input type="hidden" name="txtFaxRecipent" value="<?php echo trim($_REQUEST['selectReferringPhy']);?>">
					<input type="hidden" name="txtFaxNo" value="<?php echo trim($_REQUEST['send_fax_number']);?>">
					<input type="hidden" name="sendFaxFromCPR" value="1">
					<?php	
					}
					
					if($_REQUEST["hidexport_report"]=="Yes"){?>
					<input type="hidden" name="name" value="<?php print $fileNamewith;?>">
					<input type="hidden" name="saveOption" value="F">
					<input type="hidden" name="encPassword" value="<?php echo($encPassword);?>">
					<?php }
					?>
					<input type="hidden" name="images" value="<?php print $ChartNoteImagesStringFinal; ?>">
			   </form>
				<script type="text/javascript">
					<?php if($_REQUEST['faxSubmit']==1){?>
						top.$("#div_loading_image").show();
					<?php } else {	?>
						top.$("#div_loading_image").hide();
					<?php } ?>
					document.printFrm.submit();
				</script>
				<?php
			/* } */	
		}
	
	?>	
	</body>	
</html>
