<?php 
require_once("../../../config/config.php");
include_once($GLOBALS['DIR_PATH'].'/library/classes/functions.php');

if(!empty($_REQUEST['save_rec']))
{
	$len=count($_REQUEST['chk_box']);
	for($i=0;$i<$len;$i++)
	{
		$rec=$_REQUEST['chk_box'][$i];
		$prac_code = $_REQUEST['prac_cd'][$rec];
		$retail_price = $_REQUEST['retail_price'][$rec];
		$module_type_id = $_REQUEST['module_type_id'][$rec];
		
		if( $module_type_id=='1' || $module_type_id=='2' || $module_type_id=='3' ){
			back_prac_id($prac_code, false, $module_type_id);
		}
		
		/*$sel_rec=imw_query("Select * from in_prac_codes where id=".$rec." and prac_code!=''");
		while($sel_row=imw_fetch_array($sel_rec)){
			if($sel_row['module_id']=="2"){
				$procedureIdOld = back_prac_id($sel_row['prac_code']);
				$procedureId = back_prac_id($prac_code);
				if($sel_row['sub_module']="design"){
					imw_query("update in_lens_design set prac_code='$procedureId' where prac_code='$procedureIdOld'");
				}
				if($sel_row['sub_module']="material"){
					imw_query("update in_lens_material set prac_code_sv='$procedureId' where prac_code_sv='$procedureIdOld'");
					imw_query("update in_lens_material set prac_code_pr='$procedureId' where prac_code_pr='$procedureIdOld'");
					imw_query("update in_lens_material set prac_code_bf='$procedureId' where prac_code_bf='$procedureIdOld'");
					imw_query("update in_lens_material set prac_code_tf='$procedureId' where prac_code_tf='$procedureIdOld'");
				}
				if($sel_row['sub_module']="coating"){
					imw_query("update in_lens_ar set prac_code='$procedureId' where prac_code='$procedureIdOld'");
				}
			}
		}*/
		$query=imw_query("UPDATE `in_prac_codes` SET `prac_code`='".$prac_code."', `retail_price`='".$retail_price."' WHERE id=".$rec."");
	}
	header("location:practice_codes.php?msg=save");exit;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(document).ready(function(e) {
    $('#sel_all').click(function(){
		if($(this).is(":checked")){	
			$('.chk_box').prop("checked",true);
		}else{
			$('.chk_box').prop("checked",false);
		}
	});
});

selectCurrentCheck = function(ab)
{
	$("#"+ab).prop("checked",true);
}
</script>
<style>
#table1 tr:nth-child(even) {
	background: #F7F7F7;
}
#table1 input[type=textbox] {
	padding: 3px;
	border: 1px solid #ccc;
}
</style>
</head>
</head>
<body>
<div class="tab_container" style="float:left; width:100%;margin:10px 0 0 0;">
  <?php if($_REQUEST['msg']!=""){echo "<script>top.falert('Record(s) updated successfully');</script>";}?>
  <form method="post" action="" name="addframe" id="addframe">
    <table style="width:99.7%;" id="table1">
      <tr class="listheading">
        <th style="width:30px"><input type="checkbox" name="sel_all" id="sel_all" ></th>
        <th style="width:266px">Type</th>
        <th style="width:266px">Sub Module</th>
        <th style="width:267px">Billing Practice Code</th>
		<th style="width:266px">Retail Price</th>
      </tr>
<?php 
	  $i=1;
	$data = array();
	$query=imw_query("SELECT * FROM `in_prac_codes` WHERE `del_status`= 0 ORDER BY FIELD(LOWER(`module_id`), 1,2,3,5,6,7,8,0), FIELD(LOWER(`sub_module`), 'type_sv', 'type_pr', 'type_bf', 'type_tf', 'material', 'design', 'coating')");
	while($row=imw_fetch_array($query))
	{
		$data[$row['module_id']][] = $row;
	}
	foreach($data as $groups){
		ksort($groups);
		foreach($groups as $row){
			
			$sub_module=$row['sub_module_label'];
			
			if($row['module_id']==1 || $row['module_id']==2){
				$prac_class="prac_code_optical";
			}
			elseif($row['module_id']==3){
				$prac_class="prac_code_contact";
			}elseif($row['module_id']==5){
				$prac_class="prac_code";
			}elseif($row['module_id']==6){
				$prac_class="prac_code";
			}elseif($row['module_id']==7){
				$prac_class="prac_code";
			}
			else{
				$prac_class="prac_code";
			}
	?>
		  <tr>
			<td style="text-align:center;"><input type="checkbox" value="<?php echo $row['id'] ?>" class="chk_box" name="chk_box[]" id="<?php echo $row['id']; ?>"></td>
			<td><input type="text" name="module_id[<?php echo $row['id'] ?>]" value="<?php if($row['module_id']==1) echo "Frames";else if($row['module_id']==2) echo "Lenses"; else if($row['module_id']==3)echo "Contact Lenses"; else if($row['module_id']==5)echo "Medicines"; else if($row['module_id']==6)echo "Supplies"; else if($row['module_id']==7)echo "Accessories";?>" readonly>
			<input type="hidden" name="module_type_id[<?php echo $row['id'] ?>]" value="<?php echo $row['module_id']; ?>" /></td>
			<td><input type="text" name="sub_module[<?php echo $row['id'] ?>]" value="<?php echo ucwords($sub_module)?>" readonly></td>
			<td><input class="<?php echo $prac_class; ?>" type="text" name="prac_cd[<?php echo $row['id'] ?>]" value="<?php echo $row['prac_code'];?>" id="<?php echo $row['id'];?>" onChange="selectCurrentCheck('<?php echo $row['id']; ?>')" tabindex="<?php echo $i; ?>" autocomplete="off"></td>
			<td><input type="text" name="retail_price[<?php echo $row['id'] ?>]" value="<?php echo $row['retail_price'];?>" id="r_<?php echo $row['id'];?>" onChange="convert_float(this);selectCurrentCheck('<?php echo $row['id']; ?>')" tabindex="<?php echo ++$i; ?>" autocomplete="off"></td>
		  </tr>
	  <?php 
			$i++;
		  }
	  }?>
    </table>
      <input type="hidden" value="Save" name="save_rec" />
  </form>
</div>
<style type="text/css">
input[type="checkbox"]{display:none;}
table#table1 tr>td:first-child, table#table1 tr>th:first-child{display:none;}
input[readonly]{background-color:#efefef;}
input[type="text"]{width:96%;}
</style>
	
<script type="text/javascript">
	$(".prac_code_optical").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO'
	});
	$(".prac_code_contact").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
	$(".prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCode'
	});
function submitFrom(){
	document.addframe.submit();
}
$(document).ready(function()
{
//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>