<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<!-- Bootstrap -->

<!-- Bootstrap -->
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet" type="text/css">
<!--<link href="../../library/css/font-awesome.css" rel="stylesheet" type="text/css">-->
<!--<link href="../../library/css/style.css" rel="stylesheet" type="text/css">-->
<style>
	h4{padding-left:10px;}
	#dvBg, #dvSm{overflow:auto;border:1px solid lightgrey;}
	#dvDis{padding:4px;}
	#dvupload, #dvmarco{padding-left:20px;}
	.glyphicon-remove{ cursor:pointer; }
</style>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
       <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv/3.7.3/html5shiv.min.js"></script>
       <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>    
<script>

var zPath = "<?php echo $GLOBALS['rootdir'];?>";
var elem_per_vo = "<?php echo $elem_per_vo;?>";
var sess_pt = "<?php echo $patient_id; ?>";
var rootdir = "<?php echo $rootdir;?>";
var finalize_flag = "<?php echo $finalize_flag;?>";
var isReviewable = "<?php echo $isReviewable;?>";
var logged_user_type = "<?php echo $logged_user_type;?>";
var webroot = "<?php echo $GLOBALS['webroot'];?>";
var upload_url = "<?php echo $upload_url;?>";



function funshow(f){

	if(f=='s'||f=='u'||f=='m'){
		f= "&op="+f;
	}else{
		f="";
	}

	location=zPath+"/chart_notes/onload_wv.php?elem_action=ScarAR"+f;		
}

function showBgImg(str){
	var o = document.getElementById("dvBg");
	if(str.toUpperCase().indexOf(".PDF")!= -1){
		o.innerHTML = "<div class=\"embed-responsive embed-responsive-16by9\"><iframe class=\"embed-responsive-item\" src=\""+str+"\" ></iframe></div>";
	}else{
		o.innerHTML = "<img src=\""+str+"\" alt=\"image\" class=\"img-responsive\" >";
	}
}


function delmarco(obj){	
	
	if(""+$(obj).parent("div").children(".spAdd").length<1){		
		$(obj).parent("div").remove();	
	}else{		
		$(obj).parent("div").children("input[type]").val("");
	}	
}

function admarco(){
	
	var num = $("input[name=fnum]").val();
	var curnum = parseInt(num) + 1;	
	$("input[name=fnum]").val(curnum);	
	$("#dvmarco").append("<div>Select Marco File : <input type=\"file\" name=\"marco"+curnum+"\" ><span class=\"spDel\" onclick=\"delmarco(this)\" title=\"Delete\" >x</span>");
	
}

function uploadfile(){		
	if($.trim($("#dvmarco input[type=file]").val())!=""){
		document.formmarco.submit();	
	}else{
		var m = "<div class=\"alert alert-danger\">"+
				"Select any file to upload."+
				"</div>";
		$("#er_msg").html(m);	
	}
}

function fundel(id){
	var str=(typeof(id)!="undefined") ? ""+id : "";	
	if(str!=""){
		$.get(zPath+"/chart_notes/onload_wv.php?elem_action=ScarAR&strid="+str,function(data){  window.location.reload();  });		
	}
}

//--
$(document).ready(function () {	
	if($("#dvBg").length>0){		
		$("#dvBg, #dvSm").css({ "height":$(window).height() * 0.85+"px"});	
	}
});

</script>
</head>
<body>

<div id="dvscan_ar" class=" container-fluid ">
<div class="whtbox ">

	<div class="row">
		<div class="col-sm-4"><h4><?php echo $op_sec_nm ." > ". $op_name;?></h4></div>		
		<div class="col-sm-8">
			<!-- tabs -->
			<?php if($opType != "mp"){?>
			<ul class="nav nav-pills pull-right">
				<?php if(empty($elem_per_vo) && (empty($finalize_flag) || !empty($isReviewable))){?>
				<li class="<?php echo $op_name=="Marco" ? "active" : "" ;?>"><a href="javascript:void();" onclick="funshow('m')">Marco</a></li>
				<li class="<?php echo $op_name=="Scan" ? "active" : "" ;?>"><a href="javascript:void();" onclick="funshow('s')" >Scan</a></li>
				<li class="<?php echo $op_name=="Upload" ? "active" : "" ;?>"><a href="javascript:void();" onclick="funshow('u')">Upload</a></li>
				<?php } ?>
				<li class="<?php echo $op_name=="Preview" ? "active" : "" ;?>"><a href="javascript:void();" onclick="funshow('p')">Preview</a></li>
			</ul>
			<?php } ?>
			<!-- tabs -->
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-sm-12"><h4></h4></div>	
	</div>
	<?php if($opType == "m" || $opType == "mp"){?>
		<form name="formmarco" action="<?php echo $GLOBALS['webroot'];?>/interface/chart_notes/saveCharts.php?elem_saveForm=uploadScan_AR" method="post" enctype="multipart/form-data">
			<div id="dvmarco">
				<div id="er_msg" class="row">
					
				</div>
				<div class="row">
					<div class="col-sm-7 form-inline">
						<div class="form-group">
						<label for="marco1" class="control-label">Select Marco File :</label> 
						<input type="file" name="marco1" id="marco1" class="form-control" >	
						</div>
					</div>
					<div class="col-sm-2">
					<!--
					<span class="spDel glyphicon glyphicon-remove-sign" onClick="delmarco(this)" title="Delete"></span>
					<span class="spAdd glyphicon glyphicon-plus-sign" onClick="admarco()" title="Add" ></span>
					-->
					</div>
					<div class="col-sm-2">
					
					<input type="button" class="btn btn-success" id="upMarcobtn" value="Upload Marco" onClick="uploadfile()"/>
					
					</div>
				</div>
			</div>
			<input type="hidden" name="fnum" value="1">
			<input type="hidden" name="upType" value="<?php echo $opType;?>">			
		</form>
	<?php } ?>
	
	<?php if($opType == "u"){?>
		<div class="row">
			<div id="dvupload">				
				<!--<iframe name="iframeScanUpload" id="iframeScanUpload" src="<?php echo $scanUploadSrc;?>"  scrolling="yes" class="embed-responsive-item" style="width:100%;height:100%;"> </iframe>-->				
				<?php include($scanUploadSrc);?>				
			</div>
		</div>
	<?php } ?>
	
	<?php 
		if($opType == "s"){
			$browser = browser();
			if ($browser['name'] == "msie"){
				echo "<script>multiScan='yes';no_of_scans=100;upload_scan_url = '".$upload_url."'</script>";
				include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
			}
		}
	?>
	<?php	
	if(!isset($opType) || empty($opType) || $opType == "p"){	
	?>
	
	<div id="dvDis" class="row">
		<div id="dvBg" class="col-sm-9"></div>
		<div id="dvSm" class="col-sm-3"><?php echo $strDiv;?></div>
	</div>	
	
	<?php } ?>	

</div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<!--<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script> -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>


<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>

</body>
</html>