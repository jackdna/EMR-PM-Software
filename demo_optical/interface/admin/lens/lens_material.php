<?php
	/*
	File: lens_material.php
	Coded in PHP7
	Purpose: Add/Edit/Delete: Lens Material
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$msg_stat = "none";
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	//update practice code if not filled
	$q1=imw_query("SELECT * FROM  `in_prac_codes` where module_id=2 and sub_module='material'");
	$d1=imw_fetch_object($q1);
	$defaultPracCode=back_prac_id($d1->prac_code);
	if($defaultPracCode){
		imw_query("update in_lens_material set prac_code_sv ='$defaultPracCode' where prac_code_sv='' OR prac_code_sv=0")or die(imw_error());
		imw_query("update in_lens_material set prac_code_pr ='$defaultPracCode' where prac_code_pr='' OR prac_code_pr=0")or die(imw_error());
		imw_query("update in_lens_material set prac_code_bf ='$defaultPracCode' where prac_code_bf='' OR prac_code_bf=0")or die(imw_error());
		imw_query("update in_lens_material set prac_code_tf ='$defaultPracCode' where prac_code_tf='' OR prac_code_tf=0")or die(imw_error());
	}
	//--------- UPDATE AND INSERT LENS MATERIAL--------//
	if(isset($_POST['save']) || isset($_POST['del_hidden']))	
	{	
		if(count($_POST['select_record'])>0)
		{
			for($v=0;$v<count($_POST['select_record']);$v++)
			{
				if(trim($_POST['del_hidden'])=="")
				{
					$rec_id = $_POST['select_record'][$v];
					$rec_materialname = trim($_POST['materialname'][$rec_id]);
					$rec_vw_code = trim($_POST['item_vw_code'][$rec_id]);
					$rec_design_code = $_POST['design_code_'.$rec_id];
					$rec_wholesale_price = trim($_POST['wholesale_price'][$rec_id]);
					$rec_purchase_price = trim($_POST['purchase_price'][$rec_id]);
					$rec_retail_price = trim($_POST['retail_price'][$rec_id]);
					
					$prac_code_sv = back_prac_id_catg('optical', trim($_POST['item_prac_code_sv'][$rec_id]), true);
					$prac_code_sv = implode(';', $prac_code_sv);
					
					$prac_code_pr = back_prac_id_catg('optical', trim($_POST['item_prac_code_pr'][$rec_id]), true);
					$prac_code_pr = implode(';', $prac_code_pr);
					
					$prac_code_bf = back_prac_id_catg('optical', trim($_POST['item_prac_code_bf'][$rec_id]), true);
					$prac_code_bf = implode(';', $prac_code_bf);
					
					$prac_code_tf = back_prac_id_catg('optical', trim($_POST['item_prac_code_tf'][$rec_id]), true);
					$prac_code_tf = implode(';', $prac_code_tf);
					
					
					if($rec_materialname!="")
					{			
						$updateQry = "update in_lens_material set material_name = '".imw_real_escape_string($rec_materialname)."', prac_code_sv='".$prac_code_sv."', prac_code_pr='".$prac_code_pr."', prac_code_bf='".$prac_code_bf."', prac_code_tf='".$prac_code_tf."', vw_code='$rec_vw_code', wholesale_price='".$rec_wholesale_price."', purchase_price='".$rec_purchase_price."', retail_price='".$rec_retail_price."', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id = '".$rec_id."' ";
						
						imw_query($updateQry);
						$msg = "Record(s) Saved Successfully";
						$msg_stat = "block";
						$met_des_qry="";
						imw_query("delete from in_lens_material_design where material_id='$rec_id'");
						$rec_design_code=explode(';',$rec_design_code);
						foreach($rec_design_code as $des_val){
							if($met_des_qry!=""){
								$met_des_qry.=", ";
							}
							$met_des_qry.="('".$rec_id."', '".$des_val."')";
						}
						imw_query("insert into in_lens_material_design (material_id,design_id) values $met_des_qry");
						if(trim($_POST['input_val'])!="")
						{
							$edit_time_insert_query = "insert in_lens_material set material_name = '".imw_real_escape_string($_POST['input_val'])."' ";					
						}
					}
				}
				else
				{
					$rec_id="";
					$rec_id = $_POST['select_record'][$v];
					$updateQry = "";
					$updateQry = "update in_lens_material set del_status = '2', del_date='$date', del_time='$time', del_by='$opr_id' where id = '".$rec_id."' ";
					imw_query($updateQry);					
				}
			}
		}
		else
		{
			if(trim($_POST['input_val'])!="")
			{
				$msg = "Record(s) Saved Successfully";
				$msg_stat = "block";
				imw_query("insert in_lens_material set material_name = '".imw_real_escape_string($_POST['input_val'])."' ");
			}
		}
		if($edit_time_insert_query!="")
		{
			$msg = "Record(s) Saved Successfully";
			$msg_stat = "block";
			imw_query($edit_time_insert_query);
		}
	}
	
//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$proc_code_desc_arr=array();
	$sql = "select * from cpt_category_tbl where cpt_category like '%optical%' order by cpt_category ASC";
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
				
				$code = $rowCodes["cpt_prac_code"];
				$cpt_desc = $rowCodes["cpt_desc"];
				//$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				//$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$proc_code_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_prac_code"];
				$proc_code_desc_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_desc"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}
	//$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
	if($_REQUEST['alpha']=='09'){
		$whr = "  and (material_name LIKE '1%' OR material_name LIKE '2%'  OR material_name LIKE '3%'  OR material_name LIKE '4%' OR material_name LIKE '5%' OR material_name LIKE '6%' OR material_name LIKE '6%' OR material_name LIKE '8%' OR material_name LIKE '9%' OR material_name LIKE '0%')";
	}else if($_REQUEST['alpha'] && $_REQUEST['alpha']!='az')
	{
		$whr = " and material_name like '".$_REQUEST['alpha']."%' ";	
	}
	$targetpage = "lens_material.php"; 	
	$limit = 50; 
	$query = "SELECT COUNT(*) as num FROM in_lens_material where del_status != '2' $whr";
	$total_pages = imw_fetch_array(imw_query($query));
	$total_pages = $total_pages[num];
	$stages = 3;
	$page = imw_escape_string($_GET['page']);
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
	
$dsn_qry=imw_query("select id,design_name,vw_code,del_status from in_lens_design WHERE del_status != 2 order by design_name");	
while($dsn_row=imw_fetch_array($dsn_qry)){
	$dsn_arr[$dsn_row['id']]['name']=$dsn_row['design_name'];
	$dsn_arr[$dsn_row['id']]['isActive']=(bool)$dsn_row['del_status'];
}
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<!--script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script-->
<!--script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect_edited.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>

<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
var crnt_indx='';
function del_callBack(result)
{
	if(result==true)
	{
		$("#del_hidden").val("1");
		$("#firstform").submit();
	}	
}
function setStatus(tbname,rowid,value,colname)
{
	var dataString = 'table='+ tbname + '&id=' + rowid + '&value=' + value + '&column=' + colname + '&page=change';
	$.ajax({
		type: "POST",
		url: "change_status.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			if(response=="true")
			{
				if(value==1)
				{
					$('#status'+rowid).attr('src','../../../images/off.png');
					$('#status'+rowid).attr('title','InActive');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_lens_material',"+rowid+",'0','del_status',this)");
				}
				else if(value==0)
				{
					$('#status'+rowid).attr('src','../../../images/on.png');
					$('#status'+rowid).attr('title','Active');
					$('#status'+rowid).attr("onclick","javascript:setStatus('in_lens_material',"+rowid+",'1','del_status',this)");
				}
				
			}
		}
	});
}
	
function refrsh()
{
	window.location.href='lens_material.php';
}

function open_addnew_popup()
{
	top.WindowDialog.closeAll();
	var Add_new_popup=top.WindowDialog.open('Add_new_popup',WEB_PATH+'/interface/admin/add_new.php?module_name=lens_material&col_name=material_name&heading=Lens_Material_Name', 'Add_new_popup', 'width=1480,height=380,left=60,scrollbars=no,top=80,fullscreen=0,resizable=0');
	Add_new_popup.focus();
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

<?php /*if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php }*/ ?>
</script>
<style type="text/css">
.rptDropDown>a.multiSelect>span{width:146px !important;}
.listheading{padding-left:0;}
.listheading td{padding-left:3px;}
input[readonly]{cursor:pointer;background-color:#EBEBE4;}

/*PopUps*/
#design_div, #price_div, #prac_div{cursor:default;}
#hide_price, #hide_prac, #hide_design{
	position: absolute;
	top: 0;
	right: 8px;
	border-radius: 10px;
	background-color: #ddd;
	width: 21px;
	height: 21px;
	text-align: center;
	cursor:pointer;
}
#hide_price img, #hide_prac img, #hide_design img{
	height: 15px;
    width: 15px;
    padding-top: 3px;
}
.saveNotice{
	color: #BE0F0E;
	font-weight: bolder;
	padding-left: 4px;
	padding-bottom: 2px;
	display: block;
}
#scroller > label
{
	display: block;
}
#scroller > label.lblRed{
	color: #BE0F0E;
}
</style>
</head>
<body>
<div id="loading" style="display:block;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<?php
/*
<!-- Price PopUp -->
<div id="price_div" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none;background-color:rgba(255,255,255,0.5);z-index:2">
	<div id="price_divC" style="position:relative;background:white;border:2px solid #1289CC;border-radius:4px;width:370px;">
	<h6 style="margin:0;text-align:center;background-color:#1289CC;padding:2px 0;color:#fff;font-size:18px;line-height:20px;">Price</h6>
	<span id="hide_price"><img src="<?php echo $GLOBALS['WEB_PATH']?>/images/del.png" /></span>
		<table>
			<thead>
				<tr>
					<th>Prac Code</th>
					<td>Wholesale</td>
					<td>Purchase</td>
					<td>Retail</td>
				</tr>
			</thead>
			<tbody id="price_table">
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
*/
?>
<div id="design_div" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none;background-color:rgba(255,255,255,0.5);z-index:3">
	<div id="design_divC" style="position:relative;background:white;border:2px solid #1289CC;border-radius:4px;width:485px;height:400px; left: 300px; display: inline-block;">
		<h6 style="margin:0;text-align:center;background-color:#1289CC;padding:2px 0;color:#fff;font-size:18px;line-height:20px;">Lens Design</h6>
		<span id="hide_design" row_key=''><img src="<?php echo $GLOBALS['WEB_PATH']?>/images/del.png" /></span>
		<div id="scroller" style="height: 350px; width: 100%; overflow: auto">
			<?php 
				foreach($dsn_arr as $dsn_key=>$dsn_val){
					$delClass = ($dsn_val['isActive']===true)?' class="lblRed"':'';
			?><label<?php echo $delClass; ?>><input type="checkbox" name="dsgn_<?php echo $dsn_key; ?>" id="dsgn_<?php echo $dsn_key; ?>" value="<?php echo $dsn_key; ?>" onChange="checkSaveBox()"><?php echo $dsn_val['name']; ?></label><?php 
				}
			?>
		</div>	
		<span class='saveNotice'>* Click save button at the bottom of page to save the changes.</span>
	</div>
