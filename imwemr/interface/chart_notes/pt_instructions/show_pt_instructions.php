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
include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
include_once($GLOBALS['srcdir']."/classes/work_view/ChartAP.php");
include_once($GLOBALS['srcdir']."/classes/work_view/PnTempParser.php");
include_once($GLOBALS['srcdir']."/classes/Functions.php");
include_once($GLOBALS['srcdir']."/classes/functions.smart_tags.php");
include_once($GLOBALS['srcdir']."/classes/work_view/MedHx.php");
include_once($GLOBALS['srcdir']."/classes/work_view/Fu.php");
//require_once($GLOBALS['srcdir']."/ckeditor/ckeditor.php");
//include_once("../../main/classObjectFunction.php");
//include_once("../../main/Functions.php");  //FILE INCLUDED TO REPLACE APPT. VOCABULARY
$upload_dir = "../../../data/".constant('PRACTICE_PATH');
$library_path = $GLOBALS['webroot'].'/library';

$objParser = new PnTempParser;
$objManageData=new ManageData; //OBJECT USED FOR CALLING __getApptInfo FUNCTION BELOW
$OBJsmart_tags = new SmartTags;


$_REQUEST['upload_doc_type']=xss_rem($_REQUEST['upload_doc_type'],1);
$_REQUEST['upload_doc_file_path']=xss_rem($_REQUEST['upload_doc_file_path'],1);
$_REQUEST['upload_doc_date']=xss_rem($_REQUEST['upload_doc_date'],1);
$_REQUEST['scan_doc_file_path']=xss_rem($_REQUEST['scan_doc_file_path'],1);
$_REQUEST['scan_doc_date']=xss_rem($_REQUEST['scan_doc_date'],1);
$_REQUEST['doc_from']=xss_rem($_REQUEST['doc_from'],1);
//print_r($_POST);
if(!empty($_REQUEST['pt_id'])){
   echo "<script type='text/javascript'> 
	if(top.opener) {
		parent.document.getElementById('btn_save').className = 'btn btn-success';
	}else {
		top.btn_show('PTINST');
	}
   </script>";
}
$pt_id = $_REQUEST['pt_id'];
$patient_id = $_SESSION['patient'];
$form_id = $_GET["form_id"];
if(empty($form_id)){ $form_id = $_SESSION['finalize_id']; }

