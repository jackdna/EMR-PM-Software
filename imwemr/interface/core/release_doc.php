<?php 
include dirname(__FILE__)."/../../config/globals.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Version Release Document</title>

<link rel="stylesheet" type="text/css" href="../css/custom.css">
<style type="text/css">
.div_popup{ position:absolute; display:none; z-index:1000; background-color:#CCCCCC; width:auto; overflow:auto; }
.div_popup_heading{ background:#FFCC66 url(../../images/popup_heading.gif) repeat-x; height:15px; padding:5px 5px; border-bottom:1px solid #E6B24A;}
.section{border-top:1px solid #C5CFB9; border-left:1px solid #C5CFB9; border-right:1px solid #B4C1A5; border-bottom:1px solid #B4C1A5; background-color:#F4F9EE;}
.subheading{background-color:#1b9e95; font-weight:bold; border-top:1px solid #cccccc; border-bottom:1px solid #dedede; padding:1px 5px; font-size:12px;}
.closeBtn{display:block; float:right; height:16px; width:16px; background:transparent url(../../images/delete.gif) center no-repeat; cursor:pointer;}
.a_clr1{ color:#9900CC; font-weight:bold; font-size:12px; }
.m2{margin:2px;} .m5{margin:5px;} .m10{margin:10px;} /*--ALL SIDE MARGINS--*/
.mt15{margin-top:15px;}.mt10{margin-top:10px;} .mt7{margin-top:7px;} .mt5{margin-top:5px;} .mt2{margin-top:2px;} .mt4{margin-top:4px;} .mt3{margin-top:3px;} /*--TOP MARGINS--*/
.mr20{margin-right:20px;} .mr10{margin-right:10px;} .mr5{margin-right:5px;} .mr3{margin-right:3px;} .mr2{margin-right:2px;} /*--RIGHT MARGINS--*/
.ml20{margin-left:20px;} .ml10{margin-left:10px;} .ml5{margin-left:5px;} .ml3{margin-left:3px;} .ml2{margin-left:2px;} /*--LEFT MARGINS--*/
.mlr2{margin:0px 2px;} .mlr5{margin:0px 5px;} .mlr10{margin:0px 10px;}/*--MARGIN LEFT-RIGHT--*/
.padd2{padding:2px;} .padd5{padding:5px;} .padd10{padding:10px;} .padd0{padding:0px;} /*--ALL SIDE PADDING--*/
.pt4{padding-top:4px} .pt2{padding-top:2px} .pt5{padding-top:5px} .pt7{padding-top:7px} .pt15{padding-top:15px} /*--PADDING TOP--*/
.plr5{padding:0px 5px;} .plr10{padding:0px 10px;}  /*--PADDING LEFT AND RIGHT--*/
.pl10{padding-left:10px} .pl5{padding-left:5px} .pl2{padding-left:2px}  /*--PADDING LEFT--*/
.pr10{padding-right:10px} .pr5{padding-right:5px} .pr2{padding-right:2px}  /*--PADDING RIGHT--*/
.fl{float:left; display:block;} /*--FLOAT LEFT--*/
.fr{float:right; display:block;} /*--FLOAT RIGHT--*/
.border{border:1px solid #cccccc;} /*--ALL SIDE BORDER GRAY--*/
.noborder{border:0px solid #fff; border-style:none;}
.botborder{border-bottom:1px solid #cccccc;} /*--BOTTOM BORDER GRAY--*/
.topborder{border-top:1px solid #cccccc;} /*--top BORDER GRAY--*/
.leftborder{border-left:1px solid #cccccc;} /*--LEFT BORDER GRAY--*/
td.valignTop{ 	vertical-align:top; }
td.valignBottom{ vertical-align:bottom; }
.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
.bg1{background-color:#ECF1EA;} /* page bgcolor where forms are displayed */
.clr1{color:#9900CC;} /*purple text */
p{line-height:1.5; text-align:justify; margin:0px 0px 10px 0px;}
.unBold{font-weight:normal;}
#release_details li{margin-top:0px; margin-left:20px; line-height:1.5;}
#release_details p{margin:0px 5px 10px 0px;}
p ol li{margin:0px 5px 5px 10px;}
.prplcolor{color:#5c2a79;}
.grycolor{color:#58595b;}
.grncolor{color:#329e9c}
.text24{font-size:26px;}
.text22{font-size:22px;}
.text18{font-size:18px;}
.text16{font-size:16px;}
.text14{font-size:14px;}
.text13{font-size:13px;}
.bdrbtm{border-bottom:5px solid #CCC;}
.textItalic{font-style:italic;}
.lnhght{line-height:1.3;}
.pt10{padding-top:10px;}
.white{background-color:#FFFFFF; overflow:hidden;}
.textBold{ font-weight:bold;}
.textWhite{ color:#FFFFFF; }
.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}
/*table tbody tr td { border:0px !important; }*/
</style>
<script>
$(document).ready(function(){
	top.show_loading_image('hide');
	h = $(window).height();
	w = $(window).width();
	h_half = h/2;
	w_half = w/2;
	
	$('#release_details').height(h_half+30);
	$('#release_details').width(w_half+100);
	$('#frm_release').height(h_half+30);
	$('#frm_release').width(w_half+100);

});
</script>
</head>
<body>
<div class="section m10 bg1" >
	<div class="subheading m2">
		<span class="closeBtn" onClick="$('#div_version_release_doc').slideUp('slow');"></span>
		<h3 class="m2 textWhite" align="center">Version Release Document<br><span class="text13b">Version: <span class="unBold"><?php echo constant('PRODUCT_VERSION');?></span><?php echo constant('PRODUCT_VERSION_DATE');?></span></h3>
	</div>
	<div id="release_details" class="m5 white border padd10" style="max-height:450px; overflow-x:hidden; overflow-y:auto;">
		<iframe allowtransparency="true" id="frm_release" src="../../library/images/release_doc.pdf" style="max-height:450px; overflow-x:hidden; overflow-y:auto;"></iframe>
	</div>
	<div class="m5 alignCenter"><input onClick="$('#div_version_release_doc').slideUp('slow');" type="button" class="btn btn-danger" value="&nbsp;&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;&nbsp;&nbsp;"></div>
</div>


</body>
</html>