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

//------FILE INCLUSION--------------
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Patient.php');
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
include_once($GLOBALS['fileroot']."/library/classes/Functions.php");

//------GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();
$oSaveFile = new SaveFile($patient_id);
$clTemplate=0;
$file_location = "";

/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
$OBJsmart_tags = new SmartTags;
$arr_smartTags = $OBJsmart_tags->get_smartTags_array();

if($arr_smartTags)
{
    foreach($arr_smartTags as $key=>$val)
	{
        $prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);
    }
}
/*--SMART TAG REPLACEMENT END--*/

function replace_tag_with_property($html, $tag, $element, $elemValue, $nVal)
{
	return preg_replace('/<'.$tag.'[^>]+'.$element.'="'.preg_quote($elemValue, '/').'"[^>]*>/s', $nVal, $html);
}
//------ON SUBMIT PRINT THE DATAon Submit Print The Data//
if($_POST["printOptionType"]!="" && $_POST["finalHtmlForPrinting"]!="")
{
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
		{
			$prescriptionTemplateContentData= $_POST["finalHtmlForPrinting"];
		}
		else
		{
			$prescriptionTemplateContentData= $_POST["finalHtmlForPrinting"] ; //stripslashes($_POST["finalHtmlForPrinting"]);
		}
		
		$printOptionType=$_POST["printOptionType"];
		if(count($_REQUEST['textbox'])>0)
		{
			$arr_text_post=$_REQUEST['textbox'];
				foreach($arr_text_post as $text_val)
				{
					$prescriptionTemplateContentData = replace_tag_with_property($prescriptionTemplateContentData, "input", "value", $text_val, $text_val);
				}
		}
		$prescriptionTemplateContentData=str_ireplace('<input type="text" value="',"",$prescriptionTemplateContentData);// For Safari and IE9
		if(constant("REMOTE_SYNC") != 1 || empty($zOnParentServer))
		{
			$prescriptionTemplateContentData=str_ireplace($web_root.'/interface/common/new_html2pdf/',"",$prescriptionTemplateContentData);			
		}
		//
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
		{
			$prescriptionTemplateContentData=str_ireplace('"'.$web_root.'/interface/common/new_html2pdf/','"',$prescriptionTemplateContentData);		
		}
		//$prescriptionTemplateContentData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/gn_images/','../../data/'.PRACTICE_PATH.'/gn_images/',$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('../../interface/common/new_html2pdf/',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/',"",$prescriptionTemplateContentData);
				
		$prescriptionTemplateContentData=str_ireplace('<INPUT value="',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT value=',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT',"",$prescriptionTemplateContentData);
		
//		<A id=2 class=cls_smart_tags_link href="javascript:;" jQuery1308551487984="1">
		$regpattern='|<a id=(.*) class=(.*) href=(.*)>(.*)<\/a>|U';
		$regpattern2='|<A id=(.*) class=(.*) href=(.*)>(.*)<\/A>|U';
		$prescriptionTemplateContentData = preg_replace($regpattern, "\\4", $prescriptionTemplateContentData);
		$prescriptionTemplateContentData = preg_replace($regpattern2, "\\4", $prescriptionTemplateContentData);		

		$regpattern3='|<a class=(.*) id=(.*) href=(.*)>(.*)<\/a>|U';
		$regpattern4='|<A class=(.*) id=(.*) href=(.*)>(.*)<\/A>|U';
		$prescriptionTemplateContentData = preg_replace($regpattern3, "\\4", $prescriptionTemplateContentData);
		$prescriptionTemplateContentData = preg_replace($regpattern4, "\\4", $prescriptionTemplateContentData);		
		
		$prescriptionTemplateContentData=str_ireplace('" maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		$prescriptionTemplateContentData=str_ireplace('" maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		
		$prescriptionTemplateContentData=str_ireplace('maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=1 size=1 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);

		$prescriptionTemplateContentData=str_ireplace('" size="30" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="60" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="1" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		
		$prescriptionTemplateContentData=str_ireplace('maxLength="30" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" maxLength=30 value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength=30 value=',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="1" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength=1 value=',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="60" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength=60 value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength=60 value=',"",$prescriptionTemplateContentData);// IE9

		// For Safari
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1" tempendtextbox="">',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace(' size=1 tempendtextbox>',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace('" size="60" maxlength="60" tempendtextbox="">',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace('" size=60 tempendtextbox>',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace(' size=60 tempendtextbox>',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace('" size="30" maxlength="30" tempendtextbox="">',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace('" size=30 tempEndTextBox>',"",$prescriptionTemplateContentData);//IE9
		$prescriptionTemplateContentData=str_ireplace(' size=30 tempEndTextBox>',"",$prescriptionTemplateContentData);//IE9
		
//IE10
		$prescriptionTemplateContentData=str_ireplace('type="text" size="60"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace('type="text" size="30"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace('type="text" size="1"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="60" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="60" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="30" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="30" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="1" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="1" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="" autocomplete="off">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1" tempendtextbox="" autocomplete="off">',"",$prescriptionTemplateContentData);// IE
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="" autocomplete="off">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1',"",$prescriptionTemplateContentData);// CHROME
		
		$imgALLReplace=$GLOBALS["include_root"].'/common/new_html2pdf/';
		
		if(constant("REMOTE_SYNC") != 1)
		{
			$prescriptionTemplateContentData= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$prescriptionTemplateContentData);
		}
		
		$imgPicReplace=$GLOBALS["include_root"].'/common/new_html2pdf/pic_vision_pc.jpg';
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/pic_vision_pc.jpg',$imgPicReplace,$prescriptionTemplateContentData);
		//$prescriptionTemplateContentData=str_ireplace("../../interface/main/uploaddir","../../main/uploaddir",$prescriptionTemplateContentData);
		$signatureReplace=$GLOBALS["include_root"].'/common/new_html2pdf/tmp/';
	
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace($web_root."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$prescriptionTemplateContentData);
		
		if(strtoupper(substr(PHP_OS, 0, 3))=='LIN')
		{ 
			$prescriptionTemplateContentData= mb_convert_encoding($prescriptionTemplateContentData, "HTML-ENTITIES", 'UTF-8');
		}
		
		$prescriptionTemplateContentData = str_ireplace('&amp;nbsp;', '&nbsp;', $prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('&nbsp;', ' ', $prescriptionTemplateContentData);
		$getFinalHTMLForGivenMR=$prescriptionTemplateContentData;
		/*$fp = fopen(dirname(__FILE__).'/../common/new_html2pdf/pdffile.html','w');		
		if(strtoupper(substr(PHP_OS, 0, 3))=='LIN')
		{
			//$writeData = fwrite($fp,utf8_decode($getFinalHTMLForGivenMR));
		    $file_location = write_html(utf8_decode($getFinalHTMLForGivenMR));
			
		}else{
			//$writeData = fwrite($fp,$getFinalHTMLForGivenMR);
		    $file_location = write_html($getFinalHTMLForGivenMR);
		}*/
		
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
		{
			//
			//$printOptionStyle = ($printOptionType == 1) ? "p" : "l";
			//$zRemoteServerData["header"] = checkUrl4Remote($GLOBALS["rootdir"]."/common/new_html2pdf/createPdf.php?op=".$printOptionStyle."&htmlFileName=".$html_file_name."&patient_id=".$_SESSION['patient']."");
			
			if(!empty($_POST["ChartNoteImagesStringFinal"]))
			{	
				$ChartNoteImagesString = explode(",",$_POST["ChartNoteImagesStringFinal"]);
			}
			else
			{
				$ChartNoteImagesString=array();
			}
			
			$tmp_arr=array();
			if(count($ChartNoteImagesString)>0)
			{			
				foreach($ChartNoteImagesString as $key11 => $var11)
				{
					if(!empty($var11) && file_exists($var11))
					{
						$tmp_url = remsyn_makefulltourl($var11);	
						
						//replace paths with url
						$prescriptionTemplateContentData = str_replace($var11, $tmp_url, $prescriptionTemplateContentData );
						$tmp_arr[]= $tmp_url;
					}			
				}			
			}		
			$ChartNoteImagesStringFinal = $tmp_arr;
			
			
			$zRemoteServerData["pdf_data"]["html_data"] = $prescriptionTemplateContentData;
			$zRemoteServerData["pdf_data"]["html_data_location"] = $GLOBALS["remote"]["incdir"].'/common/new_html2pdf/pdffile.html';
			$zRemoteServerData["pdf_data"]["images_pth"] = $ChartNoteImagesStringFinal;			
			
		}
		else
		{
		
			$getFinalHTMLForGivenMR=$prescriptionTemplateContentData;
			//$fp = fopen(dirname(__FILE__).'/../common/new_html2pdf/pdffile.html','w');		
			if(strtoupper(substr(PHP_OS, 0, 3))=='LIN')
			{
			
				//$writeData = fwrite($fp,utf8_decode($getFinalHTMLForGivenMR));	    
				$file_path = write_html($getFinalHTMLForGivenMR);
			}
			else
			{
			
				//$writeData = fwrite($fp,$getFinalHTMLForGivenMR);
				$file_path = write_html($getFinalHTMLForGivenMR);	
			}
			//CALL TO SEND FAX FILE WITH REQUIRED PARAMETERS
			if(isset($_REQUEST['faxSubmit']) && intval(trim($_REQUEST['faxSubmit']))==1 && ($_REQUEST['faxworkSheetId']) )
			{
				echo '<script type="text/javascript"> window.location="sendfax_gl_cl_rx.php?pdfversion=html2pdf&txtFaxRecipent='.trim($_REQUEST['selectedReferringPhy']).'&txtFaxNo='.trim($_REQUEST['sendFaxNumber']).'&file_location='.$file_path.'&faxedWorksheetId='.trim($_REQUEST['faxworkSheetId']).'";</script>';
				exit;
			}
			
		}
		
		if($file_path && trim($getFinalHTMLForGivenMR))
		{ 
	   ?>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
		<script type="text/javascript">
    		window.focus();
    		var parWidth = 595;
    		var parHeight = 841;
    		var printOptionStyle;
    		<?php 
    		
    		if($printOptionType == 0)
			{
    		?>
    			printOptionStyle = 'l';
    		<?php	
    		}
    		elseif($printOptionType == 1)
			{
    		?>
    			printOptionStyle = 'p';
    		<?php 
			} 
			?>
    		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf('<?php echo $file_path; ?>','p','',true);
			//window.open('../../library/html_to_pdf/createPdf.php?op='+printOptionStyle,'_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
		</script>
		<?php 
		}//exit();
		$flgStopExec = 1;
}
///End On Submit Print The Data//
if(!isset($flgStopExec) || empty($flgStopExec))
{ // $flgStopExec = 1;
$printType = $_REQUEST['printType'];
$printMethod = $_REQUEST['method'];

if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"]))
{
	$form_id = $_SESSION["form_id"];	
}
else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"]))
{	
	$form_id = $_SESSION["finalize_id"];		
}
//------GET PATIENT DATA------------	
$patientId = $_SESSION['patient'];

$qryGetpatientDetail = "SELECT 
							*, 
							date_format(DOB,'".get_sql_date_format()."') as pat_dob,
							date_format(date,'".get_sql_date_format()."') as reg_date, 
							DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge, 
							email AS ptEmail
						FROM 
							`patient_data` 
						WHERE
							id = '".$patientId."'";
							
$rsGetpatientDetail	= imw_query($qryGetpatientDetail);
$numRowGetpatientDetail	= imw_num_rows($rsGetpatientDetail);
if($numRowGetpatientDetail)
{
	extract(imw_fetch_array($rsGetpatientDetail));
	$patientname = $fname.' '.$lname;
	$patientSuffix = $suffix;
	if($street){ $patientAddressFull = $street;	}
	if($street2){ $patientAddressFull .= ' '.$street2.','; }
	if($city)
	{ 
		if(!$street2){ $patientAddressFull .= ',';}
		$patientAddressFull .= ''.$city.', '.$state.' '.$postal_code;
	}
	
	$ptAgeShow = "";
	if($ptAge != ""){ $ptAgeShow = $ptAge."&nbsp;Yr."; }
}
/// End get Patient Data
	
/*$qry1=mysql_query("select date_format(date_of_service,'%m-%d-%Y') as date_of_service from  chart_left_cc_history where patient_id='$patientId' and form_id='$form_id'");
$co=mysql_num_rows($qry1);
if(($co > 0)){
$crow=mysql_fetch_array($qry1);
	$date_of_service =$crow["date_of_service"];
}*/


//------GET WORK SHEET DATA---------
$dateOfCreation = "";
if($_GET["workSheetId"]!="")
{
	$workSheetCreateDate = "";
	$GetDataQuery= "SELECT 
					DATE_FORMAT(clws_savedatetime, '".get_sql_date_format()."') AS worksheetdate,
					dos as workSheetDOS,  
					contactlensmaster.* 
				FROM 
					`contactlensmaster` 
				WHERE
					patient_id='".$_SESSION['patient']."' 
				AND
					clws_id='".trim($_GET["workSheetId"])."'";
					
 $GetDataRes = imw_query($GetDataQuery);				
$GetDataNumRow = imw_num_rows($GetDataRes);
	if($GetDataNumRow>0)
	{
		$resRow=imw_fetch_array($GetDataRes);
		@extract($resRow);
		//$date_of_service=$worksheetdate;
		//print_r($resRow);
		$workSheetCreateDate = $worksheetdate;
	}

}
//------END GET WORK SHEET DATA------

$workSheetId_dos='';
if($_GET["workSheetId"]!="")
{
	$workSheetId_dos= "and cl.clws_id='".trim($_GET["workSheetId"])."'";
}

//------GET DOS FROM CHART_MASTER_TABLE BASED ON FORM ID
$qryGetDOS="SELECT 
				DATE_FORMAT(cmt.date_of_service, '".get_sql_date_format()."') as date_of_service
			FROM
				`chart_master_table` as cmt 
				INNER JOIN contactlensmaster as cl on(cmt.id=cl.form_id) 
			WHERE
				cl.patient_id='".$patientId."' ".$workSheetId_dos." 
			ORDER BY cl.clws_id DESC";
			
$resGetDOS=imw_query($qryGetDOS);
if(imw_num_rows($resGetDOS)>0)
{
	$rowDos=imw_fetch_assoc($resGetDOS);
	$date_of_service=$rowDos['date_of_service'];
}
if($_GET["workSheetId"]!="")
{
	if($worksheetdate)
	{
		$qryCheckWorkSheetDOS=" SELECT 
									id 
								FROM 
									`chart_master_table`
								WHERE	
									patient_id='".$patientId."' 
								AND 
									date_of_service='".$workSheetDOS."'";
									
		$resCheckWorkSheetDOS=imw_query($qryCheckWorkSheetDOS);
		if(imw_num_rows($resCheckWorkSheetDOS)>0)
		{
			$date_of_service = $worksheetdate;
		}
	}
}
//------END GET ID WORK------
$today = get_date_format(date('Y-m-d'));

$GetDataQuery= "SELECT 
					cleval.*, 
					cldet.*,
					clMaster.* 
				FROM 
					`contactlensmaster` clMaster 
					LEFT JOIN contactlensworksheet_det cldet ON cldet.clws_id =  clMaster.clws_id 
					LEFT JOIN contactlens_evaluations cleval ON cleval.clws_id = clMaster.clws_id 
				WHERE
					clMaster.patient_id='".$_SESSION['patient']."' 
				AND	
					clMaster.clws_id='".trim($_GET["workSheetId"])."' 
				ORDER BY cldet.id";
$resDet = array();
$query_sql = imw_query($GetDataQuery);
while($row = imw_fetch_array($query_sql))
{
    $resDet[] = $row;
}

//------TYPES OF CL ORDERED----
for($i=0; $i<sizeof($resDet); $i++)
{
	$arrCLTypes[$resDet[$i]['clType']]=$resDet[$i]['clType'];
}
if($_GET["printType"]==2)
{
	$printType="2,4";
}
//------GET SCL TEMPLATE------
if(in_array('scl', $arrCLTypes))
{
	$qryGetTempData = "	SELECT 
							prescription_template_content as prescriptionTemplateContentData,
							printOption 
						FROM 
							`prescription_template`
						WHERE	
							prescription_template_type=2";	
							
	$rsGetTempData = imw_query($qryGetTempData)	or die($qryGetTempData.mysql_error());
	$numRowGetTempData = imw_num_rows($rsGetTempData);
	if($numRowGetTempData>0)
	{
		extract(imw_fetch_array($rsGetTempData));	
		$prescriptionTemplateContentData = '<page>'.stripslashes($prescriptionTemplateContentData);
		$printOptionType = $printOption;
		$prescriptionTemplateContentData.='</page>';
		$clTemplate=1;
	}
}
//------GET RGP TEMPLATE----
if(in_array('rgp', $arrCLTypes) || in_array('rgp_soft', $arrCLTypes) || in_array('rgp_hard', $arrCLTypes) || in_array('cust_rgp', $arrCLTypes))
{
	$qryGetTempDataRGP = "	SELECT 
								prescription_template_content as prescriptionTemplateContentDataRGP,
								printOption 
							FROM
								`prescription_template`
							WHERE
								prescription_template_type=4";
								
	$rsGetTempDataRGP = imw_query($qryGetTempDataRGP)	or die($qryGetTempDataRGP.mysql_error());
	if(imw_num_rows($rsGetTempDataRGP)>0)
	{
		$prescriptionTemplateContentDataRGP='';
		
		extract(imw_fetch_array($rsGetTempDataRGP));	
		$tempVar = $prescriptionTemplateContentDataRGP;
		
		if(in_array('rgp', $arrCLTypes) || in_array('rgp_soft', $arrCLTypes) || in_array('rgp_hard', $arrCLTypes))
		{
			$prescriptionTemplateContentDataRGP= '<page>'.stripslashes($prescriptionTemplateContentDataRGP);
			$printOptionType = $printOption;
			$prescriptionTemplateContentDataRGP.='</page>';
		}
		if(in_array('cust_rgp', $arrCLTypes))
		{
			$prescriptionTemplateContentDataCRGP= '<page>'.stripslashes($tempVar);
			$printOptionType = $printOption;
			$prescriptionTemplateContentDataCRGP.='</page>';
		}
		$clTemplate=1;
	}
}
if($clTemplate==1)
{
	
	$raceShow						 = trim($race);
	$otherRace						 = trim($otherRace);
	if($otherRace){ $raceShow		 = $otherRace; }
	$languageShow					 = str_ireplace("Other -- ","",$language);
	$ethnicityShow					 = trim($ethnicity);			
	$otherEthnicity					 = trim($otherEthnicity);
	if($otherEthnicity){ $ethnicityShow = $otherEthnicity; }
	
	if(in_array('scl', $arrCLTypes))
	{
		$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
		//---MODIFIED VARIABLES---
		$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{DATE}',$date_of_service,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentData);
		//---NEW VARIABLES------
		$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME SUFFIX}',$patientSuffix,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{STATE ZIP CODE}',$state."&nbsp;".$postal_code,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);
	
		$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
		$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
		$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
		$prescriptionTemplateContentData = str_ireplace($web_root.'/library/html_to_pdf/','',$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PT_EMAIL_ADDRESS}',$ptEmail,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{PATIENT_NICK_NAME}',$nick_name,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{DATE_OF_SHEET}',$workSheetCreateDate,$prescriptionTemplateContentData);
	}

	if(in_array('rgp', $arrCLTypes) || in_array('rgp_soft', $arrCLTypes) || in_array('rgp_hard', $arrCLTypes))
	{
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentDataRGP);
		//---MODIFIED VARIABLES-----
		$prescriptionTemplateContentDataRGP = str_ireplace('{DATE}',$today,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentDataRGP);
		//---NEW VARIABLES---------
		$prescriptionTemplateContentDataRGP = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{STATE ZIP CODE}',$state."&nbsp;".$postal_code,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT NAME SUFFIX}',$patientSuffix,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentDataRGP);
	
		$prescriptionTemplateContentDataRGP = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentDataRGP);	
		$prescriptionTemplateContentDataRGP = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentDataRGP);	
		$prescriptionTemplateContentDataRGP = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentDataRGP);	
		$prescriptionTemplateContentDataRGP = str_ireplace($web_root.'/library/html_to_pdf/','',$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{DATE_OF_SHEET}',$workSheetCreateDate,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PT_EMAIL_ADDRESS}',$ptEmail,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{PATIENT_NICK_NAME}',$nick_name,$prescriptionTemplateContentDataRGP);
	}

	if(in_array('cust_rgp', $arrCLTypes))
	{
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentDataCRGP);
		//---MODIFIED VARIABLES Variable---
		$prescriptionTemplateContentDataCRGP = str_ireplace('{DATE}',$today,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentDataCRGP);
		//---NEW VARIABLES-----------------
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{STATE ZIP CODE}',$state."&nbsp;".$postal_code,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT NAME SUFFIX}',$patientSuffix,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentDataCRGP);

		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentDataCRGP);
	
		$prescriptionTemplateContentDataCRGP = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentDataCRGP);	
		$prescriptionTemplateContentDataCRGP = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentDataCRGP);	
		$prescriptionTemplateContentDataCRGP = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentDataCRGP);	
		$prescriptionTemplateContentDataCRGP = str_ireplace($web_root.'/library/html_to_pdf/','',$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{DATE_OF_SHEET}',$workSheetCreateDate,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PT_EMAIL_ADDRESS}',$ptEmail,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{PATIENT_NICK_NAME}',$nick_name,$prescriptionTemplateContentDataCRGP);
	}


 if($resDet[0]['CLSLCEvaluationCommentsOD']!=''){ $notes=$resDet[0]['CLSLCEvaluationCommentsOD']."<br>";}
 if($resDet[0]['CLSLCEvaluationCommentsOS']!=''){ $notes.=$resDet[0]['CLSLCEvaluationCommentsOS']."<br>";}
 if($resDet[0]['CLRGPEvaluationCommentsOD']!=''){ $notesRGP=$resDet[0]['CLRGPEvaluationCommentsOD']."<br>";}
 if($resDet[0]['CLRGPEvaluationCommentsOS']!=''){ $notesRGP.=$resDet[0]['CLRGPEvaluationCommentsOS']."<br>";}

 if($resDet[0]['cl_comment']!=''){ $clcomment = $resDet[0]['cl_comment'];}
 $BRANDRGP = "";
 $BRANDODSCL = "";
 $BRANDOSSCL = "";

 $BRANDODRGP = "";
 $BRANDOSRGP = "";

 $BRANDODCUSTRGP = "";
 $BRANDOSCUSTRGP = "";

 $resSize = sizeof($resDet);
 for($i=0; $i<$resSize;$i++)
 {
	if($printMethod==1)
	{
		if($resDet[$i]['clType']=='scl')
		{
			if($resDet[$i]['clEye']=="OD")
			{
				$odAxis="";
				if(!empty($resDet[$i]['SclaxisOD']))
				{
					$odAxis=$resDet[$i]['SclaxisOD']."&#176;";
					$odAxis = htmlspecialchars($odAxis);
				}
				$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$resDet[$i]['SclBcurveOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$resDet[$i]['SclsphereOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$resDet[$i]['SclCylinderOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',trim($odAxis),$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$resDet[$i]['SclAddOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD DIAMETER}',$resDet[$i]['SclDiameterOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OD COLOR}',$resDet[$i]['SclColorOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{DVA OD}',$resDet[$i]['SclDvaOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{NVA OD}',$resDet[$i]['SclNvaOD'],$prescriptionTemplateContentData);
				
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_SPHERE_OD}',$resDet[$i]['CLSLCEvaluationSphereOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_CYLINDER_OD}',$resDet[$i]['CLSLCEvaluationCylinderOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_AXIS_OD}',$resDet[$i]['CLSLCEvaluationAxisOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_OD}',$resDet[$i]['CLSLCEvaluationDVAOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_OU_OD}',$resDet[$i]['CLSLCEvaluationDVAOU'],$prescriptionTemplateContentData);
				
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_SPHERE_OD}',$resDet[$i]['CLSLCEvaluationSphereNVAOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_CYLINDER_OD}',$resDet[$i]['CLSLCEvaluationCylinderNVAOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_AXIS_OD}',$resDet[$i]['CLSLCEvaluationAxisNVAOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_OD}',$resDet[$i]['CLSLCEvaluationNVAOD'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_OU_OD}',$resDet[$i]['CLSLCEvaluationNVAOU'],$prescriptionTemplateContentData);
				
				$BRANDODSCL .= $arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['det'];
				$MAKEONLYOD .= $arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['make_only'];
				$TYPEONLYOD .= $arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['type_only'];

				//$BRANDODRGP
			}
			if($resDet[$i]['clEye']=="OS")
			{
				$osAxis="";
				if(!empty($resDet[$i]['SclaxisOS']))
				{
					$osAxis=$resDet[$i]['SclaxisOS']."&#176;";
					$osAxis = htmlspecialchars($osAxis);
				}
				$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$resDet[$i]['SclBcurveOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$resDet[$i]['SclsphereOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$resDet[$i]['SclCylinderOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',trim($osAxis),$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$resDet[$i]['SclAddOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS DIAMETER}',$resDet[$i]['SclDiameterOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OS COLOR}',$resDet[$i]['SclColorOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{DVA OS}',$resDet[$i]['SclDvaOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{NVA OS}',$resDet[$i]['SclNvaOS'],$prescriptionTemplateContentData);
				
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_SPHERE_OS}',$resDet[$i]['CLSLCEvaluationSphereOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_CYLINDER_OS}',$resDet[$i]['CLSLCEvaluationCylinderOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_AXIS_OS}',$resDet[$i]['CLSLCEvaluationAxisOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_OS}',$resDet[$i]['CLSLCEvaluationDVAOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_DVA_OU_OS}',"",$prescriptionTemplateContentData);
				
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_SPHERE_OS}',$resDet[$i]['CLSLCEvaluationSphereNVAOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_CYLINDER_OS}',$resDet[$i]['CLSLCEvaluationCylinderNVAOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_AXIS_OS}',$resDet[$i]['CLSLCEvaluationAxisNVAOS'],$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{OVER_REFR_NVA_OS}',$resDet[$i]['CLSLCEvaluationNVAOS'],$prescriptionTemplateContentData);

				//$BRANDOS.=$arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det'];
				//BRANDOSSCL
				$MAKEONLYOS .= $arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['make_only'];
				$TYPEONLYOS .= $arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['type_only'];

				$BRANDOSSCL = $arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det'];
			}
			//OU FILEDS
			$prescriptionTemplateContentData = str_ireplace('{DVA OU}',$resDet[$i]['SclDvaOU'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{NVA OU}',$resDet[$i]['SclNvaOU'],$prescriptionTemplateContentData);
			//==========NEW VARIABLES CREATED FOR REPLACING SCL BOTH EYES OD/OS=========================//
			//==========STARTS HERE=========================//
			$prescriptionTemplateContentData = str_ireplace('{DISINFECTING}',$resDet[$i]['disinfecting'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{REPLENISHMENT}',$resDet[$i]['replenishment'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{WEAR SCHEDULER}',$resDet[$i]['wear_scheduler'],$prescriptionTemplateContentData);
			//==========ENDS HERE=========================//
			
			$BRAND=$arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det'];

			/* $BRANDODSCL = $arrLensManuf[$resDet[$i]['SclTypeOD_ID']]['det'];
 			$BRANDOSSCL = $arrLensManuf[$resDet[$i]['SclTypeOS_ID']]['det']; */
		}

			
		if($resDet[$i]['clType']=='rgp' || $resDet[$i]['clType']=='rgp_soft' || $resDet[$i]['clType']=='rgp_hard')
		{
			if($resDet[$i]['clEye']=="OD")
			{
				$odAxis="";
				if(!empty($resDet[$i]['RgpAxisOD']))
				{
					$odAxis=$resDet[$i]['RgpAxisOD']."&#176;";
					$odAxis = htmlspecialchars($odAxis);
				}	
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD BASE CURVE}',$resDet[$i]['RgpBCOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{POWER OD}',$resDet[$i]['RgpPowerOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD SPHERICAL}',$resDet[$i]['RgpPowerOD'],$prescriptionTemplateContentDataRGP);
				//$prescriptionTemplateContentDataRGP = str_ireplace('{OD CYLINDER}',$resDet[$i]['RgpOZOD']."/".$resDet[$i]['RgpCTOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD CYLINDER}',$resDet[$i]['RgpCylinderOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD AXIS}',trim($odAxis),$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD ADD}',$resDet[$i]['RgpAddOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD DIAMETER}',$resDet[$i]['RgpDiameterOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OD COLOR}',$resDet[$i]['RgpColorOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{DVA OD}',$resDet[$i]['RgpDvaOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{NVA OD}',$resDet[$i]['RgpNvaOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OZ_OD}',$resDet[$i]['RgpOZOD'],$prescriptionTemplateContentDataRGP);		
				$prescriptionTemplateContentDataRGP = str_ireplace('{CT_OD}',$resDet[$i]['RgpCTOD'],$prescriptionTemplateContentDataRGP);		
				$prescriptionTemplateContentDataRGP = str_ireplace('{PC/W OD}','',$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{2 Degree/W OD}','',$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{3 Degree/W OD}','',$prescriptionTemplateContentDataRGP);
				
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_SPHERE_OD}',$resDet[$i]['CLRGPEvaluationSphereOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_CYLINDER_OD}',$resDet[$i]['CLRGPEvaluationCylinderOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_AXIS_OD}',$resDet[$i]['CLRGPEvaluationAxisOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_OD}',$resDet[$i]['CLRGPEvaluationDVAOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_OU_OD}',$resDet[$i]['CLRGPEvaluationDVAOU'],$prescriptionTemplateContentDataRGP);
				
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_SPHERE_OD}',$resDet[$i]['CLRGPEvaluationSphereNVAOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_CYLINDER_OD}',$resDet[$i]['CLRGPEvaluationCylinderNVAOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_AXIS_OD}',$resDet[$i]['CLRGPEvaluationAxisNVAOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_OD}',$resDet[$i]['CLRGPEvaluationNVAOD'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_OU_OD}',$resDet[$i]['CLRGPEvaluationNVAOU'],$prescriptionTemplateContentDataRGP);
				
				$BRANDODRGP.=$arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det'];
				$MAKEONLYODRGP .= $arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['make_only'];
				$TYPEONLYODRGP .= $arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['type_only'];

				$BRANDODRGP = $arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det'];
 				$BRANDOSRGP = $arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
			}
			if($resDet[$i]['clEye']=="OS")
			{		
				$osAxis="";
				if(!empty($resDet[$i]['RgpAxisOS']))
				{
					$osAxis=$resDet[$i]['RgpAxisOS']."&#176;";
					$osAxis = htmlspecialchars($osAxis);
				}	
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS BASE CURVE}',$resDet[$i]['RgpBCOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{POWER OS}',$resDet[$i]['RgpPowerOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS SPHERICAL}',$resDet[$i]['RgpPowerOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS CYLINDER}',$resDet[$i]['RgpCylinderOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS AXIS}',trim($osAxis),$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS ADD}',$resDet[$i]['RgpAddOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS DIAMETER}',$resDet[$i]['RgpDiameterOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OS COLOR}',$resDet[$i]['RgpColorOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{DVA OS}',$resDet[$i]['RgpDvaOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{NVA OS}',$resDet[$i]['RgpNvaOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OZ_OS}',$resDet[$i]['RgpOZOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{CT_OS}',$resDet[$i]['RgpCTOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{PC/W OS}','',$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{2 Degree/W OS}','',$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{3 Degree/W OS}','',$prescriptionTemplateContentDataRGP);
				
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_SPHERE_OS}',$resDet[$i]['CLRGPEvaluationSphereOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_CYLINDER_OS}',$resDet[$i]['CLRGPEvaluationCylinderOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_AXIS_OS}',$resDet[$i]['CLRGPEvaluationAxisOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_OS}',$resDet[$i]['CLRGPEvaluationDVAOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_DVA_OU_OS}',$resDet[$i]['CLRGPEvaluationDVAOU'],$prescriptionTemplateContentDataRGP);
				
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_SPHERE_OS}',$resDet[$i]['CLRGPEvaluationSphereNVAOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_CYLINDER_OS}',$resDet[$i]['CLRGPEvaluationCylinderNVAOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_AXIS_OS}',$resDet[$i]['CLRGPEvaluationAxisNVAOS'],$prescriptionTemplateContentDataRGP);
				$prescriptionTemplateContentDataRGP = str_ireplace('{OVER_REFR_NVA_OS}',$resDet[$i]['CLRGPEvaluationNVAOS'],$prescriptionTemplateContentDataRGP);
				
				$BRANDOSRGP.=$arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
				$MAKEONLYOSRGP .= $arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['make_only'];
				$TYPEONLYOSRGP .= $arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['type_only'];

				$BRANDOSRGP = $arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det'];
 				$BRANDOSRGP = $arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
			}
			//OU FILEDS
			$prescriptionTemplateContentDataRGP = str_ireplace('{DVA OU}',$resDet[$i]['SclDvaOU'],$prescriptionTemplateContentDataRGP);
			$prescriptionTemplateContentDataRGP = str_ireplace('{NVA OU}',$resDet[$i]['SclNvaOU'],$prescriptionTemplateContentDataRGP);
			//==========NEW VARIABLES CREATED FOR REPLACING RGP BOTH EYES OD/OS=========================//
			//==========STARTS HERE==========================//
            $prescriptionTemplateContentDataRGP = str_ireplace('{DISINFECTING}',$resDet[$i]['disinfecting'],$prescriptionTemplateContentDataRGP);
			$prescriptionTemplateContentDataRGP = str_ireplace('{REPLENISHMENT}',$resDet[$i]['replenishment'],$prescriptionTemplateContentDataRGP);
			$prescriptionTemplateContentDataRGP = str_ireplace('{WEAR SCHEDULER}',$resDet[$i]['wear_scheduler'],$prescriptionTemplateContentDataRGP);
			//==========ENDS HERE=========================//
			
			$BRANDRGP=$arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
			//$BRANDODRGP = $arrLensManuf[$resDet[$i]['RgpTypeOD_ID']]['det'];
 			//$BRANDOSRGP = $arrLensManuf[$resDet[$i]['RgpTypeOS_ID']]['det'];
		}	
		if($resDet[$i]['clType']=='cust_rgp')
		{
			if($resDet[$i]['clEye']=="OD")
			{
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD BASE CURVE}',$resDet[$i]['RgpCustomBCOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{POWER OD}',$resDet[$i]['RgpCustomPowerOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD SPHERICAL}',$resDet[$i]['RgpCustomPowerOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD CYLINDER}',$resDet[$i]['RgpCustomCylinderOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD AXIS}',$resDet[$i]['RgpCustomAxisOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD ADD}',$resDet[$i]['RgpCustomAddOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD DIAMETER}',$resDet[$i]['RgpCustomDiameterOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OD COLOR}',$resDet[$i]['RgpCustomColorOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DVA OD}',$resDet[$i]['RgpCustomDvaOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{NVA OD}',$resDet[$i]['RgpCustomNvaOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{2 Degree/W OD}',$resDet[$i]['RgpCustom2degreeOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{3 Degree/W OD}',$resDet[$i]['RgpCustom3degreeOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PC/W OD}',$resDet[$i]['RgpCustomPCWOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{BLEND OD}',$resDet[$i]['RgpCustomBlendOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{EDGE OD}',$resDet[$i]['RgpCustomEdgeOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OZ_OD}',$resDet[$i]['RgpCustomOZOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{CT_OD}',$resDet[$i]['RgpCustomCTOD'],$prescriptionTemplateContentDataCRGP);
				
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_SPHERE_OD}',$resDet[$i]['CLRGPEvaluationSphereOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_CYLINDER_OD}',$resDet[$i]['CLRGPEvaluationCylinderOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_AXIS_OD}',$resDet[$i]['CLRGPEvaluationAxisOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_OD}',$resDet[$i]['CLRGPEvaluationDVAOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_OU_OD}',$resDet[$i]['CLRGPEvaluationDVAOU'],$prescriptionTemplateContentDataCRGP);
				
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_SPHERE_OD}',$resDet[$i]['CLRGPEvaluationSphereNVAOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_CYLINDER_OD}',$resDet[$i]['CLRGPEvaluationCylinderNVAOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_AXIS_OD}',$resDet[$i]['CLRGPEvaluationAxisNVAOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_OD}',$resDet[$i]['CLRGPEvaluationNVAOD'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_OU_OD}',$resDet[$i]['CLRGPEvaluationNVAOU'],$prescriptionTemplateContentDataCRGP);
				
				$BRANDODCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det'];
				//$BRANDODCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['det'];
				$MAKEONLYODCRGP .= $arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['make_only'];
				$TYPEONLYODCRGP .= $arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['type_only'];
			}
			if($resDet[$i]['clEye']=="OS")
			{		
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS BASE CURVE}',$resDet[$i]['RgpCustomBCOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{POWER OS}',$resDet[$i]['RgpCustomPowerOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS SPHERICAL}',$resDet[$i]['RgpCustomPowerOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS CYLINDER}',$resDet[$i]['RgpCustomCylinderOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS AXIS}',$resDet[$i]['RgpCustomAxisOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS ADD}',$resDet[$i]['RgpCustomAddOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS DIAMETER}',$resDet[$i]['RgpCustomDiameterOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OS COLOR}',$resDet[$i]['RgpCustomColorOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{DVA OS}',$resDet[$i]['RgpCustomDvaOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{NVA OS}',$resDet[$i]['RgpCustomNvaOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{2 Degree/W OS}',$resDet[$i]['RgpCustom2degreeOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{3 Degree/W OS}',$resDet[$i]['RgpCustom3degreeOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{PC/W OS}',$resDet[$i]['RgpCustomPCWOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{BLEND OS}',$resDet[$i]['RgpCustomBlendOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{EDGE OS}',$resDet[$i]['RgpCustomEdgeOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OZ_OS}',$resDet[$i]['RgpCustomOZOS'],$prescriptionTemplateContentDataCRGP);
				
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_SPHERE_OS}',$resDet[$i]['CLRGPEvaluationSphereOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_CYLINDER_OS}',$resDet[$i]['CLRGPEvaluationCylinderOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_AXIS_OS}',$resDet[$i]['CLRGPEvaluationAxisOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_OS}',$resDet[$i]['CLRGPEvaluationDVAOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_DVA_OU_OS}',$resDet[$i]['CLRGPEvaluationDVAOU'],$prescriptionTemplateContentDataCRGP);
				
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_SPHERE_OS}',$resDet[$i]['CLRGPEvaluationSphereNVAOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_CYLINDER_OS}',$resDet[$i]['CLRGPEvaluationCylinderNVAOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_AXIS_OS}',$resDet[$i]['CLRGPEvaluationAxisNVAOS'],$prescriptionTemplateContentDataCRGP);
				$prescriptionTemplateContentDataCRGP = str_ireplace('{OVER_REFR_NVA_OS}',$resDet[$i]['CLRGPEvaluationNVAOS'],$prescriptionTemplateContentDataCRGP);
				
	 			$prescriptionTemplateContentDataCRGP = str_ireplace('{CT_OS}',$resDet[$i]['RgpCustomCTOS'],$prescriptionTemplateContentDataCRGP);
				$BRANDOSCRGP.=$arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['det'];
				$MAKEONLYOSCRGP .= $arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['make_only'];
				$TYPEONLYOSCRGP .= $arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['type_only'];
			}
			//OU FILEDS
			$prescriptionTemplateContentDataCRGP = str_ireplace('{DVA OU}',$resDet[$i]['SclDvaOU'],$prescriptionTemplateContentDataCRGP);
			$prescriptionTemplateContentDataCRGP = str_ireplace('{NVA OU}',$resDet[$i]['SclNvaOU'],$prescriptionTemplateContentDataCRGP);
			//==========NEW VARIABLES CREATED FOR REPLACING CUSTOM RGP BOTH EYES OD/OS=========================//
			//==========STARTS HERE==========================//
            $prescriptionTemplateContentDataCRGP = str_ireplace('{DISINFECTING}',$resDet[$i]['disinfecting'],$prescriptionTemplateContentDataCRGP);
			$prescriptionTemplateContentDataCRGP = str_ireplace('{REPLENISHMENT}',$resDet[$i]['replenishment'],$prescriptionTemplateContentDataCRGP);
			$prescriptionTemplateContentDataCRGP = str_ireplace('{WEAR SCHEDULER}',$resDet[$i]['wear_scheduler'],$prescriptionTemplateContentDataCRGP);
			//==========ENDS HERE=========================//
			$BRANDCRGP=$arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det']."&nbsp;".$arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det'];
			$BRANDODCUSTRGP = $arrLensManuf[$resDet[$i]['RgpCustomTypeOD_ID']]['det'];
 			$BRANDOSCUSTRGP = $arrLensManuf[$resDet[$i]['RgpCustomTypeOS_ID']]['det'];
		}			
  
	 }
	 if($BRANDOD){$BRANDOD.="<br>";}
	 if($BRANDOS){$BRANDOS.="<br>";}
	 if($BRAND){$BRAND.="<br>";}
	 
	 $BRANDODRGP.="<br>";
	 $BRANDOSRGP.="<br>";
	 $BRANDRGP.="<br>";

	 $BRANDODCRGP.="<br>";
	 $BRANDOSCRGP.="<br>";
	 $BRANDCRGP.="<br>";
 }
	if(in_array('scl', $arrCLTypes))
	{
		$BRAND=strip_tags($BRAND,"<br>");
		$BRANDOD=str_replace("<br>","",$BRANDOD);
		$BRANDOS=str_replace("<br>","",$BRANDOS);
		
		$prescriptionTemplateContentData = str_ireplace('{BRAND}',$BRAND,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{BRAND OD}',$BRANDODSCL,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{BRAND OS}',$BRANDOSSCL,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE}',$BRAND,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE OD}',$BRANDODSCL,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE OS}',$BRANDOSSCL,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE TYPE OD}',$TYPEONLYOD,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE TYPE OS}',$TYPEONLYOS,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{CL COMMENT}',$clcomment,$prescriptionTemplateContentData);
	
	}
	if(in_array('rgp', $arrCLTypes) || in_array('rgp_soft', $arrCLTypes) || in_array('rgp_hard', $arrCLTypes))
	{
		//$BRANDODRGP=str_replace("<br>","",$BRANDODRGP);
		//$BRANDOSRGP=str_replace("<br>","",$BRANDOSRGP);

		//$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND}',$BRANDRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND OD}',$BRANDODRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{BRAND OS}',$BRANDOSRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MAKE}',$BRANDRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MAKE OD}',$BRANDODRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MAKE OS}',$BRANDOSRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MAKE TYPE OD}',$TYPEONLYODRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{MAKE TYPE OS}',$TYPEONLYOSRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{NOTES}',$notesRGP,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentDataRGP = str_ireplace('{CL COMMENT}',$clcomment,$prescriptionTemplateContentDataRGP);
		$prescriptionTemplateContentData.=$prescriptionTemplateContentDataRGP;
	}

	if(in_array('cust_rgp', $arrCLTypes))
	{
		//$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND}',$BRANDCRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND OD}',$BRANDODCRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{BRAND OS}',$BRANDOSCRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{MAKE}',$BRANDCRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{MAKE OD}',$BRANDODCUSTRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{MAKE OS}',$BRANDOSCUSTRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentData = str_ireplace('{MAKE TYPE OD}',$TYPEONLYODCRGP,$prescriptionTemplateContentData);
		$prescriptionTemplateContentData = str_ireplace('{MAKE TYPE OS}',$TYPEONLYOSCRGP,$prescriptionTemplateContentData);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{NOTES}',$notesCRGP,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentDataCRGP = str_ireplace('{CL COMMENT}',$clcomment,$prescriptionTemplateContentDataCRGP);
		$prescriptionTemplateContentData.=$prescriptionTemplateContentDataCRGP;
	}
	
	list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
	$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt+12,$dos_dy,$dos_yr));
	//$expirationDate =date('m-d-Y',mktime(0,0,0,date('m')+12,date('d'),date('Y')));// date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
	$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);
	
	$apptFacPhone="";
	$apptFacInfo = __getApptInfo($_SESSION['patient'],'','','');
	$apptFacname = $apptFacInfo[2];
	
	if(!empty($apptFacInfo[10])){ $apptFacstreet = $apptFacInfo[10].', ';}
	if(!empty($apptFacInfo[11])){ $apptFaccity = $apptFacInfo[11].', '; }
	if(!empty($apptFacInfo[3])){ $apptFacPhone = $apptFacInfo[3]; }
	
	$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY PHONE}',$apptFacPhone,$prescriptionTemplateContentData);
	
	$objManageData = new ManageData();  //OBJECT USED TO CALL FUNCTIONS.PHP CLASS FUNCTIONS
	//=========LOGGED IN FACILITY INFO VOCABULARY REPLACEMENTS STARTS HERE==========================
	$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = $loginFacility = $loginFacAddress = "";
	$loggedfacilityInfoArr 	= $objManageData->logged_in_facility_info($_SESSION['login_facility']);
	$loggedfacstreet 		= $loggedfacilityInfoArr[1];
	$loggedfacity 			= $loggedfacilityInfoArr[2];
	$loggedfacstate	= $loggedfacilityInfoArr[3];
	$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
	$loggedfacExt	   		= $loggedfacilityInfoArr[5];
	if($loggedfacPostalcode && $loggedfacExt){ $loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;}
	else{ $loggedzipcodext = $loggedfacPostalcode; }
	
	$loginFacility 	= $loggedfacilityInfoArr[0];
	$loginFacAddress = $loggedfacstreet.', '.$loggedfacity.',&nbsp;'.$loggedfacstate.'&nbsp;'.$loggedzipcodext;
	
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loginFacility,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loginFacAddress,$prescriptionTemplateContentData);	 		
	//=============================ENDS HERE===========================================================
	/*$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}',"<span style='background:#c0c0c0;'>____</span>",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}',"<span style='background:#c0c0c0;'>________</span>",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}',"<span style='background#c0c0c0;'>__________</span>",$prescriptionTemplateContentData);
	*/
	$findTextBox=false;
	if((stristr($prescriptionTemplateContentData,"{TEXTBOX_XSMALL}")) || (stristr($prescriptionTemplateContentData,"{TEXTBOX_SMALL}")) || (stristr($prescriptionTemplateContentData,"{TEXTBOX_MEDIUM}")))
	{
		$findTextBox=true;
	}	
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','<input type="text"  value="" size="1"  maxlength="1"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','<input type="text" name="textbox[]" value="" size="30"  maxlength="30"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','<input type="text" name="textbox[]" value="" size="60"  maxlength="60"  tempEndTextBox>',$prescriptionTemplateContentData);