</div>

<div id="prac_div" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none;background-color:rgba(255,255,255,0.5);z-index:2">
	<div id="prac_divC" style="position:relative;background:white;border:2px solid #1289CC;border-radius:4px;width:485px;">
	<h6 style="margin:0;text-align:center;background-color:#1289CC;padding:2px 0;color:#fff;font-size:18px;line-height:20px;">Prac Codes and Prices</h6>
	<span id="hide_prac" row_key=''><img src="<?php echo $GLOBALS['WEB_PATH']?>/images/del.png" /></span>
		<table>
			<thead>
				<tr>
					<th></th>
					<th>Single Vision</th>
					<th>Progressive</th>
					<th>BiFocal</th>
					<th>TriFocal</th>
				</tr>
			</thead>
			<tbody id="prac_table">
				<tr>
					<td>
						<strong>Prac Codes</strong>
					</td>
					<td>
						<input type="text" style="width:80px;" id="prac_sv" class="pracCodes" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="prac_pr" class="pracCodes" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="prac_bf" class="pracCodes" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="prac_tf" class="pracCodes" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>Wholesale Price</strong>
					</td>
					<td>
						<input type="text" style="width:80px;" id="wholesale_sv" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="wholesale_pr" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="wholesale_bf" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="wholesale_tf" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>Purchase Price</strong>
					</td>
					<td>
						<input type="text" style="width:80px;" id="purchase_sv" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="purchase_pr" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="purchase_bf" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="purchase_tf" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>Retail Price</strong>
					</td>
					<td>
						<input type="text" style="width:80px;" id="retail_sv" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="retail_pr" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="retail_bf" />
					</td>
					<td>
						<input type="text" style="width:80px;" id="retail_tf" />
					</td>
				</tr>
			</tbody>
		</table>
		<span class='saveNotice'>* Click save button at the bottom of page to save the changes.</span>
	</div>
