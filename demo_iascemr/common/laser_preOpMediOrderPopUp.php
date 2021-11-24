<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_category = "select * from preopmedicationcategory order by `categoryName`";

$rsNotes = imw_query($qry_category) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLpro(objvalue,objstrength,objdirections,mediId,mediCatID){
	
	var objectCounter	= top.frames[0].frames[0].frames[0].document.getElementById("counter").value ;
	var objectName 		= 'preOpOrdMed_med' ;
	var objSecondary 	= 'preOpOrdMed_sgt' ;
	var objtertiary 			= 'preOpOrdMed_dir' ;
	var objmediID 		= 'preOpOrdMed_id';
	var objmediCatID	= 'preOpOrdMed_cat' ;
	
	if(!isNaN(objectCounter)){
		for(i=1;i<=objectCounter; i++){
			if(top.frames[0].frames[0].frames[0].document.getElementById(objectName+i)){
				if(top.frames[0].frames[0].frames[0].document.getElementById(objectName+i).value==''){
					var str = objvalue;		
					if(str.indexOf(" - ")!=-1){
							posStr1 = str.indexOf(" - ");
							var str1 = str.substr(0, posStr1);							
							var posStr2 = posStr1+3;
							var str2 = str.substr(posStr2);
					}else{
						posStr1 = str.indexOf("  ");
							var str1 = str.substr(0, posStr1);							
							var posStr2 = "";
							var str2 = "";
					}
					if(str1!=''){ var str = str1; }
					
					top.frames[0].frames[0].frames[0].document.getElementById(objectName+i).value = str;		
					top.frames[0].frames[0].frames[0].document.getElementById(objSecondary+i).value = objstrength;					
					top.frames[0].frames[0].frames[0].document.getElementById(objtertiary+i).value = objdirections;					
					top.frames[0].frames[0].frames[0].document.getElementById(objmediID+i).value = mediId;
					top.frames[0].frames[0].frames[0].document.getElementById(objmediCatID+i).value = mediCatID;
					
					break;					
				}
			}
		}	
	}
	else
	{
		top.frames[0].frames[0].frames[0].document.getElementById(objectName+i).value = str;		
		top.frames[0].frames[0].frames[0].document.getElementById(objSecondary+i).value = objstrength;					
		top.frames[0].frames[0].frames[0].document.getElementById(objtertiary+i).value = objdirections;					
		top.frames[0].frames[0].frames[0].document.getElementById(objmediID+i).value = mediId;
		top.frames[0].frames[0].frames[0].document.getElementById(objmediCatID+i).value = mediCatID;																			
		
	}	
	
}

function opnClose(subCatId) {
	//alert(document.getElementById(subCatId).style.display); 
	if(document.getElementById(subCatId).style.display=='none') {
		document.getElementById(subCatId).style.display='block';
	}else {
		document.getElementById(subCatId).style.display='none';
	}
} 
var tOutAdminTimePreOpMed ; 
function closeAdminPreOpMed(){
//alert(top.frames[0].frames[0].document.getElementById("hiddPreDefineId"));
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].frames[0].document.getElementById('laserPreOpMediOrderDiv').style.display == "block"){
			top.frames[0].frames[0].frames[0].document.getElementById('laserPreOpMediOrderDiv').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimePreOpMed(){
	tOutAdminTimePreOpMed = setTimeout("closeAdminPreOpMed()", 500);
}
function stopClosePreOpMed() {
	clearTimeout(tOutAdminTimePreOpMed);
}



</script>

<div id="laserPreOpMediOrderDiv" onMouseOver="stopClosePreOpMed();" onMouseOut="closeAdminTimePreOpMed();" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; border:0px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"  >
		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Pre Op Medication Orders<span onClick="document.getElementById('laserPreOpMediOrderDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
        
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 	
        
        		<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped " style="padding:0;" >
	
	<?php
	$rows = 5; 
		$procedure_seq=0;
		$incr=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$incr++;
			?>
			<tr style="height:25px;" id="tr<?php echo $row_rsNotes['categoryId'];//$seq; ?>">
					<td style="padding-left:2px;cursor:pointer;" colspan="3" class="text_10b alignLeft" onClick="opnClose('subcatId<?php echo $incr;?>');" ><?php echo stripslashes($row_rsNotes['categoryName']).'';// $getRecordSetRows['name']; ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<table id="subcatId<?php echo $incr;?>" style="display:none; width:100%" >
						<?php
						 echo subCategory($row_rsNotes['categoryId']);
						?>
					</table>
				</td>
			</tr>
			<?php 
		}
	function subCategory($id)
	{
		  $qry_procedure = "select * from preopmedicationorder where mediCatId =$id order by medicationName";
			$sub_category = imw_query($qry_procedure) or die(imw_error());
			$total_row = imw_num_rows($sub_category);
			
	?>
	<?php		
			while($cat_fetch = imw_fetch_array($sub_category)){
				//echo $procedure_seq;
				 $procedure_seq++;
			?>
				<tr style="height:25px; cursor:pointer;" id="procedure<?php echo $id;?>_tr<?php echo $procedure_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $total_row; ?>','procedure<?php echo $id;?>_tr')">
						<td style="padding-left:2px; display:inline-block;" class="nowrap text_10" onClick="return getInnerHTMLpro('<?php echo $cat_fetch['medicationName'];?>','<?php echo $cat_fetch['strength'];?>','<?php echo $cat_fetch['directions'];?>','<?php echo $cat_fetch['preOpMedicationOrderId'];?>','<?php echo $cat_fetch['mediCatId'];?>')">
							<?php echo stripslashes($cat_fetch['medicationName']).''; ?>  <?php echo stripslashes($cat_fetch['strength']).''; ?>  <?php echo stripslashes($cat_fetch['directions']).''; ?>
						</td>
				</tr>
	<?php 	
				
			}	
	?>
	
	<?php		
	}
?>
</table>

        </div>
</div>