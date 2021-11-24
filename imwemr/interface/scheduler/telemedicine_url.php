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

require_once(dirname(__FILE__).'/../../config/globals.php');

if( !is_updox('telemedicine') )
{

	exit( json_encode([
		'message' => 'Telemedicine functionality is not allowed to the logged in user'
	]));
}


require_once($GLOBALS['srcdir'].'/updox/sso.php');

try{
	$sso = new sso;
	$response = $sso->getVideoToken();

	if( $response['status'] !== 'success' || empty($response['data']->token) )
	{
		exit( json_encode([
			'message' => 'Unable to retrieve SSO token from updox'
		]));
	}


	$baseUrl = $sso->apiURL;
	$baseUrl = preg_replace('/(https?:\/\/.*\..*?)\/.*/i', '$1', $baseUrl);

	$applicationId = $sso->applicationId;

	$ssoUrl = $baseUrl.'/sso/applicationOpenPage/'.$applicationId.'/'.$response['data']->token;

	exit( json_encode([
		'ssoUrl' => $ssoUrl
	]));

}
catch(Exception $e)
{
	exit( json_encode([
		'message' => 'Error in SSO token generation'
	]));
}
