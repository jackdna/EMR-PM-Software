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

include("../../../config/globals.php");
include($GLOBALS['srcdir']."/classes/dhtmlgoodies_tree.class.php");
$tree = new dhtmlgoodies_tree();
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION["patient"];
$form_id = $_SESSION['form_id'];
$operator_id = $_SESSION['authId'];

//START CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
$ChkAnyPtDocExistsNumRow 	= ptDocExistFun($patient_id); //FUNCTION FROM common/scan_function.php
$ptDocImgSrcActive 			= $GLOBALS['webroot'].'/library/images/icons_progNts_active.png';
$ptDocImgSrcDeactive 		= $GLOBALS['webroot'].'/images/icons_progNts.png';
//END CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY

//get all conditions in array();
$ptEduConditionArr	= array();
$arr_pt_edu_docs  	= array();
$arr_pt_tes_docs  	= array();
$ptEduExist		  	= '';

$ptEduConditionArr 	= getPtEduCondition($patient_id,$form_id,$operator_id); //r4/library/sql.php
$arr_pt_edu_docs	= $ptEduConditionArr[0];
$arr_pt_tes_docs 	= $ptEduConditionArr[1];
$ptEduExist 	 	= $ptEduConditionArr[2];
//get all conditions in array();

$str_pt_edu_docs 	= implode(",",$arr_pt_edu_docs);
$str_pt_tes_docs 	= implode(",",$arr_pt_tes_docs); 

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Patient Instruction :: imwemr ::</title>
    <link href="<?php echo $library_path;?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path;?>/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $library_path;?>/css/document.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
	<script>
		window.focus();
		function resize_win()
		{
			var parWidth = (screen.availWidth > 1200) ? 1200 : screen.availWidth ;
			window.resizeTo(parWidth,820);
			var t = 40;
			var l = parseInt((screen.availWidth - window.outerWidth) / 2)
			window.moveTo(l,t);
		}
	
		var ddtreepath = "<?php echo $GLOBALS['rootdir']."/chart_notes/";?>";	
		$(document).ready(function(){
			oDiv = $('#div_treeData');
			tdH = oDiv.parent('td').height();
			oDiv.height(tdH);
			resize_win();
		
		});
		function show(id,pdf){
			//var src= "doc_details.php?id="+id;
			//document.getElementById('ifrm_FolderContent').src=src;
			if(pdf=='pdf'){
				document.getElementById('btn_print').style.visibility='hidden';
			}else{
				document.getElementById('btn_print').style.visibility='visible';
			}
		}
		function window_close(){
			window.close();
		}
		function submit_form(){
			top.fmain.frames["ifrm_FolderContent"].submit_frm();
		}
		//top.opener.update_toolbar_icon(); // to update icon bg color;
	</script>
</head>
<body>

<script language="javascript">
	
	var ptDocImgSrc=scnImgMedSrc=scan_img_val_med='';
	var anyDocExistsNumRow	='<?php echo $ChkAnyPtDocExistsNumRow;?>';
	if(anyDocExistsNumRow>0) {
		ptDocImgSrc 	= '<?php echo $ptDocImgSrcActive;?>';
	}else {
		ptDocImgSrc 	= '<?php echo $ptDocImgSrcDeactive;?>';
	}
	scan_img_val 	= '<a href="javascript:void(0);"><span class="icon_glow"><img src="'+ptDocImgSrc+'" vspace="0" border="0" align="middle" title="Pt Documents" onClick="opTests(\'ptInst\')" ></span></a>';
	
	if(top.opener) {
		if(typeof (top.opener.document.getElementById('13_ioc'))=="object") {//FOR WORK VIEW
			//top.opener.document.getElementById('13_ioc').innerHTML=scan_img_val;
		}
		if(typeof (top.opener.top.Title)=="object") {
			if(top.opener.top.Title.document.getElementById('13_ioc')) {//FOR MAIN TITILE
				top.opener.top.Title.document.getElementById('13_ioc').innerHTML=scan_img_val;
			}
		}
	}
	