if($form_id!="")
{
//------START SIGNATURE LOGIC------//	
$signaTure=false;
$phy_licence='';
$qryGetSig ="SELECT 
				id,
				doctorId,
				sign_coords,
				sign_path 
			FROM 
				`chart_assessment_plans`
			WHERE 
				form_id ='".$form_id."' 
			AND 
				patient_id = $patientId ";	
				
	$rsGetSig = imw_query($qryGetSig)	or die($qryGetSig.mysql_error());
	$numRowGetSig = imw_num_rows($rsGetSig);
	if($numRowGetSig)
	{
		extract(imw_fetch_array($rsGetSig));	
		if($doctorId>0)
		{
			//---PRINT OF PHYSICIAN TITLE FIRST NAME SECOND NAME AND SUFFIX---
			if($doctorId)
			{
				$getNameQry = imw_query("
										SELECT 
											CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as  PHYSICIANNAME, 
											fname,
											mname,
											lname,
											licence,
											user_npi,
											pro_suffix,
											sign_path 
										FROM 
											`users` 
										WHERE 
											id = '".$doctorId."'"
										);	
				$getNameRow = imw_fetch_assoc($getNameQry);
				
				$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
				$phy_fname = $getNameRow['fname'];
				$phy_mname = $getNameRow['mname'];
				$phy_lname = $getNameRow['lname'];
				$phy_suffix = $getNameRow['pro_suffix'];
				$phy_licence = $getNameRow['licence'];
				$phy_npi = $getNameRow['user_npi'];
				$sign_path = trim($getNameRow['sign_path']);
				
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',$phy_npi,$prescriptionTemplateContentData);
			}
			else
			{
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
			}
			
	
			$id = $id;
			$tblName = "chart_assessment_plans";
			$pixelFieldName = "sign_coords";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";
			if($sign_path)
			{
				/*$pathExp=explode("/",$sign_path);
				$singName=end($pathExp);
				$sourceSign='uploaddir'.$sign_path;
				$destinSign =dirname(__FILE__)."/../../library/html_to_pdf/".$singName;
				copy($sourceSign,$destinSign); */
				$destinSign = trim($GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.$sign_path);
				if(file_exists($destinSign))
				{					
					
					//if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){						
						$gdFilenamePath=$destinSign;//realpath($destinSign);
						if($linux_server_path!="")
						{
							$gdFilenamePath=str_ireplace($linux_server_path,'',$destinSign);	
						}
						$ChartNoteImagesString[]=$gdFilenamePath;
					//}					
					
					$TData = "<img src='".$gdFilenamePath."' height='83' width='225'>";	
					$signaTure=true;
					$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);	
				}
			}
			else if(trim($sign_coords)!="")
			{
				include(dirname(__FILE__)."/imgGd.php");
				
				if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
				{
					$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
					$gdFilenamePath=realpath($gdFilename);
					if($linux_server_path!="")
					{
						$gdFilenamePath=str_ireplace($linux_server_path,'',$gdFilename);
					}
					$ChartNoteImagesString[]=$gdFilenamePath;
				}
				else
				{
					$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
					$gdFilenamePath=realpath($gdFilename);
					if($linux_server_path!="")
					{
						$gdFilenamePath=str_ireplace($linux_server_path,'',$gdFilename);
					}
					$ChartNoteImagesString[]=$gdFilenamePath;					
				}
				
				$TData = "<img src=".$gdFilenamePath." height='83' width='225'>";
				$signaTure=true;	
				$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);
			}
		}
		
	}
