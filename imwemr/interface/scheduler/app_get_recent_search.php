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
//require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
/*require_once("../chart_notes/common/simpleMenu.php");
$main_search_options_array = core_show_recent_search($_SESSION["authId"]);
$action = $GLOBALS['rootdir']."/main/finder/patient_select.php";
$data = getSimpleMenu($main_search_options_array,"menu_MainSearch","findBy","","","","main_search");
$divData = "<form border=\"0\" method=\"get\" target=\"fmain\" name=\"find_patient\" action=\"$action\"
				style=\"margin:0px;\" onsubmit=\"return chkB4PtSrch();\">
				<input type=\"hidden\" name=\"onSearchTab\" id=\"onSearchTab\">
				<table width=\"248\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
					<tr>
				  	<td width=\"120\" align=\"right\" valign=\"middle\">
				  		<input class=\"search_text_box\" type=\"text\" name=\"patient\" style=\"width:120px;\">
					</td>
					<td width=\"90\">
						<table width=\"90\" align=\"center\" border=\"0\"  bordercolor=\"#993300\"cellpadding=\"0\" cellspacing=\"0\">
							<tr>
								<td width=\"70\">
									<input disabled type=\"text\" size=\"12\" id=\"findByShow\" name=\"findByShow\" class=\"search_drop_down\" value=\"Active\" style=\"width:65px;color:#000000\">
									<input type=\"hidden\" size=\"7\" id=\"findBy\" name=\"findBy\" value=\"Active\" style=\"width:60px;\">
								</td>
								<td align=\"left\">
									$data
								</td>
							</tr>
						</table>
					</td>
				  <td width=\"30\" align=\"left\" valign=\"middle\">&nbsp;<input name=\"image\" type=\"image\" src=\"../../images/search.png\" /></td>
				</tr>
			</table>
		</form>";
echo $divData;
echo "~~~~~~~~~~"; //10 times
echo $patientSearch = core_get_patient_search_controls($_SESSION['authId'],"top.document.getElementById('div_loading_image')","txt_patient_app_name","findBy","../common/core_search_functions.php","hd_patient_id", $from = "scheduler", "<button type=\"button\" class=\"searchbut\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span></button>", "");*/
?>