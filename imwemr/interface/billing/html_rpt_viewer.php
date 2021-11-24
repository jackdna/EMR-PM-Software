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
File: view_reports.php
Purpose: To get reports form clearing house
Access Type: Direct Access
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
$objEBilling 	= new ElectronicBilling();
$report_id		= stripslashes(trim($_GET['report_id']));
$print		= (isset($_GET['print']) && trim($_GET['print'])=='yes') ? 'yes' : 'no';
//--- CHANGE REPORTS STATUS AS READ ----
$objEBilling->MarkReportStatus('comm',$report_id,'1');

$reportDetails = $objEBilling->getEmdeonReport($report_id);
$report_data_db = stripslashes(html_entity_decode(trim($reportDetails['report_data'])));
$report_data_db = str_ireplace('.no-print','.yes-print',$report_data_db);
if($print=='yes')$report_data_db .= '<script type="text/javascript">window.onload = function() { window.print(); }</script>';
die($report_data_db);