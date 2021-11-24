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

//------GLOBAL FILE INCLUSION CALLING--
//require_once(dirname(__FILE__)."/../globals.php");
require_once(dirname(__FILE__)."/../../config/globals.php");

$library_path = $GLOBALS['webroot'].'/library';

include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
include_once($GLOBALS['fileroot']."/library/classes/common_function.php");
//include_once($GLOBALS['fileroot']."/library/html_to_pdf/html2pdf.class.php");
//include_once($GLOBALS['fileroot']."/library/vendor/autoload.php");

//------PDF LIBRARY FILES CALLING------
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter; //DON'T COMMENT IT, IT IS AFFECTING WORK VIEW => CONSULT LETTER => SAVE AND PRINT FUNCTIONALITY


$http_host=$_SERVER['HTTP_HOST'];
if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }

if(is_updox('fax'))
{
	$btnVal = 'CL3';	
}
else if(is_interfax())
{
	$btnVal = 'CL2';
}
	$performed = "Not Performed";
	$tempId = $_REQUEST['tempId'];
	$pat_id = $_SESSION['patient'];
	$doc_id = $_SESSION['authId'];
	$patientId = $_SESSION['patient'];
	$formId = $_SESSION['form_id'];
	
	if(!$formId)	$formId = $_SESSION['finalize_id'];
	
	$today = date('Y-m-d');
	$consultTemplate = $_REQUEST['tempId'];
	$consultTemplateId = $_REQUEST['consult_form_id'];
	$templateId = $_REQUEST['templateList'];
	$templateIds = explode("!~!", $templateId);
		$templateId = $templateIds[0];
		$NameTemplate = $templateIds[1];
	$changeTemplate = $templateId;
	$consultTemplate = $templateId;
	$selectedList = $_REQUEST['selectedList'];
	$patientTempName = $_REQUEST['patientTempName'];
	
	if($_REQUEST['templateId']){ $consultTemplate = $_REQUEST['templateId']; }
	
	//$queryEmailCheck=imw_query("select g.* from groups_new as g join users ON g.gro_id=users.default_group where users.id='$_SESSION[authId]'")or die(imw_error());
	$queryEmailCheck=imw_query("SELECT 
									* 
								FROM 
									`groups_new`
								WHERE 
									config_email!='' 
								AND 
									config_pwd!='' 
								AND 
									del_status='0' 
								ORDER BY 
									name ASC 
								LIMIT 0,1
							  ") or die(imw_error());
	if(imw_num_rows($queryEmailCheck)>=1)
	{
		$dEmailCheck=imw_fetch_object($queryEmailCheck);
		$groupEmailConfig['email']=$dEmailCheck->config_email;
		$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
		$groupEmailConfig['host']=$dEmailCheck->config_host;
		$groupEmailConfig['header']=$dEmailCheck->config_header;
		$groupEmailConfig['footer']=$dEmailCheck->config_footer;
		$groupEmailConfig['port']=$dEmailCheck->config_port;
	}
	imw_free_result($queryEmailCheck);

	// patient_consult_letter_tbl
	$topMargin=$leftMargin=0;
	$refPhyAndFax			= "";
	$refPhyAndEmail			= "";
	if(!$Seq)
	{
		$getMasStatusQry 			= imw_query("SELECT 
													* 
												FROM 
													`patient_consult_letter_tbl` 
												WHERE 
													patient_consult_id  = $tempId
												");
		while($getMasStatusRows 	= imw_fetch_assoc($getMasStatusQry))
		{
			$PatientConsultId 		= $getMasStatusRows['patient_consult_id'];
			$patientConsultstatus 	= $getMasStatusRows['status'];
			$otherTemplateName 		= $getMasStatusRows['templateName'];
			$consultTemplateData 	= $getMasStatusRows['templateData'];
			$consultEmailStatus		= $getMasStatusRows['email_status'];
			$consultFaxStatus 		= $getMasStatusRows['fax_status'];
			$topMargin 				= $getMasStatusRows['top_margin'];
			$leftMargin 			= $getMasStatusRows['left_margin'];	
			$patientConsultLetterTo	= $getMasStatusRows['patient_consult_letter_to'];
			$faxNumber 				= $getMasStatusRows['fax_number'];
			$emailId				= $getMasStatusRows['email_id'];
			$preffered_reff_email	=$getMasStatusRows['email_id'];
			$refPhyId 				= $getMasStatusRows['fax_ref_phy_id'];
			$preffered_reff_fax		= str_ireplace("'","",$getMasStatusRows['preffered_reff_fax']);
			if($refPhyId)
			{
				$refPhyAndFax = get_reffphysician_detail($refPhyId);
				$refPhyAndEmail = get_reffphysician_detail_email($refPhyId);
			}
			if($consultFaxStatus=="1")
			{
				
				//$refPhyFaxNo= $getMasStatusRows['fax_number'];
				if($faxNumber) {
					$refPhyAndFax=  $patientConsultLetterTo."@@".$faxNumber;			
				}
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id'];
				$cc2_ref_phy_id	= $getMasStatusRows['cc2_ref_phy_id'];
				$cc3_ref_phy_id	= $getMasStatusRows['cc3_ref_phy_id'];
			}
			//================STARTS HERE==================================================================
			//CC1 ID USED TO DEFAULT FILLED CC1 FAX NAME AND NO. FOR SAVED CONSULT LETTERS SEND FAX POPUP
			if($getMasStatusRows['cc1_ref_phy_id']!=0 && $getMasStatusRows['cc1_ref_phy_id']!='')
			{
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id']; 
			}
			//=================ENDS HERE===================================================================
			   
			if($consultEmailStatus=="1")
			{
				if($emailId)
				{
					$refPhyAndEmail=  $patientConsultLetterTo."@@".$emailId;			
				}
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id'];
				$cc2_ref_phy_id	= $getMasStatusRows['cc2_ref_phy_id'];
				$cc3_ref_phy_id	= $getMasStatusRows['cc3_ref_phy_id'];	
			}
	
			
			/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
				//REPLACEING UNCHANGES SMART TAGS WITH NULL
			/*	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
				if($arr_smartTags){
					foreach($arr_smartTags as $key=>$val){
					  $regpattern=">$val<"; 
					  $consultTemplateData = str_ireplace($regpattern, '><', $consultTemplateData);
					}	
				}
			*/	//REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING.
				//$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U';
				$regpattern='|<a class="cls_smart_tags_link" href=(.*) id=(.*)>(.*)</a>|U';  
				$consultTemplateData = preg_replace($regpattern, "\\3", $consultTemplateData);
				$consultTemplateData = str_ireplace('<od br=""', '&lt;od', $consultTemplateData);
				$consultTemplateData = str_ireplace("<od br=''", "&lt;od", $consultTemplateData);
				$consultTemplateData = str_ireplace('</od>', '', $consultTemplateData);
				$consultTemplateData = str_ireplace('vision="">', 'vision', $consultTemplateData);
			/*--SMART TAG REPLACEMENT END--*/
		}
	}
	?>

<html>
<head>
<title>Template</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript">
<?php /*if(constant('AV_MODULE')=='YES' && isset($_GET['media_id']) && $_GET['media_id']>0){?>
if(window.top.document.getElementById('media_player_icon')){
	window.top.document.getElementById('media_player_icon').innerHTML = '<img src="../common/record_av/images/icon_play_24.png" class="ml10" style="vertical-align:middle;cursor:pointer;" title="Play MultiMedia Messages"  border="0" onClick="window.top.showMultiMediaMessage(\'consult_letter\',\'<?php echo $_GET['media_id'];?>\');" />';
}
<?php }else{*/?>
if(window.top.document.getElementById('media_player_icon'))
{
	window.top.document.getElementById('media_player_icon').innerHTML = '';
}
<?php //} ?>	

<?php
if(is_updox('fax') || is_interfax())
{
?>

if(top.fmain.document.getElementById('sendFaxBtn'))
{
	var tempId = '<?php echo $tempId;?>';
	if(parent.document.getElementById("pat_temp_id"))
	{
		parent.document.getElementById("pat_temp_id").value = tempId;
	}
	top.btn_show("<?php echo $btnVal; ?>");
	//top.document.getElementById('sendFaxBtn').style.display="inline-block";
	/*
	var selObj=top.document.getElementById('selectReferringPhy');
	var optionVal="";
	var	optionValsplit="";
	var getRefPhyId="";
	var i;
	if(selObj){
		for (i=0;i<selObj.options.length; i++){
			if(selObj.options[i].value!=""){
				optionVal=selObj.options[i].value;
				optionValsplit=optionVal.split("@@");	
				getRefPhyId=optionValsplit[0];	
				if(getRefPhyId=="<?php echo $refPhyId ?>"){
					selObj.value=optionVal;
					break;
				}else{
					selObj.value="-";
				}
			}
		}
	}	
	top.document.getElementById('send_fax_number').value="<?php echo $refPhyFaxNo; ?>"
	*/

	// Adding fmain because function is not working without it. And adding new parameter "top.fmain" which required in the function body.
	top.fmain.loadFaxRefPhy('<?php echo  $refPhyAndFax; ?>','<?php echo get_reffphysician_detail($cc1_ref_phy_id); ?>','<?php echo get_reffphysician_detail($cc2_ref_phy_id); ?>','<?php echo get_reffphysician_detail($cc3_ref_phy_id); ?>','<?php echo $preffered_reff_fax; ?>',top.fmain);
}	
<?php 
}
?>
</script>
</head>
<body class="scrol_Vblue_color Test_VF">
<form name="patientConsultLetter" method="post" action="template.php">
    <input type="hidden" name="templateId" value="<?php echo $consultTemplate; ?>">
    <input type="hidden" name="patient_consult_id" value="">
    <input type="hidden" name="tempId" value="<?php echo $tempId; ?>">
    <input type="hidden" name="elem_saveClose" value="0">
</form>
</body>
</html>
<?php
if(!$tempId)
{
	?>
	<script type="text/javascript">
		document.getElementById('patientTempName').value = '<?php echo $templateName; ?>';
	</script>
	<?php
}
if($tempId)
{
	// Adding fmain because without fmain creating an error for missing pat_temp_id field.
	echo "<script>top.fmain.document.getElementById('pat_temp_id').value='".$tempId."';</script>"; 
}

$Host = $_SERVER['HTTP_HOST'];
if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }

//BACKBOTTOM SETTING DONE DUE TO NSE AND LIESC LENGTHY FOOTER
$backbottom=15;
if(PRACTICE_PATH=='michiganeye'){ $backbottom=27; } 

$consultTemplateDataPage = '';if(($topMargin==0 || $topMargin=="") && (strstr($consultTemplateData,"<page_header>"))){$topMargin=5;}

$consultTemplateDataPage ='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="'.$backbottom.'">'.$consultTemplateData.'</page>';
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);

