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
File: show_image.php
Purpose: This file shows scanned, uploaded files.
Access Type : Direct
*/
include_once(dirname(__FILE__)."/../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';	
$id=$_REQUEST['id'];
$ext = $_REQUEST['ext'];
$noZoom = $_REQUEST['noZoom'];
$from = $_REQUEST['from'];
$scn_task = $_REQUEST['scn_task'];
$pid=$_GET["pid"];
$rootDir = substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);
$folder_id=$_REQUEST['folder_id'];
$file_name = $_GET["file_name"];
$userauthorized = $_SESSION['authId'];
$nowDate = date('Y-m-d H:i:s');
if(!$pid) {
$pid = $_SESSION['patient'];
}

if(!empty($id)){
	$sql= "select file_path,folder_categories_id,task_physician_id from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id =$id";
	$res=imw_query($sql);
	$row=imw_fetch_array($res);	
	$file_path=$row['file_path'];
	$folder_categories_id=$row['folder_categories_id'];
	$task_physician_id=$row['task_physician_id'];
}

if($_SESSION['authId'] == $task_physician_id && $scn_task=='review') {
	$updateTskAllocationQry= "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl SET task_status='1', task_review_date='".$nowDate."' WHERE scan_doc_id ='".$id."'";
	$updateTskAllocationRes=imw_query($updateTskAllocationQry);
}else if($_SESSION['authId'] == $task_physician_id){
	$updateTskAllocationQry= "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl SET task_status='2', task_review_date='".$nowDate."' WHERE scan_doc_id ='".$id."'";
	$updateTskAllocationRes=imw_query($updateTskAllocationQry);
}
//START CODE TO CREATE LOG OF SCAN
	$scanProviderLog = providerViewLogFunNew($id,$_SESSION['authId'],$_SESSION['patient'],'scan');
//END CODE TO CREATE LOG OF SCAN

//START CODE TO CHANGE SCAN ICON ACCORDING TO CONDITION
	$scnImgSrcActive = scnDocReadChkFun($_SESSION['patient'],'scan',$_SESSION['authId']);
//START CODE TO CHANGE SCAN ICON ACCORDING TO CONDITION

$ChkAnyDocExistsNumRow = scnDocExistFun(constant("IMEDIC_SCAN_DB"),$_SESSION['patient'],$folder_categories_id); //FUNCTION FROM common/scan_function.php
if(!empty($file_path) && (strtolower($ext)=='html' || strtolower($ext)=='htm') || strtolower($ext)=='doc' ){
	echo '<script type="text/javascript">window.location.href=\''.$srcDir.$file_path.'\';</script>';
	exit;
}else if(!empty($file_path) && (strtolower($ext)=='rtf')){
	echo '<script type="text/javascript">window.location.href=\''.$web_root."/library/RTF2HTML/index.php?file_root=pt_doc_root&to_format=html&file_src=".urlencode($file_path).'\';</script>';
	exit;
}
$chkUnReadCatQry = "SELECT DISTINCT(pvlt.scan_doc_id) FROM ".$dbase.".provider_view_log_tbl pvlt,".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt
					WHERE sdt.folder_categories_id='".$folder_categories_id."'
					AND pvlt.scan_doc_id=sdt.scan_doc_id
					AND pvlt.provider_id='".$_SESSION['authId']."'
					AND pvlt.patient_id='".$_SESSION['patient']."'
					AND pvlt.section_name='scan'
				";
$chkUnReadCatRes = imw_query($chkUnReadCatQry);
$chkUnReadCatNumRow = imw_num_rows($chkUnReadCatRes);

