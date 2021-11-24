<?php require_once('../config/config.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Version Release Document</title>
<link rel="stylesheet" type="text/css" href="library/css/custom.css">
<style>
.div_popup{ position:absolute; display:none; z-index:1000; background-color:#CCCCCC; width:auto; overflow:auto; }
.div_popup_heading{ background:#FFCC66 url(../<?=$GLOBALS['WEB_PATH']?>/images/popup_heading.gif) repeat-x; height:15px; padding:5px 5px; border-bottom:1px solid #E6B24A;}
.section{border-top:1px solid #4cb9f8; border-left:1px solid #4cb9f8; border-right:1px solid #4cb9f8; border-bottom:1px solid #4cb9f8; background-color:#c8e5f5;}
.subheading{background-color:#4cb9f8; font-weight:bold; border-top:1px solid #cccccc; border-bottom:1px solid #dddddd; padding:1px 5px; font-size:12px;}
.a_clr1{ color:#9900CC; font-weight:bold; font-size:12px; }
.m2{margin:2px;} .m5{margin:5px;} .m10{margin:10px;} /*--ALL SIDE MARGINS--*/
.bg1{background-color:#ECF1EA;} /* page bgcolor where forms are displayed */
.closeBtn{display:block; float:right; height:16px; width:16px; background:transparent url(<?=$GLOBALS['WEB_PATH']?>/images/del.png) center no-repeat; cursor:pointer;}
.unBold{font-weight:normal;}
.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
.dff_button { padding:5px 20px 5px 20px; background:url(<?=$GLOBALS['WEB_PATH']?>/images/btnbg.png) repeat-x;  border:1px solid #0077B3; cursor:pointer; color:#fff; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; height:31px;}
.dff_button:hover { padding:5px 20px; background:url(<?=$GLOBALS['WEB_PATH']?>/images/btnbghover.png) repeat-x; border:1px solid #0077B3; 
cursor:pointer; color:#fff; border-radius:5px; height:31px; }
table tbody tr td { border:0px !important; }
</style>
</head>
<body>
<div class="section m10 bg1" style="width:800px;">
	<div class="subheading m2">
    	<!--window.close();-->
		<span class="closeBtn" onClick="$('#div_version_release_doc').slideUp('slow');"></span>
		<h3 class="m2" align="center">Version Release Document<br><span class="text13b">Version: <span class="unBold"><?php echo constant('PRODUCT_VERSION');?></span> &nbsp;&nbsp; Date: <span class="unBold"><?php echo constant('PRODUCT_VERSION_DATE');?></span></span></h3>
	</div>
	<div id="release_details" class="m5 white border padd10" style="height:420px; overflow:hidden;">
            <iframe name="ifrm_release_details"	id="ifrm_release_details" src="images/version_release/optical_release_notes.pdf"style="height:420px; width:100%; overflow-x:hidden; overflow-y:auto;"></iframe>	
   </div>
	<div class="m5 alignCenter"><input onClick="$('#div_version_release_doc').slideUp('slow');" type="button" class="dff_button" value="&nbsp;&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;&nbsp;&nbsp;"></div>
</div>
</body>
</html>