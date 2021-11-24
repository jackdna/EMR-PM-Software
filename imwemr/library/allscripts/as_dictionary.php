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

class as_dictionary extends as_base
{
	public function __construct()
	{
		/*Invoke Parent's Constructor*/
		parent::__construct();
	}
	
	/**
	 * Search Problem
	 * @problem = Problem String
	 **/
	public function problem( $problem )
	{
		if ( $problem == '' )
			throw new asException( 'Call Error', 'Blank Problem name supplied for problem search.' );
		
		$this->prepareParameters( 'SearchProblemCodes', array( 'Master', $problem ) );
		return $this->makeCall();
	}
	
	/**
	 * Get Dictionary
	 * @table = Dictionary table name
	 **/
	private function getDictionary( $table )
	{
		if ( $table == '' )
			throw new asException( 'Call Error', 'Blank table name supplied.' );
		
		$this->prepareParameters( 'GetDictionary', array( $table ) );
		return $this->makeCall();
	}
	
	/**
	 * Retrive Clinical Progress dictionary values
	 * */
	public function clinicalProgress()
	{
		$data = $this->getDictionary( 'Management_Effective_DE' );
		return $data;
	}
	
	/**
	 * Retrive Problem Categories dictionary values
	 * */
	public function problemCategories()
	{
		$data = $this->getDictionary( 'Problem_Category_DE' );
		return $data;
	}
	
	/**
	 * Retrive Clinical Severity dictionary values
	 * */
	public function clinicalSeverity()
	{
		$data = $this->getDictionary( 'Clinical_Severity_DE' );
		return $data;
	}
	
	/**
	 * Retrive Clinical Severity dictionary values
	 * */
	public function problemTypes()
	{
		$data = $this->getDictionary( 'Problem_Type_DE' );
		return $data;
	}
	
	/**
	 * Retrive Allerty Status list
	 * */
	public function allergyStatus()
	{
		$data = $this->getDictionary( 'Allergy_Status_DE' );
		return $data;
	}
	
	/**
	 * Retrive Allergy Category list
	 * */
	public function allergyCategory()
	{
		$data = $this->getDictionary( 'Allergy_Category_DE' );
		return $data;
	}
	
	/**
	 * Retrive Allergen Reaction list
	 * */
	public function allergenReaction()
	{
		$data = $this->getDictionary( 'Allergen_Reaction_DE' );
		return $data;
	}
	
	/**
	 * Retrive List of Allergens
	 * */
	public function allergen()
	{
		$data = $this->getDictionary( 'Allergen_DE' );
		return $data;
	}
	
	/**
	 * Retrive list of Alert Types
	 * */
	public function alertTypes()
	{
		$data = $this->getDictionary( 'Alert_Type_DE' );
		return $data;
	}
	
	/**
	 * Retrive List of acceptable values for problem
	 * */
	function problemStatus()
	{
		$data = $this->getDictionary( 'Problem_Status_DE' );
		return $data;
	}
	
	/**
	 * Retrive list of Relationship Value
	 * */
	function relations()
	{
		$data = $this->getDictionary( 'Relationship_DE' );
		return $data;
	}
	
	/**
	 * Retrieve list of Laterality Qualifier
	 * */
	public function laterality()
	{
		$data = $this->getDictionary( 'Laterality_Qualifier_DE' );
		return $data;
	}
	
	/**
	 * Retrieve list of Encounter Types
	 * */
	public function encoutner_types()
	{
		$data = $this->getDictionary( 'Encounter_Type_DE' );
		return $data;
	}
	
	/**
	 * Retrieve list of DocumentTypes Types
	 * */
	public function document_types()
	{
		$data = $this->getDictionary( 'Document_Type_De' );
		return $data;
	}
}