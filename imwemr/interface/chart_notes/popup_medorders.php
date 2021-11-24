<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
<!DOCTYPE html>
<html>
<head>
<?php }?>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/wv_landing.css" rel="stylesheet">
<style>
.fl{
	float:left;
}
.ele{
	/*display:inline;*/
	vertical-align:top;
	display:table-cell;
	/*float:left;*/
	padding:2px;
}
select[id=ele_responsible_person],select[id=ele_dx_code]{height:50px;width:100px; display:marker}
.label{
	font-size:12px; 
	font-family:Calibri, Arial, Helvetica, sans-serif; 
	vertical-align:top; 
	padding-left:4px; 
	font-style:italic; 
/*	font-weight:bold;*/
	color:#000000;
	display:block;
}
#divpopup label{ border:0px solid red; width:150px; display:inline-block; padding-left:5px;  }
#divpopup select[id=elem_order_type]{height:25px;}

#loading_img{ display:none; top:50px; left:50%; z-index:1000; position:absolute; }

#div_cpoe_error{width:900px;}
.btncon{position:absolute; bottom:0px; width:100%;padding-top:5px;padding-bottom:5px;}
.btncon  div{ text-align:center; margin-left:10px }
.ele input[type=text], .ele select[id*=ele_sig]{width:100px;}
.ele input[type=text][id*=ele_order_name]{width:150px;}
.ele input[type=radio]{margin-left:20px;}
#dvAddNewOrder{margin-bottom:20px;}

#procssslbl{ margin-left:5px;line-height:25px;font-weight:bold;background-color:red;color:white;padding:2px;border:1px solid black; }
div[id*=div_imaging]{margin-left:10px;}
div[id*=div_hgt]{width:900px; height:10px;}
.order_nm{ border:0px solid red; width:150px;line-height:18px;display:inline-block; }

</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
<script>
var zPath = '<?php echo $GLOBALS['rootdir'];?>';
/*
function get_template(obj){
	//top.show_loading_image('show',300);
	var order_template_id = obj.value;
	//CKEDITOR.template_id = order_template_id;
	$('#mode').val('get_template');
	$('#order_template_id').val(order_template_id);
	frm_data = $('#frm_order').serialize();
	frm_data += "&order_template_id="+order_template_id;
	$.ajax({
		url:'popup.php',
		type:"POST",
		data:frm_data,
		complete:function(r){
			$('#div_order_template_draw').show();
			//CKEDITOR.instances['FCKeditor1'].setData(r.responseText)
			top.show_loading_image('hide');
		}
	});
	top.show_loading_image('hide');
}
function get_order_template_option(order_type_id){
	$('#mode').val('get_order_template_option');
	frm_data = $('#frm_order').serialize();
	frm_data += "&order_type_id="+order_type_id;
	WRP = top.WRP;
	$.ajax({
		url:"popup.php",
		type:"POST",
		data:frm_data,
		complete:function(r){//alert(r.responseText)
			$("#div_order_template").html(r.responseText+"<span class='label'>Order Template</span> ");
			//$("#order_template").bind('change',get_template);
		}	
	});
}
*/
function show_loading_image(val){
	document.getElementById("loading_img").style.display = val;
}
function setOrderOption(obj){
	if(typeof(obj) == "object")
	var val = obj.value;
	else
	var val = obj;
	if(typeof(val)!="undefined")
	val = val.toString();
	//get_order_template_option(val);
	fn_set_typeahead();
	//var editor = CKEDITOR.instances['FCKeditor1'];
	$("#div_order_template_draw").show();
	$("#divaddbtn").hide();
	$("#div_order_template").show();	
	
	//remove additional if any--
	if(val!=1){
	$("input[name*=ele_dosage], input[name*=ele_quantity], input[name*=ele_sig], input[name*=ele_refill], input[name*=ele_ndc_code]").each(function(indx){
		if(this.name!="ele_dosage" && this.name!="ele_quantity" && this.name!="ele_sig" && this.name!="ele_refill" && this.name!="ele_ndc_code" ){
			$(this).parent().remove();
		}
	});
	$("#divorder_multiopts br").remove();
	}
	//--
	
	
	switch(val){
		
		case "1": //Meds
			//editor.resize( '', '<?php echo $doc_height - 160;?>');
			
			var id = $("#id").val();
			//if(id==""){ //do when new insertion			
			$("#div_order_template_draw, #div_order_template").hide();
			$("#divaddbtn").show();			
			//}
			
			$("#div_meds").show();
			$("#div_dosage").show();
			$("#div_qty").show();
			$("#div_sig").show();
			$("#div_refill").show();
			$("#div_ndc").show();
			
			$("#div_responsible_person").hide();
			$("#div_test_name").hide();
			$("#div_information").hide();
			$("#div_instruction").hide();
			$("#div_lad_rad_type").hide();
			$("#div_codes").hide();
			$("#div_cpt_code").hide();
			$("#div_loinc").hide();
			$("#div_snomed").hide();			
		break;
		
		
	}
}

