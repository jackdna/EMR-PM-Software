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
*/

include_once(dirname(__FILE__).'/as_base.php');

class as_patient extends as_base
{
	public function __construct()
	{
		/*Invoke Parent's Constructor*/
		parent::__construct();
	}
	
	/**
	 * Search Patient by MRN number
	 **/
	public function mrn( $mrn='' )
	{
		if ( $mrn == '' )
			throw new asException( 'Call Error', 'Blank MRN number Supplied.' );
		
		$this->prepareParameters( 'GetPatientByMRN', array( $mrn ) );
		return $this->makeCall( true );
	}
	
	/******SEARCH PATIENT BY NAME OR OTER INFO****/
	public function PtName( $name='' )
	{
		if ( $name == '' )
			throw new asException( 'Call Error', 'Blank value Supplied.' );
		
		$this->prepareParameters( 'SearchPatients', array( $name ) );
		return $this->makeCall();
	}
	
	/**
	 * Get Patient Information
	 **/
	public function patient( $patientId )
	{
		if ( $patientId == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks Patient Id Supplied.' );
		
		$this->prepareParameters( 'GetPatient', array(), $patientId );
		return $this->makeCall( true );
	}
	
	/**
	 * Get Patient's Clinical Summary
	 **/
	public function clinicalSummary( $patientId )
	{
		if ( $patientId == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks Patient Id Supplied.' );
		
		$this->prepareParameters( 'GetClinicalSummary', array('', '', 'Y'), $patientId );
		return $this->makeCall();
	}
	
	/**
	 * Get Patient's Problems List
	 **/
	public function problems( $patientId )
	{
		if ( $patientId == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks Patient Id Supplied.' );
		
		$this->prepareParameters( 'GetPatientProblems', array('N'), $patientId );
		return $this->makeCall();
	}
	
	/**
	 * Add New Allergy
	 * */
	public function addAllergy( $allergyData )
	{
		if ( $_SESSION['as_mrn'] == '' || $_SESSION['as_id'] == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks MRN / Patient Id Supplied.' );
		
		$params = array_fill(0, 6, '');
		
		$params[1] = '<saveallergy>
						<field name="OrganizationalMRN" value="'.$_SESSION['as_mrn'].'" />
						<field name="AllergyName" value="'.$allergyData['name'].'" />
						<field name="isMedicationAllergy" value="'.$allergyData['isMed'].'" />
						<field name="Verified" value="Y" />
					</saveallergy>';
		$params[5] = $allergyData['comments'];
		
		$this->prepareParameters( 'SaveAllergy', $params, $_SESSION['as_id'] );
		return $this->makeCall(true);
	}


	/**
	 * Add New Medication
	 * */
	public function addMedication( $medDDi, $sigVal='' )
	{
		if ( $_SESSION['as_mrn'] == '' || $_SESSION['as_id'] == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks MRN / Patient Id Supplied.' );
		
		$params = array_fill(0, 6, '');
		
		$params[0] = '<formdata>
						<field name="rxaction" value="newrx" />
						<field name="DDI" value="'.$medDDi.'" />
						<field name="Sig" value="'.$sigVal.'" />
						<field name="historicalflag" value="y" />
					</formdata>';
		
		$this->prepareParameters( 'SaveHistoricalRx', $params, $_SESSION['as_id'] );
		return $this->makeCall(true);
	}
	
	/*Update Allergy Status in Touch Works*/
	public function changeAllergyStatus( $allergyData )
	{
		if ( $_SESSION['as_mrn'] == '' || $_SESSION['as_id'] == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks MRN / Patient Id Supplied.' );
		
		$this->prepareParameters( 'DoSendback', array('Allergies', $allergyData['status'], $allergyData['transId']), $_SESSION['as_id'] );
		return $this->makeCall(true);
	}
	
	/**
	 * Add / Update problem in Touch Works
	 */
	public function saveProblem( $problemData, $asPtId = '' )
	{
		if ( $asPtId == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks MRN / Patient Id Supplied.' );
		
		$includeActive = $problemData['status'] === 'Active' ? 'Y' : 'N';
		$includePMH = $problemData['status'] === 'Active' ? 'N' : 'Y';
		
		$parameter = '<saveproblemsdatarequest>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="problem" attributeid="title" value1="'.$problemData['problem'].'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="problem" attributeid="code" value1="'.$problemData['dxCode'].'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="problem" attributeid="source" value1="ICD10"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="status" value1="'.$problemData['status'].'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="onset" value1="'.$problemData['onsetDate'].'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="description" value1="'.$problemData['problem'].'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="includeactive" value1="'.$includeActive.'"/>
						<saveproblemsdata setid="'.$problemData['asId'].'" fieldid="includepastmedical" value1="'.$includePMH.'"/>
					</saveproblemsdatarequest>';
		
		$this->prepareParameters( 'SaveProblemsData', array($parameter), $asPtId );
		return $this->makeCall(true);
	}
	
	/**
	 * Push Update in Problem to Touch Works / Unity API
	 * */
	public function updateProblem( $problemData )
	{
		if ( $_SESSION['as_mrn'] == '' || $_SESSION['as_id'] == '' )
			throw new asException( 'Call Error', 'Blank TouchWorks MRN / Patient Id Supplied.' );
		
		$params = '<saveproblemsdatarequest><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="problem" attributeid="title" value1="'.$problemData['title'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="problem" attributeid="code" value1="'.$problemData['icd9'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="problem" attributeid="source" value1="AllscriptsGUID"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="status" value1="'.$problemData['status'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="onset" value1="'.$problemData['onset'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="description" value1="'.$problemData['title'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="includeactive" value1="'.$problemData['incActive'].'"/><saveproblemsdata setid="'.$problemData['as_id'].'" fieldid="includepastmedical" value1="'.$problemData['incPMH'].'"/></saveproblemsdatarequest>';
		
		$this->prepareParameters( 'SaveProblemsData', array($params), $_SESSION['as_id'] );
		return $this->makeCall(true);
	}
	
	/**
	 * Create/Update encounter in Touchworks / Unity API
	 * */
	public function encounter( $encounterType, $patientId, $dos )
	{
		
		if ( $encounterType == '' )
			throw new asException( 'Call Error', 'Encounter Type Missing.' );
		
		$this->prepareParameters( 'SaveSimpleEncounter', array($encounterType, $dos), $patientId );
		return $this->makeCall();
	}
	
	/**
	 * Upload document PDF in Touchworks / Unity API
	 * */
	public function uploaddocument($docName, $docPath, $ptfName, $ptlname, $patientId, $encounterData, $documentType, $documentID = 0)
	{
		if ( $documentType == '' )
			throw new asException( 'Call Error', 'Please provide valid document type.' );
		
		/*PDF data*/
		$pdfData = file_get_contents($docPath);
		$pdfData = base64_encode($pdfData);
		$bytes = filesize($docPath);
		/*End PDF data*/

		$documentID = (string)trim($documentID);
		$documentCommand = ($documentID==='0' || $documentID === '')?'i':'u';

		/*Call 1 to send PDF Data */
		$xmlData = '<doc>
			<item name="documentCommand" value="'.$documentCommand.'" />
			<item name="documentType" value="'.$documentType.'" />
			<item name="offset" value="0" />
			<item name="bytesRead" value="'.$bytes.'" />
			<item name="bDoneUpload" value="false" />
			<item name="documentVar" value="" />
			<item name="documentID" value="'.$documentID.'" />
			<item name="vendorFileName" value="'.$docName.'" />
			<item name="ahsEncounterDTTM" value="'.$encounterData['encounterDate'].'" />
			<item name="ahsEncounterID" value="'.$encounterData['EncounterID'].'" />
			<item name="ownerCode" value="'.$_SESSION['as_user_entry_code'].'" />
			<item name="organizationName" value="" />
			<item name="patientFirstName" value="'.$ptfName.'" />
			<item name="patientLastName" value="'.$ptlname.'" />
			<item name="Orientation" value="1" />
			<item name="auditCCDASummary" value="N" />
		</doc>';
		
		$params = array($xmlData, '', '', '', '', $pdfData);
		
		$this->prepareParameters( 'SaveDocumentImage', $params, $patientId );
		$documentData = $this->makeCall( true );
		
		$documentFinalData = '';
		/*Call 2 to commit the Document*/
		if( isset($documentData->documentVar) )
		{
			$xmlData = '<doc>
				<item name="documentCommand" value="'.$documentCommand.'" />
				<item name="documentType" value="'.$documentType.'" />
				<item name="offset" value="0" />
				<item name="bytesRead" value="0" />
				<item name="bDoneUpload" value="true" />
				<item name="documentVar" value="'.$documentData->documentVar.'" />
				<item name="documentID" value="'.$documentID.'" />
				<item name="vendorFileName" value="'.$docName.'" />
				<item name="ahsEncounterDTTM" value="'.$encounterData['encounterDate'].'" />
				<item name="ahsEncounterID" value="'.$encounterData['EncounterID'].'" />
				<item name="ownerCode" value="'.$_SESSION['as_user_entry_code'].'" />
				<item name="organizationName" value="" />
				<item name="patientFirstName" value="'.$ptfName.'" />
				<item name="patientLastName" value="'.$ptlname.'" />
				<item name="Orientation" value="1" />
				<item name="auditCCDASummary" value="N" />
			</doc>';
			
			$params = array($xmlData);
			$this->prepareParameters( 'SaveDocumentImage', $params, $patientId );
			$documentFinalData = $this->makeCall( true );
		}
		
		return ( isset($documentFinalData->documentID) ) ? trim($documentFinalData->documentID) : false;
	}
	
	/**
	 * Add Charge to Touch Works / Unity API
	 * Called explicitely to push each charge entry to Unity.
	 * Return response charge Id from Unity
	 * */
	
	public function saveCharge( $cptCode, $modifiers, $diagnosisIds, $units, $dos, $twEncounterId, $twChargeId='' )
	{
		$xmlData = "<Charge>
						<BillingComment value='Testing' />
						<ChargeCodeEntryCode value='".$cptCode."' />
						<ModifierEntryCodes value='".$modifiers."'/>
						<DiagnosisDEs value='".$diagnosisIds."' />
						<DiagnosisEntryCodes value='' />
						<UnitsToBillFor value='".$units."' />
						<DateOfService value='".$dos."' />
						<NDC value='' />
					</Charge>";
		
		$params = array( '', $twChargeId, $twEncounterId, '', $xmlData );
		
		$this->prepareParameters( 'SaveCharge', $params, $_SESSION['as_id'] );
		return $this->makeCall( true );
		
	}
	
	public function filterAssessmentProblems(&$assessmentData)
	{
		foreach($assessmentData as &$assementDetails)
		{
			$problem = $assementDetails['assessment'];
			$problemId	= $assementDetails['pbid'];
			
			$assessmentFiltered = $this->filterProblemDx($problem);
			
			$problem = $assessmentFiltered['problem'];
			$dxCode = $assessmentFiltered['dxCode'];
			
			if(!empty($dxCode)){ //As discssed with Pankaj.
			$assementDetails = array();
			$assementDetails['problem']		= $problem;
			$assementDetails['dxCode']		= $dxCode;
			$assementDetails['problemId']	= $problemId;
			}
		}
	}
	
	private function filterProblemDx($data)
	{
		$data = trim($data);
		$returnData = array('dxCode'=>'', 'problem'=>'');
		
		$lastChar = substr($data, -1);
		if($lastChar == ")"){

			$ptrn = "/\((\s*\w{3}(\.[\w\-]{1,4})?(\,)?)+\)$/";			
			if(preg_match($ptrn, $data, $match)){
				$icd10Dx_s = trim($match[0]);
				if(!empty($icd10Dx_s)){
					$icd10Dx_s = str_replace(array("(",")"," "), "", $icd10Dx_s);
					$icd10Dx = explode(",", $icd10Dx_s);
					$icd10Dx = array_filter($icd10Dx);	
				}				
			}else{		
			$icd10Dx = preg_replace('/^(?:.*\()(.*)\)/D', '$1', $data);	/*Capture ICD10 codes from end*/
			$icd10Dx = preg_replace('/\s+/', '', $icd10Dx);	/*Replace space*/
			$icd10Dx = explode(',', $icd10Dx);	/*Split by dx Codes separator*/
			}
			
			$returnData['dxCode'] = $icd10Dx;
			
			$returnData['problem'] = preg_replace('/\([^\(]*$/', '', $data);
		}
		return($returnData);
	}
}