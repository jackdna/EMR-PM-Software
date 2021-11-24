<?php 
/*
File: stock_search.php
Coded in PHP7
Purpose: Search Stock records
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

///manage width of page
if($_REQUEST['open']){
	$tabl_width='89.9';
	$td_width='200';
	$inputwidth='130';
	$heigh_div=$_SESSION['wn_height']-510;
}
else{
	$tabl_width='99.9';
	$td_width='162';
	$heigh_div='310';
}
	 
$frm_method=$_REQUEST['frm_method'];
$search_id=$_REQUEST['srch_id'];
$manufacturer_Id_Srch=$_REQUEST['manuf_id'];
$opt_vendor_id=$_REQUEST['vendor'];
$opt_brand_id=$_REQUEST['brand'];

$color_id_search=$_REQUEST['color'];
$shape_id_search=$_REQUEST['shape'];
$style_id_search=$_REQUEST['style'];
$from=$_REQUEST['from'];
$frm_dw=$_REQUEST['frm_dw'];
$price_frm=$_REQUEST['price_frm'];
$price_to=$_REQUEST['price_to'];

if($frm_dw=='pt_int' || $frm_dw=='paging' || $_POST['search_result'])
{
	
	
	if($frm_dw=='pt_int' || $frm_dw=='paging')
	{
		$frm_method=$_REQUEST['frm_method'];
		$search_id=$_REQUEST['srch_id'];
		$manufacturer_Id_Srch=$_REQUEST['manuf_id'];
		$opt_vendor_id=$_REQUEST['vendor'];
		$opt_brand_id=$_REQUEST['brand'];		
		$color_id_search=$_REQUEST['color'];
		$shape_id_search=$_REQUEST['shape'];
		$style_id_search=$_REQUEST['style'];
		$price_frm_search=$_REQUEST['price_frm'];
		$price_to_search=$_REQUEST['price_to'];
		$search_id = isset($_REQUEST['type_optical_id']) ? $_REQUEST['type_optical_id'] : 0;
		$name_txt = $_REQUEST['name_txt'];
	}
	if($_POST['search_result'])
	{
		$manufacturer_Id_Srch = $_POST['manufacturer_Id_Srch'];
		$search_id= $_POST['type_optical_id'];
		$opt_vendor_id= $_POST['opt_vendor_id'];
		$opt_brand_id = $_POST['opt_brand_id'];
		$upcval = $_POST['upc_name'];
		$name_txt = $_POST['name_txt'];
		$color_id_search=$_REQUEST['color'];
		$shape_id_search=$_REQUEST['shape'];
		$style_id_search=$_REQUEST['style'];	
		$price_frm_search=$_REQUEST['price_frm'];
		$price_to_search=$_REQUEST['price_to'];	
	}
if($search_id<=0){
	$search_id=1;
}

$paging_qry = array();
if($_REQUEST['srch_id']){
	$paging_qry['srch_id'] = $_REQUEST['srch_id'];
	$paging_qry['frm_dw'] = 'paging';
	$paging_qry['from'] = $from;
}

$and="";
$tbNameJoin="";
if($opt_vendor_id!='' && $opt_vendor_id>0){
	$tb_field= ",VT.vendor_name";
	$and="And it.vendor_id='$opt_vendor_id'";
	$tbNameJoin= "LEFT join in_vendor_details as VT on VT.id = it.vendor_id";
	$paging_qry['vendor'] = $opt_vendor_id;
}
if($opt_brand_id!='' && $opt_brand_id>0){
	$tb_field.= ",BT.frame_source";
	$and.=" And it.brand_id='$opt_brand_id'";
	$tbNameJoin.= " LEFT join in_frame_sources as BT on BT.id = it.brand_id";
	$paging_qry['brand'] = $opt_brand_id;
}
if($upcval!=''){
	$and.="And it.upc_code like('$upcval%')";
	$paging_qry['upc_name'] = $upcval;
}

if($name_txt!=''){
	
	/*Free Filter for Frame*/
	if($search_id=='1'){
		$and.="And (it.name like ('$name_txt%')";
		
		/*Matching Manufacturers*/
		$sql_manuf = "SELECT GROUP_CONCAT(`id`) AS `ids` FROM `in_manufacturer_details`
					WHERE `del_status`=0 AND `frames_chk`=1 AND
						`manufacturer_name` LIKE '".imw_real_escape_string($name_txt)."%'";
		$resp_manuf= imw_query($sql_manuf);
		if($resp_manuf){
			$manuf_data = imw_fetch_assoc($resp_manuf);
			$manuf_data = $manuf_data['ids'];
			if($manuf_data){
				$and.=" || it.manufacturer_id IN(".$manuf_data.")";
			}
		}
		
		/*Matching Brands*/
		$sql_brand = "SELECT GROUP_CONCAT(`id`) AS `ids` FROM `in_frame_sources`
					WHERE `del_status`=0 AND `frame_source` LIKE '".imw_real_escape_string($name_txt)."%'";
		$resp_brand= imw_query($sql_brand);
		if($resp_brand){
			$brand_data = imw_fetch_assoc($resp_brand);
			$brand_data = $brand_data['ids'];
			if($brand_data){
				$and.=" || it.brand_id IN(".$brand_data.")";
			}
		}
		
		/*Matching Styles*/
		$sql_style = "SELECT GROUP_CONCAT(`id`) AS `ids` FROM `in_frame_styles`
					WHERE `del_status`=0 AND `style_name` LIKE '".imw_real_escape_string($name_txt)."%'";
		$resp_style= imw_query($sql_style);
		if($resp_style){
			$style_data = imw_fetch_assoc($resp_style);
			$style_data = $style_data['ids'];
			if($style_data){
				$and.=" || it.frame_style IN(".$style_data.")";
			}
		}
		
		$and.=")";
	}
	else{
		$and.="And it.name like ('$name_txt%')";
	}
	
	$paging_qry['name_txt'] = $name_txt;
}