$scnCatUnReadImageSrc = $GLOBALS['webroot'].'/images/sign.gif';
$scnCatUnReadImage = '<img src="'.$scnCatUnReadImageSrc.'" height="13" vspace="0" border="0" align="middle" title="Unread Folder">';
if($ChkAnyDocExistsNumRow==$chkUnReadCatNumRow) { $scnCatUnReadImage = ''; }
?>
<html>
<head>
 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 	<title>imwemr</title>
  <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
  <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
  <script language="JavaScript1.2">
		window.focus();	
		<?php if( $from <> 'fav_doc' ){ ?>
		var w = screen.availWidth*0.90;
		var h = screen.availHeight*0.95;
		var t = (screen.availHeight-h)/2;
		var l = (screen.availWidth-w)/2;
		
		//window.resizeTo(w,h);
		window.moveTo(l,t)
		var init = function(){
			document.getElementById('img-container').style.Width= (w-20)+"px";
			document.getElementById('img-container').style.maxWidth= (w-30)+"px";
			document.getElementById('img-container').style.height= ((window.opener.top.innerHeight*0.95)-150)+"px";
			document.getElementById('img-container').style.maxHeight= ((window.opener.top.innerHeight*0.95)-150)+"px";
		};
		window.resizeTo(w,h);
		
		//----- REMOVE EXCALMATION ALERT FOR UNREVIEWED DOCS IN SCAN DOCS------
		if(opener && opener.parent)
			opener.parent.document.getElementById('spnUnreadDocNaviId<?php echo $id;?>').innerHTML = '';		
					//START CODE TO REFRESH TITLE
					var mainWindow = top.opener.top;
					if(typeof top.opener.top=='object') { 
						if(typeof top.opener.top.opener=='object') {
							mainWindow = top.opener.top.opener; 
						}
					}
					if(typeof mainWindow=='object') {
						var curr_tab = mainWindow.top.document.getElementById('curr_main_tab').value; 
						if( mainWindow.top.refresh_control_panel )
							mainWindow.top.refresh_control_panel(curr_tab);
					}
					//END CODE TO REFRESH TITLE
					
					//START CODE FOR SCAN ICON
					var scnImgSrc=objOpener1=objOpener2='';
					scnImgSrc 	= '<?php echo $scnImgSrcActive;?>';
					var scnCatId = '<?php echo $folder_categories_id;?>';
					var catUnreadImg = '<?php echo $scnCatUnReadImage;?>'
					if(top.opener) {
						objOpener1 = top.opener;
						if(objOpener1.document.getElementById('14_ioc')) {
							objOpener1.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
						}	
						if(objOpener1.top.Title) {
							if(objOpener1.top.Title.document.getElementById('14_ioc')) {
								objOpener1.top.Title.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
							}
						}
						if(objOpener1.top.document.getElementById('unReadCatId'+scnCatId)) {
							objOpener1.top.document.getElementById('unReadCatId'+scnCatId).innerHTML=catUnreadImg;
						}
						if(typeof top.opener.top.opener=='object') {//alert('hlo');
							objOpener2 = top.opener.top.opener;
							if(objOpener2.document.getElementById('14_ioc')) {
								objOpener2.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
							}	
							if(objOpener2.top.Title) {
								if(objOpener2.top.Title.document.getElementById('14_ioc')) {
									objOpener2.top.Title.document.getElementById('14_ioc').innerHTML='<a href="javascript:void(0);"><span class="icon_glow"><img src="'+scnImgSrc+'" vspace="0" border="0" align="middle" title="Scan Docs" onClick="opTests(\'scanDcs\')" ></span></a>';
								}
							}
							if(objOpener2.top.document.getElementById('unReadCatId'+scnCatId)) {
								objOpener2.top.document.getElementById('unReadCatId'+scnCatId).innerHTML=catUnreadImg;
							}
						}
					}
					if(top.document.getElementById('unReadCatId'+scnCatId)) {
						top.document.getElementById('unReadCatId'+scnCatId).innerHTML=catUnreadImg;
					}
					var zoomfactor=0.02 //Enter factor (0.05=5%)
					function zoomhelper(){
						if( typeof zoomfactor === 'undefined') zoomfactor = 0.02;
						if (parseInt(whatcache.style.width)>10&&parseInt(whatcache.style.height)>10){
							whatcache.style.width=parseInt(whatcache.style.width)+parseInt(whatcache.style.width)*zoomfactor*prefix
							whatcache.style.height=parseInt(whatcache.style.height)+parseInt(whatcache.style.height)*zoomfactor*prefix
						}
					}
					function zoom(originalW, originalH, what, state){
						if (!document.all&&!document.getElementById) return;
						whatcache=eval("document.images."+what);
						prefix=(state=="in")? 1 : -1;
						if (whatcache.style.width==""||state=="restore" || state=="out"){
							whatcache.style.width=whatcache.getAttribute('data-width');
							whatcache.style.height=whatcache.getAttribute('data-height');
							if (state=="restore") return;
						}
						else{ zoomhelper() }
						beginzoom = setInterval("zoomhelper()",100)
					}
					
					function clearzoom(){
					if (window.beginzoom) clearInterval(beginzoom);
					}
					<?php } ?>
		
					function openWindowlogoFull(path){
						var clHeight = parseInt(parent.parent.document.body.clientHeight) ;
						var clWidth = parseInt(parent.parent.document.body.clientWidth);							
						window.open(path,"mywindow","left=1,top=5,scrollbars=1,resizable=1,width="+clWidth+",height="+clHeight+"");
					}
					</script>
