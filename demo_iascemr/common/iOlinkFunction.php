<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
function iOLinkPracticeName($loginUserId,$uType) {
	$practiceNameCord='';
	if($loginUserId) {
		$userTypeQry = "SELECT practiceName FROM users
						WHERE  usersId = '$loginUserId' AND user_type='$uType'";
		$userTypeRes = imw_query($userTypeQry);
		$userTypeRows = imw_fetch_array($userTypeRes);
		$practiceNameCord = $userTypeRows['practiceName'];
	}
	return $practiceNameCord;
}

function getFirstSurgeryTime($srgnDos,$srgnId,$makeSurgeryTimeDiv='',$practiceName='') {
	$firstSurgeryTime='';
	$firstSurgeryLimitQry='';
	list($YrFn,$MnFn,$DyFn)=explode('-',$srgnDos);
	if($srgnId) {
		// GETTING LOGIN USER NAME
		$srgnNameQry = "SELECT fname, mname, lname FROM users WHERE usersId = '".$srgnId."'";
		$srgnNameRes = imw_query($srgnNameQry);
		$srgnNameRow = imw_fetch_array($srgnNameRes);
		$srgnFName = $srgnNameRow['fname'];
		$srgnMName = $srgnNameRow['mname'];
		$srgnLName = $srgnNameRow['lname'];
		// GETTING LOGIN USER NAME
	
		$getSurgeryAndQry = " AND stub_tbl.surgeon_fname='".addslashes($srgnFName)."' 
								AND stub_tbl.surgeon_mname='".addslashes($srgnMName)."' 
								AND stub_tbl.surgeon_lname='".addslashes($srgnLName)."'
								AND stub_tbl.surgeon_fname=users.fname 
								AND stub_tbl.surgeon_mname=users.mname 
								AND stub_tbl.surgeon_lname=users.lname";
								
	}else {
		$getSurgeryAndQry = " AND users.practiceName='$practiceName' 
								AND stub_tbl.surgeon_fname=users.fname 
								AND stub_tbl.surgeon_mname=users.mname 
								AND stub_tbl.surgeon_lname=users.lname";
	}
	
	if($makeSurgeryTimeDiv) {
		$firstSurgeryTime .= "<div id='iOLinkSurgeryTimeId".$DyFn."' style='position:absolute; z-index:9999; top:10; left:20px;background-color:#FFFF99; display:none;'>";
	}else {
		$firstSurgeryLimitQry =" limit 0,3";
	}
	
	$getSurgeryTimeQry = "SELECT stub_tbl.* FROM stub_tbl,users 
						  WHERE stub_tbl.dos='".$srgnDos."' 
						  $getSurgeryAndQry 
						  GROUP BY stub_tbl.surgeon_fname  
						  ORDER BY stub_tbl.surgery_time ASC $firstSurgeryLimitQry";
	$getSurgeryTimeRes = imw_query($getSurgeryTimeQry) or die(imw_error());
	$getSurgeryTimeNumRow = imw_num_rows($getSurgeryTimeRes);
	if($getSurgeryTimeNumRow>0) {
		
		$firstSurgeryTime .= "<table align='left' border='0' cellpadding='0' cellspacing='0' width=100% >";
		while($getSurgeryTimeRow = imw_fetch_array($getSurgeryTimeRes)) {
			$srgnStubFName = $getSurgeryTimeRow['surgeon_fname'];
			$srgnStubMName = $getSurgeryTimeRow['surgeon_mname'];
			$srgnStubLName = $getSurgeryTimeRow['surgeon_lname'];
			
			$srgnStubFirstSurgeryTime='';
			$srgnStubLastSurgeryTime='';
			$hrefLinkStart="<a class='link_home' href='javascript:void(0);' onClick='javascript:schClick(\"$YrFn\",\"$MnFn\",\"$DyFn\",\"$srgnId\");'>";
			$hrefLinkEnd="</a>";
			
			if($makeSurgeryTimeDiv) {
				$hrefLinkStart='';
				$hrefLinkEnd='';
				$firstSurgeryAndQry = " AND surgeon_fname='".$srgnStubFName."' AND surgeon_mname='".$srgnStubMName."' AND surgeon_lname='".$srgnStubLName."'";
				$firstSurgeryTimeQry = "SELECT * FROM `stub_tbl` WHERE dos='".$srgnDos."' $firstSurgeryAndQry ORDER BY surgery_time ASC";
				$firstSurgeryTimeRes = imw_query($firstSurgeryTimeQry) or die(imw_error());
				$firstSurgeryTimeNumRow = imw_num_rows($firstSurgeryTimeRes);
				if($firstSurgeryTimeNumRow>0) {
					$firstSurgeryTimeRow = imw_fetch_array($firstSurgeryTimeRes);
					$srgnStubFirstSurgeryTime = $firstSurgeryTimeRow['surgery_time'];
				}
				
				$lastSurgeryAndQry = " AND surgeon_fname='".$srgnStubFName."' AND surgeon_mname='".$srgnStubMName."' AND surgeon_lname='".$srgnStubLName."'";
				$lastSurgeryTimeQry = "SELECT * FROM `stub_tbl` WHERE dos='".$srgnDos."' $lastSurgeryAndQry ORDER BY surgery_time DESC";
				$lastSurgeryTimeRes = imw_query($lastSurgeryTimeQry) or die(imw_error());
				$lastSurgeryTimeNumRow = imw_num_rows($lastSurgeryTimeRes);
				if($lastSurgeryTimeNumRow>0) {
					$lastSurgeryTimeRow = imw_fetch_array($lastSurgeryTimeRes);
					$srgnStubLastSurgeryTime = $lastSurgeryTimeRow['surgery_time'];
				}
			}
			$srgnStubSurgeryBothTime='';
			if($srgnStubFirstSurgeryTime && $srgnStubLastSurgeryTime) {
				list($srgnStubFirstHour,$srgnStubFirstMin,$srgnStubFirstSec)=explode(':',$srgnStubFirstSurgeryTime);
				$srgnStubSurgeryFirstTimeTemp=date('h:iA',mktime($srgnStubFirstHour,$srgnStubFirstMin,$srgnStubFirstSec,0,0,0));
				
				list($srgnStubLastHour,$srgnStubLastMin,$srgnStubLastSec)=explode(':',$srgnStubLastSurgeryTime);
				$srgnStubSurgeryLastTimeTemp=date('h:iA',mktime($srgnStubLastHour,$srgnStubLastMin,$srgnStubLastSec,0,0,0));
				
				$srgnStubSurgeryBothTime=substr(trim($srgnStubSurgeryFirstTimeTemp),0,6)."-".substr(trim($srgnStubSurgeryLastTimeTemp),0,6);
			}
			$srgnStubName = substr($srgnStubFName,0,1).substr($srgnStubLName,0,1);
			$firstSurgeryTime .="<tr><td nowrap align='left' class='text_10' style='font-size:9px; '>".$hrefLinkStart.$srgnStubName."-SC ".$srgnStubSurgeryBothTime.$hrefLinkEnd."</td><tr>";
		}
		$firstSurgeryTime .="</table>";
							 
							 
	}
	if($makeSurgeryTimeDiv) {
		$firstSurgeryTime .="</div>";
		
	}				 
	
	return $firstSurgeryTime;
}


?>