function popup_dbl(divid,sourceid,destinationid,act,odiv){
	if(act=="single" || act=="all"){
			if(act=='single')	{
				$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
			}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
		}else if(act=="single_remove" || act=="all_remove"){
			if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
			if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
			$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
				var at = $(a).text(), bt = $(b).text();
				return (at > bt)?1:((at < bt)?-1:0);
			}));
			$("#"+destinationid).val('');
		}else{
			$("#"+destinationid+" option").remove();
			$("#"+odiv+" option").clone().appendTo("#"+destinationid);
			$("#"+divid).show("clip");
		}
		set_frame_height();
}
function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
		if(action=="done"){
			var sel_cnt=$("#"+sourceid+" option").length;
			$("#"+divid).hide("clip");
			$("#"+destinationid+" option").each(function(){$(this).remove();})
			$("#"+sourceid+" option").appendTo("#"+destinationid);
			$("#"+destinationid+" option").attr({"selected":"selected"});
			$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
			if(sel_cnt>8){
				$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
			}
		}else if(action=="close"){
			$("#"+divid).hide("clip");
		}
		reset_frame_height();
}
function submit_frm_order(){	
	var check = false;
	frm = document.frm_order;
	//parent.parent.show_loading_image('none');
	msg = '';
	
	if($("input[name*=ele_order_name]").length<=0){
		msg = msg + '&bull; Please select some orders.<br>';
		//alert($("input[name=elem_btnCancel]").length);
		top.fAlert(msg);
		$("input[name=elem_btnCancel]").trigger("click");
		return;
	}
	/*
	if($("input[name*=ele_order_name][value='']").length>1){
		msg = msg + '&bull; Please Enter Order Name<br>';
		frm.ele_order_name.className = 'mandatory';
		if(check == false){
			check = true;
			frm.ele_order_name.focus()
		}
	}
	*/
	if(msg == ''){
		document.getElementById('save_frm').value='save';
		<?php if(!isset($_REQUEST['callFrom'])){?>
		parent.parent.show_loading_image('block');
		frm.submit();
		<?php }elseif($_REQUEST['callFrom'] == "WV"){?>
		top.show_loading_image('block',300);
		//alert("112--1");
		parent.saveOrderDetail_new(document.frm_order,1,'<?php echo  $_REQUEST['assi']; ?>');
		<?php }?>
	}else{		
		top.fAlert(msg);
		msg='';
		return false;
	}
}
function popup_hide(){
	<?php if(!isset($_REQUEST['callFrom'])){?>
		$('.dialogMask').fadeOut('fast');
		$('#divpopup').hide();
	<?php }elseif($_REQUEST['callFrom'] == "WV"){?>
		$("#divpopupWV",window.parent.document).remove();
	<?php }?>
	
}

