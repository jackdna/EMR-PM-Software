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
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once($GLOBALS['fileroot']."/library/classes/cls_common_function.php");
$library_path = $GLOBALS['webroot'].'/library';	

	$OBJCommonFunction = new CLSCommonFunction;	
	$patient_id = $_SESSION['patient'];
    $userauthorized = $_SESSION['authId'];
    $scan_id = $_SESSION['document_scan_id'];
    $_SESSION['document_scan_id']="";
    unset($_SESSION['document_scan_id']);
    
//port
if($_SERVER["SERVER_PORT"] == 80){
	$phpHTTPProtocol="http://";
}
    
    
	$show = $_REQUEST['show'];
	if($phpServerIP != $_SERVER['HTTP_HOST'])	
	{
		$phpServerIP=$_SERVER['HTTP_HOST'];
		$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
	}		
   
   //START GET PRIMARY PHYISICIAN-ID
   if($patient_id) {
	   $priPhyIdQry = "SELECT providerId FROM patient_data WHERE pid = '".$patient_id."'";
	   $priPhyIdRes = imw_query($priPhyIdQry) or die(imw_error());
	   if(imw_num_rows($priPhyIdRes)>0) {
			$priPhyIdRow = imw_fetch_array($priPhyIdRes);   
			$priPhyId = $priPhyIdRow["providerId"];
	   }
   }
   //END GET PRIMARY PHYISICIAN-ID
   
   $folder_id=$_REQUEST['folder_id'];
   $flPth = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&cat_id=".$folder_id;   
   $alertPhysicianNew = "";
   if($folder_id) {
	   $folderQry = "SELECT alertPhysician FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_categories_id = '".$folder_id."'";
	   $folderRes = imw_query($folderQry) or die(imw_error());
	   if(imw_num_rows($folderRes)>0) {
			$folderRow = imw_fetch_array($folderRes);   
			$alertPhysicianNew = $folderRow["alertPhysician"];
	   }
   }
   
   $task_physician_id_post = $_REQUEST['task_physician_id'];
   if($_REQUEST['comment'] || $task_physician_id_post){
	$chkNewDt=date('Y-m-d H:i:s', mktime(date("H"),date("i"),date("s")-60,date("n"),date("j"),date("Y"))); //1 min before current time
	
	$comment12 = $_REQUEST['comment'];
	if($comment12)
	{
		$chkCmntQry = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where patient_id='$patient_id' && folder_categories_id ='".$folder_id."' && doc_upload_type ='upload' && scan_doc_id  ='$scan_id'";
		$chkCmntRes =imw_query($chkCmntQry);
		if(imw_num_rows($chkCmntRes)>0){
			$chkCmntRow = imw_fetch_array($chkCmntRes);
			$chkDocUploadDate = $chkCmntRow['upload_docs_date'];
			//$scanOrUploadDate = '$chkDocUploadDate';
			$explDtTm = explode(' ',$chkDocUploadDate);
			list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
			list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
			$chkNewDt = date('Y-m-d H:i:s', mktime($hr,$min,$scnd-60,$mnth,$dy,$yr));
		}
	}
	
	//
	$phrse_scn="";
	$scan_id = trim($scan_id,",");
	if(!empty($scan_id)){ $phrse_scn = " AND scan_doc_id IN (".$scan_id.") "; }else{ $phrse_scn = " AND upload_docs_date >= '".$chkNewDt."' "; }

	$qry = "update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set upload_comment ='".addslashes($comment12)."', task_physician_id ='".$task_physician_id_post."', task_status ='0' where patient_id='$patient_id' && folder_categories_id ='".$folder_id."' && doc_upload_type ='upload'  AND task_physician_id='0' ".$phrse_scn;
	$res = imw_query($qry);
	$affcted_row = imw_affected_rows();
	if($affcted_row>0) {?>
		<script>top.show_loading_image("hide");top.alert_notification_show("Record saved successfully!"); var force_refresh=true;</script>
    <?php
	}else {?>
		<script>top.show_loading_image("hide");top.fAlert("Please add file(s) to save");</script>	
    <?php		
	}
}
$selQry = "select DATE_FORMAT(upload_docs_date,'".get_sql_date_format()." %h:%i %p') AS crtDate12,upload_comment,task_physician_id from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where patient_id ='$patient_id' &&  folder_categories_id ='".$folder_id."' && doc_upload_type ='upload' order by `upload_docs_date` desc limit 0,1";
$resQry = imw_query($selQry);
$rowQry = imw_fetch_array($resQry);

