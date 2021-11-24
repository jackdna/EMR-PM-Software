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
?>
<?php
/*
File: scan_upload_drawing.php
Purpose: This file provides interface to upload files in Drawing.
Access Type : Direct
*/
?>

<script language="javascript">
	function closeWin() {
		<?php if($_REQUEST['scanOrUpload'] == "scan"){?>
			 upload();setTimeout(function(){$("#div_suc_doc").modal('hide');},1000);
		<?php }else{?>
				$("#div_suc_doc").modal('hide');
		<?php }?>
	}
	function preview(examId, formId, scanUploadfor){

		$("#div_suc_doc").modal('hide');
		$("#div_suc_doc, .modal-backdrop").remove();
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
		$.get(zPath+"/chart_notes/onload_wv.php?elem_action=get_scan_upload_preview&examId="+examId+"&formId="+formId+"&scanUploadfor="+scanUploadfor,function(d){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
			$("body").append(d);
			$("#div_suc_doc").modal('show');

			setTimeout(function(){
				var ch = $(window).height();
				ch = parseInt(ch*0.70);
				var ocw = $("#div_suc_doc .modal-body");
				var cw = ocw[0].offsetWidth;
				var cw1 = parseInt(cw*0.75);
				var cw2 = parseInt(cw*0.20);
				$("#divContent").css({"display":"inline-block","height":""+ch+"px", "width":""+cw1+"px","border":"1px solid red","overflow":"auto", "text-align": "center"});
				$("#divThumbs").css({"display":"inline-block","height":""+ch+"px", "width":""+cw2+"px","border":"1px solid green","overflow":"auto", "text-align": "center"});

			},500);
		});
	}
	function doUpload(examId, formId, scanUploadfor,save){

		if(document.getElementById("flUpload").value==""){
			alert("Please select any file to upload.");
			return;
		}

		/**
		document.getElementById("img_load").style.display = "block";
		document.getElementById("frm1").action = "save_scan_upload_drawing.php?method=upload&examId="+examId+"&formId="+formId+"&scanUploadfor="+scanUploadfor+"&canvasId="+<?php echo $_REQUEST['canvasId']; ?>+"&save="+save;
		document.frm1.submit();
		**/

		 // Get form
		var form = $('#frm1')[0];
		// Create an FormData object
		var fdata = new FormData(form);

		// If you want to add an extra field for the FormData
		fdata.append("method", "upload");
		fdata.append("examId", ""+examId);
		fdata.append("formId", ""+formId);
		fdata.append("scanUploadfor", ""+scanUploadfor);
		fdata.append("canvasId", "<?php echo $_REQUEST['canvasId']; ?>");
		fdata.append("save", save);

		// disabled the submit button
		$(".btn").prop("disabled", true);

		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: zPath+"/chart_notes/saveCharts.php",
			data: fdata,
			processData: false,
			contentType: false,
			cache: false,
			timeout: 600000,
			success: function (data) {
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
				//$("#result").text(data);
				//console.log("SUCCESS : ", data);
				$(".btn").prop("disabled", false);
				$("#btPreview").removeClass("hidden");
				$("#flUpload").val("");
				if(data && data!=""){ $("#div_suc_doc").modal('hide');	$("#div_suc_doc, .modal-backdrop").remove(); $("body").append(data);  }

			},
			error: function (e) {
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"");
				//$("#result").text(e.responseText);
				console.log("ERROR : ", e);
				$(".btn").prop("disabled", false);

			}
		});
	}

	//--------

	function setIfamePath(path, obj, path1, dbPath){
		document.getElementById('hidFilePath').value = path1;
		document.getElementById('hidFilePathDB').value = dbPath;
		document.getElementById('iframeContent').src = path;
		//$("div[id*=divInner]").css({ "border":"0px" });
		//$(obj).css({ "border":"2px solid" });
	}
	function deleteDoc(dbScanId){
		if(confirm("Are you sure to delete this Drawing Scan Upload Doc.!")){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"");
			$.get(zPath+"/chart_notes/saveCharts.php?elem_saveForm=rem_scan_upload_doc&delScanDoc="+dbScanId, function(d){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0);
				$("div.prw_"+dbScanId).remove();
			});
		}
	}
	function chooseImg(){
		loadTestImage(document.getElementById('hidFilePath').value, '<?php echo $rqScanUploadFor; ?>', '<?php echo $rqExamId; ?>', document.getElementById('hidFilePathDB').value, '<?php echo $rqCanvasId; ?>');
		$("#div_suc_doc").modal('hide');
	}

	//-------------

	function btnScan_onclick(){
		var url = '<?php echo $GLOBALS['webroot'];?>/interface/patient_info/demographics/webcam/getSessionPic.php';
		$.get(url, function(d){
			image = d;
			if(image != ""){
				//console.log(d);
				var ar = image.split("!~!");
				loadTestImage(ar[1], '<?php echo $rqScanUploadFor;?>', '<?php echo $rqExamId;?>', ar[0], '<?php echo $rqCanvasId;?>');
				$("#div_suc_doc").modal('hide');
				Webcam.reset();
			}
		} );
	}



