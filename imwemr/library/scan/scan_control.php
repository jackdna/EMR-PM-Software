<script language="Javascript" type="text/javascript">
	//<![CDATA[
	var Success;
	var CurrentImage;
	var Pages = new Array();
	var ImageCount = 0;
	function enable_duplex()
	{
		if (document.form1.duplex.checked)
			csxi.TwainDuplexEnabled  = true;
    else
    	csxi.TwainDuplexEnabled  = false;
  }
	
	function enable_multiscan()
	{
		if (document.form1.ADF.checked)
  	{
			csxi.TwainMultiImage = true;
			csxi.UseADF = true;
			csxi.twainimagestoread = no_of_scans;
		}
		else
		{
			csxi.TwainMultiImage = false;
			csxi.UseADF = false;
			csxi.twainimagestoread = 1;
		}
	}
	
	function SelectClick()
	{		
		document.getElementById("uploadbutton").disabled = false;
		if(ImageCount< no_of_scans)
		{
			csxi.SelectTwainDevice();
			if (csxi.TwainConnected)
			{	//alert(csxi.TwainDuplexEnabled );
				ScanClick();
			}
		}
		else
		{
			top.fAlert("Only "+no_of_scans+" scan(s) allowed here.");
			document.form1.scanbutton.disabled = true
			document.form1.selectbutton.disabled = true
		}
	}
	
	function ScanClick()
	{	
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
	
	function upload(f){
		document.getElementById("uploadbutton").disabled = true;
		f = f || '';
		$('#uploadMsg').html('Please wait image is uploading......').removeClass('hidden');
		setTimeout(function(){UploadClick(f)},1000);
		
	}
	
	function UploadClick(f)
	{ 
		//Add the URL to the file saving script, including the http:// prefix.
		var j=1;  
		for(var i = 1; i<=ImageCount; i++)
    {
			str = '';
      csxi.ReadBinary(0, Pages[i]);
      j = i;
			if(j<=9) { j='0'+j; }
			Success = csxi.PostImage(upload_scan_url, 'Imedic'+j+'.jpg', 'file[]', 2);
			if(Success)
			{
				if(i<ImageCount)str = ". Please wait uploading next image.... ";
				document.getElementById('uploadMsg').innerHTML = i +' Image(s) Uploaded'+str;				
				if(csxi.PostReturnFile!=""){ $('body').append(csxi.PostReturnFile);}
			}
			else
			{
		 		document.getElementById('uploadMsg').innerHTML = i +' Upload Failed '+upload_scan_url;
			}
		 	setTimeout('',100);
		}
		
		//document.getElementById("uploadbutton").disabled = false;
		ImageCount = 0;
		Pages = new Array();
			
		if(f != '')	f.submit();
	}
	
	function RectClick()
	{
		csxi.MouseSelectRectangle();document.form1.cropbutton.disabled = false;
	}

	function CropClick()
	{	
		csxi.CropToSelection();
		document.form1.cropbutton.disabled = true;
		csxi.Redraw();
		Pages[CurrentImage] = csxi.WriteBinary(0); 
	}
	
	function LeftClick()
	{
		csxi.Rotate(90);
		Pages[CurrentImage] = csxi.WriteBinary(0); 
	}
	
	function RightClick()
	{
		csxi.Rotate(270);
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
			$('#divDupCtrl').show().removeClass('hidden');
			document.form1.pagenumtext.value = "Page " + ImageCount + " of " + ImageCount;
		}
  }
	
	function checkOrientation($_this)
	{
		var $_pid	=	document.getElementById('tld_P');
		var $_lid	=	document.getElementById('tld_L');
		
		var $_cid	=	$_this.getAttribute('data-checkbox-id');
		var obj 	=	document.getElementById($_cid);
		
		$_lid.checked 	=	false;
		$_pid.checked 	=	false;
		obj.checked 		=	true;
		
		if(document.frm1 && document.frm1.pageTLD)
			document.frm1.pageTLD.value = obj.value;
	}
//]]>
</script>