//------GIVE PRIORITY TO MASTER CHART NOTES PROVIDER------//

	$qryGetProvider="SELECT 
						id,
						providerId 
					FROM 
						`chart_master_table` 
					WHERE  
						id ='".$form_id."' 
					AND 
						patient_id ='".$patientId."'";	
						
	$rsGetProviderId = imw_query($qryGetProvider)	or die($qryGetProvider.mysql_error());
	$numRowProviderGetSig = imw_num_rows($rsGetProviderId);
	if($numRowProviderGetSig && $signaTure==false)
	{
		extract(imw_fetch_array($rsGetProviderId));
		if($providerId>0)
		{
			//---PRINT OF PHYSICIAN TITLE FIRST NAME SECOND NAME AND SUFFIX
			if($providerId)
			{
				$getNameQry = imw_query("SELECT 
											CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,
											fname,
											mname,
											lname,
											licence,
											pro_suffix
										FROM 
											`users` 
										WHERE 
											id = '".$providerId."'");	
				
				$getNameRow = imw_fetch_assoc($getNameQry);
				
				$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
				$phy_fname = $getNameRow['fname'];
				$phy_mname = $getNameRow['mname'];
				$phy_lname = $getNameRow['lname'];
				$phy_suffix = $getNameRow['pro_suffix'];
				$phy_licence = $getNameRow['licence'];
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
			}
			else
			{
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
			}
			
			$id = $providerId;
			$tblName = "users";
			$pixelFieldName = "sign";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";
			include(dirname(__FILE__)."/imgGd.php");
			
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
			{
				$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
				$gdFilenamePath=realpath($gdFilename);
				if($linux_server_path!="")
				{
					$gdFilenamePath=str_ireplace($linux_server_path,'',$gdFilename);
				}
				$ChartNoteImagesString[]=$gdFilenamePath;
			}
			else
			{
				$gdFilename = dirname(__FILE__)."/../../library/html_to_pdf/tmp/".$gdFilename;
				$gdFilenamePath=realpath($gdFilename);
				if($linux_server_path!="")
				{
					$gdFilenamePath=str_ireplace($linux_server_path,'',$gdFilename);
				}
				$ChartNoteImagesString[]=$gdFilenamePath;
			}			
			
			if(!empty($gdFilenamePath))
			{
				$TData = "<img src=".$gdFilenamePath." height='83' width='225'>";			
				$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);	
				$signaTure=true;
			}
		}
		else
		{
			die("Chart is not reviewed by Physician");
		}			
	}
	$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$phy_licence,$prescriptionTemplateContentData);
	//----End Give Prioity To Master Chart Notes Provider
	if($signaTure==false)
	{
		$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"",$prescriptionTemplateContentData);		
		$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
	}