function addmoreMeds(){
	
	//find index
	var indx = $("#divorder_multiopts input[name*=ele_dosage]").length;
	
	var str="";
	
	str+="<br/>"+
	 "<div class=\"ele\" id=\"div_dosage"+indx+"\">"+
            "<input type=\"text\" name=\"ele_dosage"+indx+"\" id=\"ele_dosage"+indx+"\" value=\"\"  >"+
            "<span class=\"label\">Dosage</span> "+
	     "<input type=\"hidden\" name=\"med_id"+indx+"\" id=\"med_id"+indx+"\" value=\"\" > "+
	     "<input type=\"hidden\" name=\"id"+indx+"\" id=\"id"+indx+"\" value=\"\" />"+
        "</div>"+
        "<div class=\"ele\" id=\"div_qty"+indx+"\">"+
        "     <input type=\"text\" name=\"ele_quantity"+indx+"\" id=\"ele_quantity"+indx+"\" value=\"\"  >"+
        "     <span class=\"label\">Quantity</span> "+
        "</div>"+
        "<div class=\"ele\" id=\"div_sig"+indx+"\">"+
        "     <input type=\"text\" name=\"ele_sig"+indx+"\" id=\"ele_sig"+indx+"\" value=\"\"  >"+
        "     <span class=\"label\">Sig</span> "+
        "</div>  "+
        "<div class=\"ele\" id=\"div_refill"+indx+"\">"+
        "     <input type=\"text\" name=\"ele_refill"+indx+"\" id=\"ele_refill"+indx+"\" value=\"\"  >"+
        "     <span class=\"label\">Refill</span> "+
        "</div> "+
        "<div class=\"ele\" id=\"div_ndc"+indx+"\">"+
        "    <input type=\"text\" name=\"ele_ndc_code"+indx+"\" id=\"ele_ndc_code"+indx+"\" value=\"\"  >"+
        "    <span class=\"label\">NDC Code</span> "+
        "</div>";
	
	//
	$("#divorder_multiopts").append(str);
	
}

function deleteme(id){	
	$("#div_imaging"+id+", #div_hgt"+id+"").remove();
}

function disProcessing(v){
	if(v==1){
		//$("#loading_img").append("<label id='procssslbl' >Processing! Please wait.</label>");
		top.show_loading_image('show','200', 'Processing...');	
		//$("#loading_img").append("<div class=\"loading_container\"><div class=\"process_loader\"></div><div id=\"div_loading_text\" class=\"text-info\">Please wait, while system is getting ready for you...</div></div>");
		//$("#loading_img").show();		
	}else{	
		$("#procssslbl").remove();
		$("#loading_img").hide();
		top.show_loading_image("hide");	
	}
}

function set_emdeon_off(){	
	$("#elem_donot_check_emdeon").val("1");
	//alert("Press save button again to save anyway! ");
}

function addnewmedorder_check(obj){
	//if($.trim(obj.value)!=""){
		var flg=1;
		$("input[id*=ele_order_name]").each(function(){ if(this.value==""){flg=0;}  });		
		if(flg==1){
		addnewmedorder();
		set_frame_height();
		}
	//}
}

