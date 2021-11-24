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
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);// HTTP/1.0header("Pragma: no-cache"); 
//files commented due to no use found
//include_once("Function.inc.php");
//include("common/schedule_functions.php");

if($_REQUEST['p'])
{
	$str='';
	$phy=true;
	$tesetPhy=true;
	
	$pro_id=0;
	//get user list that are providers
	$qryPro=imw_query("select id from users where user_type =1 and id IN($_REQUEST[p])")or die(imw_error());
	if(imw_num_rows($qryPro)==1)
	{
		$proObj=imw_fetch_object($qryPro);
		$str.= $proObj->id;
		$phy=true;
	}
	
	//get user list that are testing providers
	$qryPro=imw_query("select id from users where user_type =5 and id IN($_REQUEST[p])")or die(imw_error());
	if(imw_num_rows($qryPro)==1)
	{
		$proObj=imw_fetch_object($qryPro);
		$str.= ','.$proObj->id;
		$tesetPhy=true;
	}
	
	if($phy==true && $tesetPhy==true)
	{
		echo $str;	
	}
	
}
?>