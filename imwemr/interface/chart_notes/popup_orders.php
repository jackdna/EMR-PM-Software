<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
<!DOCTYPE html>
<html>
<head>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/wv_landing.css" rel="stylesheet">
<?php }?>
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
textarea[id*=ele_sig]{width:100px;height:32px;}

/* --  -- */
#div_smart_tags_options{top:200px;left:400px; width:300px; z-index:999; display:none;}
#loading_img{display:none; top:50px; left:30%; z-index:1000; position:absolute;}
#divpopup_inner{height:90%;width:100%;display:block;}
#pop_up_responsible{display:none; width:750px; top:60px;left:300px;z-index:1002; position:absolute;background-color: #F4F9EE;}
.tbl100{width:100%;}
#all_responsible, #selected_responsible, #all_dx_code, #selected_dx_code, #all_lad_rad_type, #selected_lad_rad_type{width:350px; height:200px!important;}
#pop_up_dx_code, #pop_up_lad_rad_type{display:none; width:750px; top:110px;left:100px; z-index:1002; position:absolute;background-color: #F4F9EE;}
#ele_order_type{width:100px;}
.ele input[type=text]{width:100px;}
#ele_information{width:300px;}
#divorder_multiopts{max-height:600px;overflow:auto;position:relative;}
#divaddbtn{/*position:absolute;top:0px;right:210px;z-index:2;width:20px;height:20px;background-image:url('../../../../images/acc_add_img.png');*/display:none;}
#div_cpoe_error{width:100%;max-height:150px;overflow:auto;}
.dvsep{width:900px; height:2px;}
#ele_dx_code, #ele_lad_rad_type, #ele_responsible_person{height:30px!important; width:150px}
#ele_instruction{width:300px; height:30px!important;}
#div_order_template_draw{margin-left:10px;  text-align:center; height:200px;}
#dvbtns{position:absolute; bottom:0px; width:100%;padding-top:5px;padding-bottom:5px;text-align:center; padding-left:10px}
#div_fdb_search{position:absolute;top:10px;left:300px;width:400px;max-height:550px; background-color:white; margin:0px; display:none; z-index:9999;border:1px solid black;padding:2px;}
#fdb_content{margin:0px;overflow:auto;max-height:150px;margin-bottom:10px}
#sbmtFrm{width:80px;}
#fdb_btn{margin:0px;max-height:100px;margin-bottom:10px; text-align:center;}
#procssslbl{ margin-left:5px;line-height:25px;font-weight:bold;background-color:green;color:white;padding:2px;border:1px solid black; }
#div_template_content{height:500px;width:840px; overflow:hidden;overflow-x:auto;overflow-y:auto;text-align:center;border:1px solid black}
.editr{ width:100%;height:450px; }
#divorder_multiopts{max-height:600px;overflow:auto;position:relative;}
.alignCenter{text-align:center;}
#fdb_content label{width:15px!important;}
</style>

<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/ckeditor/ckeditor.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>

