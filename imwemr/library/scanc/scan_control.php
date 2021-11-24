<script language="Javascript" type="text/javascript">
	//<![CDATA[
	var Success;
	var CurrentImage;
	var ImageCount = 0;
	var _iLeft, _iTop, _iRight, _iBottom; //These variables are used to remember the selected area
	
	function enable_duplex()
	{
		if (document.form1.duplex.checked)
			DWObject.IfDuplexEnabled  = true;
    else
    	DWObject.IfDuplexEnabled  = false;
  }
	
	function enable_multiscan()
	{
		if (document.form1.ADF.checked)
  	{
			DWObject.IfFeederEnabled = true
			DWObject.IfAutoFeed = true;
			DWObject.XferCount = no_of_scans;
		}
		else
		{
			DWObject.IfFeederEnabled = false;
			DWObject.IfAutoFeed = false;
			DWObject.XferCount= 1;
		}
		//console.log(DWObject.IfFeederEnabled);console.log(DWObject.IfFeederLoaded);console.log(DWObject.IfAutoFeed);
	}
	
	function SelectClick()
	{	
		if( DWObject ) {
			document.getElementById("uploadbutton").disabled = false;
			if(DWObject.HowManyImagesInBuffer < no_of_scans)
			{
				DWObject.SelectSource(function () {
					if( DWObject.OpenSource() ) {
						enable_duplex(); enable_multiscan();
						//if( !checkErrorString() )
						{
							DWObject.IfDisableSourceAfterAcquire = true;
							ScanClick();
						}
					}
				});
			}
			else
			{
				if( top.fAlert ) top.fAlert("Only "+no_of_scans+" scan(s) allowed here.");
				else alert("Only "+no_of_scans+" scan(s) allowed here.");
				document.form1.scanbutton.disabled = true
				document.form1.selectbutton.disabled = true
			}
		}
	}
	
	function ScanClick()
	{	
		document.form1.scanbutton.disabled = false
		
		var OnAcquireImageSuccess, OnAcquireImageFailure;
		OnAcquireImageSuccess = function () {
			document.form1.uploadbutton.disabled = false
      document.form1.rectbutton.disabled = false
			document.form1.cropbutton.disabled = false;
      document.form1.leftbutton.disabled = false
      document.form1.rightbutton.disabled = false
		}
			
		OnAcquireImageFailure = function () {
			
			document.form1.uploadbutton.disabled = true
      document.form1.rectbutton.disabled = true
      document.form1.cropbutton.disabled = true
      document.form1.leftbutton.disabled = true
      document.form1.rightbutton.disabled = true
			
			//DWObject.CloseSource();
		};
		
		DWObject.AcquireImage(OnAcquireImageSuccess, OnAcquireImageFailure);
		
	}
	
	function upload(f,callback1,callback2){

		if( pageType == 'sdoc' && ImageCount <=0 ){
			return 'No image found';
		}
		
		document.getElementById("uploadbutton").disabled = true;
		f = f || '';
		callback1 = callback1 || '';
		callback2 = callback2 || '';
		
		$('#uploadMsg').html('Please wait image is uploading......').removeClass('hidden');
		
		UploadClick(f,callback1,callback2);
		
	}
	
	function UploadClick(f,callback1,callback2)
	{ 
		if (!checkIfImagesInBuffer()) {
        return;
    }
		
		//Add the URL to the file saving script, including the http:// prefix.
		var j=1;  
		
		// Creating DOM object for Upload URL
		var a = document.createElement('a');
		a.style.display="none";
		a.href = upload_scan_url;
		
		//Get HTTP HOST and PAth to action file 
		var strHTTPServer = a.protocol + '//'+ a.hostname;
		var strActionPage = a.pathname + a.search;
		// 1 - to save as JPG
		var strImageType = 1; 
		
		DWObject.IfSSL = Dynamsoft.Lib.detect.ssl;
		var _strPort = a.port == "" ? 80 : a.port;
    if (Dynamsoft.Lib.detect.ssl == true)
        _strPort = a.port == "" ? 443 : a.port;
    DWObject.HTTPPort = _strPort;
		
		for(var i=0,j=1; i<ImageCount; i++,j++)
    {
			str = '';
     	if(j<=9) { j='0'+j; }
			
			var uploadImage = true;
			
			var pbd = DWObject.GetImageBitDepth(i)
			if( pbd == 1 ) {
				var changeBitDepth = DWObject.ChangeBitDepth(i, 8, true);
				uploadImage = (changeBitDepth) ? uploadImage : false;
			}
			
			if( uploadImage ) {
				var uploadfilename = 'Imedic'+j+'.jpg';
				var u = DWObject.HTTPUploadThroughPostEx(strHTTPServer,i, strActionPage, uploadfilename, strImageType);
				//var u = DWObject.HTTPUpload(strHTTPServer,[i],strImageType,0,uploadfilename);
				//console.log(u);
				if( u ) {
					if(j < ImageCount)str = ". Please wait uploading next image.... ";
					document.getElementById('uploadMsg').innerHTML = j +' Image(s) Uploaded'+str;
					if(DWObject.__HTTPPostResponseString){$("body").append(DWObject.__HTTPPostResponseString);}	
				}
				else {
					document.getElementById('uploadMsg').innerHTML = j +' Upload Failed '+upload_scan_url;
				}

				setTimeout('',100);
			}
		}
		
		//if( callback1) eval(callback1);
		//if( callback2) eval(callback2);
		
		document.getElementById("uploadbutton").disabled = false;
		ImageCount = 0;
		
		if(f != '')	f.submit();
	}
	
	function RectClick()
	{
		document.form1.cropbutton.disabled = false;
	}

	function CropClick()
	{	
		 if (!checkIfImagesInBuffer()) {
        return;
    }
    if (_iLeft != 0 || _iTop != 0 || _iRight != 0 || _iBottom != 0) {
			
				DWObject.Crop(
            DWObject.CurrentImageIndexInBuffer,
            _iLeft, _iTop, _iRight, _iBottom
        );
        _iLeft = 0;
        _iTop = 0;
        _iRight = 0;
        _iBottom = 0;
        
        if (checkErrorString()) {
            return;
        }
        return;
    }
		
		//document.form1.cropbutton.disabled = true;
		
	}
	
	function LeftClick()
	{
		if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RotateLeft(DWObject.CurrentImageIndexInBuffer);
	}
	
	function RightClick()
	{
		if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RotateRight(DWObject.CurrentImageIndexInBuffer);
  }
	
	function PrevClick()
  {
		
		if (!checkIfImagesInBuffer()) {
        return;
    }
    else if (DWObject.CurrentImageIndexInBuffer == 0) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = DWObject.CurrentImageIndexInBuffer - 1;
		
		CurrentImage = DWObject.CurrentImageIndexInBuffer + 1;
    document.form1.pagenumtext.value = "Page " + CurrentImage + " of " + DWObject.HowManyImagesInBuffer; 
		
	}
  
	function NextClick()
  {
		if (!checkIfImagesInBuffer()) {
        return;
    }
    else if (DWObject.CurrentImageIndexInBuffer == DWObject.HowManyImagesInBuffer - 1) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = DWObject.CurrentImageIndexInBuffer + 1;
		CurrentImage = DWObject.CurrentImageIndexInBuffer + 1;
    document.form1.pagenumtext.value = "Page " + CurrentImage + " of " + DWObject.HowManyImagesInBuffer; 
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
	
	function checkIfImagesInBuffer() {
    if (DWObject.HowManyImagesInBuffer == 0) {
			$("#uploadMsg").html("<b>There is no image in buffer</b>").removeClass('hidden');
        //appendMessage("There is no image in buffer.<br />")
        return false;
    }
    else
        return true;
	}
	
	
	function updatePageInfo() {
		
		ImageCount = DWObject.HowManyImagesInBuffer;
		
		if(DWObject.HowManyImagesInBuffer >1){ 
			$('#divDupCtrl').show().removeClass('hidden');
			CurrentImage = DWObject.CurrentImageIndexInBuffer + 1;
			document.form1.pagenumtext.value = "Page " + CurrentImage + " of " + DWObject.HowManyImagesInBuffer; 
		}
		
  }
	
	//--------------------------------------------------------------------------------------
	//************************** Dynamic Web TWAIN Events***********************************
	//--------------------------------------------------------------------------------------

	function Dynamsoft_OnPostTransfer() {
			updatePageInfo();
	}

	function Dynamsoft_OnPostLoadfunction(path, name, type) {
			updatePageInfo();
	}

	function Dynamsoft_OnPostAllTransfers() {
		DWObject.CloseSource();
		updatePageInfo();
		checkErrorString();
	}

	function Dynamsoft_OnMouseClick(index) {
			updatePageInfo();
	}

	function Dynamsoft_OnMouseRightClick(index) {
			// To add
	}


	function Dynamsoft_OnImageAreaSelected(index, left, top, right, bottom) {
			_iLeft = left;
			_iTop = top;
			_iRight = right;
			_iBottom = bottom;
	}

	function Dynamsoft_OnImageAreaDeselected(index) {
			_iLeft = 0;
			_iTop = 0;
			_iRight = 0;
			_iBottom = 0;
	}

	function Dynamsoft_OnMouseDoubleClick() {
			return;
	}


	function Dynamsoft_OnTopImageInTheViewChanged(index) {
			_iLeft = 0;
			_iTop = 0;
			_iRight = 0;
			_iBottom = 0;
			DWObject.CurrentImageIndexInBuffer = index;
			updatePageInfo();
	}

	function Dynamsoft_OnGetFilePath(bSave, count, index, path, name) {	}
	
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
        	<input type="checkbox" name="duplex" id="duplex" value="1" onChange="enable_duplex();"/>
          <label for="duplex" >Use Duplex function of scanner, if available.</label>
        </div>
      </div>
      
      <div class="clearfix visible-xs"></div>
      
      <div class="col-xs-12 col-sm-6 hidden" id="divChkMulti">
      	<div class="checkbox">
        	<input type="checkbox" name="ADF" id="ADF" value="1" onChange="enable_multiscan();"/>
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
  
  <div class="panel panel-default mt5" style="border:solid 0px; ">
    <div class="panel-body pd5" id="dwtcontrolContainer">
  		
    </div>
 	</div>
     
</div>

<script src="<?php echo $GLOBALS['webroot'];?>/library/scanc/Resources/dynamsoft.webtwain.config.js?t="+Math.floor(Math.random() * (100 - 10 + 1)) + min></script> 
<script src="<?php echo $GLOBALS['webroot'];?>/library/scanc/Resources/dynamsoft.webtwain.initiate.js?t="+Math.floor(Math.random() * (100 - 10 + 1)) + min></script> 

<script>
	var autoLoad = autoLoad || true;
	var autoScan = autoScan || 'yes';
	var autoAcquire = autoAcquire || 'yes';
	var multiScan = multiScan || 'no';
	var duplexScan = duplexScan || 'no';
	var no_of_scans = no_of_scans || 1;	
	var scan_doc_upload_btn = scan_doc_upload_btn || '';
	var quickScan = quickScan || '';
	var pageType = pageType || '';
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
		
		if(multiScan == 'yes') {
			document.getElementById("ADF").checked = true;
			$("#divChkMulti").removeClass('hidden');
		}
		
		if(duplexScan == 'yes')	{
			document.getElementById("duplex").checked = true;
			$("#divChkDuplex").removeClass('hidden');
		}
		
		if(autoScan == "yes") { SelectClick(); }
	}
	
	<?php
		if(isset($z_uploadScanURL)&&!empty($z_uploadScanURL)){			
			echo  "var Button,ShiftState,X,Y; var upload_scan_url=\"".$z_uploadScanURL."\";   ";
		}else{
	?>
			//window.onload = function() {	scn_cntrl_main(); }
	<?php
		}
	?>
	
