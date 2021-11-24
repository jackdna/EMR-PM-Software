<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
//FUNCTION EDIT BY SURINDER 
function dateDiffCommon($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;}

//END  FUNCTION EDIT BY SURINDER

function getPracticeName($loginUserId,$uType) {
	$practiceNameCord='';
	if($loginUserId) {
		$userTypeQry = "SELECT practiceName FROM users
				WHERE  usersId = '$loginUserId' and user_type='$uType'";
		$userTypeRes = imw_query($userTypeQry);
		$userTypeRows = imw_fetch_array($userTypeRes);
		$practiceNameCord = $userTypeRows['practiceName'];
	}
	return $practiceNameCord;
}

?>