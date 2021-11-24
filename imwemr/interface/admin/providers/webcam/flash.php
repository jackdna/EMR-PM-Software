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

include_once("../../../../config/globals.php");
$opreator_id = $_SESSION['authId'];
$patient_id = $_SESSION['patient'];	
	
if(isset($_SESSION["Provider_id_img"])){
	unset($_SESSION["Provider_id_img"]);
}
$_SESSION["Provider_id_img"] = $_REQUEST["provider_id"];
?>
<!DOCTYPE html>
<html>
 <head>
	<title>imwemr-Dev</title>
	<!-- Bootstrap -->
	<link href="../../../../library/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="../../../../library/css/common.css" rel="stylesheet" type="text/css">
	<link href="../../../../library/messi/messi.css" rel="stylesheet" type="text/css">
	<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
		<link href="../../../../library/css/imw_css.css" rel="stylesheet">
	<?php } ?>
	<script type="text/javascript" src="../../../../library/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="../../../../library/js/bootstrap.js"></script>
	<script type="text/javascript" src="../../../../library/js/core_main.js"></script>
	<script type="text/javascript" src="../../../../library/messi/messi.js"></script>
	<script type="text/javascript" src="../../../../library/js/common.js"></script>
	<!-- Script -->
	<script type="text/javascript" src="../../../../library/webcam/webcam.min.js"></script>
	<script language="javascript">
		window.focus();
		var WRP = opener.top.JS_WEB_ROOT_PATH;
		window.onload =function()
		{
			var parWidth = (screen.availWidth > 750) ? 750 : screen.availWidth ;
			window.resizeTo(parWidth,640);
			var t = 10;
			var l = parseInt((screen.availWidth - window.outerWidth) / 2)
			window.moveTo(l,t);

			document.getElementById('imw_provider_camera').addEventListener("contextmenu", function(e){
				e.preventDefault();
			}, false);
		}
	
		function btnScan_onclick() { unload(); }
	
		function unload()
		{
			var url = 'getSessionPic.php';
			master_ajax_tunnel(url,set_session_pic);
		}
		function set_session_pic(r)
		{			
			image = r;
			if(image != "")
			{
				if(typeof(window.opener.fmain) != "undefined")
					window.opener.fmain.image_DIV(image,'pro_image');
				else 	
					window.opener.image_DIV(image,'pro_image');
			}
			window.close();
		}
	</script>
	<style>
		#imw_provider_camera{
			width: 320px;
			height: 240px;
			border: 1px solid black;
		}
	</style>
</head>
<body class="body_c" onUnload="unload();">
 	<form action="flash.php" method="post" name="webupload" id="webupload">
		<input type="hidden" name="formName" value="<?php echo $form_name; ?>">
      	<input type="hidden" name="show" value="<?php echo $show; ?>">
      	<input type="hidden" name="formId" value="<?php echo $form_id; ?>">
      	<input type="hidden" name="elem_delete" value="">
      	<input type="hidden" name="testId" value="<?php echo $testId;?>">
		
		  <div id="upload_image" class="modal" style="display:block;" >
        	<div class="modal-dialog" style="width:100%; margin:0;">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<h4 class="modal-title" id="modal_title">Provider Image</h4>
					</div>
				
					<div class="modal-body" id="flashArea" style="min-height:350px;">
						<div class="row">
							<div class="col-xs-12"><div class="row">
								<div class="col-xs-6 pd10"><div id="imw_provider_camera" ></div></div>
								<div class="col-xs-6 pd10"><div id="results"   ></div></div>
							</div></div>
						</div>
					</div>
				
					<div class="modal-footer ad_modal_footer" id="module_buttons">
						<button type="button" class="btn btn-success" onClick="take_snapshot()" >Take Snapshot </button>
						<button type="button" class="btn btn-success" onClick="saveSnap()" >Save & Close </button>
						<button type="button" id="btnScan" class="btn btn-danger" onclick='btnScan_onclick()'>Close</button>
					</div>
				
				</div>
       		</div>
      	</div>
    </form>
	<!-- Code to handle taking the snapshot and displaying it locally -->
	<script language="javaScript">
		var path = '../../../../library/webcam/';
		// Configure a few settings and attach camera
		function configure(){
			Webcam.set({
				width: 320,
				height: 240,
				image_format: 'jpeg',
				jpeg_quality: 90,
			});
			Webcam.attach( '#imw_provider_camera' );
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
</body>
</html>