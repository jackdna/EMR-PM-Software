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
require_once("../../../config/globals.php");


$scriptMsg = '';

if((int)$_REQUEST['ocular'] == 0){
	$_REQUEST['ocular'] = 0;
}
$txt_editid = $_REQUEST['txt_editid'];
$providerid = $authUserID;

if($_GET['txt_editid'] != ''){
	$qry_edit = "Select * from medicine_data where id='$txt_editid' and del_status = '0' order by id"; 
	$res_edit = imw_query($qry_edit);
	$row_edit = imw_fetch_assoc($res_edit);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>imwemr</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="../../../themes/default/common.css">

<link rel="stylesheet" href="../.././chart_notes/css/simpleMenu.css" type="text/css" />
<script type="text/javascript" src="../../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../../js/common.js"></script>
<script type="text/javascript" src="../../../admin/menuIncludes_menu/js/disableKeyBackspace.js"></script>	
<script type="text/javascript" src="../../../chart_notes/js/simpleMenu.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/main/javascript/actb.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/interface/main/javascript/common.js"></script>
<script type="text/javascript" src="../../../common/script_function.js"></script>
<?php echo $scriptMsg;?>
<script type="text/javascript">
	$(document).ready(function(){
		parent.parent.parent.show_loading_image('none');
		$('#dg_scroll1').val(parent.$('#div_medData').scrollTop());
	});
	function myTrim(str)
	{
		str = str.replace(/^\s+|\s+$/, '');
		return str;
	}

	function show(){
		if(document.getElementById("add1").style.display=="none"){
			document.getElementById("add1").style.display="block";
			document.getElementById("add2").style.display="none";			
			document.getElementById("add4").style.display="none";
			document.getElementById("add5").style.display="block";			
			document.getElementById("tit").innerHTML="Add Record";		
		}else{
			document.getElementById("add1").style.display="none";
			document.getElementById('add2').style.display='block';			
			document.getElementById("add4").style.display="block";
			document.getElementById("add5").style.display="none";
			document.getElementById("tit").innerHTML="";
		}
	}
	
	function chkform(){
		parent.parent.parent.show_loading_image('block');
		var objFrm = document.admin_medication;
		var objMedicineName = objFrm.medicine_name;
				
		if(objMedicineName.value == "")
		{
		   objMedicineName.className="mandatory";
			fAlert("Please Enter Medicine Name.");
			parent.parent.parent.show_loading_image('none');
			objMedicineName.focus();
			return false;
		}
		else{
			objMedicineName.value = myTrim(objMedicineName.value)
			document.admin_medication.submit();
		    return true;
		}
	}
	
	function frm_cancel(){	
		parent.parent.parent.show_loading_image('none');			
		var element; 		
		for (var i = 0; i < document.admin_medication.elements.length; i++) { 
			element = document.admin_medication.elements[i]; 		
			//alert(element.type);
			switch (element.type) { 
				case 'text': 				
					element.value = "";
				break; 
				case 'textarea': 				
					element.value = "";
				break; 
			}
		}
	}
	function enableDisable(chkID, txtBox){
		if(dgi(chkID).checked==true){
			dgi(txtBox).disabled=false;
		}else{
			dgi(txtBox).disabled=true;
		}
	}
function check_umls(obj){
	medName = top.trim(obj.value);
	if(medName!=""){
		top.show_loading_image('block');
		$('#fdb_content',window.parent.document).html('');
		$.ajax({
				type: "POST",
				url: top.WRP+"/interface/Medical_history/medications/check_umls.php?medName="+encodeURI(medName),
				complete: function(r){
					response = r.responseText;
					if(response != null && typeof(response)!='undefined' && response!=''){
						$("#div_disable",window.parent.document).css("display", "block"); 
						$('#umls_content',window.parent.document).html(response);
						$('#div_umls',window.parent.document).show();
					}else{
						$('#div_umls',window.parent.document).hide();
						$("#div_disable",window.parent.document).css("display", "none"); 
					}
					top.show_loading_image('none');
				}
			});
	}
}
		
</script>
</head>
<body class="body_c">
<form method="post" name="admin_medication" id="admin_medication" action="index.php" target="_parent" onSubmit="return chkform();">
	<input type="hidden" name="txt_editid" id="txt_editid" value="<?php echo $_REQUEST['txt_editid'];?>">
	<input type="hidden" name="txt_sbmt" id="txt_sbmt" value="Save">
	<input type="hidden" name="dg_scroll1" id="dg_scroll1" value="0">
	<table class="table_collapse section">
		<tr>
			<td colspan="12" class="section_header">Medication : 
			<?php if($_REQUEST['txt_editid']<>""){ echo 'Edit'; } else {  echo 'Add'; } ?> 
				Record
			</td>
		</tr>
		<tr id="add1">
			<td style="text-align:left;">
				<input type="text" onBlur="changeClass(this);check_umls(this);" onFocus="changeBackground('phraseTable');" name="medicine_name" id="medicine_name" value="<?php echo $row_edit['medicine_name'];?>" class="input_text_10" >
				<div class="label">Name</div>
			</td>
			<td style="text-align:left;">
				<input type="checkbox" name="ocular" id="ocular" value="1" <?php if($row_edit['ocular'] == 1){ echo "checked"; } ?> />
				<div class="label">Oc</div>
			</td>
			<td style="text-align:left;">
				<input type="checkbox" name="glucoma" id="glucoma" value="1" <?php if($row_edit['glucoma'] == 1){ echo "checked"; } ?> />
				<div class="label">Gl</div>
			</td>
            <td style="text-align:left;">
				<input type="checkbox" name="ret_injection" id="ret_injection" value="1" <?php if($row_edit['ret_injection'] == 1){ echo "checked"; } ?> />
				<div class="label">Ret Inj.</div>
			</td>
			<td style="text-align:left;">
				<textarea style="height:15px;width:110px;" onFocus="changeBackground('phraseTable');" name="alias" id="alias" value="" class="input_text_10" size=""><?php echo $row_edit['alias'];?></textarea>
				<div class="label">Alias</div>
			</td>
			<td style="text-align:left;">
				<input type="text" onFocus="changeBackground('phraseTable');" name="recall_code" id="recall_code" value="<?php echo $row_edit['recall_code'];?>" class="input_text_10" size="14">
				<div class="label">Recall Code</div>
			</td>
			<td style="text-align:left;">
				<input type="text" onFocus="changeBackground('phraseTable');" name="procedure" id="procedure" value="<?php echo $row_edit['procedure'];?>" class="input_text_10" size="14">
				<div class="label">Procedure</div>
			</td>
			<td style="text-align:left;">
				<textarea style="height:15px; width:200px;" onFocus="changeBackground('phraseTable');" name="description" id="description" class="input_text_10" cols="35"><?php echo $row_edit['description'];?></textarea>
				<div class="label">Description</div>
			</td>
            <td style="text-align:left;">
          <?php 	$chked ='';	$disabled ='';
		   			if($row_edit['prescription'] == 1){ $chked="checked"; } ?>
          		<input type="checkbox" name="prescription" id="prescription" value="1" <?php echo $chked;?>  />
				<div class="label">Rx Req.</div>
			</td>
		    <td style="text-align:left;">
          <?php 	$chked ='';	$disabled ='';
		   			if($row_edit['alert'] == 1){ $chked="checked"; }else{ $disabled='disabled'; } ?>
          		<input type="checkbox" name="alert" id="alert" value="1" <?php echo $chked;?>  onClick="javascript:enableDisable(this.id,'alertmsg');" />
				<div class="label">Alert</div>
			</td>
			<td>
		          <textarea type="text" onBlur="changeClass(this)" onFocus="changeBackground('phraseTable');" <?php echo $disabled;?> name="alertmsg" id="alertmsg" class="input_text_10" rows="1" style="width:168px;"><?php echo $row_edit['alertmsg'];?></textarea>
				<div class="label">Alert Message</div>
			</td>
            <td style="text-align:left;">
				<input type="text" name="ccda_code" id="ccda_code" value="<?php echo $row_edit['ccda_code'];?>" class="input_text_10" size="14">
				<div class="label">RxNorm Code</div>
			</td>
             <td style="text-align:left;">
				<input type="text" name="fdb_id" id="fdb_id" value="<?php echo $row_edit['fdb_id'];?>" class="input_text_10" size="14">
				<div class="label">FDB Id</div>
			</td>
	  </tr>
	</table>
</form>
</body>
</html>