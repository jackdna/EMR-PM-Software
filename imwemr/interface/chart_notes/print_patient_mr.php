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
/*
File: print_patient_mr.php
Purpose: This file provides print function for vision->MR section.
Access Type : Direct
*/

die('Print patient MR');
include_once(dirname(__FILE__)."/../globals.php");
include_once(dirname(__FILE__)."/../billing/billing_globals.php");

//-----  Get data from remote server -------------------

$zRemotePageName = "main/print_patient_mr";
require(dirname(__FILE__)."/../chart_notes/get_chart_from_remote_server.inc.php");

//-----  Get data from remote server -------------------

/*---SAMRT TAG CODE START---*/
include_once(dirname(__FILE__)."/../admin/documents/smart_tags/functions.smart_tags.php");
include_once(dirname(__FILE__)."/../chart_notes/common/ChartNote.php");
include_once(dirname(__FILE__)."/../chart_notes/common/Vision.php");
include_once(dirname(__FILE__)."/../chart_notes/common/functions.php");
require_once(dirname(__FILE__)."/Functions.php");

//
$_GLOBALS["MR_RX_PRINT_PREVIOUS"] = "NO";

global $ChartNoteImagesString;
$ChartNoteImagesString=array();
$OBJsmart_tags = new SmartTags;
$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
$billing_global_server_name_str=strtolower($billing_global_server_name);
global $billing_global_server_name_str;
$objManageData = new ManageData;

//print_r($arr_smartTags);
/*---SAMRT TAG CODE END---*/
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

function getMrGivenFromLastVisit(){
	$ret="";
	
	if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ return $ret; }
	
	$flg=0;
	$qryGetSpacialCharValue = "select  c1.status_elements, c1.id
						   from chart_vis_master c1
						   LEFT JOIN chart_master_table c2 ON c1.form_id=c2.id
						   where  c1.patient_id = '".$_SESSION['patient']."' 
						   ORDER BY  c2.date_of_service DESC, c2.id DESC
						   LIMIT 0, 1  ";
	/*
	AND (c1.vis_statusElements like  '%elem_mrNoneGiven1=1%' OR 
		c1.vis_statusElements like  '%elem_mrNoneGiven2=1%' OR 
		c1.vis_statusElements like  '%elem_mrNoneGiven3=1%')
	*/					   
	$row = sqlQuery($qryGetSpacialCharValue);		
	if($row!=false){
		$tmp = $row["status_elements"];
		if(preg_match("/elem_mrNoneGiven\d+=1/",$tmp)){				
			$flg=$row["id"];
		}
	}
	
	if(!empty($flg)){
		//Multiple MR
		$sql = "SELECT mr_none_given FROM chart_pc_mr
				WHERE patient_id = '".$_SESSION['patient']."' AND delete_by = '0' AND id_chart_vis_master='".$flg."' AND ex_type='MR'	";
		$rez = sqlStatement($sql);		
		for($i=1; $row!=sqlFetchArray($rez); $i++){	
			if(!empty($row["mr_none_given"])){  if(!empty($ret)){ $ret.=","; }	$ret .= $row["mr_none_given"];	}
		}
	}	
	
	return $ret;
}
function replace_tag_with_property($html, $tag, $element, $elemValue, $nVal) {
	return preg_replace('/<'.$tag.'[^>]+'.$element.'="'.preg_quote($elemValue, '/').'"[^>]*>/s', $nVal, $html);
}

////////on Submit Print The Data//
$html_file_name='pdffile_'.$_SESSION['patient'];
if($_POST["printOptionType"]!="" && $_POST["finalHtmlForPrinting"]!=""){
		
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
			$prescriptionTemplateContentData= $_POST["finalHtmlForPrinting"];
		}else{
			$prescriptionTemplateContentData= stripslashes($_POST["finalHtmlForPrinting"]);
		}
		
		$printOptionType=$_POST["printOptionType"];
		if(count($_REQUEST['textbox'])>0){
			$arr_text_post=$_REQUEST['textbox'];
				foreach($arr_text_post as $text_val){
					$prescriptionTemplateContentData = replace_tag_with_property($prescriptionTemplateContentData, "input", "value", $text_val, $text_val);
			}
		}
		$prescriptionTemplateContentData=str_ireplace('../../interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<input type="text" value="',"",$prescriptionTemplateContentData);// For Safari and IE9
		$prescriptionTemplateContentData=str_ireplace('<INPUT value="',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT value=',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT',"",$prescriptionTemplateContentData);
