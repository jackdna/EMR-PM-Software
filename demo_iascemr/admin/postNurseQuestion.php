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
<title>Post Op Nurse Question</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

if($_REQUEST['delRecords']=='true'){
$counter=0;
	$chkBoxArray = $_REQUEST['chkBox'];
	
	foreach($chkBoxArray as $postNurseQuestionId){
		$rec_del=$objManageData->delRecord('postopnursequestion', 'postOpNurseQuestionId', $postNurseQuestionId);
		if($rec_del)$counter++;
	}
	if($rec_del)
	{
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
	}
}

$mainCategoryList = $_REQUEST['mainCategoryList'];
if($mainCategoryList=='') {
	$getdefaultCategoryDetails = imw_query("select * from postopnursecategory") or die(imw_error());
	$getdefaultCategoryDetailsRow = imw_fetch_array($getdefaultCategoryDetails);
	$mainCategoryList =$getdefaultCategoryDetailsRow['categoryId'];
}
if($_REQUEST['sbtForm']){
	$postOpNurseCategoryListArr = $_REQUEST['postOpNurseCategoryList'];
	$specialtyIDArr			= $_REQUEST['specialty_id'];
	$nurseQuestionId = $_REQUEST['nurseQuestionId'];
	$postOpNurseQuestionName = $_REQUEST['postOpNurseQuestionName'];	
	if(is_array($postOpNurseQuestionName)){
		foreach($postOpNurseQuestionName as $key => $orderName){
			if($orderName!=''){
				$prevOrderId = $nurseQuestionId[$key];
				if($prevOrderId){
					$arrayUpdateRecord['specialty_id'] = $specialtyIDArr[$key];
					$arrayUpdateRecord['postOpNurseQuestionName'] = addslashes($orderName);
					$arrayUpdateRecord['postOpNurseCatId'] = $postOpNurseCategoryListArr[$key];
					if($postOpNurseCategoryListArr[$key]) {
						$c=$objManageData->UpdateRecord($arrayUpdateRecord, 'postopnursequestion', 'postOpNurseQuestionId', $prevOrderId);
					}
				}else{
					$arrayAddRecord['specialty_id'] = $specialtyIDArr[$key];
					$arrayAddRecord['postOpNurseQuestionName'] = addslashes($orderName);
					$arrayAddRecord['postOpNurseCatId'] = $postOpNurseCategoryListArr[$key];
					if($postOpNurseCategoryListArr[$key]) {
						$d=$objManageData->addRecords($arrayAddRecord, 'postopnursequestion');
					}
				}
			}
		}
	
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}	
	elseif($c)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	
	}
}
?>

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
$(document).ready(function(){
	$(".my_table_Checkall table #checkall").click(function () {
			if ($(".my_table_Checkall #checkall").is(':checked')) {
				$(".my_table_Checkall input[type=checkbox]").each(function () {
					$(this).prop("checked", true);
				});
	
			} else {
				$(".my_table_Checkall input[type=checkbox]").each(function () {
					$(this).prop("checked", false);
				});
			}
		});
	
	$('#manage_modal').on('click',function(){
		$('#mymodalmanagecat').modal({
			show: true,
			backdrop: true,
			keyboard: true
		});
	});
		

});
function add_btn_click() {
	
	$('#my_modal_add_new').modal({
		show: true,
		backdrop: true,
		keyboard: true
	});
}
var LOD_ASC	=	function()
{
		var BH	=	$(window).height() - $(".subtracting-head").outerHeight(true);
		//console.log(' Body ' + BH)
		$("#dataBody").css({ 'height' : BH + 'px', 'max-height': BH+'px','overflow':'hidden','overflow-y':'auto'});
};