</script>

<!-- Modal -->
<div id="div_suc_doc" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">
		<?php
			if(isset($zscanOrUpload) && $zscanOrUpload=="Preview"){
				echo "Drawing Scan Upload Preview For:".$dbPatName." ".$dbPatMname;
			}else	if($_REQUEST['scanOrUpload'] == "upload"){
				echo "Upload Image For Drawing";
			}
			else if($_REQUEST['scanOrUpload'] == "scan"){
				echo "Scan Image For Drawing";
			}else if($_REQUEST['scanOrUpload'] == "upload-WEBCAM"){
				echo "Camera Image For Drawing";
			}
		?>
	</h4>
      </div>
      <div class="modal-body">

<?php
	if(isset($zscanOrUpload) && $zscanOrUpload=="Preview"){
?>
	<input type="hidden" id="hidFilePath" name="hidFilePath" value="<?php echo $strPathForCanvasIframe; ?>" />
	<input type="hidden" id="hidFilePathDB" name="hidFilePathDB" value="<?php echo $strDBDocPath; ?>" />
	<div id="mainDiv">
		<div id="divContent" >
			<img id="iframeContent" src="<?php echo $strIframeFilePath;?>" alt="img" >
		</div>
		<div id="divThumbs" >
			<?php echo $strTrFile;?>
		</div>
	</div>

<?php

}//End preview

if($_REQUEST['scanOrUpload'] == "scan"){
?>
                    <!-- SCAN -->
		<div class="row">
			<div class="col-xs-12">
				<?php
					$browser = browser();
					if($browser['name'] == 'msie' )
					{
						$z_uploadScanURL=$GLOBALS['php_server']."/interface/chart_notes/saveCharts.php?elem_saveForm=upload_idoc_drawings&method=scan&examId=".$_REQUEST['examId']."&formId=".$_REQUEST['formId']."&scanUploadfor=".$_REQUEST['scanUploadfor']."&canvasId=".$_REQUEST['canvasId'];
						include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
					}
					else {
						echo "<script>web_root='".$GLOBALS['php_server']."';autoLoad=false;no_of_scans=1;upload_scan_url = '".$GLOBALS['php_server']."/interface/chart_notes/saveCharts.php?elem_saveForm=upload_idoc_drawings&method=scan&examId=".$_REQUEST['examId']."&formId=".$_REQUEST['formId']."&scanUploadfor=".$_REQUEST['scanUploadfor']."&canvasId=".$_REQUEST['canvasId']."';</script>";

						include_once $GLOBALS['fileroot']. "/library/scanc/scan_control.php";
					}
					//else if($browser['name'] == "chrome"){} //COMPATIBILITY
					?>

			</div>
		</div>
                    <!-- SCAN -->
<?php
	}
?>

<?php
if($_REQUEST['scanOrUpload'] == "upload"){
?>
	<form name="frm1" id="frm1" method="post" enctype="multipart/form-data">
	<input type="hidden" name="elem_saveForm" value="upload_idoc_drawings">
	<!-- Upload -->
		<div class="row">
		<div class="form-group">
			<label class="col-sm-5 control-label">Please Upload Image For Drawing:</label>
			<div class="col-sm-7">
				<input class="form-control" type="file" name="flUpload" id="flUpload" >
			</div>
		</div>
		</div>
	<!-- Upload -->
	</form>
<?php
}
?>