//		<A id=2 class=cls_smart_tags_link href="javascript:;" jQuery1308551487984="1">
		$regpattern='|<a id=(.*) class=(.*) href=(.*)>(.*)<\/a>|U';
		$regpattern2='|<A id=(.*) class=(.*) href=(.*)>(.*)<\/A>|U';
		$prescriptionTemplateContentData = preg_replace($regpattern, "\\4", $prescriptionTemplateContentData);
		$prescriptionTemplateContentData = preg_replace($regpattern2, "\\4", $prescriptionTemplateContentData);		

		$prescriptionTemplateContentData=str_ireplace('" maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		$prescriptionTemplateContentData=str_ireplace('" maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		
		$prescriptionTemplateContentData=str_ireplace('maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=1 size=1 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);


		
		$prescriptionTemplateContentData=str_ireplace('" size="30" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="60" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="1" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		
		$prescriptionTemplateContentData=str_ireplace('maxLength="30" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="1" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="60" value="',"",$prescriptionTemplateContentData);// IE9

		$prescriptionTemplateContentData=str_ireplace('maxlength="1" value=\'',"",$prescriptionTemplateContentData);// IE11
		$prescriptionTemplateContentData=str_ireplace('maxlength="30" value=\'',"",$prescriptionTemplateContentData);// IE11
		$prescriptionTemplateContentData=str_ireplace('maxlength="60" value=\'',"",$prescriptionTemplateContentData);// IE11
		
		$prescriptionTemplateContentData=str_ireplace('\' tempendtextbox="">',"",$prescriptionTemplateContentData);// IE11

		// For Safari
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1" tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" size="60" maxlength="60" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		
		$prescriptionTemplateContentData=str_ireplace('" size="30" maxlength="30" tempendtextbox="">',"",$prescriptionTemplateContentData);

		// For Safari
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
		
	
		$tmp_include_root = (constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]["incdir"] : $GLOBALS["include_root"];
		//if(constant("REMOTE_SYNC") != 1){
		$imgALLReplace=$GLOBALS["include_root"].'/common/new_html2pdf/';
		$prescriptionTemplateContentData= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$prescriptionTemplateContentData);
		//}
		
		$signatureReplace=$GLOBALS["include_root"].'/common/new_html2pdf/tmp/';
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$prescriptionTemplateContentData);		
		
		$imgPicReplace=$tmp_include_root.'/common/new_html2pdf/pic_vision_pc.jpg';
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/pic_vision_pc.jpg',$imgPicReplace,$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace("../../interface/main/uploaddir","../../main/uploaddir",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace($GLOBALS['webroot']
."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$prescriptionTemplateContentData);

		$prescriptionTemplateContentData=str_ireplace("&Acirc;","",$prescriptionTemplateContentData);

		$getFinalHTMLForGivenMR=$prescriptionTemplateContentData;
		$fp = fopen(dirname(__FILE__).'/../common/new_html2pdf/'.$html_file_name.'.html','w');
		if(strtoupper(substr(PHP_OS, 0, 3))=='LIN') {
	
			$writeData = fwrite($fp,utf8_decode($getFinalHTMLForGivenMR));	    
	
		}else{
	
			$writeData = fwrite($fp,$getFinalHTMLForGivenMR);
	
		}
		
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
			//
			//$printOptionStyle = ($printOptionType == 1) ? "p" : "l";
			//$zRemoteServerData["header"] = checkUrl4Remote($GLOBALS["rootdir"]."/common/new_html2pdf/createPdf.php?op=".$printOptionStyle."&htmlFileName=".$html_file_name."&patient_id=".$_SESSION['patient']."");
			
			if(!empty($_POST["ChartNoteImagesStringFinal"])){	
				$ChartNoteImagesString = explode(",",$_POST["ChartNoteImagesStringFinal"]);
			}else{
				$ChartNoteImagesString=array();
			}
			
			$tmp_arr=array();
			if(count($ChartNoteImagesString)>0){			
				foreach($ChartNoteImagesString as $key11 => $var11){
					if(!empty($var11) && file_exists($var11)){
						$tmp_url = remsyn_makefulltourl($var11);	
						
						//replace paths with url
						$prescriptionTemplateContentData = str_replace($var11, $tmp_url, $prescriptionTemplateContentData );
						$tmp_arr[]= $tmp_url;
					}			
				}			
			}		
			$ChartNoteImagesStringFinal = $tmp_arr;
			
			
			$zRemoteServerData["pdf_data"]["html_data"] = $prescriptionTemplateContentData;
			$zRemoteServerData["pdf_data"]["html_data_location"] = $GLOBALS["remote"]["incdir"].'/common/new_html2pdf/'.$html_file_name.'.html';
			$zRemoteServerData["pdf_data"]["images_pth"] = $ChartNoteImagesStringFinal;			
			
		}else{
		
			$getFinalHTMLForGivenMR=$prescriptionTemplateContentData;
			$fp = fopen(dirname(__FILE__).'/../common/new_html2pdf/'.$html_file_name.'.html','w');
			if(strtoupper(substr(PHP_OS, 0, 3))=='LIN') {
			
				$writeData = fwrite($fp,utf8_decode($getFinalHTMLForGivenMR));	    
			
			}else{
			
				$writeData = fwrite($fp,$getFinalHTMLForGivenMR);
			
			}
		
		}
		?> 
		<script type="text/javascript">
		window.focus();
		var parWidth = 595;
		var parHeight = 841;
		var printOptionStyle
		<?php 
		if($printOptionType == 0){
		?>
			printOptionStyle = 'l';
		<?php	
		}
		elseif($printOptionType == 1){
		?>
		printOptionStyle = 'p';
		<?php } ?>
		window.open('../common/new_html2pdf/createPdf.php?op='+printOptionStyle+'&htmlFileName=<?php echo $html_file_name; ?>&patient_id=<?php echo $_SESSION['patient']; ?>','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
		</script>
		<?php 
		//exit();
		$flgStopExec = 1;
		
			
}
///End On Submit Print The Data//

if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;

$printType = $_REQUEST['printType']; 
$givenMrValue =trim(str_ireplace('%20'," ",$_REQUEST['givenMr'])); 
$mrGivenOrNot=false;
$mrArray=array();
if(strpos($givenMrValue,",")){
	$mrArray=explode(",",$givenMrValue);
	
}
//
if(isset($_REQUEST['printone']) && !empty($_REQUEST['printone']) && ($_REQUEST['printone']>=1 && $_REQUEST['printone']<=3)){
	$mrArray=array();
	$tmp = "MR ".$_REQUEST['printone'];
	$mrArray[] = $tmp;
}

if(count($mrArray)>0){
		if(in_array("MR 1",$mrArray) || in_array("MR 2",$mrArray)|| in_array("MR 3",$mrArray)){
			$mrGivenOrNot=true;
		}
	}elseif($givenMrValue=="MR 1" || $givenMrValue=="MR 2" || $givenMrValue=="MR 3"){
	$mrGivenOrNot=true;
}
if($mrGivenOrNot==false){	
	//get MR given from previous visits
	$givenMrValue=getMrGivenFromLastVisit();
	if(!empty($givenMrValue)){$mrArray=explode(",",$givenMrValue);}else{ $mrArray=array(); }
	if(count($mrArray)>0){
		$mrGivenOrNot=true;
	}else{	
		echo "<script>window.focus();</script>";
		echo("<center>No MR Prescription is given.</center>");
		$flgStopExec = 1;
	}	
}

