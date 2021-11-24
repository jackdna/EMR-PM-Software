<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
set_time_limit(900);
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

if($_REQUEST['delRecords']=='true'){
	$counter=0;
	$chkBoxArray = $_REQUEST['chkBox'];
	/*
	//CHECK RECORD IN SURGEON'S PROFILE
		$chkBoxArrayImplode = implode(',',$chkBoxArray);
		
		$medicationNameQry = "select * from preopmedicationorder where preOpMedicationOrderId  in($chkBoxArrayImplode)";
		$medicationNameRes = imw_query($medicationNameQry) or die(imw_error()); 
		$medicationNameNumRow = imw_num_rows($medicationNameRes);
		if($medicationNameNumRow>0) {
			while($medicationNameRow = imw_fetch_array($medicationNameRes)) {
				$medicationMainName[] = $medicationNameRow["medicationName"];
			}
			$medicationMainNameImplode = implode("%' || preOpOrders like '%",$medicationMainName);
			
		}
		echo $checkArrayQry = "select * from surgeonprofile where preOpOrders like '%$medicationMainNameImplode%'";
		$checkArrayRes = imw_query($checkArrayQry);
		$checkArrayNumRow = imw_num_rows($checkArrayRes);
		if($checkArrayNumRow>0) {
		?>
		<script>
			if(confirm("Selected Medicine is already used in Surgeon's profile\nAre you sure to delete selected records")) {
				//alert('deleted');
			}
		</script>
		<?php
		}
		
	//END CHECK RECORD IN SURGEON'S PROFILE
	*/
	foreach($chkBoxArray as $preMediOrderId){
		$del_rec=$objManageData->delRecord('preopmedicationorder', 'preOpMedicationOrderId', $preMediOrderId);
		if($del_rec)$counter++;
	}
	if($del_rec)
	{
		echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
	}
}

