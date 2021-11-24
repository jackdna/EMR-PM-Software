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

class as_dataValues extends as_base
{
	private $value;
	private $type;
	private $asPtId;
	
	public function __construct($dailyCallsFlag=true)
	{
		/*Invoke Parent's Constructor*/
		parent::__construct($dailyCallsFlag);
	}
	
	/**
	 * Base function to query data values from Unity API
	 * @value = Data to be searched from allscripts
	 * @type = Type of value to be queried
	 * @asPtId = AllScripts internal Patient Id
	 **/
	public function query($value, $type, $asPtId)
	{
		if ( $value === '' ||  $type === '' || $asPtId === '' )
			throw new asException( 'Call Error', 'Blank data supplies for values query.' );
		
		$this->value = $value;
		$this->type = $type;
		$this->asPtId = $asPtId;
		
		switch( $this->type )
		{
			
			case 'allergies':
				$resp = $this->allergy();
				break;
			case 'dxcode':
				$resp = $this->dxCode();
				break;
			case 'medications':
				$resp = $this->medications();
				break;
			default :
				throw new asException( 'Call Error', 'Invalid value type queried.' );
				break;
		}
		return $resp;
	}
	
	/**
	 * Get Additional data about allergy from allScripts
	 * @value = Name of the allergy to be searched
	 * */
	private function allergy()
	{
		$data = array();
		$resp = array();
		$this->prepareParameters( 'searchAllergy', array( $this->value ), $this->asPtId );
		$data = $this->makeCall();
		
		foreach( $data as &$vals )
		{
			$temp = array();
			$vals->TransID = explode( '|', $vals->TransID );
			$temp['type']	= $vals->TransID[0];
			$temp['name']= trim($vals->Allergen);
			
			$resp[$vals->TransID[1]] = $temp;
		}
		unset($data);
		return $resp;
	}

	/**
	 * Search Medication in TW
	 * @value = Name of the allergy to be searched
	 * */
	private function medications()
	{
		$data = array();
		$resp = array();
		$this->prepareParameters( 'SearchMeds', array( $this->value, 'Y' ), $this->asPtId );
		$data = $this->makeCall();

		return $data;
	}

	/**
	 * Get dxCode details from Unity API / TW
	 * @value = 
	 * */
	private function dxCode()
	{
		$data = array();
		$resp = array();
		$this->prepareParameters( 'SearchDiagnosisCodes', array( 'Master', $this->value ), $this->asPtId );
		$data = $this->makeCall();

		if(array_key_exists('status', $data[0]))
		{
			throw new asException( 'dxAlert', 'no match' );
		}

		foreach( $data as &$vals)
		{
			$temp = array();
			$temp['description']	= trim($vals->DisplayName);
			$temp['guid']			= trim($vals->ID);
			
			$vals->ICD10DiagnosisCode = trim($vals->ICD10DiagnosisCode);

			if( !array_key_exists($vals->ICD10DiagnosisCode, $resp) )
				$resp[$vals->ICD10DiagnosisCode] = array();
			
			array_push($resp[$vals->ICD10DiagnosisCode], $temp);
		}
		unset($data, $vals);

		return $resp;
	}
	
	/**
	 * Validate SSO token passed from launch button.
	 * @ssoToken = token passed in 'ssotoken' argument.
	 * */
	public function validateToken( $ssoToken )
	{
		if ( !$ssoToken || trim($ssoToken) === '' )
			throw new asException( 'Error', 'Blank SSO token supplied.' );
		
		$this->prepareParameters( 'GetTokenValidation', array( $ssoToken ) );
		return $this->makeCall( true );
	}

	/**
	 * Get TW user ID
	 * */
	public function getUserID( )
	{
		if ( !$this->getEhrUserID() || trim($this->getEhrUserID()) === '' )
			throw new asException( 'Error', 'Blank TW UserID supplied.' );

		$this->prepareParameters( 'GetUserID' );
		return $this->makeCall( true );
	}

	/**
	 * Get TW Logged In Facility Details
	 * @numUserID = TW numeric user ID.
	 * */
	public function getUserSiteInfo( $numUserID )
	{
		$numUserID = (int)$numUserID;

		if ( !$numUserID || $numUserID <= 0 )
			throw new asException( 'Error', 'Invalid TW numeric UserID supplied .' );

		$this->prepareParameters( 'GetUserSiteInfo', array($numUserID) );
		return $this->makeCall( true );
	}
	
	/**
	 * Validate SSO token passed from launch button.
	 * @providerUserName = Touch Works username of the provider
	 * */
	public function getProvider( $providerUserName)
	{
		if ( !$providerUserName || trim($providerUserName) === '' )
			throw new asException( 'Error', 'Please provider username for the provider' );
		
		$this->prepareParameters( 'GetProvider', array( '', $providerUserName ) );
		return $this->makeCall( true );
	}
}