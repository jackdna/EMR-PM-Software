<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
Purpose: This file provides pdf version of all providers for a patient.
Access Type : Direct
*/

//indexPdf.php

//For including TCPDF class
$include_tcpdf = 'yes';

include_once("../../../config/globals.php");
include_once("../chart_globals.php");
include(dirname(__FILE__)."/PtProviders.php");

if(empty($_SESSION["patient"])){
	echo("Please choose any patient.");
	$flgStopExec = 1;
}
function getPatient_Name($id,$noFac="0")
	{
		$sql = "SELECT id,fname, lname, mname,default_facility FROM patient_data ".
			   "WHERE id = '$id' ";

		$res = imw_query($sql);
		while($row = imw_fetch_array($res)){
			$facility_id_p=$row['default_facility'];
			if($facility_id_p<>"")
			{
					$query="select facilityPracCode from pos_facilityies_tbl where pos_facility_id='$facility_id_p'";
					$result=imw_query($query);
					$rows=imw_fetch_array($result);

					$patient_facility="(".$rows['facilityPracCode'].")";
			}
			$sep="-";
			if( $noFac == "1" ){
				$patient_facility = "";
				$sep=" - ";
			}

			if($row != false)
			{
				$ret = $row["fname"]." ".$row["mname"]." ".$row["lname"].$sep.$row["id"]." ".$row['suffix'].$patient_facility;
				return $ret;
			}
			else
			{
				return false;
			}
		}
	}

if(!isset($flgStopExec) || empty($flgStopExec)){ //$flgStopExec = 1;

$patient_id = $_SESSION["patient"];
$patientName = getPatient_Name($patient_id,"1");

//PtProvider
$oPtPro = new PtProviders($patient_id);

// remove default header/footer
$oPtPro->setPrintHeader(false);
$oPtPro->setPrintFooter(false);

// set font
$oPtPro->SetFont('times', '', 9);

//Add Page
$oPtPro->AddPage();

//Set Title
$oPtPro->setPdfHeader($patientName);

$oPtPro->SetFont('times', '', 9);

//Content
$oPtPro->getPdf();

// reset pointer to the last page
$oPtPro->lastPage();

//Out Put On Browser
$oPtPro->Output("pt_tests.pdf","I");


}//$flgStopExec

?>