//------END SIGNATURE LOGIC-------		
}

/*-----SEARCHING SMART TAGS (IF FOUND)--*/
$showHtmlPage = false;

if($arr_smartTags)
{
	foreach($arr_smartTags as $key=>$val)
	{
		$showHtmlPage = stripos($prescriptionTemplateContentData,"[".$val."]");
		if($showHtmlPage !== false)
		{//smarttag found
			$showHtmlPage = true;
			break;
		}
	}
	foreach($arr_smartTags as $key=>$val)
	{
		$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
	}	
}

	//---IN CASE OF CONTACT LENS SELECTED BOTH SCL AND RGP IN INTERFACE------
	$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD DIAMETER}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD ADD}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD COLOR}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{BRAND}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{BRAND OD}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{POWER OD}',"",$prescriptionTemplateContentData);
	
	$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS DIAMETER}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS ADD}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS COLOR}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{BRAND OS}',"",$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{POWER OS}',"",$prescriptionTemplateContentData);
	
	$prescriptionTemplateContentData = str_ireplace('{CL COMMENT}',"",$prescriptionTemplateContentData);

	//====================================================================//
if(constant("REMOTE_SYNC") != 1)
{
	if (strncasecmp(PHP_OS, 'WIN', 3) == 0) 
	{
		$prescriptionTemplateContentData=str_ireplace($web_root.'/library/html_to_pdf/',"",$prescriptionTemplateContentData);
	}
	else
	{
    	$prescriptionTemplateContentData=str_ireplace($webserver_root.'/library/html_to_pdf/',"",$prescriptionTemplateContentData);
	}
}