function addnewmedorder(){
var c=  $("#elem_lenOrders").val();
pfs = (c==0)?"":c;
c++;
var str = "<div id=\"div_imaging"+pfs+"\" >"+
   "<input type=\"hidden\" name=\"id"+pfs+"\" id=\"id"+pfs+"\" value=\"0\" />"+
    "<div class=\"ele\" id=\"div_order_name"+pfs+"\">"+
        "<input type=\"text\" class=\"form-control\" name=\"ele_order_name"+pfs+"\" id=\"ele_order_name"+pfs+"\" value=\"\"   onchange=\"addnewmedorder_check(this)\" placeholder=\"Add New Order\" >"+
        "<input type=\"hidden\" name=\"med_id"+pfs+"\" id=\"med_id"+pfs+"\" value=\"\" >"+
        "<input type=\"hidden\" name=\"ele_order_type"+pfs+"\" id=\"ele_order_type"+pfs+"\" value=\"1\" >"+
        "<div class=\"label\" >Order Name</div>"+
    "</div>"+    
    "<input type=\"hidden\" id=\"order_template"+pfs+"\" name=\"order_template"+pfs+"\" value=\"\">"+
        "<div class=\"ele\" id=\"div_dosage"+pfs+"\">"+
            "<input type=\"text\" class=\"form-control\" name=\"ele_dosage"+pfs+"\" id=\"ele_dosage"+pfs+"\" value=\"\"  >"+
            "<span class=\"label\">Dosage</span> "+
        "</div>"+
        "<div class=\"ele\" id=\"div_qty"+pfs+"\">"+
            "<input type=\"text\" class=\"form-control\" name=\"ele_quantity"+pfs+"\" id=\"ele_quantity"+pfs+"\" value=\"\"  >"+
            "<span class=\"label\">Quantity</span> "+
        "</div>"+
        "<div class=\"ele\" id=\"div_sig"+pfs+"\">"+
            "<input type=\"text\" class=\"form-control\" name=\"ele_sig"+pfs+"\" id=\"ele_sig"+pfs+"\" value=\"\"  >"+
            "<span class=\"label\">Sig</span> "+
        "</div>  "+
        "<div class=\"ele\" id=\"div_refill"+pfs+"\">"+
            "<input type=\"text\" class=\"form-control\" name=\"ele_refill"+pfs+"\" id=\"ele_refill"+pfs+"\" value=\"\"  >"+
            "<span class=\"label\">Refill</span> "+
        "</div> "+
        "<div class=\"ele\" id=\"div_ndc"+pfs+"\">"+
            "<input type=\"text\" class=\"form-control\" name=\"ele_ndc_code"+pfs+"\" id=\"ele_ndc_code"+pfs+"\" value=\"\"  >"+
            "<span class=\"label\">NDC Code</span> "+
        "</div>"+	
	"<div class=\"ele form-inline\" id=\"div_site"+pfs+"\">       "+
	"<div class=\"radio\"><input type=\"radio\" id=\"elem_order_site"+pfs+"_ou\" name=\"elem_order_site"+pfs+"\" value=\"OU\"  ><label for=\"elem_order_site"+pfs+"_ou\" ><b class=\"ou\">OU</b></label></div> "+
	"<div class=\"radio\"><input type=\"radio\" id=\"elem_order_site"+pfs+"_od\" name=\"elem_order_site"+pfs+"\" value=\"OD\"  ><label for=\"elem_order_site"+pfs+"_od\" ><b class=\"od\">OD</b></label></div> "+
	"<div class=\"radio\"><input type=\"radio\" id=\"elem_order_site"+pfs+"_os\" name=\"elem_order_site"+pfs+"\" value=\"OS\"  ><label for=\"elem_order_site"+pfs+"_os\" ><b class=\"os\">OS</b></label></div> "+
	"<div class=\"radio\"><input type=\"radio\" id=\"elem_order_site"+pfs+"_po\" name=\"elem_order_site"+pfs+"\" value=\"PO\"  ><label for=\"elem_order_site"+pfs+"_po\" ><b >PO</b></label></div> "+
     "<span class=\"label\">Site</span> 	"+
     "</div>"+
     "<div class=\"ele\"   >"+
	"<span class=\"glyphicon glyphicon-remove\" title=\"Delete\" onclick=\"deleteme('"+pfs+"')\"></span> 	"+
     "</div>"+    
"</div>"+
"<div id=\"div_hgt"+pfs+"\" ></div>"+
"";

$("#dvAddNewOrder").append(str);
$("#elem_lenOrders").val(c);

//typeahead
//var obj70 = new actb(document.getElementById('ele_order_name'+pfs),custom_array_medicine,"","",document.getElementById('med_id'+pfs),custom_array_medicine_id);
fn_set_typeahead();

}