if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;

$patientId = $_SESSION['patient'];
	if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"]))	{
		$form_id = $_SESSION["form_id"];	
		$finalize_flag = 0;		
	}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){	
		#form id
		$form_id = $_SESSION["finalize_id"];		
		$finalize_flag = 1;						
	}
	#####
// IF PRINT THEN FORM ID
$print_form_id = $_REQUEST['print_form_id'];
	if($print_form_id){
		$form_id = $print_form_id;
}
if($_REQUEST['chartIdPRS']){ //This ID comes from PRS for printing previous dos.
	$form_id=$_REQUEST['chartIdPRS'];
}
//Object
$oVis = new Vision($patientId,$form_id);

//
$getInputForTextBoxes=false;
$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='".$printType."'";	
$rsGetTempData = mysql_query($qryGetTempData) or die($qryGetTempData.mysql_error());
$numRowGetTempData = mysql_num_rows($rsGetTempData);
if($numRowGetTempData<=0){
	
	echo "<script>alert('Please create your Glasses template to precede print.');</script>";
	//exit();
	$flgStopExec = 1;
	
}else if($numRowGetTempData>0){
	$resArrayTemplate=mysql_fetch_array($rsGetTempData);
	$printOptionType=$resArrayTemplate["printOption"];
	$prescriptionTemplateContentData = stripslashes($resArrayTemplate["prescriptionTemplateContentData"]);
	if(strpos($prescriptionTemplateContentData,'{TEXTBOX_XSMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_SMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_MEDIUM}')>0){
		$getInputForTextBoxes=true;
	}

	if($arr_smartTags){
		foreach($arr_smartTags as $key=>$val){
			$showHtmlPage = stripos($prescriptionTemplateContentData,"[".$val."]");
			if($showHtmlPage !== false){//smarttag found
				$getInputForTextBoxes = true;
				break;
			}
		}
		/*
		foreach($arr_smartTags as $key=>$val){
			$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
		}
		*/	
	}
}

if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;

//get MR values when Given was actually Given--
function chkMRGivenActual($patientId, $sql, $mr, $sel){
	if($mr == "MR 3"){
		$mr_ind="3";
		$stts_chk="elem_providerNameOther_3=1";	
	}else if($mr == "MR 2"){
		$mr_ind="2";
		$stts_chk="elem_providerNameOther=1";
	}else{//MR 1
		$mr_ind="1";
		$stts_chk="elem_providerName=1";	
	}
	
	//
	$flg_chk=0;
	$stts_chk2="elem_mrNoneGiven".$mr_ind."=1";
	$qryGetSpacialCharValue = $sql;
	$row = sqlQuery($qryGetSpacialCharValue);
	if($row!=false){
		//check given 
		if(strpos($row["vis_statusElements"], $stts_chk2)!==false && strpos($row["vis_statusElements"], $stts_chk)!==false){
			$flg_chk=1;
		}else{
			//get given values when given was actually given			
			$givendt="";
			if(!empty($row["vis_mr_pres_dt"]) && ($row["vis_mr_pres_dt"]!="0000-00-00")){ 
			
				$givendt=$row["vis_mr_pres_dt"]; 
				$qryGetSpacialCharValue = "
					SELECT 
					".$sel."		
					FROM chart_vis_master c4 
					LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
					LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
					LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
						
					WHERE c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$mr_ind."' 
					AND c1.mr_pres_date='".$givendt."'
					AND c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%'
					AND c4.status_elements like  '%".$stts_chk."%'
					AND c1.delete_by='0'  
					Order By c4.id;
				";
				
				$row = sqlQuery($qryGetSpacialCharValue);	   
				if($row!=false){	
					$flg_chk=1;
				}
			}			
		}
	}
	
	//
	if($flg_chk==0){		
		$qryGetSpacialCharValue = "
					SELECT 
					".$sel."		
					FROM chart_vis_master c4 
					LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
					LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
					LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'					 	
					WHERE c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$mr_ind."' 
					AND c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%' AND c4.status_elements like  '%".$stts_chk."%'
					AND c1.delete_by='0'  
					Order By mr_pres_date DESC, c4.id DESC;
				";					   
	}	
	
	return $qryGetSpacialCharValue;
}
//--

