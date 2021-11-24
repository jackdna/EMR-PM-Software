<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

set_time_limit(0);
include_once("../../common/conDb.php");

require_once(dirname(__DIR__).'/../../'.$iolinkDirectoryName.'/library/amd/amd_patient.php');
$patient = new amd_patient();

$procInserted = 0;
$modInserted = 0;

/*Get AMD ID for Procedure Codes*/
$sqlCodes = "SELECT `p`.`code` FROM `procedures` `p` LEFT JOIN `amd_codes` `a` ON(`a`.`code`=`p`.`code` AND `a`.`code_type`=1) WHERE `p`.`code`!='' AND `a`.`code` IS NULL";
$respCodes = imw_query($sqlCodes);

if( $respCodes && imw_num_rows($respCodes) > 0 )
{
	while( $row = imw_fetch_assoc($respCodes) )
	{
		try{
			
			$codeResp = $patient->searchProcCode($row['code']);
			
			$code = (is_object($codeResp))?$codeResp:array_pop($codeResp);
			do{
				$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='1', `amd_object`='".json_encode($code)."'";
				if( imw_query($sqlAdd) )
					$procInserted++;
			}while( is_array($codeResp) && $code = array_pop($codeResp) );
			
		}
		catch(amdException $e)
		{}
	}
}

/*Get AMD ID for Modifier Codes*/
$sqlCodes = "SELECT `m`.`modifierCode` AS 'code' FROM `modifiers` `m` LEFT JOIN `amd_codes` `a` ON(`m`.`modifierCode` COLLATE latin1_general_ci =`a`.`code` AND `a`.`code_type`=3) WHERE `m`.`modifierCode`!='' AND `a`.`code` IS NULL";
$respCodes = imw_query($sqlCodes);

if( $respCodes && imw_num_rows($respCodes) > 0 )
{
	while( $row = imw_fetch_assoc($respCodes) )
	{
		try{
			
			$codeResp = $patient->searchModCode($row['code']);
			
			$code = (is_object($codeResp))?$codeResp:array_pop($codeResp);
			do{
				$sqlAdd = "INSERT INTO `amd_codes` SET `code`='".$code->{'@code'}."', `amd_id`='".$code->{'@id'}."', `code_type`='3', `amd_object`='".json_encode($code)."'";
				if( imw_query($sqlAdd) )
					$modInserted++;
			}while( is_array($codeResp) && $code = array_pop($codeResp) );
			
		}
		catch(amdException $e)
		{}
	}
}


?>

<html>
<head>
<title>Import Advanced MD IDs for Procedure Codes, Modifier</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2" color="green">
		Procedure Code ID Imported: <?php echo $procInserted; ?><br />
		Modifiers Code ID Imported: <?php echo $modInserted; ?><br />
	</font>
<?php
@imw_close();
?> 
</body>
</html>