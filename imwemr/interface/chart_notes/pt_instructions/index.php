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
/*
include_once("../../main/main_functions.php");
include_once("../../main/Functions.php");
include("../common/ChartApXml.php");
include_once("../common/scan_function.php");
require_once("../../inc_form_id.php");
$xml_arr = new ChartApXml();*/
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
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title> Patient Instruction :: imwemr ::</title>
  <!-- Bootstrap -->
  <link href="<?php echo $library_path;?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path;?>/css/common.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path;?>/css/document.css" rel="stylesheet" type="text/css">
			
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
  <!-- Bootstrap -->
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
 	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
  <!--<script type="text/javascript" src="../js/jsFunction.js"></script>
	<script type="text/javascript" src="../js/jsscript.js"></script>
	<script type="text/javascript" src="../js/javascript2.js"></script>	
	<script type="text/javascript" src="../js/simpletreemenu.js"></script>
	<script type="text/javascript" src="../js/autoSave.js"></script>-->
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
			top.frames["ifrm_FolderContent"].submit_frm();
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

<div class="panel panel-primary">
  <div class="panel-heading">Patient Instruction Documents</div>
  <div class="panel-body popup-panel-body" style="min-height:660px">
  	<div class="col-sm-3" style="max-height:100%;overflow:scroll;">
    	<?php
				//Given Document Code
				$materailAvail='';
				$docIdGivenToArr = array();
				$p_doc = 1;
				$tree->addToArray($p_doc,"Given to Pt",0);
				$qrydoc = "SELECT distinct DATE_FORMAT(A.date_time, '".get_sql_date_format('','Y','-')."') date_time 
 							from document_patient_rel  A 
 							where A.p_id ='".$patient_id."' AND A.status = '0' AND A.doc_id!='0' ORDER BY A.date_time desc" ;
				$patientSignConsentFormCreatedDatedoc = get_array_records_query($qrydoc);
				$h_doc=$p_doc+1;
				for($z=0;$z<count($patientSignConsentFormCreatedDatedoc);$z++){
					$r_doc = $h_doc;
					$formCreatedDateDoc=$patientSignConsentFormCreatedDatedoc[$z]['date_time'];
					$tree->addToArray($h_doc,$formCreatedDateDoc,$p_doc,"");	
					$h_doc++;
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
					$t_doc = $h_doc;
					for($x=0;$x<count($patientSigndocForm);$x++){
						$doc_id = $patientSigndocForm[$x]['doc_id'];
						$docIdGivenToArr[] = $patientSigndocForm[$x]['doc_id'];
						$id_doc = $patientSigndocForm[$x]['id'];
						$scan_id = $patientSigndocForm[$x]['scan_id'];
						$form_id_doc = $patientSigndocForm[$x]['form_id'];
						$dat_final_doc=$patientSigndocForm[$x]['date_time'];
						$date_time_doc=date("g:i A",strtotime($patientSigndocForm[$x]['date_time']));
						
						$docFormName_doc = $patientSigndocForm[$x]['name']."(".$date_time_doc."</b>)";
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
						$tree->addToArray($h_doc,$docFormName_doc,$r_doc,"doc_details.php?id=$id_doc&pdf=$pdf","ifrm_FolderContent","$path_img",$givenToImgOptional,$givenToImgOptionalUrl,$givenToImgOptionalTarget,$givenToImgOptionalAlt,"","","",$showInfo,$instDateTime,$operatorName,"");
						$h_doc++;
						
					}
				}
				$p_doc = $h_doc+1;
				//END Given Documents Code
				
				//START CODE for PENDING DOCUMENTS
				$docIdGivenToImplode=0;
				if($docIdGivenToArr) {
					$docIdGivenToImplode = implode("','",$docIdGivenToArr);	
				}
				
				if(empty($str_pt_edu_docs)==true) {
					$str_pt_edu_docs='0';
				}


				$sel_doc=imw_query("SELECT id, name FROM document WHERE `pt_edu` = 1 AND status='0' AND id IN(".$str_pt_edu_docs.") AND id NOT IN('".$docIdGivenToImplode."') ORDER BY name ASC");
				$sel_doc2=imw_query("SELECT id,name FROM document WHERE `pt_test` = 1 AND  status='0' AND id IN (".$str_pt_tes_docs.") AND id NOT IN('".$docIdGivenToImplode."') ORDER BY name ASC");
				if(imw_num_rows($sel_doc)>0 || imw_num_rows($sel_doc2)>0){
					$tree->addToArray($p_doc,"<span style='background-color:#FFE400; font-weight:bold; padding:1px 4px 1px 0px;'>Pending</span>",0);
				}else{
					$tree->addToArray($p_doc,"Pending",0);
				}

				$h_doc=$p_doc+1;
				while($row=imw_fetch_array($sel_doc)){					
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
					$tree->addToArray($h_doc,$doc_name,$p_doc,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$eduImgOptional,$eduImgOptionalUrl,$eduImgOptionalTarget,$eduImgOptionalAlt);
				}
				
				// ADD Pending Instructions Documents				
				$h_doc=$p_doc+1;
				while($row=imw_fetch_array($sel_doc2)){
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
					
					$tree->addToArray($h_doc,$doc_name,$p_doc,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$testImgOptional,$testImgOptionalUrl,$testImgOptionalTarget,$testImgOptionalAlt);
				}
				
				$p_doc = $h_doc+1;
				//END PENDING DOCUMENTS CODE
			
				
				/*--COLLECTING DATA TO MAKE FOLDER FOR ALL DOCUMENTS--*/
				$tree->addToArray($p_doc,"All Documents",0); //NEW FOLDER ADDED TO TREE
				$sel_all_doc=imw_query("SELECT id,name FROM `document` WHERE (`pt_edu` = 1 OR `pt_test` = 1) AND status='0' ORDER BY name ASC");
				
				$h_doc=$p_doc+1;
				while($row=imw_fetch_array($sel_all_doc)){
					$materailAvail='yes';
					$r_doc = $h_doc;
					$doc_id = $row['id'];
					$doc_name = $row['name'];
					$path_img="glyphicon-open-file";//"../../main/images/dhtmlgoodies_sheet.gif";
					$h_doc++;
					
					$allImgOptional=$allImgOptionalUrl=$allImgOptionalTarget=$allImgOptionalAlt='';
					$chkallScnUpldNumRow = scnUploadPtEduExistFun($doc_id,'admin_documents','0');
					if($chkallScnUpldNumRow>0) {
						/*$allImgOptional = "../../../images/scanner_icon.png";
						$allImgOptional.='" onClick="window.open(\'../../Medical_history/view_scan_images.php?scanOrUpload=upload&upload_from=admin_documents&lab_id='.$doc_id.'&mode=view\',\'admin_documents\',\'width=670,height=550\');';
						$allImgOptionalUrl = 'javascript:void(0); ';
						$allImgOptionalAlt = 'View Scan/Upload Documents';*/
						$allImgOptional='';
					}
					
					$tree->addToArray($h_doc,$doc_name,$p_doc,"show_pt_instructions.php?pt_id=$doc_id&form_id=$form_id","ifrm_FolderContent","$path_img",$allImgOptional,$allImgOptionalUrl,$allImgOptionalTarget,$allImgOptionalAlt);
				}
			?>
			<div style="overflow-y:auto;overflow-x:hidden;" id="div_treeData">
			<?php
				$tree->writeCSS();
			?>
			<style type="text/css">
				#dhtmlgoodies_tree .tree_link{
					font-size:12px;
				}
			</style>
			<?php
				$tree->writeJavascript();
				$tree->drawTree();
			?>
			</div>	
			<!-- Navi List -->
    </div>
    
    <div class="col-sm-9">
 			<div id="divData" class="well pd0 " style="height:650px; margin-bottom:0px; background-color:#FFF;" >
				<iframe name="ifrm_FolderContent" id="ifrm_FolderContent" frameborder="0" style="width:100%; height:inherit;" src=""></iframe> <!--../folder_category.php-->
			</div>
   	</div>
    
  </div>
  <footer class="panel-footer">
  	<input name="button" type="button" class="btn btn-success invisible" id="btn_save" onClick="submit_form();" value="Done" />
    <button type="button" class="btn btn-danger" onClick="window.close();">Close</button>
  </footer>
</div>

<?php if(count($patientSignConsentFormCreatedDatedoc)>0 && $materailAvail!='yes') {?>
<script>if(opener) {top.get_pt_edu_alert();}</script>
<?php } ?>
</body>
</html>