function get_mr_dos($patientId, $form_id){
	$qryGetDOS="select cmt.date_of_service as dos,cmt.id as form_id,cmt.patient_id as patient_id from chart_vis_master as cv 
		 LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
		 LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id
	 where cv.status_elements!='' and cv.patient_id='".$patientId."' and cv.form_id='".$form_id."' and cpm.ex_type='MR' AND cpm.delete_by='0' AND
	(cv.status_elements like  '%elem_visMrOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
	|| cv.status_elements like  '%elem_visMrOdS=1,%'
	|| cv.status_elements like  '%elem_visMrOdC=1,%'
	|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
	|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
	|| cv.status_elements like  '%elem_visMrOdP=1,%'
	|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
	|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
	|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
	|| cv.status_elements like  '%elem_providerName=1,%'
	
	|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
	|| cv.status_elements like  '%elem_providerNameOther=1,%'
	|| cv.status_elements like  '%elem_providerIdOther=1,%'
	
	|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
	)
	";
	//$qry1=mysql_query("select vis_statusElements,exam_date from chart_vision where vis_mr_none_given!='' and patient_id='".$patientId."' and form_id='".$form_id."'");
	$qry1=imw_query($qryGetDOS);
	$co=imw_num_rows($qry1);
	if(($co > 0)){
		$crow=imw_fetch_array($qry1);
		//$date_of_service = date("m-d-Y", strtotime($crow["dos"]));	
		$date_of_service = wv_formatDate($crow["dos"]);
		$form_id_cv=$crow["form_id"];
		$patient_id_cv=$crow["patient_id"];
		
	}else{
	
		$qryGetDOS="select cmt.date_of_service as dos,cmt.id as form_id,cmt.patient_id as patient_id from chart_vis_master as cv 
		 LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
		  LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id 
	 where cv.status_elements!='' and cv.patient_id='".$patientId."' and 
	(cv.status_elements like  '%elem_visMrOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
	|| cv.status_elements like  '%elem_visMrOdS=1,%'
	|| cv.status_elements like  '%elem_visMrOdC=1,%'
	|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
	|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
	|| cv.status_elements like  '%elem_visMrOdP=1,%'
	|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
	|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
	|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
	|| cv.status_elements like  '%elem_providerName=1,%'
	
	|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
	|| cv.status_elements like  '%elem_providerNameOther=1,%'
	|| cv.status_elements like  '%elem_providerIdOther=1,%'
	
	|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
	|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
	) 
	ORDER BY  cmt.date_of_service DESC, cmt.id DESC limit 1
	";	
		/*
		$qryGetDOS="select form_id,patient_id from chart_vision 
			 where vis_statusElements!='' and patient_id='".$patientId."'
			  and 
			(vis_statusElements like  '%elem_visMrOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOdAdd=1,%'
			|| vis_statusElements like  '%elem_visMrOdS=1,%'
			|| vis_statusElements like  '%elem_visMrOdC=1,%'
			|| vis_statusElements like  '%elem_visMrOdTxt1=1,%'
			|| vis_statusElements like  '%elem_visMrOdTxt2=1,%'
			|| vis_statusElements like  '%elem_visMrOdP=1,%'
			|| vis_statusElements like  '%elem_visMrOdSel1=1,%'
			|| vis_statusElements like  '%elem_visMrOdSlash=1,%'
			|| vis_statusElements like  '%elem_visMrOdPrism=1,%'
			|| vis_statusElements like  '%elem_providerName=1,%'
			
			|| vis_statusElements like  '%elem_visMrOtherOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdAdd=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt2=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt1=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdS=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdC=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdP=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdSel1=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdSlash=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdPrism=1,%'
			|| vis_statusElements like  '%elem_providerNameOther=1,%'
			|| vis_statusElements like  '%elem_providerIdOther=1,%'
			
			|| vis_statusElements like  '%elem_visMrOtherOdS_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdC_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdAdd_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt1_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdA_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdP_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdPrism_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsAdd_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsS_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsPrism_3=1,%'
			) order by form_id DESC limit 1
			";
		*/	
		$qryGetPrevious=imw_query($qryGetDOS);
		$resGetPrivious=imw_num_rows($qryGetPrevious);
		if($resGetPrivious>0){
			$rowExamDate=imw_fetch_array($qryGetPrevious);
			$form_id_cv	   = $rowExamDate["form_id"];
			$patient_id_cv = $rowExamDate["patient_id"];
			
		}
		
	}	
	return array($date_of_service,$form_id_cv,$patient_id_cv);
}

function getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue,$prescriptionTemplateContentData){
global $gdFilename, $oVis, $zOnParentServer,$ChartNoteImagesString,$billing_global_server_name_str,$objManageData;
/////get patient data////
$qryGetpatientDetail = "select *,date_format(DOB,'".getSqlDateFormat()."') as pat_dob,date_format(date,'".getSqlDateFormat()."') as reg_date,
						DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(patient_data.dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(patient_data.dob, '00-%m-%d')) AS ptAge
						from patient_data where id = '$patientId'";
$rsGetpatientDetail	= mysql_query($qryGetpatientDetail)	or die($qryGetpatientDetail.mysql_error());
$numRowGetpatientDetail	= mysql_num_rows($rsGetpatientDetail);
if($numRowGetpatientDetail){
	extract(mysql_fetch_array($rsGetpatientDetail));
	$patientname = $fname.' '.$lname; 
	//$patientAddressFull = $street.' '.$street2.','.$city.','.$state.','.$postal_code;
	if($street){
		$patientAddressFull = $street;
	}
	//$patientAddressFull = $street.' '.$street2.','.$city.','.$state.','.$postal_code;
	if($street2){
		//$patientAddressFull .= ' '.$street2;
		$patientAddressFull .= ' '.$street2.',';
	}
	if($city){		
		if(!$street2){
			$patientAddressFull .= ',';
		}
		$patientAddressFull .= ' '.$city.', '.$state.' '.$postal_code;
		
		$patientGeoData = $city.', '.$state.' '.$postal_code;
	}
	$ptAgeShow = "";
	if($ptAge != ""){
		$ptAgeShow = $ptAge."&nbsp;Yr.";
	}
}

// IF PRINT THEN FORM ID
//echo("select * from  chart_left_cc_history where patient_id='$patientId' and form_id='$form_id'"."<BR> CHECK FORMID IS COMING<br>");
   //$qry1=mysql_query("select * from  chart_master_table where patient_id='$patientId' and id='$form_id'");
//   echo "select cmt.date_of_service from chart_master_table where patient_id='$patientId' and id='$form_id'";
	list($date_of_service, $form_id_cv,$patient_id_cv) = get_mr_dos($patientId, $form_id);	
	
	if($_REQUEST['chartIdPRS']){ //This ID comes from PRS for printing previous dos.
		$form_id_cv=$_REQUEST['chartIdPRS'];
	}

	if($form_id_cv && $patient_id_cv){
		$qryGetDos="select date_of_service from chart_master_table where patient_id='".$patient_id_cv."' and id='".$form_id_cv."'";
		$resGetDos=mysql_query($qryGetDos);
		if(mysql_num_rows($resGetDos)>0){
			$rowGetDos=mysql_fetch_assoc($resGetDos);
			$date_of_service = getDateFormat($rowGetDos["date_of_service"]);
		}
	}
	//die($date_of_service);
	
/////End date of sevice Code////////////////
//get today date//
	$today = getDateFormat(date('Y-m-d'));
//end today date//
//echo("Which MR GIVEN :<input type='text' value='$givenMrValue'/><br>");
if(!empty($givenMrValue) && preg_match("/MR \d+/",$givenMrValue)){
	$ex_number = str_replace("MR","",$givenMrValue);
	$ex_number = trim($ex_number);
	$sel = "
		c1.provider_id, c1.ex_desc as notes, c1.mr_pres_date as vis_mr_pres_dt, c1.form_id as  vis_form_id,
		c2.sph as OdSpherical, c2.cyl as odCylinder, c2.axs as odAxis, c2.ad as odAdd, c2.prsm_p as odPrism1, c2.prism as odBase2, c2.slash as odBase1, c2.sel_1 as odPrism2,				
		c3.sph as osSpherical, c3.cyl as osCylinder, c3.axs as osAxis, c3.ad as osAdd, c3.prsm_p as osPrism1, c3.prism as osBase2, c3.slash as osBase1, c3.sel_1 as osPrism2,  
		c4.status_elements as vis_statusElements
	";
	
	$qryGetSpacialCharValue = "
		SELECT 
		".$sel."		
		FROM chart_vis_master c4
		LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
		LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
		LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'		 	
		WHERE c4.form_id='".$form_id."' AND c4.patient_id = '".$patientId."' AND c1.ex_type='MR' AND c1.ex_number='".$ex_number."' AND c1.delete_by='0'  
		Order By ex_number;
	";
	
	$indx1=$indx2="";
	if($ex_number>1){
		$indx1="Other";
		if($ex_number>2){
			$indx2="_".$ex_number;
		}
	}
	$stts_chk="elem_providerName".$indx1.$indx2."=1";
	
	//get MR values when Given was actually Given--
	$qryGetSpacialCharValue = chkMRGivenActual($patientId,$qryGetSpacialCharValue, $givenMrValue, $sel);

}

//echo("QUERY:".$qryGetSpacialCharValue."<br>");

	if($qryGetSpacialCharValue!=""){
			$rsGetSpacialCharValue = mysql_query($qryGetSpacialCharValue)	or die($qryGetSpacialCharValue.mysql_error());
			$numRowGetSpacialCharValue = mysql_num_rows($rsGetSpacialCharValue);		
			
			$flgLF=0;
			//ifNo Record
			if($numRowGetSpacialCharValue<=0){			
				//$date_of_service = $oVis->getDos();			
				//get values of last finalized if any				
				$dt = $oVis->formatDate($date_of_service,0,0,"insert");
				$res = $oVis->getLastRecord($sel,"0",$dt);
				if($res!=false){$rsGetSpacialCharValue=$res->fields;}else{$rsGetSpacialCharValue=false;}
				$numRowGetSpacialCharValue = $res->RecordCount();
				$flgLF=1;
			}
			
				if($numRowGetSpacialCharValue){
					
					if($flgLF==0){
						$rowTmp = mysql_fetch_array($rsGetSpacialCharValue);
					}else{
						$rowTmp = $rsGetSpacialCharValue;
					}
					
					extract($rowTmp);
					
					//if(strpos($vis_statusElements, $stts_chk)===false){
						/*
						//Empty Record
						$OdSpherical="";
						$odCylinder="";
						$odAxis="";
						$odPrism1="";
						$odPrism2="";
						$odBase1="";
						$odBase2="";
						$odAdd="";

						$osSpherical="";
						$osCylinder="";
						$osAxis="";
						$osPrism1="";
						$osPrism2="";
						$osBase1="";
						$osBase2="";
						$osAdd="";
						$notes="";
						*/
						
						//3/28/2012 :: Printing both MR's when only wanted new... please see attached image.
						//return ""; //Comment to print all  given MR
					//}else{
					
						$odPrism ="";
						$osBase ="";
						if($odPrism1){
							$odPrism = $odPrism1;
						}
						if($odPrism2 && $odPrism1){
							//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
							$prismimage="../common/new_html2pdf/pic_vision_pc.jpg";
							$odPrism .= "<img src='".$prismimage."'/>". $odPrism2;
						}
						
						if($odBase1){
							$odBase = $odBase1;
						}
						if($odBase2 && $odBase1){
							$odBase .= ' '. $odBase2;
						}
					///////////////////////////
						if($osPrism1){
							$osPrism = $osPrism1;
						}
						if($osPrism2 && $osPrism1){
							//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
							$prismimage="../common/new_html2pdf/pic_vision_pc.jpg";
							$osPrism .= "<img src='".$prismimage."'/>". $osPrism2;
						}
						
						if($osBase1){
							$osBase = $osBase1;
						}
						if($osBase2 && $osBase1){
							$osBase .= ' '. $osBase2;
						}
					///////////////////////////
						if($odAxis){
							$odAxis .= "&deg;";
						}
						
						if($osAxis){
							$osAxis .= "&deg;";
						}
						
						if($notes){
							$notes= htmlspecialchars($notes);
						}
					//}
					
					//DOS should be prescription date						
					if(!empty($vis_mr_pres_dt)&&$vis_mr_pres_dt!="0000-00-00"){
						$vis_mr_pres_dt_show =  $oVis->formatDate($vis_mr_pres_dt,0,0,"show") ; 					
					}else if(!empty($vis_form_id)){
						$oChartNote = new ChartNote($patientId,$vis_form_id);
						$vis_mr_pres_dt_show = $oChartNote->getDos();						
					}else{
						$vis_mr_pres_dt_show =  $date_of_service ;
					}
					
					
					//set form to show previous doctor--
					if(!empty($vis_form_id)){
						$form_id_cv=$vis_form_id;					
					}
					//set form to show previous doctor--					
					
					
		}
	}
	

//$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='".$printType."'";	
//$rsGetTempData = mysql_query($qryGetTempData)	or die($qryGetTempData.mysql_error());
//$numRowGetTempData = mysql_num_rows($rsGetTempData);
//extract(mysql_fetch_array($rsGetTempData));	
//$printOptionType = $printOption;

if($prescriptionTemplateContentData!=""){
	$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
	//$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('%20',' ',$prescriptionTemplateContentData);
	/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$OBJsmart_tags = new SmartTags;
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	if($arr_smartTags){
		foreach($arr_smartTags as $key=>$val){
			$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<a id="'.$key.'" class="cls_smart_tags_link" href="javascript:;" oncontextmenu="return false">'.$val.'</a>',$prescriptionTemplateContentData);	
		}	
	}
	/*--SMART TAG REPLACEMENT END--*/
	
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT GEOGRAPHICAL DATA}',$patientGeoData,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$OdSpherical,$prescriptionTemplateContentData);
	
	if($odCylinder!=""){
		$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$odCylinder,$prescriptionTemplateContentData);
	}else{	
		
		$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
	}
	
	$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',$odAxis,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD PRISM}',$odPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD HORIZONTAL PRISM}',$odPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$odAdd,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$osSpherical,$prescriptionTemplateContentData);
	
	if($osCylinder!=""){
	
		$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
	}else{	
		
		$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
	}
	//$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',$osAxis,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS PRISM}',$osPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS HORIZONTAL PRISM}',$osPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$osAdd,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
	
	
	/*
	$prescriptionTemplateContentData = str_ireplace('{PATIENT DOB}',$pat_dob,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PT AGE}',$ptAgeShow,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD BASE}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS BASE}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TODAY DATE}',$today,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
	*/

	//Modified Variables
	$prescriptionTemplateContentData = str_ireplace('{DOB}',$pat_dob,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD VERTICAL PRISM}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS VERTICAL PRISM}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DOS}',$vis_mr_pres_dt_show,$prescriptionTemplateContentData);
	
	//New variable added	
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{STATE ZIP CODE}',$state.' '.$postal_code,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);	
	
	$raceShow						 = trim($race);
	$otherRace						 = trim($otherRace);
	if($otherRace) { 
		$raceShow					 = $otherRace;
	}
	$languageShow					 = str_ireplace("Other -- ","",$language);
	$ethnicityShow					 = trim($ethnicity);			
	$otherEthnicity					 = trim($otherEthnicity);
	if($otherEthnicity) { 
		$ethnicityShow				 = $otherEthnicity;
	}
	$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
	
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','<input type="text"  value="" size="1"  maxlength="1"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','<input type="text" name="textbox[]" value="" size="30"  maxlength="30"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','<input type="text" name="textbox[]" value="" size="60"  maxlength="60"  tempEndTextBox>',$prescriptionTemplateContentData);
	//echo date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
	if($billing_global_server_name_str=='swagelwootton' || $billing_global_server_name_str=='greenwich' || $billing_global_server_name_str=='eyeclinicsmichigan' || $billing_global_server_name_str=='lodenvision'){
		list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
		$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt+12,$dos_dy,$dos_yr));
	}else if($billing_global_server_name_str=='miramar'){
		list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
		$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt+24,$dos_dy,$dos_yr));
	}else{
		$expirationDate = getDateFormat(date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y'))),'mm-dd-yyyy');
	}
	$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);	
	$apptFacInfo = $objManageData->__getApptInfo($_SESSION['patient'],'','','');
	$apptFacname = $apptFacInfo[2];
	if(!empty($apptFacInfo[10])){
		$apptFacstreet = $apptFacInfo[10].', ';	
	}
	if(!empty($apptFacInfo[11])){
		$apptFaccity = $apptFacInfo[11].', ';	
	}
	$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);	
	