</script>
<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
</head>
<body class="bg2">
<?php } ?>
<div align="center" id="loading_img" >
	Loading...
</div>
<div id="divpopup_inner"  class="bg2"> <?php /* style="height:<?php echo $doc_height;?>px;width:<?php echo $doc_width;?>px;display:block;" */ ?>

<form name="frm_order" id="frm_order" method="post" action="" onSubmit="return submit_frm_order();">

<input type="hidden" name="save_frm" id="save_frm" value="save_frm">
<input type="hidden" name="save_meds_form" id="save_meds_form" value="1">
<?php if($_REQUEST['callFrom'] == "WV"){?>
<input type="hidden" name="elem_plan_num" value="<?php echo $_REQUEST['assi'];?>">
<input type="hidden" id="elem_donot_check_emdeon" name="elem_donot_check_emdeon" value="">
<?php }?>
<!--<input type="hidden" name="order_template_id" id="order_template_id" value="<?php echo $_REQUEST['id']; ?>" />-->

<?php $onClick = (isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV")?"$('#divpopup',window.parent.document).remove()":"popup_hide();";?>
<?php if($_REQUEST['callFrom']!="WV"){?>
<div class="section_header alignLeft text12b boxhead" id="divHeader" ><span class="closeBtn" onclick="<?php echo  $onClick;?>"></span>Orders</div>
<?php }?>

<?php

$arln = count($arr_row_order); 
if($arln>0){

foreach($arr_row_order as $k => $row_order){

$pfs = $row_order["pfs"];
$db_order_type_id = $row_order["db_order_type_id"];

?>

<div id="div_imaging<?php echo $pfs; ?>" >
   <input type="hidden" name="id<?php echo $pfs; ?>" id="id<?php echo $pfs; ?>" value="<?php echo $row_order['id']; ?>" />	
   <div class="ele" id="div_order_name<?php echo $pfs; ?>">
   <?php 
	$var = (isset($row_order['name']))?$row_order['name']:'';
    if($_REQUEST['callFrom']!="WV" || empty($var)){?>
        <input type="text" class="form-control" name="ele_order_name<?php echo $pfs; ?>" id="ele_order_name<?php echo $pfs; ?>" value="<?php echo (isset($row_order['name']))?$row_order['name']:'';?>" >
    <?php }else{ 	
	echo '<input type="hidden" name="ele_order_name'.$pfs.'" id="ele_order_name'.$pfs.'" value="'.$var.'"  >';
		$str_name=$row_order['name'];
		$str_name=(strlen($str_name)>20) ? substr($str_name,0,20).".." : $str_name;
		echo "<label class=\"order_nm\" title=\"".$row_order['name']."\">".$str_name."</label>";
	}?>
        <input type="hidden" name="med_id<?php echo $pfs; ?>" id="med_id<?php echo $pfs; ?>" value="<?php echo $row_order['med_id']; ?>" > 
        <input type="hidden" name="ele_order_type<?php echo $pfs; ?>" id="ele_order_type<?php echo $pfs; ?>" value="<?php echo $db_order_type_id; ?>" > 
        <div class="label" >Order Name</div> 
    </div>
    <?php if($_REQUEST['callFrom']!="WV" ){ //|| empty($db_order_template_id)?>
    <div class="ele" id="div_order_template<?php echo $pfs; ?>"> 
            <?php print $str_tmp_select; ?>
             <span class="label">Order Template</span> 
    </div> 
    <?php }else{?>
    <input type="hidden" id="order_template<?php echo $pfs; ?>" name="order_template<?php echo $pfs; ?>" value="<?php echo $db_order_template_id;?>">
    <?php }?>
    
        <div class="ele" id="div_dosage<?php echo $pfs; ?>">
            <input type="text" class="form-control" name="ele_dosage<?php echo $pfs; ?>" id="ele_dosage<?php echo $pfs; ?>" value="<?php echo (isset($row_order['dosage']))?$row_order['dosage']:'';?>"  >
            <span class="label">Dosage</span> 
        </div>
        <div class="ele" id="div_qty<?php echo $pfs; ?>">
            <input type="text" class="form-control" name="ele_quantity<?php echo $pfs; ?>" id="ele_quantity<?php echo $pfs; ?>" value="<?php echo (isset($row_order['qty']))?$row_order['qty']:'';?>"  >
            <span class="label">Quantity</span> 
        </div>
        <div class="ele" id="div_sig<?php echo $pfs; ?>">
	<?php
		if(isset($row_order['sig']) && !empty($row_order['sig']) && strpos($row_order['sig'],"\n")!==false){
			$echo_sel = ""	;
			$ar_t_sig = explode("\n",$row_order['sig']);
			if(count($ar_t_sig)>0){
				foreach($ar_t_sig as $k => $v_t_sig){
					$v_t_sig = trim($v_t_sig);
					$v_t_sig = trim($v_t_sig,"\r\n");
					if(!empty($v_t_sig)){
						$echo_sel .= "<option value=\"".$v_t_sig."\">".$v_t_sig."</option>";
					}
				}
			}
			
			if(!empty($echo_sel)){
				$echo_sel = "<select name=\"ele_sig".$pfs."\" id=\"ele_sig".$pfs."\" class=\"form-control\" ><option value=\"\"></option>".$echo_sel."</select>";
			}
			echo $echo_sel;
		}else{
	?>
		<input type="text" class="form-control" name="ele_sig<?php echo $pfs; ?>" id="ele_sig<?php echo $pfs; ?>" value="<?php echo (isset($row_order['sig']))?$row_order['sig']:'';?>"  >	
	<?php
		}
	?>	
            <span class="label">Sig</span> 
        </div> 
	<div class="ele" id="div_refill<?php echo $pfs; ?>">
            <input type="text" class="form-control" name="ele_refill<?php echo $pfs; ?>" id="ele_refill<?php echo $pfs; ?>" value="<?php echo (isset($row_order['refill']))?$row_order['refill']:'';?>"  >
            <span class="label">Refill</span> 
         </div> 
         <div class="ele" id="div_ndc<?php echo $pfs; ?>">
            <input type="text" class="form-control" name="ele_ndc_code<?php echo $pfs; ?>" id="ele_ndc_code<?php echo $pfs; ?>" value="<?php echo (isset($row_order['ndccode']))?$row_order['ndccode']:'';?>"  >
            <span class="label">NDC Code</span> 
         </div>
	<?php if($_REQUEST['callFrom'] == "WV"){?> 
	    <div class="ele form-inline" id="div_site<?php echo $pfs; ?>">       
		<div class="radio"><input type="radio" id="elem_order_site<?php echo $pfs; ?>_ou" name="elem_order_site<?php echo $pfs; ?>" value="OU" <?php if($row_order['orders_site_text']=="OU"){ echo "CHECKED";} ?> ><label for="elem_order_site<?php echo $pfs; ?>_ou" ><b class="ou">OU</b></label></div>
		<div class="radio"><input type="radio" id="elem_order_site<?php echo $pfs; ?>_od" name="elem_order_site<?php echo $pfs; ?>" value="OD" <?php if($row_order['orders_site_text']=="OD"){ echo "CHECKED";} ?> ><label for="elem_order_site<?php echo $pfs; ?>_od" ><b class="od">OD</b></label></div>
		<div class="radio"><input type="radio" id="elem_order_site<?php echo $pfs; ?>_os" name="elem_order_site<?php echo $pfs; ?>" value="OS" <?php if($row_order['orders_site_text']=="OS"){ echo "CHECKED";} ?> ><label for="elem_order_site<?php echo $pfs; ?>_os" ><b class="os">OS</b></label></div>
		<div class="radio"><input type="radio" id="elem_order_site<?php echo $pfs; ?>_po" name="elem_order_site<?php echo $pfs; ?>" value="PO" <?php if($row_order['orders_site_text']=="PO"){ echo "CHECKED";} ?> ><label for="elem_order_site<?php echo $pfs; ?>_po" ><b >PO</b></label></div>
	     <span class="label">Site</span> 	
	     </div>
	     <div class="ele"  >
		<span class="glyphicon glyphicon-remove" title="Delete" onclick="deleteme('<?php echo $pfs; ?>')"></span> 	
	     </div>
	<?php }?> 
</div>
<div id="div_hgt<?php echo $pfs; ?>" ></div>

<?php
	$c++;
	} //end while
}//end block if id
//end order -- ?>

<?php  //add new meds  ?>

<div id="dvAddNewOrder">
</div>


<?php //if($_REQUEST['callFrom'] == "WV"){?> 
    
<?php //}?>

<?php  //add new meds  ?>

<div id="div_cpoe_error" ></div>

<div class="alignCenter bg5 btncon" >
	<div >
	<input type="button" name="elem_btnSave" value="&#10004; Save"  onclick="submit_frm_order();" class="btn btn-success" >
	<input type="button" name="elem_btnCancel" value="Cancel"  onclick="popup_hide();" class="btn btn-danger" >
	<input type="hidden" id="elem_lenOrders" name="elem_lenOrders" value="<?php echo $c; ?>" >
    <?php if($_REQUEST['callFrom'] == "WV"){?>
   <!-- <input type="button" name="elem_btnPrint" value="Print" class="dff_button" onClick="print_order(<?php echo $_REQUEST['chart_order_id'];?>)">-->
    <?php }?>
    </div>
</div>
</form>
</div>
<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
</html>
</body>
<?php }?>
<script>
function fn_set_typeahead(){
	cn_typeahead_order();
	/*
	order_type_id = $("#ele_order_type").val();
	switch(order_type_id){
		
		case "1": //Meds
			//var obj7 = new actb(document.getElementById('ele_order_name'),custom_array_medicine,"","",document.getElementById('med_id'),custom_array_medicine_id);
		break;
		
		default:
			//var obj7 = new actb(document.getElementById('ele_order_name'),custom_array_medicine,"","",document.getElementById('med_id'),custom_array_medicine_id);
			
		break;
	}
	*/
}

