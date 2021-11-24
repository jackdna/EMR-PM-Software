<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php if($js_inc!=true){ ?>
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
function upload(f){
	if(ImageCount<=0) {
		alert("Please select device/image to scan");
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
				<form name="form1" action="">
					
					<div class="block_wrap_scanner" >
					
						<div class="form_inner_m">
							
							<div class="clearfix margin_adjustment_only"></div>
							
							<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
								<label class="text-left option-label" for="select_Epost"> 
									Select Option Below
								</label>
							</div>
							
							<div class="clearfix margin_adjustment_only"></div>
                            
							<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12">
								<ul class="btns-ul"> 
									<li><button type="button" class="btn-primary btn" name="selectbutton" id="selectbutton" onClick="SelectClick()"> Select Device </button></li>
                                    <li><button type="button" name="scanbutton" id="scanbutton" class="btn-primary btn" onClick="ScanClick()" > Scan Image </button> </li> 
									<li><button type="button" name="uploadbutton" id="uploadbutton" onClick="upload()" class="btn-primary btn"> <b class="fa fa-upload"></b> Upload </button> </li> 
								</ul>
								
								<ul class="btns-ul">
									<li><button type="button" name="rectbutton" id="rectbutton" class="btn-primary btn" onClick="RectClick()" > Select Area </button> </li>
									<li><button type="button" name="cropbutton" id="cropbutton" onClick="CropClick()" class="btn-primary btn"> <b class="fa fa-crop"></b> Crop  </button></li> 
									<li><button type="button" name="leftbutton" id="leftbutton" class="btn-default btn" onClick="LeftClick()"> <b class="fa fa-rotate-left"></b> Rotate Left </button></li> 
									<li><button type="button" name="rightbutton" id="rightbutton" onClick="RightClick()"   class="btn-default btn"> <b class="fa fa-rotate-right "></b> Rotate Right</button> </li> 
								</ul>
							</div>
							
							<div class="clearfix hidden-lg hidden-md border-dashed"></div>
							<div class="clearfix hidden-lg margin_adjustment_only"></div>
							
							<div class="col-md-12 col-lg-5 col-xs-12 col-sm-12 scanner_labels">
								<span id="uploadMsg" class="warning text12b" style="display:none;">Please wait image is uploading......</span>
								<label id="divChkDuplex" style="display:none;">
									<input type="checkbox" name="duplex" value="1"  onclick="csxi.twainduplexenabled = true;" /> Use Duplex function of Scanner, if available
								</label>	
								<label id="divChkMulti" style="display:none;">
									<input type="checkbox" name="ADF" value="1" id="ADF" onclick="enable_multiscan();" /> Use  Multi Scanning of Scanner, if available
								</label>
								<div id="divDupCtrl" style="display:none">
								  <button type="button" name="prevbutton" onclick="PrevClick()" class="btn btn-info"><b class="fa fa-arrow-circle-left"></b>&nbsp;Prev.</button>
								  <button type="button" name="nextbutton" onclick="NextClick()" class="btn btn-info">Next&nbsp;<b class="fa fa-arrow-circle-right"></b></button>
								  <button type="text" name="pagenumtext" style=" border:none" readonly="readonly" class="text_small"></button>
								</div>
							</div>
							
							<div class="clearfix border-dashed"></div>
							<div class="clearfix margin_adjustment_only"></div>     
							
						</div><!-- form-inner-m -->
						
					</div><!-- Block Wrap Scanner -->
					
					<div class="clearfix margin_adjustment_only"></div>
					
					<div class="block_wrap_scanner" >
						
						<div class="form_inner_m">
						
							<div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
								<label class="add" for="add_new">Scan Name</label>
							</div>
							
							<div class="col-md-8 col-sm-6 col-xs-12 col-lg-10">
								<input type="text" name="scan_img_name" id="scan_img_name" class="form-control" />
							</div>
						
						</div><!-- form-inner-m -->
						
					</div><!-- Block Wrap Scanner -->	
					
					
				</form>
				
				<div class="clearfix margin_adjustment_only"> </div>
					
					
				<div class="block_wrap_scanner" style="border:dashed 2px #DDD; max-height:350px; overflow:hidden;" >
					
						<object classid="clsid:5220cb21-c88d-11cf-b347-00aa00a28331" style="z-index:0;">
							<param name="LPKPath" value="scan_control/csximage.lpk" />
						</object>
						
						<object id="csxi" classid="clsid:62e57fc5-1ccd-11d7-8344-00c1261173f0" codebase="scan_control/csXImage.cab" width="100%" height="325" ></object>
					
				</div>
				
				

<script>
var autoScan = autoScan || 'yes';
var autoAcquire = autoAcquire || 'yes';
var multiScan = multiScan || 'no';
var duplexScan = duplexScan || 'yes';
var no_of_scans = no_of_scans || 1;
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
		//SelectClick();
	}
	if(document.getElementById("scan_img_name")) {
		document.getElementById("scan_img_name").value='emr'+scanDtm;	
	}
	
	$("#scanButton").click(function(){
		SelectClick();
		
	})
	$("#saveButton").click(function(){
		upload();
	})
}
</script>
<script language="JavaScript" for="csxi" event="onAcquire(Button, ShiftState, X, Y);" type="text/javascript">
csxiacquire(Button, ShiftState, X, Y);
</script>