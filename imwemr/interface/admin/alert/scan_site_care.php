<?php
	require_once('../admin_header.php');
	if($phpServerIP != $_SERVER['HTTP_HOST']){
		$phpServerIP = $_SERVER['HTTP_HOST'];
		$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
	}	
?>
	<body>
		<script>
			var upload_url = '<?php echo $GLOBALS['webroot']."/interface/admin/alert/uploadSiteCareScan.php?imwemr=".session_id()."&method=upload&siteCareId=".$_REQUEST['siteCareId'];?>';
			
			var upload_scan_url = '<?php echo $GLOBALS['php_server']."/interface/admin/alert/uploadSiteCareScan.php?method=scan&siteCareId=".$_REQUEST['siteCareId']; ?>';
			var scan_container_height = 350;
		</script>
		<div class="whtbox">
			<div class="tblBg">
				<div class="row" style="height:450px; overflow:scroll; overflow-x:hidden">
				<?php 
					if($_REQUEST['scanOrUpload'] == 'upload'){
						include($GLOBALS['srcdir']."/upload/index.php");
					}else if($_REQUEST['scanOrUpload'] == 'scan'){
						$browser = browser();
						if($browser['name'] == "msie" || $browser['name'] != "chrome"){
							include_once $GLOBALS['srcdir']."/scan/scan_control.php";
						}
						else include_once $GLOBALS['srcdir']."/scanc/scan_control.php";
					}
				?>	
				</div>
			</div>	
            
		</div>
        <div class="row">
            <div class="col-xs-12 text-center mt5">
                <input class="btn btn-success" id="close" type="button" name="close" value="Save & Close" onClick="closeWin();">
            </div>
        </div>
        
		<script>
			function closeWin() {
				<?php if($scanOrUpload == "scan"){?>
				browser = get_browser();
				//if(browser == "ie")
				upload();
				<?php }?>
				setTimeout(unload,1000);
			}
			var xmlHttpPic;
			function unload(){
				ajaxURL = "getSiteCareSessionPic.php?scanOrUpload=<?php echo $scanOrUpload;?>";
				
				$.ajax({
				  url: ajaxURL,
				  type:'POST',
				  success: function(r){
					//window.opener.image_DIV(r,'divSiteCareScanDoc');	
					var divSiteCareName = "divSiteCareScanDoc";
					<?php if($scanOrUpload == "upload"){?>
							divSiteCareName = "divSiteCareUploadDoc";
					<?php } ?>
					window.opener.top.callChildWin(r,divSiteCareName,'<?php echo $_REQUEST['siteCareId'];?>');
					window.close();
					
				  }
				});
			
			
			}
			function setSiteCarePic(){			
				if(xmlHttpPic.readyState == 4){		
					image = xmlHttpPic.responseText							
					window.opener.image_DIV(image,'divSiteCareScanDoc');	
					window.close();
				}
			}
			
			$(window).load(function(){
				window.focus();
				var width = 800;
				var height = 600;
				window.resizeTo(width, height);
				window.moveTo(((screen.width - width) / 2), ((screen.height - height) / 2)); 
			});
		</script>
	</body>
</html>	