<?php 
/*
File: index.php
Coded in PHP7
Purpose: Patient Interface Main Screen 
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php"); 
$_SESSION['default_tab']="patient_interface";
unset($_SESSION['order_id']);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH']; ?>';
$(document).ready(function(e) {
	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("li_select").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		if($(".tab_container").css('display')=='none'){
			$('#admin_iframe').attr('src','about:blank').hide();
			$(".tab_container").fadeIn();
		}
		$("ul.tabs li").removeClass("li_select"); //Remove any "active" class
		$("ul.tabs li a").removeClass("a_select"); //Remove any "active" class
		$(this).addClass("li_select"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content		
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		var activeTabLink = $(this).find("a").attr("id"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		$("#"+activeTabLink).addClass('a_select');
		return false;
	});
	
	$(".tab_container div.tab_content ul li a").click(function(){
		href = $(this).attr('href');
		$(".tab_container").hide();
		$('#default_frame_pt_hst').css("display","none");
		$('#admin_iframe').attr('src',href).fadeIn();
		return false;
	});
	
	
	//BUTTONS
	var mainBtnArr = new Array();
	top.btn_show("admin",mainBtnArr);
	
newpt = function()
{
	
	window.location.href = WEB_PATH+"/interface/demographics/index.php?newpt";
}
});

function new_orders()
{
	var dataString = 'action=PlaceNewOrder';
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			window.location.reload(true);
		}
	});
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
/*function sess_destr()
{
	$.ajax({
		type:"POST",
		url:"session_unset.php",
		data:"",
		success:function(msg)
		{
			top.falert(msg);
		}
	});
}*/
function closept(){
	//top.window.location.href = top.window.location.protocol+"//"+top.window.location.hostname +top.window.location.pathname + "?closept";
	window.location.href =WEB_PATH+'/interface/demographics/index.php?closept';
    top.location =WEB_PATH+'/index2.php?closept';
	//top.location.reload();
}
</script>
<style type="text/css">
ul.tabs1 {
    margin: 0;
    overflow: hidden;
    padding: 0px;
    display: block;
}
.tabs1 li.li_select {
    background: transparent url(../../images/menuleft.png) no-repeat left 0px !important;
    float: left;
    height: 32px;
    margin: 0 40px 0 0;
    text-align: center;
}
.a_select{
	background: transparent url("../../images/menuright.png") no-repeat right 0px !important;
    color: #FFFFFF !important;
    display: block;
    height: 16px;
    margin: 0 -30px 0 0;
    padding: 0px 10px 16px 10px;
    text-decoration: none;
    vertical-align: middle;
}
</style>
</head>
<!--onUnload="sess_destr();"-->
    <body>
            <ul class="tabs1" style="width:100%;">
                <li class="li_select" style="width:160px;" onClick="event.preventDefault();">
				<span class="a_select" id="link1" style="cursor:pointer;">Point of Sale</span></li>
<?php
/*Get Patient's Vision Plan*/

$sqlInsCases = "SELECT insct.case_name, insc.ins_caseid, inscomp.name AS 'comp_name'
FROM 
	insurance_case_types insct 
	JOIN insurance_case insc ON (insc.ins_case_type = insct.case_id AND insc.case_status = 'Open') 
	JOIN insurance_data insd ON (insd.ins_caseid = insc.ins_caseid AND insd.provider > 0) 
	JOIN insurance_companies inscomp ON (inscomp.id = insd.provider AND inscomp.in_house_code != 'n/a') 
WHERE 
	insc.patient_id = '".$_SESSION['patient_session_id']."' AND insd.actInsComp = 1 AND (LOWER(insd.type)= 'primary' OR LOWER(insd.type) = 'secondary') 
GROUP BY 
	insc.ins_caseid 
ORDER BY 
	insc.ins_case_type";
$respInsCases = imw_query($sqlInsCases);

$sqlVisionPlan = "SELECT insct.case_name,insc.ins_caseid,inscomp.name AS 'comp_name',insd.co_ins,insd.comments
					FROM insurance_case_types insct 
					JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
					JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
					JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
					WHERE insc.patient_id='".$_SESSION['patient_session_id']."' AND insct.vision=1 AND insd.actInsComp=1
					AND ( LOWER(insd.type)= 'primary' OR LOWER(insd.type) = 'secondary')
					GROUP BY insc.ins_caseid 
					ORDER BY insc.ins_case_type LIMIT 1";
