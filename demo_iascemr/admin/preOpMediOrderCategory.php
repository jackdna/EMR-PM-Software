<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$content = $_REQUEST['contentOf'];
$preDefines = $_REQUEST['preDefines'];
$sbmtFrm = $_REQUEST['sbmtFrm'];
$predefineIds = $_REQUEST['predefineIds'];
$table = $_REQUEST['table'];
$idField = $_REQUEST['idField'];
//DELETE SELECTED RECORDS
$deleteSelected = $_REQUEST['deleteSelected'];
if($deleteSelected){
	$counter=0;
	$chkBox = $_REQUEST['chkBox'];
	foreach($chkBox as $prededines_id){
		$objManageData->delRecord($table, $idField, $prededines_id);
		if($table=='preopmedicationcategory') {//delete its child medication also
			$del_rec=$objManageData->delRecord('preopmedicationorder', 'mediCatId', $prededines_id);
			if($del_rec)$counter++;
		}
	}
	if($del_rec)
	{
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
	}
}
//DELETE SELECTED RECORDS

if($sbmtFrm){
	// ADD UPDATE RECORDS
	foreach($preDefines as $key => $preDefineDesc){
		if($preDefineDesc!=''){
			$preDefineDesc = addslashes($preDefineDesc);
			$predefinedID = $predefineIds[$key];
			// ADD UPDATE RECORDS			
			if($content=='Health Questioner'){
				$arrayRecord['question'] = $preDefineDesc;
			}else if($content=='Medications Category'){
				$arrayRecord['categoryName'] = $preDefineDesc;
			}else{
				$arrayRecord['name'] = $preDefineDesc;
			}			
			if($predefinedID){
				$b=$objManageData->UpdateRecord($arrayRecord, $table, $idField, $predefinedID);
			}else{
				$a=$objManageData->addRecords($arrayRecord, $table);
			}
			// ADD UPDATE RECORDS
		}
	}
	
	if($b)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($a)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
}

//DISPLAY RECORDS
switch($content){
	case 'Medications Category':
		$getDetails = $objManageData->getArrayRecords('preopmedicationcategory');
		$table = 'preopmedicationcategory';
		$idField = 'categoryId';
	break;
	case 'Allergies':
		$getDetails = $objManageData->getArrayRecords('allergies');
		$table = 'allergies';
		$idField = 'allergiesId';
	break;
	
}
//DISPLAY RECORDS
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PreOP Medication Category</title>
<?php include("adminLinkfile.php");?>
<script>
function newProcCat(obj){
	if(obj.value == 'Other'){
		document.getElementById('procCatTd').innerHTML = '<input type="text" class="text_10" name="preDefineProcCat" size="30">';
	}
}

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
	<form name="frmPreOpMediOrderCategory" action="preOpMediOrderCategory.php" method="post" class="alignCenter">
		<input type="hidden" name="sbmtFrm" value="true">
		<input type="hidden" name="contentOf" value="<?php echo $content; ?>">
		<input type="hidden" name="deleteSelected" value="">
		<input type="hidden" name="table" value="<?php echo $table; ?>">
		<input type="hidden" name="idField" value="<?php echo $idField; ?>">
        
       <Div class="all_admin_content_agree wrap_inside_admin">      
     <Div class="subtracting-head">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
                Medications Category 
            </span>
          </div>
            
    </Div>
        
     <Div class="wrap_inside_admin scrollable_yes">
       <div class="scheduler_table_Complete ">
        <div class="my_table_Checkall adj_tp_table">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                   <!-- <thead class="cf">
                        <tr>
                            <th class="text-center"><input type="checkbox"  id="checkall"></th>
                            <th class="text-left">Medication Category </th>
                            <th class="text-left">Medication Order Name</th>
                            <th class="text-left">Strength</th>
                        </tr>
                    </thead>-->
                    <tbody>
                    <tr>
							<?php
								if(count($getDetails)>0){
									foreach($getDetails as $key => $detailsPreDefine){
										if($content=='Medications Category'){
											$preDefineDesc = stripslashes($detailsPreDefine->categoryName);
											$preDefineID = $detailsPreDefine->categoryId;
										}
										if($content=='Allergies'){
											$preDefineDesc = $detailsPreDefine->name;
											$preDefineID = $detailsPreDefine->allergiesId;
										}
										
										++$tr;
										?>
														
										<td style="text-align:center"><input type="hidden" value="<?php echo $preDefineID; ?>" name="predefineIds[]"><input type="checkbox" name="chkBox[]" value="<?php echo $preDefineID; ?>"></td>
										<td style="text-align:left"><input type="text"  class="form-control" name="preDefines[]" value="<?php echo $preDefineDesc; ?>"></td>
									<?php
										if($tr>1){
											$tr = 0;
											echo '</tr><tr class="valignTop" style="height:25px;">';
										}
									}
									if((count($getDetails)%2) != 0  && count($getDetails)!=1) { echo '<td colspan="4"></td>';}
								}
								?>
								</tr>
                    </tbody>
            </table>
         </div>                
      </div>	
     </Div>
      </Div>
      
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
                        <?php
						for($i=0;$i<1;$i++)
						{
							++$new;
							?>
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="ps" class="text-left"> 
                                          Category Name
                                    </label>
                                </div>
                                
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <input type="text" name="preDefines[]"  <?php if($content=='Diagnosis'){ ?> style="font-weight:lighter;color:#CCCCCC;" onBlur=" if(this.value==''){  this.value = 'Dx.code, Description'; this.style.color = '#CCCCCC'; }" onFocus="if(this.value=='Dx.code, Description'){ this.value = ''; this.style.color = '#000000'; }" <?php } ?> value="<?php if($content=='Diagnosis') echo 'Dx.code, Description'; ?>" class="form-control">
                                </div>
                            </div>
                         <?php }?>
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
			
	</form>
	<?php
}
?>
</body>
</html>