////////////////////////Statrt Signature Logic/////////	
$signaTure=false;
$phy_licence='';
//echo $qryGetSig = "SELECT doctorId,sign_coords,id,sign_path FROM chart_assessment_plans WHERE form_id ='".$form_id."' and patient_id ='".$patientId."'";	die();
//echo("QUERY1 To GET Signature:".$qryGetSig."<br>");
	//$rsGetSig = mysql_query($qryGetSig)	or die($qryGetSig.mysql_error());
	//$numRowGetSig = mysql_num_rows($rsGetSig);
	//if($numRowGetSig){
	//	extract(mysql_fetch_array($rsGetSig));	
	//	if($doctorId>0){
		//print Of Physcian Title First name Second name and Suffix//
			//if($doctorId){}else{}
			/*$id = $id;
			$tblName = "chart_assessment_plans";
			$pixelFieldName = "sign_coords";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";*/
			//if($gdFilename!=""){
			//$gdFilenamePath=realpath(dirname(__FILE__)."/../common/new_html2pdf/tmp/".$gdFilename);
			//if($sign_path){}else{}
	//	}
		
	//}
//Give Prioity To Master Chart Notes Provider//

//====get physician who save the MR1,MR2,MR3 value===// 
	if($form_id_cv && $patient_id_cv){
		$form_id=$form_id_cv;
		$patientId=$patient_id_cv;
	}
