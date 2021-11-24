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
<title>Laser Procedure</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?php include("adminLinkfile.php");?>
<script>

	function move_templete(obj){
		var val_id=obj;
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
		top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('backButton').style.display = 'none';
		document.frmLaserProcedure.submit();
		top.frames[0].document.frameSrc.source.value = 'laser_procedure_templete.php';
	}

</script>
</head>
<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$laser_ProcedureId = $_REQUEST['laser_ProcedureId'];
?>
		<form name="frmLaserProcedure" action="laser_procedure_admin.php" method="post">
          <div id=""></div>
          <div class="margin_bottom_mid_adjustment scheduler_margins_head" id="procedureLabelId">
            <div class="container-fluid padding_0">
                <div class="inner_surg_middle ">
                        
                      
                      <div style="" id="" class="all_content1_slider">	         
                      <div class="wrap_inside_admin">      
                        <div class="subtracting-head">
                             <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                <span>
                                    Laser
                                </span>
                              </div>
                                
                        </div>        
                          <div class="wrap_inside_admin "> <!-- all_admin_content height_adjust_prefer -->
                          
                           <div class="form_outer custom_surgeon_margin" style="">
                            <div class="col-lg-4 visible-lg"></div>
                            <div class="col-md-4 visible-md"></div>
                            <div class="col-sm-2 visible-sm"></div>
                            
                           <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                                <div class="form_reg wrap_surgeon border_customize_r" id="hid_laser">	 
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <select class="selectpicker" name="laser_ProcedureId" id="laser_ProcedureId" onChange="javascript:move_templete(this.value);">
                                           <option value="">Select Laser Procedure</option>
                                            <?php
												$category_laser='2';
												unset($conditionArr);
												$conditionArr['catId'] = $category_laser;
												$laserprocedure_Select = $objManageData->getMultiChkArrayRecords("procedures", $conditionArr,"name","ASC"," AND del_status !='yes' ");
												if($laserprocedure_Select) {
													foreach($laserprocedure_Select as $laser_procedureDetail){
												?>
															<option value="<?php echo $laser_procedureDetail->procedureId; ?>"><?php echo ucfirst($laser_procedureDetail->name);?></option>
														<?php
														}
													}
												
												?>
                                        </select>
                                    </div>
                                 </div><!-------------------Form Reg-----------------------------> 	
                             </div>	
                                            
                         </div>
                         </div>
                      </div>
                      </div>  
                      <!-- Necessary PUSH     -->	 
                      <div class="push"></div>
                      <!-- NEcessary PUSH     -->
                </div>
            </div>   
        </div>	
        <?php 
		$laserProcedureFrmSrc = "laser_procedure_templete.php";
		if($laser_ProcedureId) {
		?>
		
			<iframe name="laserProcedureFrame" style="width:100%; height:" frameborder="0" src="<?php echo $laserProcedureFrmSrc;?>?laser_ProcedureId=<?php echo $laser_ProcedureId; ?>"></iframe>
		<?php
		}
		?>
	
		</form>
<?php

	if($_REQUEST['laser_ProcedureId']<>"") {
	?>
	<script>
		//alert(document.getElementById('anesthesiologistList_id'));
		document.getElementById('procedureLabelId').style.display = 'none';
		document.getElementById('laser_ProcedureId').style.display = 'none';
		document.getElementById('selectedLaserProcedureNameId').style.display = 'block';
		top.frames[0].document.getElementById('saveButton').style.display = 'block';
		top.frames[0].document.frameSrc.source.value = 'laser_procedure_templete.php';
		
				
	</script>
	<?php	
	} else {
	?>
	<script>
		document.getElementById('procedureLabelId').style.display = 'block';
		document.getElementById('laser_ProcedureId').style.display = 'block';
		document.getElementById('selectedLaserProcedureNameId').style.display = 'none';
	</script>
	<?php	
	}

?>		
<script>
$(window).load(function()
{
var LDL = function()
{
	var H = top.frames[0].$("#iframeMain").attr('height')
	//parent.top.$("#div_middle").height() - top.frames[0].$("#div_innr_btn").outerHeight() - 20;
	$("iframe").attr('height', H);
}
LDL();
$(window).resize(function(e) { 
		   LDL();
		});
});

</script>
</body>
</html>	