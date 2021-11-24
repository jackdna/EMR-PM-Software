<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

/*
 * File: index.php
 * Purpose: 
 * Access Type: Include
*/

include_once(dirname(__FILE__).'/../../interface/globals.php');
include_once(dirname(__FILE__).'/amd_base.php');
error_reporting(E_ALL);
ini_set('display_errors', true);

$callFromSC = true;

include_once(dirname(__FILE__).'/amd_patient.php');
$patient = new amd_patient();

/*Update INS Order Value for ALL patient with AMD Patient ID*/
$sql = "SELECT `amd_patient_id` FROM `patient_data_tbl` WHERE `amd_ins_order`='' AND `amd_patient_id`!='0'";
$resp = imw_query($sql);

if( $resp && imw_num_rows($resp) > 0 )
{
	while( $row = imw_fetch_assoc($resp) )
	{
		/*Query for Patient Details*/
		try{
			$patientData = $patient->getDemographics($row['amd_patient_id']);
			
			/*Update Data in DB*/
			if( isset($patientData->{'@insorder'}) )
			{
				$sqlUpd = "UPDATE `patient_data_tbl` SET `amd_ins_order`='".$patientData->{'@insorder'}."' WHERE `amd_patient_id`='".$row['amd_patient_id']."'";
				imw_query( $sqlUpd );
			}
			else
				throw new amdException( 'Patient Data Error', 'Insurance Order Not supplied from Advanced MD' );
		}
		catch( amdException $e )
		{}
	}
}

?>