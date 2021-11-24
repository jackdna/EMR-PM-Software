<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
$tempContent = "";
include_once('../../../../config/globals.php');
if($_REQUEST['order_details_popup'] && $_REQUEST['order_details_popup'] == 'first_time_load'){
    $orderId = $_REQUEST['order_id'];
    $result = imw_query("SELECT template_content FROM order_details WHERE id = '".$orderId."'");
    $tempContent = "";
    while($row = imw_fetch_array($result)){
        $tempContent = stripcslashes(html_entity_decode($row['template_content']));
    }
    echo $tempContent;
    exit;
}
require_once("../../admin_header.php");
//--- GET ALL USERS DETAILS ------
$userQry  = imw_query("select id,lname,fname,mname from users where delete_status = '0'");
$userNameArr = array();
while($userQryRes = imw_fetch_array($userQry)){	
	$id = $userQryRes['id'];
	$name = $userQryRes['lname'].', ';
	$name .= $userQryRes['fname'].' ';
	$name .= $userQryRes['mname'];
	$name = ucwords(trim($name));
	if($name[0] == ','){
		$name = substr($name,1);
	}
	$userNameArr[$id] = $name;
}



//--- GET ALL GROUP DETAILS -----
$groupsQry  = imw_query("select id,name from user_groups");
$groupsNameArr = array();
while($groupsQryRes = imw_fetch_array($groupsQry)){	
	$id = $groupsQryRes['id'];
	$groupsNameArr[$id] = $groupsQryRes['name'];
}


//--- GET ALL DX CODES ------
$dxQry  = imw_query("SELECT id AS diagnosis_id, icd10 AS d_prac_code, icd10_desc AS diag_description FROM icd10_data");
$dxDetailsArr = array();
while($dxQryRes = imw_fetch_array($dxQry)){	
	$diagnosis_id = $dxQryRes['diagnosis_id'];
	$d_prac_code = $dxQryRes['d_prac_code'];
	$diag_description = $dxQryRes['diag_description'];
	$dxDetailsArr[$diagnosis_id] = $d_prac_code.'  '.$diag_description;
}

//--- GET LAB / RADIOLOGY DETAILS ----
$labQry= imw_query("select lab_radiology_name, lab_radiology_tbl_id from lab_radiology_tbl where lab_radiology_status = '0'");
$labOptionArr = array();
while($labQryRes = imw_fetch_array($labQry)){	
	$lab_radiology_name = $labQryRes['lab_radiology_name'];
	$tbl_id = $labQryRes['lab_radiology_tbl_id'];
	$labOptionArr[$tbl_id] = $lab_radiology_name;
}