</script>
<?php
	//if(!isset($z_uploadScanURL)||empty($z_uploadScanURL))
	{		
?>
<script language="JavaScript">
	// Initialize Twain on load
	var DWObject;
	
	Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', Dynamsoft_OnReady);
	
	function Dynamsoft_OnReady(){
			if( autoLoad ) {
				DWObject = Dynamsoft.WebTwainEnv.GetWebTwain('dwtcontrolContainer');

				scn_cntrl_main();

				if (DWObject.ErrorCode == 0) {

					DWObject.LogLevel = 0;
					DWObject.IfAllowLocalCache = true;
					DWObject.ImageCaptureDriverType = 4;
					DWObject.MaxImagesInBuffer = no_of_scans;
					DWObject.HttpFieldNameOfUploadedImage = 'file[]';
					DWObject.BufferMemoryLimit = 2000;



					DWObject.RegisterEvent("OnTopImageInTheViewChanged", Dynamsoft_OnTopImageInTheViewChanged);
					DWObject.RegisterEvent("OnMouseClick", Dynamsoft_OnMouseClick); 

					_iLeft = 0;
					_iTop = 0;
					_iRight = 0;
					_iBottom = 0;

					DWObject.RegisterEvent("OnPostTransfer", Dynamsoft_OnPostTransfer);
					DWObject.RegisterEvent("OnPostLoad", Dynamsoft_OnPostLoadfunction);
					DWObject.RegisterEvent("OnPostAllTransfers", Dynamsoft_OnPostAllTransfers);           
					DWObject.RegisterEvent("OnImageAreaSelected", Dynamsoft_OnImageAreaSelected);
					DWObject.RegisterEvent("OnImageAreaDeSelected", Dynamsoft_OnImageAreaDeselected);
					DWObject.RegisterEvent("OnGetFilePath", Dynamsoft_OnGetFilePath);
				}
			}
	}
	
	function checkErrorString() {
    return checkErrorStringWithErrorCode(DWObject.ErrorCode, DWObject.ErrorString);
	}

	function checkErrorStringWithErrorCode(errorCode, errorString, responseString) {
    if (errorCode == 0) {
			//$("#uploadMsg").html("<b>" + errorString + "</b>").removeClass('hidden');
     	//appendMessage("<span style='color:#cE5E04'><b>" + errorString + "</b></span><br />");
			return true;
    }
    if (errorCode == -2115) //Cancel file dialog
        return true;
    else {
        if (errorCode == -2003) {
					
					Dynamsoft.WebTwainEnv.ShowDialog(400,200,"<b>ErrorMessage: </b>"+responseString);
          //var ErrorMessageWin = window.open("", "ErrorMessage", "height=500,width=750,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no");
          //ErrorMessageWin.document.writeln(responseString); //DWObject.HTTPPostResponseString);
        }
				$("#uploadMsg").html("<b>" + errorString + "</b>").removeClass('hidden');
        //appendMessage("<span style='color:#cE5E04'><b>" + errorString + "</b></span><br />");
        return false;
    }
}
	
	function LoadControl() {
  	//Load the control manually
		Dynamsoft.WebTwainEnv.Load(); 
	}
	function UnloadControl() {
		//Unload the control manually
		Dynamsoft.WebTwainEnv.Unload();
	}
</script>
<?php } ?>