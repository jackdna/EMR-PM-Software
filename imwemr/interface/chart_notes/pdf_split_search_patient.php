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
include("../../config/globals.php");

if($_GET['getPtInfoAjax']=='yes') {
	if($_GET['ptId']) {
		$ptIdAjaxQry = "SELECT pid,fname,lname FROM patient_data WHERE pid='".$_GET['ptId']."'" ;
		$ptIdAjaxRes = imw_query($ptIdAjaxQry) or die(imw_error());	
		if(imw_num_rows($ptIdAjaxRes)>0) {
			$ptIdAjaxRow = imw_fetch_array($ptIdAjaxRes);	
			$patientNameAjax = $ptIdAjaxRow['lname'].', '.$ptIdAjaxRow['fname'];
			$ptNameIdAjax = $patientNameAjax.' - '.$ptIdAjaxRow['pid'];
			echo $ptNameIdAjax;
		}else {
			echo "patient not exist";	
		}
	}
	exit();
}
?>