/*
var custom_array_medicine=custom_array_medicine_id=[];
<?php if($stringAllMedicine!=""){?>
		var custom_array_medicine= new Array(<?php echo remLineBrk($stringAllMedicine); ?>);
<?php }
	if($stringAllMedicineId!=""){?>
	var custom_array_medicine_id= new Array(<?php echo remLineBrk($stringAllMedicineId); ?>);
<?php } ?>
*/



//

function set_frame_height(){
	var frame = $('#frame_order', window.parent.document);
	height = $(document).height();
	width = $(document).width();
	height = height+50;
	frame.height(height);
	frame.width(width);
	<?php if($_REQUEST['callFrom'] == "WV"){?>
	$("#divHeader").width(width-20);
	<?php }?>
}

$(document).ready(function()
{
addnewmedorder();
setOrderOption(<?php echo $db_order_type_id;?>);
document_height = $(document).height();
document_width = $(document).width();
<?php if($_REQUEST['callFrom'] != "WV"){?>
$('#divpopup').draggable({"handle":"#divHeader"});
<?php }?>

//test
$( "#divorder_multiopts" ).scroll(function() { var v=$( "#divaddbtn" ).css("top"); v=parseInt($( "#divorder_multiopts" ).scrollTop())+10; $( "#divaddbtn" ).css( {"top":v+"px"} );});

//
<?php if($_REQUEST['callFrom'] == "WV"){?>
parent.$("#loadingdiv").remove();
<?php }?>

fn_set_typeahead();

//
top.show_loading_image('hide');

//
<?php if($_REQUEST['callFrom'] == "WV"){?>
$('#divpopup',window.parent.document).draggable();
set_frame_height(); 
<?php }?>

});

</script>