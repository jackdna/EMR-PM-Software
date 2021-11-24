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
require_once (dirname(__FILE__).'/../../library/classes/landing_page.php');

switch($_SESSION['logged_user_type']){
	case '1':
		include(dirname(__FILE__)."/landing_physician.php");//
		break;
	case '3':
		include(dirname(__FILE__)."/landing_technician.php");
		break;
	case '13':
		include(dirname(__FILE__)."/landing_technician.php");
		break;
	case '18':
		include(dirname(__FILE__)."/landing_billing.php");
		break;
	default:
		include(dirname(__FILE__)."/landing_default.php");
}
?>