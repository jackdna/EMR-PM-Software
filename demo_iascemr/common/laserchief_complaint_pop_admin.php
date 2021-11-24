<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_chiefcomplaint_admin = "select * from laserpredefine_chiefcomplaint_tbl order by `name`";
$res_chiefcomplaint_admin = imw_query($qry_chiefcomplaint_admin) or die(imw_error());

$totalRows_chiefcomplaint_admin = imw_num_rows($res_chiefcomplaint_admin);
?>
<script>
	
function getInnerHTMLchiefcomplaintAdmin(obj){
	var  val = obj;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areachief_complaint_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimechiefcomplant; 
function closeAdminchiefcomplaint(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationchief_complaint_div').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationchief_complaint_div').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimecheifcomplaint(){
	tOutAdminTimechiefcomplant = setTimeout("closeAdminchiefcomplaint()", 500);
}
function stopClosechiefcomplaintAdmin() {
	clearTimeout(tOutAdminTimechiefcomplant);
}

</script>
<style>
.hoverdiv:hover{background:#FFFFFF;
color:#06C;}
</style>
<div id="evaluationchief_complaint_div" onMouseOver="stopClosechiefcomplaintAdmin();" onMouseOut="closeAdminTimecheifcomplaint();" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Chief Complaints<span onClick="document.getElementById('evaluationchief_complaint_div').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $chiefcomplaint_seq=0;
			 while($res_complaint_row = imw_fetch_array($res_chiefcomplaint_admin)){
				$chiefcomplaint_seq++;
				?>
				
				<!--<tr style="cursor:pointer; height:25px;" id="Chief_tr<?php echo $chiefcomplaint_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_chiefcomplaint_admin; ?>','Chief_tr');" >
					<td colspan="3" class="text_10 alignLeft" style=" width:100%;padding-left:2px; cursor:pointer;" id="tdChief<?php echo $res_complaint_row['chiefcomplaintID']; ?>" onClick="return getInnerHTMLchiefcomplaintAdmin(this)"><?php echo stripslashes($res_complaint_row['name']).''; ?></td>
					</tr>-->
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLchiefcomplaintAdmin('<?php echo stripslashes($res_complaint_row['name']); ?>')">
	                <?php echo stripslashes($res_complaint_row['name']); ?>
                </div>
				<?php
				
			}
	?>
	</div>
</div>