<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

set_time_limit(0);
include_once("../../common/conDb.php");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once(dirname(__DIR__).'/../../'.$iolinkDirectoryName.'/library/amd/amd_patient.php');
$patient = new amd_patient();

$dxCodesInserted = 0;

/*List all codes for laterality*/
$lateralities = array(1,2,3,4,5);
$severaties = array(0,1,2,3,4);
$stagings = array('A','B','D','G','K','S');

/*Get AMD ID for Diagnosis Codes*/
$sqlDxCodes = "SELECT TRIM(`icd10`) AS 'icd10', TRIM(`laterality`) AS 'laterality', TRIM(`staging`) AS 'staging', TRIM(`severity`) AS 'severity' FROM `icd10_data` WHERE `icd10`!='' ORDER BY `id` ASC";
$respDxCodes = imw_query($sqlDxCodes);

$i = 1;
if( $respDxCodes && imw_num_rows($respDxCodes) > 0 )
{
	while( $row = imw_fetch_assoc($respDxCodes) )
	{
		/*Build Diagnosis Code with Laterality, Severity and Staging*/
		$dxCode = $row['icd10'];
		$dxCodesList = array();
		
		array_push($dxCodesList, str_replace('-', '', $dxCode));
		
		$replacementCount = substr_count($dxCode, '-');
		if( $replacementCount == 1 ){
			
			/*List all Lateralities*/
			foreach($lateralities as $replacement)
			{
				$dxCodeTemp = preg_replace('/-/', $replacement, $dxCode, 1);
				array_push($dxCodesList, $dxCodeTemp);
			}
			
			/*List all Severaties*/
			foreach($severaties as $replacement)
			{
				$dxCodeTemp = preg_replace('/-/', $replacement, $dxCode, 1);
				array_push($dxCodesList, $dxCodeTemp);
			}
			
			/*List all Staging*/
			foreach($stagings as $replacement)
			{
				$dxCodeTemp = preg_replace('/-/', $replacement, $dxCode, 1);
				array_push($dxCodesList, $dxCodeTemp);
			}
			$dxCodesList = array_unique($dxCodesList);
		}
		elseif( $replacementCount == 2 ){
			
			/*List all Lateralities*/
			foreach($lateralities as $replacement)
			{
				$dxCodeTemp = preg_replace('/-/', $replacement, $dxCode, 1);
				array_push($dxCodesList, $dxCodeTemp);
			}
			
			$dxCodesListTemp = $dxCodesList;
			foreach( $dxCodesListTemp as $key=>$dxCodeVal )
			{
				/*List all Severaties*/
				foreach($severaties as $replacement)
				{
					$dxCodeTemp = preg_replace('/-/', $replacement, $dxCodeVal, 1);
					array_push($dxCodesList, $dxCodeTemp);
				}
				
				/*List all Staging*/
				foreach($stagings as $replacement)
				{
					$dxCodeTemp = preg_replace('/-/', $replacement, $dxCodeVal, 1);
					array_push($dxCodesList, $dxCodeTemp);
				}
				
				$dxCodesList[$key] = str_replace('-', '', $dxCodesList[$key]);
				//unset($dxCodesList[$key]);
			}
			$dxCodesList = array_unique($dxCodesList);
		}
		
		foreach($dxCodesList as $dxCodeFinal)
		{
			/*Check if AMD ID already saved in IMW DB*/
			$sqldx = "SELECT `id` FROM `amd_codes` WHERE `code`='".$dxCodeFinal."' AND `code_type`=2";
			$respdx = imw_query($sqldx);
			
			if( $respdx && imw_num_rows($respdx) == 0 )
			{
				/*Get Disgnosis Code ID from Advanced MD*/
				try{
					$codeResp = $patient->searchDiagCode($dxCodeFinal);
					
					$code = (is_object($codeResp))?$codeResp:array_pop($codeResp);
					do{
						$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='2', `amd_object`='".json_encode($code)."'";
						if( imw_query($sqlAdd) )
							$dxCodesInserted++;
					}while( is_array($codeResp) && $code = array_pop($codeResp) );
				}
				catch(amdException $e)
				{}
			}
		}
	}
}
?>
<html>
<head>
<title>Import Advanced MD IDs for Diagnosis Codes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2" color="green">
		Diagnosis Code ID Imported: <?php echo $dxCodesInserted; ?><br />
	</font>
<?php
@imw_close();
?> 
</body>
</html>