//START CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY
$ctId = scnfoldrCatIdFunNew(constant("IMEDIC_SCAN_DB"),'Medication');
$ChkMedDocExistsNumRow='';
if($ctId) {
	$ChkMedDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$patient_id,$ctId); //FUNCTION FROM scan_function.php	
}
$ChkAnyDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$patient_id); //FUNCTION FROM scan_function.php
$scnImgSrcActive 	= $GLOBALS['webroot'].'/library/images/scanDcs_active.png';
$scnImgSrcDeActive 	= $GLOBALS['webroot'].'/library/images/scanDcs_deactive.png';

if($ChkAnyDocExistsNumRow>0) { $scnImgSrcActive = scnDocReadChkFun($patient_id,'scan',$_SESSION['authId']); }
?>
<script language="javascript">
	//START CODE TO REFRESH TITLE 
	if(typeof top.opener.top=='object') {
		var mainWindow = top.opener.top;
		var curr_tab = mainWindow.top.document.getElementById('curr_main_tab').value; 
		mainWindow.top.refresh_control_panel(curr_tab);
	}
	//END CODE TO REFRESH TITLE 	
	var anyDocExistsNumRow='<?php echo $ChkAnyDocExistsNumRow;?>';
	var scnImgSrc;
	if(anyDocExistsNumRow>0) {
		scnImgSrc = '<?php echo $scnImgSrcActive;?>';
	}else {
		scnImgSrc = '<?php echo $scnImgSrcDeActive;?>';
	}
	if(top.opener) {
		if(top.opener.document.getElementById('14_ioc')) {
			top.opener.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
		}	
		if(top.opener.top.Title) {
			if(top.opener.top.Title.document.getElementById('14_ioc')) {
				top.opener.top.Title.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
			}
		}
		//START CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
		var scnImgMedSrc=scan_img_val_med='';
		var medDocExistsNumRow	= '<?php echo $ChkMedDocExistsNumRow;?>';
		if(medDocExistsNumRow>0) {
			scnImgMedSrc		= '<?php echo $scnImgSrcActive;?>';
			scan_img_val_med	= '<img src="'+scnImgMedSrc+'"  style="cursor:pointer;" vspace="0" border="0" align="middle" title="Scan Docs" onClick="window.open(\'scan_docs/index.php?med_type=Medication\',\'scanDocs\',\'resizable=yes,scrollbars=1,location=yes,status=yes, width=1000 height=700\');" >';
		}
		if(top.opener.document.getElementById('14_ioc_med')) {//OCULAR MEDICATION IN GENERAL HEALTH DIV
			top.opener.document.getElementById('14_ioc_med').innerHTML=scan_img_val_med;
		}
		//END CODE TO SET SCAN-ICON IN GENERAL HEALTH MEDICATION(IF MEDICATION EXISTS)
		
	}
	
</script>
<?php
//END CODE TO CHECK IF ANY DOCUMENT(SCAN/UPLOAD) EXIST FOR THIS PATIENT AND SET BACKGROUND OF SCAN-DOC ACCORDINGLY

?>
<html>
<head>
<title>Upload Multiple Documents</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    
    

    <link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css"  type="text/css">
    <link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
    
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	
	<script>
	   function enlargeImage(sId){		
       }
		function showpdfScnDocs( id,pdf ){	
			if( (typeof id != "undefined") && (id != "") ){
				var url = "../show_image.php?id="+id+"&ext="+pdf;
				window.open(url,"","width=300,height=200,resizable=1,scrollbars=1");
				
			}
		}
		function hideClose(){
			if(top.document.getElementById("btn_close")){
				var o = document.getElementById("Close");
				if(o){
					o.style.display="none";
				}
			}
			if(top.document.getElementById("btSaveComment")){
				top.document.getElementById("btSaveComment").style.display = "inline-block";
			}
			if(top.document.getElementById("btBackFolderCat")){
				top.document.getElementById("btBackFolderCat").style.display = "inline-block";
			}
			if(top.document.getElementById("scnDocmntBtn")){
				top.document.getElementById("scnDocmntBtn").style.display = "none";
			}
			if(top.document.getElementById("upldDocmntBtn")){
				top.document.getElementById("upldDocmntBtn").style.display = "none";
			}
			if(top.document.getElementById("btAddNew")){
				top.document.getElementById("btAddNew").style.display = "none";
			}
		}
		
		function reload_page(){
			if(top.frames['fmain']) {
				top.show_loading_image("show",""," Loading...");
				top.frames['fmain'].location.href = '<?php echo $flPth; ?>';
			}
		
		}
		function init()
		{
			alert("1")
		}
		function frm_submit()
		{
			document.upload_doc.submit();
		}
		function call_search1(){

			document.getElementById('show').value='search1';
			frm_submit();
		}
		function save_comments(){
			top.show_loading_image("show",""," Loading...");
			frm_submit();
		}
		function go_back_folder_cat(){
			reload_page();
		}
	</script>
