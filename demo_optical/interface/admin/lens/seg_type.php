<?php
/*
File: seg_type.php
Purpose: Add/Edit/Delete: Seg Type Vcodes
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");

$msg_stat = "none";
$opr_id = $_SESSION['authId'];
$date = date("Y-m-d");
$time = date("h:i:s");
  
  //------------------------START GETTING DATA Lens Type, CPT4, Prac code-----------------------//
$proc_code_arr=array();
$proc_code_desc_arr=array();
$proc_cpt4_code_arr=array();
$lensarr = array();
 
$sql_ltype = "select id, type_name from `in_lens_type` where del_status = '0' order by FIELD(type_name, 'Single Vision', 'Progressive', 'BiFocal', 'TriFocal') ";
$res_ltype = imw_query($sql_ltype);
$num_ltype = imw_num_rows($res_ltype);
	if($num_ltype>0){
		while($row_ltype = imw_fetch_assoc($res_ltype)){
		  $lenstypeid= $row_ltype['id'];
		  $lenstypename=$row_ltype['type_name'];
		  $lenstypename_arr[]=array($row_ltype[$lenstypename]);
		  $lensarr[$lenstypeid] = $lenstypename;
		}
	}
  
  //------------- UPDATE AND INSERT NEW Seg TYPE Vcodes --------------//
  if(isset($_POST['save_action'])){	
	  
	  if(count($_POST['rec'])>0){
		  
		  $action = "save";
		  if($_POST['save_action']=="delete"){
			  $action = "del";
		  }
		   
		  $records = $_POST['rec'];
		  foreach($records as $record){
			  
			  if(!isset($record['id']))
				  continue;
			  
			  if($action=='del'){
				  
				  if($record['id']!=''){
					  $del_qry = 'UPDATE `in_lens_type_vcode`  SET `del_status`=2, `del_date`="'.$date.'", `del_time`="'.$time.'", `del_by`="'.$opr_id.'" WHERE `id`='.$record['id'];
					  imw_query($del_qry);
				  	  $msg = "Record(s) deleted successfully";
				  	  $msg_stat = "block";	
				  }
				  continue;
			  }
			  
			  if($record['id']!=''){
				
				  $procedureId = back_prac_id($record['Sv_it'], false, 2);
				  
				  $updateQry = "update `in_lens_type_vcode` set lens_type_id='".$record['type_id']."', lens_type='".$lensarr[$record['type_id']]."',prac_code='".$record['Sv_it']."',  prac_id='".$procedureId."', sph_plus_from='".$record['sph_plus_from']."', sph_plus_to='".$record['sph_plus_to']."', sph_min_from='".$record['sph_min_from']."', sph_min_to='".$record['sph_min_to']."', cyl_from='".$record['cyl_from']."', cyl_to='".$record['cyl_to']."', modified_date='".$date."', modified_time='".$time."', modified_by='".$opr_id."',wholesale_price='".$record['wholesale_price']."',purchase_price='".$record['purchase_price']."', retail_price='".$record['retail_price']."', entry_type='".$record['entry_type']."' where id = '".$record['id']."'";
				  imw_query($updateQry);
				  $msg = "Record(s) saved successfully";
				  $msg_stat = "block";
			  }
			  else{
				  
				  $procedureId = back_prac_id($record['Sv_it'], false, 2);
				  
				  $InsertQry = "INSERT `in_lens_type_vcode` set lens_type_id='".$record['type_id']."', lens_type='".$lensarr[$record['type_id']]."',prac_code='".$record['Sv_it']."', prac_id='".$procedureId."', sph_plus_from='".$record['sph_plus_from']."', sph_plus_to='".$record['sph_plus_to']."', sph_min_from='".$record['sph_min_from']."', sph_min_to='".$record['sph_min_to']."', cyl_from='".$record['cyl_from']."', cyl_to='".$record['cyl_to']."', entered_date='".$date."', entered_time='".$time."', entered_by='".$opr_id."',wholesale_price='".$record['wholesale_price']."',purchase_price='".$record['purchase_price']."', retail_price='".$record['retail_price']."', entry_type='".$record['entry_type']."'";
				  imw_query($InsertQry);
				  $msg = "Record(s) saved successfully";
				  $msg_stat = "block";
			  }
		  }
	  }
  }
  
$sql = "select * from cpt_category_tbl order by cpt_category ASC";
$rez = imw_query($sql);	
while($row=imw_fetch_array($rez)){
	$cat_id = $row["cpt_cat_id"];		
	$sql = "select * from cpt_fee_tbl WHERE cpt_cat_id='".$cat_id."' AND status='active' AND delete_status = '0' order by cpt_prac_code ASC";
	$rezCodes = imw_query($sql);
	$arrSubOptions = array();
	if(imw_num_rows($rezCodes) > 0){
		while($rowCodes=imw_fetch_array($rezCodes)){
			$arrSubOptions[] = array($rowCodes["cpt_prac_code"]."-".$rowCodes["cpt_desc"],$xyz, $rowCodes["cpt_prac_code"]);
			$arrCptCodesAndDesc[] = $rowCodes["cpt_fee_id"];
			$arrCptCodesAndDesc[] = $rowCodes["cpt_prac_code"];
			$arrCptCodesAndDesc[] = $rowCodes["cpt_desc"];
			$arrCptCodesAndDesc[] = $rowCodes["cpt4_code"];
			
			$proc_code_arr[$rowCodes["cpt_fee_id"]]=trim($rowCodes["cpt_prac_code"]);
			$proc_code_desc_arr[$rowCodes["cpt_fee_id"]]=trim(($rowCodes["cpt_desc"]));
			$proc_cpt4_code_arr[$rowCodes["cpt_fee_id"]]=trim($rowCodes["cpt4_code"]);
		}
	$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
	}		
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="../../../library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="../../../library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
function del_callBack(result)
{
	if(result==true)
	{
		$("#save_action").val("delete");
		$("#addsigtype").submit();
	}	
}

$(document).ready(function(){
	
	del = function()
	{
	 	if( $(".getchecked:checked").length == 0 ) 
		{
           top.falert('Please check atleast one record');
        }
		else
		{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	
	$("#selectall").click(function()
	{		
		if($(this).is(":checked"))
		{
			$(".getchecked").prop('checked', true);
		}
		else
		{
			$(".getchecked").prop('checked', false);
		}
	});
		
});

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

<?php if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php } ?>

var rowData_c='';
var tr  = '';
	
function addrow(id, type_id)
{  
  var getRows = $("#totRows").val();
  y = parseInt(getRows)+1;
  rowData_c+='<tr id="tr_b_'+y+'">';
  rowData_c+='<td style="width:20px;">';
  	rowData_c+='<input class="getchecked" type="checkbox" value="" id="checked_'+y+'" name="rec['+y+'][id]" />';
  	rowData_c+='<input type="hidden" name="rec['+y+'][type_id]" id="type_id_'+y+'" value="'+type_id+'" />';
  	rowData_c+='<input type="hidden" name="rec['+y+'][entry_type]" id="entry_type_'+y+'" value="1" />';
  rowData_c+='</td>';
    
	rowData_c+='<td style="width:100px;text-align:center;"><input type="text" class="prac_code" id="Sv_it_'+y+'" style="width:90px;" name="rec['+y+'][Sv_it]" value="" onChange="$(\'#Sv_it_cpt4_'+y+'\').val(this.value); check_row('+y+');" autocomplete="off" /><input type="hidden" id="Sv_it_desc_'+y+'" style="width:320px;" name="rec['+y+'][Sv_it_desc]" value="" disabled /><input type="hidden" id="Sv_it_cpt4_'+y+'" style="width:70px;" name="rec['+y+'][Sv_it_cpt4]" value=""disabled /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="sph_plus_from_'+y+'" style="width:70px;" name="rec['+y+'][sph_plus_from]" value="" onchange="check_row('+y+');" /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="sph_plus_to_'+y+'" style="width:70px;" name="rec['+y+'][sph_plus_to]" value="" onchange="check_row('+y+');" /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="sph_min_from_'+y+'" style="width:70px;" name="rec['+y+'][sph_min_from]" value="" onchange="check_row('+y+');" /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="sph_min_to_'+y+'" style="width:70px;" name="rec['+y+'][sph_min_to]" value="" onchange="check_row('+y+');" /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="cyl_from_'+y+'" style="width:70px;" name="rec['+y+'][cyl_from]" value="" onchange="check_row('+y+');" /></td>';
                
    rowData_c+='<td style="width:80px;"><input type="text" id="cyl_to_'+y+'" style="width:70px;" name="rec['+y+'][cyl_to]" value=""  onchange="check_row('+y+');"/></td>';
     
	rowData_c+='<td style="width:110px;"><input type="text" id="wholesale_price_'+y+'" name="rec['+y+'][wholesale_price]" style="width:100px;" value=""  onChange="check_row('+y+');" /></td>';
	rowData_c+='<td style="width:110px;"><input type="text" id="purchase_price_'+y+'" name="rec['+y+'][purchase_price]" style="width:100px;" value=""  onChange="check_row('+y+');" /></td>';
	rowData_c+='<td style="width:110px;"><input type="text" id="retail_price_'+y+'" name="rec['+y+'][retail_price]" style="width:100px;" value=""  onChange="check_row('+y+');" /></td>';
						 
	 
	rowData_c+='<td align="right"> <img style="cursor:pointer;" id="addbtn_'+y+'" onClick="addrow('+y+','+type_id+');" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" /> ';
	
	rowData_c+='</tr>';
	$("#tr_b_"+id).after(rowData_c);
	rowData_c='';
	$("#totRows").val(y);

	var totalRows = $(".countrow tr").size();
	
	$("#Sv_it_"+y).ajaxTypeahead({ 
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeOSeg',
		hidIDelem: $('#Sv_it_desc_'+y)
	});
}
function check_row(i){
	var checkBox = $('#checked_'+i);
	if(checkBox.length>0)
		$(checkBox).prop('checked', true);
}
</script>
</head>
<body>
<form name="addsigtype" id="addsigtype" action="" method="post" class="mt10">
	<div  class="listheading" style="background: #0088cc;">Lens – Power Charges</div>
    <table class="table_collapse" style="width:1120px;">
        <tr class="listheading">
            <td style="width:20px;">  <input type="hidden" id="save_action" name="save_action" value="save" />
            <input type="checkbox" id="selectall" value="" /></td>
            <td style="width:110px;text-align:left;">Practice Code</td>
            <td style="width:140px;text-align:left;">Sphere Range +</td>
            <td style="width:140px;text-align:left;">Sphere Range -</td>
            <td style="width:100px;text-align:left;">Cyl Range</td>
			<td style="width:95px;text-align:left;">Wholesale Price</td>
			<td style="width:95px;text-align:left;">Purchase Price</td>
			<td style="width:120px;text-align:left;">Retail Price</td>
        </tr>
    </table>
    <div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-600;?>px; overflow-x: hidden; overflow-y:auto;">
		<span class="success_msg" style="display:<?php echo $msg_stat;?>;text-align:left;padding-top:2px; width:160px; font-weight:bold;"><?php echo $msg; ?></span>
        <table class="table_collapse" id="POITable">
	    	<tbody class="table_cell_padd2">
	            <?php 
					$i=1;
					foreach($lensarr as $key=>$val){
					?>	
						<tr><td class="even" style="font-weight:bold;padding-top:3px; width:1080px;" colspan="12"> <?php echo $val;?></td></tr>
					<?php	
					//Get and displayed data of seg type vcodes
					$sql="select * from `in_lens_type_vcode` where del_status = '0' and lens_type_id='$key' and entry_type=1 order by prac_id asc";
					$res = imw_query($sql);
	                $num = imw_num_rows($res);
					
	                if($num>0)
						{
							while($row = imw_fetch_array($res))
							{
								$status = $row['del_status'];
								$lenstypecodeid = $row['lens_type_id']; 
								$lenstype = $row['lens_type'];
								$prac_id = $row['prac_id'];
								$prac_code = $row['prac_code'];
								$proc_code_name = $proc_code_arr[$prac_id];
								$proc_code_desc = $proc_code_desc_arr[$prac_id];
								$proc_cpt4_code = $proc_cpt4_code_arr[$prac_id];
								if($i%2==0)	
								{
									$rowbg="even";	
								}
								else
								{
									$rowbg="odd";	
								}
						?>
						<tr class="<?php echo $rowbg; ?>" id="tr_b_<?php echo $i; ?>">
							<td style="width:20px;"><input class="getchecked" type="checkbox" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][id]" />
								<input type="hidden" name="rec[<?php echo $i; ?>][type_id]" id="type_id_<?php echo $i; ?>" value="<?php echo $key; ?>" />
								<input name="rec[<?php echo $i; ?>][entry_type]" id="entry_type_<?php echo $i; ?>" type="hidden" value="1">
							</td>
							
							<td style="width:100px;text-align:center;">
								<input type="text" class="prac_code" id="Sv_it_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][Sv_it]" style="width:90px;" value="<?php echo $proc_code_name;?>" onChange="$('#Sv_it_cpt4_<?php echo $i; ?>').val(this.value); check_row(<?php echo $i; ?>);" autocomplete="off" />
								<input type="hidden" id="Sv_it_desc_<?php echo $i; ?>" style="width:320px;" name="rec[<?php echo $i; ?>][Sv_it_desc]" value="<?php echo $proc_code_desc; ?>" onChange="check_row(<?php echo $i; ?>);" disabled />
								<input type="hidden" id="Sv_it_cpt4_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][Sv_it_cpt4]" value="<?php echo $proc_cpt4_code; ?>" disabled />
							</td>
							<td style="width:80px;"><input type="text" id="sph_plus_from_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][sph_plus_from]" value="<?php echo $row['sph_plus_from']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:80px;"><input type="text" id="sph_plus_to_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][sph_plus_to]" value="<?php echo $row['sph_plus_to']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:80px;"><input type="text" id="sph_min_from_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][sph_min_from]" value="<?php echo $row['sph_min_from']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:80px;"><input type="text" id="sph_min_to_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][sph_min_to]" value="<?php echo $row['sph_min_to']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:80px;"><input type="text" id="cyl_from_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][cyl_from]" value="<?php echo $row['cyl_from']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:80px;"><input type="text" id="cyl_to_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][cyl_to]" value="<?php echo $row['cyl_to']; ?>" onChange="check_row(<?php echo $i; ?>);" /></td>
							
							<td style="width:110px;"><input type="text" id="wholesale_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][wholesale_price]" style="width:100px;" value="<?php echo $row['wholesale_price']; ?>"  onChange="convert_float(this);check_row(<?php echo $i; ?>);" autocomplete="off" /></td>
							<td style="width:110px;"><input type="text" id="purchase_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][purchase_price]" style="width:100px;" value="<?php echo $row['purchase_price']; ?>"  onChange="convert_float(this);check_row(<?php echo $i; ?>);" /></td>
							<td style="width:110px;"><input type="text" id="retail_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][retail_price]" style="width:100px;" value="<?php echo $row['retail_price']; ?>"  onChange="convert_float(this);check_row(<?php echo $i; ?>);" /></td>
							<td style="width:20px;"><img style="cursor:pointer;" id="addbtn_1" onClick="addrow('<?php echo $i; ?>', <?php echo $key; ?>);" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />
							</td>
					   </tr>
					   <?php
							$i++; 
							}
						}
						else{
						?>
							<tr class="even" id="tr_b_<?php echo $i; ?>">
							 <td style="width: 20px;"><input name="rec[<?php echo $i; ?>][id]" class="getchecked" id="checked_<?php echo $i; ?>" type="checkbox" value="">
						 	 <input name="rec[<?php echo $i; ?>][type_id]" id="type_id_<?php echo $i; ?>" type="hidden" value="<?php echo $key; ?>">
						  	<input name="rec[<?php echo $i; ?>][entry_type]" id="entry_type_<?php echo $i; ?>" type="hidden" value="1">
						  </td>
						  <td style="width: 100px; text-align: center;">
						  	<input name="rec[<?php echo $i; ?>][Sv_it]" class="prac_code" id="Sv_it_<?php echo $i; ?>" style="width: 90px;" onchange="$('#Sv_it_cpt4_<?php echo $i; ?>').val(this.value); check_row(<?php echo $i; ?>);" type="text" value="" autocomplete="off" />
							<input name="rec[<?php echo $i; ?>][Sv_it_desc]" disabled="" id="Sv_it_desc_<?php echo $i; ?>" style="width: 320px;" onchange="check_row(<?php echo $i; ?>);" type="hidden" value="">
							<input name="rec[<?php echo $i; ?>][Sv_it_cpt4]" disabled="" id="Sv_it_cpt4_<?php echo $i; ?>" style="width: 70px;" type="hidden" value="">
						</td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][sph_plus_from]" id="sph_plus_from_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][sph_plus_to]" id="sph_plus_to_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][sph_min_from]" id="sph_min_from_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][sph_min_to]" id="sph_min_to_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][cyl_from]" id="cyl_from_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width: 80px;"><input name="rec[<?php echo $i; ?>][cyl_to]" id="cyl_to_<?php echo $i; ?>" style="width: 70px;" onchange="check_row(<?php echo $i; ?>);" type="text" value=""></td>
						  
						  <td style="width:110px;"><input type="text" id="wholesale_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][wholesale_price]" style="width:100px;" value=""  onChange="check_row(<?php echo $i; ?>);" /></td>
						  <td style="width:110px;"><input type="text" id="purchase_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][purchase_price]" style="width:100px;" value=""  onChange="check_row(<?php echo $i; ?>);" /></td>
						  <td style="width:110px;"><input type="text" id="retail_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][retail_price]" style="width:100px;" value=""  onChange="check_row(<?php echo $i; ?>);" /></td>
						  
						  <td style="width: 20px;"><img title="Add New Row" id="addbtn_<?php echo $i; ?>" style="cursor: pointer;" onclick="addrow('<?php echo $i; ?>', <?php echo $key; ?>);" alt="Add New Row" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png">
						  </td>
						  </tr>
						<?php
						$i++; 
						
						}
					}
	          ?>
	        </tbody>
		</table>
    </div>

    <div id="prismDiopter">
		<div  class="listheading" style="background: #0088cc;">Prism – Price per diopter</div>

		<table class="table_collapse" style="width:1120px;">
	        <tr class="listheading">
	            <td style="width:130px;text-align:left;">Practice Code</td>
				<td style="width:150px;text-align:left;">Retail Price</td>
				<td></td>
	        </tr>
	        <!-- Prism diaopter pricing Row -->
	        <?php
	        	/*Get Prism Diaopter data from DB*/
	        	$sql = "SELECT * FROM `in_lens_type_vcode` WHERE `entry_type` = 2 AND del_status = 0";
	        	$resp = imw_query($sql);
	        	$row = array();

	        	$status = '';
				$lenstypecodeid = '';
				$lenstype = '';
				$prac_id = '';
				$prac_code = '';
				$proc_code_name = '';
				$proc_code_desc = '';
				$proc_cpt4_code = '';

	        	if( $resp && imw_num_rows($resp) === 1 )
	        	{
	        		$row = imw_fetch_assoc($resp);

	        		$status = $row['del_status'];
					$lenstypecodeid = $row['lens_type_id']; 
					$lenstype = $row['lens_type'];
					$prac_id = $row['prac_id'];
					$prac_code = $row['prac_code'];
					$proc_code_name = $proc_code_arr[$prac_id];
					$proc_code_desc = $proc_code_desc_arr[$prac_id];
					$proc_cpt4_code = $proc_cpt4_code_arr[$prac_id];
	        	}

	        ?>
			<tr>
				<td style="width: 100px; text-align: center;">
					<input name="rec[<?php echo $i; ?>][entry_type]" id="entry_type_<?php echo $i; ?>" type="hidden" value="2">
					<input name="rec[<?php echo $i; ?>][id]" id="checked_<?php echo $i; ?>" type="hidden" value="<?php echo $row['id']; ?>">

					<input type="text" class="prac_code" id="Sv_it_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][Sv_it]" style="width:118px;" value="<?php echo $proc_code_name;?>" onChange="$('#Sv_it_cpt4_<?php echo $i; ?>').val(this.value);" autocomplete="off" />
					<input type="hidden" id="Sv_it_desc_<?php echo $i; ?>" style="width:320px;" name="rec[<?php echo $i; ?>][Sv_it_desc]" value="<?php echo $proc_code_desc; ?>" disabled />
					<input type="hidden" id="Sv_it_cpt4_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][Sv_it_cpt4]" value="<?php echo $proc_cpt4_code; ?>" disabled />
				</td>
				<td style="width:110px;">
					<input type="text" id="retail_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][retail_price]" style="width:100px;" value="<?php echo $row['retail_price']; ?>"  onChange="convert_float(this);check_row(<?php echo $i; ?>);" />
				</td>

			</tr>

	    </table>
	    <?php $i++; ?>
	</div>

	<!-- Oversized Lens charges (Frame attribute 'A' > 59) -->
	<div id="oversizedLens">
		<div  class="listheading" style="background: #0088cc;">Oversized Lens – Charge</div>

		<table class="table_collapse" style="width:1120px;">
	        <tr class="listheading">
	            <td style="width:130px;text-align:left;">Practice Code</td>
				<td style="width:150px;text-align:left;">Retail Price</td>
				<td></td>
	        </tr>
	        <!-- Prism diaopter pricing Row -->
	        <?php
	        	/*Get Prism Diaopter data from DB*/
	        	$sql = "SELECT * FROM `in_lens_type_vcode` WHERE `entry_type` = 3 AND del_status = 0";
	        	$resp = imw_query($sql);
	        	$row = array();

	        	$status = '';
				$lenstypecodeid = '';
				$lenstype = '';
				$prac_id = '';
				$prac_code = '';
				$proc_code_name = '';
				$proc_code_desc = '';
				$proc_cpt4_code = '';

	        	if( $resp && imw_num_rows($resp) === 1 )
	        	{
	        		$row = imw_fetch_assoc($resp);

	        		$status = $row['del_status'];
					$lenstypecodeid = $row['lens_type_id']; 
					$lenstype = $row['lens_type'];
					$prac_id = $row['prac_id'];
					$prac_code = $row['prac_code'];
					$proc_code_name = $proc_code_arr[$prac_id];
					$proc_code_desc = $proc_code_desc_arr[$prac_id];
					$proc_cpt4_code = $proc_cpt4_code_arr[$prac_id];
	        	}

	        ?>
			<tr>
				<td style="width: 100px; text-align: center;">
					<input name="rec[<?php echo $i; ?>][entry_type]" id="entry_type_<?php echo $i; ?>" type="hidden" value="3">
					<input name="rec[<?php echo $i; ?>][id]" id="checked_<?php echo $i; ?>" type="hidden" value="<?php echo $row['id']; ?>">

					<input type="text" class="prac_code" id="Sv_it_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][Sv_it]" style="width:118px;" value="<?php echo $proc_code_name;?>" onChange="$('#Sv_it_cpt4_<?php echo $i; ?>').val(this.value);" autocomplete="off" />
					<input type="hidden" id="Sv_it_desc_<?php echo $i; ?>" style="width:320px;" name="rec[<?php echo $i; ?>][Sv_it_desc]" value="<?php echo $proc_code_desc; ?>" disabled />
					<input type="hidden" id="Sv_it_cpt4_<?php echo $i; ?>" style="width:70px;" name="rec[<?php echo $i; ?>][Sv_it_cpt4]" value="<?php echo $proc_cpt4_code; ?>" disabled />
				</td>
				<td style="width:110px;">
					<input type="text" id="retail_price_<?php echo $i; ?>" name="rec[<?php echo $i; ?>][retail_price]" style="width:100px;" value="<?php echo $row['retail_price']; ?>"  onChange="convert_float(this);check_row(<?php echo $i; ?>);" />
				</td>

			</tr>

	    </table>
	    <?php $i++; ?>
	    <input type="hidden" name="totRows" id="totRows" value="<?php echo $i; ?>" />
	</div>

</form>

<script type="text/javascript">
var pracBind = '<?php echo $i; ?>';
for(i=0;i<pracBind; i++){
	$("#Sv_it_"+i).ajaxTypeahead({ 
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO',
		hidIDelem: $('#Sv_it_desc_'+i)
	});
}

function submitFrom(){
	document.addsigtype.submit();
}
$(document).ready(function()
{
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","Delete","top.main_iframe.admin_iframe.del()");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>