$(window).load(function(){ LOD_ASC(); });
$(window).resize(function(){ LOD_ASC(); });
</script>
</head>
<body>
	<form name="postOpNurseQuestionListFrm" action="postNurseQuestion.php" method="post">
	<input type="hidden" name="delRecords" value="">
	<input type="hidden" name="mainCategoryList" value="<?php echo $mainCategoryList;?>">	
	<input type="hidden" name="sbtForm" value="">	
	
  
  <div class="margin_bottom_mid_adjustment scheduler_margins_head">
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
					
                  
                  <div style="" id="" class="all_content1_slider">	         
                  <div class="all_admin_content_agree wrap_inside_admin">      
					<div class="subtracting-head">
                         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                            <span>
                                Post-Op Nurse 
                            </span>
                          </div>
                    </div>        
                      <div class="wrap_inside_admin scrollable_yes " id="dataBody"> <!-- all_admin_content height_adjust_prefer -->
                      
           			   <div class="scheduler_table_Complete">
					
                  			
                  		<div class="container-fluid padding_0">	
                        <div class="my_table_Checkall col-xs-12 col-md-12 col-lg-12 col-sm-12 adj_tp_table padding_0">
                                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                   <thead class="cf">
                                        <tr>
                                        	<th class="text-center"><input type="checkbox"  id="checkall" onClick="return checkAllFn();"> </th>
                                            <!--<th class="text-left">Specialty</th>-->
                                          	<th class="text-left">Question Category</th>
                                            <th class="text-left">Question</th>
                                        </tr>
                                    </thead>
									<tbody>
                                    <?php
									$postNurseDetails = $objManageData->getArrayRecords('postopnursequestion','','','postOpNurseQuestionName','ASC');
									if(count($postNurseDetails)>0){
										foreach($postNurseDetails	as $postNurseQuestion){
											$postOpNurseQuestionId = $postNurseQuestion->postOpNurseQuestionId;
											$postOpNurseSpecialtyId = $postNurseQuestion->specialty_id;
											$postOpNurseQuestionName = stripslashes($postNurseQuestion->postOpNurseQuestionName);
											$postOpNurseCatId = $postNurseQuestion->postOpNurseCatId;
											?>	
                                   	    <tr>
                                        	<td class="text-center"><input type="checkbox" name="chkBox[]" value="<?php echo $postOpNurseQuestionId; ?>">
                                            	<input type="hidden" name="nurseQuestionId[]" value="<?php echo $postOpNurseQuestionId; ?>">
                                           	</td>
                                            <!--
                                            <td align="left">
                                            	<select name="specialty_id[]" class="form-control selectpicker " title="Select Specialty" data-container="body" >
													<?php
                                                        $getSpecialties = $objManageData->getArrayRecords('specialty','' ,'' ,'specialty_name','ASC');
                                                        foreach($getSpecialties as $row)
                                                        {
                                                            if($row->del_status != 'yes' || $postOpNurseSpecialtyId == $row->specialty_id) {	
                                                    ?>
                                                                <option value="<?php echo $row->specialty_id; ?>" <?php if($postOpNurseSpecialtyId==$row->specialty_id) echo "selected"; ?>><?php echo $row->specialty_name; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    ?>
                                    			</select>
                                			</td>
											-->
                                            <td class="text-left low_width_t"> 
                                            <select class="selectpicker" name="postOpNurseCategoryList[]" title="Select Question Category">
											<?php
                                            $getCategoryDetails = $objManageData->getArrayRecords('postopnursecategory','' ,'' ,'categoryName','ASC');
                                            foreach($getCategoryDetails as $cat_desc){?>
                                                <option value="<?php echo $cat_desc->categoryId; ?>" <?php if($postOpNurseCatId==$cat_desc->categoryId) echo "SELECTED"; ?>><?php echo $cat_desc->categoryName; ?></option>
                                                <?php
                                          	  }
												?>
                                            </select> 
                                            </td>
                                            <td class="text-left medium_width_t">
                                            
                                            <input type="text" class="form-control" name="postOpNurseQuestionName[]" value="<?php echo $postOpNurseQuestionName; ?>">
                                            </td>
									   </tr>
                                       <?php 
									   }
									}
										?>
                                    </tbody>
                            </table>
                         </div>                
                      </div>
                    </div>  	
                     </div>
                  </div>
                  </div>  
                    
				  <!-- NEcessary PUSH     -->	 
                  <div class="push"></div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>   
	</div>
    <div class="modal fade " id="my_modal_add_new">
     <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header text-center">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">ADD NEW  </h4>  
            </div>
            <div class="modal-body">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Question Category
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <select name="postOpNurseCategoryList[]" class="selectpicker form-control" title="Select Question Category" >
										<?php
										$getCategoryDetails = $objManageData->getArrayRecords('postopnursecategory','' ,'' ,'categoryName','ASC');
										foreach($getCategoryDetails as $cat_desc){
											?>
											<option value="<?php echo $cat_desc->categoryId; ?>" <?php if($postOpNurseCatId==$cat_desc->categoryId) echo "SELECTED"; ?>><?php echo $cat_desc->categoryName; ?></option>
											<?php
										}
										?>
									</select>
                            </div>
                        </div>
                    </div>
                    </div>
               		 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="ps" class="text-left"> 
                                     Question 
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                 <input name="postOpNurseQuestionName[]" type="text" class="form-control" >
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="clearfix"></Div>
                    <!-- 
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="ps" class="text-left"> 
                                       Specialty
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select name="specialty_id[]" class="form-control selectpicker " title="Select Specialty" >
                                        <?php
                                            $getSpecialties = $objManageData->getArrayRecords('specialty','' ,'' ,'specialty_name','ASC');
                                            foreach($getSpecialties as $row)
                                            {
                                                if($row->del_status != 'yes' ) {	
                                        ?>
                                                    <option value="<?php echo $row->specialty_id; ?>" ><?php echo $row->specialty_name; ?></option>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                
                    -->
                    
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onClick="top.frames[0].document.getElementById('saveButton').click();">  <b class="fa fa-save"></b>  Save </a>
                <a class="btn btn-danger" href="javascript:void(0)" onClick="top.frames[0].document.getElementById('cancelButton').click();" data-dismiss="modal"><b class="fa fa-times"></b>	Cancel  </a>
            </div>
         
        </div>
     </div>
    </div>
</form>
</body>
</html>

