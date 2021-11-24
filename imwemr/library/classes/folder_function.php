<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
 
function getChild1($catID, $level="",$no_pid="")
{ 
	global $pid;
	$andQry="and patient_id=0";
	if($no_pid != 'no_pid') {
		$andQry = "and patient_id='$pid'";	
	}
	$query = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE  parent_id=$catID  AND folder_status ='active' ".$andQry." order by folder_name";
	$resultSet = imw_query($query) or die('Error while retrieving categories : '.imw_error());
	$level++;
	$mainArr = array();
	$tempArr = array();
	if(imw_num_rows($resultSet))
	{
		while($row = imw_fetch_assoc($resultSet))
		{
			$categoryID = $row['folder_categories_id'];
			$categoryName = ucfirst($row['folder_name']);
			$parentID = $row['parent_id'];
			$space = '&nbsp;';
			for($cntr=0;$cntr<$level;$cntr++)
				$space .= "&nbsp;&nbsp;&nbsp;";
			$mainArr[$categoryID] = $space.'&gt;'.$categoryName;
			$tempArr = getChild1($categoryID, $level,$no_pid);			
			if(is_array($tempArr) && count(($tempArr)) > 0  )
				$mainArr = mergeArr($mainArr, $tempArr);
		}
	}
	return $mainArr;
}
?>