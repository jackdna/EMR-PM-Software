<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
//START DELETE DUPLICATE BLANK SCAN FOLDERS
if($pConfId) {
	$iolinkDuplicateFolderArr = array('Pt. Info','Clinical','H&P','EKG','Health Questionnaire','Ocular Hx','Consent','IOL');
	foreach($iolinkDuplicateFolderArr as $iolinkDuplicateFolder){
		$chk_duplicate_scan_document_qry 			= "select document_id from scan_documents where document_name = '".$iolinkDuplicateFolder."' AND confirmation_id = '".$pConfId."' LIMIT 0,2";
		$chk_duplicate_scan_document_res 			= imw_query($chk_duplicate_scan_document_qry) or die($chk_duplicate_scan_document_qry.imw_error());
		$chk_duplicate_scan_document_numrow			= imw_num_rows($chk_duplicate_scan_document_res);
		if($chk_duplicate_scan_document_numrow>1) {
			while($chk_duplicate_scan_document_row 	= imw_fetch_array($chk_duplicate_scan_document_res)) {
				$dupDocId = $chk_duplicate_scan_document_row["document_id"];
				$get_duplicate_scan_document_qry 	= "select document_id from scan_upload_tbl where document_id = '".$dupDocId."' LIMIT 0,1";
				$get_duplicate_scan_document_res 	= imw_query($get_duplicate_scan_document_qry) or die($get_duplicate_scan_document_qry.imw_error());
				$get_duplicate_scan_document_numrow	= imw_num_rows($get_duplicate_scan_document_res);
				
				if($get_duplicate_scan_document_numrow<=0) {
					$deleteDupliDocQry 				= "DELETE FROM scan_documents WHERE document_id = '".$dupDocId."' AND confirmation_id = '".$pConfId."' ";	
					$deleteDupliDocRes 				= imw_query($deleteDupliDocQry) or die($deleteDupliDocQry.imw_error());	
				}
			}
		}
					
	}
}
//END DELETE DUPLICATE BLANK SCAN FOLDERS


?>
