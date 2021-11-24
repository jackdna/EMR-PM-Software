<?php 
/*
File: pt_picture.php
Coded in PHP7
Purpose: ADD/EDIT Patient Picture
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname(__FILE__)."/../../library/classes/common_functions.php"); 
$patient_id=$_SESSION['patient_session_id'];	
$order_id=$_SESSION['order_id']; 
	
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_REQUEST['chkBox']) && count($_REQUEST['chkBox'])>0){
		foreach($_REQUEST['chkBox'] as $id){
			$qry = "SELECT image FROM in_patient_pictures WHERE id = '".$id."'";
			$res = imw_query($qry);
			$row = imw_fetch_assoc($res);
			@unlink($row['image']);
			$qry = "DELETE FROM in_patient_pictures WHERE id = '".$id."'";
			imw_query($qry);
		}
	}
}

$def_img = "select id,pt_wear_pic from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='1' and show_default='1' and del_status='0'";
$res = imw_query($def_img);
$result = imw_fetch_array($res);
$pt_wear_pic = $result['pt_wear_pic'];

$pt_img = "select id from in_patient_pictures where image ='$pt_wear_pic' and patient='$patient_id'";
$pt_res = imw_query($pt_img);
$pt_result = imw_fetch_array($pt_res);
$pt_img_id = $pt_result['id'];

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link href="webcam/webcam.css?<?php echo constant("cache_version"); ?>" type="text/css" rel="stylesheet"/>
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="webcam/jquery.js?<?php echo constant("cache_version"); ?>" type="text/javascript"></script>
<script>
imgDir = "img/";
imgW = 600;
imgH = 500;
lThbW = 250;
wDiv = imgW/lThbW; 
hDiv = imgH/lThbW; 
frac = (wDiv > hDiv) ? wDiv : hDiv;
lThbH = imgH/frac;
rThbW = 100;
rThbH = 100;
$(document).ready(function(e) {
    chk_sel_checkbox();
});
function openCamera(){
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup','<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/webcam/flash.php','Take Picture','height=580,width=600,resizable=1');
}
function enlarge_pic(img_src,img_id){
	

	$('#full_pic').html('');
	
	var imgFull = document.createElement('img');
	imgFull.src = img_src;
	imgFull.width = 580;
	imgFull.height = 330;
	imgFull.onclick = function(){$('#full_pic').hide();}
	
	var divFClose = document.createElement('div');
	divFClose.className = 'close';
	divFClose.onclick = function(){$('#full_pic').hide();}
	
	var divTxt = document.createElement('div');
	$(divTxt).css({"float":"left","position":"absolute","margin-left":"200px","color":"white","font-size":"12px","font-weight":"bold"});
	$(divTxt).text("Image is selected for order");
	
	document.getElementById("full_pic").appendChild(divFClose);
	document.getElementById("full_pic").appendChild(divTxt);
	document.getElementById("full_pic").appendChild(imgFull);
	
	$('#sel_pic').val(img_id);
	$('#full_pic').show();

	
}
function loadPics(img_src,img_id){ 
	if(typeof(document.getElementById("txt_msg")) != "undefined" && typeof(document.getElementById("txt_msg")) != null){
		var div = document.getElementById("txt_msg");
		if(div != 'undefined' && div != null)
		div.parentNode.removeChild(div);
	}

	load_left_thumb(img_src,img_id);
	load_right_thumb(img_src,img_id)
	chk_sel_checkbox();
}
function rem_pic(obj){
	var x = obj.parentNode.parentNode; x.removeChild(obj.parentNode);
}


var lastNode =  0;
function load_left_thumb(img_src, img_id){

	document.getElementById(img_id).checked=true;
	
	if($('#left_blk '+'#'+img_id).length > 0){
		return;
	}
	var imgL = document.createElement('img');
	imgL.src = img_src;
	imgL.width = lThbW;
	imgL.height = lThbH;
	imgL.title = "Click to enlarge pic";
	imgL.onclick = function(){enlarge_pic(img_src,img_id)};
	
	var divLClose = document.createElement('div');
	divLClose.className = 'close';
	divLClose.onclick = function(){rem_pic(divLClose);}
	
	var divLThumb = document.createElement('div');
	divLThumb.className = 'left_thumb fl';
	divLThumb.id = img_id;
	divLThumb.appendChild(divLClose);
	divLThumb.appendChild(imgL);
	//document.getElementById('left_blk').className = 'left_blk flborder';
	divCnt = parseInt($('#left_blk .left_thumb').size());
	if(divCnt<4){
		if(divCnt == 0)$('#sel_pic').val(img_id);
		document.getElementById('left_blk').appendChild(divLThumb);
	}
	else{
		var container = document.getElementById('left_blk');
			$(divLThumb).insertBefore("#left_blk .left_thumb:eq("+lastNode+")");
			$("#left_blk .left_thumb:eq("+(lastNode+1)+")").remove();
			if(lastNode>=3) lastNode = 0;
			else lastNode++;
	}
	if($('#left_blk  .left_thumb').length > 4){
		$('.left_blk').find(".left_thumb:last").remove();
	}
}
function chk_sel_checkbox(){
        $("#right_blk input:checkbox").each(function(index, element) {
			jID = "#"+this.id;
            if($("#left_blk "+jID).length>0){
				$(this).attr('checked','checked');
			}else{
				$(this).attr('checked','');
			}
        });
}
function click_chk_box(img_src, img_id,obj){
	if(obj.checked){
		load_left_thumb(img_src, img_id,obj)
	}
	else{
	left_thb_id = "#"+img_id;
	$('#left_blk '+left_thb_id).remove();
	}
}
function load_right_thumb(img_src, img_id){
	var imgR = document.createElement('image');
	imgR.src = img_src;
	imgR.onclick = function(){load_left_thumb(img_src,img_id)};
	
	wFrac = lThbW/rThbW;
	hFrac = lThbH/rThbW;
	frac = (wFrac > hFrac)? wFrac : hFrac;
	imgR.width = rThbW;
	imgR.height = lThbH/frac;
	
	var chkBox = document.createElement('input');
	chkBox.className = 'chkBox';
	chkBox.type = "checkbox";
	chkBox.id = img_id;
	chkBox.value = img_id;
	chkBox.name = "chkBox[]";
	chkBox.onclick = function(){click_chk_box(img_src, img_id,this);};
	
	var divRThumb = document.createElement('div');
	divRThumb.className = 'right_thumb fl';
	divRThumb.appendChild(chkBox);
	divRThumb.appendChild(imgR);
	
	document.getElementById('chkBox').checked;
	document.getElementById('right_blk').appendChild(divRThumb);
}
function del_img(){
	if( $(".chkBox:checked").length == 0 ) 
	{
	   top.falert('Please check atleast one record');
	}
	else
	{
		top.fconfirm("Do you want to delete image(s)",del_img_callBack);
	}
}
function del_img_callBack(result)
{
	if(result==true)
	{
		document.frm.submit();	
	}
}
function refresh_left_blk(){
	document.getElementById('left_blk').innerHTML = '';
}

$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
	<div class="listheading mt5">
		<div style="width:<?php if($order_id!=""){ echo "340"; } else { echo "715"; } ?>px; float:left;">Pictures</div>
		<?php if($order_id!="" || $order_id>0) { ?>
		<div style="width:375px; float:left;text-align:center;">Order #<?php echo $order_id; ?></div>
		<?php } ?>
	</div>
	
<?php 
function get_image($img_path, $type,$img_id){
	$imgW = 600; $imgH = 500;
	
	$lThbW = 250; $rThbW = 100;
	if($type == "full"){
		$wFrac = $imgW/$lThbW;
		$hFrac = $imgH/$lThbW;
		
		$frac = ($wFrac > $hFrac)? $wFrac : $hFrac;
		$width = $lThbW;
		$height = $imgH/$frac ;
		$img = "<img src='".$img_path."' width='".$width."' height='".$height."' onClick='enlarge_pic(\"".$img_path."\",\"".$img_id."\")' title='Click to enlarge pic'>";
		
	}
	else if($type == "thumb"){
		$arrSize = getimagesize($img_path);
		//$lThbW = $arrSize['0'];
		//$lThbH = $arrSize['1'];
		
		$wFrac = $imgW/$rThbW;
		$hFrac = $imgH/$rThbW;
		
		$frac = ($wFrac > $hFrac)? $wFrac : $hFrac;
		$width = $rThbW;
		$height = $imgH/$frac;
		$img = "<img src='".$img_path."' width='".$width."' height='".$height."' onclick='load_left_thumb(\"".$img_path."\",\"".$img_id."\")'>";
	}
	return $img;
}
$qry = "SELECT id,image FROM in_patient_pictures WHERE patient = '".$_SESSION['patient_session_id']."' ORDER BY id DESC";
$res = imw_query($qry);
$arrImg = array();
while($row = imw_fetch_assoc($res)){
	$arrImg[$row['id']] = $row['image'];
}
?>

<form id="frm" name="frm" method="post" style="margin:0px 20px;">
<div style="height:<?php echo $_SESSION['wn_height']-423;?>px; overflow-y:auto; float:left; width:100%">
<div class="main_blk" style="height:<?php echo $_SESSION['wn_height']-440;?>px;">
<div>
    <div class="left_blk fl" id="left_blk" style="height:<?php echo $_SESSION['wn_height']-440;?>px;">
    <div id="full_pic" class="full_img" style="position:absolute; z-index:100; display:none"><div class="close" onClick="$('#full_pic').hide();"></div></div>
    <?php if(count($arrImg)<=0){echo '<div class="module_heading" id="txt_msg" style="margin-top:19%; text-align:center;text-indent:27%;">Please upload picture</div>';}?>
    <?php  $count = 0;
            foreach($arrImg as $id=>$img_path){
			if($count == 0){
				echo "<script>
						$(document).ready(function(e){
							$('#sel_pic').val('".$id."');
						});
					</script>";
			}
            echo '<div class="left_thumb fl" id="'.$id.'"><div class="close" onclick="rem_pic(this)"></div>'.get_image($img_path,'full',$id).'</div>';
            $count++;
            if($count >= 4)break;
           }
    ?>
    </div>
    <div class="right_blk fl" id="right_blk" style="height:<?php echo $_SESSION['wn_height']-440;?>px;">
    <?php  foreach($arrImg as $id=>$img_path){
            echo '<div class="right_thumb fl" ><input type="checkbox" id="'.$id.'" value="'.$id.'" name="chkBox[]" class="chkBox" onclick="click_chk_box(\''.$img_path.'\','.$id.',this)">'.get_image($img_path,'thumb',$id).'</div>';
           }
    ?>
    </div>
</div>

</div>
</div>
<div class="btn_cls" style="margin:0px; padding:0">
<input type="hidden" name="sel_pic" id="sel_pic">
</div>
</form>
<script>
function redirect_window(mode){
	if(mode=='order'){
		window.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/pt_frame_selection_1.php?sel_pic='+$('#sel_pic').val();
	}else{
		window.parent.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/index.php';
	}
}

$(document).ready(function(e) {
	var pt_img_path = '<?php echo $pt_wear_pic; ?>';
	var pt_img_id = '<?php echo $pt_img_id; ?>';
	if(pt_img_path!="" && pt_img_id!="" )
	{
		enlarge_pic(pt_img_path,pt_img_id);
	}

	//BUTTONS
	var mainBtnArr=[];
	mainBtnArr[0] = new Array("frame","Order","top.main_iframe.admin_iframe.redirect_window('order');");
	mainBtnArr[1] = new Array("frame","Return","top.main_iframe.admin_iframe.redirect_window('return')");
	mainBtnArr[2] = new Array("frame","Delete","top.main_iframe.admin_iframe.del_img()");
	mainBtnArr[3] = new Array("frame","Capture","top.main_iframe.admin_iframe.openCamera()");
	top.btn_show("admin",mainBtnArr);		
});
</script>
</body>
</html>