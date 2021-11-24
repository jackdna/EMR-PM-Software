<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_past_med_hx_admin = "select * from laserpredefine_past_medical_hx_tbl order by `name`";
$res_past_med_hx_admin = imw_query($qry_past_med_hx_admin) or die(imw_error());

$totalRows_past_med_hx_admin = imw_num_rows($res_past_med_hx_admin);
?>
<script>
	
function getInnerHTMLpast_med_hxAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_areapast_med_hx');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimepast_med_hx; 
function closeAdminpast_med_hx(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationpast_med_hx_div').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationpast_med_hx_div').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimepast_med_hx(){
	tOutAdminTimepast_med_hx = setTimeout("closeAdminpast_med_hx()", 500);
}
function stopClosepast_med_hxAdmin() {
	clearTimeout(tOutAdminTimepast_med_hx);
}

</script>
<div id="evaluationpast_med_hx_div" onMouseOver="stopClosepast_med_hxAdmin();" onMouseOut="closeAdminTimepast_med_hx();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Past Med. Hx<span onClick="document.getElementById('evaluationpast_med_hx_div').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
			 $past_med_hx_seq=0;
			 while($res_past_med_hx_row = imw_fetch_array($res_past_med_hx_admin)){
				$past_med_hx_seq++;
				?>
				
    	        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLpast_med_hxAdmin('<?php echo stripslashes($res_past_med_hx_row['name']); ?>')"> 
	                <?php echo stripslashes($res_past_med_hx_row['name']); ?>
                </div>
				<?php
				
			}
	?>
	</div>
</div>
