<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
/*
$qry_diagnosis = "select * from diagnosis_tbl where del_status!='yes' order by diag_code ";
$rsNotes = imw_query($qry_diagnosis) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
*/
$qry_diagnosis = "SELECT icd10_desc FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc";
$rsNotes = imw_query($qry_diagnosis) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);

?>
<script>
function getInnerHTMLPostdiag(obj){
	var  val = obj;
	var obj2 = document.getElementById('postop_diag_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('perop_diag_area_id')){
		document.getElementById('perop_diag_area_id').style.backgroundColor = '#FFFFFF';
	}
	textAreaAdjust(obj2);	
}
</script>
<div id="evaluationPostDiagnosisDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPostDiagnosisDiv');"  style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4">

	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Post-Op Diagnosis<span onClick="document.getElementById('evaluationPostDiagnosisDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
	<?php
	/*
	$rows = 5;
		$diag_seq=0;
		//START CODE TO SHOW ALIAS FIRST
		$row_rsNotesAliasQry = "select * from diagnosis_tbl where del_status!='yes' order by diag_alias";
		$rsNotesAliasRes = imw_query($row_rsNotesAliasQry) or die(imw_error());
		$totalRows_rsNotesAlias = imw_num_rows($rsNotesAliasRes);
		 
		 while ($row_rsNotesAlias = imw_fetch_assoc($rsNotesAliasRes)){
			$diagAlias = $row_rsNotesAlias['diag_alias'];
			if($diagAlias) {
				$diag_seq++;
			?>
			<!--<tr style="cursor:pointer; height:25px;" id="postDiag_tr<?php echo $diag_seq;?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotesAlias; ?>','postDiag_tr')">
				<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLPostdiag(this)"><?php echo stripslashes($diagAlias).''; ?></td>
			</tr>-->
                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLPostdiag('<?php echo stripslashes($diagAlias); ?>')"> 
	                <?php echo stripslashes($diagAlias); ?>
                </div>
			<?php
			}
		}
		//END CODE TO SHOW ALIAS FIRST
		 $diagDescArray = array();
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$diagCode = $row_rsNotes['diag_code'];
			if($diagCode) {
				$searchComma = strstr($diagCode, ',');
				if($searchComma) {
					$diagDescExplode =  explode(",",$diagCode);
					$diagDescArray[] = $diagDescExplode[1];
				}else {
					$diagDescArray[] = $diagCode;
				}	
			}
		}
		asort($diagDescArray);
		foreach($diagDescArray as $diagDesc) {
			$diag_seq++;
		?>
			<!--<tr style="cursor:pointer; height:25px;" id="postDiag_tr<?php echo $diag_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','postDiag_tr')">
				<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLPostdiag(this)"><?php echo stripslashes($diagDesc).'';// $getRecordSetRows['name']; ?></td>
			</tr>-->
             <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLPostdiag('<?php echo stripslashes($diagDesc); ?>')"> 
	                <?php echo stripslashes($diagDesc); ?>
                </div>
        <?php		
		}
		*/
?>
		<?php
		while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$diagDesc = $row_rsNotes['icd10_desc'];
		?>
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLPostdiag('<?php echo stripslashes($diagDesc); ?>')"> 
				<?php echo stripslashes($diagDesc); ?>
            </div>
        <?php	
		}
		?>
	</div>
</div>