</div>


<form onSubmit="return validateform()" name="addframe" id="firstform" action="" method="post" class="mt10">
	<?php
	$aprxHght=($total_pages>$limit)?465:435;
	?>
	<div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-$aprxHght;?>px; overflow-y:scroll;">
    <table class="table_collapse">
		<thead style="position:fixed;padding-right:17px;z-index:1;">
			<tr class="listheading">
			  <td style="width:20px;">
				<input type="hidden" id="del_hidden" name="del_hidden" value="" />
				<input type="checkbox" id="selectall" value="" /></td>
			  <td style="width:206px;">Lens Material</td>
			  <td style="width:166px;">VisionWeb Code</td>
			  <td style="width:172px;">Lens Design</td>
			  <td style="width:144px;">Prac Code</td>
			  <td style="width:114px;">Wholesale Price</td>
			  <td style="width:110px;">Purchase Price</td>
			  <td style="width:87px;">Retail Price</td>
			  <td align="center" style="width:48px;">Status</td>
			</tr>
		</thead>
        <tbody style="padding-top:25px;display:block;">
			<tr>
			  <td style="padding-left:3px;width:20px;"></td>
			  <td style="width:210px;"></td>
			  <td style="width:170px;"></td>
			  <td style="width:175px;"></td> 
			  <td style="width:146px;"></td>
			  <td style="width:120px;"></td>
			  <td style="width:110px;"></td>
			  <td style="width:92px;"></td>
			  <td style="width:50px;"></td>
			</tr>
			<tr>
				<td colspan="9">
					<div style="display:<?php echo $msg_stat;?>;color: rgb(255, 0, 0);font-size: 14px;text-align: center;font-weight: bold;"><?php echo $msg; ?></div>
				</td>
			</tr>
            <?php 
			
				$sql_trt_des=imw_query("select * from in_lens_material_design order by material_id");
				while($row_trt_des=imw_fetch_array($sql_trt_des)){
					$trt_mat_arr[$row_trt_des['material_id']][]=$row_trt_des['design_id'];
				}
				
				$sql="SELECT * FROM in_lens_material where del_status != '2' $whr order by material_name asc LIMIT $start, $limit";	  
                $res = imw_query($sql);
                $num = imw_num_rows($res);
                if($num>0)
                {
                    $i=0;
                    while($row = imw_fetch_array($res))
                    {
                        $status = $row['del_status'];
                        if($i%2==0)	
                        {
                            $rowbg="even";	
                        }
                        else
                        {
                            $rowbg="odd";	
                        }
				$design_id_arr=$trt_mat_arr[$row['id']];
            ?>
            <tr class="<?php echo $rowbg;?>">
              <td style="padding-left:3px;"><input type="checkbox" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $i; ?>" name="select_record[]" /></td>
              <td>
                <input type="text" value="<?php echo $row["material_name"]; ?>" class="materialname_field" id="materialname_field_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>')" name="materialname[<?php echo $row['id']; ?>]"  style="width:95%;"/>
              </td>
              <td>
                <input type="text" value="<?php echo $row['vw_code'];?>" id="item_vw_code_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>');" name="item_vw_code[<?php echo $row['id']; ?>]" style="width:94%;"/>
              </td>
			  <td class="rptDropDown" onClick="show_design_div(<?php echo $row['id']; ?>,'<?php echo $i; ?>');">
			  <input type="hidden" name="design_code_<?php echo $row['id']; ?>" id="design_code_<?php echo $row['id']; ?>" value="<?php echo implode(';',$design_id_arr);?>">
			  <?php
				$strtoshow='';
				foreach($design_id_arr as $d_id)
				{
					$strtoshow.=$dsn_arr[$d_id]['name'].", ";	
				}
				?>
			  <input type="text" id="dis_design_code_<?php echo $i; ?>" value="<?php echo $strtoshow;?>" title="<?php echo str_replace(", ","\n", $strtoshow); ?>" readonly>
			  
			  
<!--                <select name="design_code[<?php echo $row['id']; ?>]" id="design_code_<?php echo $row['id']; ?>" style="width:95%;" class="design_code">
                	<option value="">Lens Design</option>
                    <?php //foreach($dsn_arr as $dsn_key=>$dsn_val){ ?>
                    	<option value="<?php echo $dsn_key; ?>" <?php if(in_array($dsn_key,$design_id_arr)){echo"selected";} ?>><?php echo $dsn_val['name']; ?></option>
                    <?php //} ?>
                </select>-->
              </td>
			  <td onClick="show_prac_div(<?php echo $i; ?>);">
			  <?php
			  	$row_prac_codes = $row_prac_desc = array();
				
			  	$prac_val_sv = explode(";", $row['prac_code_sv']);
				$prac_val_pr = explode(";", $row['prac_code_pr']);
				$prac_val_bf = explode(";", $row['prac_code_bf']);
				$prac_val_tf = explode(";", $row['prac_code_tf']);
				
				/*$wholesale_price = explode(";", $row['wholesale_price']);
				$purchase_price = explode(";", $row['purchase_price']);
				$retail_price = explode(";", $row['retail_price']);*/
				$wholesale_price = json_decode($row['wholesale_price'], true);
				$wholesale_price_h = $row['wholesale_price'];
				
				$purchase_price = json_decode($row['purchase_price'], true);
				$purchase_price_h = $row['purchase_price'];
				
				$retail_price = json_decode($row['retail_price'], true);
				$retail_price_h = $row['retail_price'];
				
				$prac_code_sv = $prac_code_pr = $prac_code_bf = $prac_code_tf = "";
				$prac_desc_sv = $prac_desc_pr = $prac_desc_bf = $prac_desc_tf = "";
				
				//$multi = (count($prac_val_sv)>1)?"readonly":"";
				//$multi_event = (count($prac_val_sv)>1)?"onClick=\"editPrices(".$i.")\"":"";
				
				foreach($prac_val_sv as $key=>$prac_id){
					/*if(!isset($wholesale_price[$key]))
						$wholesale_price[$key] = "0.00";
					
					if(!isset($purchase_price[$key]))
						$purchase_price[$key] = "0.00";
						
					if(!isset($retail_price[$key]))
						$retail_price[$key] = "0.00";*/
						
					$prac_code_sv .= $proc_code_arr[$prac_id].";";
					$prac_desc_sv .= $proc_code_desc_arr[$prac_id]."; ";
					array_push($row_prac_codes, $proc_code_arr[$prac_id]);
					array_push($row_prac_desc, $proc_code_desc_arr[$prac_id]);
				}
				$prac_code_sv = rtrim($prac_code_sv, ";");
				$prac_desc_sv = rtrim($prac_desc_sv, ";");
				
				foreach($prac_val_pr as $key=>$prac_id){
					$prac_code_pr .= $proc_code_arr[$prac_id].";";
					$prac_desc_pr .= $proc_code_desc_arr[$prac_id]."; ";
					array_push($row_prac_codes, $proc_code_arr[$prac_id]);
					array_push($row_prac_desc, $proc_code_desc_arr[$prac_id]);
				}
				$prac_code_pr = rtrim($prac_code_pr, ";");
				$prac_desc_pr = rtrim($prac_desc_pr, ";");
				
				foreach($prac_val_bf as $key=>$prac_id){
					$prac_code_bf .= $proc_code_arr[$prac_id].";";
					$prac_desc_bf .= $proc_code_desc_arr[$prac_id]."; ";
					array_push($row_prac_codes, $proc_code_arr[$prac_id]);
					array_push($row_prac_desc, $proc_code_desc_arr[$prac_id]);
				}
				$prac_code_bf = rtrim($prac_code_bf, ";");
				$prac_desc_bf = rtrim($prac_desc_bf, ";");
				
				foreach($prac_val_tf as $key=>$prac_id){
					$prac_code_tf .= $proc_code_arr[$prac_id].";";
					$prac_desc_tf .= $proc_code_desc_arr[$prac_id]."; ";
					array_push($row_prac_codes, $proc_code_arr[$prac_id]);
					array_push($row_prac_desc, $proc_code_desc_arr[$prac_id]);
				}
				$prac_code_tf = rtrim($prac_code_tf, ";");
				$prac_desc_tf = rtrim($prac_desc_tf, ";");
				
				$row_prac_desc = array_unique($row_prac_desc);
				
				$row_prac_codes = array_filter($row_prac_codes, function($elemVal){
					if($elemVal!='' || !is_null($elemVal))
						return($elemVal);
				});
				
				$row_prac_codes = array_unique($row_prac_codes);
			  ?>
                <!--input type="text" value="<?php echo $prac_code_sv; ?>" id="item_prac_code_sv_<?php echo $i; ?>" onChange="selectCurrentCheck('<?php echo $i; ?>');" name="item_prac_code_sv[<?php echo $row['id']; ?>]" style="width:93%;" title="<?php echo $prac_desc_sv; ?>" autocomplete="off" class="pracCodes" /-->
				
				<input type="text" id="item_prac_code_disp_<?php echo $i; ?>" value="<?php echo implode(';', $row_prac_codes); ?>" style="width:93%;" title="<?php echo implode("\n", $row_prac_desc); ?>" readonly />
				
				<input type="hidden" value="<?php echo $prac_code_sv; ?>" id="item_prac_code_sv_<?php echo $i; ?>" name="item_prac_code_sv[<?php echo $row['id']; ?>]" autocomplete="off" />
				<input type="hidden" value="<?php echo $prac_code_pr; ?>" id="item_prac_code_pr_<?php echo $i; ?>" name="item_prac_code_pr[<?php echo $row['id']; ?>]" autocomplete="off" />
				<input type="hidden" value="<?php echo $prac_code_bf; ?>" id="item_prac_code_bf_<?php echo $i; ?>" name="item_prac_code_bf[<?php echo $row['id']; ?>]" autocomplete="off" />
				<input type="hidden" value="<?php echo $prac_code_tf; ?>" id="item_prac_code_tf_<?php echo $i; ?>" name="item_prac_code_tf[<?php echo $row['id']; ?>]" autocomplete="off" />
              
			  </td>
			   <?php /*echo $multi_event*/; ?>
			   <?php /*echo $multi;*/ ?>
			  <td onClick="show_prac_div(<?php echo $i; ?>);">
                <input type="text" value="<?php echo implode(';', $wholesale_price); ?>" id="wholesale_price_<?php echo $i; ?>" style="width:92%;" readonly />
				<input type="hidden" value='<?php echo $wholesale_price_h; ?>' id="wholesale_price_<?php echo $i; ?>_h" name="wholesale_price[<?php echo $row['id']; ?>]" readonly />
              </td>
			  <td onClick="show_prac_div(<?php echo $i; ?>);">
			  	<input type="text" value="<?php echo implode(';', $purchase_price); ?>" id="purchase_price_<?php echo $i; ?>" style="width:90%;" readonly />
				<input type="hidden" value='<?php echo $purchase_price_h; ?>' id="purchase_price_<?php echo $i; ?>_h" name="purchase_price[<?php echo $row['id']; ?>]" readonly />
			  </td>
			  <td onClick="show_prac_div(<?php echo $i; ?>);">
			  	<input type="text" value="<?php echo implode(';', $retail_price); ?>" id="retail_price_<?php echo $i; ?>" style="width:90%;" readonly />
				<input type="hidden" value='<?php echo $retail_price_h; ?>' id="retail_price_<?php echo $i; ?>_h" name="retail_price[<?php echo $row['id']; ?>]" readonly/>
			  </td>
              <td align="center">
                <?php if($status=="1") 
                { ?>	
                <img id="status<?php echo $row['id']; ?>" title="InActive" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_lens_material','<?php echo $row['id']; ?>','0','del_status',this)" src="../../../images/off.png" complete="complete"/>
                <?php } else { ?>
                <img id="status<?php echo $row['id']; ?>" title="Active" class="noborder" style="cursor: pointer;" onClick="javascript:setStatus('in_lens_material','<?php echo $row['id']; ?>','1','del_status',this)" src="../../../images/on.png" complete="complete"/>
                <?php 
                } ?>
              </td>
            </tr>
            <?php
                    $i++; 
                    }
                }
                else
                {
					$numrows=1;
            ?>
            <tr>
              <td colspan="9" align="center" class="even">No Record Exist</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="btn_cls">
	<?php
	require_once'../paging_new.php';
	$alpha=array();
	
	$alpha[1] = "a";
	$alpha[2] = "b";
	$alpha[3] = "c";
	$alpha[4] = "d";
	$alpha[5] = "e";
	$alpha[6] = "f";
	$alpha[7] = "g";
	$alpha[8] = "h";
	$alpha[9] = "i";
	$alpha[10] = "j";
	$alpha[11] = "k";
	$alpha[12] = "l";
	$alpha[13] = "m";
	$alpha[14] = "n";
	$alpha[15] = "o";
	$alpha[16] = "p";
	$alpha[17] = "q";
	$alpha[18] = "r";
	$alpha[19] = "s";
	$alpha[20] = "t";
	$alpha[21] = "u";
	$alpha[22] = "v";
	$alpha[23] = "w";
	$alpha[24] = "x";
	$alpha[25] = "y";
	$alpha[26] = "z";
	?>
	
	<ul style="float:left; margin:10px 0 10px 170px; width:100%; <?php if($numrows==1 && !isset($_REQUEST['alpha'])){ ?>display:none;<?php } ?>">
	<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:3px; background:<?php if($_REQUEST['alpha']=="09") { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=09&filter_type=<?php echo $_REQUEST['filter_type']; ?>">0-9</a></li>
	<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:3px; background:<?php if($_REQUEST['alpha']=="az") { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=az">A-Z</a></li>
	<?php foreach($alpha as $key=>$value) 
	{ ?>
	<li class="fl"><a style="border-radius:5px; padding:2px 6px; color:#fff; text-decoration:none; font-weight:bold; text-transform:uppercase; margin:2.3px; background:<?php if($_REQUEST['alpha']==$value) { echo "#fb9608"; } else { echo "#09F"; } ?>" href="?alpha=<?php echo $value; ?>"><?php echo $value; ?></a></li>
	<?php } ?>
	</ul>
</div>
</form>


<script type="text/javascript">
function submitFrom(){
	document.addframe.submit();
}

function parsejson(value){
	var rtData = '';
	try{
		rtData = $.parseJSON(value);
	}
	catch(err){
		rtData = {'sv':'0.00', 'pr':'0.00', 'bf':'0.00', 'tf':'0.00'};
	}
	return rtData;
}
function show_design_div(key,indx)
{
	crnt_indx=indx;
	//var topPos = (($("#design_div").height())/2)-185;
	//var leftPos = (($("#design_div").width())/2)-184;
	//$('#design_divC').css({'left':'300px'});
	checkSelected(key);
	$("#design_div").show();
	$('#hide_design').attr('row_key', key);
}
function show_prac_div(key){
	
	var topPos = (($("#prac_div").height())/2)-185;
	var leftPos = (($("#prac_div").width())/2)-184;
	
	$('#prac_divC').css({'top':(topPos)+'px', 'left':leftPos+'px'});
	
	$('#prac_sv').val($('#item_prac_code_sv_'+key).val());
	$('#prac_pr').val($('#item_prac_code_pr_'+key).val());
	$('#prac_bf').val($('#item_prac_code_bf_'+key).val());
	$('#prac_tf').val($('#item_prac_code_tf_'+key).val());
	
	/*Prices*/
	this.wholesale = $('#wholesale_price_'+key+'_h').val();
	this.purchase = $('#purchase_price_'+key+'_h').val();
	this.retail = $('#retail_price_'+key+'_h').val();
	
	this.wholesale	= parsejson(this.wholesale);
	this.purchase	= parsejson(this.purchase);
	this.retail		= parsejson(this.retail);
	
	var prices = new Array('wholesale', 'purchase', 'retail');
	var seg_types = new Array('sv', 'pr', 'bf', 'tf');
	
	func_obj = this;
	
	var field_id = '';
	$.each(prices, function(key0, val){
		
		$.each(seg_types, function(key1, val1){
			
			field_id = '#'+val+'_'+val1
			price_val = func_obj[val];
			$(field_id).val(price_val[val1]);
			
			$(field_id).on('change', function(){
				
				var vals = {};
				$.each(seg_types, function(key3, val3){
					tmp_val0 = $('#'+val+'_'+val3).val();
					tmp_val = tmp_val0.split(';');
					
					var tmp_val = $.map(tmp_val, function(val){
						retrun_val = parseFloat(val).toFixed(2);
						retrun_val = isNaN(retrun_val)?'0.00':retrun_val;
						return retrun_val;
					}).join(';');
					
					vals[val3] = tmp_val;
					
					if(tmp_val0!=tmp_val)
						$('#'+val+'_'+val3).val(tmp_val);
				});	
				
				var vals1 = $.map(vals, function(val){
					return val;
				}).join(';');
				
				$('#'+val+'_price_'+key).val(vals1);
				$('#'+val+'_price_'+key+'_h').val(JSON.stringify(vals));
				selectCurrentCheck(key);
			});
			
			$(field_id).on('keypress', function(){
				
				if(event.which !== 46 && event.which !== 59 && (event.which < 48 || event.which > 57)){
					event.preventDefault();
				}
			});
		});
	});
	
	
	$('#prac_sv').on('change', function(){
		var val = $.trim($(this).val());
		val = (val.slice(-1)==';')?val.slice(0, -1):val;
		$('#item_prac_code_sv_'+key).val(val);
		selectCurrentCheck(key);
	});
	$('#prac_pr').on('change', function(){
		var val = $.trim($(this).val());
		val = (val.slice(-1)==';')?val.slice(0, -1):val;
		$('#item_prac_code_pr_'+key).val(val);
		selectCurrentCheck(key);
	});
	$('#prac_bf').on('change', function(){
		var val = $.trim($(this).val());
		val = (val.slice(-1)==';')?val.slice(0, -1):val;
		$('#item_prac_code_bf_'+key).val(val);
		selectCurrentCheck(key);
	});
	$('#prac_tf').on('change', function(){
		var val = $.trim($(this).val());
		val = (val.slice(-1)==';')?val.slice(0, -1):val;
		$('#item_prac_code_tf_'+key).val(val);
		selectCurrentCheck(key);
	});
	
	$('#hide_prac').attr('row_key', key);
	$("#prac_div").show();
}

<?php
/*
function editPrices(key){
	var pracCodes = $("#item_prac_code_sv_"+key).val();
	var wholesale = $("#wholesale_price_"+key).val();
	var purchase = $("#purchase_price_"+key).val();
	var retail = $("#retail_price_"+key).val();
	
	var topPos = (($("#price_div").height())/2)-185;
	var leftPos = (($("#price_div").width())/2)-184;
	
	pracCodes = pracCodes.split(";");
	wholesale = wholesale.split(";");
	purchase = purchase.split(";");
	retail = retail.split(";");
	
	
	var html = "";
	$.each(pracCodes, function(index, prac){
		html += "<tr>";
			html += "<td><input type=\"text\" value=\""+prac+"\" style=\"width:80px;\" disabled=\"disabled\" /></td>";
			html += "<td><input onChange=\"convert_float(this);changePrice('wholesale_price_"+key+"', 'wholesale', "+key+")\" class=\"wholesale\" type=\"text\" value=\""+wholesale[index]+"\" style=\"width:80px;\" /></td>";
			html += "<td><input onChange=\"convert_float(this);changePrice('purchase_price_"+key+"', 'purchase', "+key+")\" class=\"purchase\" type=\"text\" value=\""+purchase[index]+"\" style=\"width:80px;\" /></td>";
			html += "<td><input onChange=\"convert_float(this);changePrice('retail_price_"+key+"', 'retail', "+key+")\" class=\"retail\" type=\"text\" value=\""+retail[index]+"\" style=\"width:80px;\" /></td>";
		html += "</tr>";
	});
	
	//$("#price_table").html(html);
	document.getElementById("price_table").innerHTML = html;
	$("#price_divC").css({'top':(topPos)+'px', 'left':leftPos+'px'});
	
	$("#price_div").show();
}

function changePrice(id, cl, key){
	
	var price = $("."+cl);
	var priceVal = new Array();
	$(price).each(function(){
		priceVal.push($(this).val());
	});
	
	priceVal = priceVal.join(";");
	$("#"+id).val(priceVal);
	selectCurrentCheck(key);
}
/*Multiple Price Option* /
$("#hide_price").click(function(){
	$("#price_div").hide();
});
*/ ?>

$(document).ready(function()
{
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
	
	selectCurrentCheck = function(ab)
	{
	   $("#checked_"+ab).prop('checked', true);
	
	   var currentval = $('#materialname_field_'+ab);
	
		if($.trim(currentval.val()) == "")
		{
				top.falert('Please Enter Lens Material');	
				setTimeout(function(){$( currentval ).focus(); },0);
		}
		else
		{
			$(".materialname_field").each(function( index ) 
			{
				//top.falert($(this).val()+" == "+currentval);
				if($.trim($(this).val()).toLowerCase() == $.trim(currentval.val()).toLowerCase() && $(this).attr('id') != currentval.attr('id'))
				{
					top.falert(currentval.val()+' Already Exist');
					$( currentval ).val('');
					setTimeout(function(){$( currentval ).focus(); },0);
				}
			});	
		}
	}
	  
	validateform = function()
	{	
		$(".materialname_field").each(function(index)
		{
			if($.trim($(this).val()) == "")
			{
				top.falert("Please Enter Lens Material");
				$(this).focus();
				return false;
			}
		});
	}
	
	/*var cont = '<?php echo $i; ?>';
	for(var i=0;i<cont;i++)
	{
		var obj8 = new actb(document.getElementById('item_prac_code_'+i),customarrayProcedure);	
	}*/
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.admin_iframe.open_addnew_popup()");
	mainBtnArr[2] = new Array("frame","Delete","top.main_iframe.admin_iframe.del()");
	top.btn_show("admin",mainBtnArr);
	
	/*var dd_pro = new Array();
	dd_pro["listHeight"] = 200;
	dd_pro["noneSelected"] = "Select All";
	$(".design_code").multiSelect(dd_pro);*/
	
	$(".pracCodes").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO',
		multiple: true
	});
	
	$('#hide_design').click(function(){
		
		var row_key = $(this).attr('row_key');
		setSelected(row_key);
		$('#design_div').hide();
	});
	
	$('#hide_prac').click(function(){
		
		var row_key = $(this).attr('row_key');
		
		$('#prac_div').hide();
	});
	
	$(document).on('keyup', function(){
		
		if(event.keyCode===27 && $('#prac_div').is(':visible')){
			$('#hide_prac').trigger('click');
		}
		
		if(event.keyCode===27 && $('#design_div').is(':visible')){
			$('#hide_design').trigger('click');
		}
	});
	
	$("#loading").hide();
});
	
	function uncheckAll()
	{
		var divID='design_div';
		$('#'+divID).find(':checkbox').each(function(){

			$(this).prop('checked', false);

		});
	}
	
	function checkSelected(key)
	{
		uncheckAll();
		//get selected value if any
		var selected=$("#design_code_"+key).val();
		if(selected)
		{
			var sel_arr=selected.split(';');
		}
		$.each( sel_arr, function( i, val ) {
			$("#dsgn_"+val).prop('checked', true);
		});
	}
	
	function setSelected(key)
	{
		var arr=[];
		var divID='design_div';
		$('#'+divID).find(':checkbox').each(function(){
			if($(this).is(':checked'))
			{
				arr.push($(this).val());
			}

		});
		$("#design_code_"+key).val('');
		if(arr.length)
		{
			$("#design_code_"+key).val(arr.join(';'));
		}
	}
	function checkSaveBox()
	{
		$("#checked_"+crnt_indx).prop('checked', true);
		var arr=[];
		var divID='design_div';
		$('#'+divID).find(':checkbox').each(function(){
			if($(this).is(':checked'))
			{
				arr.push($(this).val());
			}

		});

		var key = $('#hide_design')[0].attributes[1].value;
		$("#design_code_"+key).val('');
		if(arr.length)
		{
			$("#design_code_"+key).val(arr.join(';'));
		}
	}
	
</script>  
</body>
</html>