<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php require_once('conDb.php'); 
session_start();
$ascID = $_SESSION['ascId']; 
$tableID = $_GET['tableID'];
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;

//gurleen
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
$consentMultipleId = $_REQUEST['epostTemplateId'];
$consentMultipleAutoIncrId = $_GET['consentMultipleAutoIncrId'];
//gurleen end
if($consentMultipleId) {
	$consentMultipleIdQry = " AND ep.consent_template_id = '$consentMultipleId'";
}
//gurleen
if($_REQUEST['consentMultipleAutoIncrId']){
	$consentAutoIncIdQry = " AND ep.consentAutoIncId = '$consentMultipleAutoIncrId'";
}

//CODE TO GET TABLE NAME 
	$getEpostTableNameQry = "SELECT `table_name` FROM eposted WHERE epost_id = '$tableID' AND patient_conf_id = '$pConfId' ";
	$getEpostTableNameRes = imw_query($getEpostTableNameQry) or die("error1");
	$getEpostTableNameNumRow = imw_num_rows($getEpostTableNameRes);
	if($getEpostTableNameNumRow>0) {
		$getEpostTableNameRow = imw_fetch_array($getEpostTableNameRes);
		$EpostTableName = $getEpostTableNameRow["table_name"];
	}
//END CODE TO GET TABLE NAME

//DELETE EPOST
	$query_rsNotes = "DELETE ep FROM eposted ep WHERE ep.epost_id = '$tableID' AND ep.patient_conf_id = '$pConfId' $consentAutoIncIdQry";
	$rsNotes = imw_query($query_rsNotes) or die(imw_error());
//END DELETE EPOST

$query_rsNotes = "SELECT * FROM eposted ep WHERE ep.table_name = '$EpostTableName' AND ep.patient_conf_id = '$pConfId' $consentMultipleIdQry $consentAutoIncIdQry";
$rsNotes =imw_query($query_rsNotes) or die(imw_error());
$totalRows_rsNotes =imw_num_rows($rsNotes);
?>
<!-- <td nowrap id="epostDelId">  -->
	<?php
		while($row = imw_fetch_array($rsNotes)){
			if($totalRows_rsNotes > 0){
	?>
				<a class="btn-xs btn-primary " title="ePostIt" onMouseOver="showEpost('<?php echo $row['epost_id']; ?>','<?php echo $pConfId; ?>')" >
					<b class="fa fa-comment"></b>
				</a>
	<?php
			}
		}
	?>
<!-- </td> -->~@
<?php
$rsNote_bk_class = "epost_title";
$qryGetEpostedata = "SELECT ep.epost_id, ep.epost_data,ep.T_time, TIME_FORMAT(ep.T_time, '%l:%i %p') as ePostTime,
						ep.consent_template_id, ep.consentAutoIncId,
						IFNULL(concat(SUBSTRING(usr1.fname,1,1),SUBSTRING(usr1.lname,1,1)),'') AS created_operator_name, 
						IFNULL(concat(SUBSTRING(usr2.fname,1,1),SUBSTRING(usr2.lname,1,1)),'') AS modified_operator_name
						FROM eposted ep 
						LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)
						LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)
						WHERE ep.table_name = '$EpostTableName' 
						AND ep.patient_conf_id = '$pConfId' 
						$consentMultipleIdQry
						$consentAutoIncIdQry
						";
$resGetEpostedata = imw_query($qryGetEpostedata) or die(imw_error());
$intTotRowRetrive = imw_num_rows($resGetEpostedata);
if($intTotRowRetrive>0) {
	$intCountChild = 0;
	$top = 0;
	$left = 0;
	while($rowResGetEpostedata = imw_fetch_assoc($resGetEpostedata)) {
		$tableID = $rowResGetEpostedata['epost_id'];
		$consentMultipleAutoIncrId = $rowResGetEpostedata['consentAutoIncId'];
		$consentTemplateIdEpost = $rowResGetEpostedata['consent_template_id'];
		//$EpostTime = $rowResGetEpostedata['ePostTime'];
		$EpostTime = $objManageData->getTmFormat($rowResGetEpostedata['T_time']);
		$epostdata = stripslashes($rowResGetEpostedata['epost_data']);
		$created_operator_name 	= $rowResGetEpostedata['created_operator_name'];
		$modified_operator_name = $rowResGetEpostedata['modified_operator_name'];
		$operatorVal = "";
		if($modified_operator_name) {
			$operatorVal = "Modified - ".$modified_operator_name;	
		}else if($created_operator_name) {
			$operatorVal = "Created - ".$created_operator_name;	
		}
		?>
        <div id="epostMainDivChild<?php echo $intCountChild;?>" class="drsElement drsMoveHandle" style="top:<?php echo $top;?>px; left:<?php echo $left;?>px;width:310px;position:absolute;background-color:transparent;border:0px none;z-index:11">
            <div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
                <span style="">E-postit</span>
                <span style=""><?php echo $operatorVal.' '.$EpostTime; ?></span>
                <button style="opacity: .9;" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="document.getElementById('epostMainDivChild<?php echo $intCountChild;?>').style.display='none';">
                    <span aria-hidden="true" style="">×</span>
                </button>
            </div>
            <div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px;overflow-y: auto;overflow-x:auto; padding: 5px;">
                <?php echo $epostdata; ?>
            </div>
            <div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
                <a id="CancelBtn" class="btn btn-danger epost_del" onClick="deleteEpost(<?php echo $tableID; ?>,'<?php echo $consentTemplateIdEpost;?>','<?php echo $consentMultipleAutoIncrId?>','<?php echo $pConfId;?>');" href="javascript:void(0)">
                    <b class="fa fa-times"></b>&nbsp;Delete
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php		
		$intCountChild++;
		$left+=320;
		if($left==960){
			//$top = 0;
			$left = 0;
			$top+=160;
		}
	}
}
?>