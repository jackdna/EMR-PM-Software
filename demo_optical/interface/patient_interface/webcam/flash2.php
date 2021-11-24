<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link href="webcam.css?<?php echo constant("cache_version"); ?>" type="text/css" rel="stylesheet"/>
<script src="jquery.js?<?php echo constant("cache_version"); ?>"></script>
<script src="webcam.js?<?php echo constant("cache_version"); ?>" language="javascript"></script>
<script language="JavaScript">
	webcam.set_api_url( 'save.php' );
	webcam.set_quality( 90 ); // JPEG quality (1 - 100)
	webcam.set_shutter_sound( true ); // play shutter click sound
</script>
<script language="JavaScript">
	document.write( webcam.get_html(600, 500) );
</script>
	
<form>
<div class="btn_cls">
<input type="button" name="load" value="Load Image" onClick="take_snapshot();" class="btn_cls">
<input type="button" name="close" value="Close" onClick="window.close();" class="btn_cls">
<input type="button" name="refresh" value="Refresh" onClick="window.location.reload();" class="btn_cls">
</div>
<!--		<input type=button value="Configure..." onClick="webcam.configure()">
		&nbsp;&nbsp;
		<input type=button value="Take Snapshot" onClick="take_snapshot()">
-->	</form>
	
	<!-- Code to handle the server response (see test.php) -->
	<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
		function take_snapshot() {
			// take snapshot and upload to server
			document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
			webcam.snap();
		}
		
		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg.match(/(http\:\/\/\S+)/)) {
				var image_url = RegExp.$1;
				// show JPEG image in page
				document.getElementById('upload_results').innerHTML = 
					'<h1>Upload Successful!</h1>' + 
					'<h3>JPEG URL: ' + image_url + '</h3>' + 
					'<img src="' + image_url + '">';
				
				// reset camera for another shot
				webcam.reset();
			}
			else alert("PHP Error: " + msg);
		}
	</script>
	
	</td><td width=50>&nbsp;</td><td valign=top>
		<div id="upload_results" style="background-color:#eee;"></div>
	</td></tr></table>