</script>
<?php
$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 290)) ;
?>
<div class="col-xs-12 bg-white">
	<div class="row">
    	<div class=" col-xs-2 " style="height:<?php echo $col_height;?>px; max-height:100%; overflow:scroll">
            <?php
				$showInfo = "";
				include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
				if(!$p) { $p=1;}
				if($_REQUEST["doc_name"]=="view_pt_instruction_docs") {				
					$p++;
					$tree->addToArray($p,"Pt. Instruction Docs",0,"","",$initInstructionClass);
					
					//Given Document Code
					$materailAvail='';
					$docIdGivenToArr = array();
					$a=$p;
					$p++;
					
					$qrydoc = "SELECT distinct DATE_FORMAT(A.date_time, '".get_sql_date_format('','Y','-')."') date_time 
 							from document_patient_rel  A 
 							where A.p_id ='".$patient_id."' AND A.status = '0' AND A.doc_id!='0' ORDER BY A.date_time desc" ;
					$patientSignConsentFormCreatedDatedoc = get_array_records_query($qrydoc);
					$ptInstructionDocsClass = (count($patientSignConsentFormCreatedDatedoc) > 0 ) ? 'icon-folder-filled' : 'icon-folder';
					$tree->addToArray($p,"Given to Pt",$a,"","",$ptInstructionDocsClass);
					$b=$p;
					
					for($z=0;$z<count($patientSignConsentFormCreatedDatedoc);$z++){
						$p++;
						$formCreatedDateDoc=$patientSignConsentFormCreatedDatedoc[$z]['date_time'];
						$tree->addToArray($p,$formCreatedDateDoc,$b,"","","icon-folder-filled");
						$qrynew1 = "SELECT dp.id,dp.name,dp.date_time,dp.form_id,dp.operator_id,dp.scan_id,dp.doc_id, 
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM document_patient_rel dp 
									LEFT JOIN users u ON(u.id = dp.operator_id)
									WHERE dp.p_id='$patient_id' 
									AND dp.doc_id!='0' 
									AND DATE_FORMAT(dp.date_time, '".get_sql_date_format('','Y','-')."')='$formCreatedDateDoc' 
									AND dp.status = 0 
									ORDER BY dp.date_time DESC" ;
						$patientSigndocForm = get_array_records_query($qrynew1);
						$c=$p;
						for($x=0;$x<count($patientSigndocForm);$x++){
							$p++;
							$doc_id = $patientSigndocForm[$x]['doc_id'];
							$docIdGivenToArr[] = $patientSigndocForm[$x]['doc_id'];
							$id_doc = $patientSigndocForm[$x]['id'];
							$scan_id = $patientSigndocForm[$x]['scan_id'];
							$form_id_doc = $patientSigndocForm[$x]['form_id'];
							$dat_final_doc=$patientSigndocForm[$x]['date_time'];
							$date_time_doc=date("g:i A",strtotime($patientSigndocForm[$x]['date_time']));
							
							
							$docFormName_doc = $patientSigndocForm[$x]['name'];
							$docFormName_doc = trim(ucwords($docFormName_doc));
							$path_img='pdf-icon';
							$sel_scan1=imw_query("select file_type from ".constant("IMEDIC_SCAN_DB").".scans where  scan_id='$scan_id'");
							if(imw_num_rows($sel_scan1)>0) {
								$row_scan1=imw_fetch_array($sel_scan1);
								$file_type=$row_scan1['file_type'];
								
								if($file_type=="application/pdf"){
									$pdf='pdf';
									$path_img='pdf-icon';//$GLOBALS['webroot']."/library/images/pdf_small.png";
								}else{
									$path_img='glyphicon-open-file ';//$GLOBALS['webroot']."/library/images/sc5.png";
								}
							}
							$givenToImgOptional=$givenToImgOptionalUrl=$givenToImgOptionalTarget=$givenToImgOptionalAlt='';
							$chkGivenToScnUpldNumRow = scnUploadGivenToExistFun($doc_id,'admin_documents',$_SESSION['patient']);
							if($chkGivenToScnUpldNumRow>0) {
								$givenToImgOptional = '';
							}
							$showInfo 	  = "yes";
							$instDateTime = date("m-d-Y g:i A",strtotime($patientSigndocForm[$x]['date_time']));
							$operatorName = stripslashes($patientSigndocForm[$x]['operator_name']);
							$tree->addToArray($p,$docFormName_doc,$c,"doc_details.php?id=$id_doc&pdf=$pdf","ifrm_FolderContent","$path_img",$givenToImgOptional,$givenToImgOptionalUrl,$givenToImgOptionalTarget,$givenToImgOptionalAlt,"","","",$showInfo,$instDateTime,$operatorName,"","","",true);
						}
					}
					//END Given Documents Code
				}
				
				if($_REQUEST["doc_name"]=="pt_instruction_template") {			
					/*
					$p++;
					if(!$subTemplateCnt) {
						$subTemplateCnt = $p;
					}	
					$p++;
					$tree->addToArray($p,"Pt. Instruction Templates",$subTemplateCnt,"","","icon-folder","","","","","","","","","","","","","active");
					$a=$p;
					
					//START CODE for PENDING DOCUMENTS
					$docIdGivenToImplode=0;
					if($docIdGivenToArr) {
						$docIdGivenToImplode = implode("','",$docIdGivenToArr);	
					}
					
					if(empty($str_pt_edu_docs)==true) {
						$str_pt_edu_docs='0';
					}
		
		
					$p++;
					$sel_doc=imw_query("SELECT id, name FROM document WHERE `pt_edu` = 1 AND status='0' AND id IN(".$str_pt_edu_docs.") AND id NOT IN('".$docIdGivenToImplode."') ORDER BY name ASC");
					$sel_doc2=imw_query("SELECT id,name FROM document WHERE `pt_test` = 1 AND  status='0' AND id IN (".$str_pt_tes_docs.") AND id NOT IN('".$docIdGivenToImplode."') ORDER BY name ASC");
					if(imw_num_rows($sel_doc)>0 || imw_num_rows($sel_doc2)>0){
						$tree->addToArray($p,"<span style='background-color:#FFE400; font-weight:bold; padding:1px 4px 1px 0px;'>Pending</span>",$a);
					}else{
						$tree->addToArray($p,"Pending",$a);
					}
		
					$b=$p;
					while($row=imw_fetch_array($sel_doc)){					
						$p++;
						$materailAvail='yes';
						$doc_id = $row['id'];
						$doc_name = $row['name'];
						$path_img="glyphicon-open-file";//"../../main/images/dhtmlgoodies_sheet.gif";
						//$h_doc++;
						
						$eduImgOptional=$eduImgOptionalUrl=$eduImgOptionalTarget=$eduImgOptionalAlt='';
						$chkEduScnUpldNumRow = scnUploadPtEduExistFun($doc_id,'admin_documents','0');
						if($chkEduScnUpldNumRow>0) {
							$eduImgOptional='';
						}
						$tree->addToArray($p,$doc_name,$b,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$eduImgOptional,$eduImgOptionalUrl,$eduImgOptionalTarget,$eduImgOptionalAlt);
					}
					
					// ADD Pending Instructions Documents				
					while($row=imw_fetch_array($sel_doc2)){
						$p++;
						$materailAvail='yes';
						$r_doc = $h_doc;
						$doc_id = $row['id'];
						$doc_name = $row['name'];
						$path_img="glyphicon-open-file";//"../../main/images/dhtmlgoodies_sheet.gif";
					//	$h_doc++;
						
						$testImgOptional=$testImgOptionalUrl=$testImgOptionalTarget=$testImgOptionalAlt='';
						$chkTestScnUpldNumRow = scnUploadPtEduExistFun($doc_id,'admin_documents','0');
						if($chkTestScnUpldNumRow>0) {
							$testImgOptional='';
						}
						
						$tree->addToArray($p,$doc_name,$b,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$testImgOptional,$testImgOptionalUrl,$testImgOptionalTarget,$testImgOptionalAlt);
					}
					
					//END PENDING DOCUMENTS CODE
					
					
					//--COLLECTING DATA TO MAKE FOLDER FOR ALL DOCUMENTS--
					$p++;
					$tree->addToArray($p,"All Documents",$a); //NEW FOLDER ADDED TO TREE
					$sel_all_doc=imw_query("SELECT id,name FROM `document` WHERE (`pt_edu` = 1 OR `pt_test` = 1) AND status='0' ORDER BY name ASC");
					
					$b=$p;
					while($row=imw_fetch_array($sel_all_doc)){
						$p++;
						$materailAvail='yes';
						$r_doc = $h_doc;
						$doc_id = $row['id'];
						$doc_name = $row['name'];
						$path_img="glyphicon-open-file";//"../../main/images/dhtmlgoodies_sheet.gif";
						$allImgOptional=$allImgOptionalUrl=$allImgOptionalTarget=$allImgOptionalAlt='';
						$chkallScnUpldNumRow = scnUploadPtEduExistFun($doc_id,'admin_documents','0');
						if($chkallScnUpldNumRow>0) {
							$allImgOptional='';
						}
						
						$tree->addToArray($p,$doc_name,$b,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$allImgOptional,$allImgOptionalUrl,$allImgOptionalTarget,$allImgOptionalAlt);
					}*/
					
				}
				
				include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
				$p++;
				$tree->writeCSS();
				$tree->writeJavascript();
				$tree->drawTree();				
			?>
	    </div>
         <div class="col-xs-10 ">
            <div class="row">
                <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
                <iframe name="ifrm_FolderContent" id="ifrm_FolderContent" <?php echo $consentScroll;?>  style="width:100%; height:<?php echo $col_height;?>px;" src="treeInstructionDetails.php" frameborder="0"></iframe>
                </div>   
            </div>
        </div>
	</div>
</div>        

<?php if(count($patientSignConsentFormCreatedDatedoc)>0 && $materailAvail!='yes') {?>
<script>//if(opener) {top.get_pt_edu_alert();}</script>
<?php } ?>
    <script>
			$(function(){
						$('[data-toggle="tooltip"]').tooltip({container:'body'});
			});	
			
			top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
		</script>	

</body>
</html>