<script>
zPath="<?php echo $GLOBALS['rootdir']; ?>";
function get_template(obj, mode){
	top.show_loading_image('show',300);
	var order_template_id = obj.value;
	CKEDITOR.template_id = order_template_id;

	var tempMode = 'get_template';
	if(mode && typeof(mode) !== 'undefined') tempMode = mode;
	
	$('#frm_order #mode').val(tempMode);
	$('#order_template_id').val(order_template_id);
	var frm_data = $('#frm_order').serialize();
	frm_data += "&order_template_id="+order_template_id;
	var id = $("#id").val();
	var assi = $("#elem_plan_num").val();
	var u = zPath+"/chart_notes/requestHandler.php?elem_formAction=GetOrderDetail&id="+id+"&callFrom=WV&assi="+assi+"&form_id=&chart_order_id=&req_ptwo=1";
	//CKEDITOR.instances.FCKeditor1.setData("");
	$.ajax({
		url:u,
		type:"POST",
		data:frm_data,
		success:function(response){
			$('#div_order_template_draw').show();
		    if(typeof(mode) !== 'undefined'){
    		    var parameters = "order_details_popup=first_time_load&order_id="+id;
    	        //CKEDITOR.instances.FCKeditor1.setData("");
    	        $.post(zPath+"/admin/order_sets/Order/orderList.php", parameters, function(d){				
    	        	CKEDITOR.instances.FCKeditor1.setData(d);
    			});
		    }else{
		    	CKEDITOR.instances['FCKeditor1'].setData(response);
			    CKEDITOR.instances.FCKeditor1.setData(response);
		    }
			top.show_loading_image('hide');
		}
	});
	top.show_loading_image('hide');
}
function get_order_template_option(order_type_id){	
	$('#mode').val('get_order_template_option');
	frm_data = $('#frm_order').serialize();
	frm_data += "&order_type_id="+order_type_id;
	frm_data += "&req_ptwo=1";
	frm_data += "&elem_formAction=GetOrderDetail";
	WRP = top.WRP;
	var id = $("#id").val();
	var assi = $("#elem_plan_num").val();
	var u = zPath+"/chart_notes/requestHandler.php";
	$.ajax({
		url:u,
		type:"POST",
		data:frm_data,
		complete:function(r){//alert(r.responseText)
			$("#div_order_template").html(r.responseText+"<span class='label'>Order Template</span> ");
			//$("#order_template").bind('change',get_template);
		}	
	});
}
function setOrderOption(obj){
	if(typeof(obj) == "object")
	var val = obj.value;
	else
	var val = obj;
	if(typeof(val)!="undefined")
	val = val.toString();
	get_order_template_option(val);
	fn_set_typeahead();
	var editor = CKEDITOR.instances['FCKeditor1'];
	$("#div_order_template_draw").show();
	$("#divaddbtn").hide();
	$("#div_order_template").show();	
	
	//remove additional if any--
	if(val!=1){
	$("input[name*=ele_dosage], input[name*=ele_quantity], :input[name*=ele_sig], input[name*=ele_refill], input[name*=ele_ndc_code], input[name*=ele_fdb_code]").each(function(indx){		
		if(this.name!="ele_dosage" && this.name!="ele_quantity" && this.name!="ele_sig" && this.name!="ele_refill" && this.name!="ele_ndc_code" && this.name!="ele_fdb_code" ){			
			$(this).parent().remove();
		}
	});
	$("#divorder_multiopts br").remove();
	}
	//--
	
	
	switch(val){
		case "5": //Information/Instructions
			//editor.resize( '', '<?php echo $doc_height - 120;?>');
			$("#div_information").show();
			$("#div_codes").show();
			$("#div_information").show();
			//$("#div_dx_code").show();
			$("#div_snomed").show();
		  
			$("#div_responsible_person").hide();
			$("#div_cpt_code").hide();
			$("#div_dx_code").hide();
			$("#div_loinc").hide();
			$("#div_test_name").hide();
			$("#div_instruction").hide();
			$("#div_lad_rad_type").hide();
			$("#div_meds").hide();
			$("#div_dosage").hide();
			$("#div_qty").hide();
			$("#div_sig").hide();
			$("#div_refill").hide();
			$("#div_ndc,#div_fdb").hide();
			//$("#div_information").hide();
		break;
		case "1": //Meds
			//editor.resize( '', '<?php echo $doc_height - 160;?>');
			
			var id = $("#id").val();
			//if(id==""){ //do when new insertion
			<?php if((isset($flgInsideAdmin) && $flgInsideAdmin==1)){ ?>
			$("#div_order_template_draw, #div_order_template").hide();			
			<?php } ?>
			$("#divaddbtn").css('display', 'table-cell');			
			//}
			
			$("#div_meds").show();
			$("#div_dosage").show();
			$("#div_qty").show();
			$("#div_sig").show();
			$("#div_refill").show();
			$("#div_ndc,#div_fdb").show();
			
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
		case "4": //Procedure/Sx
			//editor.resize( '', '<?php echo $doc_height - 200;?>');
			$("#div_responsible_person").show();
			$("#div_instruction").show();
			$("#div_lad_rad_type").show();
			$("#div_codes").show();
			$("#div_cpt_code").show();
			$("#div_snomed").show();
			//$("#div_dx_code").show();
			
			$("#div_meds").hide();
			$("#div_dosage").hide();
			$("#div_qty").hide();
			$("#div_sig").hide();
			$("#div_refill").hide();
			$("#div_ndc,#div_fdb").hide();
			$("#div_information").hide();
			$("#div_dx_code").hide();
			$("#div_loinc").hide();
		break;
		case "2": //Labs
			//editor.resize( '', '<?php echo $doc_height - 200;?>');
			$("#div_codes").show();
			
			$("#div_loinc").show();
			$("#div_snomed").show();
			$("#div_dx_code").show();
			
			$("#div_responsible_person").show();
			$("#div_test_name").show();
			$("#div_instruction").show();
			
			$("#div_lad_rad_type").hide();
			$("#div_cpt_code").hide();
			
			$("#div_meds").hide();
			$("#div_dosage").hide();
			$("#div_qty").hide();
			$("#div_sig").hide();
			$("#div_refill").hide();
			$("#div_ndc,#div_fdb").hide();
			$("#div_information").hide();
			break;
		case "3": //Imaging/Rad
		default:
			//editor.resize( '', '<?php echo $doc_height - 200;?>');
			$("#div_codes").show();
			$("#div_cpt_code").show();
			$("#div_loinc").show();
			$("#div_snomed").show();
			
			$("#div_responsible_person").show();
			$("#div_test_name").show();
			$("#div_instruction").show();
			$("#div_lad_rad_type").show();
			
			$("#div_meds").hide();
			$("#div_dosage").hide();
			$("#div_qty").hide();
			$("#div_sig").hide();
			$("#div_refill").hide();
			$("#div_ndc,#div_fdb").hide();
			$("#div_information").hide();
			$("#div_dx_code").hide();
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
	var str = CKEDITOR.instances.FCKeditor1.getData();
	$('#hdnCKeditor').val(str);
	var check = false;
	frm = document.frm_order;
	//parent.parent.show_loading_image('none');
	msg = '';
	if(typeof(frm.ele_order_name)!="undefined" && typeof(frm.ele_order_name)!= null && frm.ele_order_name.value==""){
		msg = msg + '&bull; Please Enter Order Name<br>';
		frm.ele_order_name.className = 'mandatory';
		if(check == false){
			check = true;
			frm.ele_order_name.focus()
		}
	}
	if(msg == ''){
		document.getElementById('save_frm').value='save';
		<?php if(!isset($_REQUEST['callFrom'])){?>
		top.show_loading_image('block');
		//frm.submit();
		var prm = $("#frm_order").serialize();		
		prm += "&req_ptwo=1";
		prm += "&elem_formAction=saveOrder";
		top.show_loading_image('block',300);
		$.post(zPath+"/chart_notes/requestHandler.php", prm, function(d){
			if(d=="0"){ window.location.replace("orderList.php"); }
		});
		
		<?php }elseif($_REQUEST['callFrom'] == "WV"){?>
		top.show_loading_image('block',300);
		parent.saveOrderDetail_new(document.frm_order,1,'<?php echo  $_REQUEST['assi']; ?>');
		<?php }?>
	}else{
		if(typeof(fAlert)!="undefined"){	fAlert(msg);}
		else if(typeof(top.fAlert)!="undefined"){ top.fAlert(msg);}
		
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
            "<input type=\"text\" name=\"ele_dosage"+indx+"\" id=\"ele_dosage"+indx+"\" value=\"\" class=\"form-control\"  >"+
            "<span class=\"label\">Dosage</span> "+
	     "<input type=\"hidden\" name=\"med_id"+indx+"\" id=\"med_id"+indx+"\" value=\"\" > "+
	     "<input type=\"hidden\" name=\"id"+indx+"\" id=\"id"+indx+"\" value=\"\" />"+
        "</div>"+
        "<div class=\"ele\" id=\"div_qty"+indx+"\">"+
        "     <input type=\"text\" name=\"ele_quantity"+indx+"\" id=\"ele_quantity"+indx+"\" value=\"\" class=\"form-control\"  >"+
        "     <span class=\"label\">Quantity</span> "+
        "</div>"+
        "<div class=\"ele\" id=\"div_sig"+indx+"\">";
       
       <?php if((isset($flgInsideAdmin) && $flgInsideAdmin==1)){ ?>
	 str+="     <textarea name=\"ele_sig"+indx+"\" id=\"ele_sig"+indx+"\" class=\"form-control\" ></textarea>";
	<?php }else{ ?>
	 str+="     <input type=\"text\" name=\"ele_sig"+indx+"\" id=\"ele_sig"+indx+"\" value=\"\" class=\"form-control\"  >";
	<?php } ?>
	
        str+="     <span class=\"label\">Sig</span> "+
        "</div>  "+
        "<div class=\"ele\" id=\"div_refill"+indx+"\">"+
        "     <input type=\"text\" name=\"ele_refill"+indx+"\" id=\"ele_refill"+indx+"\" value=\"\" class=\"form-control\" >"+
        "     <span class=\"label\">Refill</span> "+
        "</div> "+
        "<div class=\"ele\" id=\"div_ndc"+indx+"\">"+
        "    <input type=\"text\" name=\"ele_ndc_code"+indx+"\" id=\"ele_ndc_code"+indx+"\" value=\"\" class=\"form-control\"  >"+
        "    <span class=\"label\">NDC Code</span> "+
        "</div> "+
        "<div class=\"ele\" id=\"div_fdb"+indx+"\">"+
        "    <input type=\"text\" name=\"ele_fdb_code"+indx+"\" id=\"ele_fdb_code"+indx+"\" value=\"\"  readonly onClick=\"check_fdb(this)\" class=\"form-control\">"+
        "    <span class=\"label\">FDB ID</span> "+
        "</div>";
	//
	$("#divorder_multiopts").append(str);
	
}

function disProcessing(v){
	if(v==1){		
		//$("#loading_img").append("<label id='procssslbl' >Processing! Please wait.</label>");
		$("#loading_img").show();	
		top.show_loading_image('show','200', 'Processing! Please wait...');	
	}else{	
		$("#procssslbl").remove();
		$("#loading_img").hide();	
		top.show_loading_image('hide');	
	}
}

function set_emdeon_off(){	
	$("#elem_donot_check_emdeon").val("1");
	//alert("Press save button again to save anyway! ");
}

</script>
<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
</head>
<body class="bg2">
<?php } ?>

<div class="div_popup white border" id="div_smart_tags_options"  >
	<div class="section_header"><span class="closeBtn" onClick="$('#div_smart_tags_options').hide();"></span>Smart Tag Options</div>
</div>
<div align="center" id="loading_img" >
	Loading...
</div>
<div id="divpopup_inner" class="bg2"><?php  //echo $_SESSION['wn_height'];?>

<form name="frm_order" id="frm_order" method="post" action="" onSubmit="return submit_frm_order();">
<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
<input type="hidden" name="save_frm" id="save_frm" value="save_frm">
<input type="hidden" name="hdnCKeditor" id="hdnCKeditor" value="">
<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>" />
<input type="hidden" name="order_set_associate_details_id" id="order_set_associate_details_id" value="<?php echo $_REQUEST['chart_order_id']; ?>" />
<?php if($_REQUEST['callFrom'] == "WV"){?>
<input type="hidden" name="elem_plan_num" id="elem_plan_num" value="<?php echo $_REQUEST['assi'];?>">
<input type="hidden" id="elem_donot_check_emdeon" name="elem_donot_check_emdeon" value="">
<?php }?>
<!--<input type="hidden" name="order_template_id" id="order_template_id" value="<?php echo $_REQUEST['id']; ?>" />-->

<div class="alignLeft W100per  div_shadow"  id="pop_up_responsible" >
<table class="tblBg tbl100 alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
    <tr >
        <td colspan="3" class="text_10b section_header" >Please Add/Remove Responsible Person(s) using Arrow Buttons.</td>
    </tr>
    <tr class="grid_heading">
        <td class="text_10b subheading">List of Responsible Person</td>
        <td class="subheading">&nbsp;</td>
        <td class="text_10b subheading">List of Selected Responsible Person</td>
    </tr>
    <tr>
        <td class="alignCenter"  >
	    <select  class="input_text_10 border form-control"  id="all_responsible" name="all_responsible[]"  size="15" multiple="multiple">
	       <?php print $userOption; ?>
	    </select>
	</td>
	    <td class="tdbtnslct" style="">
		<input class="button btn btn-success" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_responsible','all_responsible','selected_responsible','all');">
		<input class="button btn btn-success" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_responsible','all_responsible','selected_responsible','single');">
		<input class="button btn btn-success" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_responsible','selected_responsible','all_responsible','single_remove');">
		<input class="button btn btn-success" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_responsible','selected_responsible','all_responsible','all_remove');">        
	    </td>
	    <td class="alignCenter">
		<select class="input_text_10 border form-control"  id="selected_responsible" name=""  size="15" multiple="multiple"></select>
	    </td>
    </tr>
    <tr>
        <td colspan="3" class="alignCenter"><br><input type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_responsible','selected_responsible','ele_responsible_person','div_responsible_person','done')">
            &nbsp;
            <input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_responsible','selected_responsible','ele_responsible_person','div_responsible_person','close')">
        </td>
    </tr>
</table>
</div>

<div class="alignLeft W100per  div_shadow"  id="pop_up_dx_code" >
<table class="tblBg tbl100 alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
    <tr >
        <td colspan="3" class="text_10b section_header" >Please Add/Remove Responsible Person(s) using Arrow Buttons.</td>
    </tr>
    <tr class="grid_heading">
        <td class="text_10b subheading">List of Dx Code</td>
        <td class="subheading">&nbsp;</td>
        <td class="text_10b subheading">List of Selected Dx Code</td>
    </tr>
    <tr>
        <td class="alignCenter"  >
	    <select  class="input_text_10 border form-control" id="all_dx_code" name="all_dx_code[]"  size="15" multiple="multiple">
	       <?php print $dxOptions; ?>
	    </select>
	</td>
	<td style="vertical-align:top;">
		<input class="button btn btn-success" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','all');">
		<input class="button btn btn-success" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','single');">
		<input class="button btn btn-success" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_dx_code','selected_dx_code','all_dx_code','single_remove');">
		<input class="button btn btn-success" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_dx_code','selected_dx_code','all_dx_code','all_remove');">
        
	</td>
	<td class="alignCenter">
		<select class="input_text_10 border form-control"  id="selected_dx_code" name="selected_dx_code"  size="15" multiple="multiple"></select>
	</td>
    </tr>
    <tr>
        <td colspan="3" class="alignCenter"><br><input type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_dx_code','selected_dx_code','ele_dx_code','div_dx_code','done')">
            &nbsp;
            <input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_dx_code','selected_dx_code','ele_dx_code','div_dx_code','close')">
        </td>
    </tr>
</table>
</div>

<div class="alignLeft W100per  div_shadow"  id="pop_up_lad_rad_type" >
<table  class="tblBg tbl100 alignLeft table_collapse_autoW border" cellspacing="2" cellpadding="2" >
    <tr >
        <td colspan="3" class="text_10b section_header" >Please Add/Remove Responsible Person(s) using Arrow Buttons.</td>
    </tr>
    <tr class="grid_heading">
        <td class="text_10b subheading">List of Lad Rad Type</td>
        <td class="subheading">&nbsp;</td>
        <td class="text_10b subheading">List of Selected Lad Rad Type</td>
    </tr>
    <tr>
        <td class="alignCenter"  >
    <select  class="input_text_10 border form-control"  id="all_lad_rad_type" name="all_lad_rad_type[]"  size="15" multiple="multiple" >										
        
       <?php print $labOptionData; ?>
    </select>
    </td>
    <td style="vertical-align:top;">
        <input class="button btn btn-success" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_lad_rad_type','all_lad_rad_type','selected_lad_rad_type','all');">
        <input class="button btn btn-success" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_lad_rad_type','all_lad_rad_type','selected_lad_rad_type','single');">
        <input class="button btn btn-success" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_lad_rad_type','selected_lad_rad_type','all_lad_rad_type','single_remove');">
        <input class="button btn btn-success" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_lad_rad_type','selected_lad_rad_type','all_lad_rad_type','all_remove');">
        
    </td>
    <td class="alignCenter">
    <select class="input_text_10 border"  id="selected_lad_rad_type" name="selected_lad_rad_type"  size="15" multiple="multiple" class="form-control"></select>
    </td>
    </tr>
    <tr>
        <td colspan="3" class="alignCenter"><br><input type="button" class="btn btn-success"  value="Done" onClick="selected_ele_close('pop_up_lad_rad_type','selected_lad_rad_type','ele_lad_rad_type','div_lad_rad_type','done')">
            &nbsp;
            <input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_lad_rad_type','selected_lad_rad_type','ele_lad_rad_type','div_lad_rad_type','close')">
        </td>
    </tr>
</table>
</div>

<?php $onClick = (isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV")?"$('#divpopup',window.parent.document).remove()":"popup_hide();";?>
<?php if($_REQUEST['callFrom']!="WV"){?>
<div class="purple_bar" id="divHeader" ><span class="glyphicon glyphicon-remove pull-right" onclick="<?php echo  $onClick;?>"></span>Orders</div>
<?php }?>
<div id="div_imaging" >
    <div class="ele" id="div_order_type">
    <?php $display = (isset($_REQUEST['id']) && !empty($_REQUEST['id']))?"none":"block";
		 if(isset($_REQUEST['id']) && !empty($_REQUEST['id']))
		 echo "<div id='lbl_order_type'>".$order_type[$db_order_type_id]."</div>";
		?>
        <select id="ele_order_type" name="ele_order_type" onchange="setOrderOption(this)" style="display:<?php echo $display;?>" class="form-control">
            <?php print $type_option_val; ?>
        </select>
        <div class="label" >Order Type</div> 
    </div>
    <div class="ele" id="div_order_name">
    <?php 	
    if($_REQUEST['callFrom']!="WV" || empty($var)){?>
        <input type="text" name="ele_order_name" id="ele_order_name" value="<?php echo (isset($row_order['name']))?$row_order['name']:'';?>" class="form-control" >
    <?php }else{ 
	
	echo '<input type="hidden" name="ele_order_name" id="ele_order_name" value="'.$var.'"  >';
		echo $row_order['name'];
	}?>
        <input type="hidden" name="med_id" id="med_id" value="<?php echo $row_order['med_id']; ?>" >
        <input type="hidden" name="med_id" id="curr_fdb_id" value="<?php echo $row_order['med_id']; ?>" > 
        <input type="hidden" name="med_id" id="old_med_name" value="<?php echo $row_order['med_id']; ?>" > 
        <div class="label" >Order Name</div> 
    </div>
    <?php if($_REQUEST['callFrom']!="WV" || empty($db_order_template_id)){?>
    <div class="ele" id="div_order_template"> 
            <?php print $str_tmp_select; ?>
             <span class="label">Order Template</span> 
    </div> 
    <?php }else{?>
    <input type="hidden" id="order_template" name="order_template" value="<?php echo $db_order_template_id;?>">
    <?php }?>
   <!-- <div class="groupopts" style="max-height:600px;overflow:auto;position:relative;">-->
     <div class="ele" id="div_test_name">
       <input type="text" name="ele_test_name" id="ele_test_name" value="<?php echo (isset($row_order['testname']))?$row_order['testname']:'';?>" class="form-control" >
        <span class="label">Test Name</span> 
    </div> 
     <div class="ele" id="div_information">
        <input type="text" name="ele_information" id="ele_information" value="<?php echo (isset($row_order['inform']))?$row_order['inform']:'';?>" class="form-control" >
         <span class="label">Information</span> 
    </div> 
     <div class="ele" id="div_cpt_code">
        <input type="text" name="ele_cpt_code" id="ele_cpt_code" value="<?php echo (isset($row_order['cpt_code']))?$row_order['cpt_code']:'';?>" class="form-control"  >
         <span class="label">CPT Code</span> 
    </div>
    <div class="ele" id="div_loinc">
        <input type="text" name="ele_loinc" id="ele_loinc" value="<?php echo (isset($row_order['loinc_code']))?$row_order['loinc_code']:'';?>" class="form-control"  >
         <span class="label">LOINC</span> 
    </div>  
    <div class="ele" id="div_snomed">
            <input type="text" name="ele_snowmed" id="ele_snowmed" value="<?php echo (isset($row_order['snowmed']))?$row_order['snowmed']:'';?>" class="form-control" >
             <span class="label">SNOWMED CODE</span> 
     </div>
     
     <?php if($_REQUEST['callFrom'] == "WV"){?> 
    <div class="ele" id="div_site">       
	<div class="radio radio-inline"><input type="radio" name="elem_order_site" id="elem_order_site_ou" value="OU" <?php if($row_order['orders_site_text']=="OU"){ echo "CHECKED";} ?> ><label for="elem_order_site_ou" >OU</label></div>
	<div class="radio radio-inline"><input type="radio" name="elem_order_site" id="elem_order_site_od" value="OD" <?php if($row_order['orders_site_text']=="OD"){ echo "CHECKED";} ?> ><label for="elem_order_site_od" >OD</label></div>
	<div class="radio radio-inline"><input type="radio" name="elem_order_site" id="elem_order_site_os" value="OS" <?php if($row_order['orders_site_text']=="OS"){ echo "CHECKED";} ?> ><label for="elem_order_site_os" >OS</label></div>
     <span class="label">Site</span> 	
     </div>
     
    <?php }?>
   
   <div id="divorder_multiopts" >          
        <div class="ele" id="div_dosage">
            <input type="text" name="ele_dosage" id="ele_dosage" value="<?php echo (isset($row_order['dosage']))?$row_order['dosage']:'';?>" class="form-control" >
            <span class="label">Dosage</span> 
        </div>
        <div class="ele" id="div_qty">
            <input type="text" name="ele_quantity" id="ele_quantity" value="<?php echo (isset($row_order['qty']))?$row_order['qty']:'';?>" class="form-control" >
            <span class="label">Quantity</span> 
        </div>
        <div class="ele" id="div_sig">
	<?php if((isset($flgInsideAdmin) && $flgInsideAdmin==1) ){?>
	   <textarea name="ele_sig" id="ele_sig" class="form-control" ><?php echo (isset($row_order['sig']))?$row_order['sig']:'';?></textarea>
	<?php }else{ ?>
            <input type="text" name="ele_sig" id="ele_sig" value="<?php echo (isset($row_order['sig']))?$row_order['sig']:'';?>" class="form-control" >
	<?php } ?>    
            <span class="label">Sig</span> 
        </div>  
        <div class="ele" id="div_refill">
            <input type="text" name="ele_refill" id="ele_refill" value="<?php echo (isset($row_order['refill']))?$row_order['refill']:'';?>" class="form-control" >
            <span class="label">Refill</span> 
        </div> 
        <div class="ele" id="div_ndc">
            <input type="text" name="ele_ndc_code" id="ele_ndc_code" value="<?php echo (isset($row_order['ndccode']))?$row_order['ndccode']:'';?>" class="form-control" >
            <span class="label">NDC Code</span> 
        </div>	
        <div class="ele" id="div_fdb">
            <input type="text" name="ele_fdb_code" id="ele_fdb_code" value="<?php echo (isset($row_order['fdb_id']))?$row_order['fdb_id']:'';?>"  readonly onClick="check_fdb(this)" class="form-control" >
            <span class="label">FDB ID</span> 
        </div>	
	
	<?php
		//$tmpdisplay = (isset($flgInsideAdmin) && $flgInsideAdmin==1) ? "block":"none";
		if((isset($flgInsideAdmin) && $flgInsideAdmin==1) ){
	?>
	<div id="divaddbtn" title="Add More" class="glyphicon glyphicon-plus"  onclick="addmoreMeds()"></div>
	<?php 
	
		//add for edit				
		echo $htm_addmoreMeds4Edit;
	
	} 		
	?>
	
	</div>	
	
	
	
	<!--</div>--><?php //-- group -- ?>
   <!-- <div style="margin:1px;display:none; width:610px; float:left" id="div_meds">
    
      
</div>-->
    
</div>

<div class="vhgt" ></div>

<div id="div_codes">
    <div class="ele" id="div_responsible_person" onClick="return popup_dbl('pop_up_responsible','all_responsible','selected_responsible','','ele_responsible_person')">
        <select name="ele_responsible_person[]" id="ele_responsible_person" multiple size="3" class="form-control" >
        <?php echo $db_resp_per_options;?>
        </select>
        <span class="label"><span  class="a_clr1">Responsible Person</span></span> 
    </div>
    <div class="ele" id="div_dx_code" onClick="return popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','','ele_dx_code')" >
        <select  name="ele_dx_code[]" id="ele_dx_code" multiple size="3" class="form-control"  >
        <?php echo $db_dxOptions;?>
        </select>
         <span class="label"><span class="a_clr1">Dx Code</span></span> 
    </div>
    <div class="ele" id="div_lad_rad_type" onClick="return popup_dbl('pop_up_lad_rad_type','all_lad_rad_type','selected_lad_rad_type','','ele_lad_rad_type')">
    <select name="ele_lad_rad_type[]" id="ele_lad_rad_type" multiple size="1" class="form-control" >
    <?php print $db_labOptionData; ?>
    </select>
        <!--<input type="text" name="ele_lad_rad_type" id="ele_lad_rad_type" value="<?php echo (isset($row_order['order_lab_name']))?$row_order['order_lab_name']:'';?>" style="width:100px;" >-->
         <span class="label">Lad/Rad Type</span> 
    </div>
     <div class="ele" id="div_instruction">
        <textarea name="ele_instruction" id="ele_instruction" class="form-control" ><?php echo (isset($row_order['instruction']))?$row_order['instruction']:'';?></textarea>
         <span class="label">Instruction</span> 
    </div> 
</div>

<div class="vhgt"></div>

<?php if($_REQUEST['callFrom'] == "WV"){?>
     <div id="div_cpoe_error" ></div>
<?php }?>

<div class="vhgt"></div>

<div id="div_order_template_draw">
<?php	
	/*
	include_once($GLOBALS['srcdir']."/ckeditor/ckeditor.php");
	$CKEditor = new CKEditor();
	$CKEditor->basePath = $GLOBALS['webroot'].'/library/ckeditor/';
	if($_REQUEST['callFrom'] == "WV"){
		$CKEditor->config['height'] = $doc_height -200;//385;
	}else
		$CKEditor->config['height'] = $doc_height - 270;//360;
	$CKEditor->config['width'] = $doc_width - 30;
	$CKEditor->returnOutput = true;
	$editor = $CKEditor->editor("FCKeditor1", $db_template_content);
	echo $editor;
	*/
	//
	echo "<textarea name=\"FCKeditor1\" id=\"FCKeditor1\" class=\"editr\" >".$db_template_content."</textarea>"; //
	
?>
<div id="hold_temp_smarttag_data" class="hide"></div>
</div>
<div class="vhgt"></div>
<div class="alignCenter bg5" id="dvbtns" >
	<div >
	<input type="button" name="elem_btnSave" value="&#10004; Save"  onclick="submit_frm_order();" class="btn btn-success">
	<input type="button" name="elem_btnCancel" value="Cancel"  onclick="popup_hide();" class="btn btn-danger">
    <?php if($_REQUEST['callFrom'] == "WV"){?>
   <!-- <input type="button" name="elem_btnPrint" value="Print" class="dff_button" onClick="print_order(<?php echo $_REQUEST['chart_order_id'];?>)">-->
    <?php }?>
    </div>
</div>
</form>

<div id="div_fdb_search"  class="section">
    <div class="text12b purple_bar ">FDB Results</div>
    <div id="fdb_content" ></div>
    <div id="fdb_btn">
        <input name="sbmtFrm" id="sbmtFrm" type="button" class="btn btn-danger" value="Close" onClick="$('#div_fdb_search').hide();">
    </div>
</div>

</div>
<?php if(isset($_REQUEST['callFrom']) && $_REQUEST['callFrom'] == "WV"){?>
</html>
</body>
<?php }?>
<script>
function check_fdb(t){
	var medName = $('#ele_order_name').val();
	if(medName==''){alert('No medication name entered.');return;}
	$('#curr_fdb_id').val($(t).attr('id'));
	if($('#old_med_name').val()==medName){
		$('#div_fdb_search').show();
	}else{
		$('#fdb_content').html('');
		if($('#ele_order_type').val()!='1') return;
		if(medName!=""){
			top.show_loading_image('hide');			
			top.show_loading_image('show','200', 'Checking FDB ID...');
			$.ajax({
				type: "POST",
				url: zPath+"/admin/console/Medication_type_ahead/check_fdb.php?med_name="+encodeURI(medName),
				complete: function(r){
					response = r.responseText;
					if(response != null && typeof(response)!='undefined' && response!=''){
						$('#fdb_content').html(response);
						$('#div_fdb_search').show();
						$('#old_med_name').val(medName);
					}
					top.show_loading_image('hide');
				}
			});
		}
		$('#old_med_name').val(medName);
	}
}
function fill_fdb_code(fdb,index){
	$('#'+$('#curr_fdb_id').val()).val(fdb);
}

function fn_set_typeahead(){
	order_type_id = $("#ele_order_type").val();
	
	switch(order_type_id){
		
		case "1": //Meds
			//var obj7 = new actb(document.getElementById('ele_order_name'),custom_array_medicine,"","",document.getElementById('med_id'),custom_array_medicine_id);
			cn_typeahead_order();
		break;
		case "2": //Labs
		case "3": //Imaging/Rad
		case "4": //Procedure/Sx
		case "5": //Information/Instructions
		default:
			//var obj7 = new actb(document.getElementById('ele_order_name'),'',"","",document.getElementById('med_id'),'');
		break;
	}
	
}



<?php if($_REQUEST['callFrom'] != "WV"){?>
var dw_height = <?php echo $doc_height-85;?>;
<?php }else{?>
var dw_height = 510;
<?php }?>

   


var smart_tag_current_object = new Object;
function display_tag_options(){
	var WRP = '<?php echo $GLOBALS['webroot']; ?>';
	$('#div_smart_tags_options').html('<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>Smart Tag Options</div><img src="../../../../images/ajax-loader.gif">');
	$('#div_smart_tags_options').show();
	var parentId = $('#smartTag_parentId').val();
	$.ajax({
		type: "GET",
		//url: WRP+"/interface/admin/documents/smart_tags/ajax.php?do=getTagOptions&id="+parentId,
		url: WRP+"/interface/chart_notes/requestHandler.php?elem_formAction=getTagOptions&id="+parentId,
		success: function(resp){
			$('#div_smart_tags_options').html(resp);
		}
	});
}

function replace_tag_with_options(){
	var strToReplace = '';
	var parentId = $('#smartTag_parentId').val();
	
	var arrSubTags = document.all.chkSmartTagOptions;
	$(arrSubTags).each(function (){
		if($(this).attr('checked')){
			if(strToReplace=='')
				strToReplace +=  $(this).val();
			else
				strToReplace +=  ', '+$(this).val();
		}
	});
	
	/*--GETTING FCK EDITOR TEXT--*/

	fram = 'FCKeditor1___Frame';
	//FCKtext = window.frames[fram].FCK.GetData();//SetData('aaa',true);//xEditingArea.frames[0].src;
	FCKtext = CKEDITOR.instances['FCKeditor1'].getData();
	$('#hold_temp_smarttag_data').html(FCKtext);

	if(strToReplace!='' && smart_tag_current_object){	//	alert(smart_tag_current_object==$('.cls_smart_tags_link[id="'+parentId+'"]'));
		$('.cls_smart_tags_link[id="'+parentId+'"]').html(strToReplace);
		//$(smart_tag_current_object).html(strToReplace);
		RemoveString = window.location.protocol+'//'+window.location.host; //.innerHTML BUG adds host url to relative urls.
		/*
		fram = 'FCKeditor1___Frame';
		FCKtext = window.frames[fram].FCK.GetData();//SetData('aaa',true);//xEditingArea.frames[0].src;
		$('#hold_temp_smarttag_data').html(FCKtext);
		*/
		var strippedData = $('#hold_temp_smarttag_data').html();
		strippedData = strippedData.replace(new RegExp(RemoveString, 'g'),'');

		//window.frames[fram].FCK.SetData(strippedData,true);
		CKEDITOR.instances['FCKeditor1'].setData(strippedData,function(){});
		$('#div_smart_tags_options').hide();
	}else{
		alert('Select Options');
	}
}
<?php //}?>		
function print_order(chart_order_id){
	parent.print_order(chart_order_id);
}
function set_frame_height(){	
	var t = $("#div_cpoe_error").html()||"";
	if(t!=""){	editor.resize( '', '200');}
	var frame = $('#frame_order', window.parent.document);
	height = $(document).height();
	width = $(document).width();
	frame.height(height);
	frame.width(width);
	<?php if($_REQUEST['callFrom'] == "WV"){?>
	$("#divHeader").width(width-20);		
	<?php }?>
}
function reset_frame_height(){
	var frame = $('#frame_order', window.parent.document);
	height = frame.height();
	width = frame.width();
	//frame.height(height-50);
	//frame.width(width-300);
	frame.height(document_height);
	frame.width(document_width);
}

var editor;
function ckedtr(CKEDITOR){
    /*
    CKEDITOR.config.extraPlugins = 'drawing';
     CKEDITOR.config.toolbar_Custom =
    	 [
    	  { name: 'drawing', items : [ 'drawing' ] }
    	 ];
    */	 
    editor = CKEDITOR.instances['FCKeditor1'];
    editor.on( 'instanceReady', function(e) {
    
    	
    	/*
    	document_height = $(document).height();
    	document_width = $(document).width();
    	//set_frame_height();
    	editor.addCommand("markDraw", {
    		exec : function( editor )
    		{		
    			var content = editor.getSelection().getStartElement().getOuterHtml();
    			var selection = editor.getSelection();
    			var el = selection.getStartElement();
    			var parent = el.getParent();
    			var text = parent.getText();
    			if (el && parent.hasClass("ymarker")) {
    			} else {
    				var save = selection.getNative();
    				var element = CKEDITOR.dom.element.createFromHtml( '<draw>' + content + '</draw>' );
    				editor.insertElement(element);
    			}
    		}
    	});
    	editor.addCommand("showTags", {
    		exec : function( editor )
    		{		
    			sel = editor.getSelection();
    			var node = editor.document.getBody().getFirst();
    			var parent = node.getParent();
    			sellink = CKEDITOR.plugins.link.getSelectedLink(editor);
    			if(sellink)
    			document.getElementById('smartTag_parentId').value = sellink.getAttribute("id");
    			display_tag_options();
    		}
    	});
    	var showImageTags = {
    	label : "Mark Draw",
    	command : 'markDraw',
    	group : 'image'
    	};
    	var showAnchorTags = {
    	label : "Show Tag Options",
    	command : 'showTags',
    	group : 'anchor'
    	};
    	editor.contextMenu.addListener( function( element, selection ) {
    		return { 
    		showAnchorTags : CKEDITOR.TRISTATE_OFF 
    		};
    	});
    	editor.contextMenu.addListener( function( element, selection ) {
    		return { 
    		showImageTags : CKEDITOR.TRISTATE_OFF 
    		};
    	});
    	editor.addMenuItems({
    		showImageTags : {
    		  label : "Mark Draw",
    		  command : 'markDraw',
    		  group : 'image',
    		  order : 1
    		}
    	});
    	editor.addMenuItems({
    		showAnchorTags : {
    		  label : "Show Tag Options",
    		  command : 'showTags',
    		  group : 'anchor',
    		  order : 1
    		}
    	});
    	*/
    	//
    	<?php if($_REQUEST['callFrom'] == "WV"){?>	
    	parent.$("#loadingdiv").remove();
    	
    	<?php }?>
    });
    
    CKEDITOR.instances.FCKeditor1.on("instanceReady", function(event)
    {	
        setOrderOption(<?php echo $db_order_type_id;?>);
        document_height = $(document).height();
        document_width = $(document).width();
        <?php if($_REQUEST['callFrom'] != "WV"){?>
        $('#divpopup').draggable({"handle":"#divHeader"});
        <?php }?>
        <?php if($_REQUEST['callFrom'] == "WV"){?>
        $('#divpopup',window.parent.document).draggable();
        set_frame_height(); 
        <?php }?>
        var obj = $('#order_template')[0];
        get_template(obj, 'get_template');
    });
}

$(document).ready(function()
{
    fn_set_typeahead();
    $('#divpopup',window.parent.document).draggable();
    //test
    $( "#divorder_multiopts" ).scroll(function() { var v=$( "#divaddbtn" ).css("top"); v=parseInt($( "#divorder_multiopts" ).scrollTop())+10; $( "#divaddbtn" ).css( {"top":v+"px"} );});
    //
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace( 'FCKeditor1', { width:'90%', height:'100%'} );
    ckedtr(CKEDITOR);
    
    top.show_loading_image('hide');

    String.fromHtmlEntities = function(string){
        return (string+"").replace(/&#\d+;/gm,function(s) {
            return String.fromCharCode(s.match(/\d+/gm)[0]);
        })
    };
});
</script>