//--- GET ALL ORDERS DETAILS -----
$orderDetalQuery = imw_query("select * from order_details where delete_status = '0' order by name");
$data = array();
while($res = imw_fetch_array($orderDetalQuery)){
	//--- RE PONSIBLE PERSON CHECK ---
	$resp_person_arr = preg_split('/,/',$res['resp_person']);
	$user_name_arr = array();
	for($l=0;$l<count($resp_person_arr);$l++){
		$user_name_arr[] = $userNameArr[$resp_person_arr[$l]];
	}
	$userName = join(', ',$user_name_arr);
	$userName = ucwords($userName);
	
	//--- DX CODE EXISTS CHECK ----
	$orders_dx_code_arr = preg_split('/,/',$res['orders_dx_icd10_code']);
	$dxCodeNameArr = array();
	for($l=0;$l<count($orders_dx_code_arr);$l++){
		$dxId = trim($orders_dx_code_arr[$l]);
		$dxCodeNameArr[] = $dxDetailsArr[$dxId];
	}	
	$dxCodeNameStr = join(', ',$dxCodeNameArr);
	
	//--- GROUP EXISTS CHECK ----
	$resp_group_arr = preg_split('/,/',$res['resp_group']);
	$groups_name_arr = array();
	for($l=0;$l<count($resp_group_arr);$l++){
		$groups_name_arr[] = ucwords($groupsNameArr[$resp_group_arr[$l]]);
	}
	$groups_name = join(', ',$groups_name_arr);

	$id = $res['id'];
	$name = ucwords($res['name']);
	$o_type = ucfirst($res['o_type']);
	$order_type_id = ucfirst($res['order_type_id']);
	$ref_code = ucfirst($res['ref_code']);
	$instruction = ucfirst($res['instruction']);
	$order_set_option = ucfirst($res['order_set_option']);
	$type_other_value = ucfirst($res['type_other_value']);
	if(empty($type_other_value) == false){
		$o_type = preg_replace('/Other/',$type_other_value,$o_type);
	}
	$optionArr = array();
	$order_set_option_arr = preg_split('/\n/',$order_set_option);
	for($l=0;$l<count($order_set_option_arr);$l++){
		$val = ucfirst(trim($order_set_option_arr[$l]));
		if(empty($val) == false){
			$optionArr[] = $val;
		}
	}
	$order_set_option = join(', ',$optionArr);
	
	$dosage  = ucfirst($res['dosage']);
	$qty  = ucfirst($res['qty']);
	$sig  = ucfirst($res['sig']);
	$refill  = ucfirst($res['refill']);
	$ndc_code  = ucfirst($res['ndccode']);
	$test  = ucfirst($res['testname']);
	$loinc_code  = ucfirst($res['loinc_code']);
	$cpt_code  = ucfirst($res['cpt_code']);
	$inform  = ucfirst($res['inform']);
	$order_lab_name = !empty($res['order_lab_name']) ? $labOptionArr[$res['order_lab_name']] : "" ;
	$snowmed  = ucfirst($res['snowmed']);
	
	if($order_type_id=="1" || $o_type=="Meds" || $o_type=="Medication"){ // Meds
	$order_type_id = 1;
	$data[$order_type_id].= '<tr style="height:21px;">
			<td>
				<!-- href="index.php?id=$id&sectoin_type=Meds" -->
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$name.'</a>
			</td>
			<!--<td class="text_10 alignCenter">
				<a href="#"  onClick="image_load_imz();">$o_type</a>
			</td>-->
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$dosage.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$qty.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$sig.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$refill.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Meds\');">'.$ndc_code.'</a>
			</td>
				
			<td class="text-center">
				<span onClick="confirm_del(\''.$id.'\');" class="glyphicon glyphicon-remove"></span>
			</td>
		</tr>';

	}else if($order_type_id=="2" || $o_type=="Labs" || $o_type=="Lab"){ //Labs
	$order_type_id="2";
	$data[$order_type_id].= '<tr style="height:21px;">
			<td>
				<!-- href="index.php?id=$id&sectoin_type=Labs" -->
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$name.'</a>
			</td>
			<!--<td>
				<a href="#"  onClick="image_load_imz();">$o_type</a>
			</td>-->
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$dxCodeNameStr.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$instruction.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$userName.'</a>
			</td>
			
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$test.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$loinc_code.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'Labs\');">'.$snowmed.'</a>
			</td>
			
			<td class="text-center">
				<span onClick="confirm_del(\''.$id.'\');" class="glyphicon glyphicon-remove"></span>
			</td>
		</tr>';

	}else if($order_type_id=="3" || $o_type=="Imaging/Rad" || $o_type=="Radiology/Imaging" || $o_type=="Imaging"){ // Imaging/Rad
	$order_type_id="3";
	$data[$order_type_id] .= '<tr style="height:21px;">
			<!-- //index.php?id=$id&sectoin_type=ImgRad -->
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$name.'</a>
			</td>
			
			<!--<td>
				<a href="#" onClick="image_load_imz();">$o_type</a>
			</td>-->
						
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$instruction.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$userName.'</a>
			</td>
					
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$test.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$loinc_code.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$cpt_code.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ImgRad\');">'.$snowmed.'</a>
			</td>
					
			<td class="text-center">
				<span onClick="confirm_del(\''.$id.'\');" class="glyphicon glyphicon-remove"></span>
			</td>
		</tr>';
	
	}else if($order_type_id=="4" || $o_type=="Procedure/Sx" || $o_type=="Surgery" || $o_type=="Procedural"){ // Procedure/Sx
	$order_type_id="4";
	$data[$order_type_id] .= '<tr style="height:21px;">
			<!-- //href="index.php?id=$id&sectoin_type=ProSx" -->
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$name.'</a>
			</td>
			<!--<td>
				<a href="#"  onClick="image_load_imz();">$o_type</a>
			</td>-->
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$instruction.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$userName.'</a>
			</td>		
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$cpt_code.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$order_lab_name.'</a>
			</td>
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'ProSx\');">'.$snowmed.'</a>
			</td>
		
			<td class="text-center">
				<span onClick="confirm_del(\''.$id.'\');" class="glyphicon glyphicon-remove"></span>
			</td>
		</tr>';

	}else if($order_type_id=="5" || $o_type=="Information/Instructions" || $o_type=="Information"){ //Information/Instructions
	$order_type_id="5";
	$data[$order_type_id] .= '<tr style="height:21px;">
			<!--//href="index.php?id=$id&sectoin_type=InfIns" -->
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'InfIns\');">'.$name.'</a>
			</td>
			
			<!--<td>
				<a href="#"  onClick="image_load_imz();">$o_type</a>
			</td>-->
			
			<td>
				<a href="javascript:void(0);"  onClick="image_load_imz('.$id.',\'InfIns\');">'.$inform.'</a>
			</td>
		
			<td class="text-center">
				<span onClick="confirm_del(\''.$id.'\');" class="glyphicon glyphicon-remove"></span>
			</td>
		</tr>';
	}
	
}
?>
<style>
#divpopup{position:absolute;top:5px; left:50%;margin-right:-50%;border:1px solid black;width:90%;height:90%;display:none;background-color:white;transform: translateX(-50%)}
</style>
<div class="whtbox" id="main_div">
<div class="table-responsive respotable adminnw" id="Meds">
		<table class="table table-bordered">
			<thead>
				<tr data-toggle="collapse" data-target="#Med_data" data-parent="#main_div">
					<th class="pointer">Orders Name (Meds)</th>
					<th class="pointer">Dosage</th>
					<th class="pointer">Qty</th>
					<th class="pointer">Sig</th>
					<th class="pointer">Refill</th>
					<th class="pointer">NDC Code</th>
					<th style="width:70px" class="text-center pointer">Action</th>
				</tr>
			</thead>
			<tbody id="Med_data" class="collapse ">
				<?php echo $data["1"]; // Meds?>
			</tbody>
		</table>
	</div>
	
	<div class="table-responsive respotable adminnw" id="Labs">
		<table class="table table-bordered">
			<thead>
				<tr data-toggle="collapse" data-target="#Lab_data" data-parent="#main_div">
					<th class="pointer">Orders Name (Labs)</th>
					<th class="pointer" nowrap >Dx code</th>
					<th class="pointer">Instruction</th>
					<th class="pointer">Resp. Person</th>			
					<th class="pointer">Test</th>
					<th class="pointer">Loinc Code</th>
					<th class="pointer">Snowmed</th>	
					<th style="width:70px" class="text-center pointer">Action</th>
				</tr>
			</thead>
			<tbody id="Lab_data" class="collapse  ">
				<?php echo $data["2"]; // Labs?>
			</tbody>
		</table>
	</div>
	
	<div class="table-responsive respotable adminnw" id="ImgRad">
		<table class="table table-bordered">
			<thead>
				<tr data-toggle="collapse" data-target="#ImgRad_data" data-parent="#main_div">
					<th class="pointer">Orders Name (Imaging/Rad)</th>
					<th class="pointer">Instruction</th>
					<th class="pointer">Resp. Person</th>		
					<th class="pointer">Test</th>
					<th class="pointer">Loinc Code</th>
					<th class="pointer">CPT Code</th>
					<!--<th class="pointer">Lab/Rad Type</th>-->
					<th class="pointer">Snowmed</th>
					<th style="width:70px" class="text-center pointer">Action</th>
				</tr>
			</thead>
			<tbody id="ImgRad_data" class="collapse  ">
				<?php echo $data["3"]; //ImgRad?>
			</tbody>
		</table>
	</div>
	
	<div class="table-responsive respotable adminnw" id="ProSx">
		<table class="table table-bordered">
			<thead>
				<tr data-toggle="collapse" data-target="#ProSx_data" data-parent="#main_div">
					<th class="pointer">Orders Name (Procedure/Sx)</th>
					<th class="pointer">Instruction</th>
					<th class="pointer">Resp. Person</th>				
					<th class="pointer">CPT Code</th>
					<th class="pointer">Lab/Rad Type</th>
					<th class="pointer">Snowmed</th>
					<th style="width:70px" class="text-center pointer">Action</th>
				</tr>
			</thead>
			<tbody id="ProSx_data" class="collapse  ">
				<?php echo $data["4"]; // Procedure/Sx?>
			</tbody>
		</table>
	</div>
	
	<div class="table-responsive respotable adminnw" id="InfIns">
		<table class="table table-bordered">
			<thead>
				<tr data-toggle="collapse" data-target="#InfIns_data" data-parent="#main_div">
					<th class="pointer">Orders Name (Information/Instructions)</th>
					<th class="pointer">Information</th>
					<th style="width:70px" class="text-center pointer">Action</th>
				</tr>
			</thead>
			<tbody id="InfIns_data" class="collapse  ">
				<?php echo $data["5"]; //Information/Instructions?>
			</tbody>
		</table>
	</div>
