<?php
	/*
	File: index.php
	Coded in PHP7
	Purpose: View All Orders, Pending, Ordered, Received, Dispensed Status
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$msg_stat = "none";
	$start=0;$search_item_type = '';
	$patient_id=$_SESSION['patient_session_id'];
	if(!isset($_REQUEST['rec_type']))$_REQUEST['rec_type']=$_REQUEST['rec_type2']='all';
	$ext_parm="&nw_stl=$_REQUEST[nw_stl]&rec_type=$_REQUEST[rec_type]";
	if(isset($_REQUEST['item_type']))
	{
		$ext_parm.="&item_type=$_REQUEST[item_type]";
		$search_item_type = trim($_REQUEST['item_type']);
	}
	$search_item_type .= ($search_item_type!='') ? ",9" : "";
	
	$loc_arr = array();
	$query2=imw_query("select id,loc_name from in_location");
	while($row2=imw_fetch_array($query2)){
		$loc_arr[$row2['id']]=$row2['loc_name'];
	}
	
	
	function getReasonById($id)
	{
		$q=imw_query("select reason_name from in_reason where id='$id'")or die(imw_error());
    	$dlist=imw_fetch_object($q);	
		return $dlist->reason_name;
	}
	//get patient paid detail from in_order_lens_price_detail table
	/*$qPtPaid=imw_query("select SUM(pt_paid) as total_pt_paid, order_id from in_order_lens_price_detail where patient_id='$patient_id' GROUP BY order_id")or die(imw_error());
	while($ptPaid=imw_fetch_object($qPtPaid))
	{
		$pat_paid[$ptPaid->order_id]+=$ptPaid->total_pt_paid;
	}
	*/
	
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
	$ret_pat_whr = "";
	if(isset($pagename) && $pagename=="pt_hst")
	{
		$ret_pat_whr = " and patient_id='$patient_id'";
	}
	$sel_ret=imw_query("select order_id,patient_id from in_order_return where del_status='0' $ret_pat_whr group by order_id");
	while($row_ret=imw_fetch_array($sel_ret)){
		$ret_ord_arr[$row_ret['order_id']]=$row_ret['order_id'];
	}
	$saved=0;
	if(isset($_POST['txt_save']) && $_POST['txt_save']=="Save")
	{
		$orderr_id = $_POST['update_order_id'];
		$page = $_POST['pager'];
		$reason=($_POST['reason_id'])?getReasonById($_POST['reason_id']):'';
		$orders_count = count($orderr_id);
		for($i=0;$i<$orders_count;$i++)
		{
			
			$single_order = $orderr_id[$i];
			$status = $_POST['status_'.$single_order];
			$red_st = $_POST['reduc_stock'];
			$sel_ord = change_order_status($orderr_id[$i], $status, $page, $red_st, $reason);
			$get_status = update_in_order_status($orderr_id[$i]);
			
			//UPDATE INVOICE,WS PRICE
			$qry="Update in_order SET invoice_no='".$_POST['invoice'.$single_order]."', 
			
			wholesale_price='".imw_real_escape_string(trim(preg_replace('/[^0-9|.]/','',$_POST['wholesale_price'.$single_order])))."' 
			WHERE id='".$orderr_id[$i]."'";
			$rs=imw_query($qry);
			unset($rs);
			
			//UPDATE CHILD TABLE FOR WHOLE SALE PRICE.
			if($_POST['hidden_wsprice'.$single_order]!=$_POST['wholesale_price'.$single_order]){
				$wholesaleTot='';
				$qry="Select id FROM in_order_details WHERE order_id='".$single_order."' AND del_status='0'";
				$rs=imw_query($qry);
				$numRows=imw_num_rows($rs);
				unset($rs);
				if($numRows==1){
					$qry="Update in_order_details SET wholesale_price='".$_POST['wholesale_price'.$single_order]."' 
					WHERE order_id='".$single_order."' AND del_status='0'";	
					$rs=imw_query($qry);
					unset($rs);
				}else{
					$qry="Update in_order_details SET wholesale_price='' WHERE order_id='".$single_order."'";
					$rs=imw_query($qry);
					unset($rs);
				}
			}
		}
		
		$saved=1;
	}
	
	$del_check_os=" and os.del_status='0'  AND os.arch_status=''";//AND os.order_status!='dispensed' - removed on berkeley req on 19may18
	$del_check=" and del_status='0'";//AND order_status!='dispensed' - removed on berkeley req on 19may18
    if($_REQUEST['rec_type']=='all')
	{
		$del_check=$del_check_os=" ";
	}
	elseif($_REQUEST['rec_type']!='Active' && $_REQUEST['rec_type']!='cancelled' && $_REQUEST['rec_type']!='all' && $_REQUEST['rec_type']!='' && strtolower($_REQUEST['rec_type'])!='archived' && strtolower($_REQUEST['rec_type'])!='returned')
	{
		if($_REQUEST['rec_type']=='pending')
		{
			$status_check_os=" AND (os.order_status='$_REQUEST[rec_type]' OR os.order_status='')";
			$status_check=" AND (order_status='$_REQUEST[rec_type]' OR order_status='')";	
		}
		else
		{
			$status_check_os=" AND os.order_status='$_REQUEST[rec_type]'";
			$status_check=" AND order_status='$_REQUEST[rec_type]'";
		}
		$del_check_os=" and os.del_status='0' AND os.arch_status=''";
		$del_check=" and del_status='0'";
	
	}elseif($_REQUEST['rec_type']=='cancelled')
	{
		$del_check_os=" and os.del_status='1'";
		$del_check=" and del_status='1'";
	}
	elseif(strtolower($_REQUEST['rec_type'])=='archived')
	{
		$del_check_os=" AND os.arch_status='archived'";	
		$del_check="";	
	}elseif(strtolower($_REQUEST['rec_type'])=='returned')
	{
		$ret_ord_imp=implode("','",$ret_ord_arr);
		$del_check_os="";	
		$del_check="";
		$ret_ord_whr=" and os.id in('".$ret_ord_imp."')";
	}
	
	if(isset($_REQUEST['search']) && $_REQUEST['search']=="Search")
	{
		$search_ordernum = $_REQUEST['order_num'];
		$search_upcCode = $_REQUEST['upc_code'];
		$search_itemname = $_REQUEST['item_name'];
		$start_date=$_REQUEST['date_from'];
		$end_date=$_REQUEST['date_to'];
		$pagename = $_REQUEST['pagename'];
		$order_page = $_REQUEST['order'];
		
		$swhere="";
		
		if($pagename=="all")
		{
			$page = "where arch_status=''";
			$targetpage="index.php?order=all".$ext_parm;
		}
		elseif($pagename=="pending")
		{
			$page = "where ((ord.order_status='".$pagename."' or ord.order_status='') AND arch_status='' )";
			$targetpage="index.php?order=pending".$ext_parm;
		}
		elseif($pagename=="pt_hst")
		{
			$page = "where ord.patient_id='$patient_id'";
			$status_where=" where patient_id='$patient_id'";
			$targetpage="index.php?order=pt_hst".$ext_parm;
		}
		elseif($pagename=="archived")
		{
			$page="where arch_status='archived'";
			$targetpage="index.php?order=archived".$ext_parm;	
		}
		else
		{
			$page = "where ord.order_status='".$pagename."' AND arch_status=''";
			$targetpage="index.php?order='".$pagename."'".$ext_parm;
		}
		
		$targetpage .= "&date_from=".$start_date."&date_to=".$end_date;
		
		if($search_ordernum!="")
		{
			$swhere .= " and os.id = '".$search_ordernum."'";
		}
	
		if($search_upcCode!="")
		{
			$swhere .= " and ord.upc_code like '".$search_upcCode."%'";
		}
		
		if($search_itemname!="")
		{
			$swhere .= " and ord.item_name like '".$search_itemname."%'";
		}
		if($start_date!=="" && $end_date=="")
		{
			$date=explode("-",$start_date);
			$y=$date[2];
			$m=$date[0];
			$d=$date[1];
			$new_date=$y."-".$m."-".$d;
			$swhere .=" and ord.entered_date >= '".$new_date."'";
		}
		if($end_date!=="")
		{
			if($start_date!="")
			{
			
			$s_date=explode("-",$start_date);
			$e_date=explode("-",$end_date);
			$e_d=$e_date[1];
			$e_m=$e_date[0];
			$e_y=$e_date[2];
			
			$s_y=$s_date[2];
			$s_m=$s_date[0];
			$s_d=$s_date[1];
			$new_s_date=$s_y."-".$s_m."-".$s_d;
			$new_e_date=$e_y."-".$e_m."-".$e_d;
			
			$swhere .=" and os.entered_date between '".$new_s_date."%' and '".$new_e_date."%'";
			}
			else
			{
				echo "<script>top.falert('Select Start date first')</script>";
			}
		}
		if($search_item_type!=''){
			$swhere .= " and ord.module_type_id IN(".$search_item_type.")";
		}
		
		$qry = "select os.id, os.entered_date, os.patient_id, os.total_qty, os.total_price, os.modified_by,  os.order_status, os.del_status, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.mname as us_mname, us.lname as us_lname, ord.upc_code, ord.item_name, SUM(ord.pt_paid) as pt_total_paid, SUM(ord.ins_amount + ord.ins_amount_os) as ins_paid, os.order_enc_id from in_order as os inner join patient_data as pd on pd.id = os.patient_id inner join users as us on us.id = os.modified_by inner join in_order_details as ord on ord.order_id = os.id ".$page.$swhere." $del_check_os $status_check_os $ret_ord_whr GROUP BY os.id order by os.entered_date desc, os.id desc";// and ord.pof_check='0'
		
		
	}
	else
	{
		$start_date=isset($_REQUEST['date_from'])?$_REQUEST['date_from']:'';
		$end_date=isset($_REQUEST['date_to'])?$_REQUEST['date_to']:'';
		$order_page = $_REQUEST['order'];

		if($order_page=="pending")
		{
			$wh = "where (ord.order_status='".$order_page."' or ord.order_status='' and arch_status='')";
			$targetpage="index.php?order=pending".$ext_parm;
		}
		elseif( $order_page == 'all')
		{
			$wh = "where 1=1";
			$targetpage="index.php?order=all".$ext_parm;
		}
		elseif($order_page=="pt_hst")
		{
			$wh = "where ord.patient_id='$patient_id'";
			$status_where=" where patient_id='$patient_id'";
			$targetpage="index.php?order=pt_hst&nw_stl=yes".$ext_parm;
		}
		elseif($order_page=="archived")
		{
			$wh="where arch_status='archived'";
			$targetpage="index.php?order=archived".$ext_parm;	
		}
		else
		{
			$wh = "where ord.order_status='".$order_page."'";
			$targetpage="index.php?order=".$order_page.$ext_parm;
		}
		$targetpage .= "&date_from=".$start_date."&date_to=".$end_date;
		
		if($start_date!="" && $end_date=="")
		{
			$date=explode("-",$start_date);
			$y=$date[2];
			$m=$date[0];
			$d=$date[1];
			$new_date=$y."-".$m."-".$d;
			$wh .=" and ord.entered_date >= '".$new_date."%'";
		}
		if($end_date!=="")
		{
			if($start_date!="")
			{
			
			$s_date=explode("-",$start_date);
			$e_date=explode("-",$end_date);
			$e_d=$e_date[1];
			$e_m=$e_date[0];
			$e_y=$e_date[2];
			
			$s_y=$s_date[2];
			$s_m=$s_date[0];
			$s_d=$s_date[1];
			$new_s_date=$s_y."-".$s_m."-".$s_d;
			$new_e_date=$e_y."-".$e_m."-".$e_d;
			
			$wh.=" and os.entered_date between '".$new_s_date."%' and '".$new_e_date."%'";
			}
			else
			{
				/*echo "<script>top.falert('Select Start date first')</script>";*/
			}
		}
		
		if($search_item_type!=''){
			$ret_ord_whr .= " and ord.module_type_id IN(".$search_item_type.")";
		}
		
		$qry="select os.id, os.entered_date, os.patient_id, os.total_qty, os.total_price, os.modified_by, os.order_status, os.del_status, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.mname as us_mname, us.lname as us_lname, ord.id as orderid, ord.order_status as item_order_status, SUM(ord.pt_paid) as pt_total_paid, SUM(ord.ins_amount + ord.ins_amount_os) as ins_paid, os.order_enc_id from in_order as os inner join patient_data as pd on pd.id = os.patient_id inner join users as us on us.id = os.modified_by inner join in_order_details as ord on ord.order_id = os.id ".$wh." $del_check_os $status_check_os $ret_ord_whr GROUP BY os.id order by os.entered_date desc, os.id desc";// and ord.pof_check='0'
	}
	$disp_qry=imw_query("select max(order_date) as status_date,order_time,order_id from in_order_detail_status $status_where group by order_id order by id asc");// where order_status='dispensed'
	while($disp_data=imw_fetch_array($disp_qry)){
		$disp_ord_data[$disp_data['order_id']]=getDateFormat($disp_data['status_date']);
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<?php include_once("../../reports/report_includes.php");?>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<!--tool tip files-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-ui.js?<?php echo constant("cache_version"); ?>"></script>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery-ui.css?<?php echo constant("cache_version"); ?>" />
<!--tool tip files end here-->

<script type="text/javascript">

var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
function selectCurrentCheck(ab){
	$("#checked_"+ab).prop('checked', true);
	if($("#checked_"+ab).is(':checked')){
		$('#txt_save').val('Save');
	}
}
function del_callBack(result)
{
	if(result==true)
	{
		$("#del_hidden").val("1");
		$("#firstform").submit();
	}	
}
$(document).ready(function(){

	del = function(){
	 	if( $(".getchecked:checked").length == 0 ){
           top.falert('Please check atleast one record');
        }else{
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$("tr:not(.delRow) .getchecked").prop('checked', true);
		}else{
			$(".getchecked").prop('checked', false);
		}
	});
	
	
	var saved='<?php echo $saved;?>'
	if(saved=='1'){
		$('#txt_save').val('');
	}
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

function prod_popup(order_id,page)
{
	//window.open('order_ajax.php?ord_id='+order_id+'&page='+page,'prod_popup','width=800,height=500,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');		
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('win1','order_ajax.php?ord_id='+order_id+'&page='+page+'&frameName='+iframe,'','width=920,height=500,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');
	win1.focus();
}
	
function status_hx_popup(order_id)
{
	//window.open('order_ajax.php?ord_id='+order_id+'&page='+page,'prod_popup','width=800,height=500,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');		
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('win1','status_hx.php?ord_id='+order_id+'&frameName='+iframe,'','width=700,height=500,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');
	win1.focus();
}

function cancel_popup_callBack(result)
{
	if(result!=false)
	{
		 var sThisVal ='';
		//collect order ids
		$('input:checkbox.getchecked').each(function () {
		  sThisVal = sThisVal+(this.checked ? $(this).val()+',' : "");
		});
		top.WindowDialog.closeAll();
		var win2=top.WindowDialog.open('win2',WEB_PATH+'/interface/admin/order/cancel_order_ajax.php?reason='+result+'&ord_id='+sThisVal+'&frameName='+iframe,'win2','width=100,height=100,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');
		win2.focus();	
	}
}
function cancel_popup()
{
	 var sThisVal ='';
	//collect order ids
	$('input:checkbox.getchecked').each(function () {
      sThisVal = sThisVal+(this.checked ? $(this).val()+',' : "");
	});

	if(!sThisVal)
	{
		top.falert("Please select a record");
	}
	else
	{
		top.fprompt('Cancel Orders','Reason',cancel_popup_callBack, true);
	}
}

function return_item_popup(order_id,page)
{
	top.WindowDialog.closeAll();
	var win3=top.WindowDialog.open('win3','return_order_itms.php?ord_id='+order_id+'&page='+page+'&frameName='+iframe,'Return_item_popup','width=840,height=500,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');
	win3.focus();		
}

function open_pos(order_id,type_id)
{
	if(type_id==1 || type_id==2)
	{
		top.frames['main_iframe'].document.getElementsByClassName('tab_container')[0].style.display="none";
		top.frames['main_iframe'].document.getElementById('default_frame_pt_hst').style.display="none";
		adminFrame = top.frames['main_iframe'].document.getElementById('admin_iframe');
		$(adminFrame).attr('src','pt_frame_selection_1.php?order_id='+order_id).show();
		//top.frames['main_iframe'].frames['admin_iframe'].location.href ='../../patient_interface/pt_frame_selection_1.php?order_id='+order_id;
	}
	else if(type_id==3)
	{
		top.frames['main_iframe'].document.getElementsByClassName('tab_container')[0].style.display="none";
		top.frames['main_iframe'].document.getElementById('default_frame_pt_hst').style.display="none";
		adminFrame = top.frames['main_iframe'].document.getElementById('admin_iframe');
		$(adminFrame).attr('src','contact_selection.php?order_id='+order_id).show();
		//top.frames['main_iframe'].location.href = '../../patient_interface/contact_selection.php?order_id='+order_id;
	}
	else
	{
		top.frames['main_iframe'].document.getElementsByClassName('tab_container')[0].style.display="none";
		top.frames['main_iframe'].document.getElementById('default_frame_pt_hst').style.display="none";
		adminFrame = top.frames['main_iframe'].document.getElementById('admin_iframe');
		$(adminFrame).attr('src','other_selection.php?order_id='+order_id).show();
		//top.frames['main_iframe'].location.href = '../../patient_interface/contact_selection.php?order_id='+order_id;
	}
	//window.location.href='../../patient_interface/patient_pos.php?pt_pos='+order_id;
}

<?php
	$AllUpcArray=$AllNameArray=$AllUpcIdArray="";
	if($AllUpcArray!=""){?>
	//var custom_array_upc= new Array(<?php //echo remLineBrk($AllUpcArray); ?>);
<?php } ?>

<?php if($AllNameArray!=""){?>
	//var custom_array_name= new Array(<?php //echo remLineBrk($AllNameArray); ?>);
<?php } ?>

var custom_array_upc_id;
<?php if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	//custom_array_upc_id= new Array(<?php //echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	//custom_array_upc_id= new Array('<?php //echo $AllUpcIdArray; ?>');
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

function change_archive(b)
{
	
	var id=document.getElementsByName('update_order_id[]');
	var id_len=document.getElementsByName('update_order_id[]').length;
	var a=0;
	var order_id="";
	for(i=0;i<id_len;i++)
	{
		if(id[i].checked==true)
		{
			order_id+=id[i].value+",";
			a++;
		}
	}
	
	if(order_id==""){
		
		if(a==0){
			if(b==1){
				top.falert("Please Select Record(s) to Un-Archive");
			}
			else{
				top.falert('Please Select Record(s) to Archive');	
			}
		}
		return false;
	}
	
	
	if(b!=1)
	{
		$.ajax({
			type:"POST",
			url:"order_arch.php",
			data:"order_id="+order_id,
			cache:false,
			success: function(msg)
			{
				if(msg!="")
				{
				top.falert("Selected Record(s) Moved To Archive");
				location.reload();
				}
			}
		});
	}
	else
	{
		$.ajax({
			type:"POST",
			url:"order_arch.php",
			data:"un_order_id="+order_id,
			cache:false,
			success: function(msg)
			{
				if(msg==0)
				{
					top.falert("Selected Record(s) Updated Successfully");
				}
				location.reload();
			}
		});
	}
	
}
</script>
</head>
<body>
<?php if($_REQUEST['nw_stl']=="yes")
{?>
<div class="" style="height:<?php echo $_SESSION['wn_height']-520;?>px;">
<?php }else
{?>
<div class="mt10" style="height:<?php echo $_SESSION['wn_height']-202;?>px;">
  <?php }?>
  <form name="search_rec" id="firstform" method="post" style="margin:0px;" action="./index.php?order=<?php echo $order_page; ?>&nw_stl=<?php echo $_REQUEST['nw_stl'];?>">
    <input type="hidden" name="upc_id" id="upc_id" value="">
    <table class="table_collapse table_cell_padd2" style="margin: 0px 0px 3px;">
      <tr style="background: #D5EAF7;">
        <?php if($_REQUEST['nw_stl']=="yes")
        {?>
        <td width="45" style="font-weight:bold;"> History</td>
        <?php }
          else
			{
		?>
        <td style="font-weight:bold;" colspan="13">View
          <?php if($order_page=="pt_hst") { echo "Patient History"; } else { echo ucfirst($order_page); ?>
          Orders
          <?php } ?></td></tr><tr style="background: #D5EAF7;">
        <?php } ?>
        <td width="55"><label for="order_num">Order#:</label></td>
        <td width="80"><input type="text" style="width:60px;" name="order_num" id="order_num" value="<?php echo $_POST['order_num']; ?>" autocomplete="off"></td>
        <td width="88" style="text-align:right;"><label for="upc_code">UPC Code:</label></td>
        <td width="75"><input type="text" style="width:75px;" name="upc_code" id="upc_code" value="<?php echo $_POST['upc_code']; ?>" onChange="javascript:upc(document.getElementById('upc_id'));" autocomplete="off"></td>
        <td width="88" style="text-align:right;"><label for="item_name">Item Name:</label></td>
        <td width="80"><input type="text" style="width:75px;" id="item_name" name="item_name" value="<?php echo $_POST['item_name']; ?>" onChange="javascript:upc(document.getElementById('upc_id'));" autocomplete="off"></td>
        <td width="81" style="text-align:right;"><label for="date_from">Start Date</label></td>
        <td width="54" nowrap="nowrap">
        	<input type="text"  class="date-pick" size="9" name="date_from" id="date_from" style="height: 21px; background-size:17px 21px;width: 70px;" value="<?php echo $start_date; ?>" autocomplete="off" />
        </td>
        <td width="76" style="text-align:right;"><label for="date_to">End Date</label></td>
        <td width="56" nowrap="nowrap" style="padding-left: 5px;">
        	<input type="text" size="9" class="date-pick" name="date_to" id="date_to" style="height: 21px; background-size:17px 21px;width: 70px;" value="<?php echo $end_date; ?>" autocomplete="off" />
        </td>
        <td width="40" nowrap="nowrap" style="padding-left: 5px;">
        	<select name="item_type" id="item_type" style="width: 90px;">
        		<option value="">Item Type</option>
        		<option <?php echo ($search_item_type=='1,2')?'selected':''; ?> value="1,2">Frame & Lenses</option>
        		<option <?php echo ($search_item_type=='3')?'selected':''; ?> value="3">Contact Lenses</option>
        		<option <?php echo ($search_item_type=='6')?'selected':''; ?> value="6">Medication</option>
        		<option <?php echo ($search_item_type=='5,7')?'selected':''; ?> value="5,7">Other</option>
        	</select>
        </td>
        <td width="42" class="btn_cls">
           <select name="rec_type2" id="rec_type2" onChange="show_records(this)" style="padding:4px;width:70px;">
            <option value="all" <?php echo ($_REQUEST['rec_type']=='all' || $_REQUEST['rec_type']=='')?' selected':'';?>>All</option>
            <option value="Active" <?php echo ($_REQUEST['rec_type']=='Active')?' selected':'';?>>Active</option>
            <option value="Archived" <?php echo (strtolower($_REQUEST['rec_type'])=='archived')?' selected':'';?>>Archived</option>
            <option <?php if($_REQUEST['rec_type']=="cancelled") { echo "selected='selected'"; } ?> value="cancelled">Cancelled</option>
            <option <?php if($_REQUEST['rec_type']=="dispensed") { echo "selected='selected'"; } ?> value="dispensed">Dispensed</option>
			<option <?php if($_REQUEST['rec_type']=="notified") { echo "selected='selected'"; } ?> value="notified">Notified</option>
            <option <?php if($_REQUEST['rec_type']=="ordered") { echo "selected='selected'"; } ?> value="ordered">Ordered</option>
            <option <?php if($_REQUEST['rec_type']=="pending") { echo "selected='selected'"; } ?> value="pending">Pending</option>
            <option <?php if($_REQUEST['rec_type']=="received") { echo "selected='selected'"; } ?> value="received">Received</option>
			<option <?php if($_REQUEST['rec_type']=="returned") { echo "selected='selected'"; } ?> value="returned">Returned</option>
          </select></td>
        <td width="64" class="btn_cls"><input type="hidden" name="pagename" id="pagename" value="<?php echo $order_page; ?>">
          <input type="submit" value="Search" name="search"></td>
      </tr>
    </table>
    <input type="hidden" name="rec_type" id="rec_type" value="<?php echo $_REQUEST['rec_type'];?>">
  </form>
  <form name="change_status" id="firstform1" method="post" style="margin:0px;">
    <?php 
					$rows = imw_query($qry)or die(imw_error().' 453');
					$cnt  =	imw_num_rows($rows);
					
					$limit = 20;
					$total_pages = $cnt/$limit;
					$page1 = $page;
					$page_space=($cnt>$limit)?35:65;
					$page = imw_escape_string($_GET['page']);
					if($page){
						$start = ($page - 1) * $limit; 
					}else{
						$start = 0;	
					}
					
					//get patient paid detail from in_order table
					$qPtPaid = "SELECT SUM(`ord`.`pt_paid`+`ord`.`pt_paid_os`) as 'total_pt_paid', `ord`.`order_id`, `ord`.`module_type_id`, `os`.`tax_pt_paid`, SUM(ord.ins_amount + ord.ins_amount_os) as ins_paid FROM `in_order_details` `ord` LEFT JOIN `in_order` `os` ON(`ord`.`order_id` = `os`.`id`) ";
					
					if($_REQUEST['search']==""){
						$qPtPaid .= $wh." ".$del_check_os.$status_check_os;
					}
					else{
						$qPtPaid .= $page1.$swhere." ".$del_check_os.$status_check_os;
					}
					
					$qPtPaid .= " $ret_ord_whr and ord.`del_status`=0 GROUP BY `ord`.`order_id` ORDER BY `os`.`entered_date` DESC, `os`.`id` DESC LIMIT $start, $limit";// AND `ord`.`pof_check`='0'
					
					$qPtPaid=imw_query($qPtPaid)or die(imw_error());
					while($ptPaid=imw_fetch_object($qPtPaid))
					{
						if($ptPaid->module_type_id=="3"){
							$data1 = imw_query("SELECT SUM(`pt_paid`) AS 't_pt_paid' FROM `in_order_cl_detail` WHERE `order_id`='".$ptPaid->order_id."' AND `del_status`=0");
							if($data1 && imw_num_rows($data1)>0){
								$data1row = imw_fetch_object($data1);
								$ptPaid->total_pt_paid = $ptPaid->total_pt_paid+$data1row->t_pt_paid;
							}
						}
						$pat_paid[$ptPaid->order_id]=$ptPaid->total_pt_paid+$ptPaid->tax_pt_paid;
						$ins_paid[$ptPaid->order_id]=$ptPaid->ins_paid;
					}
					
					  $qry = "select os.re_make_id,if(od.module_type_id!=2,od.order_chld_id,orlp.order_chld_id) as order_chld_id,od.id, os.id, os.entered_date, os.patient_id, os.total_qty, os.total_price,os.grand_total, os.modified_by,  os.order_status, os.del_status, os.arch_status, os.invoice_no, os.wholesale_price, os.comment, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.mname as us_mname, us.lname as us_lname, od.upc_code, od.item_name,os.order_enc_id,os.loc_id, os.due_date 
					  FROM in_order as os 
					  INNER JOIN patient_data as pd ON pd.id = os.patient_id 
					  INNER JOIN users as us ON us.id = os.modified_by 
					  INNER JOIN 
						(SELECT MAX(id) order_detail_id,patient_id,order_id,upc_code,del_status as del_status_det,item_name,order_chld_id,module_type_id,qty,qty_right,loc_id,price,total_amount, order_status, entered_date
							FROM in_order_details where 1=1 $del_check
							GROUP BY order_id
						)
						as ord on ord.order_id = os.id 
						INNER JOIN in_order_details od ON ord.order_detail_id = od.id 
						LEFT JOIN in_order_lens_price_detail as orlp ON (ord.order_id=orlp.order_id and orlp.order_detail_id= ord.order_detail_id and orlp.order_chld_id!='')
								";
					if($_REQUEST['search']=="")
					{ 
						// $qry = "select os.re_make_id, os.id, os.entered_date, os.patient_id, os.total_qty, os.total_price,os.grand_total, os.modified_by,  os.order_status, os.del_status, os.arch_status, os.invoice_no, os.wholesale_price, os.comment, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.mname as us_mname, us.lname as us_lname, ord.upc_code, ord.item_name,os.order_enc_id,os.loc_id, os.due_date from in_order as os inner join patient_data as pd on pd.id = os.patient_id inner join users as us on us.id = os.modified_by inner join in_order_details as ord on ord.order_id = os.id ".$wh." $del_check_os $status_check_os and  ord.pof_check='0' $ret_ord_whr GROUP BY os.id order by os.entered_date desc, os.id desc LIMIT $start, $limit";
						$qry .= $wh." $del_check_os $status_check_os $ret_ord_whr 
						GROUP BY os.id order by os.entered_date desc,os.id desc LIMIT $start, $limit";// and  od.pof_check='0'
					}
					else
					{
						// $qry = "select os.re_make_id, os.id, os.entered_date, os.patient_id, os.total_qty, os.total_price,os.grand_total, os.modified_by,  os.order_status, os.del_status, os.arch_status, os.invoice_no, os.wholesale_price, os.comment, pd.fname, pd.mname, pd.lname, us.fname as us_fname, us.mname as us_mname, us.lname as us_lname, ord.upc_code, ord.item_name,os.order_enc_id,os.loc_id, os.due_date from in_order as os inner join patient_data as pd on pd.id = os.patient_id inner join users as us on us.id = os.modified_by inner join in_order_details as ord on ord.order_id = os.id ".$page1.$swhere." ".$del_check_os.$status_check_os." and  ord.pof_check='0' $ret_ord_whr GROUP BY os.id order by os.entered_date desc, os.id desc LIMIT $start, $limit";
						  
						$qry .= $page1.$swhere." ".$del_check_os.$status_check_os." $ret_ord_whr 
						GROUP BY os.id order by os.entered_date desc,os.id desc LIMIT $start, $limit";// and  ord.pof_check='0'
					}
					$res = imw_query($qry)or die(imw_error().' 442');
					$nums = imw_num_rows($res);
					
					if($nums > 0)
					{
						
						 if($_REQUEST['nw_stl']=="yes")
{?>
    <table class="table_cell_padd2" cellpadding="0" cellspacing="0" width="100%">
      <tr class="listheading" style="text-align:center;background-size:4px;">
        <?php //if($order_page!="dispensed") 
		  //{ ?>
        <td style="text-align:left;width:30px;"><input type="checkbox" name="select_all" id="selectall" /></td>
        <?php //} ?>
        <td style="text-align:center;width:70px;">Order#</td>
        <td style="text-align:center;width:120px;">Order Date</td>
        <?php if($order_page!="pt_hst") { ?>
        <td style="text-align: center;width:125px;">Patient-Id</td>
        <?php } ?>
        <td style="text-align: center;width:65px;">Qty.</td>
        <td style="text-align: left;width:90px;">Amount</td>
        <td style="text-align: left;width:95px;">Pt. Paid</td>
        <td style="text-align: left;width:90px;">Balance</td>
        <td style="text-align: left;width:100px;">Invoice#</td>
        <td style="text-align: left;width:100px;">WS Price</td>
		<?php if($order_page=="pt_hst") { ?>
       		<td style="text-align: left;width:140px;">Comments</td>
		<?php }
		else{ ?>
			<td style="text-align: left;width:140px;">Cmnts</td>
		<?php } ?>
		
        <td style="width:60px;">Oper.</td>
        <?php if($order_page=="pt_hst") { ?>
        <td style="width:70px;">Type</td>
        <?php } ?>
        <td style="width:125px;">
			<select id="bulk_update_status" onChange='if(this.value!="0"){$(".order_status_dd").val(this.value).trigger("change");}' style="float:right;">
				<option value="0">Status</option>
				<option value="dispensed">Dispensed</option>
				<option value="notified">Notified</option>
				<option value="ordered">Ordered</option>
				<option value="pending">Pending</option>
				<option value="received">Received</option>
			</select>
		</td>
        <?php if($order_page=="pt_hst") { ?>
        <td style="width:auto">&nbsp;</td>
        <?php } ?>
      </tr>
    </table>
    <div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-(692-$page_space);?>px;overflow-y:auto">
    <?php }else
{?>
    <table class="table_cell_padd2" width="100%" cellpadding="0" cellspacing="0">
      <tr class="listheading" style="text-align:center;background-size:4px;">
        <?php //if($order_page!="dispensed") 
		  { ?>
        <td style="width:30px;"><input type="checkbox" name="select_all" id="selectall" /></td>
        <?php } ?>
        <td style="width:60px;">Order#</td>
        <td style="width:100px;">Order Date</td>
        <?php if($order_page!="pt_hst") { ?>
        <td style="text-align: left; width: 125px;">Patient-Id</td>
        <?php } ?>
        <td style="width:50px;">Qty.</td>
        <td style="width:95px;text-align: left;">Amount</td>
        <td style="text-align: left;width:90px;">Pt. Paid</td>
        <td style="text-align: left;width:85px;">Balance</td>
        <td style="width:75px;">Invoice#</td>
        <td style="width:75px;">WS Price</td>
        <td style="width:140px;">Cmnts</td>
        <td style="width:30px;">Oper.</td>
        <?php if($order_page=="pt_hst") { ?>
        <td style="width:70px;">Type</td>
        <?php } ?>
        <td style="width:125px;text-align:right;">
			<select id="bulk_update_status" onChange='if(this.value!="0"){$(".order_status_dd").val(this.value).trigger("change");}'>
				<option value="0">Status</option>
				<option value="dispensed">Dispensed</option>
				<option value="notified">Notified</option>
				<option value="ordered">Ordered</option>
				<option value="pending">Pending</option>
				<option value="received">Received</option>
			</select>
		</td>
        <?php if($order_page=="pt_hst") { ?>
        <td style="width:auto">&nbsp;</td>
        <?php } ?>
      </tr>
    </table>
    <div id="listing_record" style="height:<?php echo $_SESSION['wn_height']-(530-$page_space);?>px; overflow-y:auto;">
      <?php }?>
      <table class="table_collapse table_cell_padd2">
        <tr>
          <td style="width:20px;"></td>
          <td style="width:80px;"></td>
          <td style="width:117px;"></td>
          
          <?php if($order_page!="pt_hst") { ?>
          <td style="width:100px;"></td>
          <?php } else {?>
          <td style="width:60px;"></td>
          <?php }?>
          <td style="width:90px;"></td>
          <td style="width:85px;"></td>
          <td style="width:85px;"></td>
          <td style="width:75px;"></td>
          <td style="width:100px;"></td>
		  <td style="width:145px;"></td>
          <?php if($order_page=="pt_hst") { ?>
          <td style="width:30px;"></td>
          <?php }?>
          <td style="width:75px;"></td>
          <?php if($order_page=="pt_hst") { ?>
          <td style="width:auto"></td>
          <?php } ?>
        </tr>
        <?php /*echo '<tr class="listheading" style="text-align:center;">
          <?php //if($order_page!="dispensed") 
		  { ?>
          <td width="10"><input type="checkbox" name="select_all" id="selectall" /></td>
          <?php } ?>
          <td width="55">Order#</td>
          <td width="90">Order Date</td>
          <?php if($order_page!="pt_hst") { ?>
          <td style="text-align: left; width: 200px;">Patient-Id</td>
          <?php } ?>
          <td width="60">Qty.</td>
          <td width="60" style="text-align: right;">Amount</td>
          <td width="75">Oper.</td>
          <?php if($order_page=="pt_hst") { ?>
          <td width="60">POS</td>
          <?php } ?>
          <td width="90">Status</td>
          <?php if($order_page=="pt_hst") { ?>
          <td width="60">&nbsp;</td>
          <?php } ?>
        </tr>';*/
						while($rows = imw_fetch_array($res)) {
							$opr_fname = $rows['us_fname'][0];
							$opr_mname = $rows['us_mname'][0];
							$opr_lname = $rows['us_lname'][0];
							$oper_name = $opr_fname.$opr_mname.$opr_lname;
							$loc_name = $loc_arr[$rows['loc_id']];
						if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";} 
						$it_ord_stat=array();
						$sel_status_itm = imw_query("select order_status, `safety_glass`, `pof_check`, `lab_id`, `module_type_id`,qty_reduced from in_order_details where order_id='".$rows['id']."'  $del_check $status_check")or die(imw_error().' 535');
						$safety_glass = false;
						
						$reduce_qty_pof = false;
						$reduce_qty_lab = false;
						$qty_reduced = false;
						
						while($row_stat = imw_fetch_array($sel_status_itm))
						{
							$it_ord_stat[] = $row_stat['order_status'];
							if($row_stat['safety_glass']==1){$safety_glass=true;}
							if($row_stat['qty_reduced']==1){$qty_reduced=true;}
							if($row_stat['pof_check']=="0" && $row_stat['module_type_id']==1){$reduce_qty_pof = true;}
							else if($row_stat['lab_id']=="0" && $row_stat['module_type_id']==2){$reduce_qty_lab = true;}
							else if($row_stat['module_type_id']>2){$reduce_qty_lab = $reduce_qty_pof = true;}
						}
						$remakeBg = "";
						if($rows['re_make_id']!="0"){
							$remakeBg = "remake_bg";
						}
						elseif($safety_glass){
							$remakeBg = "sagety_glass_bg";
						}
						$order_discount = get_total_order_discount($rows['id']);
				?> 
        <tr class="<?php echo $rowbg." ".$remakeBg; echo(($rows['del_status']==1)?" delRow":""); ?>" style="cursor:pointer;<?php echo($rows['del_status']==1 || strtolower($rows['arch_status'])=='archived')?'  text-decoration:line-through; color:#F00':'';?>">
          <?php //if($order_page!="dispensed") 
		  { ?>
          <td style="text-align:center;">
          <input type="hidden" name="hidden_wsprice<?php echo $rows['id'];?>" value="<?php echo $rows['wholesale_price'];?>">
          <input type="checkbox" class="getchecked" name="update_order_id[]" value="<?php echo $rows['id'];?>" id="checked_<?php echo $rows['id']; ?>" <?php echo($rows['del_status']==1)?' disabled':'';?>></td>
          <?php } ?>
          <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');">
		 	<?php if($rows['order_chld_id']>0){?>
		 		
          		<span><img src="../../../images/flag_green.png" style="width:16px; display:inline-block;" title="Posted"/></span>
           <?php } ?>
			<?php echo $rows['id']; ?>
		  </td>
          <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo getDateFormat($rows['entered_date']); ?></td>
          <?php if($order_page!="pt_hst") { ?>
          <td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo $rows['lname'].",&nbsp;".$rows['fname']."&nbsp;".$rows['mname']." - ".$rows['patient_id']; ?></td>
          <?php } ?>
          <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo $rows['total_qty']; $gQty+=$rows['total_qty'];?></td>
          <td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo currency_symbol(); ?><?php echo $rows['grand_total'];  $gTotalPrice+=$rows['grand_total'] ?></td>
          <td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo currency_symbol(); ?><?php echo number_format($pat_paid[$rows['id']],2,'.',''); $gPtPaid+=$pat_paid[$rows['id']];?></td>
          <td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo currency_symbol(); ?><?php echo number_format($rows['grand_total']- ($pat_paid[$rows['id']]+$order_discount+$ins_paid[$rows['id']]),2,'.',''); 
							$gTotalBalance += $rows['grand_total']- ($pat_paid[$rows['id']]+$order_discount+$ins_paid[$rows['id']]); ?></td>
          <td style="text-align:center;">
          	<input type="text" name="invoice<?php echo $rows['id'];?>" onChange="selectCurrentCheck('<?php echo $rows['id']; ?>')" value="<?php echo $rows['invoice_no'];?>" style="width:70px">
          </td>
          <td style="text-align:center;">
          	<input type="text" name="wholesale_price<?php echo $rows['id'];?>" onChange="selectCurrentCheck('<?php echo $rows['id']; ?>')" value="<?php echo $rows['wholesale_price'];?>" style="width:70px">
          </td>
          <td style="text-align:left;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');" title="<?php echo stripcslashes($rows['comment'])?>"><?php echo substr(stripcslashes($rows['comment']),0,17); if($rows['comment'])echo'...';?></td>
          <td style="text-align:center;" onClick="javascript:prod_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><?php echo $oper_name; ?></td>
          
          <?php if($order_page=="pt_hst") { 
		  
		  $sel_status_itm = imw_query("select module_type_id from in_order_details where order_id='".$rows['id']."' $del_check $status_check")or die(imw_error().' 556');
		  $type_id=imw_fetch_array($sel_status_itm);
		  $qur_frm=imw_query("select * from in_module_type where id=".$type_id['module_type_id']."");
		  $type=imw_fetch_array($qur_frm);
		  ?>
          <td style="text-align: center;" title="<?php echo $type['module_type_name'];?>" onclick="<?php if($rows['del_status']==0 || (strtolower($type['module_type_name'])=="frame" || strtolower($type['module_type_name'])=="lenses" || strtolower($type['module_type_name'])=="contact lenses")){ echo "javascript:open_pos(".$rows['id'].",'".$type_id['module_type_id']."');"; }else{echo "javascript:prod_popup('".$rows['id']."','".$order_page."');";} ?>"><!--<td style="text-align: center;" onclick="<?php echo "javascript:open_pos(".$rows['id'].",'".$type_id['module_type_id']."');";?>">--><?php 
		  
		  if(strtolower($type['module_type_name'])=="frame" || strtolower($type['module_type_name'])=="lenses")
		  {
			?>
            <img src="../../../images/pt_interface/frame.jpg" style="width:40px;height:40px;border:1px solid #CCC;">
            <?php 
		  }
		  elseif(strtolower($type['module_type_name'])=="contact lenses")
		  {
		  	?>
            <img src="../../../images/pt_interface/lenses.jpg" style="width:40px;height:40px;border:1px solid #CCC;">
            <?php
		  }else { ?>
            <img src="../../../images/pt_interface/others.jpg" style="width:40px;height:40px;border:1px solid #CCC;">
            <?php } ?></td>
          <?php }/*echo $type['module_type_name']."<br />";*/ ?>
		  <?php
		  	$dispansed_date="";
		  	if($rows['order_status']=="dispensed"){
				$dispansed_date='Dispensed <br>'.$disp_ord_data[$rows['id']];
			}
		  ?>
          <td style="text-align:left;" nowrap><?php 
		  if($rows['order_status']=="dispensed") { 
		  	echo $dispansed_date;
		  }else if($order_page=="all" || $order_page=="pt_hst"  || $order_page=="archived") { ?>
            <select name="status_<?php echo $rows['id'];?>" style="width:98px;" onChange="selectCurrentCheck('<?php echo $rows['id']; ?>')" class="status_dropdown_<?php echo $rows['id'];?><?php echo ((!$reduce_qty_pof && !$reduce_qty_lab) || $qty_reduced)?" noQtyReduce":""; ?> order_status_dd" <?php echo($rows['del_status']==1 || $rows['arch_status']=='archived' || $rows['order_status']=="dispensed")?' disabled':'';?>>
             <option <?php if($rows['order_status']=="dispensed") { echo "selected='selected'"; } ?> value="dispensed">Dispensed</option>
			 <option <?php if($rows['order_status']=="notified") { echo "selected='selected'"; } ?> value="notified">Notified</option>
		     <option <?php if($rows['order_status']=="ordered") { echo "selected='selected'"; } ?> value="ordered">Ordered</option>
             <option <?php if($rows['order_status']=="pending" || $rows['order_status']=="") { echo "selected='selected'"; } ?> value="pending">Pending</option>
              <option <?php if($rows['order_status']=="received") { echo "selected='selected'"; } ?> value="received">Received</option>
            </select>
            <?php } else { ?>
            <select name="status_<?php echo $rows['id'];?>" style="width:98px;" onChange="selectCurrentCheck('<?php echo $rows['id']; ?>')" class="status_dropdown_<?php echo $rows['id'];?><?php echo ((!$reduce_qty_pof && !$reduce_qty_lab) || $qty_reduced)?" noQtyReduce":""; ?> order_status_dd" <?php echo($rows['del_status']==1 || $rows['order_status']=="dispensed")?' disabled':'';?>>
			<option <?php if($order_page=="dispensed") { echo "selected='selected'"; } ?> value="dispensed">Dispensed</option>
			<option <?php if($order_page=="notified") { echo "selected='selected'"; } ?> value="notified">Notified</option>
			<option <?php if($order_page=="ordered") { echo "selected='selected'"; } ?> value="ordered">Ordered</option>
            <option <?php if($order_page=="pending" || $rows['order_status']=="") { echo "selected='selected'"; } ?> value="pending">Pending</option>
              <option <?php if($order_page=="received") { echo "selected='selected'"; } ?> value="received">Received</option>
            </select>
            <?php } ?></td>
          <?php 
		  if(in_array("dispensed",$it_ord_stat)){ 
		  	if($ret_ord_arr[$rows['id']]>0){
				$ret_img="return_green";
			}else{
				$ret_img="return";
			}
		  ?>
          <td style="text-align: center;" class="btn_cls" onClick="javascript:return_item_popup('<?php echo $rows['id'];?>','<?php echo $order_page; ?>');"><img style="height:30px; cursor:pointer;" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/<?php echo $ret_img; ?>.png" /></td>
          <?php } else { ?>
          <td style="width:auto"></td>
          <?php  } ?>
        </tr>
		<?php $cols_chk="14";/*if($_REQUEST['nw_stl']=="yes"){$cols_chk="13";}*/ if($loc_name!=""){
		if($rows['due_date']!='' && $rows['due_date']!='0000-00-00'){
		  list($due_y,$due_m,$due_d)=explode('-',$rows['due_date']);
		  $due_date="$due_m-$due_d-$due_y";
		}else{$due_date='--';}
		  ?>
		<tr class="<?php echo $rowbg." ".$remakeBg; echo(($rows['del_status']==1)?" delRow":""); ?>" style="cursor:pointer;<?php echo($rows['del_status']==1 || strtolower($rows['arch_status'])=='archived')?'  text-decoration:line-through; color:#F00':'';?>">
			<td colspan="<?php echo $cols_chk-4; ?>" style="text-align:left"> <strong>Location : </strong> <?php echo $loc_name; ?>&nbsp;&nbsp; <strong>Order Due Date: </strong> <?php echo $due_date; ?></td>
			<td colspan="4" style="text-align: left" onClick="javascript:status_hx_popup('<?php echo $rows['id'];?>')";><strong>Status last update : </strong><?php echo $disp_ord_data[$rows['id']];?>
			</td>
		</tr>
        <?php }} ?>
      </table>
      <?php }
		else
		{
				
				 ?>
      <table width="100%">
        <tr>
          <?php if($_REQUEST['nw_stl']=="yes"){
			?>
          <td colspan="10" style="text-align:center"> No Records Exists </td>
          <?php 
		}
		else
		{?>
          <td colspan="8" style="text-align:center"> No Records Exists </td>
          <?php }?>
        </tr>
        <?php } ?>
      </table>
    </div>
    <?php if($nums > 0 && $order_page=="pt_hst") {?>
    <table cellpadding="2" cellspacing="0" width="100%" style="border-bottom:#6ab8e6 1px dotted;border-top:#6ab8e6 1px dotted">
      <tr style="text-align:center; font-weight:bold">
        <td width="200" style="text-align:right;width:200px;">Grand Total</td>
        <td width="80" style="text-align: center;width:80px;"><?php echo $gQty;?></td>
        <td width="82" style="text-align: left;"><?php echo currency_symbol(); ?><?php echo number_format($gTotalPrice,2,'.','');?></td>
        <td width="84" style="text-align: left;"><?php echo currency_symbol(); ?><?php echo number_format($gPtPaid,2,'.','');?></td>
        <td width="65" style="text-align: left;"><?php echo currency_symbol(); ?><?php echo number_format($gTotalBalance,2,'.','');?></td>
        <td width="72" >&nbsp;</td>
        <td width="72">&nbsp;</td>
        <td width="84">&nbsp;</td>
        <td width="1" style="width:auto">&nbsp;</td>
      </tr>
    </table>
    <?php }else{?>
    
    <?php }?>
    <div class="btn_cls">
      <?php
	  	$stages = 3;
		require_once'paging_new.php';
	  ?>
    </div>
    <input type="hidden" name="txt_save" id="txt_save" value="Save" />
    <input type="hidden" name="cancel" id="cancel" value="" />
    <input type="hidden" name="pager" value="<?php echo $order_page; ?>">
    <input type="hidden" name="reduc_stock" id="reduc_stock" value="no">
    <!--this variable is not being used on that page but placed here to stop js error , as it is being used in common script-->
    <input type="hidden" name="updateitems_ws" id="updateitems_ws" value="no">
    <input type="hidden" name="reason_id" id="reason_id" value="">
  </form>
</div>
<script type="text/javascript">
function show_records(obj)
{
	var strUser = obj.options[obj.selectedIndex].value;
	$("#rec_type").val(strUser);
	$("#firstform").submit();
}
function callMe(val1,val2)
{
	alert('say hi');	
}

jQ(document).ready(function(){
	
	$(function() {
    $( document ).tooltip();
  });
  
	jQ( ".date-pick" ).datepicker({
		changeMonth: true,changeYear: true,
		dateFormat: 'mm-dd-yy',
		onSelect: function() {
		$(this).change();
		}
	});
	<?php if($_REQUEST['s_v']){?>
		var sel_val='<?php echo $_REQUEST['s_v']; ?>';
		$("#rec_type2").val(sel_val);
		var obj_rec=document.getElementById("rec_type2");
		show_records(obj_rec);
	<?php }	?>
});

//var obj7 = new actb(document.getElementById('upc_code'),custom_array_upc,"","",document.getElementById('upc_id'),custom_array_upc_id);
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
		
//var obj8 = new actb(document.getElementById('item_name'),custom_array_name,"","",document.getElementById('upc_id'),custom_array_upc_id);
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

var iframe='admin_iframe';
<?php if($_REQUEST['order']=='pt_hst'){?> iframe='ptOrderHxIframe'; <?php }?>
	
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr = new Array();
	<?php if($nums>0){?>
		mainBtnArr[0] = new Array("frame","Print Order ","top.main_iframe."+iframe+".printpos();");
		mainBtnArr[1] = new Array("frame","Cancel Order","top.main_iframe."+iframe+".cancel_popup()");
		mainBtnArr[2] = new Array("frame","Save","top.main_iframe."+iframe+".check_dispensed(window.top.main_iframe."+iframe+")");
		<?php if($_REQUEST['order']!="archived") {?>
			mainBtnArr[3] = new Array("frame","Archive","top.main_iframe."+iframe+".change_archive(0)");	
		<?php }else{?>
			mainBtnArr[3] = new Array("frame","UnArchive","top.main_iframe."+iframe+".change_archive(1)");	
		<?php }?>
		top.btn_show("admin",mainBtnArr);
	<?php }else {?>	
		top.btn_show("admin",mainBtnArr);
	<?php }?>
});

</script>
<div id="reason_div_to_show" style="display:none">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
<tr>
    <td><select id="status_reason" style="width:100%">
    <option value="">Please Select Reason</option>
    <?php
    $q=imw_query("select id, reason_name from in_reason where del_status=0 order by reason_name asc")or die(imw_error());
    while($dlist=imw_fetch_object($q)){
        echo'<option value="'.$dlist->id.'">'.$dlist->reason_name.'</option>';
    }
    ?></select></td>
</tr>
<tr>
    <td><label id="reduce_qty_lb"><input type="checkbox" id="reduce_qty" name="reduce_qty" value="1">
    Yes, I want to reduce Qty from stock.</label></td>
</tr>
<tr>
    <td><label id="update_wholesale_lb"><input type="checkbox" id="update_wholesale" name="update_wholesale" value="1">
    Yes, I want to update whole sale price for stock.</label></td>
</tr>
</table>
</div>
</body>
</html>