<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Consent Category</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("adminLinkfile.php");?>
<script>
function checkAllFn(){
	var obj = document.getElementsByName("chkBox[]");
	var len = obj.length;
	for(i=0; i<len; i++){
		if(obj[i].checked==true){
			obj[i].checked = false;
		}else{
			obj[i].checked = true;
		}
	}
}
</script>
<script>
	top.frames[0].document.getElementById('addNew').style.display = 'inline-block';
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'none';	
	top.frames[0].document.getElementById('formsButton').style.display = 'inline-block';	
</script>
</head>
<body>
<?php
$flag=0;
include_once("classObjectFunction.php");
$objManageData = new manageData;
$CategoryList='';
$CategoryList = $_REQUEST['CategoryList'];
$delete_category = $_REQUEST['delete_category'];
$insert_category = $_REQUEST['insert_category'];
$categoryId = $_REQUEST['categoryId'];
$existingCategory = $_REQUEST['category_name_update'];
//Insert new category
if($_REQUEST['form_submit']=="true")
{

	//START UPDATE CATEGORY
	if($existingCategory && $delete_category!="true") {
		foreach($existingCategory as $extKey=> $existingCategoryName) {
			unset($arrayUpdateRecord);
			$arrayUpdateRecord['category_name'] = addslashes($existingCategoryName);
			$de=$objManageData->UpdateRecord($arrayUpdateRecord, 'consent_category', 'category_id', $categoryId[$extKey]);
		}
		if($de)
		{
			echo "<script>top.frames[0].alert_msg('update')</script>";
		}
	}
	//END UPDATE CATEGORY

//Insert new category
	if(trim($_REQUEST['category_name'])!='')
	{
		unset($arrayRecord);
		$arrayRecord['category_name'] = addslashes($_REQUEST['category_name']);
		$c=$objManageData->addRecords($arrayRecord, 'consent_category');
		if($c && !$de)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
	}
	
//delete new category
	if($delete_category=="true")
	{
		$flag=1;
		$chkBoxSub = $_REQUEST['chkBoxSub'];
		//echo $chkBoxSub;
		$counter=0;
		foreach($chkBoxSub as $chkBoxSub_id)
		{
			unset($arrayRecord);
			$arrayRecord['category_status'] = 'true';
			$rec_del=$objManageData->UpdateRecord($arrayRecord, 'consent_category', 'category_id', $chkBoxSub_id);
			if($rec_del)$counter++;
			
			//check consent form for that category to delete 
			//unset($arrayRecord1);
			$templateDelete = $objManageData->getArrayRecords('consent_forms_template', 'consent_category_id', $chkBoxSub_id,'consent_id','DESC');
			if(count($templateDelete)==0)
			{}
			else
			{
				foreach($templateDelete	as $Category_consentDelete)
				{
					 $del_consent_id= $Category_consentDelete->consent_id;
					// echo "<br>kk = ".$del_consent_id;
					
					 $sub_update="update consent_forms_template SET consent_delete_status= 'true' where consent_category_id='$chkBoxSub_id'";
					 $result1=imw_query($sub_update)or die("Cannot be updated ...!".imw_error());
				}
			}
		}
		if($rec_del)
		{
			echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
			//$flag=1;
		}
	}
}

if($mainCategoryList=='') {
	$getdefaultCategoryDetails = imw_query("select * from consent_category where category_status!='true' ") or die(imw_error());
	$getdefaultCategoryDetailsRow = imw_fetch_array($getdefaultCategoryDetails);
	$mainCategoryList =$getdefaultCategoryDetailsRow['categoryId'];
}

//echo $_REQUEST['category_name'];

?>
<form name="frmcategory" action="consent_category.php" method="post">
<Div class="all_admin_content_agree wrap_inside_admin">
    <Div class="subtracting-head" id="surgeon-header">
        <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>Consent Category</span>
        </div>
    </Div>
    <Div class="wrap_inside_admin  adj_tp_table">
        <div class="scheduler_table_Complete">
            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf table-striped">
            <tr>
            <?php $var_limit=1;
                  $categoryDetail = $objManageData->getArrayRecords('consent_category ','category_status',' ','category_name','ASC');
                  if(count($categoryDetail)>0){
                    foreach($categoryDetail	as $categoryDetail1){
                        $category_id = $categoryDetail1->category_id;
                        $category_name = $categoryDetail1->category_name;
                        $category_status = ($categoryDetail1->category_status);
            ?>			<td style="width:2%; text-align:center">
                            <input type="hidden" value="<?php echo $category_id; ?>" name="categoryId[]">
                            <input type="checkbox" name="chkBoxSub[]" value="<?php echo $category_id; ?>">
                        </td>
                        <td style="width:48%"><input type="text" class="form-control" name="category_name_update[]" value="<?php echo ucfirst($category_name); ?>"></td>
                        
            <?php		
                        //if(count($categoryDetail)%2 != 0 && count($categoryDetail)==$var_limit) {
                         //   echo "<td colspan='2'></td>";	
                        //}
                        if($var_limit%2==0){
                            echo "</tr><tr>";
                        }
                        $var_limit++;
                    }
                    
                 }
            ?>
            </tr>
            </table>
           
        </div>
    </Div>
</Div>   
 <div class="modal fade" id="newCategory">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob"> Add New Category </h4>  
            </div>
            <div class="modal-body">
               <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12">
				<tr >
					<td style="padding:0px 10px">
						<input type="text" name="category_name" value="" class="form-control">
					
                    	<input type="hidden" name="delete_category" value="">
						<input type="hidden" name="insert_category" value="">
						<input type="hidden" name="form_submit" value="true">
						<input type="hidden" name="CategoryList" value="<?php echo $CategoryList;?>">	
					</td>
				</tr>
				<tr>
					<td style="padding:0px 10px"><i>Category Name</i></td>
				</tr>
			</table> 
            </div>
            <div class="modal-footer">
            <a href="javascript:void(0)" class="btn btn-success " id="saveButton" style="display: inline-block;" onclick="return top.frames[0].getPageSrc('Save');"><b class="fa fa-save"></b>	Save  </a>
            <a href="javascript:void(0)" class="btn btn-danger " id="cancelButton" style="display: inline-block;"  data-dismiss="modal"><b class="fa fa-times"></b>	Cancel</a>
            </div>
             
         </div>
     </div>
 </div>
</form>
</body>
</html>