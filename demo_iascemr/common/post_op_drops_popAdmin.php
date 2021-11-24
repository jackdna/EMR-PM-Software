<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_opdrops = "select * from postopdrops order by `name`";
$rsNotes = imw_query($qry_opdrops) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLOpDropsAdmin(obj){
	var  val = obj.innerHTML;
	//var obj1 = top.frames[0].frames[1].document.getElementById('perop_diag_area_id');
	var obj2 = top.frames[0].frames[0].document.getElementById('postOpDropId');
	if(!obj2 )
	{
		obj2 = top.frames[0].frames[0].frames[0].document.getElementById('postOpDropId');		
	}
	//var len = obj1.length;
	
	//for(i=0; i<len; i++){		
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
			
			
		
	
} 
</script>

<div class="modal fade " id="evaluationPostOpDropsAdminDiv">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">Post-Op Drops  </h4>  
            </div>
            <div class="modal-body">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
	
	<?php
	//$getRecordSetStr = "SELECT * FROM evaluation";
	//$getRecordSetQry = imw_query($getRecordSetStr);
	$rows = 5; //imw_num_rows($getRecordSetQry);
	//while($getRecordSetRows = imw_fetch_array($getRecordSetQry)){
		$drops_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$drops_seq++;
			?>
			<tr style="height:25px; cursor:hand;" id="DropsAdmin_tr<?php echo $drops_seq;//$seq; ?>">
				<td colspan="3" style="padding-left:2px; cursor:pointer" onClick="return getInnerHTMLOpDropsAdmin(this)"><?php echo stripslashes($row_rsNotes['name']).'';// $getRecordSetRows['name']; ?></td>
			</tr>
			<?php
		}
	//}
?>
</table>
             </div>
            
         
        </div>
     </div>
    </div>
    
