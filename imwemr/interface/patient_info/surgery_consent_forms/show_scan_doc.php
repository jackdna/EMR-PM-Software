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
require_once("../../../config/globals.php");
$scanDocId = $_REQUEST['scanDocId'];

//print_r($_REQUEST);
if($_REQUEST["delId"]<>"" && $_REQUEST["scanDocPath"]<>""){
	$scanDocPath = trim($_REQUEST["scanDocPath"]);	
	$deleteScanDocId = $_REQUEST["delId"];
	if(file_exists($scanDocPath)){
		unlink($scanDocPath);
		$qryDeleteScanDoc = "delete from surgery_center_patient_scan_docs where id = $deleteScanDocId";
		$rsDeleteScanDoc = imw_query($qryDeleteScanDoc);	
		
?>
<script type="text/javascript">
	if(top.fmain) {
		top.fmain.left_panel();	
		var f = top.fmain.document.getElementById('consent_data_id_surgery');
		f.contentWindow.location.reload(false);
	}
	top.alert_notification_show('Scanned/uploaded document successfully deleted.');
</script>
<?php
	}
}

$qry = "select scan_doc_add as scanDocPath,mask from surgery_center_patient_scan_docs where id = $scanDocId";
$sql = imw_query($qry);
$num = imw_num_rows($sql);
if($num > 0){
	extract(imw_fetch_array($sql));
}

$message = '';
$fileExists = false;
$isPdf = false;
$img_width = 0;
$img_height = 0;
if ($scanDocPath!=""){
	$dir_path = substr(data_path(),0,-1).$scanDocPath;
	$web_path = substr(data_path(1),0,-1).$scanDocPath;
	
	$arrPatientImage = explode('/',$scanDocPath);
	if($mask){
		$arrMask = explode('.',$mask);
		$patientImageName = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrPatientImage[2]);
		$patientImageName = $arrFilename[0];
	}
	if( file_exists($dir_path) )
	{
		$fileExists = true;
		$isPdf = (stristr($scanDocPath,'.pdf')===false) ? $isPdf : true;		
		if( !$isPdf){
			list($img_width, $img_height) = getimagesize($dir_path);
		}
	}
	else
	{
		$message = 'No scaned documents !';
	}
}
else
{
	$message = 'No scaned documents !';
	
}
			
?>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script language="javascript">
	window.focus();
	function print_scan_form() {
		var scanDocId = '<?php echo $scanDocId;?>';
		window.open('../patient_info/surgery_consent_forms/show_scan_doc.php?scanDocId='+scanDocId,'scanPrint','');	
}
	function delImg(id,scanDocPath){
		if(confirm('Sure ! You Want To Delete Scanned Document')){
			document.getElementById("delId").value = id;
			document.getElementById("scanDocPath").value = scanDocPath;
			document.delFrmScanDoc.submit();
		}
	}
</script>
<body class="bg-white"> 

	<div class="container-fluid">
  	<form name="delFrmScanDoc" action="" method="post">
  		<input type="hidden" name="delId" id="delId" value="">
			<input type="hidden" name="scanDocPath" id="scanDocPath" value="">
		
    	<?php if( $fileExists): ?>
    	<div class="row">
      	<div class="col-xs-12">
        	
          <?php if( $isPdf): ?>
          <div id="pdfContent">
          	<object data="<?php echo $web_path; ?>" width="100%" type="application/pdf" height="95%"></object>
          </div>
          <?php else: ?>
          <div id="imgContent" style="max-width:100%; min-width:100%; max-height:93%; overflow:hidden;overflow-y:auto;">
          	<img src="<?php echo $web_path;?>" border="1" style="height:<?php echo $img_height;?>px;">
					</div>
          <?php endif; ?>
      	    
        </div>
      </div>
      
      <div class="row">
      	<div class="col-xs-12 text-center">
        	<?php echo $patientImageName; ?>
      	</div>
      </div>
      
      <div class="row">
      	<div class="col-xs-12 text-center">
        	<a class="btn btn-danger" href="javascript:delImg('<?php echo $scanDocId;?>','<?php echo $dir_path;?>');">Delete</a>
      	</div>
      </div>
      
      <?php else: ?>
      <div class="row">
      	<div class="col-xs-12 text-center pd15 pt30">
      		<?php echo $message;?>  
        </div>
     	</div>   
      <?php endif; ?>
		</form>
	</div>    
</body>