$qry_doc = imw_query("select * from document where id='$pt_id'");
$doc_fet = imw_fetch_array($qry_doc);
if(imw_num_rows($qry_doc)>0){
	$name = addslashes($doc_fet['name']);
	$visit = $doc_fet['visit'];
	$tests = $doc_fet['tests'];
	$dx = $doc_fet['dx'];
	$cpt = $doc_fet['cpt'];
	$medications = $doc_fet['medications'];
	$content = $doc_fet['content'];
	$old_name = $doc_fet['name'];
	$scan_id = $doc_fet['scan_id'];
	$pt_edu = $doc_fet['pt_edu'];
	$pt_test = $doc_fet['pt_test'];
	
	$doc_from = $doc_fet['doc_from'];
	$scan_doc_file_path = $doc_fet['scan_doc_file_path'];
	$upload_doc_file_path = $doc_fet['upload_doc_file_path'];
	$upload_doc_type = $doc_fet['upload_doc_type'];
	$scan_doc_date = $doc_fet['scan_doc_date'];
	$upload_doc_date = $doc_fet['upload_doc_date'];
	
	$FCKeditor1 = addslashes($_POST['FCKeditor1']);
}
if($_POST['edit_mode']=='yes'){		
		$operator_id = $_SESSION['authId'];
		
		$ins_doc_pat=imw_query("insert into document_patient_rel set doc_id='$pt_id',name='$name',
							p_id='$patient_id',form_id='$form_id',date_time=now(),doc_from='admin',
							scan_id='$scan_id',operator_id='$operator_id',visit_rel='$visit',tests_rel='$tests',
							dx_rel='$dx',cpt_rel='$cpt',medications_rel='$medications',
							doc_scn_upload_from='$doc_from',scan_doc_file_path='$scan_doc_file_path',upload_doc_file_path='$upload_doc_file_path',
							upload_doc_type='$upload_doc_type',scan_doc_date='$scan_doc_date',upload_doc_date='$upload_doc_date',
							description='$FCKeditor1'");
		
		//START CODE TO SAVE SCAN/UPLOAD DOCS FOR PATIENT-DOCUMENTS
		$chkScnUpldQry = "SELECT upload_lab_rad_data_id,givenToEduMultiPtId FROM upload_lab_rad_data WHERE uplaod_primary_id='".$pt_id."' AND scan_from='admin_documents' AND upload_status='0'";
		$chkScnUpldRes = imw_query($chkScnUpldQry) or die($chkScnUpldQry.imw_error());
		if(imw_num_rows($chkScnUpldRes)>0) {
			while($chkScnUpldRow = imw_fetch_array($chkScnUpldRes)) {
				$upload_lab_rad_data_id =  $chkScnUpldRow['upload_lab_rad_data_id'];
				$givenToEduMultiPtId = trim($chkScnUpldRow['givenToEduMultiPtId']);
				$saveGivenToPtId = "'".$patient_id."'";
				if($givenToEduMultiPtId) {
					$saveGivenToPtId = $givenToEduMultiPtId.","."'".$patient_id."'";	
				}
				$updtScnUpldQry = 'UPDATE upload_lab_rad_data SET givenToEduMultiPtId="'.$saveGivenToPtId.'" WHERE upload_lab_rad_data_id="'.$upload_lab_rad_data_id.'"';
				$updtScnUpldRes = imw_query($updtScnUpldQry) or die($updtScnUpldQry.imw_error());
			}
		}
		
		//END CODE TO SAVE SCAN/UPLOAD DOCS FOR PATIENT-DOCUMENTS
}
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
  <script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
 	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
  <script type="text/javascript">
	function submit_frm(){
		document.getElementById('edit_mode').value='yes';
		document.frm.submit();
	}
	</script>
</head>
<body>
	<div style="vertical-align:top;position:relative;overflow:hidden; max-height: 99%;">
  	<form action="" name="frm" method="post" class="margin_0">
    	<input type="hidden" name="edit_mode" id="edit_mode" value="">
      <input type="hidden" value="<?php echo $edit_id;?>" name="eid">
      <input type="hidden" value="<?php echo $scan_doc_file_path;?>" name="scan_doc_file_path">
      <input type="hidden" value="<?php echo $upload_doc_file_path;?>" name="upload_doc_file_path">
      <input type="hidden" value="<?php echo $upload_doc_type;?>" name="upload_doc_type">
      <input type="hidden" value="<?php echo $doc_from;?>" name="doc_from">
      <input type="hidden" value="<?php echo $scan_doc_date;?>" name="scan_doc_date">
      <input type="hidden" value="<?php echo $upload_doc_date;?>" name="upload_doc_date">
      <input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
      
			<?php
				if(!$doc_from || $doc_from == 'writeDoc') {
					$contents = stripslashes($content); 
					/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
					$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
					if($arr_smartTags){
						foreach($arr_smartTags as $key=>$val){
							$contents = str_ireplace("[".$val."]",'<a id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$contents);	
						}	
					}
					/*--SMART TAG REPLACEMENT END--*/
					
					$contents = $objParser->getDataParsed($contents,$patient_id,$form_id,'','','','','','','education_instruction');
					//echo $contents; die;
					//=======================START CODE TO REPLACE APPT VOCABULARY================
					$apptInfoArr	= $objManageData->__getApptInfo($patient_id,'','','','',1);
					
					//===========FACILITY ADDRESS VARIABLE CONCATENATION==================
					if($apptInfoArr[10] && $apptInfoArr[11])
					{
						$facilityAddress .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
					}
					else if($apptInfoArr[10])
					{
						$facilityAddress .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
					}
					else if($apptInfoArr[11])
					{
						$facilityAddress .= $apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];
					}
					//============10 ==> STREET/ 11 ==> CITY/ 12 ==> STATE/ 13-14 ==> ZIP CODE - EXT/ 3 ==> PHONE===
					$contents = str_ireplace("{APPT DATE}",$apptInfoArr[0],$contents);
					$contents = str_ireplace("{APPT DATE_F}",$apptInfoArr[1],$contents);
					$contents = str_ireplace("{APPT PROVIDER}",$apptInfoArr[5],$contents);
					// NEW APPOINTMENT VARIABLES REPLACEMENT WORK
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr[0],$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr[8],$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr[5],$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr[4],$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr[16],$contents);
					$contents = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr[17],$contents);
					//================================CODE END===================================
					//---------Patient Discussion Variable Replacement-------
					$pt_discussion = "";	
					if(($patient_id) && ($form_id))
					{
						$qry_pt_dscs = "SELECT 
											commentsForPatient 
										FROM 
											`chart_assessment_plans` 
										WHERE 
											patient_id='".$patient_id."' 
										AND 
											form_id ='".$form_id."' ";
											
						$qry_pt_dscs_res = imw_query($qry_pt_dscs) or die($qry_pt_dscs.imw_error());
						
						if(imw_num_rows($qry_pt_dscs_res)>0)
						{
							$qry_pt_dscs_row = imw_fetch_array($qry_pt_dscs_res);
							
							$pt_discussion_row	= $qry_pt_dscs_row['commentsForPatient'];
							$pt_discussion_exp	= explode('~||~',$pt_discussion_row);
							$pt_discussion		= $pt_discussion_exp[0];
						}
					}	
					
					$contents = str_ireplace("{PT_DISCUSSION}",$pt_discussion,$contents);
					
					//=======================REPLACING IMG PATH FOR PDF PRINITING================
					$http_host=$_SERVER['HTTP_HOST'];
					if($protocol==''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';}
					$contents = str_ireplace('src="'.$protocol.$http_host,'src="',$contents);
					$contents = str_ireplace('src=\''.$protocol.$http_host,'src=\'',$contents);
					//$contents = str_ireplace($protocol.$http_host,'',$contents); 
					//$contents = str_ireplace("/$web_RootDirectoryName/data/".PRACTICE_PATH."/",'../../../data/'.PRACTICE_PATH.'/',$contents);
	
					//================================CODE END===================================
					
					//var_dump($contents); die();
					$contents = str_replace('cccccc','ffffff',$contents);
					str_replace('<div style="margin: 0in 0in 0pt; line-height: normal; text-align: center;"><u><strong><span style="font-size: larger"><span style="font-family: Arial"><font size="2">Document</font></span></span></strong></u></div>', '', $contents);
						
					echo '<textarea id="FCKeditor1" name="FCKeditor1">'.stripslashes($contents).'</textarea>';
			?>	
      		<script>
				CKEDITOR.replace( 'FCKeditor1', { width:'100%', height:'590px', toolbarStartupExpanded:false} );
			</script>	
			<?php		
				}
				else if($doc_from == 'scanDoc' && $scan_doc_file_path) {
			?>
        	<img name="viewImgScn" src="<?php echo $upload_dir.$scan_doc_file_path;?>" border="0" >
      <?php
				}
				else if($doc_from == 'uploadDoc' && $upload_doc_file_path) {
					if($upload_doc_type=='pdf') {
						echo "<iframe src=\"".$upload_dir.$upload_doc_file_path."\" width=\"100%\" height=\"100%\" scrolling=\"no\"></iframe>";
					}else {
			?>
          	<img name="viewImgScn"  src="<?php echo $upload_dir.$upload_doc_file_path;?>" border="0" >
   		<?php	
					}
				}
			?>
      </form>
	</div>
  
	<?php
		if($_POST['edit_mode']=='yes'){
			echo '<script>
					var objFrm = top.fmain;
					if(top.opener) {
						objFrm = top;	
					}
					objFrm.location.reload();
				  </script>';
		}
		if(!$doc_from || $doc_from == 'writeDoc') {
	?>
  		<!--<div class="div_popup white border" id="div_smart_tags_options" style="position:absolute; top:200px; left:400px; width:300px; z-index:999;">
      	<div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
        <img src="../../../library/images/ajax-loader.gif">
     	</div>-->
      
      <script type="text/javascript">
				var smart_tag_current_object = new Object;
				function display_tag_options(){
					var WRP = '<?php echo $GLOBALS['webroot']; ?>';
					$('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="../../../library/images/ajax-loader.gif">');
					$('#div_smart_tags_options').show();
					var parentId = $('#smartTag_parentId').val();
					$.ajax({
						type: "GET",
						//url: WRP+"/interface/admin/documents/smart_tags/ajax.php?do=getTagOptions&id="+parentId,
						url: WRP+"/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId,
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
					
					/*--GETTING FCK EDITOR TEXT--*/
				
					fram = 'FCKeditor1___Frame';
					//FCKtext = window.frames[fram].FCK.GetData();//SetData('aaa',true);//xEditingArea.frames[0].src;
					FCKtext = CKEDITOR.instances['FCKeditor1'].getData();
					$('#hold_temp_smarttag_data').html(FCKtext);
				
					if(strToReplace!='' && smart_tag_current_object){	//	alert(smart_tag_current_object==$('.cls_smart_tags_link[id="'+parentId+'"]'));
						$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
						//$(smart_tag_current_object).html(strToReplace);
						RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
						/*
						fram = 'FCKeditor1___Frame';
						FCKtext = window.frames[fram].FCK.GetData();//SetData('aaa',true);//xEditingArea.frames[0].src;
						$('#hold_temp_smarttag_data').html(FCKtext);
						*/
						var strippedData = $('#hold_temp_smarttag_data').html();
						strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');
				
						//window.frames[fram].FCK.SetData(strippedData,true);
						CKEDITOR.instances['FCKeditor1'].setData(strippedData,function(){});
						$('#div_smart_tags_options').hide();
					}else{
						alert('Select Options');
					}
				}
				//--------END CONFIGURE CKEDITOR FOR SMART TAGS--------------
			</script>
      
      <div id="hold_temp_smarttag_data" class="hide"></div>
	<?php
		}
	?>
</body>
</html>