if($GLOBALS['webroot']!='')
{
	$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/gn_images/','../../data/'.	PRACTICE_PATH.'/gn_images/',$consultTemplateDataPage);
}

//------IMAGES REPLACEMENT WORK STARTS HERE------
$consultTemplateDataPage = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace('../../../library/images/','../../library/images/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/library/images/','../../library/images/',$consultTemplateDataPage);
//$consultTemplateDataPage = str_ireplace($GLOBALS['webroot']."/library/images/","../../library/images/",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace("interface/common/html2pdf/","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot']."/library/common/html_to_pdf/","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/interface/common/'.$htmlFolder.'/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/interface/common/html2pdf/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace('interface/common/html2pdf/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace('interface/common/new_html2pdf/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/','../../data/'.PRACTICE_PATH.'/PatientId_'.$patient_id.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($webServerRootDirectoryName.$GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/redactor/images/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($GLOBALS['webroot'].'/redactor/images/','',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace("../../interface/main/uploaddir/","../../data/".PRACTICE_PATH."/",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$http_host.'/'.$RootDirectoryName.'/data/','../../data/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$http_host.$GLOBALS['webroot'].'/data/','../../data/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/','../../data/',$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$myExternalIP.$GLOBALS['webroot'].'/data/','../../data/',$consultTemplateDataPage);

