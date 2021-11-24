<?php
	/*
	File: remove_order.php
	Coded in PHP7
	Purpose: View All Remove Orders
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$msg_stat = "none";
	$patient_id=$_SESSION['patient_session_id'];
	
	/*$stringAllUpc = get_upc_name_id();
	$AllUpcArray=array();
	$AllUpcIdArrays=array();
	$AllNameArray = array();

	foreach($stringAllUpc as $key=>$value)
	{
		$AllUpcIdArrays[]=$key;
		$exp = explode('-:',$value);
		$AllUpcArray[]="'".$value."'";
		$AllNameArray[]="'".$exp[1]."'";
	}
	
	$AllUpcIdArray = implode(',',$AllUpcIdArrays);
	$AllUpcArray = implode(',',$AllUpcArray);
	$AllNameArray = implode(',',$AllNameArray);*/
	
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	if($_REQUEST['delete']!="" && $_REQUEST['delete']=="Delete")
	{
		$orderid = $_POST['update_order_id'];
		for($i=0;$i<count($orderid);$i++)
		{
			$single_order = $orderid[$i];
			$upd_ord = imw_query("update in_order_return set del_status='2', del_date='$entered_date', del_time='$entered_time', del_by='$operator_id' where order_id='$single_order' and del_status='0' and status='remove'");
		}
	}
	
	$delwhere = " and ord_ret.del_status = '0'";	
	if($_REQUEST['search']!="" && $_REQUEST['search']=="Search")
	{
		$search_ordernum = $_REQUEST['order_num'];
		$search_upcCode = $_REQUEST['upc_code'];
		$search_itemname = $_REQUEST['item_name'];
		$search_stat = $_REQUEST['delstatus'];
		$swhere="";
		if($search_ordernum!="")
		{
			$swhere .= " and ord_ret.order_id = '".$search_ordernum."'";
		}
	
		if($search_upcCode!="")
		{
			$swhere .= " and ord.upc_code like '".$search_upcCode."%'";
		}
		
		if($search_itemname!="")
		{
			$swhere .= " and ord.item_name like '".$search_itemname."%'";
		}
		
		if($search_stat==1)
		{
			$delwhere = "";
		}
	}
	
	$qry = "select ord_ret.id, ord_ret.order_id, ord_ret.entered_date, ord_ret.patient_id, ord_ret.entered_by, ord_ret.del_status, rr.return_reason, ord_ret.status, sum(ord_ret.return_qty) as total_qty, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.lname as us_lname from in_order_return as ord_ret 
inner join patient_data as pd on pd.id = ord_ret.patient_id 
inner join users as us on us.id = ord_ret.entered_by 
inner join in_order_details as ord on ord.id = ord_ret.order_detail_id
inner join in_return_reason as rr on rr.id = ord_ret.reason
where ord_ret.status='remove' and  ord.pof_check='0' $swhere $delwhere GROUP BY ord_ret.order_id order by ord_ret.entered_date desc, ord_ret.id desc";
//die();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 

<script type="text/javascript">
function del_callBack(result)
{
	if(result==true)
	{
		$("#del_hidden").val("1");
		$("#firstform").submit();
	}	
}
$(document).ready(function(){
	selectCurrentCheck = function(ab){
		$("#checked_"+ab).prop('checked', true);
	}
	del = function(){
	 	if( $(".getchecked:checked").length == 0 ){
           top.falert('Please check atleast one record');
        }else{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$(".getchecked").prop('checked', true);
		}else{
			$(".getchecked").prop('checked', false);
		}
	});
});

function upc(upc_code)
{
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&upc='+ucode;
	$.ajax({
		type: "POST",
		url: "../ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = $.parseJSON(response);
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					$("#upc_code").val(item.upc_code);
					$("#item_name").val(item.name);
				 });
			 }
			 else
			 {
			 }
		}
	}); 
}

function prod_popup(order_id)
{
	var up_code = $("#upc_code").val();
	var it_name = $("#item_name").val();
	var dl_stat = $("#delstat").val();
	top.WindowDialog.closeAll();
	var prod_popup=top.WindowDialog.open('prod_popup','remove_order_detail.php?ord_id='+order_id+'&ucod='+up_code+'&itm='+it_name+'&delstatus='+dl_stat,'prod_popup','width=770,height=300,left=100,scrollbars=no,top=80,fullscreen=0,resizable=0');	
	prod_popup.focus();	
}

function chk_del()
{
	if($('[name="update_order_id[]"]:checked').length==0)
	{
		top.falert("Please select a record");
		return false;
	}
	if($('[name="update_order_id[]"]:checked').length>0)
	{
		top.fconfirm('Are you sure to delete selected record(s) ?',chk_del_callBack);
	}

}
function chk_del_callBack(result)
{
	return result;
}
<?php if($AllUpcArray!=""){?>
	//var custom_array_upc= new Array(<?php echo remLineBrk($AllUpcArray); ?>);
<?php } ?>

<?php if($AllNameArray!=""){?>
	//var custom_array_name= new Array(<?php echo remLineBrk($AllNameArray); ?>);
<?php } ?>

