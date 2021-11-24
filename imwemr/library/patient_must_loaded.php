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

?><?php
/*
File: patient_must_loaded.php
Purpose: Redirect control to default landing page; if patient is not in session
Access Type: Direct Access (in frame)
*/
@ob_start();
require_once(dirname(__FILE__).'/../config/globals.php');
if(!isset($_SESSION["patient"]) || empty($_SESSION["patient"]) || $_SESSION["patient"]=='0'){
	if(!isset($window_popup_mode) || !$window_popup_mode){
		echo  '<script type="text/javascript">top.document.getElementById("fmain").src="'.$GLOBALS['webroot'].'/interface/landing/index.php";</script></body></html>';
	}else if($window_popup_mode){
		echo  '<script type="text/javascript">window.close();</script></body></html>';
	}
	exit;
}
@ob_end_clean();
?>