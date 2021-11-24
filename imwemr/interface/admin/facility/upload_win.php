<?php 
	include_once("../admin_header.php");
	$library_path = $GLOBALS['webroot'].'/library';
?>
		<script>
			//var global_js_vars = JSON.parse('<?php echo $global_js_array; ?>');
			var upload_url = '<?php echo $GLOBALS['webroot']."/interface/admin/facility/ajax.php?imwemr=".session_id()."&upType=".$opType ?>';
			function submit12(){
				browser = get_browser();
				if(browser == "ie")
				upload(document.compareFrm)
				else if(browser == "chrome")
				document.compareFrm.submit();
				window.close();
			}
			function frm_submit(){
				document.uploadFrm.submit();
			}
			function resize_window() 
			{ 
				var parWidth = (screen.availWidth > 900) ? 900 : screen.availWidth ;
				var parHeight = 620;//(browser_name == 'msie') ? 640 : 670;
				window.resizeTo(parWidth,parHeight);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2);
				window.moveTo(l,t);
			}
			function close_window(){
				window.close();
			}
			resize_window();
		</script>		
        <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/tests.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js"></script> 
		<script src="<?php echo $library_path; ?>/js/admin/admin_facility.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="panel panel-primary">
			<div class="panel-heading">Facility logo</div>
			<div class="panel-body" style="height:400px !important;">
                <div class="row">
                    <div class="col-xs-1"></div>
                    <div class="col-xs-10">
                        <div class="clearfix">&nbsp;</div>
                        <div class="row">
                            <div class="col-xs-12"><?php include $GLOBALS['srcdir'].'/upload/index.php'; ?></div>   	
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-xs-1"></div>
                </div>
	     	</div>
	     	<div class="panel-footer" style="height:110px;border-top:0px;">
	     		<form name="compareFrm" method="post" onSubmit="return chkForm();">
	     			<input type="hidden" name="formName" value="<?php echo $formName; ?>">
                   <input type="hidden" name="show" value="<?php echo $show; ?>">
                   <input type="hidden" name="formId" value="<?php echo $form_id; ?>">
                   <input type="hidden" name="elem_delete" value="">
                   <input type="hidden" name="testId" value="<?php echo $testId;?>">
                    <div class="clearfix"></div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success" id="Close" onClick="submit12();">Save &amp; Close</button>
                    </div>
	     		</form>
	     	</div>
		</div>
	</body>
	<!-- <body>
		<div class="mainwhtbox">
			<div class="row"> 
				<div class="col-sm-12">
					<?php 
						//include($GLOBALS['srcdir']."/upload/index.php");
					?>	
				</div>	
			</div>	
		</div>
	</body>-->
<?php 
	include_once('../admin_footer.php');
?>