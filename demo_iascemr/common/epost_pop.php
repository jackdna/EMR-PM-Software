<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
require_once('conDb.php'); 
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;
$ascID = $_SESSION['ascId']; 
$tableID = $_GET['tableID'];
$pConfId = $_REQUEST['pConfId'];
$actionDenied	=	$_REQUEST['AD'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
$qryGetEpostedata = "SELECT ep.epost_id, ep.epost_data,T_time, TIME_FORMAT(ep.T_time, '%l:%i %p') as ePostTime,
						ep.consent_template_id, ep.consentAutoIncId,
						IFNULL(concat(SUBSTRING(usr1.fname,1,1),SUBSTRING(usr1.lname,1,1)),'') AS created_operator_name, 
						IFNULL(concat(SUBSTRING(usr2.fname,1,1),SUBSTRING(usr2.lname,1,1)),'') AS modified_operator_name
						FROM eposted ep 
						LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)
						LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)
						WHERE ep.epost_id = '$tableID' AND
						ep.patient_conf_id = '$pConfId' ";
$resGetEpostedata = imw_query($qryGetEpostedata) or die(imw_error());
$intTotRowRetrive = imw_num_rows($resGetEpostedata);
$rowResGetEpostedata = imw_fetch_assoc($resGetEpostedata);
$consentMultipleAutoIncrId = $rowResGetEpostedata['consentAutoIncId'];

$consentTemplateIdEpost = $rowResGetEpostedata['consent_template_id'];
//$EpostTime = $rowResGetEpostedata['ePostTime'];
$EpostTime = $objManageData->getTmFormat($rowResGetEpostedata['T_time']);
$created_operator_name 	= $rowResGetEpostedata['created_operator_name'];
$modified_operator_name = $rowResGetEpostedata['modified_operator_name'];
$operatorVal = "";
if($modified_operator_name) {
	$operatorVal = "Modified - ".$modified_operator_name;	
}else if($created_operator_name) {
	$operatorVal = "Created - ".$created_operator_name;	
}
$rsNote_bk_class = "epost_title";
 ?>
<div id="evaluationLastEpostDiv" style="position:absolute;background-color:transparent;top:25px;left:360px;width:300px;display:block;z-index:11"> 
<?php if($intTotRowRetrive>0) { ?>
	<div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
		<span style="">E-postit</span>
		<span style=""><?php echo $operatorVal.' '.$EpostTime; ?></span>
	</div>
	<div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px;overflow-y: auto;overflow-x:auto; padding: 5px;">
		<?php echo $rowResGetEpostedata['epost_data']; ?>
	</div>
	<div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
		<?php if($actionDenied <> 'yes'){ ?>
    <a id="CancelBtn" class="btn btn-danger epost_del" onClick=" deleteEpost(<?php echo $tableID; ?>,'<?php echo $consentTemplateIdEpost;?>','<?php echo $consentMultipleAutoIncrId?>','<?php echo $pConfId;?>');" href="javascript:void(0)">
			<b class="fa fa-times"></b>&nbsp;Delete
		</a>
   	<?php } ?>
	</div>
	<div class="clearfix"></div>
<?php } ?>
 </div>