<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
//require_once('../common/functions.inc.php');
?>
<html>
<head>
<style>
/*star hover tab*/
.hovertab{margin:0px 0px 0px 2px; padding:0px; float:left;}
.hovertab li.left{list-style:none; display:block; float:left; 
			background:url(images/img_84_left.jpg) repeat-x 0 0 scroll; height:29px; width:5px;}
.hovertab li.center{
	list-style:none; 
	display:block; 
	float:left; 
	background:url(images/img_84_mid.gif) repeat-x 0 0 scroll; 
	height:29px; 
	font-family:Verdana, Geneva, sans-serif; 
	font-size:12px; 
	font-weight:bold; 
	text-align:center;
	width:130px;
	cursor:pointer;
	
	}
.hovertab li.right{list-style:none; display:block; float:left; 
			background:url(images/img_84_right.jpg) repeat-x 0 0 scroll; height:29px; width:5px;}
.hovertab li span{position:relative; top:7px;}


/*star normal tab*/
.normaltab{margin:0px 0px 0px 2px; padding:0px; float:left;}
.normaltab li.left{list-style:none; display:block; float:left; 
			background:url(images/main_navigation_left.jpg) repeat-x 0 0 scroll; height:29px; width:5px;}
.normaltab li.center{
	list-style:none; 
	display:block; 
	float:left; 
	background:url(images/main_navigation_mid.gif) repeat-x 0 0 scroll; 
	width:130px; 
	height:29px; 
	font-family:Verdana, Geneva, sans-serif; 
	font-size:12px; 
	font-weight:bold; 
	text-align:center; 
	color:#FFF;
	cursor:pointer;
		
	}
.normaltab li.right{list-style:none; display:block; float:left; 
			background:url(images/main_navigation_right.jpg) repeat-x 0 0 scroll; height:29px; width:5px;}			
.normaltab li span{position:relative; top:7px;}

</style>
<title>iOlink : Insurance Scan Document</title>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<!-- <script type="text/javascript" src="../common/script_function.js"></script> -->
<script type="text/javascript">
window.focus();		
	function changeSelection(tdObj){
		var currTab = document.getElementById("curr_tab").value;
		document.getElementById(currTab).className = 'normaltab';
		document.getElementById(tdObj.id).className = 'hovertab';
		document.getElementById("curr_tab").value = tdObj.id;
		var isRecordExists = document.frmtype.isRecordExists.value;
		var myvals = document.frmtype.hide_type.value;		
		var patient_id = document.frmtype.patient_id.value;		
		var waiting_id = document.frmtype.waiting_id.value;		
		var currentCaseid = document.frmtype.currentCaseid.value;		
		switch(tdObj.id){
			case 'new_scan':
				document.frames['scans'].location.href = 'new_scan_card.php?type='+myvals+'&isRecordExists='+isRecordExists+'&patient_id='+patient_id+'&waiting_id='+waiting_id+'&currentCaseid='+currentCaseid; //?type=primary&isRecordExists=yes
			break;
			case 'old_scan':
				document.frames['scans'].location.href = 'prev_scan_card.php?ty='+myvals+'&isRecordExists='+isRecordExists+'&patient_id='+patient_id+'&waiting_id='+waiting_id+'&currentCaseid='+currentCaseid;
			break;
		}
	}
	
	function getNewImage(type,patient_id,waiting_id,currentCaseid){
		var pgNme='insurance_primary.php';
		/*
		if(type=='primary') {
			pgNme='insurance_primary.php';
		}else if(type=='secondary') {
			pgNme='insurance_secondary.php';
		}else if(type=='tertiary') {
			pgNme='insurance_tertiary.php';
		}*/
		pgNme=pgNme+'?pid='+patient_id;
		pgNme=pgNme+'&wid='+waiting_id;
		pgNme=pgNme+'&type='+type;
		pgNme=pgNme+'&insCaseIdList='+currentCaseid;
		window.opener.location.href = pgNme;
		//opener.location.reload();
		return false;
	}
	
	function close_window()
	{
		//window.opener.location.href = '../patient_info/insurance/index.php';
		window.close();
	}
	function changeBackImage(obj,val){
		var cur_tab = document.getElementById("curr_tab").value;
		if(cur_tab != obj.id){
			var clasName = val == false ? 'hovertab' : 'normaltab';
			
			obj.className = clasName;
		}
	}	
</script>		
</head>
<body class="body_c" onUnload="getNewImage('<?php echo $_REQUEST['type'];?>','<?php echo $_REQUEST['patient_id'];?>','<?php echo $_REQUEST['waiting_id'];?>','<?php echo $_REQUEST['currentCaseid'];?>');">
<form name="frmtype" method="post" action="">
	<input type="hidden" value="<?php echo $_GET['type'];?>" name="hide_type">
	<input type="hidden" value="<?php echo $_GET['isRecordExists'];?>" name="isRecordExists">
	<input type="hidden" value="<?php echo $_GET['patient_id'];?>" name="patient_id">
	<input type="hidden" value="<?php echo $_GET['waiting_id'];?>" name="waiting_id">
	<input type="hidden" value="<?php echo $_GET['currentCaseid'];?>" name="currentCaseid">
	<!-- <input type="hidden" value="<?php //echo $_GET['type'];?>" name="type"> -->
</form>
<input type="hidden" name="curr_tab" id="curr_tab" value="new_scan">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="left">
            <table border="0" width="100%" cellspacing="0" cellpadding="1">
                <tr align="left">
                    <td valign="middle" nowrap="nowrap" width="100%">
                        <ul id="new_scan" onClick="changeSelection(this);" class="hovertab" onMouseOver="changeBackImage(this,false);" onMouseOut="changeBackImage(this,true);">
                            <li class="left"></li>
                            <li class="center"><span style="text-align:center;">Scan Insurance</span></li>
                            <li class="right"></li>
                        </ul>
                        <ul id="old_scan" onClick="changeSelection(this);" class="normaltab" onMouseOver="changeBackImage(this,false);" onMouseOut="changeBackImage(this,true);">
                            <li class="left"></li>
                            <li class="center"><span style="text-align:center;">Previous Insurance</span></li>
                            <li class="right"></li>
                        </ul>
                    </td>
                 </tr>
            </table>						
        </td>									
    </tr> 
    <tr height="30">   
        <td align="center" valign="top">		
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td id="demographics" class="ContentShown">				
                        <iframe name="scans" width="100%" height="630" frameborder="0" scrolling="no" src="new_scan_card.php?type=<?php echo $_REQUEST['type'];?>&isRecordExists=<?php echo $_REQUEST['isRecordExists'];?>&patient_id=<?php echo $_REQUEST['patient_id'];?>&currentCaseid=<?php echo $_REQUEST['currentCaseid'];?>"></iframe>
                    </td>
                </tr>
            </table>
        </td>
    </tr>    
</table>
</body>
</html>