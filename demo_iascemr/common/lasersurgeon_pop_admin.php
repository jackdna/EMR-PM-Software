<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_surgeon_admin = "select * from users where user_type='Surgeon' order by `lname` ASC";
$res_surgeon_admin = imw_query($qry_surgeon_admin) or die(imw_error());

$totalRows_surgeon_admin = imw_num_rows($res_surgeon_admin);
?>
<script>
	
function getInnerHTMLsurgeonAdmin(obj){
	var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areasurgeon');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimesurgeon; 
function closeAdminsurgeon(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationsurgeon_div').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationsurgeon_div').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimesurgeon(){
	tOutAdminTimesurgeon = setTimeout("closeAdminsurgeon()", 500);
}
function stopClosesurgeonAdmin() {
	clearTimeout(tOutAdminTimesurgeon);
}

</script>
<div id="evaluationsurgeon_div" onMouseOver="stopClosesurgeonAdmin();" onMouseOut="closeAdminTimesurgeon();"   style="position:absolute;background-color:#E0E0E0;width:280px;height:120px;display:none;overflow:auto;"> 

 <!--<div id="evaluationsurgeon_div"    style="position:absolute;background-color:#E0E0E0;width:250px;height:100px;display:none;overflow:auto;">  -->
	<table class="table_collapse" style="border:none;">
		
        <tr>
            <td class="alignRight" style=" background-color:#BCD2B0;"><img src="../images/left.gif" style=" width:3px; height:24px;"></td>
            <td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="../images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('evaluationsurgeon_div').style.display='none';"></td>
            <td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="../images/right.gif" style=" width:3px; height:24px; "></td>
        </tr>
        
		<tr style="cursor:pointer;height:25px;" id="surgeon_tr<?php echo "all_surgeon"; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_surgeon_admin; ?>','surgeon_tr');" >
			<td colspan="3" class="text_10" style="width:100%;padding-left:2px;"  id="tdsurgeon<?php echo "all_surgeon"; ?>" onClick="return getInnerHTMLsurgeonAdmin(this)"><?php echo "All Surgeons"; ?></td>
		</tr>
		<?php
			 $surgeon_seq=0;
			 while($res_surgeon_row = imw_fetch_array($res_surgeon_admin)){
				$surgeon_seq++;
				?>
                <tr style="cursor:pointer; height:25px;" id="surgeon_tr<?php echo $surgeon_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_surgeon_admin; ?>','surgeon_tr');">
                    <td colspan="3" class="text_10" style=" width:100%;padding-left:2px;text-align:left; cursor:pointer;" onClick="return getInnerHTMLsurgeonAdmin(this)"><?php echo stripslashes(ucfirst($res_surgeon_row['fname'])).' '.stripslashes(ucfirst($res_surgeon_row['lname'])).''; ?></td>
                </tr>
				<?php
			}
	?>
	</table>

</div>