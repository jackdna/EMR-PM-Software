<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
include_once("../common/conDb.php");
$upload_url = $_REQUEST['upload_url'];
//echo $upload_url;
$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$servername = $_SERVER['SERVER_NAME'];
$upload_url_new = $protocol.$servername.urldecode($upload_url);
?>

<html>
<head>
<?php if($js_inc!=true){ ?>
<script src="../js/jquery.js"></script>
<?php } ?>
</head>
<body scroll="yes">
<!-- The following OBJECT tag calls the LPK file. The VALUE attribute must contain the path to the LPK file. -->
<div style="height:1px;  overflow:hidden;">
<OBJECT classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331"><PARAM NAME="LPKPath" VALUE="csxMultiUpload/csxmultiupload.lpk"></OBJECT>
</div>
<div style="height:480px; width:700px; overflow:hidden;">
<!-- The following OBJECT tag calls the csXMultiUpload trial control -->
<OBJECT id="upload" classid="CLSID:C5BD337E-B091-46B1-9D2E-AA8F5AF11929" CODEBASE="csxMultiUpload/csxmultiupload.cab" width="700" height="300">
<PARAM name="RemoteURL" Value="<?php echo $upload_url_new;?>">
<PARAM name="FormTagName" Value="files">
</OBJECT>
</div>
<SCRIPT LANGUAGE="JavaScript" FOR="upload" EVENT="onStartFile(FileNumber, FileTotal)">
if (FileNumber == FileTotal)
{
upload.AddFormVariable('LastFile', 'true');
}
</SCRIPT>
<script language="javascript">
window.onload = function()
  {
    if (screen.deviceXDPI) { // To avoid throwing JavaScript errors in other browsers
        window.onresize = onResize;
 
    }

	$("#iolinkUploadBtn",top.document).click(function(){
		if(top.consent_tree) {
			top.consent_tree.location.reload();	
		}
	})  
  }

  function onResize()
  {
    var zoom;
    zoom = screen.deviceXDPI / 96;
    uploadControl = document.getElementById("upload")
    uploadControl.width = 377 / zoom
    uploadControl.height = 477 / zoom
   // alert(uploadControl.width = 377 / zoom);
  }

  function DisplayReturnText()
  {
    x=document.getElementById("returnfile");
    if(upload.ReturnText!='')
	alert(upload.ReturnText);
    y = document.getElementById("uploadstatus");
   // y.innerHTML = upload.uploadstatus;
  }
function controlNotLoaded()
{
     var obj = document.getElementById("upload");
     return (obj.object == null);
}
//controlNotLoaded();


</script>

<script language="javascript" for="upload" event="OnUploadComplete">
DisplayReturnText();
</script>

<div id="returnfile"></div>
<div id="uploadstatus"></div>
</body>
</html>