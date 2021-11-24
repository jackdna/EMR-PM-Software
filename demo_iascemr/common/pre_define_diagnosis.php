<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_diagnosis = "SELECT icd10_desc FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc";
$rsNotes = imw_query($qry_diagnosis) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);

?>
<script>
function getInnerHTMLdiag(obj){
	var  val = obj;
	var obj2 = document.getElementById('perop_diag_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('postop_diag_area_id')){	
		document.getElementById('postop_diag_area_id').style.backgroundColor = '#FFFFFF';
	}
	textAreaAdjust(obj2);
}
</script>
<div id="evaluationPreDiagnosisDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDiagnosisDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4">
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Pre-Op Diagnosis<span onClick="document.getElementById('evaluationPreDiagnosisDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
		<?php
		while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$diagDesc = $row_rsNotes['icd10_desc'];
		?>
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLdiag('<?php echo stripslashes($diagDesc); ?>')"> 
				<?php echo stripslashes($diagDesc); ?>
            </div>
        <?php	
		}
		?>
	</div>
</div>