var custom_array_upc_id;
<?php if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	//custom_array_upc_id= new Array(<?php echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	//custom_array_upc_id= new Array('<?php echo $AllUpcIdArray; ?>');
<?php } ?>

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
    <div class="mt10" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
        <form name="search_rec" id="firstform" method="post" style="margin:0px;">
		<input type="hidden" name="upc_id" id="upc_id" value="">
            <table class="table_collapse table_cell_padd2" style="margin: 0px 0px 3px;">
                <tr style="background: #D5EAF7;">
                  <td width="470" style="font-weight:bold;">View Removed Orders</td>
          		  <td width="55"><label for="order_num">Order#:</label></td>
                  <td width="70"><input type="text" style="width:60px;" name="order_num" id="order_num" value="<?php echo $_POST['order_num']; ?>"></td>
                  <td width="75" style="text-align:right;"><label for="upc_code">UPC Code:</label></td>
                  <td width="70">
				  	<input type="text" style="width:65px;" name="upc_code" id="upc_code" value="<?php echo $_POST['upc_code']; ?>" onChange="javascript:upc(document.getElementById('upc_id'));">
				  </td>
                  <td width="80" style="text-align:right;"><label for="item_name">Item Name:</label></td>
                  <td width="70"><input type="text" style="width:65px;" id="item_name" name="item_name" value="<?php echo $_POST['item_name']; ?>" onChange="javascript:upc(document.getElementById('upc_id'));"></td>
				  <td width="90">
				  	<select name="delstatus" id="delstat" style="width:85px;">
						<option value="0" <?php if($_REQUEST['delstatus']==0) { echo "selected"; } ?>>Active</option>
						<option value="1" <?php if($_REQUEST['delstatus']==1) { echo "selected"; } ?>>All</option>
					</select>
				  </td>
                  <td width="70" class="btn_cls">
                  <input type="submit" value="Search" name="search"></td>
                </tr>
            </table>
        </form>
        
        <form name="change_status" id="secondform" method="post" style="margin:0px;">   
        <div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-460;?>px; overflow-y:auto;"> 
            <table class="table_collapse table_cell_padd2">
                
                
                <?php
					$res = imw_query($qry);
					$nums = imw_num_rows($res);
					if($nums > 0)
					{
						echo '<tr class="listheading" style="text-align:center;">
                  <td width="10"><input type="checkbox" name="select_all" id="selectall" /></td>
                  <td width="55">Order#</td>
                  <td width="90">Order Date</td>
				  <td style="text-align: left; width: 250px;">Patient-Id</td>
                  <td width="60">Qty.</td>
                  <td width="70">Oper.</td>
                </tr>';
						while($rows = imw_fetch_array($res)) {
							$opr_fname = $rows['us_fname'][0];
							$opr_lname = $rows['us_lname'][0];
							$oper_name = $opr_lname.$opr_fname;
						$stat_del = array();
						$sel_del_stat = imw_query("select del_status from in_order_return where order_id='".$rows['order_id']."'");
						while($del_rows = imw_fetch_array($sel_del_stat))
						{
							$stat_del[] = $del_rows['del_status'];
						}
						if(in_array("0",$stat_del))
						{ 
							if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";} 	
							$dis="inline-block";
						}
						else { $rowbg="del_bg"; $dis="none"; }
				?>
              
               <tr class="<?php echo $rowbg; ?>" style="cursor:pointer;">
                	<td style="text-align:center;"><input type="checkbox" class="getchecked" name="update_order_id[]" value="<?php echo $rows['order_id'];?>" style="display:<?php echo $dis; ?>" id="checked_<?php echo $rows['order_id']; ?>"></td>
                    <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['order_id'];?>');"><?php echo $rows['order_id']; ?></td>
                    <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['order_id'];?>');"><?php echo getDateFormat($rows['entered_date']); ?></td>
                  	<td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['order_id'];?>');"><?php echo $rows['lname'].",&nbsp;".$rows['fname']."&nbsp;".$rows['mname']." - ".$rows['patient_id']; ?></td>
                    <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['order_id'];?>');"><?php echo $rows['total_qty']; ?></td>
                    <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['order_id'];?>');"><?php echo $oper_name; ?></td>
                </tr>
                <?php } }
				
				else
				{
				 ?>
                <tr>
                	<td colspan="6" style="text-align:center"> No Records Exists </td>
                </tr>
                <?php } ?>
                
            </table>
            </div>
			 <div style="display:none">
           		<input type="submit" name="delete" id="deleteBtn" value="Delete" onClick="return chk_del();">      
       		 </div>
          </form>
    </div>
<script type="text/javascript">
//var obj7 = new actb(document.getElementById('upc_code'),custom_array_upc,"","",document.getElementById('upc_id'),custom_array_upc_id);
//var obj8 = new actb(document.getElementById('item_name'),custom_array_name,"","",document.getElementById('upc_id'),custom_array_upc_id);
$("#upc_code").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'mixedData',
			hidIDelem: document.getElementById('upc_id'),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'upc'
		});
		
$("#item_name").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'mixedData',
			hidIDelem: document.getElementById('upc_id'),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'name'
		});
function form_action(){
	$('#deleteBtn').click();
}

$(document).ready(function(e) {
	var mainBtnArr = new Array();
	<?php if($nums>0){?>
		mainBtnArr[0] = new Array("frame","Delete","top.main_iframe.admin_iframe.form_action();");
	<?php }?>	
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>