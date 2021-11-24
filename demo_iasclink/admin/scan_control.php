<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
if(!$iolinkWebrootDirectoryName) 		{ $iolinkWebrootDirectoryName=$iolinkDirectoryName;					}
?>
<?php if($js_inc!=true){ ?>
<script src="../js/jquery.js"></script>
<?php } 
$scanDtm = date("ymdHis");
?>
<script language="Javascript" type="text/javascript">

//<![CDATA[
var Success;
var CurrentImage;
var Pages = new Array();
var ImageCount = 0;
var scanDtm = '<?php echo $scanDtm;?>';
function enable_duplex(){
	if (document.form1.duplex.checked)
    {
      csxi.TwainDuplexEnabled  = true;
    }
    else
    {
      csxi.TwainDuplexEnabled  = false;
    }//alert(csxi.twainduplexenabled)

}
function enable_multiscan(){
	if (document.form1.ADF.checked)
    {
		csxi.TwainMultiImage = true;
		csxi.UseADF = true;
		csxi.twainimagestoread = no_of_scans;
	}else{
		csxi.TwainMultiImage = false;
		csxi.UseADF = false;
		csxi.twainimagestoread = 1;
	}

}
function SelectClick()
{	
	document.getElementById("uploadbutton").disabled = false;
	/*enable_duplex();
	if(multiScan == 'yes')
	enable_multiscan();*/
	if(ImageCount< no_of_scans){  
		csxi.SelectTwainDevice();
		if (csxi.TwainConnected)
		{	//alert(csxi.TwainDuplexEnabled );
			ScanClick();
		}
	}else{
		alert("Only "+no_of_scans+" scan(s) allowed here.");
		document.form1.scanbutton.disabled = true
		document.form1.selectbutton.disabled = true
	}
}
function ScanClick()
{	
	//if(multiScan == 'yes')
	//setDefault();
	document.form1.scanbutton.disabled = false
	csxi.Acquire();
	CurrentImage = ImageCount;
    if (csxi.ImageHeight != 0)
    {
      document.form1.uploadbutton.disabled = false
      document.form1.rectbutton.disabled = false
      document.form1.leftbutton.disabled = false
      document.form1.rightbutton.disabled = false
    }
    else
    {
      document.form1.uploadbutton.disabled = true
      document.form1.rectbutton.disabled = true
      document.form1.cropbutton.disabled = true
      document.form1.leftbutton.disabled = true
      document.form1.rightbutton.disabled = true
    }
}
function upload(f,doNotShowAlert){
	if(ImageCount<=0) {
		if(doNotShowAlert) {
			//DO NOT SHOW ALERT
			if(top.consent_tree) {
				top.consent_tree.location.reload();	
			}
			if(document.frm_new_scan) {
				document.frm_new_scan.submit();	
			}
		}else {
			alert("Please select device/image to scan");
		}
	}else {
		document.getElementById("uploadbutton").disabled = true;
		f = f || '';
		document.getElementById('uploadMsg').style.display = 'block';
		setTimeout(function(){UploadClick(f)},1000);
	}
}
function trimScan(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function UploadClick(f)
{ //Add the URL to the file saving script, including the http:// prefix.
	var j=1;
	var scanImgName = 'Imedic';
	var scanFinalmgName = '';
	if(document.getElementById("scan_img_name")) {
		if(trimScan(document.getElementById("scan_img_name").value)!='') {
			scanImgName = document.getElementById("scan_img_name").value;	
		}
	}
	for(var i = 1; i<=ImageCount; i++)
    {	str = '';
      	csxi.ReadBinary(0, Pages[i]);
      	//csxi.Redraw();
		j=i;
		if(j<=9){
			j='0'+j;
		}
		scanFinalmgName = scanImgName;
		if(ImageCount>1) {
			scanFinalmgName = scanImgName+'-'+j;	
		}
		Success = csxi.PostImage(uploadScanURL, scanFinalmgName, 'file[]', 2);
		if (Success)
		{ 	if(i<ImageCount)str = ". Please wait uploading next image.... ";
			alert('Image Uploaded');
			document.getElementById('uploadMsg').innerHTML = i +' Image(s) Uploaded'+str;
			if(top.consent_tree) {
				top.consent_tree.location.reload();	
			}
			if(document.frm_new_scan) {
				document.frm_new_scan.submit();	
			}
			
			//a = window.open();
			//a.document.write(csxi.PostReturnFile)
		}
			
		else
		{alert('Upload Failed');
		 document.getElementById('uploadMsg').innerHTML = i +'Upload Failed';}
		 setTimeout('',100);
	}
	ImageCount = 0;
	Pages = new Array();
		if(f != '')
		f.submit();
}
function RectClick()
{csxi.MouseSelectRectangle();document.form1.cropbutton.disabled = false;}
function CropClick()
{	
	csxi.CropToSelection();
	document.form1.cropbutton.disabled = true;
	csxi.Redraw();
	Pages[CurrentImage] = csxi.WriteBinary(0); 
}
function LeftClick()
{csxi.Rotate(90);
Pages[CurrentImage] = csxi.WriteBinary(0); 
}
function RightClick()
{csxi.Rotate(270);
Pages[CurrentImage] = csxi.WriteBinary(0); 
}
 function PrevClick()
  {
    if (CurrentImage > 1)
    {
      CurrentImage -= 1;
      csxi.ReadBinary(0, Pages[CurrentImage]);
      csxi.Redraw();
      document.form1.pagenumtext.value = "Page " + CurrentImage + " of " + ImageCount; 
    }
  }
   function NextClick()
  {
    if (CurrentImage < ImageCount)
    {
      CurrentImage += 1;
      csxi.ReadBinary(0, Pages[CurrentImage]);
      csxi.Redraw();
      document.form1.pagenumtext.value = "Page " + CurrentImage + " of " + ImageCount; 
    }
  }
  function csxiacquire(Button, ShiftState, X, Y)
  {
	if(ImageCount< no_of_scans){  
		csxi.Redraw();
		ImageCount += 1;
		Pages[ImageCount] = csxi.WriteBinary(0);
	}
	if(ImageCount >1){ 
		$('#divDupCtrl').show()
		document.form1.pagenumtext.value = "Page " + ImageCount + " of " + ImageCount;
	}
  }
//]]>
</script>
<center>
<form name="form1" action="">
<table  border="0" >
<tr>
  <td><input type="button" name="selectbutton" id="selectbutton" value="Select Device" onClick="SelectClick()"    class="dff_button_sm"  /></td>
  <td colspan="2"><input type="button" name="scanbutton" id="scanbutton" value="Scan Image" onClick="ScanClick()"    class="dff_button_sm" /></td>
 <td><input type="button" name="uploadbutton" id="uploadbutton" value="Upload Image" onClick="upload()"   class="dff_button_sm"/></td>
</tr>
<tr>
  <td><input type="button" name="rectbutton" id="rectbutton" value="Select Area" onClick="RectClick()" class="dff_button_sm"/></td>
  <td><input type="button" name="cropbutton" id="cropbutton" value="Crop" onClick="CropClick()"  class="dff_button_sm" /></td>
  <td><input type="button" name="leftbutton" id="leftbutton" value="Rotate Left" onClick="LeftClick()" class="dff_button_sm"/></td>
  <td><input type="button" name="rightbutton" id="rightbutton" value="Rotate Right" onClick="RightClick()"   class="dff_button_sm"/></td>
</tr>

<tr>
  <td colspan="4"></td>	
</tr>

<tr>
  <td colspan="4">
  <div id="divChkDuplex" style="display:none;">
  <input type="checkbox" name="duplex" value="1"  onclick="csxi.twainduplexenabled = true;"/>Use Duplex function of scanner, if available.
  </div>
  <div id="divChkMulti" style="display:none;">
  <input type="checkbox" name="ADF" value="1" id="ADF" onclick="enable_multiscan();"/>Use Multi Scanning of scanner, if available.
  </div>
  <div id="divDupCtrl" style="display:none">
  <input type="button" name="prevbutton" value="Prev." onclick="PrevClick()" class="dff_button_sm"/>
  <input type="button" name="nextbutton" value="Next" onclick="NextClick()" class="dff_button_sm"/>
  <input type="text" name="pagenumtext" value=""  style=" border:none" readonly="readonly" class="text_small"/>
  </div>
  </td>
</tr>
<tr>
  <td colspan="4"></td>	
</tr>
<tr>
  <td colspan="4" class="text_10" style="white-space:nowrap;">Scan Name<span style="padding-left:5px;"><input type="text" name="scan_img_name" id="scan_img_name" value="" class="text_10" /></span></td>
</tr>

<tr><td colspan="4"><div id="uploadMsg" class="warning text12b" style="display:none;">Please wait image is uploading......</div></td></tr>
</table>
</form>
<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331" style="z-index:0">
  <param name="LPKPath" value="<?php echo '/'.$iolinkWebrootDirectoryName;?>/admin/scan_control/csximage.lpk" />
</object>
<object id="csxi" classid="clsid:62e57fc5-1ccd-11d7-8344-00c1261173f0" codebase="<?php echo '/'.$iolinkWebrootDirectoryName;?>/admin/scan_control/csXImage.cab" width="500" height="350" ></object>                                        
</center>
<script>
var autoScan = autoScan || 'yes';
var autoAcquire = autoAcquire || 'yes';
var multiScan = multiScan || 'no';
var duplexScan = duplexScan || 'no';
var no_of_scans = no_of_scans || 1;
var imgNme = imgNme || 'iolink';
var showDtm = showDtm || 'yes';
if(showDtm!='yes') {
	scanDtm='';	
}
//if(autoScan == "undefined" || autoScan== null){

//}

window.onload = function() {
	//var onacquireevent = document.getElementById("csxi");
    //onacquireevent.addEventListener('onAcquire', csxiacquire);

    document.form1.selectbutton.disabled = false
    document.form1.scanbutton.disabled = true
    document.form1.uploadbutton.disabled = true
    document.form1.rectbutton.disabled = true
    document.form1.cropbutton.disabled = true
    document.form1.leftbutton.disabled = true
    document.form1.rightbutton.disabled = true
	if(multiScan == 'yes'){
	document.getElementById("ADF").checked = true;
	$("#divChkMulti").show();
	}
	enable_duplex();
	enable_multiscan();

	if(duplexScan == 'yes')
	$("#divChkDuplex").show();

	if(autoScan == "yes"){
				SelectClick();
	}
	if(document.getElementById("scan_img_name")) {
		document.getElementById("scan_img_name").value=imgNme+scanDtm;	
	}
	
	$("#scanButton").click(function(){
		SelectClick();
		
	})
	$("#iolinkUploadBtn",top.document).click(function(){
		upload('','doNotShowAlert');
	})
	$("#closeButton").click(function(){
		upload('','doNotShowAlert');
	})
}
</script>
<script language="JavaScript" for="csxi" event="onAcquire(Button, ShiftState, X, Y);" type="text/javascript">
csxiacquire(Button, ShiftState, X, Y);
</script>