$consultTemplateDataPage = str_ireplace("Â","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace("&shy;","",$consultTemplateDataPage);
$consultTemplateDataPage = str_ireplace($protocol.$_SERVER['HTTP_HOST'].$protocol.$_SERVER['HTTP_HOST'],$protocol.$_SERVER['HTTP_HOST'],$consultTemplateDataPage);
$consultTemplateDataPage = rawurldecode($consultTemplateDataPage); //For decoding %## codes like %20 => ' '
$consultTemplateDataPage = preg_replace('/font-family.+?;/', "", $consultTemplateDataPage);

//------WRITE HTML FILE------
$file_path = write_html($consultTemplateDataPage);
?>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php
if($file_path) 
{
	//--START CODE TO SAVE PDF FOR SEND FAX 
	
	$getPCIP=$_SESSION["authId"];			
	$getPCIP=str_ireplace("::","_",$getPCIP);
	$getIP=str_ireplace(".","_",$getPCIP);
	$faxPdfName="savedFax_".$getIP;
	$savePdfFilePath = substr(data_path(), 0, -1);
	
	if(!file_exists($savePdfFilePath.'/consult_form_fax'))
	{
		mkdir($savePdfFilePath.'/consult_form_fax');
	}
	
	$html_file_name_fax='consult_form_fax/'.$faxPdfName;
	
	$savePdfFileName = $html_file_name_fax.'.pdf';
	
	if(file_exists($savePdfFilePath.'/'.$savePdfFileName)) 
	{
		unlink($savePdfFilePath.'/'.$savePdfFileName);	
	}
	/*
	$op = 'p';
	$html2pdf = new HTML2PDF($op,'A4','en');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->WriteHTML($consultTemplateDataPage, isset($_GET['vuehtml']));
	$html2pdf->Output($savePdfFilePath.'/'.$savePdfFileName,'F');
	*/
	try
	{
        $op = 'P';
        $html2pdf = new Html2Pdf($op,'A4','en');
        $html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($consultTemplateDataPage, isset($_GET['vuehtml']));
		$newFileName=$html2pdf->output($savePdfFilePath.'/'.$savePdfFileName,'F');
	} catch (Html2PdfException $e) {
		$html2pdf->clean();
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}
	
	//END CODE T SAVE PDF FOR FAX
?>
	<script>
	top.JS_WEB_ROOT_PATH="<?php echo $GLOBALS['webroot']; ?>";
	html_to_pdf('<?php echo $file_path; ?>','p','',true);
	</script>
<?php		
}
?>