if($manufacturer_Id_Srch!='' && $manufacturer_Id_Srch>0){
	$and.=" And it.manufacturer_id='$manufacturer_Id_Srch'";
	$paging_qry['manuf_id'] = $manufacturer_Id_Srch;
}

if($color_id_search!='' && $color_id_search>0){
	$and.=" And it.color='$color_id_search'";
	$paging_qry['color'] = $color_id_search;
}
if($shape_id_search!='' && $shape_id_search>0){
	$and.=" And it.frame_shape='$shape_id_search'";	
	$paging_qry['shape'] = $shape_id_search;
}
if($style_id_search!='' && $style_id_search>0){
	$and.=" And it.frame_style='$style_id_search'";
	$paging_qry['style'] = $style_id_search;
}
if($price_frm_search>0){
	$and.=" And it.retail_price>='$price_frm_search'";
	$paging_qry['price_frm'] = $price_frm_search;
}
if($price_to_search>0){
	$and.=" And it.retail_price<='$price_to_search'";
	$paging_qry['price_to'] = $price_to_search;	
}

if(isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1'){
	$and.=" And it.qty_on_hand>0";
	$paging_qry['in_stock_chk'] = '1';	
}

	$frameFieldsToshow = $_REQUEST['frameFields'];
	$paging_qry['frameFields'] = $frameFieldsToshow;
/*Paging*/
	
	$query_string = http_build_query($paging_qry);
	$targetpage = "stock_search.php?".$query_string;
	$ext_parm = true;
	
	$limit = 100;
	$query = "SELECT COUNT(it.id) as num FROM in_item as it 
		LEFT join in_module_type as FT on FT.id = it.module_type_id
		where it.del_status='0' and it.module_type_id = '$search_id'
		".$and;
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages['num']; 
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
/*End paging*/

	$qry = "select it.*,
	FT.module_type_name,MT.manufacturer_name
	$tb_field
	from in_item  as it 
	LEFT join in_module_type as FT on FT.id = it.module_type_id
	LEFT join in_manufacturer_details as MT on MT.id = it.manufacturer_id
	$tbNameJoin
	where it.del_status='0' and it.module_type_id = '$search_id'
	".$and." order by it.upc_code asc, it.name asc LIMIT $start, $limit";
	$sql = imw_query($qry);
}
$show_heading='<div class="fl">Search Records </div>';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript">
CURRENCY_SYMBOL = '<?php currency_symbol(); ?>';
if(window.opener)
	window.opener = window.opener.main_iframe.admin_iframe;