</head>
<body <?php if( $from <> 'fav_doc' ){ ?> onLoad="init();" <?php } ?> >
<?php
if(($ext == "pdf") || (!empty($file_name) && !empty($pid)) ){
	$src = "";
	if(!empty($file_path)){
		$src = substr(data_path(1), 0, -1).$file_path;
	}else if($ext == "pdf"){
		$src = "common/pdfLoader.php?id=$id&ext=$ext";
	}else if(!empty($file_name) && !empty($pid)){
		$src = data_path(1)."PatientId_".$pid."/Folder/id_".$folder_id."/".$file_name.".pdf";
	}
	echo "<table width=\"100%\" height=\"100%\" border=\"0\" align=\"center\">";
	echo "<tr><td><iframe src=\"".$src."\" width=\"100%\" height=\"100%\" scrolling=\"no\"></iframe></td></tr>";
	echo "</table>";
}
else{
?>
<table width="100%" height="100%" border="0" align="center">
<?php if(!isset($noZoom) || empty($noZoom)){ ?>
	<tr height="65">
		<td align="center" valign="top">
			<a href="#" target="_self"  onMouseOver="zoom(200,150,'logo','in')" onMouseOut="clearzoom()" class="text_9">Zoom In(+)</a>&nbsp;|&nbsp;
			<a href="#" target="_self"  onMouseOver="zoom(650,550,'logo','restore')" class="text_9">Normal</a><br><br>
		</td>
	</tr>
<?php } ?>
<tr>
	<td align="center" valign="top">
		<div id="img-container" style="overflow:auto; ">
			<?php
				 $src1 = $rootDir.$file_path;
				 $file_path_web = $srcDir.$file_path;
				 if($ext=='tif' && $GLOBALS['gl_browser_name']!='ie'){
					 $tifSize = getimagesize($src1);
					 $tifJPGpath = substr($src1,0,-3).'jpg';
					 if(!file_exists($tifJPGpath)){		
						 exec('convert -density 300 -trim "'.$src1.'" -strip -quality 100 -interlace line -colorspace RGB -resize '.$tifSize[0].' "'.$tifJPGpath.'"', $output, $return_var);
					 }
					 $file_path_web = substr($file_path_web,0,-3).'jpg';
				 }
				 if(file_exists($src1)) {
					 $g1 = get_image_prop($src1,700,600);
					 $ow=$g1[0]; $oh=$g1[1];
			?>
	
				<img name="logo" data-width="<?php echo $ow;?>" data-height="<?php echo $oh;?>" src="<?php echo $file_path_web; ?>" style="max-width:none!important;width:<?php echo $ow;?>px;height:<?php echo $oh;?>px;"
				 onDblClick="openWindowlogoFull('<?php echo $file_path_web; ?>');" />
			<?php }  else { ?>	
				Error: Image not found.
			<?php } ?>
		</div>			
	</td>
</tr>
<?php if((!isset($noZoom) || empty($noZoom)) && !isset($_GET['hide_close_btn'])){?>
<tr>
	<td align="center" id="module_buttons" class="ad_modal_footer"><input type="button" value="close" class="btn btn-danger" onClick="javascript:window.close();" />
</tr>
<?php } ?>	
</table>
<?php } ?>
</body>
</html>