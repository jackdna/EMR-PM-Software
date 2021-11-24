<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "dischargesummarysheet";
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
$ascId = $_SESSION['ascId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
//GETTING PATIENTCONFIRMATION DETAILS

	$confirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	$surgeonId = $confirmationDetails->surgeonId;
	$finalizeStatus = $confirmationDetails->finalize_status;
	$primary_procedure = $confirmationDetails->patient_primary_procedure;
	$secondary_procedure = $confirmationDetails->patient_secondary_procedure;
	$primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
	$secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;
//GETTING PATIENTCONFIRMATION DETAILS
?>

<div class="panel panel-default bg_panel_sum" style="width:250px">
    <div class="panel-heading haed_p_clickable ">
    	<h3 class="panel-title rob"> Procedure Performed </h3>
    </div>
    <div class="panel-body">
		<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<?php if($secondary_procedure_id=='0'){?>
            <?php echo $primary_procedure;?>
            <br>
            <input type="button" class="button" value="Ok" style="width:65px;height:20px;" onClick="document.getElementById('divprocedure').style.display = 'none';">
            <br>
        <?php } 
        else{?>
        <b  class="col-lg-12 col-sm-12 col-xs-12 col-md-12 text-center">Primary Procedure</b>
        <span class="text_10"><?php echo $primary_procedure;?></span>
        <br>
        <b  class="col-lg-12 col-sm-12 col-xs-12 col-md-12 text-center">Secondary Procedure</b>
        <br>
        <span class="text_10"><?php echo $secondary_procedure;?></span>
        <br />
        <span class="col-lg-12 col-sm-12 col-xs-12 col-md-12 text-center">
        <a class="btn btn-info" onClick="document.getElementById('divprocedure').style.display = 'none';" href="javascript:void(0)">&nbsp;  OK &nbsp; </a>
        </span>
        <br>
   		<?php }?>	
    </div>
	</div>
</div>

