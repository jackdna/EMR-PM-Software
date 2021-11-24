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
include_once(dirname(__FILE__)."/../../config/globals.php");

//$login->app_login_process("", $p_w, true);
$_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];
		
if(isset($_POST["suP"]) && !empty($_POST["suP"])){ //POST used for security reasons only, please use $this->query_string instead
	if($this->app_login_process("", $_POST["suP"], true) !== false){
		$_SESSION["sess_user_switched"] = "";
		die("OK");
	}else{
		$_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];
		die("Incorrect Password.");
	}
}

?>