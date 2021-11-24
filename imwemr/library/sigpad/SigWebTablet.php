<script type="text/javascript">

	var sigpadAreaWidth = 500;
	var sigpadAreaHeight = 100;
	var cnum = ''; var canvasFld = ''; var hidCanvasFld = '';
	var pnum = ''; var pCanvasFld = ''; var pHidCanvasFld = '';
	
	function ext_installed(){
		var isInstalled = document.documentElement.getAttribute('SigPlusExtLiteExtension-installed');  
		if (!isInstalled) {
			if( top.fAlert) top.fAlert("Error: SigPlusExtLite extension is either not installed or disabled.<br><br><a href='https://www.topazsystems.com/software/SigPlusExtLite_UserInstall.pdf' target=\"_BLANK\"><b>Click here</b></a> for Download Links & Installation Guide ");
			else alert("Error: SigPlusExtLite extension is either not installed or disabled.\n\nhttps://www.topazsystems.com/software/SigPlusExtLite_UserInstall.pdf\n\nVisit above link for Download Links & Installation Guide ");
			return;
		}
		return true;
	}
	
	function OnSign(num,cvsFld,hidCvsFld) 
	{   
		try {
			
			if( ext_installed() ) 
			{
				
				if( cnum ) pnum = cnum;
				if( canvasFld ) pCanvasFld = canvasFld;
				if( hidCanvasFld ) pHidCanvasFld = hidCanvasFld;
				
				cnum = num || 1;	
				canvasFld = cvsFld || 'SigPlus1';
				hidCanvasFld = hidCvsFld || 'SigData1';
				//console.log(cnum); console.log(canvasFld);console.log(hidCanvasFld);
	  		
				var canvasObj = document.getElementById(canvasFld);
				//canvasObj.getContext('2d').clearRect(0, 0, canvasObj.width, canvasObj.height);
				//document.getElementById(hidCanvasFld).value = '';
				
				sigpadAreaWidth = canvasObj.width;
				sigpadAreaHeight = canvasObj.height;
				
				var message = { "firstName": "", "lastName": "", "eMail": "", "location": "", "imageFormat": 1, "imageX": sigpadAreaWidth, "imageY": sigpadAreaHeight, "imageTransparency": false, "imageScaling": false, "maxUpScalePercent": 0.0, "rawDataFormat": "ENC", "minSigPoints": 25 };
			
				top.document.addEventListener('SignResponse', SignResponse, false);
				
				var messageData = JSON.stringify(message);
				
				var element = document.createElement("MyExtensionDataElement");
				element.setAttribute("messageAttribute", messageData);
				document.documentElement.appendChild(element);
				
				var evt = document.createEvent("Events");
				evt.initEvent("SignStartEvent", true, false);				
				element.dispatchEvent(evt);
			} 
		}
		catch (Exception) {
			console.log('Exception: '+ Exception);
		}
		
  }
	
	function SignResponse(event)
	{
		try {
			var str = event.target.getAttribute("msgAttribute");
			var obj = JSON.parse(str);
			SetValues(obj, sigpadAreaWidth, sigpadAreaHeight);
		}
		catch (Exception) {
			console.log('Exception: '+ Exception);
		}	
	}
	
	function SetValues(objResponse, imageWidth, imageHeight)
	{
		try { 
     	var obj = null;
			if(typeof(objResponse) === 'string'){
				obj = JSON.parse(objResponse);
			} else{
				obj = JSON.parse(JSON.stringify(objResponse));
			}		
		
	    var ctx = document.getElementById(canvasFld).getContext('2d');
			
			if (obj.errorMsg != null && obj.errorMsg!="" && obj.errorMsg!="undefined") {
				
				if( top.fAlert ) {
					top.fAlert(obj.errorMsg);
				} else alert(obj.errorMsg);
				
				if( obj.errorMsg == 'User cancelled signing.') {
					clearDefaultVal();
				}
				if( obj.errorMsg == 'SigCapture failed to start, another instance already running.'){
					if( pnum ) cnum = pnum ;
					if( pCanvasFld ) canvasFld = pCanvasFld ;
					if( pHidCanvasFld ) hidCanvasFld = pHidCanvasFld ;
				}
			}
    	else {
				if (obj.isSigned)
				{
					document.getElementById(hidCanvasFld).value = obj.sigString;
					var img = new Image();
					img.onload = function () 
					{
						ctx.drawImage(img, 0, 0, imageWidth, imageHeight);
					}
					img.src = "data:image/png;base64," + obj.imageData;
					
				}
				clearDefaultVal();
			}
		}
		catch (Exception) {
			console.log('Exception: '+ Exception);
		}
	}
	
	function OnClear(num,cvsFld,hidCvsFld) {
		//console.log('onClear1');console.log(cnum); console.log(canvasFld);console.log(hidCanvasFld);
		if( typeof cnum !=='undefined' && cnum !== '' ) { 
			if( top.fAlert) top.fAlert('SigCapture service is already running. Please complete signature or close signature window.');
			else alert('SigCapture service is already running. Please complete signature or close signature window.');
			return false;
		}
		
		cnum = num || 1;
		canvasFld = cvsFld || 'SigPlus1';
		hidCanvasFld = hidCvsFld || 'SigData1';
		//console.log('onClear');console.log(cnum); console.log(canvasFld);console.log(hidCanvasFld);
		
		var canvasObj = document.getElementById(canvasFld);
		canvasObj.getContext('2d').clearRect(0, 0, canvasObj.width, canvasObj.height);
		
		document.getElementById(hidCanvasFld).value = '';
		
		clearDefaultVal();
	}
	
	function clearDefaultVal(){
		
		cnum = ''; canvasFld = ''; hidCanvasFld = '';
		
	}
	
	function saveSig(){ 
		
		if( typeof cnum !=='undefined' && cnum !== ''  ) {
			if( top.fAlert) top.fAlert('SigCapture service is running. Please complete signature or close signature window.');
			else alert('SigCapture service is running. Please complete signature or close signature window.');
			return false;
		}	
		return true;
	}
</script>	