$serverRootDirectoryName = rtrim($webServerRootDirectoryName,'/'); 
$prescriptionTemplateContentData = str_ireplace($serverRootDirectoryName,'',$prescriptionTemplateContentData);
$prescriptionTemplateContentData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
$prescriptionTemplateContentData = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
$prescriptionTemplateContentData = str_ireplace('/'.$RootDirectoryName.'../../data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
$prescriptionTemplateContentData=str_ireplace('../../library/html_to_pdf/',"",$prescriptionTemplateContentData);
$prescriptionTemplateContentData=str_ireplace('../../library/html_to_pdf',"",$prescriptionTemplateContentData);		
$prescriptionTemplateContentData=str_ireplace($web_root."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$prescriptionTemplateContentData);
//$prescriptionTemplateContentData=str_ireplace($webServerRootDirectoryName.PRACTICE_PATH,$protocol.$myExternalIP."/".PRACTICE_PATH,$prescriptionTemplateContentData);

$prescriptionTemplateContentData = str_ireplace('&nbsp;',' ',$prescriptionTemplateContentData);
$prescriptionTemplateContentData = str_ireplace('{EDGE OS}','',$prescriptionTemplateContentData);
$prescriptionTemplateContentData = str_ireplace('{EDGE OD}','',$prescriptionTemplateContentData);

$file_path = write_html(html_entity_decode($prescriptionTemplateContentData));
//echo "write data: ".$writeData;
//remote
if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer))
{
	$getFinalHTMLForGivenMR = $prescriptionTemplateContentData;
	$getFinalHTMLForGivenMR_Org = $getFinalHTMLForGivenMR;
	$ChartNoteImagesString_Org = $ChartNoteImagesString;
	$tmp_arr=array();
	if(count($ChartNoteImagesString)>0)
	{			
		foreach($ChartNoteImagesString as $key11 => $var11)
		{
			if(!empty($var11) && file_exists($var11))
			{
				$tmp_url = remsyn_makefulltourl($var11);	
				//replace paths with url
				$getFinalHTMLForGivenMR = str_replace($var11, $tmp_url, $getFinalHTMLForGivenMR );
				$tmp_arr[]= $tmp_url;
			}			
		}			
	}		
	$ChartNoteImagesStringFinal = $tmp_arr;

	//
	//$printOptionStyle = ($printOptionType == 1) ? "p" : "l";
	//$zRemoteServerData["header"] = checkUrl4Remote($GLOBALS["remote"]["rootdir"]."/common/new_html2pdf/createPdf.php?op=".$printOptionStyle."&htmlFileName=".$html_file_name."&patient_id=".$_SESSION['patient']."");


	$zRemoteServerData["pdf_data"]["html_data"] = $getFinalHTMLForGivenMR;
	$zRemoteServerData["pdf_data"]["html_data_location"] = $GLOBALS["remote"]["incdir"].'/library/html_to_pdf/pdffile.html';
	$zRemoteServerData["pdf_data"]["images_pth"] = $ChartNoteImagesStringFinal;

}
//remote
if(($showHtmlPage || $findTextBox==true || ($_REQUEST['sectionName'] && $_REQUEST['sectionName']=='fromPRS')))
{
	include_once(dirname(__FILE__)."/include_show_cl_print_html.php");
}
else
{
	
	if($file_path && trim($prescriptionTemplateContentData))
	{
		?>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
		<script type="text/javascript">
			var parWidth = 595;
			var parHeight = 841;
			<?php 
				if($printOptionType == 0)
				{
				?>
					printOptionStyle = 'l';
				<?php	
				}
				elseif($printOptionType == 1)
				{
				?>
					printOptionStyle = 'p';
				<?php	
				}
			?>
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf('<?php echo $file_path; ?>','p','',true);
			//var file_location = '<?php //echo $_SERVER['DOCUMENT_ROOT'].$GLOBALS['webroot']; ?>/library/html_to_pdf/pdffile.html';
			//top.html_to_pdf(file_location, 'p', 'contactlens');
			//window.open('../../library/html_to_pdf/createPdf.php?op='+printOptionStyle,'_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
			//window.open('../../library/html_to_pdf/createPdf.php?op='+printOptionStyle,'_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
        
        </script>
		<?php 
	}
}//end of else of $showHtmlPage

}
else
{
	echo "<script>alert('Please create your Contact Lenses template to proceed for print.');</script>";
}
}//flgstopexec
?>
<script type="text/javascript">
var smart_tag_current_object = new Object;
$(document).ready(function()
{
	$('.cls_smart_tags_link').mouseup(function(e)
	{
		if(e.button==2)
		{
			alert("Line 1159");
			$('#smartTag_parentId').val($(this).attr('id'));
			smart_tag_current_object = $(this);
			display_tag_options(e);
		//	document.oncontextmenu="return false;"    		
		}
		
	});
});
function display_tag_options(e, obj)
{
	$('#div_smart_tags_options').css('left',e.pageX+10);
	$('#div_smart_tags_options').css('top',e.pageY+10);
	$('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="../../images/ajax-loader.gif">');
	$('#div_smart_tags_options').show();
	var parentId = $('#smartTag_parentId').val();
	$.ajax({
		type: "GET",
		url: "../admin/documents/smart_tags/ajax.php?do=getTagOptions&id="+parentId,
		success: function(resp){
			$('#div_smart_tags_options').html(resp);
		}
	});
}
</script>
<!DOCTYPE HTML>
<html>
<head>
	<title>Print MR Prescriptions For Patient</title>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script language="javascript" type="text/javascript">
