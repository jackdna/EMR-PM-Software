<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_listfood_admin = "select * from fooddrinkslist order by `name`";
$res_listfood_admin = imw_query($qry_listfood_admin) or die(imw_error());

$totalRows_listfood_admin = imw_num_rows($res_listfood_admin);
?>
<script>
	
function getInnerHTMLlistfoodAdmin(obj){
	var  val = obj;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txtarea_list_food_take');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimelistfood; 
function closeAdminlistfood(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationlistfood_div').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationlistfood_div').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimelistfood(){
	tOutAdminTimelistfood = setTimeout("closeAdminlistfood()", 500);
}
function stopCloselistfoodAdmin() {
	clearTimeout(tOutAdminTimelistfood);
}

</script>
<style>
.hoverdiv:hover{background:#FFFFFF;
color:#06C;}
</style>
<div id="listContent_food_taken" onMouseOver="stopCloseAdminPopup();" onMouseOut="closeAdminPopup(this.id);" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">List Food Taken<span onClick="document.getElementById('listContent_food_taken').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $listfood_seq=0;
			 while($res_listfood_row = imw_fetch_array($res_listfood_admin)){
				$listfood_seq++;
				?>
				
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLlistfoodAdmin('<?php echo stripslashes($res_listfood_row['name']); ?>')">
	                <?php echo stripslashes($res_listfood_row['name']); ?>
                </div>
				<?php
				
			}
	?>
	</div>
</div>