//====================================================//
	$qryGetProvider = "SELECT id,providerId,finalize,finalizerId FROM chart_master_table WHERE  id ='".$form_id."' and patient_id ='".$patientId."'";
	$rsGetProviderId = mysql_query($qryGetProvider)	or die($qryGetProvider.mysql_error());
	//echo("QUERY2 To GET Signature:".$qryGetProvider."<br>");
	
	$numRowProviderGetSig = mysql_num_rows($rsGetProviderId);
	if($numRowProviderGetSig && $signaTure==false){
		extract(mysql_fetch_array($rsGetProviderId));
		if($providerId>0){
		//print Of Physcian Title First name Second name and Suffix//
			if($providerId){
				if($finalize=='1'){
					$providerId = $finalizerId;
				}
				$getNameQry = mysql_query("SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,fname,mname,lname,pro_suffix,licence,user_npi,sign_path FROM users WHERE id = '".$providerId."'");	
				$getNameRow = mysql_fetch_assoc($getNameQry);
				$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
				$phy_fname = $getNameRow['fname'];
				$phy_mname = $getNameRow['mname'];
				$phy_lname = $getNameRow['lname'];
				$phy_suffix = $getNameRow['pro_suffix'];
				$phy_licence = $getNameRow['licence'];
				$phy_npi =$getNameRow['user_npi'];
				$sign_path=$getNameRow['sign_path'];
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',$phy_npi,$prescriptionTemplateContentData);
		}else{
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
			}
			if($sign_path && file_exists("uploaddir".$sign_path)){
				$gdFilenamePath="../../interface/main/uploaddir".$sign_path;
			}else{
				$id = $providerId;
				$tblName = "users";
				$pixelFieldName = "sign";
				$idFieldName = "id";
				$imgPath = "";
				$saveImg = "3";
				include(dirname(__FILE__)."/imgGd.php");
			//	if($gdFilename!=""){
			
				//$gdFilenamePath=realpath(dirname(__FILE__)."/../common/new_html2pdf/tmp/".$gdFilename);
				$gdFilenamePath="../common/new_html2pdf/tmp/".$gdFilename;
			}
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
				//$gdFilenamePath=checkUrl4Remote($GLOBALS['rootdir']."/common/new_html2pdf/tmp/".$gdFilename);
				
				$gdFilenamePath=realpath(dirname(__FILE__)."/".$gdFilenamePath);
				$ChartNoteImagesString[]=$gdFilenamePath;
			}
			
			if($gdFilenamePath){
				$TData = "<img align='left' src='".$gdFilenamePath."'>";
				$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);	
				$signaTure=true;
			}
			//}
		}	
	}
	
	$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$phy_licence,$prescriptionTemplateContentData);
//End Give Prioity To Master Chart Notes Provider
	if($signaTure==false){
		$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"",$prescriptionTemplateContentData);		
		$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
	}
/////////////////////////End Signature Logic/////////
}// End Template HTml Blank Check
	
