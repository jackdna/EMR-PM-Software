<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_shots_admin = "select * from laserpredefine_shots_tbl  order by `name`";
$res_shots_admin = imw_query($qry_shots_admin) or die(imw_error());

$totalRows_shots_admin = imw_num_rows($res_shots_admin);
?>
<script>
	
function getInnerHTMLshotsAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areashots_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimeshots; 
function closeAdminshots(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationshots_div_admin').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationshots_div_admin').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimeshots(){
	tOutAdminTimeshots = setTimeout("closeAdminshots()", 500);
}
function stopCloseshotsAdmin() {
	clearTimeout(tOutAdminTimeshots);
}

</script>
<div id="evaluationshots_div_admin" onMouseOver="stopCloseshotsAdmin();" onMouseOut="closeAdminTimeshots();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;"># of Shots<span onClick="document.getElementById('evaluationshots_div_admin').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $shots_seq=0;
			 while($res_shots_row = imw_fetch_array($res_shots_admin)){
				$shots_seq++;
				?>
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLshotsAdmin('<?php echo stripslashes($res_shots_row['name']); ?>')"> 
	                <?php echo stripslashes($res_shots_row['name']); ?>
                </div>
				<?php
			}
	?>
	</div>
</div>