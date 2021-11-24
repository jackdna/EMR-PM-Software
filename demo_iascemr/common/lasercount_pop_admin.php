<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_count_admin = "select * from laserpredefine_count_tbl order by `name`";
$res_count_admin = imw_query($qry_count_admin) or die(imw_error());

$totalRows_count_admin = imw_num_rows($res_count_admin);
?>
<script>
	
function getInnerHTMLcountAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areacount_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimecount; 
function closeAdmincount(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationcount_div_admin').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationcount_div_admin').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimecount(){
	tOutAdminTimecount = setTimeout("closeAdmincount()", 500);
}
function stopClosecountAdmin() {
	clearTimeout(tOutAdminTimecount);
}

</script>
<div id="evaluationcount_div_admin" onMouseOver="stopClosecountAdmin();" onMouseOut="closeAdminTimecount();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Count<span onClick="document.getElementById('evaluationcount_div_admin').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $count_seq=0;
			 while($res_count_row = imw_fetch_array($res_count_admin)){
				$count_seq++;
				?>
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLcountAdmin('<?php echo stripslashes($res_count_row['name']); ?>')"> 
	                <?php echo stripslashes($res_count_row['name']); ?>
                </div>
				<?php
			}
	?>
	</div>
</div>
