<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
	
include_once("common/conDb.php");
	

	$nurseNotesEditId = $_REQUEST['editId'];

	$newnotesEditQry = "select * from `genanesthesianursesnewnotes` where  newnotes_id = '".$nurseNotesEditId."'";
	$newnotesEditRes = imw_query($newnotesEditQry) or die(imw_error()); 
	$newnotesEditNumRow = imw_num_rows($newnotesEditRes);
	if($newnotesEditNumRow>0) {
		$newnotesEditRow = imw_fetch_array($newnotesEditRes);
		$newnotesEditDesc = $newnotesEditRow['newnotes_desc'];
	}
?>

<div class="row" style="width:350px; padding:0; border:solid 1px #DDD; background:white">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f; color:white;  padding-top:5px; color:#FFF;">
    	NURSE NOTES
        <span onClick="closeNurseNote('<?php echo $nurseNotesEditId;?>');" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="height:70px; padding:5px; text-align:center; width:100%"> 
    	<textarea name="edit_NurseNotesDesc" id="edit_NurseNotesDesc<?php echo $nurseNotesEditId;?>"  style="overflow:hidden;; width:99%; height:60px !important; margin-bottom:5px; border:none; padding: 1%" ><?php echo $newnotesEditDesc; ?></textarea>
        
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="background:white;" > 
    	<a class="btn btn-info" onClick="return edit_newnotes_value('<?php echo $nurseNotesEditId;?>');">Edit Nurse Note</a>	
        
    </div>
    
    
    
    
    
</div>
