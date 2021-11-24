<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_diagnosis_admin = "SELECT icd10_desc FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc";
$res_diagnosis_admin = imw_query($qry_diagnosis_admin) or die(imw_error());

$totalRows_diagnosis_admin = imw_num_rows($res_diagnosis_admin);
?>
<script>
function getInnerHTMLdiagnosisAdmin(val){
	//var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('txt_area_pre_op_diagnosis_admin');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}

var tOutAdminTimediagnosis; 
function closeAdmindiagnosis(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('evaluationpre_op_diagnosis_div_admin').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('evaluationpre_op_diagnosis_div_admin').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimediagnosis(){
	tOutAdminTimediagnosis = setTimeout("closeAdmindiagnosis()", 500);
}
function stopClosediagnosisAdmin() {
	clearTimeout(tOutAdminTimediagnosis);
}

</script>
<div id="evaluationpre_op_diagnosis_div_admin" onMouseOver="stopClosediagnosisAdmin();" onMouseOut="closeAdminTimediagnosis();"   style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Pre-Op Diagnosis<span onClick="document.getElementById('evaluationpre_op_diagnosis_div_admin').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
		while ($res_diagnosis_row = imw_fetch_assoc($res_diagnosis_admin)){
			$diagDesc = $res_diagnosis_row['icd10_desc'];
		?>
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLdiagnosisAdmin('<?php echo stripslashes($diagDesc); ?>')"> 
				<?php echo stripslashes($diagDesc); ?>
            </div>
        <?php	
		}
		?>
	</div>
</div>