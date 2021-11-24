<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

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
	<body scroll="yes">

		<!-- The following OBJECT tag calls the LPK file. The VALUE attribute must contain the path to the LPK file. -->
		<div style="height:1px;  overflow:hidden;">
			<OBJECT classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331">
				<PARAM NAME="LPKPath" VALUE="/<?php echo $surgeryCenterDirectoryName;?>/admin/csxMultiUpload/csxmultiupload.lpk">
			</OBJECT>
		</div>
		
		<div style="height:480px; width:700px; overflow:hidden;">
			<!-- The following OBJECT tag calls the csXMultiUpload trial control -->
			<OBJECT id="upload" classid="CLSID:C5BD337E-B091-46B1-9D2E-AA8F5AF11929" CODEBASE="/<?php echo $surgeryCenterDirectoryName;?>/admin/csxMultiUpload/csxmultiupload.cab" width="700" height="300">
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
<!--<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331"><param name="LPKPath" value="/<?php echo $surgeryCenterDirectoryName;?>/admin/csXThumbUpload/csXThumbUpload.lpk"></object>
<object id="upload" classid="CLSID:078B03B7-23A0-4EA6-A0A9-6E27EA68BE79" width="800" height="450" codebase="/<?php echo $surgeryCenterDirectoryName;?>/admin/csXThumbUpload/csXThumbUpload.cab#version=1,0,2,1">

<PARAM name="RemoteURL" Value="<?php echo urldecode($upload_url);?>">
<PARAM name="FormTagName" Value="files">

<param name="TileWidth" value="100" />
<param name="TileHeight" value="100" />
<param name="TilesPerRow" value="5" />  
<param name="DisplayThumbnails" value="true" /> 
<param name="DisplayIconsFirst" value="true" />
<param name="PreviewAreaWidth" value="550" />
<param name="PreviewAreaHeight" value="300" />


<param name="FormColor" value="&H8110000B" />
<param name="TileColor" value="&H8110000B" /> 
</object>

<script language="javascript">
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
<div id="uploadstatus"></div>-->
</body>
</html>