</script>
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect_edited.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<style>
.btn_cls{
    background: url("../../images/btnbg.png") repeat-x scroll 0 0 transparent;
    border: medium none;
    border-radius: 5px 5px 5px 5px;
    color: #FFFFFF;
    cursor: pointer;
    height: 31px;
    padding: 5px 20px;
}
.txtcolr{
	color:#000;
	text-decoration:none;
}
td a:hover{
	color:#0080FF;
	text-decoration:none;
}
.table_cell_padd5 td, .listheading td{
	padding: 5px 2px;
}
.table_cell_padd5 td{
    word-break: break-word;
	word-wrap: break-word;
}
.table_cell_padd5 tr:first-child td{
	padding:0px;
	border:0px;
}
.paginate {
    font-family: Arial, Helvetica, sans-serif;
    padding: 3px;
    margin: 3px;
}
.multiSelect>span,
.multiSelect+div.multiSelectOptions>label{font-family: Arial !important;font-size: 13.3px;clear:both;}
.multiSelect+div.multiSelectOptions>label>input{margin:0px 2px 0px 0;vertical-align: top;float:none;}
#mainWrapper .listheading td {
	padding: 5px 0px;
}
</style>
<script language="javascript"> 
function getBrandStyle(bid,sid,num)
{
	if(bid!='' && bid!='0')
	{
		if(typeof(num)=='undefined')
		{
			var num = 1;
		}
		var string = 'action=get_style&bid='+bid+'&sid='+sid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Select Style</option>"+response;
				$('#style').html(opt_data);
			}
		});
	}
	else{
		$('#style').html("<option value='0'>Select Style</option>");
	}
}
function changeParent(upc_val, name) {
	name = (typeof(name)=="undefined")?false:name;
	if(typeof(window.opener)!="undefined"){
		var as = $( "#type_optical_id option:selected" ).text();
		var type = as.toLowerCase();
		var otherTypePage ='<?php echo $_REQUEST['otherTempPage'];?>';
		var page_Other = "<?php echo $_REQUEST['module_typePat'];?>";
		var frm_method = "<?php echo $_REQUEST['frm_method'];?>";
		var search_id = "<?php echo $_REQUEST['srch_id'];?>";
		var pt_picture = "<?php echo $_REQUEST['picture'];?>";
		var itemCounter = "<?php echo $_REQUEST['itemCounter'];?>";
		
		
		if(pt_picture!="")
		{
			var pt_pic = '&sel_pic='+pt_picture;
		}
		else
		{
			var pt_pic = '';
		}
		 var redriect_page;
		 redriect_page=page_change_acc_type();
		
		if(otherTypePage=='other_selPage'){
			window.opener.document.getElementById('upc_id_1').value=upc_val;
			window.opener.get_upcbyid(upc_val);
		}
		else if(type=='medicine' && page_Other=='patient_interPage'){
			 window.opener.location.href = redriect_page+'?upc_name='+upc_val+'&frm_method='+frm_method;
		}
		else if(type=='contact lenses' && search_id=='3'){
			if(window.opener.document.getElementById('upc_id_1')){
			 window.opener.document.getElementById('upc_id_1').value=upc_val;	
			 window.opener.get_details_by_upc(upc_val);
			}else{
				window.opener.location.href = redriect_page+'?upc_name='+upc_val+'&frm_method='+frm_method;	
			}
		}
		else if(type=='lenses' && search_id=='2'){
			if(window.opener.document.getElementById('upc_id_1_lensD')){
				window.opener.document.getElementById('upc_id_1_lensD').value=upc_val;	
				window.opener.get_details_by_upc_lensD(upc_val);
			}else{
				window.opener.location.href = redriect_page+'?upc_name_lens='+upc_val+'&frm_method_lens='+frm_method;	
			}
		}
		else{
			if(itemCounter>0){
				window.opener.document.getElementById("upc_id_"+itemCounter).value=upc_val;
				window.opener.document.getElementById("upc_name_"+itemCounter).onchange();
			}
			else{
				try{
					window.opener.document.getElementById("upc_id"+itemCounter).value=upc_val;
					window.opener.document.getElementById("upc_name"+itemCounter).onchange();
				}
				catch(e){
					if('<?php echo $_REQUEST['source']?>'=='medstock' && name){
						window.opener.document.getElementById("name").value=name;
						window.opener.document.getElementById("name_flag").value=1;
						window.opener.load_medicine(name);
					}
					else{
						window.opener.document.getElementById("upc_id_1").value=upc_val;
						window.opener.document.getElementById("upc_name_1").onchange();
					}
					console.log(name);
				}
			}
			/*Commented because multiple items in Frames & Lenses Section*/
			/*window.opener.location.href = redriect_page+'?upc_name='+upc_val+'&frm_method='+frm_method+pt_pic;*/
		}	 
	}
  	window.close();
} 


function page_change_acc_type()
{
	var as = $( "#type_optical_id option:selected" ).text();
	var type = as.toLowerCase();
	var pages = new Array();
	var page_action = "<?php echo $_REQUEST['module_typePat'];?>";
	
	if(page_action=='patient_interPage'){
		pages['frame'] = "../patient_interface/pt_frame_selection_1.php";
		pages['lenses'] = "../patient_interface/lens_selection.php";
		pages['contact lenses'] = "../patient_interface/contact_selection.php";
		pages['medicine'] = "../patient_interface/other_selection.php";
	}
	else{
		pages['frame'] = "frame/index.php";
		pages['lenses'] = "lens/index.php";
		pages['contact lenses'] = "contact_lens/index.php";
		pages['supplies'] = "supplies/index.php";
		pages['medicine'] = "medicines/index.php";
		pages['accessories'] = "accessories/index.php";
	}
	return pages[type];
}

function show_brand(get_type_id)
{
	if(get_type_id=="5" || get_type_id=="6" || get_type_id=="7"  || get_type_id=="2")
	{
		$("#srch_brand").css('display','none');
		$("#srch_brand_sel").css('display','none');
		$(".res_brand").css('display','none');
		$(".res_brand_dis").css('display','none');	
	}
	else
	{
		$("#srch_brand").css('display','');
		$("#srch_brand_sel").css('display','');
		$(".res_brand").css('display','');
		$(".res_brand_dis").css('display','');
	}
}

