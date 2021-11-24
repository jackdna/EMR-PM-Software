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
require_once("../../config/globals.php");
require_once("../../library/classes/ChartTestPrev.php");
$patient_id = $_SESSION["patient"];

$elem_testId 			= $_POST["elem_testId"];
$elem_examTime 			= $_POST["elem_examTime"];
if(empty($elem_examTime)) $elem_examTime = "0000-00-00 00:00:00";
$elem_examDate 			= getDateFormatDB($_POST["elem_examDate"]);
$elem_examNM 			= $_POST["elem_examNM"];
$elem_dir 				= $_POST["elem_dir"];
$elem_flgComp 			= $_POST["elem_flgComp"];
$test_template_id 		= $_POST["template_id"];

$oChartTestPrev = new ChartTestPrev($patient_id,$elem_examNM,$test_template_id);
$str = $oChartTestPrev->getPrevval($elem_examDate,$elem_examTime,$elem_testId,$elem_dir,$elem_flgComp);
print_r($str);

?>