return $prescriptionTemplateContentData	;
} //End Function
if($_GLOBALS["MR_RX_PRINT_PREVIOUS"]=="NO"){
$arr_vis_statusElements=array();
$qryGetDOS_check="select cv.status_elements, cv.id from chart_vis_master as cv 
					 LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
				 where cv.status_elements!='' and cv.patient_id='".$patientId."' and cv.form_id='".$form_id."'";
$resGetDOS_check=mysql_query($qryGetDOS_check) or die(mysql_error());
if(mysql_num_rows($resGetDOS_check)>0){
	$rowGetDOS_check=mysql_fetch_assoc($resGetDOS_check);
	$vis_statusElements=trim($rowGetDOS_check['status_elements']);
	if($vis_statusElements){
		$arr_vis_statusElements=explode(",",$vis_statusElements);
	}
}
if(!(in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements)) && (!in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements)) && (!in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))){
	die("<center>No MR Prescription is given</center>");	
}
}//
if(count($mrArray)>0){
	//if(in_array("MR 1",$mrArray) && (in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements))){
	if(in_array("MR 1",$mrArray)){
		$flg_tmp = 1;
		if($_GLOBALS["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements))) ? 1 : 0; }
		if($flg_tmp == 1){
		$tmp = getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 1",$prescriptionTemplateContentData);
		if(!empty($tmp)){
		$getFinalHTMLForGivenMR="<page>".$tmp."</page>";
		}
		}
	}
	//if(in_array("MR 2",$mrArray) && (in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements))){
	if(in_array("MR 2",$mrArray)){
		$flg_tmp = 1;
		if($_GLOBALS["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements))) ? 1 : 0; }
		if($flg_tmp == 1){
		$tmp = getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 2",$prescriptionTemplateContentData);
		if(!empty($tmp)){
		$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";	
		}
		}
	}
	//if(in_array("MR 3",$mrArray) && (in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))){
	if(in_array("MR 3",$mrArray)){
		$flg_tmp = 1;
		if($_GLOBALS["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))) ? 1 : 0; }
		if($flg_tmp == 1){
		$tmp = getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue="MR 3",$prescriptionTemplateContentData);
		if(!empty($tmp)){
		$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";
		}
		}
	}
}else if($givenMrValue=="MR 1" || $givenMrValue=="MR 2" || $givenMrValue=="MR 3"){
	$tmp = getHTMLForGivenMR($patientId,$form_id,$printType,$givenMrValue,$prescriptionTemplateContentData);
	if(!empty($tmp)){
	$getFinalHTMLForGivenMR="<page>".$tmp."</page>";
	}
}

if($getInputForTextBoxes==false){
		
		$tmp_include_root = (constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]["incdir"]  : $GLOBALS["include_root"];	

		$imgALLReplace=$tmp_include_root.'/common/new_html2pdf/';
		if(constant("REMOTE_SYNC") != 1){
			$getFinalHTMLForGivenMR= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$getFinalHTMLForGivenMR);
		}		
		
		$imgPicReplace=$tmp_include_root.'/common/new_html2pdf/pic_vision_pc.jpg';
		$getFinalHTMLForGivenMR=str_ireplace('../common/new_html2pdf/pic_vision_pc.jpg',$imgPicReplace,$getFinalHTMLForGivenMR);
		
		$signatureReplace=$tmp_include_root.'/common/new_html2pdf/tmp/';
		$getFinalHTMLForGivenMR=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$getFinalHTMLForGivenMR);
		$getFinalHTMLForGivenMR=str_ireplace('../../interface/common/new_html2pdf/','',$getFinalHTMLForGivenMR);
		$getFinalHTMLForGivenMR=str_ireplace("../../interface/main/uploaddir","../../main/uploaddir",$getFinalHTMLForGivenMR);
		$getFinalHTMLForGivenMR=str_ireplace($web_root."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$getFinalHTMLForGivenMR);
		
}else{
	$getFinalHTMLForGivenMR=$getFinalHTMLForGivenMR;
}
//../../interface/common/new_html2pdf/


//remote
if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
$getFinalHTMLForGivenMR_Org = $getFinalHTMLForGivenMR;
$ChartNoteImagesString_Org = $ChartNoteImagesString;
$tmp_arr=array();
if(count($ChartNoteImagesString)>0){			
	foreach($ChartNoteImagesString as $key11 => $var11){
		if(!empty($var11) && file_exists($var11)){
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
$zRemoteServerData["pdf_data"]["html_data_location"] = $GLOBALS["remote"]["incdir"].'/common/new_html2pdf/'.$html_file_name.'.html';
$zRemoteServerData["pdf_data"]["images_pth"] = $ChartNoteImagesStringFinal;


}
//remote

$fp = fopen(dirname(__FILE__).'/../common/new_html2pdf/'.$html_file_name.".html",'w');
if(strtoupper(substr(PHP_OS, 0, 3))=='LIN') {
	
	$writeData = fwrite($fp,utf8_decode($getFinalHTMLForGivenMR));	    

}else{

	$writeData = fwrite($fp,$getFinalHTMLForGivenMR);

}
//echo $prescriptionTemplateContentData;die;
if($writeData && $getInputForTextBoxes==false){	
	
	?>
<script type="text/javascript">
	window.focus();
	var parWidth = 595;
	var parHeight = 841;
	var printOptionStyle
	<?php 
	if($printOptionType == 0){
	?>
		printOptionStyle = 'l';
	<?php	
	}
elseif($printOptionType == 1){
?>
printOptionStyle = 'p';
<?php	
}
?>
window.open('../common/new_html2pdf/createPdf.php?op='+printOptionStyle+'&htmlFileName=<?php echo $html_file_name; ?>&patient_id=<?php echo $_SESSION['patient']; ?>','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
</script><?php 

	

}else{


?>
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
function submitPrintRequest(){
		setTouchInputs();// Set Value Attribute
		
		<?php 
			if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		?>			
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer_ORG") ){
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer_ORG").innerHTML;
			return true;
		}else{
		return false;
		
		<?php }else{ ?>
		
		if(document.getElementById("finalHtmlForPrinting") && document.getElementById("FinalHtmlContainer") ){
			document.getElementById("finalHtmlForPrinting").value=document.getElementById("FinalHtmlContainer").innerHTML;
			return true;
		}else{
		return false;
		
		<?php } ?>
	}
}
// Function Set InnerHTML of Final Output In TextArea And Submit Form To Print//	
</script>
<link rel="stylesheet" type="text/css" href="<?php echo !empty($GLOBALS["remote"]['webroot']) ? $GLOBALS["remote"]['webroot'] : $GLOBALS['webroot'];?>/interface/themes/default/common.css">
<body class="body_c"  bgcolor="#ffffff" topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 marginwidth=0 marginheight=0 o1ncontextmenu="return false;">
	<form name="printMRForm" id="printMRForm" method="post" action="print_patient_mr.php">
	<input type="hidden" name="printOptionType" value="<?php echo($printOptionType);?>">
	<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
	<input type="hidden" name="ChartNoteImagesStringFinal" value="<?php echo implode(",",$ChartNoteImagesStringFinal);?>">
	
	<input type="hidden" name="ChartNoteImagesStringFinal" value="<?php echo implode(",",$ChartNoteImagesString_Org);?>">		
	<div id='FinalHtmlContainer_ORG' style="width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;display:none;"><?php echo $getFinalHTMLForGivenMR_Org;?></div>
	
	<table cellpadding="2"  cellspacing="2" width="100%" border="0">
		<tr>
			<td align="left" colspan='4'>
				<div id='FinalHtmlContainer' style="width:99%; height:<?php echo($_SESSION["wn_height"]-200);?>px; overflow:auto;">
					<?php echo($getFinalHTMLForGivenMR);?>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" colspan='4' height="15">
			<!-- To Store Final HTMl FOR Printing-->
				<textarea name="finalHtmlForPrinting" id="finalHtmlForPrinting" rows="20" cols="40" style="display:none;">
				
				</textarea>
			<!-- To Store Final HTMl FOR Printing-->
			</td>
		</tr>
		<tr height="15">
			<td width="15%" align="left"></td>
			<td width="12%" align="right"><input type="submit" class="dff_button" id="directPrint" name="directPrint" title="Print"  value="Print" onClick="javascript: return submitPrintRequest();"></td>
			<td width="26%" align="left" class="text_10b" ><input type="button" class="dff_button" id="butIdCancel" title="Cancel"   value="Cancel" onClick="window.close();"></td>
			<td width="37%" align="right"></td>

		</tr>
	</table>
	</form>
	<div class="div_popup white border" id="div_smart_tags_options" style="top:200px;left:400px; width:300px; z-index:999;">
		<div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
		<img src="../../images/ajax-loader.gif">
	</div>
	
	<script type="text/javascript">
	var smart_tag_current_object = new Object;
	$(document).ready(function(){
		$('.cls_smart_tags_link').mouseup(function(e){
			if(e.button==2){
				$('#smartTag_parentId').val($(this).attr('id'));
				smart_tag_current_object = $(this);
				display_tag_options(e);
			//	document.oncontextmenu="return false;"    		
			}
			
		});
	});
	
	function display_tag_options(e, obj){
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
	
	function replace_tag_with_options(){
		var strToReplace = '';
		var parentId = $('#smartTag_parentId').val();
		
		var arrSubTags = document.all.chkSmartTagOptions;
		$(arrSubTags).each(function (){
			if($(this).attr('checked')){
				if(strToReplace=='')
					strToReplace +=  $(this).val();
				else
					strToReplace +=  ', '+$(this).val();
			}
		});
		//alert(strToReplace);
		
		/*--GETTING FCK EDITOR TEXT--*/
		if(strToReplace!='' && smart_tag_current_object){
			$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
			//$(smart_tag_current_object).html(strToReplace);
	/*		
			RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
			var strippedData = $('#hold_temp_smarttag_data').html();
			strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
	*/		
			$('#div_smart_tags_options').hide();
		}else{
			alert('Select Options');
		}
	}
	window.moveTo(0,0);
	window.resizeTo(1200,screen.height);
	</script>
	</body>
</html>

<?php }

}//$flgStopExec = 1;
}//$flgStopExec = 1;
}//$flgStopExec = 1;
?>
