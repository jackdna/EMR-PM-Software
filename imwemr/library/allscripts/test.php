<?php

include_once( '../../interface/globals.php' );

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once( './as_base.php' );
include_once( './as_patient.php' );
include_once( './as_dictionary.php' );
include_once( './as_dataValues.php' );


$dictionary = new as_dataValues();
$data = $dictionary->getProvider('jmedici');

var_dump(isset($data->FirstName));
print "\n";
print_r($data);

die;
class testing
{
	
	
	public function __construct()
	{
		
		$patientObj = new as_patient();
		
		/*$data = $patientObj->encounter('NonAppt', '102');*/
		
		print_r($data);
		
		/*Upload PDF*/
		
		/*$encounterData = array('encounterDate'=>'May 10 2017 12:14AM', 'EncounterID'=>'41033');
		
		$file = $GLOBALS['fileroot'].'/interface/common/new_html2pdf/patient_67886_09_35_41.pdf';
		
		$pdfData = file_get_contents($file);
		$pdfData = base64_encode($pdfData);
		
		$bytes = filesize($file);
			
		$dictionary = new as_patient();
		$data = $dictionary->uploaddocument( 'patient_67886_09_35_41.pdf', $pdfData, $bytes, 'Albert', 'Stanley-NoteSwift', 102, $encounterData);
		
		print_r($data);*/
		
		
//		$dictionary = new as_dictionary();
//		$data = $dictionary->encoutner_types();
//		print_r($data);
		//return $data;
		
		
		
	}
	
}

/*$test = new testing;*/


//151013124747340
//151118061254317


/*
Array
(
    [0] => stdClass Object
        (
            [OrgID] => 3
            [VisitID] => 40402
            [encounterDate] => May 10 2017 12:14AM
            [EncounterID] => 41033
        )
	[0] => stdClass Object
        (
            [OrgID] => 3
            [VisitID] => 40413
            [encounterDate] => May 15 2017  7:37AM
            [EncounterID] => 41153
        )

)
*/



?>