$respVisionPlan = imw_query($sqlVisionPlan);
$ptVisionPlanId	= 0;
$insCoPay = array('ins'=>'0', 'pt'=>'0');
$insVisComment = '';
if( $respVisionPlan && imw_num_rows($respVisionPlan) > 0 ){
	$visRow = imw_fetch_object($respVisionPlan) ;
	echo '<span id="visionPlan"><strong>'.$visRow->case_name.': <span id="visionPlanId">'.$visRow->comp_name.'</span></strong></span>';
	$ptVisionPlanId = (int)$visRow->ins_caseid;
	if( trim($visRow->co_ins) != '' ){
		$insCoPayTemp = trim($visRow->co_ins);
		$insCoPayTemp = explode('/', $insCoPayTemp);
		if( count($insCoPayTemp) > 0 ){
			$insCoPay['pt']	= (float)array_pop($insCoPayTemp);
			$insCoPay['ins']= (float)array_pop($insCoPayTemp);
		}
		$insVisComment = trim($visRow->comments);
	}
}
imw_free_result($respVisionPlan);
echo '<select id="visIncCases" style="margin:2px 10px;max-width:100px;" onChange="changePosIns(this)">';
	echo '<option value="0">Self Pay</option>';
if(imw_num_rows($respInsCases)>0){
	while($rowInsCases = imw_fetch_assoc($respInsCases)){
		echo '<option value="'.$rowInsCases['ins_caseid'].'" '.(($ptVisionPlanId==$rowInsCases['ins_caseid'])?'selected':'').'>'.$rowInsCases['case_name'].'-'.$rowInsCases['ins_caseid'].'</option>';
	}
}
echo "<select>";
?>
            </ul>
    <?php 
		if($_SESSION['patient_session_id']==""){
	?>
   		<div class="module_heading" style="text-align:center;">
		<div style="height:<?php echo $_SESSION['wn_height']-450;?>px;">
 		 <div style="padding-top:150px;">Please select Patient to Proceed <br><br> To add new patient click on Add New Patient button below</div>
  		</div>
	</div>
    
<script type="text/javascript">
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Add New Patient","top.main_iframe.newpt();");
	top.btn_show("patient",mainBtnArr);    
});
</script>
    <?php		
		}else{
	?>
<script>
var ptVisionPlanId	= <?php echo $ptVisionPlanId; ?>;
var ptVisionCopay	= <?php echo json_encode($insCoPay); ?>;
$(document).ready(function(){

<?php
/*Ins Alert*/
if( $_SESSION['patient_session_ins_alert'] == 0 && $insVisComment!='' ){
	echo 'top.falert(\'<strong>Vision Plan:</strong><br /><br />'.$insVisComment.'\');';
	$_SESSION['patient_session_ins_alert'] = 1;
}
/*End Ins Alert*/
?>	
	
	$('#link1').click(function(){
		top.page_link('pos-');
		//window.location.href='index.php';
	})
	

  $("#jump_to").change(function(){
		
		top.page_link('pos-'+this.value);
		/*$('#default_frame_pt_hst').css("display","none");
		if(this.value==''){
			window.location.href='index.php';
		}
		if(this.value=='frame' || this.value=="lens"){
			$("#frame_li").click();
		}
		if(this.value=="contact_lens"){
			$("#contact_li").click();
		}
		if(this.value=="patient_rx_history"){
			$("#patient_rx_history").click();
		}
		if(this.value=="other_selection"){
			$("#other_selection").click();
		}
		if(this.value=="patient_pos"){
			$("#patient_pos").click();
		}
		if(this.value=="patient_history"){
			$("#patient_history").click();
		}*/
	});	
});

function changePosIns(obj){
	
	var selected = $(obj).val();
	
	var insList = top.main_iframe.admin_iframe.$('#main_ins_case_id_1');
	
	if( insList.length == 1 ){
		$(insList).val(selected).trigger('change');
	}
}

</script>
	
    <div class="btn_cls" style="padding: 0px; text-align: right; margin: -29px 5px 2px 0;"><!--<input type="button" onClick="javascript:new_orders();" name="new_order" value="New Order"/>-->
  	<select name="jump_to" id="jump_to">
    	<option value="">Point of Sale</option>
        <option value="frame">Frames & Lenses</option>
        <option value="contact_lens">Contact Lenses</option>
        <option value="patient_rx_history">Patient Rx History</option>
        <option value="other_selection">Other Selection</option>
    </select>
	<span style="padding:0 10px 0 10px;display:inline-block;vertical-align:middle;">
		<img style="margin: 0px; cursor: pointer;" onClick="javascript:closept();" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/delete_record.png" alt="remove_patient" title="Close Patient">
	</span>
    </div>
    
     <div class="tab_container" style="float:left; width:100%;">
        <div id="tab1" class="tab_content">
		  <ul style="float:left;width:100%;">
			<!--<li class="m10 fl alignCenter">
				<a href="pt_picture.php" onClick="top.WindowDialog.closeAll();"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/picture.jpg" class="min_icon25" /></a>
				<br />
				<a class="text_purpule" href="pt_picture.php" onClick="top.WindowDialog.closeAll();">Picture</a>
			</li>-->
			<li class="m10 fl alignCenter">
				<a id="frame_li" href="pt_frame_selection_1.php" onClick="top.WindowDialog.closeAll();"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/frame.jpg" class="min_icon25" /></a>
				<br />
				<a class="text_purpule" href="pt_frame_selection_1.php" onClick="top.WindowDialog.closeAll();">Frames & Lenses</a>
			</li>
			<!--li class="m10 fl alignCenter"><a id="lens_li" href="lens_selection.php"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/contacts.jpg" class="min_icon25" /></a><br /><a class="text_purpule" href="lens_selection.php">Lenses</a></li-->
			<li class="m10 fl alignCenter">
				<a id="contact_li" href="contact_selection.php" onClick="top.WindowDialog.closeAll();"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/lenses.jpg" class="min_icon25" /></a>
				<br />
				<a class="text_purpule" href="contact_selection.php" onClick="top.WindowDialog.closeAll();">Contact Lenses</a>
			</li>
			<li class="m10 fl alignCenter">
				<a id="patient_rx_history" href="patient_rx_history.php" onClick="top.WindowDialog.closeAll();"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/pt_history.jpg" class="min_icon25" /></a>
				<br />
				<a class="text_purpule" href="patient_rx_history.php" onClick="top.WindowDialog.closeAll();">Patient Rx History</a>
			</li>
			
            <!--<li class="m10 fl alignCenter"><a id="patient_history" href="../admin/order/index.php?order=pt_hst"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/patient_history.jpg" /></a><br /><a class="text_purpule" href="../admin/order/index.php?order=pt_hst">Patient History</a></li>-->
			
            <li class="m10 fl alignCenter">
				<a id="other_selection" href="other_selection.php" onClick="top.WindowDialog.closeAll();"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/others.jpg" class="min_icon25" /></a>
				<br />
				<a class="text_purpule" href="other_selection.php" onClick="top.WindowDialog.closeAll();">Other Selection</a>
			</li>
			<!--<li class="m10 fl alignCenter"><a id="patient_pos" href="patient_pos.php"><img border="0" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/pt_interface/pos.jpg" /></a><br /><a class="text_purpule" href="patient_pos.php">POS</a></li>-->
		  </ul>  
        </div>
     </div>

   <iframe src="about:blank" name="admin_iframe" id="admin_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-337;?>px; margin:0px; display:none; overflow:hidden;" frameborder="0" framespacing=0 scrolling="no" onload="top.loading.style.display='none';"></iframe>
       
       <div style="width:100%;float:left;height:<?php echo $_SESSION['wn_height']-500;?>px;overflow:hidden" id="default_frame_pt_hst">
       <iframe id="ptOrderHxIframe" name="ptOrderHxIframe" src="../admin/order/index.php?order=pt_hst&nw_stl=yes" style="width:100%;height:100%;margin:0px;" frameborder="0" framespacing="0"></iframe>
       </div>
       
	   <?php }?>

    </body>
</html>    
    
