<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

$content 			= $_REQUEST['contentOf'];
$preDefines 	= $_REQUEST['preDefines'];
$sbmtFrm 		= $_REQUEST['sbmtFrm'];
$predefineIds	= $_REQUEST['predefineIds'];
$table 				= $_REQUEST['table'];
$idField 			= $_REQUEST['idField'];

//DELETE SELECTED RECORDS
$deleteSelected = $_REQUEST['deleteSelected'];
if($deleteSelected){
	$counter=0;
	$chkBox = $_REQUEST['chkBox'];
	foreach($chkBox as $prededines_id)
	{
		$objManageData->delRecord($table, $idField, $prededines_id);
		
		if($content=='Pre-Op Nurse Question Category' )
			$rec_del=$objManageData->delRecord('preopnursequestion', 'preOpNurseCatId', $prededines_id);
		
		else if($content=='Post-Op Nurse Question Category')
			$rec_del=$objManageData->delRecord('postopnursequestion', 'postOpNurseCatId', $prededines_id);
		
		if($rec_del)$counter++;
	
	}
	
	if($rec_del)
	{
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
	}
}
//DELETE SELECTED RECORDS


$categoryAlreadyExist='';
if($sbmtFrm)
{
		// ADD UPDATE RECORDS
		foreach($preDefines as $key => $preDefineDesc)
		{
			if($preDefineDesc!='')
			{
				$preDefineDesc = addslashes($preDefineDesc);
				$predefinedID = $predefineIds[$key];
				// ADD UPDATE RECORDS			
				if($content=='Pre-Op Nurse Question Category' || $content=='Post-Op Nurse Question Category'){
					$updateField	=	'categoryName';
				}
				else{
					$updateField	=	'name';
				}			
				$arrayRecord[$updateField] = trim($preDefineDesc);
				$chkCategoryNameDetails = $objManageData->getRowRecord($table, $updateField, $preDefineDesc);
				
				if($chkCategoryNameDetails)
				{
					if($predefinedID){
						//DO NOT SHOW ALERT, ALSO DO NOT UPDATE/ADD RECORD
					}else {
						$categoryAlreadyExist = 'Yes'; //SHOW ALERT -- SEE AT THE BOTTOM(ALERT SCRIPT)
					}
				}
				else
				{
					if($predefinedID){
						$c=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedID);
					}else{
						$d=$objManageData->addRecords($arrayRecord, $table);
					}
				}	
		
		}
}
	
	if($c)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
}


//DISPLAY RECORDS
switch($content)
{
	case 'Pre-Op Nurse Question Category':
		$getDetails	= $objManageData->getArrayRecords('preopnursecategory','','','categoryName','ASC');
		$table 			= 'preopnursecategory';
		$idField 		= 'categoryId';
		$title				=	'Pre-Op Nurse Question Category' ;
		break;
	case 'Post-Op Nurse Question Category':
		$getDetails	=	$objManageData->getArrayRecords('postopnursecategory','','','categoryName','ASC');
		$table 			=	'postopnursecategory';
		$idField		=	'categoryId';
		$title				=	'Post-Op Nurse Question Category' ;
		break;
}
//DISPLAY RECORDS


?>
<!DOCTYPE html>
<html>
<head>
<title><?=$title?></title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script>
function add_btn_click() {
	
	$('#my_modal_add_new').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});
}
</script>

</head>
<body>
<?php
if($content){
		?>
		<form name="frmPreOpnurseQuestionCategory" action="nurseQuestionCategory.php" method="post">
			<input type="hidden" name="sbmtFrm" value="true">
			<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
			<input type="hidden" name="deleteSelected" value="">
			<input type="hidden" name="table" value="<?php echo $table; ?>">
			<input type="hidden" name="idField" value="<?php echo $idField; ?>">
			

    <div class="all_admin_content_agree wrap_inside_admin">      
     <div class="subtracting-head">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
               <label><?=$title?></label>
            </span>
          </div>
    </div>
        
     <div class="wrap_inside_admin scrollable_yes">
       <div class="scheduler_table_Complete ">
        <div class="my_table_Checkall adj_tp_table">
             <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
            	<tbody>
                    <tr>
			<?php
                if(count($getDetails)>0){
					$q=0;
					foreach($getDetails as $key => $detailsPreDefine){
						$q++;
						if($content=='Pre-Op Nurse Question Category' || $content=='Post-Op Nurse Question Category'){
							$preDefineDesc = stripslashes($detailsPreDefine->categoryName);
							$preDefineID = $detailsPreDefine->categoryId;
						}
						
						++$tr;
						?>

                        <td class="text-center"><input type="hidden" value="<?php echo $preDefineID; ?>" name="predefineIds[]">
                        	<input type="checkbox" name="chkBox[]" value="<?php echo $preDefineID; ?>"></td>
                        <td><input type="text" size="60" class="form-control" name="preDefines[]" value="<?php echo $preDefineDesc; ?>"></td>
                        
                      <?php if($tr>1 && count($getDetails)!=$q){
								$tr = 0;
								echo '</tr><tr class="valignTop" style=" height:25px;">';
							}
						}
						if((count($getDetails)%2) != 0  && count($getDetails)!=1) { echo '<td colspan="2"></td>';}
	                }
    	            ?>
						</tr>
                    </tbody>
            </table>	
         </div>                
      </div>	
     </div>
      </div>
      <div class="modal fade" id="my_modal_add_new">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">ADD NEW </h4>  
            </div>
            <div class="modal-body">
                	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                        
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                      Category Name
                                </label>
                            </div>
                            
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <input class="form-control" type="text" name="preDefines[]">
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onClick="top.frames[0].document.getElementById('saveButton').click();">  <b class="fa fa-save"></b>  Save </a>
                <a class="btn btn-danger" href="javascript:void(0)" onClick="top.frames[0].document.getElementById('cancelButton').click();" data-dismiss="modal"><b class="fa fa-times"></b>	Cancel  </a>
            </div>
         
        </div>
     </div>
    </div>
	<?php
}

if($categoryAlreadyExist == 'Yes') {
?>
	<script>alert('Category already exist');</script>
<?php
}
$categoryAlreadyExist ='';
?>
	</form>
</body>
</html>
