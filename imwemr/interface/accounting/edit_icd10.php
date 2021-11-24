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
include_once("../main/Functions.php");
$patient_id = $_SESSION['patient'];
$operatorId = $_SESSION['authId'];
$operatorName = $_SESSION['authUser'];
$transactionDate = date('Y-m-d');
$operator_id = $_SESSION['authId'];
$entered_date = date('Y-m-d H:i:s');
$objManageData = new ManageData;
$get_lat=imw_query("select * from icd10_laterality where deleted='0' order by under asc,id asc");
while($row_lat=imw_fetch_array($get_lat)){
	// Laterality = 1 or 2
	// Severity   = 3
	// Staging    = 4 or 5
	$lat_arr[$row_lat['under']][$row_lat['code']]=$row_lat['abbr'];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit ICD-10</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="../themes/default/common.css" type="text/css">
<style>

label[for]{ cursor:pointer; text-align:left; border:2px solid transparent;margin-right:5px;padding:2px;} 

input[type=checkbox]{  display:none; }

input[type=checkbox]:checked + label { background-color:white; font-size:12px; font-weight:bold; color:Red; border:2px solid #1569C7;}

table.elems_tbl td{ text-align:left; height:32px; width:42px;}
.line_bg_col{background-color:#e1edfb;}
.line_bg_wht_col{background-color:#ffffff;}
.right_brd{border-right:2px solid #c9302c;}
</style>
<!-- including javascript function file common to provider module-->
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
function get_dx_code(){
	$('.opt_cls').css({"display":"none"});
	var j=0;
	for(var i=1;i<=12;i++){
		var diagText="diagText_"+i;
		var edit_diagText="edit_diagText_"+i;
		var span_edit_diagText="span_edit_diagText_"+i;
		var tr_dx="tr_dx_"+i;
		var lit_dx="lit_diagText_"+i;
		
		var opener_dxcode=window.opener.top.fmain.all_data2.document.getElementById(diagText).value;
		var opener_lit=window.opener.top.fmain.all_data2.document.getElementById(lit_dx).value;
		document.getElementById(edit_diagText).value=opener_dxcode;
		document.getElementById(span_edit_diagText).innerHTML=opener_dxcode;
		if(opener_dxcode.substr((opener_dxcode.length-1),1)=='-'){
			var opener_lit_arr = opener_lit.split(',');
			if(opener_lit_arr[0]>0 || opener_lit_arr[1]>0 || opener_lit_arr[2]>0){
				j++;
				for(var k=0;k<=2;k++){
					if(opener_lit_arr[k]>0){
						$('#opt'+opener_lit_arr[k]+'_'+i).css({"display":"inline"});
					}
				}
				if(j%2){
					document.getElementById('td_dx_1_'+i).className = "input_text_10 line_bg_col right_brd";
				}else{
					document.getElementById('td_dx_1_'+i).className = "input_text_10 line_bg_wht_col right_brd";
					document.getElementById('td_dx_2_'+i).className = "input_text_10 line_bg_wht_col";
					document.getElementById('td_dx_3_'+i).className = "input_text_10 line_bg_wht_col";
					document.getElementById('td_dx_4_'+i).className = "input_text_10 line_bg_wht_col";
				}
			}
		}else{
			document.getElementById(tr_dx).style.display='none';
		}
	}
}
function set_icd10(){
	for(var i=1;i<=12;i++){
		var diagText="diagText_"+i;
		var dx_lat_css="dx_lat_css_"+i;
		var old_diagText="old_diagText_"+i;
		var span_dx_id="span_edit_diagText_"+i;
		var opener_dxcode=window.opener.top.fmain.all_data2.document.getElementById(diagText).value;
		window.opener.top.fmain.all_data2.document.getElementById(old_diagText).value= opener_dxcode;
		if(document.getElementById(span_dx_id)){
			var span_dx_id_val=document.getElementById(span_dx_id).innerHTML;
			if(span_dx_id_val!=""){
				if(span_dx_id_val.substr((span_dx_id_val.length-1),1)!='-'){
			   		window.opener.top.fmain.all_data2.document.getElementById(diagText).value=span_dx_id_val;
			   		window.opener.top.fmain.all_data2.document.getElementById(diagText).className = "input_text_10 dx_box_12";
				}
		   }
		}
	}
	window.opener.top.fmain.all_data2.crt_dx_dropdown_icd10();
	window.close();
}
function uncheck(obj){ 
	var id = $(obj).parents("td[id]").attr("id"); 
	var tr_id = $(obj).parents("tr[id]").attr("id");
	$("#"+id+" :checked").each(function(){ if(this.id!=obj.id){ this.checked=false; }  });
	
	var dx_id="edit_diagText_"+tr_id.replace("tr_dx_","");
	var span_dx_id="span_edit_diagText_"+tr_id.replace("tr_dx_","");
	var dxcode_val=document.getElementById(dx_id).value;
	var td_dx_id="td_dx_1_"+tr_id.replace("tr_dx_","");
	var sel_val =obj.value;
	var sel_id =obj.id;
	/*var dxcode_val_arr = dxcode_val.split('-');
	if(typeof dxcode_val_arr[2]!="undefined"){
		var dx_inner_val=document.getElementById(span_dx_id).innerHTML;
		var new_opener_dxcode = dxcode_val_arr[0]+dxcode_val_arr[1]+dxcode_val_arr[2];
		document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
		$('#'+td_dx_id).removeClass("right_brd");
	}else{
		if(dxcode_val.substr((dxcode_val.length-1),1)=='-'){
			var new_opener_dxcode=dxcode_val.substr(0,(dxcode_val.length-1))+sel_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
			$('#'+td_dx_id).removeClass("right_brd");
		}
	}*/
	if(dxcode_val.substr((dxcode_val.length-2),2)=='--'){
		if(sel_id.substr((sel_id),9)=='elem_lat3'){
			var dx_inner_val=document.getElementById(span_dx_id).innerHTML;
			var new_opener_dxcode=dx_inner_val.substr(0,(dx_inner_val.length-1))+sel_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
		}else{
			var dx_inner_val=document.getElementById(span_dx_id).innerHTML;
			var last_val=dx_inner_val.substr((dx_inner_val.length-1),1);
			var new_opener_dxcode=dx_inner_val.substr(0,(dx_inner_val.length-2))+sel_val+last_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
		}
		$('#'+td_dx_id).removeClass("right_brd");
	}else if(dxcode_val.substr((dxcode_val.length-3),3)=='-X-' || dxcode_val.substr((dxcode_val.length-3),3)=='-x-'){
		if(sel_id.substr((sel_id),9)=='elem_lat3'){
			var dx_inner_val=document.getElementById(span_dx_id).innerHTML;
			var new_opener_dxcode=dx_inner_val.substr(0,(dx_inner_val.length-1))+sel_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
		}else{
			var dx_inner_val=document.getElementById(span_dx_id).innerHTML;
			var last_val=dx_inner_val.substr((dx_inner_val.length-2),2);
			var new_opener_dxcode=dx_inner_val.substr(0,(dx_inner_val.length-3))+sel_val+last_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
		}
		$('#'+td_dx_id).removeClass("right_brd");
	}else{
		if(dxcode_val.substr((dxcode_val.length-1),1)=='-'){
			var new_opener_dxcode=dxcode_val.substr(0,(dxcode_val.length-1))+sel_val;
			document.getElementById(span_dx_id).innerHTML=new_opener_dxcode;
			$('#'+td_dx_id).removeClass("right_brd");
		}
	}
}
	
$(document).ready(function () { 
	$("input[type=checkbox]").bind("click", function(){ uncheck(this); });
});
</script>
</head>
<body class="body_c" style="background:#ffffff;">
      
	<table class="table_separate" cellspacing="1" style="background-color:#acacac;">
    	<tr>
            <td style="width:100%; text-align:left; height:25px;" class="section_header text_b" colspan="5">
                &nbsp;Edit ICD-10
            </td>
        </tr>
        <tr>
        	<td class="text_b_w" style="text-align:center; width:110px; padding-left:10px; height:30px; background-color:#333;">
            	#
            </td>
        	<td class="text_b_w" style="text-align:center; width:85px; background-color:#333;">
            	ICD-10
            </td>
            <td class="text_b_w" style="text-align:center; width:225px; background-color:#333;">
            	Site
            </td>
            <td class="text_b_w" style="text-align:center; width:225px; background-color:#333;">
            	Staging
            </td>
            <td class="text_b_w" style="text-align:center; width:225px; background-color:#333;">
            	Severity
            </td>
        </tr>
        <?php
			for($d=1;$d<=12;$d++){
		?>
        <tr id="tr_dx_<?php echo $d;?>" class="border3" style="height:37px;">
        	<td class="input_text_10" style="padding-left:7px; background-color:#e8e7e7;">
            	<span style="padding-right:10px; padding-left:10px;">&#8226;</span><strong>DX <?php echo $d;?></strong>
            </td>
        	<td class="input_text_10 line_bg_col" style="padding-left:10px;" id="td_dx_1_<?php echo $d;?>">
            	<input id="edit_diagText_<?php echo $d; ?>"  type="hidden" value="" name="edit_diagText_<?php echo $d; ?>" class="input_text_10" style="width:95px;">
           		<span id="span_edit_diagText_<?php echo $d; ?>"></span>
            </td>
            <td class="input_text_10 line_bg_col" style="padding-left:10px;" id="td_dx_2_<?php echo $d;?>">
             	<div id="opt1_<?php echo $d;?>" class="opt_cls">
			    	<table class="elems_tbl">
                    	<tr>
                        	<?php foreach($lat_arr[1] as $k=>$v){?>
                                <td style="border:none;">
                                    <input type="checkbox" id="elem_lat1<?php echo $d.$k;?>" name="elem_lat" value="<?php echo $k; ?>" class="dx_lat_css_<?php echo $d;?>"><label for="elem_lat1<?php echo $d.$k;?>"><?php echo $v; ?></label>
                                </td>
                            <?php } ?>
                        </tr>
                     </table>	
                 </div>
                 <div id="opt2_<?php echo $d;?>" class="opt_cls">
			    	<table class="elems_tbl">
                    	<tr>
                        	<?php foreach($lat_arr[2] as $k=>$v){?>
                                <td style="border:none;">
                                    <input type="checkbox" id="elem_lat2<?php echo $d.$k;?>" name="elem_lat" value="<?php echo $k; ?>" class="dx_lat_css_<?php echo $d;?>"><label for="elem_lat2<?php echo $d.$k;?>" ><?php echo $v; ?></label>
                                </td>
                            <?php } ?>
                        </tr>
                     </table>	
                 </div>
            </td>
             <td class="input_text_10 line_bg_col" style="padding-left:10px;" id="td_dx_4_<?php echo $d;?>">
                <div id="opt4_<?php echo $d;?>" class="opt_cls">
			    	<table class="elems_tbl">
                    	<tr>
                        	<?php foreach($lat_arr[4] as $k=>$v){?>
                                <td style="border:none;">
                                    <input type="checkbox" id="elem_lat4<?php echo $d.$k;?>" name="elem_lat" value="<?php echo $k; ?>" class="dx_lat_css_<?php echo $d;?>"><label for="elem_lat4<?php echo $d.$k;?>" ><?php echo $v; ?></label>
                                </td>
                            <?php } ?>
                        </tr>
                     </table>	
                 </div>
                  <div id="opt5_<?php echo $d;?>" class="opt_cls">
			    	<table class="elems_tbl">
                    	<tr>
                        	<?php foreach($lat_arr[5] as $k=>$v){?>
                                <td style="border:none;">
                                    <input type="checkbox" id="elem_lat5<?php echo $d.$k;?>" name="elem_lat" value="<?php echo $k; ?>" class="dx_lat_css_<?php echo $d;?>"><label for="elem_lat5<?php echo $d.$k;?>" ><?php echo $v; ?></label>
                                </td>
                            <?php } ?>
                        </tr>
                     </table>	
                 </div>
            </td>
             <td class="input_text_10 line_bg_col" style="padding-left:10px;" id="td_dx_3_<?php echo $d;?>">
                <div id="opt3_<?php echo $d;?>" class="opt_cls">
			    	<table class="elems_tbl">
                    	<tr>
                        	<?php foreach($lat_arr[3] as $k=>$v){?>
                                <td style="border:none;">
                                    <input type="checkbox" id="elem_lat3<?php echo $d.$k;?>" name="elem_lat" value="<?php echo $k; ?>" class="dx_lat_css_<?php echo $d;?>"><label for="elem_lat3<?php echo $d.$k;?>" ><?php echo $v; ?></label>
                                </td>
                            <?php } ?>
                        </tr>
                     </table>	
                 </div>
            </td>
        </tr>
        <?php } ?>
	</table>
    <table class="table_collapse">
    	<tr><td style="padding-top:5px;">&nbsp;</td></tr>
        <tr>
            <td style="text-align:center;" class="text_10b">
                <input type="submit" name="applySubmitBtn" value="Done" class="dff_button" id="applySubmitBtn" onClick="set_icd10();" />
                &nbsp;&nbsp;
                <input type="button" name="cancel" value="Cancel" class="dff_button" id="cancel" onClick="window.close();" />
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">get_dx_code();</script>
</body>
</html>