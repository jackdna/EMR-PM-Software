<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_post_progress_admin = "select * from laserpredefine_postprogressnotes_tbl order by `name`";
$res_post_progress_admin = imw_query($qry_post_progress_admin) or die(imw_error());

$totalRows_post_progress_admin = imw_num_rows($res_post_progress_admin);
?>
<script>
	
function getInnerHTMLpost_progressAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areapost_progress_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimepost_progress; 
function closeAdminpost_progress(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationpost_progress_div_admin').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationpost_progress_div_admin').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimepost_progress(){
	tOutAdminTimepost_progress = setTimeout("closeAdminpost_progress()", 500);
}
function stopClosepost_progressAdmin() {
	clearTimeout(tOutAdminTimepost_progress);
}

</script>
<div id="evaluationpost_progress_div_admin" onMouseOver="stopClosepost_progressAdmin();" onMouseOut="closeAdminTimepost_progress();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Post-Op Orders<span onClick="document.getElementById('evaluationpost_progress_div_admin').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $post_progress_seq=0;
			 while($res_post_progress_row = imw_fetch_array($res_post_progress_admin)){
				$post_progress_seq++;
				?>
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLpost_progressAdmin('<?php echo stripslashes($res_post_progress_row['name']); ?>')"> 
	                <?php echo stripslashes($res_post_progress_row['name']); ?>
                </div>
				<?php
			}
	?>
	</div>
</div>