$mainCategoryList = $_REQUEST['mainCategoryList'];
if($mainCategoryList=='') {
	$getdefaultCategoryDetails = imw_query("select * from preopmedicationcategory LIMIT 0,1") or die(imw_error());
	$getdefaultCategoryDetailsRow = imw_fetch_array($getdefaultCategoryDetails);
	$mainCategoryList =$getdefaultCategoryDetailsRow['categoryId'];
}
if($_REQUEST['sbtForm']){
	$procCategoryListArr = $_REQUEST['procCategoryList'];
	$medicationOrderId = $_REQUEST['medicationOrderId'];
	$medicationOrder = $_REQUEST['medicationOrder'];	
	$strength = $_REQUEST['strength'];
	$direction = $_REQUEST['direction'];
	if(is_array($medicationOrder)){
		foreach($medicationOrder as $key => $orderName){
			if($orderName!=''){
				$prevOrderId = $medicationOrderId[$key];
				if($prevOrderId){
					$arrayUpdateRecord['medicationName'] = addslashes($orderName);
					$arrayUpdateRecord['strength'] = addslashes($strength[$key]);
					$arrayUpdateRecord['directions'] = addslashes($direction[$key]);
					$arrayUpdateRecord['mediCatId'] = $procCategoryListArr[$key];
					$b = $objManageData->UpdateRecord($arrayUpdateRecord, 'preopmedicationorder', 'preOpMedicationOrderId', $prevOrderId);
				}else{
					$arrayAddRecord['medicationName'] = addslashes($orderName);
					$arrayAddRecord['strength'] = addslashes($strength[$key]);
					$arrayAddRecord['directions'] = addslashes($direction[$key]);
					$arrayAddRecord['mediCatId'] = $procCategoryListArr[$key];
					$a=$objManageData->addRecords($arrayAddRecord, 'preopmedicationorder');						
				}
			}
		}
		
		if($b && !$a)
		{	//echo $b;
			echo "<script>top.frames[0].alert_msg('update')</script>";
		}
		if($a)
		{
			echo "<script>top.frames[0].alert_msg('success')</script>";
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Pre-Op Medication Order</title>
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

$(window).load(function()
{
	var LDL	=	function()
	{
		/*var H	=	parent.top.$("#div_middle").height()- top.frames[0].$("#div_innr_btn").outerHeight();
		H=H-($(".subtracting-head").height()+10);
		var height_custom_scroll_new=	top.frames[0].frames[0].$('.scrollable_yes');
		height_custom_scroll_new.css({ 'min-height' : H , 'max-height': H});*/
		
		var WH	=	$(window).height();
		var SH		=	$(".subtracting-head").outerHeight(true);
		var H		=	WH - SH ;
		
		var height_custom_scroll_new=	top.frames[0].frames[0].$('.scrollable_yes');
		height_custom_scroll_new.css({ 'min-height' : H , 'max-height': H});
		
	}
	LDL();
	$(window).resize(function(e) {
	   LDL();
	});
});

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

</script>
</head>
<body>
	<form name="preOpMediOrderListFrm" action="preOpMediOrder.php" method="post" style="margin:0px;" class="alignCenter">
	<input type="hidden" name="delRecords" value="">
	<input type="hidden" name="mainCategoryList" value="<?php echo $mainCategoryList;?>">	
	<input type="hidden" name="sbtForm" value="">



    <Div class="all_admin_content_agree wrap_inside_admin">     
 <Div class="subtracting-head">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
                Pre-Op Med 
            </span>
          </div>
            <div class="msg msg-clear text-center"> 
                    <div class="slider-wrap-s">
                        <div class="tab-slider">
                                 <div class="bot-links text-center">
                          			<?php
									$clkStylDefault = '';
									if($mainCategoryList=='0') { 
										$clkStylDefault = ' text-decoration:none;color:#fff; background:#333; -webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; ';		
									}
									?>
                                    <a style=" <?php echo $clkStylDefault; ?>"  href="#" onClick="javascript:document.preOpMediOrderListFrm.mainCategoryList.value='<?php echo '0'; ?>';document.preOpMediOrderListFrm.submit();"><?php echo 'Default'; ?></a>
                                    
                                    <?php
									$getMainCategoryDetails = $objManageData->getArrayRecords('preopmedicationcategory','','','categoryName', 'ASC');
									foreach($getMainCategoryDetails as $catMain_desc){
										$clkStyl = '';
										if($mainCategoryList==$catMain_desc->categoryId) {
											$clkStyl = ' text-decoration:none;color:#fff; background:#333; -webkit-border-radius: 20px;-moz-border-radius: 20px;border-radius: 20px; ';		
										}
									?>	
                                        <a style=" <?php echo $clkStyl; ?>"  href="#" onClick="javascript:document.preOpMediOrderListFrm.mainCategoryList.value='<?php echo $catMain_desc->categoryId; ?>';document.preOpMediOrderListFrm.submit();"><?php echo $catMain_desc->categoryName; ?></a>
                                    <?php
									}
									?>    
                                </div>

                                                                                       
                        </div>

                   </div> 
              
              </div>
    </Div>
        
     <Div class="wrap_inside_admin scrollable_yes" >
       
       <div class="scheduler_table_Complete ">
    
            
    
        <div class="my_table_Checkall adj_tp_table">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                    <thead class="cf">
                        <tr>
                            <th class="text-center"><input type="checkbox"  id="checkall"></th>
                            <th class="text-left">Medication Category </th>
                            <th class="text-left">Medication Order Name</th>
                            <th class="text-left">Strength</th>
                            <th "text-left">Directions</th>
                        </tr>
                    </thead>
                    <Tbody>
                    <?php 
					if($mainCategoryList=='0') {$mainCategoryList=' 0'; }
                    $preMediDetails = $objManageData->getArrayRecords('preopmedicationorder','mediCatId',$mainCategoryList,'medicationName','ASC');
                    if(count($preMediDetails)>0){
                        foreach($preMediDetails	as $preMediOrder){
                            $preOpMedicationOrderId = $preMediOrder->preOpMedicationOrderId;
                            $medicationName = stripslashes($preMediOrder->medicationName);
                            $strength = stripslashes($preMediOrder->strength);
                            $directions = stripslashes($preMediOrder->directions);
                            $mediCatId = $preMediOrder->mediCatId;
					?>
                            <tr>
                                <Td class="text-center"><input type="checkbox" name="chkBox[]" value="<?php echo $preOpMedicationOrderId; ?>">	
                                </Td>
                                <Td class="text-left low_width_t" >
                                <input type="hidden" name="medicationOrderId[]" value="<?php echo $preOpMedicationOrderId; ?>">
                                <select name="procCategoryList[]" class="selectpicker" data-container="body" >
                                    <option>Default</option>
                                    <?php
                                    $getCategoryDetails = $objManageData->getArrayRecords('preopmedicationcategory','' ,'' ,'categoryName','ASC');
                                    foreach($getCategoryDetails as $cat_desc){
                                        ?>
                                        <option value="<?php echo $cat_desc->categoryId; ?>" <?php if($mediCatId==$cat_desc->categoryId) echo "SELECTED"; ?>><?php echo $cat_desc->categoryName; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                </Td>
                                <Td class="text-lef medium_width_t"><input type="text" class="form-control" name="medicationOrder[]" value="<?php echo $medicationName; ?>" /></Td>
                                <Td class="text-left low_width_t"> <input type="text" class="form-control" name="strength[]" value="<?php echo $strength; ?>" /></Td>
                                <Td class="text-left high_width_t"><input type="text" class="form-control" name="direction[]" value="<?php echo $directions; ?>" /></Td>
                            </tr>
                    <?php		
						}
					}
                    ?>							
                    
                    
                    </Tbody>
            </table>
         </div>                
      
      </div>	
     </Div>
      </Div>	        
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
                                         Medication Category 
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <select name="procCategoryList[]" class="selectpicker" >
                                        <option>Default</option>
                                        <?php
                                        $getCategoryDetails = $objManageData->getArrayRecords('preopmedicationcategory','' ,'' ,'categoryName','ASC');
                                        foreach($getCategoryDetails as $cat_desc){
                                            ?>
                                            <option value="<?php echo $cat_desc->categoryId; ?>"><?php echo $cat_desc->categoryName; ?></option>
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
                                    <label for="ps2" class="text-left"> 
                                         Medication 
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <input name="medicationOrder[]" type="text" class="form-control" >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                                    <Div class="clearfix"></Div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="ps3" class="text-left"> 
                                         Strength 
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <input name="strength[]" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>                 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="ps4" class="text-left"> 
                                         Directions 
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                     <input name="direction[]" type="text" class="form-control">
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
    
</form>
</body>
</html>

