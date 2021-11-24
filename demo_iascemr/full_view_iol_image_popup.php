<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
extract($_GET);
 $patient_id = $_SESSION['patient_id'];
 $ascId = $_SESSION['ascId'];
 $pConfId = $_SESSION['pConfId'];

//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

        $ViewOpRoomRecordQry = "select * from `operatingroomrecords` where  confirmation_id = '$pConfId' AND ascId='$ascId'";
		$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
		$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
		$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
		$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
		$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE
?>
<?php
if($iol_ScanUpload){
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF" height="100%">	
<tr>
<td class="text_10" align="center">
<img border="0" src="admin/logoImg.php?from=op_room_record&id=<?php echo $operatingRoomRecordsId;?>" align="center">
</td>
</tr>
</table>
<?php }?>