// Function Set Value Attribute For All Inputs To Resolve Issues in IE9 Safari//
	function setTouchInputs() { 
	var everything = document.getElementsByTagName('input'); 
	var everythinglength = everything.length; 
	for(var i = 0;i<everythinglength;i++) {
		try{
			everything[i].setAttribute('value',everything[i].value);
			}
			catch(e){
				alert(e.message); 
				} 
			}
	} 	
// Function Set Value Attribute For All Inputs To Resolve Issues in IE9 Safari//	
// Function Set InnerHTML of Final Output In TextArea And Submit Form To Print//	
function submitPrintRequest()
{
		setTouchInputs();// Set Value Attribute
		
		<?php 
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		?>			
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer_ORG") )
		{
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer_ORG").innerHTML;
			return true;
		}
		else
		{
		return false;
		
		<?php }else{ ?>
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer") )
		{
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer").innerHTML;
			return true;
		}
		else
		{
		return false;
		
		<?php } ?>
	}
}
//------FUNCTION SET INNERHTML OF FINAL OUTPUT IN TEXTAREA AND SUBMIT FORM TO PRINT	
</script>
<link rel="stylesheet" type="text/css" href="<?php echo !empty($GLOBALS["remote"]['webroot']) ? $GLOBALS["remote"]['webroot'] : $GLOBALS['webroot'];?>/interface/themes/default/common.css">
<body class="body_c"  bgcolor="#ffffff" topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 marginwidth=0 marginheight=0 o1ncontextmenu="return false;">
	<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
	<div id="div_smart_tags_options" class="modal" role="dialog">
		<div class="modal-dialog modal-sm">
	  	<!-- Modal content-->
	    <div class="modal-content">
	    
	    	<div class="modal-header bg-primary">
	      	<button type="button" class="close" onclick="$('#div_smart_tags_options').hide();">X</button>
	        <h4 class="modal-title" id="modal_title">Smart Tag Options</h4>
	     	</div>
	      
	      <div class="modal-body pd0" style="min-height:250px; max-height:400px; overflow:hidden; overflow-y:auto;">
	      	<div class="loader"></div>
	      </div>
	      	
	      <div class="modal-footer pd5"></div>
	      
	  	</div>
	  </div>
	</div>
	<script type="text/javascript">
	var smart_tag_current_object = new Object;
	$(document).ready(function(){
		if(typeof(win_op)=='undefined'){var win_op='';}
		if(win_op==''){
			$('.cls_smart_tags_link').mouseup(function(e)
			{
				if(e.button==0 || e.button==2)
				{ //click event.
					$('#smartTag_parentId').val($(this).attr('id'));
					smart_tag_current_object = $(this);
					display_tag_options(e);
				}			
			});
		}
		else if(win_op==1)
		{
			$('.cls_smart_tags_link').click(function(e)
			{
					$('#smartTag_parentId').val($(this).attr('id'));
					smart_tag_current_object = $(this);
					display_tag_options(e);
			}
			);
		}
	});
	
	function display_tag_options(e)
	{
		//css({'left':e.pageX,'top':e.pageY})
		$('#div_smart_tags_options').show();
		
		var parentId = $('#smartTag_parentId').val();
		/*
		ArrtempParentId = parentId.split('_');
		parentId = ArrtempParentId[0];
		*/
		var x = (top.imgPath) ? top.imgPath : top.JS_WEB_ROOT_PATH;
		$.ajax({
			type: "GET",
			url: "<?php echo $GLOBALS['webroot']; ?>/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId+'&is_return=1',
			dataType:"json",
			success: function(resp)
			{
				$('#div_smart_tags_options .modal-title').html(resp.title);
				$('#div_smart_tags_options .modal-body').html(resp.data);
				$('#div_smart_tags_options .modal-footer').html(resp.footer_btn);
				$("object").hide();
			}
		});
		
		$('.close').on('click', function (e)
		{
			 $("object").show();
	    });
	}
	function replace_tag_with_options()
	{
		var strToReplace = '';
		var parentId = $('#smartTag_parentId').val();
		
		var arrSubTags = document.all.chkSmartTagOptions;
		$(arrSubTags).each(function ()
		{
			if( $(this).is(':checked') )
			{
				if(strToReplace=='')
					strToReplace +=  $(this).val();
				else
					strToReplace +=  ', '+$(this).val();
			}
		});
		//alert(strToReplace);
		
		/*--GETTING FCK EDITOR TEXT--*/
		if(strToReplace!='' && smart_tag_current_object)
		{
			$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
			//$(smart_tag_current_object).html(strToReplace);
			var hiddclass = $(smart_tag_current_object).attr('id');
			$('.'+hiddclass).val($(smart_tag_current_object).text());
			/*		
			RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
			var strippedData = $('#hold_temp_smarttag_data').html();
			strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
			*/		
			$('#div_smart_tags_options').hide();
			
			//Enabling Object Signature
			$("object").show();
			
			
			$("object").css({"visibility":"visible"});
		}
		else
		{
			alert('Select Options');
		}
	}
	window.moveTo(0,0);
	window.resizeTo(1200,screen.height);
	</script>
	</body>
</html>