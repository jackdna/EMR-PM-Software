<?php 
/*
File: lens_stock_search.php
Coded in PHP7
Purpose: Lens Stock Search Criteria
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

function get_data_from_query($tb,$ordby,$id="")
{
	if($tb=="in_manufacturer_details")
	{
		$whr.=" and lenses_chk='1'";
	}
	if($id!="")
	{
		$whr.=" and id IN($id)";
	}
	$qry = "select * from $tb where del_status='0' $whr order by $ordby asc";
	$res = data($qry);
	return $res;
}

$frm_method=$_REQUEST['frm_method'];
$search_id=$_REQUEST['srch_id'];
$manufature_id=$_REQUEST['manuf_id'];

$manufacturer_Id_Srch = $manufature_id;
$type_id_Srch=$_REQUEST['type'];
$material_id_Srch=$_REQUEST['material'];
$a_r_id_Srch=$_REQUEST['air'];
$transition_id_Srch=$_REQUEST['transition'];
$polarized_id_Srch=$_REQUEST['polarized'];
$tint_id_Srch=$_REQUEST['tint'];
$color_id_Srch=$_REQUEST['color'];

$from=$_REQUEST['from'];

if($from=='lenses' || $_POST['search_result'])
{

if($from=='lenses')
{
	$search_id=$_REQUEST['srch_id'];
	$manufacturer_Id_Srch = $_REQUEST['manuf_id'];
	$type_id_Srch = $_REQUEST['type'];
	$material_id_Srch = $_REQUEST['material'];
	$transition_id_Srch = $_REQUEST['transition'];
	$a_r_id_Srch = $_REQUEST['air'];
	$tint_id_Srch = $_REQUEST['tint'];
	$polarized_id_Srch = $_REQUEST['polarized'];
	$color_id_Srch = $_REQUEST['color'];

}
if($_POST['search_result'])
{
	$manufacturer_Id_Srch = $_POST['manufacturer_Id_Srch'];
	$type_id_Srch = $_POST['type_id_Srch'];
	$material_id_Srch = $_POST['material_id_Srch'];
	$transition_id_Srch = $_POST['transition_id_Srch'];
	$a_r_id_Srch = $_POST['a_r_id_Srch'];
	$tint_id_Srch = $_POST['tint_id_Srch'];
	$polarized_id_Srch = $_POST['polarized_id_Srch'];
	$color_id_Srch = $_POST['color_id_Srch'];
	$pgx_Srch = $_POST['pgx_Srch'];
	$uv400_Srch = $_POST['uv400_Srch'];
	$upcval = $_POST['upc_name'];
	$name_txt = $_POST['name_txt'];
	
	
	$sph = $_POST['sph'];
	$cyl = $_POST['cyl'];
}

if($search_id<=0){
	$search_id=1;
}
$and="";
if($manufacturer_Id_Srch!='' && $manufacturer_Id_Srch>0){
	$and.=" and it.manufacturer_id='$manufacturer_Id_Srch'";
}
elseif($manufature_id!='' && $manufature_id>0)
{
	$and.=" and it.manufacturer_id='$manufature_id'";
}
if($type_id_Srch!='' && $type_id_Srch>0){
	$and.=" and it.type_id='$type_id_Srch'";
}
if($material_id_Srch!='' && $material_id_Srch>0){
	$and.=" and it.material_id='$material_id_Srch'";
}

if($transition_id_Srch!='' && $transition_id_Srch>0){
	$and.=" and it.transition_id='$transition_id_Srch'";
}

if($a_r_id_Srch!='' && $a_r_id_Srch>0){
	$and.=" and FIND_IN_SET($a_r_id_Srch,it.a_r_id)";
}

if($tint_id_Srch!='' && $tint_id_Srch>0){
	$and.=" and it.tint_id='$tint_id_Srch'";
}

if($polarized_id_Srch!='' && $polarized_id_Srch>0){
	$and.=" and it.polarized_id='$polarized_id_Srch'";
}

if($color_id_Srch!='' && $color_id_Srch>0){
	$and.=" and it.color='$color_id_Srch'";
}

if($pgx_Srch=='1'){
	$and.=" and it.pgx_check='$pgx_Srch'";
}

if($uv400_Srch=='1'){
	$and.=" and it.uv_check='$uv400_Srch'";
}

if($upcval!=''){
	$and.=" and it.upc_code like('$upcval%')";
}

if($name_txt!=''){
	$and.=" and it.name like ('$name_txt%')";
}

if(isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1'){
	$and.=" And it.qty_on_hand>0";
	$paging_qry['in_stock_chk'] = '1';	
}

if($sph!=''){
	$str=" AND (('$sph' BETWEEN it.sphere_positive AND it.sphere_positive_max) OR ('$sph' BETWEEN it.sphere_negative AND it.sphere_negative_max) OR (it.sphere_negative='' AND it.sphere_negative_max=''))";
	if(strstr($sph,'+'))//search in positive fields
	{
		$sph1=str_replace('+','',$sph);
		$str=" AND (('$sph1' BETWEEN it.sphere_positive AND it.sphere_positive_max) OR (it.sphere_positive='' AND it.sphere_positive_max=''))";
	}
	elseif(strstr($sph,'-'))//search in positive fields
	{
		$sph1=str_replace('-','',$sph);
		$str=" AND (('$sph1' BETWEEN it.sphere_negative AND it.sphere_negative_max) OR (it.sphere_negative='' AND it.sphere_negative_max=''))";
	}
	
	$and.=$str;
}

if($cyl!=''){
	$str=" AND (('$cyl' BETWEEN it.cylindep_positive AND it.cylindep_positive_max) OR ('$cyl' BETWEEN it.cylindep_negative AND it.cylindep_negative_max) OR (it.cylindep_negative='' AND it.cylindep_negative_max=''))";
	if(strstr($cyl,'+'))//search in positive fields
	{
		$cyl1=str_replace('+','',$cyl);
		$str=" AND (('$cyl1' BETWEEN it.cylindep_positive AND it.cylindep_positive_max) OR (it.cylindep_positive='' AND it.cylindep_positive_max=''))";
	}
	elseif(strstr($cyl,'-'))//search in positive fields
	{
		$cyl1=str_replace('-','',$cyl);
		$str=" AND (('$cyl1' BETWEEN it.cylindep_negative AND it.cylindep_negative_max) OR (it.cylindep_negative='' AND it.cylindep_negative_max=''))";
	}
	$and.=$str;
}

	$qry = "select it.*,
	FT.module_type_name,MT.manufacturer_name
	$tb_field
	from in_item  as it 
	LEFT join in_module_type as FT on FT.id = it.module_type_id
	LEFT join in_manufacturer_details as MT on MT.id = it.manufacturer_id
	where it.del_status='0' and it.module_type_id = '$search_id'
	".$and." order by it.upc_code asc, it.name asc";
	$sql = imw_query($qry);
}


 ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
window.opener = window.opener.main_iframe.admin_iframe;
</script>
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
</style>
<script language="javascript">
var rx='<?php echo $_REQUEST['rx'];?>';
 
function changeParent(upc_val) { 
	if(typeof(window.opener)!="undefined"){
		var page_Other = "<?php echo $_REQUEST['module_typePat'];?>";
		var frm_method = "<?php echo $_REQUEST['frm_method'];?>";
		var search_id = "<?php echo $_REQUEST['srch_id'];?>";
		var itemCounter = "<?php echo $_REQUEST['itemCounter'];?>";
		if(search_id=='2')
		{
			if(itemCounter>0)
			{
				if(window.opener.document.getElementById('upc_id_'+itemCounter+'_lensD')){
					window.opener.document.getElementById('upc_id_'+itemCounter+'_lensD').value=upc_val;
					window.opener.document.getElementById("upc_name_"+itemCounter+"_lensD").onchange();	
					//window.opener.get_details_by_upc_lensD(upc_val);
				}else{
					window.opener.location.href = window.opener.location+'?upc_name_lens='+upc_val+'&frm_method_lens='+frm_method;	
				}		
			}
			else
			{
				if(window.opener.document.getElementById('upc_id')){
					window.opener.document.getElementById('upc_id').value=upc_val;
					window.opener.document.getElementById("upc_name").onchange();	
					//window.opener.get_details_by_upc_lensD(upc_val);
				}else{
					window.opener.location.href = window.opener.location+'?upc_name_lens='+upc_val+'&frm_method_lens='+frm_method;	
				}	
			}
			
		}
		else { 
				window.opener.location.href = window.opener.location+'?upc_name='+upc_val+'&frm_method='+frm_method;
		}	 
	}
  	window.close();
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

/*Validate Form*/
function submitForm(){
	
	sOpts = new Array();
	sOpts[0] = manufacturer = $("#manufacturer_Id_Srch").val();
	sOpts[1] = vendor = $("#type_id_Srch").val();
	sOpts[2] = brand = $("#material_id_Srch").val();
	sOpts[3] = color = $("#transition_id_Srch").val();
	sOpts[4] = shape = $("#a_r_id_Srch").val();
	sOpts[5] = style = $("#tint_id_Srch").val();
	sOpts[6] = minPrice = $("#polarized_id_Srch").val();
	sOpts[7] = maxPrice = $("#color_id_Srch").val();
	sOpts[8] = pgx = ($("#pgx_Srch").is(":checked"))?"1":"0";
	sOpts[9] = uv = ($("#uv400_Srch").is(":checked"))?"1":"0";
	sOpts[10] = upcId = $("#upc_name").val();
	sOpts[11] = prodName = $("#name_txt").val();
	
	sOpts[12] = sph = $("#sph").val();
	sOpts[13] = cyl = $("#cyl").val();
	
	
	nullVals = new Array("0", "Min", "Max", " ", "");
	error = true;
	$(sOpts).each(function(key,val){
		if(nullVals.indexOf(val)== "-1"){
			error = false;
		}
	});
	
	if(error){
		top.falert("Please make a selection");
		return false;
	}
	else{
		return true;
	}
}
var item_counter='<?php echo ($_REQUEST['itemCounter'])?$_REQUEST['itemCounter']:1;?>'
$(document).ready(function(e) {
   if(rx=='1'){
	   if(document.getElementById('shp').value==''){
		  document.getElementById('shp').value=window.opener.document.getElementById('lens_sphere_od_'+item_counter+'_lensD').value;
	   }
	   if(document.getElementById('cyl').value==''){
		  document.getElementById('cyl').value=window.opener.document.getElementById('lens_cylinder_od_'+item_counter+'_lensD').value;
	   }
   }
});
</script> 
</head>
<body>
	<div style="padding:0px; width:1040px; margin:0 auto;">
        <div class="listheading">
			<div style="text-align:center;">Lens Search</div>
		</div>
        <div>
        	<form method="post" action="" name="stock_srch_frm">
               <table  style="width:100%;margin-top:5px;">
                <tr class="table_collapse listheading">
					<td style="width:110px; text-align:center;">Manufacturer</td>
					<td style="width:100px; text-align:center;">Seg Type</td>
					<td style="width:75px; text-align:center;">Material</td>
					<!--<td style="width:85px; text-align:center;">Transition</td>-->
					<td style="width:65px; text-align:center;">Treatment</td>
					<?php if($_REQUEST['rx']==1){?>
					<td style="width:60px; text-align:center;">SPH</td>
					<td style="width:60px; text-align:center;">CYL</td>
					<?php }else{?>
                    <!--<td style="width:70px; text-align:center;">Tint</td>
					<td style="width:85px; text-align:center;">Polarized</td>
					<td style="width:60px; text-align:center;">Color</td>
					<td style="width:35px; text-align:center;">PGX</td>
					<td style="width:55px; text-align:center;">UV400</td>-->
                    <?php } ?>
					<td style="width:60px; text-align:center;">UPC</td>
					<td style="width:60px; text-align:center;">Name</td>
					<td style="width:60px; text-align:center;">In Stock</td>
					<td style="width:40px; text-align:center;">&nbsp;</td>
                </tr>
               
                <tr>
               	<td style="text-align:center;">
                	<select name="manufacturer_Id_Srch" id="manufacturer_Id_Srch" style="width:90%;">
                    	<option value="">Select Manufacturer</option>
                        <?php $rows_manufacturer="";
                              $rows_manufacturer = get_data_from_query('in_manufacturer_details','manufacturer_name');
							  foreach($rows_manufacturer as $result_manufatchr)
                              {
								   ?>
                                <option value="<?php echo $result_manufatchr['id']; ?>" <?php if($result_manufatchr['id']==$manufacturer_Id_Srch) { echo "selected"; } ?>><?php echo ucfirst($result_manufatchr['manufacturer_name']); ?></option>	
                        <?php } ?>
                    </select>
                </td>
                <td style="text-align:center;">
                	<select name="type_id_Srch" id="type_id_Srch" style="width:90%;">
						<option value="">Select</option>
						<?php  
						$seg_qry = "SELECT `id`, `type_name` FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')";
						$seg_resp = imw_query($seg_qry);
						
						while($focal = imw_fetch_object($seg_resp)){ ?>
						<option <?php if($type_id_Srch==$focal->id){ echo "selected='selected'"; } ?> value="<?php echo $focal->id; ?>"><?php echo $focal->type_name; ?></option>
						<?php }	?> 
					</select>
				</td>
                <td style="text-align:center;">
                	<select name="material_id_Srch" id="material_id_Srch" style="width:90%;">
                	<option value="">Select</option>
				<?php  
                $material="";
				$material = get_data_from_query('in_lens_material','material_name');
                foreach($material as $material_rows)
                 { ?>
					<option <?php if($material_id_Srch==$material_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $material_rows['id']; ?>"><?php echo $material_rows['material_name']; ?></option>	
					<?php }	?>
                </select>
				</td>
                <!--td style="text-align:center;">
                	<select name="transition_id_Srch"  id="transition_id_Srch" style="width:85px;">
						<option value="">Select</option>
							<?php  
							/*$transition="";
							$transition = get_data_from_query('in_lens_transition','transition_name');
							foreach($transition as $transition_rows)
                 			{?>
							<option <?php if($transition_id_Srch==$transition_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $transition_rows['id']; ?>"><?php echo $transition_rows['transition_name']; ?></option>
							
							<?php }*/	?>
					</select>
				</td-->
              
			    <td style="text-align:center;">
                	<select name="a_r_id_Srch"  id="a_r_id_Srch" style="width:90%;">
                	<option value="">Select</option>
					<?php  
                    $a_r="";
					$a_r = get_data_from_query('in_lens_ar','ar_name');
                    foreach($a_r as $a_r_rows)
                 	{ ?>
                    <option <?php if($a_r_id_Srch==$a_r_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $a_r_rows['id']; ?>"><?php echo $a_r_rows['ar_name']; ?></option>
                    <?php }	?>
                </select>
				</td>
				<?php if($_REQUEST['rx']==1){?>
					<td style="width:<?php echo $td_width;?>px; text-align:center;"><input type="text" name="sph" id="shp" value="<?php echo $sph;?>" style="width:90%;" /></td>
					<td style="width:<?php echo $td_width;?>px; text-align:center;"><input type="text" name="cyl" id="cyl" value="<?php echo $cyl;?>" style="width:90%;" /></td>
				<?php }else{/*
					
					?>
                    <td style="text-align:center;">
                    <select name="tint_id_Srch" id="tint_id_Srch" style="width:70px;">
                        <option value="">Select</option>
                        <?php  
                        $tint="";
                        $tint = get_data_from_query('in_lens_tint','tint_type');
                        foreach($tint as $tint_rows)
                        { ?>
                        <option <?php if($tint_id_Srch==$tint_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $tint_rows['id']; ?>"><?php echo $tint_rows['tint_type']; ?></option>
                        <?php }	?>
                    </select>
                    </td>
                    
                    <td style="text-align:center;">
                    <select name="polarized_id_Srch"  id="polarized_id_Srch" style="width:85px;">
                        <option value="">Select</option>
                        <?php
                        $polarized="";
                        $polarized = get_data_from_query('in_lens_polarized','polarized_name');
                        
                        foreach($polarized as $polarized_rows)
                        { ?>
                        <option <?php if($polarized_id_Srch==$polarized_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $polarized_rows['id']; ?>"><?php echo $polarized_rows['polarized_name']; ?></option>
                        <?php }	?>
                    </select>
                    </td>
                    
                    <td style="text-align:center;">
                    <select name="color_id_Srch" id="color_id_Srch" style="width:60px;">
                        <option value="">Select</option>
                        <?php  
                        $color="";
                        $color = get_data_from_query('in_lens_color','color_name');
                        foreach($color as $color_rows)
                        { ?>
                        <option <?php if($color_id_Srch==$color_rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $color_rows['id']; ?>"><?php echo $color_rows['color_name']; ?></option>
                        <?php }	?> 
                    </select>
                    </td>
                    
                    <td style="text-align:center;">
                        <input type="checkbox" name="pgx_Srch" id="pgx_Srch" value="1" <?php if($pgx_Srch==1){echo"checked";} ?>>
                    </td>
                    
                    <td style="text-align:center;">
                        <input type="checkbox" name="uv400_Srch" id="uv400_Srch" value="1" <?php if($uv400_Srch==1){echo"checked";} ?>>
                    </td>
                <?php */}
				?>
               	<td style="width:<?php echo $td_width;?>px; text-align:center;">
                	<input type="text" name="upc_name" id="upc_name" value="<?php echo $upcval;?>" style="width:90%;" />
                </td>
                <td style="text-align:center;">
                    <input type="text" name="name_txt" id="name_txt" style="width:90%;" value="<?php echo $name_txt; ?>" />
				</td>
				<td style="text-align:center;">
					<input style="margin:0;height:18px;width:18px;vertical-align:middle;cursor:pointer;" type="checkbox" name="in_stock_chk" id="in_stock_chk" value="1" <?php echo (isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1')?'checked':''; ?>/>
				</td>
                <td style="text-align:center;">
                  <input class="btn_cls" type="submit" name="search_result" value="Search" onClick="return submitForm();" /></td>
                </tr>
               </table>
              </form> 
        </div>
<table class="table_collapse listheading table_cell_padd5" align="center" style="width:99.7%; margin-top:5px; display:<?php if($from=='lenses' || isset($_POST['search_result'])){ echo "inline-table"; } else { echo "none"; } ?>;">
                <tr>
                    <td style="width:48px; text-align:center; font-size:14px;">Sr&nbsp;No.</td>
                    <td style="text-align: center; font-size: 14px; width: 95px;">UPC-Name</td>
                    <td style="text-align: center; font-size: 14px; width: 100px;">Manufacturer</td>
					<td style="text-align: center; font-size: 14px; width: 90px;">Seg Type</td>
					<td style="text-align: center; font-size: 14px; width: 88px;">Material</td>
					<!--<td style="width: 93px; font-size: 14px; text-align: center;">Transition</td>-->
					<td style="width: 93px; font-size: 14px; text-align: center;">Treatment</td>
					<!--<td style="width: 93px; font-size: 14px; text-align: center;">Tint</td>
					<td style="width:93px; text-align:center; font-size:14px;">Polarized</td>
					<td style="width: 80px; font-size: 14px; text-align: center;">Color</td>-->
                    <td style="width:47px; text-align:left; font-size:14px;">Cost</td>
                    <td style="width:55px; text-align:left; font-size:14px;">T. Qty</td>
                    <td style="width:65px; text-align:left; font-size:14px;">Fac. Qty</td>
                </tr>
             </table>
            
                  <div style="overflow-y:scroll; height:300px; width:99.7%; display:<?php if($from=='lenses' || isset($_POST['search_result'])){ echo "block"; } else { echo "none"; } ?>">
                
                   <table class="table_collapse cellBorder table_cell_padd5" align="center" style="width:99.7%" >   
						<tr>
							<td style="width:50px;border:0px;"></td>
							<td style="width:100px;border:0px;"></td>
							<td style="width:87px;border:0px;"></td>
							<td style="width:100px;border:0px;"></td>
							<td style="width:75px;border:0px;"></td>
							<!--<td style="width:93px;border:0px;"></td>-->
							<td style="width:93px;border:0px;"></td>
							<!--<td style="width:93px;border:0px;"></td>
							<td style="width:93px;border:0px;"></td>
							<td style="width:80px;border:0px;"></td>-->
							<td style="width:47px;border:0px;"></td>
							<td style="width:55px;border:0px;"></td>
							<td style="width:75px;border:0px;"></td>
						</tr>
						
						<?php 
						if(imw_num_rows($sql)>0){
                        $sr_no=1;
						
                          while($sql_result = imw_fetch_array($sql)){
							$fac_stock='';
							
							//GETTING FACILITY STOCK
							$qry="Select SUM(stock) as 'fac_stock' FROM in_item_loc_total WHERE loc_id='".$_SESSION['pro_fac_id']."' AND item_id='".$sql_result['id']."'";
							$rs=imw_query($qry);
							$res=imw_fetch_assoc($rs);
							$fac_stock=$res['fac_stock'];
							unset($rs);
                        ?>
                         <tr>
                            <td class="break_word" style="width:50px; text-align:center;"><?php echo $sr_no;?>
								<input type="hidden" name="upc_hidden_val<?php echo $sr_no; ?>" id="upc_hidden_val<?php echo $sr_no; ?>" value="<?php echo $sql_result['id'];?>" />
							</td>
							
                            <td class="break_word" style="width:110px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><span class="gg_val"><?php if($sql_result['name']!="") { echo $sql_result['upc_code']."-".$sql_result['name'];} else { echo $sql_result['upc_code']; } ?></span></a></td>
							
                            <td class="break_word" style="width:110px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['manufacturer_name']?></a></td>
							
                            <td class="break_word" style="width:100px; text-align:center;">
							<?php unset($focal_arr);if($sql_result['type_id']>0){$focal_arr = get_data_from_query('in_lens_type','type_name',$sql_result['type_id']);}
							foreach($focal_arr as $res_focal)
								{ ?>
							<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
							<?php echo $res_focal['type_name']; ?></a> 
							<?php }	?>
							</td>
							<td class="break_word" style="width:100px; text-align:center;">
								<?php $material = get_data_from_query('in_lens_material','material_name',$sql_result['material_id']); 
								foreach($material as $res_material)
								{ ?> 
								<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
								<?php echo $res_material['material_name'];?></a><?php }?>
							</td>
							<!--<td style="width:100px; text-align:center;">
								<?php 
								/*$transition = get_data_from_query('in_lens_transition','transition_name',$sql_result['transition_id']);
								foreach($transition as $res_transition)
								{?> 
								<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
								<?php echo $res_transition['transition_name'];?></a> <?php }*/ ?>
							</td>-->
							<td class="break_word" style="width:100px; text-align:center;">
							<?php
								if($sql_result['a_r_id']!=""){
									$a_r = get_data_from_query('in_lens_ar','ar_name',$sql_result['a_r_id']);
									foreach($a_r as $res_a_r){
							?>
										<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
										<?php echo $res_a_r['ar_name'];?></a> 
							<?php 
									}
								}
								else{
									echo '<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById(\'upc_hidden_val'.$sr_no.'\').value)">ALL</a>';
								}
							?>
							</td>
							<!--<td style="width:100px; text-align:center;">
							<?php /*$tint = get_data_from_query('in_lens_tint','tint_type',$sql_result['tint_id']);
								  foreach($tint as $res_tint)
								  { ?>
								  <a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
								  <?php echo $res_tint['tint_type'];?></a> <?php }*/
							?>
							</td>
							<td style="width:100px; text-align:center;">
								<?php 
								/*if($sql_result['polarized_id']>0){
								$polarized = get_data_from_query('in_lens_polarized','polarized_name',$sql_result['polarized_id']);
								foreach($polarized as $res_polarized)
                 					{ ?>
									<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
									<?php echo $res_polarized['polarized_name']; ?></a> <?php }
									
								}*/?>
							</td>
							<td style="width:80px; text-align:center;">
							<?php 
							unset($edge_arr);
							 /*if(trim($sql_result['color'])!=""){$edge_arr = get_data_from_query('in_lens_color','color_name',$sql_result['color']);}
								foreach($edge_arr as $res_edge)
                 				{ ?>
								<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)">
								<?php echo $res_edge['color_name'];?></a> <?php }*/?>
							</td>-->

                            <td class="break_word" style="width:50px; text-align:right;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php currency_symbol(); ?><?php echo number_format($sql_result['retail_price'],2)?></a></td>
							
                            <td class="break_word" style="width:50px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['qty_on_hand']?></a></td>
                            <td class="break_word" style="width:50px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $fac_stock;?></a></td>
                        </tr>
                        <?php 
                        $sr_no++;
                        }
						}
						else 
						{
							?>
                            <tr>
                            	<td colspan="13" align="center" height="50">
                                	No Record Found
                                </td>
                            </tr>
				<?php 	}	?>
                    </table> 
                  </div>  
 			</div>
    </body>
</html>