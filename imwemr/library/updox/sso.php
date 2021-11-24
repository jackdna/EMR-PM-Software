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
require_once( dirname(__FILE__).'/updoxDirect.php' );
class sso extends updoxDirect{

	
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Get SSO URL
	 * @faxId = Updox fax Id to fetch PDF data
	 * */
	public function getVideoToken()
	{
		$data = array('auth'=>$this->auth);
		
		$data['destinationPage'] = [
			'name' => 'video'
		];
		
		$resp = $this->call('ApplicationOpenPage', $data);

		return $resp;
	}

	public function __get($property)
	{
		if( property_exists($this, $property) )
		{
			return $this->{$property};
		}

		throw new Exception($property.' not found/accessbile');
	}

}

