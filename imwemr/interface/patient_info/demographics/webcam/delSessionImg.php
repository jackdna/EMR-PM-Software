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
?>
<?php
/*	
File: delSessionImg.php
Purpose: To delete session image
Access Type: Direct 
*/
include_once("../../../../config/globals.php");
$upload_dir = '../../../../data/'.constant('PRACTICE_PATH');
if(isset($_REQUEST["del_path"]) && !empty($_REQUEST["del_path"])){
	if(file_exists($upload_dir.$_REQUEST["del_path"])){
		unlink($upload_dir.$_REQUEST["del_path"]);
	}
}
?>