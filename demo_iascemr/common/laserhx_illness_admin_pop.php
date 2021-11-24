<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_hx_illness_admin = "select * from laserpredefine_hx_present_illness_tbl  order by `name`";
$res_hx_illness_admin = imw_query($qry_hx_illness_admin) or die(imw_error());

$totalRows_hx_illness_admin = imw_num_rows($res_hx_illness_admin);
?>
<script>
	
function getInnerHTMLhx_illnessAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areahx_illness');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimehx_illness; 
function closeAdminhx_illness(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationhx_illness_div').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationhx_illness_div').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimehx_illness(){
	tOutAdminTimehx_illness = setTimeout("closeAdminhx_illness()", 500);
}
function stopClosehx_illnessAdmin() {
	clearTimeout(tOutAdminTimehx_illness);
}

</script>
<div id="evaluationhx_illness_div" onMouseOver="stopClosehx_illnessAdmin();" onMouseOut="closeAdminTimehx_illness();" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Hx. of Present Illness<span onClick="document.getElementById('evaluationhx_illness_div').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $hx_illness_seq=0;
			 while($res_hx_illness_row = imw_fetch_array($res_hx_illness_admin)){
				$hx_illness_seq++;
				?>
				
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLhx_illnessAdmin('<?php echo stripslashes($res_hx_illness_row['name']); ?>')"> 
	                <?php echo stripslashes($res_hx_illness_row['name']); ?>
                </div>
				<?php
				
			}
	?>
	</div>
</div>

