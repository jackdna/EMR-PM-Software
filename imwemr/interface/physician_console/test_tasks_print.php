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
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php'); //to get save location
include_once($GLOBALS['fileroot'] . '/library/classes/common_function.php'); //function to write html

$msgConsoleObj = new msgConsole();
$result_data_arr = $msgConsoleObj->get_tests_tasks('tests');
$tableElem = '';
if (count($result_data_arr) > 0) {
    $tableElem = '<table width="750" >
							<thead>
                                <tr>
									<th class="tb_heading alignLeft pdl5" style="width:750px;" colspan="4">Tests/Tasks</th>
								</tr>
								<tr>
									<th width="150">DOS</th>
									<th width="200">Patient Name</th>
									<th width="200">Test Name</th>
									<th width="200">Comments</th>
								</tr>
							</thead>
							<tbody>
						';
    $ar_pt_info = array();
    foreach ($result_data_arr as $key => $val_arr) {

        $pt_id = "";
        if (isset($val_arr['patient_id'])) {
            $pt_id = $val_arr['patient_id'];
        } else if (isset($val_arr['patientId'])) {
            $pt_id = $val_arr['patientId'];
        }
        if (empty($pt_id)) {
            continue;
        }

        if (isset($ar_pt_info[$pt_id])) {
            $pt_arr = $ar_pt_info[$pt_id];
        } else {
            $pt_arr = $msgConsoleObj->get_patient_more_info($pt_id, " concat(lname,', ',fname,' ',mname,' - ',id) as patient_name, providerID ");
            $ar_pt_info[$pt_id] = $pt_arr;
        }

        if (!$pt_arr || count($pt_arr) <= 0) {
            continue;
        }

        $pt_name = $pt_arr["patient_name"];
        $pt_prov_id = $pt_arr["providerID"];

        $ordrby = $val_arr['ordrby'];
        if (empty($ordrby)) {
            if (!empty($pt_prov_id) && $pt_prov_id != $msgConsoleObj->operator_id) {
                continue;
            }
        }

        if (trim($val_arr['comments']) == "!~!") {
            $val_arr['comments'] = "";
        }
        $comments= wordwrap($val_arr['comments'], 30, "<br />\n", true);
        $val_arr['test_table_name'] = $val_arr['testName'];
        if ($val_arr['testName'] == 'discexternal')
            $val_arr['test_table_name'] = 'disc_external';

        $tableElem .= '<tr>
											<td>' . $val_arr['taskDate'] . '</td>
											<td>' . $pt_name . '</td>
											<td>' . strtoupper($val_arr['testDesc']) . '</td>																						
											<td>' . $comments . '</td>																						
										</tr>';
    }
}

$tableElem .= '</tbody></table>';	

$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
$strHTML = $stylePDF.$tableElem;
$print_file_name = "test_tasks_" . $_SESSION["authId"];
$file_location = write_html($strHTML, $print_file_name . ".html");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript">
    top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
    top.html_to_pdf('<?php echo $file_location; ?>', 'p', '', true, false);
</script>

