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
<title>Injection Misc. Procedures</title>
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
		document.frmInjectionMisc.submit();
		top.frames[0].document.frameSrc.source.value = 'injection_misc_data.php';
	}
</script>
</head>
<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$injProcedureId = $_REQUEST['injProcedureId'];
?>
		<form name="frmInjectionMisc" action="<?=basename($_SERVER['PHP_SELF'])?>" method="post">
    	<div id=""></div>
      <div class="margin_bottom_mid_adjustment scheduler_margins_head" id="procedureLabelId">
      	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
          	<div style="" id="" class="all_content1_slider">
            	<div class="wrap_inside_admin">
              	
                <div class="subtracting-head">
                	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
                  	<span>Injection/Misc</span>
                 	</div>
              	</div>
                
                <div class="wrap_inside_admin "> <!-- all_admin_content height_adjust_prefer -->
                	<div class="form_outer custom_surgeon_margin" style="">
                  	<div class="col-lg-4 visible-lg"></div>
                    <div class="col-md-4 visible-md"></div>
                    <div class="col-sm-2 visible-sm"></div>
                   	<div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                    	<div class="form_reg wrap_surgeon border_customize_r" id="hid_injection">
                      	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        	<?php
															$injMiscData	=	$objManageData->getInjectionMiscProcedures();
													?>			
                        	<select class="selectpicker" name="injProcedureId" id="injProcedureId" onChange="javascript:move_templete(this.value);">
                          	<option value="">Select Injection/Misc Procedure</option>
                            <?php
															if($injMiscData)
															{
																foreach($injMiscData as $injMiscDetail)
																{
														?>
                            			<option value="<?php echo $injMiscDetail->procedureId; ?>"><?php echo ucfirst($injMiscDetail->name);?></option>
                           	<?php
																}
															}
														?>
                       		</select>
                      	</div>
                    	</div>
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
				$injectionMiscFrmSrc = "injection_misc_data.php";
				if($injProcedureId)
				{
			?>
      		<iframe name="injectionMiscFrame" style="width:100%; height:" frameborder="0" src="<?php echo $injectionMiscFrmSrc;?>?injProcedureId=<?=$injProcedureId?>"></iframe>
    	<?php
				}
			?>
   	</form>
<?php
	if($_REQUEST['injProcedureId'])
	{
?>
		<script>
				document.getElementById('procedureLabelId').style.display = 'none';
				document.getElementById('injProcedureId').style.display = 'none';
				<!--document.getElementById('selectedInjProcedureNameId').style.display = 'block';-->
				top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
				top.frames[0].document.frameSrc.source.value = 'injection_misc_data.php';
		</script>
<?php	
	}
	else
	{
?>
		<script>
				document.getElementById('procedureLabelId').style.display = 'block';
				document.getElementById('injProcedureId').style.display = 'block';
				<!--document.getElementById('selectedInjProcedureNameId').style.display = 'none';-->
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
			$("iframe").attr('height', H);
		}
		LDL();
		$(window).resize(function(e) { LDL(); });
	});
</script>
</body>
</html>	