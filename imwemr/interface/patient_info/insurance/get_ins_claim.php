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
require_once(dirname(__FILE__).'/../../../config/globals.php');
if($_GET['insCompanyId']){
	$qryCheckInsCompClaim="Select claim_type,ins_accept_assignment from insurance_companies where id=".$_GET['insCompanyId'];
	$resCheckInsCompClaim=imw_query($qryCheckInsCompClaim)or die(imw_error());
	$rowCheckInsCompClaim=imw_fetch_assoc($resCheckInsCompClaim);
	echo $insCompClaim=$rowCheckInsCompClaim['claim_type'].'~~~'.$rowCheckInsCompClaim['ins_accept_assignment'];
}
?>