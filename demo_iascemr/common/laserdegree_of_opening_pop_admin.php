<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_degree_of_opening_admin = "select * from laserpredefine_degree_opening_tbl  order by `name`";
$res_degree_of_opening_admin = imw_query($qry_degree_of_opening_admin) or die(imw_error());

$totalRows_degree_of_opening_admin = imw_num_rows($res_degree_of_opening_admin);
?>
<script>
	
function getInnerHTMLdegree_of_openingAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areadegree_of_opening_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimedegree_of_opening; 
function closeAdmindegree_of_opening(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationdegree_of_opening_div_admin').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationdegree_of_opening_div_admin').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimedegree_of_opening(){
	tOutAdminTimedegree_of_opening = setTimeout("closeAdmindegree_of_opening()", 500);
}
function stopClosedegree_of_openingAdmin() {
	clearTimeout(tOutAdminTimedegree_of_opening);
}

</script>
<div id="evaluationdegree_of_opening_div_admin" onMouseOver="stopClosedegree_of_openingAdmin();" onMouseOut="closeAdminTimedegree_of_opening();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Degree of opening<span onClick="document.getElementById('evaluationdegree_of_opening_div_admin').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $degree_of_opening_seq=0;
			 while($res_degree_of_opening_row = imw_fetch_array($res_degree_of_opening_admin)){
				$degree_of_opening_seq++;
				?>
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLdegree_of_openingAdmin('<?php echo stripslashes($res_degree_of_opening_row['name']); ?>')"> 
	                <?php echo stripslashes($res_degree_of_opening_row['name']); ?>
                </div>
				<?php
			}
	?>
	</div>
</div>