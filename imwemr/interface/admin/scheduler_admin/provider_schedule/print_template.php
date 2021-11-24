<?php
require_once(dirname(__FILE__).'/../../../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location
$file_location = write_html($_POST['content']);
	
	if($file_location){
	?>
	<html>
		<body>
        	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
			<script type="text/javascript">
                top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
                top.html_to_pdf('<?php echo $file_location; ?>','p','','','false');
				//window.self.close();
            </script>

			
		</body>
	</html>
	<?php
	}
?>