<?php
if($_REQUEST['scanOrUpload'] == "upload-WEBCAM"){
?>
 <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/webcam/webcam.min.js"></script>

 <style>
	 #imw_camera{
		 width: 320px;
		 height: 240px;
		 border: 1px solid black;
	 }
 </style>

 <form action="flash.php" method="post" name="webupload" id="webupload">
	 <input type="hidden" name="formName" value="<?php echo $form_name; ?>">
	 <input type="hidden" name="show" value="<?php echo $show; ?>">
	 <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
	 <input type="hidden" name="elem_delete" value="">
	 <input type="hidden" name="testId" value="<?php echo $testId;?>">

	 <div id="flashArea" >
		 	 <div class="row">
				 <div class="col-xs-6 pd10"><div id="imw_camera" ></div></div>
				 <div class="col-xs-6 pd10"><div id="results"></div></div>
			 </div>
	 </div>
 </form>

 <!-- Code to handle taking the snapshot and displaying it locally -->
 <script language="javaScript">
	 var path = '<?php echo $GLOBALS['webroot'];?>/library/webcam/';
	 // Configure a few settings and attach camera
	 function configure(){
		 Webcam.set({
			 width: 320,
			 height: 240,
			 image_format: 'jpeg',
			 jpeg_quality: 90,
		 });
		 Webcam.attach( '#imw_camera' );
	 }
	 // A button for taking snaps
	 configure();

	 // preload shutter audio clip
	 var shutter = new Audio();
	 shutter.autoplay = false;
	 shutter.src = navigator.userAgent.match(/Firefox/) ? path +'shutter.ogg' : path+'shutter.mp3';

	 function take_snapshot() {
		 // play sound effect
		 shutter.play();

		 // take snapshot and get image data
		 Webcam.snap( function(data_uri) {
			 // display results in page
			 document.getElementById('results').innerHTML =
				 '<img id="imageprev" src="'+data_uri+'"/>';
		 } );

	 //	Webcam.reset();
	 }

	 function saveSnap(){

		 if( !document.getElementById("imageprev") ) {
			 top.fAlert('Please take snapshot first.');
			 return false;
		 }
		 // Get base64 value from <img id='imageprev'> source

		 var base64image =  document.getElementById("imageprev").src;

			Webcam.upload( base64image, 'save.php', function(code, text) {
				//console.log( code,text);
				if( code == 200 )
				{
				 btnScan_onclick();
				}
				else{
				 top.fAlert('Error while saving: Please try again !!! ');
				 return false;
				}
					 });

	 }
 </script>

<?php
}
?>

	</div>
      <div class="modal-footer">
	<?php
	if(isset($zscanOrUpload) && $zscanOrUpload=="Preview"){
	?>
		<input type="button" class="btn btn-success <?php if(empty($strTrFile)){ echo " hidden "; }?>" id="btChooseImg"  value="Choose Image" onClick="chooseImg();"/>

	<?php
	}
	?>


	<?php
	if($_REQUEST['scanOrUpload'] == "scan"){
	?>
		<button type="button" class="btn btn-danger" name="btClose" id="btClose" onClick="closeWin();">Save & Close</button>
	<?php
	}
	?>

	<?php
	if($_REQUEST['scanOrUpload'] == "upload-WEBCAM"){
	?>
		<button type="button" class="btn btn-success" onClick="take_snapshot()" >Take Snapshot </button>
		<button type="button" class="btn btn-danger" name="btClose" id="btClose" onClick="saveSnap()">Use Image</button>
	<?php
	}
	?>

	<?php
		if($_REQUEST['scanOrUpload'] == "upload"){
	?>
		<button type="button" class="btn btn-success" name="btUploadSave" id="btUpload" name="btUploadSave" onClick="doUpload('<?php echo $_REQUEST['examId'];?>','<?php echo $_REQUEST['formId'];?>', '<?php echo $_REQUEST['scanUploadfor'];?>',1);" >Upload for All Patients</button>
		<button type="button" class="btn btn-success" name="btUploadPatient" id="btUploadPatient" onClick="doUpload('<?php echo $_REQUEST['examId'];?>','<?php echo $_REQUEST['formId'];?>', '<?php echo $_REQUEST['scanUploadfor'];?>',0);">Upload for This Patient</button>
	<?php
		}
	?>


		<button type="button" class="btn btn-danger <?php  if(empty($flg_show_preview)){ echo " hidden "; } ?>" name="btPreview" id="btPreview" onClick="preview('<?php echo $_REQUEST['examId'];?>','<?php echo $_REQUEST['formId'];?>', '<?php echo $_REQUEST['scanUploadfor'];?>');" >Preview</button>

        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
