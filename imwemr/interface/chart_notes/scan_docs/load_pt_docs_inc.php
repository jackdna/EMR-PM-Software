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

//----------LOAD PT DOCS TEMPLATES--------------------------
//-----------FILES INCLUSION--------------------------------
//include_once("../../../config/globals.php");
function create_html_file_4pdf( $z_patient_id, $mode="", $temp_id="", $type="", $des_pth="" ){
	global $zflg_incfile_once;

	$ret = array();
	$uid = $_SESSION['authId'];

	if(!isset($zflg_incfile_once)){
		$zflg_incfile_once=1;
		include_once($GLOBALS['fileroot']."/interface/chart_notes/chart_globals.php");
		include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
		$library_path = $GLOBALS['webroot'].'/library';
		include_once($GLOBALS['fileroot']."/library/classes/functions.smart_tags.php");
		include_once($GLOBALS['fileroot']."/library/classes/work_view/PnTempParser.php");
		include_once($GLOBALS['fileroot']."/library/classes/work_view/wv_functions.php");
		include_once($GLOBALS['fileroot']."/library/classes/work_view/Printer.php");
		include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartNote.php");
		include_once($GLOBALS['fileroot']."/library/classes/work_view/ChartDraw.php");
		include_once($GLOBALS['fileroot']."/library/classes/Functions.php");
		include_once($GLOBALS['fileroot']."/library/classes/class.tests.php");
	}

	//-----------OBJECTS-----------------------------------------
	$OBJsmart_tags 	= new SmartTags;
	$objManageData 	= new ManageData;
	$objParser 		= new PnTempParser;

	$patient_id = $z_patient_id;

	//-----------CALL FROM DOCS TAB => PT-DOCS------------------

	$ptDocsPrint = '';


	//-----------MED HX FUNCTION CALL----------------------------
	//$medHx=$objParser->getMedHx_public($_SESSION['patient']);

	//-----------MED HX -> OCULAR OTHER FIELD DATA---------------
	//$ocularother=$objParser->getMedHx_public($_SESSION['patient'],"","Ocular_Other");

	//-----------GET FORM ID FROM CHART MASTER TABLE ------------
	if(!$form_id)
	{
		$formIdQry ="SELECT
						id
					FROM
						`chart_master_table`
					WHERE
						patient_id='".$patient_id."'
					ORDER BY
						date_of_service
					DESC
						LIMIT 0,1";
		$formIdRes = get_array_records_query($formIdQry);
		$form_id   = $formIdRes[0]['id'];
	}

	//-----------DIFFERENT PRINTING MODE------------------------

	if( $mode == "ins" )
	{
		$pth = "";
		$initPtDocInsQry="SELECT ins.id,ins.pid,ins.scan_card,ins.scan_label,ins.type, ins.ins_caseid, inct.case_name FROM insurance_data ins
	  								JOIN insurance_case inc ON (ins.ins_caseid= inc.ins_caseid and inc.del_status = 0  )
	                  JOIN insurance_case_types inct ON (inct.case_id= inc.ins_case_type )
	  								WHERE ins.pid=".$patient_id." AND ins.id='".$temp_id."'
	  								AND (ins.scan_card <> '' OR ins.scan_card2 <> '')
	  								ORDER BY inc.ins_caseid Desc";

		$initPtDocInsRes=imw_query($initPtDocInsQry);
		if($initPtDocInsRes && imw_num_rows($initPtDocInsRes)>0){
    	$row = imw_fetch_assoc($initPtDocInsRes);
			if(!empty($row["scan_card"])&&!empty($row["pid"])){
				$oSaveFile = new SaveFile($row["pid"]);
				$pth = $oSaveFile->getFilePath($row["scan_card"],"i");
				$pntr = $row["scan_card"];
			}
  	}
	}
	else if( $mode == "intrprttns" )
	{
		list($id, $fid) = explode("_", $temp_id);
		$pid = $patient_id;
		$exam_name = "FundusExam";
		$oChartDraw = new ChartDraw($pid, $fid, $exam_name);
		$file_path = $oChartDraw->print_report_interp($id, "1");
		$fileName = basename($file_path);
	}
	else if( $mode == "PtTstMngr" )
	{
		$pth = "";
		$q = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans WHERE scan_id='".$temp_id."' ";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$doc_file_path	= $rs['file_path'];			
			if(!empty($doc_file_path)){
				$oSaveFile = new SaveFile();
				$pth	= $oSaveFile->getFilePath($doc_file_path,'i');
				$pntr = $doc_file_path;
			}
		}
	}
	//-----------SECTION PRINT MODE CALL FROM DOCS->PT-DOCS/COLLECTIONS SAVED TEMPLATES
	//-----------SECTION FACESHEET MODE CALL FROM SCHEDULER -> FACESHEET
	else if(empty($temp_id) === false && ($mode == 'print'))
	{
		//-------GET SAVED TEMPLATE DATA----------------------
		$apptId="";
		$patientId = $patient_id;

		global $phpServerIP;
		global $phpHTTPProtocol;

		//-------APPT ID FROM SCHEDULER FACEHSHEET PRINT POP-UP THROUGH URL
		//if($_REQUEST['apptId']){ $apptId = $_REQUEST['apptId'];}

		if($mode=="print")
		{
			if($type=="PtInstructionsDocs")
			{
				$qry = "select * from document_patient_rel where id='$temp_id'";

				$qry_doc = imw_query($qry);
				$doc_fet = imw_fetch_array($qry_doc);
				$content = stripslashes($doc_fet['description']);

				$doc_scn_upload_from = $doc_fet['doc_scn_upload_from'];
				$scan_doc_file_path = $doc_fet['scan_doc_file_path'];
				$upload_doc_file_path = $doc_fet['upload_doc_file_path'];
				$upload_doc_type = $doc_fet['upload_doc_type'];
				$scan_doc_date = $doc_fet['scan_doc_date'];
				$upload_doc_date = $doc_fet['upload_doc_date'];

				$regpattern='|<a class=\"cls_smart_tags_link\" href=(.*) id=(.*)>(.*)<\/a>|U';
				$content = preg_replace($regpattern, "\\3", $content);
				$tmp = core_get_patient_name($patient_id);
				$patient_name = core_name_format($tmp[2],$tmp[1],$tmp[3]);

				$strHTML  = '
					<page backtop="10mm" backbottom="10mm">
						<page_footer>
							<table style="width: 100%;">
								<tr>
									<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
								</tr>
							</table>
						</page_footer>

						<page_header>
							<table style="background-color:#ccc; width:100%;" cellpadding="4" cellspacing="0">
								<tr>
									<td width="500" align="left">'.$patient_name.'</td>
									<td width="100" align="right" style="font-weight:bold;">Given: </td>
									<td width="150">'.get_date_format(date('Y-m-d'))." ".date('h:i A').'</td>
								</tr>
							</table>
						</page_header>
						<table cellpadding="4" cellspacing="0" width="100%" height="100%">';
						if(!$doc_scn_upload_from || $doc_scn_upload_from=='writeDoc') {
								$strHTML .=	"<tr><td class='text_10' valign='top'>".$content."</td></tr></table></page>";
								$templateData = $strHTML;
						}else{
							$ret =array();
							if(($doc_scn_upload_from=='scanDoc' && !empty($scan_doc_file_path)) ||
						 			($doc_scn_upload_from=='uploadDoc' && !empty($upload_doc_file_path))){
									$pntr="";
									if(!empty($scan_doc_file_path)){
										$pntr=$scan_doc_file_path;
									}else	if(!empty($upload_doc_file_path)){
										$pntr=$upload_doc_file_path;
									}

									if(!empty($pntr)){
										$oSaveFile = new SaveFile(0);
										$pth = $oSaveFile->getFilePath($pntr,"i");
										if(file_exists($pth)){
											if(!empty($des_pth) && $des_pth=="pt_msg_mails"){
												$oSaveFile = new SaveFile($uid, 1,"users");
												$pntr = $oSaveFile->copyfile(array("tmp_name"=>$pth, "name"=>basename($pth)),$des_pth,"","","1");
												$pth = $oSaveFile->getFilePath($pntr, "i");
											}
											//Get File path
											$ret = array($pth, $pntr);
										}
									}
							}
							return $ret;
						}

			}else if($type=="pt_orders")
			{
				$ordersQry = "select order_file_content from print_orders_data
							where print_orders_data_id = '".$temp_id."'";
				$ordersQryRes = get_array_records_query($ordersQry);
				$templateData = $ordersQryRes[0]['order_file_content'] ;
			}
			//---COLLECTION SAVED LETTER PRINTS--------------
			else if($type=="collection")
			{
				$templQry = "SELECT
								template_content
							FROM
								`pt_docs_collection_letters`
							WHERE
								id ='$temp_id'";
				$templQryRes = get_array_records_query($templQry);
				$templateData 	= $templQryRes[0]['template_content'];
				$templateData = preg_replace("/[{}]/", "" ,$templateData);
				preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$templateData);
			}
			else
			{
				$templQry = "SELECT
								template_content,
								pt_enable_footer
							FROM
								`pt_docs_patient_templates`
							WHERE
								pt_docs_patient_templates_id = '$temp_id'";
				$templQryRes = get_array_records_query($templQry);

				//$pt_docs_template_name 	= $templQryRes[0]['pt_docs_template_name'];
				$pt_docs_template_content 	= $templQryRes[0]['template_content'];
				$pt_enable_footer 			= $templQryRes[0]['pt_enable_footer'];
			}
		}

		//----------GET PATIENT DETAILS---------------------------
		if($type!="collection" && $type!="pt_orders" && $type!="PtInstructionsDocs")
		{
			$patQry = "	SELECT
							patient_data.*,
							pos_facilityies_tbl.facilityPracCode,
							heard_about_us.heard_options ,
							heard_about_us_desc.heard_desc,
							employer_data.name emp_name,
							employer_data.street as emp_street,
							employer_data.street2 as emp_street2,
							employer_data.state as emp_state,
							employer_data.postal_code as emp_postal_code,
							employer_data.city as emp_city,
							users.lname as users_lname,
							users.fname as users_fname,
							users.mname as users_mname,
							date_format(patient_data.date, '".get_sql_date_format()."') as reg_date,
							date_format(patient_data.DOB, '".get_sql_date_format()."') as patient_dob
						FROM
							`patient_data`
							LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = default_facility
							LEFT JOIN heard_about_us ON patient_data.heard_abt_us = heard_about_us.heard_id
							LEFT JOIN heard_about_us_desc ON heard_about_us_desc.heard_id = heard_about_us.heard_id
							LEFT JOIN employer_data ON employer_data.pid = patient_data.id
							LEFT JOIN users ON users.id = patient_data.providerID
						WHERE
							patient_data.id = '$patientId'";
			$patQryRes = get_array_records_query($patQry);
			//-------------REPLACE VARIABLES USED INTO TEMPLATES------

			$templateData = str_ireplace("{PATIENT_NICK_NAME}",ucwords($patQryRes[0]['nick_name']),$pt_docs_template_content);
			$templateData = $objManageData->__loadTemplateData($templateData,$patQryRes[0], '0','','', $read_from_database=1,$apptId);

			$footerAdd='';
			$footerPageNum = '<tr><td style="text-align:center;width:100%" class="text_value">Page [[page_cu]]/[[page_nb]]</td></tr>';

			if($pt_enable_footer=="yes")
			{
				//--------FIXED CODE FOR CEC SERVER-------------------
				$footerAdd ='<tr>
								<td style="text-align:center;width:100%" class="text_value">
									7001 S Edgerton Rd, Suite B&nbsp;&nbsp;&nbsp; Brecksville, OH&nbsp; 44141&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 440-526-1974&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 800-875-0300
								</td>
							</tr>';
				$footerPageNum = '';
			}
			else if($pt_enable_footer=="disable_page_no")
			{
				$footerPageNum = '';
			}
			if($enable_footer=="disable_page_no")
			{
				$footerPageNum = '';
			}


		//--------ADD PAGE SETTING FOR PDF PRINTING ----
		$templateData = <<<DATA
			<page backtop="-3mm" backbottom="1mm"  backleft="-2mm"  backright="0mm">
			<page_footer>
			<table style="width: 100%;">
				$footerAdd
				$footerPageNum
			</table>
			</page_footer>$templateData</page>
DATA;
}

		//----------END------------------------------------------------

		//----------CREATE HTML FILE FOR PDF PRINTING------------------
		$tmp = trim($z_patient_id)."_".$uid."_".trim($temp_id)."_".trim($type)."_".trim($mode);
		$fileName = 'fl_'.$tmp."_".time().".html";

		//----------IMAGES REPLACEMENT WORK STARTS HERE----------------
		$templateData = str_ireplace($protocol.$phpServerIP,'',$templateData);
		$templateData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$templateData);
		$templateData = str_ireplace($GLOBALS['webroot'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$templateData);
		$templateData = str_ireplace('/'.$GLOBALS['php_server'].'/library/images/',$GLOBALS['php_server'].'/library/images/',$templateData);
		$templateData = str_ireplace($GLOBALS['webroot'].'/interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$templateData);
		$templateData = str_ireplace('../../interface/main/uploaddir/','../../data/'.PRACTICE_PATH.'/',$templateData);
		$templateData = str_ireplace($protocol.$phpServerIP.$GLOBALS['webroot']."/library/redactor/images/",$GLOBALS['webroot']."/redactor/images/",$templateData);
		$templateData = str_ireplace(rtrim($webServerRootDirectoryName,'/'),'',$templateData);
		$templateData = str_ireplace($webroot."/interface/reports/new_html2pdf/","",$templateData);
		$templateData = str_ireplace($webroot."/interface/common/new_html2pdf/","",$templateData);
		$templateData = rawurldecode($templateData); //FOR DECODING %## CODES LIKE %20 => ' '
		$templateData = str_ireplace("&nbsp;","&amp;nbsp;",$templateData);
		$templateData = str_ireplace("&amp;nbsp;"," ",$templateData);
		$templateData = str_ireplace($webroot."/redactor/images/","../../../redactor/images/",$templateData);
		$templateData = str_ireplace("&Acirc;","",$templateData);

		//----------REPLACE FONT-FAMILY INTO TEMPLATE DATA-----------
		$templateData = preg_replace('/font-family.+?;/', "", $templateData);

		if(empty($GLOBALS['webroot']))
		{
			$templateData = str_ireplace('/../../data/','../../data/',$templateData);
		}
		//----------DATA WRITE INTO HTML FILE HERE------------------

		$file_path = write_html(html_entity_decode($templateData),$fileName);
	}

	//copy file
	if($mode == "ins" || $mode == "PtTstMngr"){
		  if(empty($pth) || !file_exists($pth)){
				$pth = "";
			}
			if(!empty($pth)){						
				if(!empty($des_pth) && $des_pth=="pt_msg_mails"){
					$oSaveFile = new SaveFile($uid, 1,"users");
					$pntr = $oSaveFile->copyfile(array("tmp_name"=>$pth, "name"=>basename($pth)),$des_pth,"","","1");
					$pth = $oSaveFile->getFilePath($pntr, "i");
				}
				//Get File path
				$ret = array($pth, $pntr);
			}
	}
	//--

	//create pdf
	if(count($ret)<=0 && !empty($file_path) && !empty($fileName)){
		$des_file_pth = "";
		if(!empty($des_pth) && $des_pth=="pt_msg_mails"){
			$oSaveFile = new SaveFile($uid, 1,"users");
			$file_path_pntr = $oSaveFile->copyfile(array("tmp_name"=>$file_path, "name"=>$fileName),$des_pth);
			$file_path = $oSaveFile->getFilePath($file_path_pntr, "i");
			$des_file_pth = str_replace(".html",".pdf", $file_path);
		}

		$tmp = create_pdf($file_path, $des_file_pth );
		if(empty($tmp)){
			$file_path = $des_file_pth;
			if(!empty($des_pth) && $des_pth=="pt_msg_mails"){
				$file_path_pntr = $oSaveFile->getFilePath($file_path, "db2");
			}
			$ret = array($file_path, $file_path_pntr);
		}
	}

	return $ret;
}
?>