<div class="container-fluid">
	<form name="form1" action="">
  	
    <div class="row mt20">
    
    	<div class=" col-xs-12">
      	<div class="row">
        	
          <div class="btn-group">
          	<!-- Select Device Button -->	
          	<input type="button" name="selectbutton" id="selectbutton" value="Select Device" onClick="SelectClick()" class="btn btn-primary"  />	
            <!-- Scan Image Button -->
            <input type="button" name="scanbutton" id="scanbutton" value="Scan Image" onClick="ScanClick()"    class="btn btn-info" />
            <!-- Select Area Button -->
            <input type="button" name="rectbutton" id="rectbutton" value="Select Area" onClick="RectClick()" class="btn btn-success" />
            <!-- Crop Area Button -->
            <input type="button" name="cropbutton" id="cropbutton" value="Crop" onClick="CropClick()"  class="btn btn-danger" />
          	<!-- Roatate Left Button -->
            <input type="button" name="leftbutton" id="leftbutton" value="Rotate Left" onClick="LeftClick()" class="btn btn-warning"/>
            <!-- Roatate Right Button -->
            <input type="button" name="rightbutton" id="rightbutton" value="Rotate Right" onClick="RightClick()"   class="btn btn-primary"/>
            <!-- Upload Image Button -->
            <input type="button" name="uploadbutton" id="uploadbutton" value="Upload Image" onClick="uploadScanCntrlChk()"   class="btn btn-success"/>
          </div>
          
          <div class="mt5" id="divChkOrientation">
          	<label>Orientation : </label>
            <div class="checkbox checkbox-inline mt0" data-checkbox-id="tld_P" onclick="checkOrientation(this);">
            	<input type="checkbox" name="tld" value="P" id="tld_P" checked/>
            	<label for="tld_P" >Portrait</label>
            </div>
            
            <div class="checkbox checkbox-inline mt0" data-checkbox-id="tld_L" onclick="checkOrientation(this);">
            	<input type="checkbox" name="tld" value="L" id="tld_L"/>
            	<label for="tld_L">Landscape</label>
            </div>
         </div>
          
      	</div>
      </div>
      
      <div class="clearfix"></div>
      
      <div class="col-xs-12 col-sm-6 hidden" id="divChkDuplex">
      	<div class="checkbox">
        	<input type="checkbox" name="duplex" id="duplex" value="1" onclick="csxi.twainduplexenabled = true;"/>
          <label for="duplex" >Use Duplex function of scanner, if available.</label>
        </div>
      </div>
      
      <div class="clearfix visible-xs"></div>
      
      <div class="col-xs-12 col-sm-6 hidden" id="divChkMulti">
      	<div class="checkbox">
        	<input type="checkbox" name="ADF" id="ADF" value="1" onclick="enable_multiscan();"/>
					<label for="ADF">Use Multi Scanning of scanner, if available.</label>
        </div>
      </div>
      
    </div>
    
    <div class="clearfix"></div>
    
    <div class="row">
    	<div class="col-xs-12 hidden" id="divDupCtrl">
      
      	<div class="row">
		  <div class="col-xs-4 col-lg-3">
					<div class="col-xs-7 col-lg-6">
						<div class="input-group-btn">
							<input type="button" name="prevbutton" value="Prev." onclick="PrevClick()" class="btn btn-primary"/>
							<input type="button" name="nextbutton" value="Next" onclick="NextClick()" class="btn btn-primary"/>
						</div>	
					</div>
        
					<div class="col-xs-5 col-lg-5">
						<input type="text" name="pagenumtext" value="" readonly class="form-control" style=" height:26px !important;"/>
					</div> 
					</div>
        </div>  
  		</div>
    </div>
    
    <div class="clearfix"></div>
    
    <div id="uploadMsg" class="alert alert-info hidden pd5">Please wait image is uploading......</div>
    
    <div class="clearfix"></div>
    
	</form>
  
  <div class="panel panel-default mt5">
    <div class="panel-body pd5">
  		
      <!-- This first object tag tells the browser where to find the licence information needed to allow csXImage to run.  
			The file csximage.lpk should be copied to the web server in the same directory as the .ocx file.  -->
	
      <object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331">
        <param name="LPKPath" value="<?php echo $GLOBALS['webroot'];?>/library/scan/csximage.lpk" />
      </object>

			<!-- A second object tag identifies the csXImage control itself and allows the size of the control as displayed in the browser to be set.  -->

			<object id="csxi" classid="clsid:62e57fc5-1ccd-11d7-8344-00c1261173f0" codebase="<?php echo $GLOBALS['webroot'];?>/library/scan/csXImage.cab" width="100%" height="300">

  
  		</object>
  	</div>
 	</div>
     
</div>
<script>
	var autoScan = autoScan || 'yes';
	var autoAcquire = autoAcquire || 'yes';
	var multiScan = multiScan || 'no';
	var duplexScan = duplexScan || 'no';
	var no_of_scans = no_of_scans || 1;	
	var scan_doc_upload_btn = scan_doc_upload_btn || '';
	var quickScan = quickScan || '';
	
	function uploadScanCntrlChk() {
		if( scan_doc_upload_btn == 'yes' ) {
			if( quickScan){
				if( top.save_docs )	
					top.save_docs('pdf');
			}
			else {
				if(top.fmain.ifrm_FolderContent){
					if(top.fmain.ifrm_FolderContent.save_pdf_jpg) {
						top.fmain.ifrm_FolderContent.save_pdf_jpg('pdf');
					}
				} 
			}

		} else {
			upload();
		}
	}
	function scn_cntrl_main(){
		
		document.form1.selectbutton.disabled = false
		document.form1.scanbutton.disabled = true
		document.form1.uploadbutton.disabled = true
		document.form1.rectbutton.disabled = true
		document.form1.cropbutton.disabled = true
		document.form1.leftbutton.disabled = true
		document.form1.rightbutton.disabled = true
		if(multiScan == 'yes')
		{
			document.getElementById("ADF").checked = true;
			$("#divChkMulti").removeClass('hidden');
		}
		enable_duplex();
		enable_multiscan();
		
		if(duplexScan == 'yes')	$("#divChkDuplex").removeClass('hidden');
		
		if(autoScan == "yes") { SelectClick(); }
	}
	
	<?php
		if(isset($z_uploadScanURL)&&!empty($z_uploadScanURL)){			
			echo  "var Button,ShiftState,X,Y; var upload_scan_url=\"".$z_uploadScanURL."\";   ";
		}else{
	?>
	
	window.onload = function() {	scn_cntrl_main(); }
	
	<?php
		}
	?>
	
</script>
<?php
	if(!isset($z_uploadScanURL)||empty($z_uploadScanURL)){		
?>
<script language="JavaScript" for="csxi" event="onAcquire(Button, ShiftState, X, Y);" type="text/javascript">
csxiacquire(Button, ShiftState, X, Y);
</script>
<?php } ?>