function change_width(wid)
{
	if(wid=="7" || wid=="6" || wid=="5" || wid=="2")
	{
		$("#type_optical_id").css('width','200px');
		$("#manufacturer_Id_Srch").css('width','150px');
		$("#opt_vendor_id").css('width','150px');
	}
	else
	{
		$("#type_optical_id").css('width','120px');
		$("#manufacturer_Id_Srch").css('width','150px');
		$("#opt_vendor_id").css('width','100px');
	}
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
function clean_val(id){
	if(document.getElementById(id).value=="Min"){
		document.getElementById(id).value="";
	}
	if(document.getElementById(id).value=="Max"){
		document.getElementById(id).value="";
	}
}

/*Validate Form*/
function submitForm(){
	
	sOpts = new Array();
	sOpts[0] = manufacturer = $("#manufacturer_Id_Srch").val();
	sOpts[1] = vendor = $("#opt_vendor_id").val();
	sOpts[2] = brand = $("#opt_brand_id").val();
	sOpts[3] = color = $("#color").val();
	sOpts[4] = shape = $("#shape").val();
	sOpts[5] = style = $("#style").val();
	sOpts[6] = minPrice = $("#price_frm").val();
	sOpts[7] = maxPrice = $("#price_to").val();
	sOpts[8] = upcId = $("#upc_name").val();
	sOpts[9] = prodName = $("#name_txt").val();
	
	nullVals = new Array("0", "Min", "Max", " ", "");
	error = true;
	$(sOpts).each(function(key,val){
		if(nullVals.indexOf(val)== "-1"){
			error = false;
		}
	});
	
	if(error){
		falert("Please make a selection");
		return false;
	}
	else{
		return true;
	}
}
</script> 
</head>
<body>
	<div style="padding:0px; width:100%;">
        <div class="listheading"><?php echo $show_heading; $style_upc="";?></div>
        <div>
        	<form method="post" action="" name="stock_srch_frm">
               <table style="width:<?php echo (isset($_REQUEST['open'])&&$_REQUEST['open']=='front')?'100':$tabl_width;?>%;margin-top:5px;">
                <tr class="table_collapse listheading">
					<td >Type</td>
					<td >Manufacturer</td>
					<td style="width:<?php if($from=="style"){ echo "100px"; }else{ echo "300px";} ?>; text-align:center;">Vendor</td>
					<td id="srch_brand" style="width:<?php if($from=="style"){ echo "100px"; } else { echo "200px"; } ?>; text-align:center;">Brand</td>
					<?php if($from=="style"){ ?>
					<td style="width:80px; text-align:center;">Color</td>
					<td style="width:80px; text-align:center;">Shape</td>
					<td style="width:80px; text-align:center;">Style</td>
					<td style="width:130px; text-align:center;">Price Range</td>
					<?php } ?>
					<?php if($from=="style"){ $td_width="150px"; } else{ $td_width="300px";} ?>
					<td style="width:<?php if(isset($_REQUEST['open'])&&$_REQUEST['open']=='front'){echo'200px';}elseif($from=="style") { echo "90px"; }else{ echo "300px"; } ?>; text-align:center;">UPC</td>
					<td style="width:<?php if(isset($_REQUEST['open'])&&$_REQUEST['open']=='front'){echo'200px';}elseif($from=="style") { echo "90px"; }else{ echo "300px"; } ?>; text-align:center;">Name</td>
					<td style="width:<?php echo ($from!="style")?200:72; ?>px; text-align:center;">In Stock</td>
					<?php if($from=="style"): ?>
					<td style="width:80px;text-align:center;">Fields</td>
					<?php endif; ?>
					<td style="width:<?php if($from=="style"){ echo "120px"; } else { echo "400px"; } ?>; text-align:center;" colspan="2">&nbsp;</td>
                </tr>               
                <tr>
               		<td style="text-align:center;">
                	<select name="type_optical_id" id="type_optical_id" onChange="javascript:get_type_manufacture(this.value,'0');  get_brandFromVendor(document.getElementById('opt_vendor_id').value,'0',this.value); show_brand(this.value); change_width(this.value);" style="width: 100%">
                          <?php  $rowsType="";
							$selected_module = '';
                          $rowsType = data("select * from in_module_type order by module_type_name asc");
                          foreach($rowsType as $rsultType)
                          { 
						  if($rsultType['module_type_name']=="medicine" || $rsultType['module_type_name']=="supplies" || $rsultType['module_type_name']=="accessories"){ $style_upc="126";}else{$style_upc="90";}
						  ?>
                            <option value="<?php echo $rsultType['id']; ?>" <?php if($rsultType['id']==$search_id) { $selected_module = $rsultType['module_type_name']; echo "selected"; }?>><?php echo ucfirst($rsultType['module_type_name']); ?></option>	
                    <?php }	?>
                    </select></td>
                <td style="text-align:center;">
                	<select name="manufacturer_Id_Srch" id="manufacturer_Id_Srch" onChange="javascript:get_vendorFromManufacturer(this.value,'0'); javascript:get_brandFromManufacturer(this.value,'0');">
                    	<option value="0">Select Manufacturer</option>
                    </select>
                </td>
                
                <td style="text-align:center;">
                	<select name="opt_vendor_id" style="width:100%" id="opt_vendor_id" onChange="javascript:get_brandFromVendor(this.value,'0',document.getElementById('type_optical_id').value);">
                    	<option value="0">Select Vendor</option>
						<?php $rows="";
							  $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
							  foreach($rows as $r)
							  { ?>
								<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['vendor_name']); ?></option>	
						<?php }	?>
			        </select></td>
                <td id="srch_brand_sel" style="text-align:center;">
                	<select name="opt_brand_id"  id="opt_brand_id" style="width:100%" onChange="getBrandStyle(this.value,<?php echo (isset($_REQUEST['style'])&&$_REQUEST['style']!="")?$_REQUEST['style']:0; ?>);">
                    	<option value="0">Select Brand</option>
						<?php $rows="";
							if($_REQUEST['srch_id']==3 || $_REQUEST['type_optical_id']==3)
							{
								$rows = data("select id,brand_name as frame_source from in_contact_brand where del_status='0' order by brand_name asc");
							}
							else
							{
							  $rows = data("select id, frame_source from in_frame_sources where del_status='0' order by frame_source asc");
							 }
							  foreach($rows as $r)
							  { ?>
								<option <?php if($_REQUEST['brand']==$r['id'] || $_REQUEST['opt_brand_id']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_source']); ?></option>	
						<?php }	?>
                    </select></td>							
          
				<?php if($from=="style"){ ?>                    
                <td style="text-align:center;">
				<?php
					if( strtolower($selected_module)=='frame' ){
						$sql_color = "SELECT `id`, `color_name` FROM `in_frame_color` WHERE (`color_name`='".addslashes($_REQUEST['color'])."' OR `id`='".$color_id_search."') AND `del_status`='0' and color_name!='' LIMIT 1";
						$sql_color = imw_query($sql_color);
						$frame_color_id = '';
						$frame_color_name = '';
						if($sql_color && imw_num_rows($sql_color)==1){
							$sql_color = imw_fetch_assoc($sql_color);
							$frame_color_id = $sql_color['id'];
							$frame_color_name = $sql_color['color_name'];
						}
						
				?>
					<input type="text" name="color_name" id="color_name" value="<?php echo $frame_color_name; ?>" style="width:74px;" autocomplete="off" />
					<input type="hidden" name="color" id="color" value="<?php echo $frame_color_id; ?>" />
				<?php
					}
					else{
				?>
					<select name="color"  id="color" style="width:80px;">
						<option value="0">Select Color</option>
						<?php 
						$rows="";
						$rows = data("select id,color_name as frame_color from in_frame_color where del_status='0' order by color_name asc");
						foreach($rows as $r)
						{ ?>
						<option <?php if($_REQUEST['color']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_color']); ?></option>	
						<?php }	?>
					</select>
				<?php
					}
				?>
                </td>
                <td style="text-align:center;">
                <select name="shape"  id="shape" style="width:100%">
                <option value="0">Select Shape</option>
                <?php 
                $rows="";
                $rows = data("select id,shape_name from in_frame_shapes where del_status='0' order by shape_name asc");
                foreach($rows as $r)
                { ?>
                <option <?php if($_REQUEST['shape']==$r['id']) { echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['shape_name']); ?></option>	
                <?php }	?>
                </select>
                </td>
                <td style="text-align:center;">
					<select name="style"  id="style" style="width:100%;">
					<option value="0">Select Style</option>
						<?php 
					   /* Commented to speedup the page
					   $rows="";
						$rows = data("select id,style_name from in_frame_styles where del_status='0' order by style_name asc");
						foreach($rows as $r)
						{ ?>
						<option <?php if($_REQUEST['style']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['style_name']); ?></option>	
						<?php }	?>
						*/
						?>
					</select>
				</td>
                <?php
					/*if($price_frm==""){
						$price_frm="Min";
					}else{
						$price_frm=$_REQUEST['price_frm'];
					}
					if($price_to==""){
						$price_to="Max";
					}else{
						$price_to=$_REQUEST['price_to'];
					}*/
				?>
                <td style="text-align:center;">
                 <input type="text" name="price_frm" id="price_frm" value="<?php echo $price_frm;?>" style="width:32px;" onClick="clean_val('price_frm');" class="currency" /> - <input type="text" name="price_to" id="price_to" value="<?php echo $price_to;?>" style="width:32px;" onClick="clean_val('price_to');" class="currency" />
                </td>
                <?php } ?>
                <td style="text-align:center;">
                <?php if($from=="style"){ $inputwidth="80"; }else{  $inputwidth="200"; } ?>
                <input type="text" name="upc_name" id="upc_name" value="<?php echo $upcval;?>" style="width:<?php echo (isset($_REQUEST['open'])&&$_REQUEST['open']=='front')?'80':$inputwidth;?>px;" />
                </td>
                <td style="text-align:center;">
               		<input type="text" name="name_txt" id="name_txt" value="<?php echo $name_txt;?>" style="width:<?php echo (isset($_REQUEST['open'])&&$_REQUEST['open']=='front')?'80':$inputwidth;?>px;" />
				</td>
				<td style="text-align:center;">
					<input style="margin:0;height:18px;width:18px;vertical-align:middle;cursor:pointer;" type="checkbox" name="in_stock_chk" id="in_stock_chk" value="1" <?php echo (isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1')?'checked':''; ?>/>
				</td>
				<?php if($from=="style"): ?>
				<td >
					<select name="frameFields" id="frameFields">
						<option <?php echo (in_array('A', $frameFieldsToshow))?'selected':''; ?>>A</option>
						<option <?php echo (in_array('B', $frameFieldsToshow))?'selected':''; ?>>B</option>
						<option <?php echo (in_array('ED', $frameFieldsToshow))?'selected':''; ?>>ED</option>
						<option <?php echo (in_array('DBL', $frameFieldsToshow))?'selected':''; ?>>DBL</option>
						<option <?php echo (in_array('Temple', $frameFieldsToshow))?'selected':''; ?>>Temple</option>
						<option <?php echo (in_array('Bridge', $frameFieldsToshow))?'selected':''; ?>>Bridge</option>
						<option <?php echo (in_array('FPD', $frameFieldsToshow))?'selected':''; ?>>FPD</option>
					</select>
				</td>
				<?php endif; ?>
                <td style="width:<?php if($from=="style"){ echo "120px"; } else { echo "200px"; } ?>; text-align:center;" colspan="2">
                <input class="btn_cls" type="submit" name="search_result" value="Search" onClick="return submitForm();" /></td>
                </tr>
                </table>
                </form> 
        </div>

<div id="mainWrapper" style="overflow-y:hidden;overflow-x:auto;width:100%;">
<?php
$increaseWidth = 0;
foreach($frameFieldsToshow as $fieldShow){
	$increaseWidth = $increaseWidth+52.572;
}
?>
<div style="display:block;width:<?php echo ($from=="style")?((1417)+$increaseWidth.'px;'):'99.7%;overflow-x: hidden;'; ?>;overflow-y:scroll;">
<table class="table_collapse listheading" align="center" style="margin: 5px 0px 0 0px; display:<?php if(isset($_POST['search_result']) || $from=='style' || $frm_dw=='paging'){ echo "inline-table"; } else { echo "none"; } ?>; background-size:4px;">

<?php
	$noStyle = true;
	if($_REQUEST['srch_id']=="1"||$_REQUEST['type_optical_id']=="1"){ 
		$noStyle=false;
	}
?>

                <tr>
                    <td style="width:30px; text-align:center; font-size:14px;">Sr #</td>
                    <!--<td style="width:90px; text-align:center; font-size:14px;">Type</td>-->
                    <td style="width:<?php if($_REQUEST['open']){ echo "90"; }else{ echo "147"; } ?>px; text-align:center; font-size:14px;">UPC</td>
                    <td style="width:<?php if($_REQUEST['open']){ echo ($noStyle)?"175":"80"; }else{ echo ($noStyle)?"227":"133"; } ?>px; text-align:<?php if($_REQUEST['open']){ echo "center";}else { echo "center";} ?>;font-size:14px;">Name</td>
<?php if(!$noStyle){ ?>
					<td style="width: <?php if($_REQUEST['open']){ echo "95";}else{ echo "117";} ?>px; font-size: 14px; text-align:center;">Style</td>
<?php } ?>
					<td style="width:<?php if($_REQUEST['open']){ echo "110"; }else{ echo "117";}  ?>px; text-align:center<?php   ?>; font-size:14px;">Prac Code</td>
                    <td style="width:<?php if($_REQUEST['open']){ echo "90"; }else{ echo "127"; } ?>px; font-size: 14px; text-align:<?php if($_REQUEST['open']){ echo "left";}else{ echo "center";} ?>;"> &nbsp; Manufacturer</td>
                    <td style="width: <?php if($_REQUEST['open']){ echo "80";}else { echo "105";} ?>px; font-size: 14px; text-align:<?php if($_REQUEST['open']){ echo "center";}else{ echo "center";}  ?>;">&nbsp; Vendor</td>
                    <td class="res_brand" style="width:<?php if($_REQUEST['open']){ echo "80";}else { echo "115";} ?>px; font-size: 14px; text-align:center;">Brand</td>
					
	<?php // if(isset($_REQUEST['open'])) {echo "left";}else{echo "center"; } ?>				
                    <?php if($from=='style' || $search_id==1){ ?>
                    <td style="width: 70px; font-size: 14px; text-align:center;">Color</td>
                    <td style="width: 62px; font-size: 14px; text-align:center;">Shape</td>
                    <?php } ?>
                    <td style="width: 62px; font-size: 14px; text-align:<?php if($_REQUEST['open']){ echo "center";}else { echo "center";} ?>;">Cost</td>
                    <td style="width: 82px; font-size: 14px; text-align:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;T. Qty</td>
                    <td style="width: 82px; font-size: 14px; text-align:center;">Fac. Qty</td>
<?php if( in_array('A', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">A</td>
<?php endif; ?>
<?php if( in_array('B', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">B</td>
<?php endif; ?>
<?php if( in_array('ED', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">ED</td>
<?php endif; ?>
<?php if( in_array('DBL', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">DBL</td>
<?php endif; ?>
<?php if( in_array('Temple', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">Temple</td>
<?php endif; ?>
<?php if( in_array('Bridge', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">Bridge</td>
<?php endif; ?>
<?php if( in_array('FPD', $frameFieldsToshow) ): ?>
					<td style="width:40px;font-size: 14px; text-align:center;"">FPD</td>
<?php endif; ?>
                </tr>
             </table>
</div>            
                  <div style="width:<?php echo ($from=="style")?((1417)+$increaseWidth.'px;'):'99.7%;'; ?>;overflow-y:scroll; overflow-x: hidden;height:<?php echo $heigh_div;?>px; display:<?php if(isset($_POST['search_result']) || $from=='style' || $frm_dw=='paging'){ echo "block"; } else { echo "none"; } ?>">
                   <table class="table_collapse cellBorder table_cell_padd5" align="center">
				   		 <tr>
							<td style="width:30px;"></td>
							<!--<td style="width:90px; text-align:center; font-size:14px;">Type</td>-->
							<td style="width:<?php if($_REQUEST['open']){ echo "90"; }else{ echo "147"; } ?>px;"></td>
							<td style="width:<?php if($_REQUEST['open']){ echo ($noStyle)?"175":"80"; }else{ echo ($noStyle)?"227":"110"; } ?>px;"></td>
						<?php if(!$noStyle){ ?>
							<td style="width: <?php if($_REQUEST['open']){ echo "95";}else{ echo "117";} ?>px;"></td>
						<?php } ?>
							<td style="width:<?php if($_REQUEST['open']){ echo "110"; }else{ echo "117";} ?>px;"></td>
							<td style="width:<?php if($_REQUEST['open']){ echo "90"; }else{ echo "127"; } ?>px;"></td>
							<td style="width: <?php if($_REQUEST['open']){ echo "80";}else { echo "105";} ?>px;"></td>
							<td class="res_brand" style="width:<?php if($_REQUEST['open']){ echo "80";}else { echo "105";} ?>px;"></td>
							
						<?php if($from=='style' || $search_id==1){ ?>
							<td style="width: 70px;"></td>
							<td style="width: 62px;"></td>
						<?php } ?>
							<td style="width: 62px;"></td>
							<td style="width: 82px;"></td>
							<td style="width: 82px;"></td>
<?php if( in_array('A', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('B', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('ED', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('DBL', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('Temple', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('Bridge', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
<?php if( in_array('FPD', $frameFieldsToshow) ): ?>
							<td style="width:40px;"></td>
<?php endif; ?>
						</tr>
						<?php
						
						if(imw_num_rows($sql)>0){
                        $sr_no=1;
							$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
							$default_formula = '';
							
							/*Get Default Formula for the item Type*/
							if( in_array($search_id, $retail_price_markup_modules) ){
								if( $search_id=='1' ){
									$default_formula = get_retail_formula($search_id, array('manufacturer_id'=>$manufacturer_Id_Srch, 'brand_id'=>$opt_brand_id, 'frame_style'=>$style_id_search));
								}
								else{
									$default_formula = get_retail_formula($search_id, array('manufacturer_id'=>$manufacturer_Id_Srch, 'brand_id'=>$opt_brand_id));
								}
							}
							/*End Get Default Formula for the item Type*/
							
							while($sql_result = imw_fetch_array($sql)){
							
							/*Retail Prices Markup - Caclulation*/
							if( in_array($sql_result['module_type_id'], $retail_price_markup_modules) && $sql_result['retail_price_flag']=='0' ){
								
								if( trim($sql_result['formula'])=='' ){
									$sql_result['formula'] = $default_formula;
								}
								
								/*Final Retail Price for the Item - based on formula calculation*/
								if( $sql_result['formula']!='' ){
									$sql_result['retail_price'] = calculate_markup_price($sql_result['formula'],  $sql_result['wholesale_cost'],  $sql_result['purchase_price']);
								}
								/*End Final Retail Price for the Item*/
							}
							/*End Retail Prices Markup - Caclulation*/
							
							  	$sql_result['brand_id'];
								$sql_result['vendor_id'];
															  
							 $get_vandor_name=imw_query('select vendor_name from in_vendor_details where id in('.$sql_result['vendor_id'].')'); 
							 $rowsvandor_name = imw_fetch_array($get_vandor_name);
							if($_REQUEST['type_optical_id']==3)
							{
								$get_brand_name = imw_query('select brand_name as frame_source from in_contact_brand where id in('.$sql_result['brand_id'].')');
								
								//$get_style_name=imw_query('select style_name from in_contact_style where id in('.$sql_result['style'].')');
							}
							else
							{
							 	$get_brand_name=imw_query('select frame_source from in_frame_sources where id in('.$sql_result['brand_id'].')'); 
								
								$get_style_name=imw_query('select style_name from in_frame_styles where id in('.$sql_result['frame_style'].')');
							 }
							 $rowsbrand_name = imw_fetch_array($get_brand_name);
							 $rowsstyle_name = imw_fetch_array($get_style_name);
							 
							 $get_color_name=imw_query('select color_name from in_frame_color where id in('.$sql_result['color'].')'); 
							 $rowscolor_name = imw_fetch_array($get_color_name);
							 
							 $get_shape_name=imw_query('select shape_name from in_frame_shapes where id in('.$sql_result['frame_shape'].')'); 
							 $rowsshape_name = imw_fetch_array($get_shape_name);
							 
							$fac_stock='';
							//GETTING FACILITY STOCK
							$qry="Select SUM(stock) as 'fac_stock' FROM in_item_loc_total WHERE loc_id='".$_SESSION['pro_fac_id']."' AND item_id='".$sql_result['id']."'";
							$rs=imw_query($qry);
							$res=imw_fetch_assoc($rs);
							$fac_stock=$res['fac_stock'];
							unset($rs);							 
                        ?>
						 <tr>
                            <td style="text-align:center;"><?php echo $sr_no;?></td>
                            <!-- <td style="width:90px;text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['module_type_name']?></a></td>-->
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><span class="gg_val"><?php echo $sql_result['upc_code'];?></span></a></td>
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['name'];?></a>
							<input type="hidden" name="upc_hidden_val<?php echo $sr_no; ?>" id="upc_hidden_val<?php echo $sr_no; ?>" value="<?php echo $sql_result['id'];?>" />
							</td>
							<?php if(!$noStyle){ ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value);"><?php echo $rowsstyle_name['style_name'];?></a></td>
							<?php } ?>
							<td style="text-align:center;">
								<?php $qry = imw_query("select cpt_prac_code, cpt_desc from cpt_fee_tbl where cpt_fee_id='".$sql_result['item_prac_code']."' and delete_status = '0'");
								  $res = imw_fetch_assoc($qry); ?>
								<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')" title="<?php echo $res['cpt_desc'];?>"><?php echo $res['cpt_prac_code'];?></a>
							</td>
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['manufacturer_name']?></a></td>
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo  $rowsvandor_name['vendor_name']?></a></td>
                            <td class="res_brand_dis" style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $rowsbrand_name['frame_source']?></a></td>

							<?php if($from=="style" || $search_id==1){ ?>
                                                        
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value);"><?php echo $rowscolor_name['color_name'];?></a></td>
                            <td style="text-align:center;"><a class="txtcolr" href="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value);"><?php echo $rowsshape_name['shape_name'];?></a></td>
                            <?php } ?>
                  
                            <td style="text-align:right;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php currency_symbol(); ?><?php echo number_format($sql_result['retail_price'],2)?></a></td>

                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['qty_on_hand']?></a></td>

                            <td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $fac_stock;?></a></td>
							
<?php if( in_array('A', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['a']; ?></a></td>
<?php endif; ?>
<?php if( in_array('B', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['b']; ?></a></td>
<?php endif; ?>
<?php if( in_array('ED', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['ed']; ?></a></td>
<?php endif; ?>
<?php if( in_array('DBL', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['dbl']; ?></a></td>
<?php endif; ?>
<?php if( in_array('Temple', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['temple']; ?></a></td>
<?php endif; ?>
<?php if( in_array('Bridge', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['bridge']; ?></a></td>
<?php endif; ?>
<?php if( in_array('FPD', $frameFieldsToshow) ): ?>
							<td style="text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value, '<?php echo addslashes($sql_result['name']);?>')"><?php echo $sql_result['fpd']; ?></a></td>
<?php endif; ?>
                        </tr>
                        <?php 
                        $sr_no++;
                        }
						}
						else 
						{
							?>
                            <tr>
                            	<td colspan="14" align="center" height="50">
                                	No Record Found
                                </td>
                            </tr>
				<?php 	}	?>
                    </table> 
                  </div>
</div>  
 			</div>
			
<div style="text-align:center;margin-top:14px;">
<?php
	require_once'paging_new.php';
?>
</div>
			
<script>
$(document).ready(function(e) { 
	var type_id =$("#type_optical_id").val();
	var srch_id = document.getElementById('type_optical_id').value;
	var vendor_id ="<?php echo $opt_vendor_id; ?>";
	var brand_id ="<?php echo $opt_brand_id; ?>";
	var manufacture_id ="<?php echo $manufacturer_Id_Srch; ?>";
	get_type_manufacture(type_id,manufacture_id);
	
	if(manufacture_id!="" && manufacture_id!="0")
		get_vendorFromManufacturer(manufacture_id,vendor_id);
	if(vendor_id!="" && vendor_id!="0")
		get_brandFromVendor(vendor_id,brand_id,srch_id);
	else if(manufacture_id!="" && manufacture_id!="0" && brand_id!="" || brand_id!="0")
		get_brandFromManufacturer(manufacture_id,brand_id);
	
	show_brand(srch_id);
	change_width(srch_id);
	if("<?php echo $_REQUEST['brand'] ?>"!="")
		$("#opt_brand_id").trigger('change');
	
	dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	$("#frameFields").multiSelect(dd_pro);

<?php if( strtolower($selected_module)=='frame' ): ?>
	$("#color_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'frameColorsSearch',
		hidIDelem: document.getElementById('color'),
		showAjaxVals: 'name'
	});
<?php endif; ?>
});
</script>  
    </body>
</html>