</div>
<script>
zPath="<?php echo $GLOBALS['rootdir']; ?>";
function load_pop_up(m,i,t){
	top.show_loading_image('show',300);
	var q="";	
	if(typeof(i)!="undefined" && i!=""){ q+="&id="+i;  }
	if(typeof(t)!="undefined" && t!=""){ q+="&sectoin_type="+t;  }
	
	$.get(zPath+"/chart_notes/requestHandler.php?elem_formAction=GetOrderDetail&req_ptwo=1&flgInsideAdmin=1"+q, function(d){
		top.show_loading_image('hide');
		if(d!=""){
			if($("#divpopup").length<=0){
				$("body").append("<div id=\"divpopup\">"+d+"</div>");
				newForm12(m,i,t);
			}
		}
	});	
}
function newForm12(mode,id,tp){ //	
	if($("#divpopup").length<=0){ load_pop_up(mode,id,tp);return; }	
	
	mode = mode || '';
	document.getElementById("divpopup").style.display="block";
	$('.dialogMask').fadeIn('fast');
	if(mode == "new"){
		$("#save_frm").val('');
		$("#mode").val('');
		$("#id").val('');
		$("#selected_responsible").val('');
		$("#selected_dx_code").val('');
		$("#selected_lad_rad_type").val('');
		$('#ele_order_type option[value=1]').attr('selected','selected');
		//$("#ele_order_type").val('');
		$("#ele_order_name").val('');
		//$("#order_template").val('');
		$('#order_template option[value=""]').attr('selected','selected');
		$("#med_id").val('');
		$("#ele_test_name").val('');
		$("#ele_information").val('');
		$("#ele_instruction").val('');
		$("#ele_lad_rad_type").children().remove();
		$("#ele_responsible_person").children().remove();
		$("#ele_cpt_code").val('');
		$("#ele_dx_code").children().remove();
		$("#ele_loinc").val('');
		$("#ele_snowmed").val('');
		$("#ele_dosage").val('');
		$("#ele_quantity").val('');
		$("#ele_sig").val('');
		$("#ele_refill").val('');
		$("#ele_ndc_code").val('');
		$("#ele_fdb_code").val('');
		$("#lbl_order_type").hide();
		$("#ele_order_type").show();
		if(CKEDITOR && CKEDITOR.instances['FCKeditor1']){CKEDITOR.instances['FCKeditor1'].setData('');}
		setOrderOption(1);
		
		//remove additional if any--
		$("input[name*=ele_dosage], input[name*=ele_quantity], input[name*=ele_sig], input[name*=ele_refill], input[name*=ele_ndc_code], input[name*=ele_fdb_code]").each(function(indx){
			if(this.name!="ele_dosage" && this.name!="ele_quantity" && this.name!="ele_sig" && this.name!="ele_refill" && this.name!="ele_ndc_code" && this.name!="ele_fdb_code" ){
			$(this).parent().remove();
			}
		});
		$("#divorder_multiopts br").remove();
		//remove additional if any--
	}
	
	//set height
	var df = $(window).height();
	var df1 = $(window).width()*0.90;
	var df3 = parseInt($(window).scrollTop()) + 25;
	
	//var df2=$("#divpopup").css("height");
	var df2 = parseInt(df)-100;			
	$("#divpopup_inner, #divpopup").css({"height":df2+"px", "width":""+df1+"px"});
	//alert(df+" - "+df2);
	$("#divpopup").css({ "top": df3+"px" });
}

function confirm_del(id,msg){
	if(typeof(msg)!='boolean'){msg = true;}
	if(msg){
		top.fancyConfirm("Are you sure to delete this record?","", "window.top.fmain.confirm_del('"+id+"',false)");
	}else{
		top.show_loading_image('show',300);
		//top.fmain.all_data.all_data2.orderlistframe.redirect_del(id);
		var prm = "el_del_id="+id;
		prm += "&req_ptwo=1";
		prm += "&elem_formAction=saveOrder";
		$.post(zPath+"/chart_notes/requestHandler.php", prm, function(d){				
			if(d=="0"){ window.location.replace("orderList.php"); }
		});
	}
}

function image_load_imz(id,type){
$("#divpopup").remove();
newForm12('',id,type);
top.show_loading_image('show',300);
}

//--
var ar = [["new_order_sets","Add New","top.fmain.newForm12('new');"]];
$(document).ready(function(){
top.btn_show("ADMN",ar);
set_header_title('Orders');
<?php
	//if(!empty($_REQUEST['id'])){
	//	echo " newForm12(); ";
	//}
?>
});
</script>