</head>
<body  class="scrol_Vblue_color Test_VF" >
<div id="divImages" style="position:relative;overflow:auto;width:100%;height:100%;">

<?php if($show != 'search1'){?>
<div class="col-xs-12">Upload Multiple Documents</div>
<div id="upload_id_div" class="col-xs-12 " style="max-height:95%; height:95%; overflow:hidden; overflow-y:auto;">
	
    <div class="col-xs-12">&nbsp;</div>
    <div class="col-xs-12">
		<?php  
		$arrBrow = browser(); // --defined in sql.php file------
		if($arrBrow['name'] == 'msie' && $arrBrow['version'] < 10){
			/* ?><iframe src="../../common/csxthumbupload.php?folder_id=<?php echo $_REQUEST['folder_id'];?>&upload_url=<?php echo urlencode($GLOBALS['php_server']."/interface/chart_notes/upload_test.php?imwemr=".session_id()."&folder_id=".$_REQUEST['folder_id']."&activex=1");?>" width="1050" height="500" scrolling="yes"> </iframe> <?php */
		}else{
			$upload_url=$GLOBALS['webroot']."/interface/chart_notes/upload_test.php";
		?>
            <script> var upload_url = '<?php echo $upload_url;?>'; </script>
		<?php
			include_once $GLOBALS['fileroot']."/library/upload/index.php";
		}?>
    </div>
</div>
<form action="upload_multi_docs.php" method="post" name="upload_doc" id="upload_doc">
    <input type="hidden" name="show" value="<?php echo $show; ?>">
    <input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id']; ?>">
    <input type="hidden" name="prevType" value="<?php echo $_REQUEST['prevType']; ?>">
    <input type="hidden" name="refreshLeftNav" id="refreshLeftNav" value="<?php echo $_REQUEST['refreshLeftNav']; ?>">
    <input type="hidden" name="sId" value="">
    <div class="col-xs-2">
        <?php 
        if($alertPhysicianNew=='1') { $txtAreaWidth='400px;';?>		
            <label style="size:30; position:relative; "><b>Physician&nbsp;Alert:</b></label>
            <select class="selectpicker"   name="task_physician_id" id="task_physician_id" >
                <option value="">Nothing selected</option>
                <?php
                $task_physician_id = $priPhyId;
                echo $OBJCommonFunction->dropDown_providers($task_physician_id,'','1');
                ?>
            </select>
        <?php 
        }else {$txtAreaWidth='600px;';?>
            <input type="hidden" name="task_physician_id" id="task_physician_id">
        <?php 
        }?>                                        	
    
    </div>
    <div class="col-xs-1"></div>
    <div class="col-xs-4">
        <label style="size:30; position:relative; "><b>Comment:</b></label>
        <textarea name="comment" id="comment" class="form-control" ><?php echo $rowQry['upload_comment'];?></textarea>
        <input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id'];?>">
    </div>
    <div class="col-xs-5">&nbsp;</div>
	<?php 
    if(($rowQry['crtDate12'] != '00-00-0000 12:00 AM') && ($rowQry['crtDate12'] != '')){?>
        <div class="col-xs-12">
            Last Upload Date Time-:&nbsp;<?php echo $rowQry['crtDate12'];?>
        </div>
    <?php 
    }?>

 <script>hideClose();</script>
   <?php }else{
		$search13 = $_POST['search1'];
		   $selQry = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where upload_comment LIKE '$search13'";
					$res = imw_query($selQry);
					$rowQry = imw_fetch_array($res);
					$search12= $rowQry['upload_comment'];	
					echo '<table width="100%">';
					echo '<tr valign="middle">';
				if($search12 != ''){
						$getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
						where patient_id = '$patient_id' 
						AND upload_comment = '$search12'
						ORDER BY scan_doc_id";
				}
				if($_REQUEST['CompareBtn'] == "Compare"){
						$imageArr = $_REQUEST['imageArr'];
						if(count($imageArr)>0){
							foreach($imageArr as $imagesCompareId){
								if($imagesId){
									$imagesId = $imagesId.', '.$imagesCompareId;
								}else{
									$imagesId = $imagesCompareId;
								}
							}
						}
					$getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl
					WHERE scan_doc_id in ($imagesId)";
				}
				if($_REQUEST['sId']){
						$sId = $_REQUEST['sId'];
					   $getImagesToShowStr = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
						WHERE scan_doc_id in ($sId)";
				}
				//echo $getImagesToShowStr;
				$getImagesToShowQry = imw_query($getImagesToShowStr);
					if(imw_num_rows($getImagesToShowQry)>0){				
						while($getImagesToShowRows = imw_fetch_assoc($getImagesToShowQry)){
							$scan_id = $getImagesToShowRows['scan_doc_id'];
							$image_name = $getImagesToShowRows['doc_title'];					
							$imageScanedArr[]  = $scan_id;
							$doc_title = $getImagesToShowRows['doc_title'];
							$pdf_url = $getImagesToShowRows['pdf_url']; 					
							$fileType = $getImagesToShowRows['doc_type'];
							if($count>=4){
								echo '</tr><tr>';
								$count = 0;
							}
							$count++;
							?>
							<td align="left" valign="middle">
							 <table border="1" cellpadding="0" cellspacing="0">
							  <tr>
							   <td align="center">
								<?php if( $fileType == "pdf"){ ?>
								<img style="cursor:hand; " src="<?php echo $GLOBALS['rootdir']."/front_office/pdflogo.jpg";?>" alt="pdf file" onClick="showpdfScnDocs('<?php echo $getImagesToShowRows['scan_doc_id'];?>','pdf')">
								<?php }else{?>									
								<img style="cursor:hand; " border="0" xonDblClick="return enlargeImage('<?php echo $getImagesToShowRows['scan_doc_id'];?>');" onClick="showpdfScnDocs('<?php echo $getImagesToShowRows['scan_doc_id']; ?>','')" id="imgThumbNail<?php echo $getImagesToShowRows['scan_doc_id']; ?>" src="../folder_large_image.php?id=<?php echo $getImagesToShowRows['scan_doc_id'];?>"height="70" width="120">
								<!-- <img style="cursor:hand; " border="0" xonDblClick="return enlargeImage('<?php //echo $scan_id; ?>');" onClick="showpdf('<?php echo $scan_doc_id; ?>','')" id="imgThumbNail<?php echo $scan_doc_id; ?>" src="logoImg.php?from=scanImage&scan_id=<?php echo $scan_doc_id; ?>"> -->
							      <?php }?>
							       </td>
							       </tr>							
							         <tr>
							          <td align="center">
							           <?php echo $search1; ?>
							            <!--<input type="button" name="elem_btnDel<?php echo $scan_doc_id; ?>" id="elem_btnDel" value="Delete" class="dff_button" n onMouseOver="button_over('elem_btnDel<?php echo $scan_id; ?>')" onMouseOut="button_over('elem_btnDel<?php echo $scan_id; ?>','');" onClick="delImg( '<?php echo $scan_doc_id; ?>' );" >-->
							            </td>
							           </tr><br>
							          </table>
							        </td>
								 <?php }	
							 } else{
					         ?>
					      <tr>
					    <td align="center"><b>No Image Found.</b></td>
					  </tr>
					 </tr>
				</table>
			<?php } 
	  }if($show=='search1'){ ?>
			<table width="100%" id="imageTable1"> 
			<tr height="1">
				<td ></td>
			    </tr>
			      <tr>
				    <td valign="middle" align="center" height="40px">
					<input type="button" style="width:100px;" id="back" onClick="document.getElementById('show').value='';document.upload_doc.submit();" class="dff_button" name="backBtn" onMouseOver="button_over('back')" onMouseOut="button_over('back','')" value="Back">
				</td>
			</tr>
		</table>
  <?php } ?>
</form>

</div>
</body>
<script>
top.show_loading_image("hide");
var dh_split = $( document ).height()-140;
$("#upload_id_div").height(dh_split+'px');
$(document).ready(function(e) {
    $("select.selectpicker").selectpicker();
});
	if(force_refresh==true)
	{
		reload_page();
	}
</script>
</html>	