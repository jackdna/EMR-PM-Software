<?php
	/*
	File: order_ajax.php
	Coded in PHP7
	Purpose: Edit/Save Order Status
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../config/config.php");
	require_once(dirname('__FILE__')."/../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];
	$order_id= $_REQUEST['order_id'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />

<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script type="text/javascript">
window.opener = window.opener.main_iframe.admin_iframe;
</script>

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
	
	$(".prac_code_cls").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		hidIDelem: document.getElementById('remake_prac_code_id'),
		showAjaxVals: 'defaultCodeOC'
	});
	
	selectCurrentCheck = function(ab){
		$("#checked_"+ab).prop('checked', true);
	}
	del = function(){
	 	if( $(".getchecked:checked").length == 0 ){
           falert('Please check atleast one record');
        }else{
			fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
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

<body><div id="modal-window" style="position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; z-index: 1050; overflow: auto; display:none">
<div class="modal-box modal-size-normal" style="position: absolute; top: 50%; left: 50%; margin-top: -102.5px; margin-left: -280px;">
<div class="modal-inner"><div class="modal-title">
<h3 id="modal-window-title">imwemr</h3>
<a class="modal-close-btn" onClick="closeMe()"></a></div>
<div id="modal-window-detail" class="modal-text">detail will be here</div>
<div class="modal-buttons">
<a class="modal-btn" onClick="closeMe()">Cancel</a>
<a class="modal-btn btn-light-blue" onClick="submitMe();">Confirm</a>
</div></div></div></div>

	<div style="width:750px; margin:0 auto;">
    <form name="addframe" id="firstform1" method="post" style="margin:0px;">
       <div class="module_border" style="height:250px">
            <div class="listheading"> Confirm Remake Order #<?php echo $order_id; ?>
			 </div>
            
          <table cellpadding="2" cellspacing="0" border="0" width="100%">
          <tr>
            <td>Reason:</td>
            <td>Prac Code:</td>
			<td>Price:</td>
          </tr>
          <tr>
          	<td width="70%">
            <select id="status_reason" style="width:100%" onChange="getOtherInfo(this)" required>
            <option value="0">Please Select Reason</option>
            <?php
            $q=imw_query("select * from in_return_reason where del_status=0 order by return_reason")or die(imw_error());
            while($dlist=imw_fetch_object($q)){
                echo'<option value="'.$dlist->id.'" prac="'.$dlist->prac_code.'" prac_id="'.$dlist->prac_code_id.'" price="'.$dlist->price.'">'.$dlist->return_reason.'</option>';
            }
            ?>
			<option value="other">Other</option>
			</select>
			<input type="text" name="other_reason" id="other_reason" style="width:496px;height:19px;display:none;" />
			<img id="other_reason_back" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/icon_back.png" style="display:none;cursor:pointer;" onClick="remake_reason_back();"/>
            </td>
            <td width="30%">
            <input type="text" class="prac_code_cls" name="prac_code" id="prac_code" value="" style="width: 96%;">
            </td>
			<td width="30%">
				<input type="text" class="price_cls" name="remake_price" id="remake_price" value="" width="120px">
            </td>
          </tr>
          <tr>
            <td>Comments:</td>
            <td></td>
			<td></td>
          </tr>
          <tr>
            <td colspan="3"><textarea name="comment" id="comment" style="width:99%" rows="3"></textarea></td>
          </tr>
          <tr>
            <td>Select Remake Facility:</td>
            <td></td>
			<td></td>
          </tr>
          <tr>
            <td>
				<select name="faclity" id="faclity" style="width:100%">
					<?php $fac_name_qry = imw_query("select id, loc_name from in_location where del_status='0' and loc_name!='' order by loc_name asc");
						  while($fac_name_row = imw_fetch_array($fac_name_qry)) { 
						  ?>
					<option value="<?php echo $fac_name_row['id']; ?>" <?php echo ($fac_name_row['id']==$_SESSION['pro_fac_id'])?'selected=selected':'';?>><?php echo $fac_name_row['loc_name']; ?></option>
					<?php } ?>
				</select>
			</td>
			<td></td>
			<td></td>
          </tr>
          <tr>
            <td>
				<label>
					<input type="checkbox" name="remkae_with_charges" id="remkae_with_charges" style="margin:0 0 0 2px;vertical-align: middle;height:16px;width:16px;"/>
					With Charges
				</label>
			</td>
            <td></td>
			<td></td>
          </tr>
          
          <tr>
            <td colspan="3">
            <div id="doc_list" style="display:none">
            <div>Select Doctor</div>
            <div>
            <select id="doc_id" name="doc_id">
            	<option value="">Select Doctor</option>
                <?php
				$q=imw_query("select CONCAT(lname,',',fname,' ', mname) as name, id from users where delete_status=0 and user_type=1 order by name asc");
				while($dlist=imw_fetch_object($q)){
					echo'<option value="'.$dlist->id.'">'.$dlist->name.'</option>';
				}
				?>
            </select>
            </div>
            
            </div>
            <div id="optician_list" style="display:none">
            <div>Select Optician</div>
            <div>
            <select name="optician_id" id="optician_id">
            	<option value="">Select Optician</option>
            </select>
            </div>
            
            </div>
            <div id="lab_list" style="display:none">
            <div>Select Lab</div>
            <div>
            <select name="remake_id" id="remake_id">
            	<option value="">Select Lab</option>
            <?php
            $q=imw_query("select * from in_lens_lab where del_status=0 order by lab_name")or die(imw_error());
            while($dlist=imw_fetch_object($q)){
                echo'<option value="'.$dlist->id.'">'.$dlist->lab_name.'</option>';
            }
            ?>
            </select>
            </div>
            
            </div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td></td>
          </tr>
          </table>
       </div>
       <input type="hidden" name="remake_prac_code_id" id="remake_prac_code_id" value="">
        <div class="btn_cls mt10">
            <input type="button" name="save" value="Save" onClick="proceed();"/>
            <input type="button" name="save" value="Close" onClick="javascript:window.self.close();"/>          
        </div>
        </form>
    </div>
    <script type="text/javascript">
	function proceed()
	{
		if(($("#status_reason").val()==""))
		{
			falert('Please provide reason for remake.');
			return false;
		}
		var win=window.opener.document;
		if( $("#status_reason").val() != '' &&  $("#status_reason").val() != '0' ){
			var reason = $("#status_reason option:selected").text()
		}
		else{
			var reason = $("input#other_reason").val();
		}
		
		window.opener.document.getElementById("remake_reason_id").value=$("#status_reason").val();
		window.opener.document.getElementById("remake_reason").value=reason;
		window.opener.document.getElementById("remake_prac_code").value=$("#prac_code").val();
		window.opener.document.getElementById("remake_prac_code_id").value=$("#remake_prac_code_id").val();
		window.opener.document.getElementById("remake_price").value=$("#remake_price").val();
		window.opener.document.getElementById("remake_comments").value=$("#comment").val();
		
		var remakeCharges = $( '#remkae_with_charges' ).is( ':checked' );
		window.opener.document.getElementById("remake_without_charges").value=(remakeCharges)?0:1;
		
		window.opener.document.getElementById("remake_doctor").value=$("#doc_id").val();
		window.opener.document.getElementById("remake_optician").value=$("#optician_id").val();
		window.opener.document.getElementById("remake_lab").value=$("#remake_id").val();
		window.opener.document.getElementById("remake_fac").value=$("#faclity").val();
		
		
		window.opener.document.getElementById("frm_method").value='remake';
		window.opener.document.addframe.submit();	
		
		window.self.close();
	}
	function getOtherInfo(obj)
	{
		/*Reset Values*/
		$("#doc_id, #optician_id, #remake_id").val('');
		/*End reset values*/
		
		var selected_reason = $("#status_reason option:selected");
		
		var text = $(selected_reason).text();	
		var value = $(selected_reason).attr('prac');
		var prac_id = $(selected_reason).attr('prac_id');
		var price = $(selected_reason).attr('price');
		if(value)
		{
			//var valArr=value.split('-');	
			$("#prac_code").val(value);
			$("#remake_prac_code_id").val(prac_id);
			$("#remake_price").val(price);
		}
		$("#doc_list").hide();
		$("#optician_list").hide();
		$("#lab_list").hide();
		
		if(text.toLowerCase()=='doctor error')
		{
			$("#doc_list").show();	
		}
		else if(text.toLowerCase()=='optician error')
		{
			$("#optician_list").show();	
		}
		else if(text.toLowerCase()=='lab error')
		{
			$("#lab_list").show();	
		}
		else if(text=='Other'){
			$('input#other_reason').val('').show();
			$('img#other_reason_back').show();
			$('select#status_reason').val('0').hide();
		}
	}
	
	function remake_reason_back(){
		$('input#other_reason').val('').hide();
		$('img#other_reason_back').hide();
		$('select#status_reason').val('0').show();
	}
	</script>

</body>
</html>