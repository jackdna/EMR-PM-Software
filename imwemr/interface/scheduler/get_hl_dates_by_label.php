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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

$obj_scheduler = new appt_scheduler();

$selected_date=$_POST['selected_date'];
$provider_dates=$_POST['provider_dates'];
$provider_id=$_POST['provider_id'];
$selected_facilities=$_POST['selected_facilities'];
$labels_arr=explode(',',trim($_POST['labels']));

$obj_scheduler->get_hl_dates_by_labels($provider_dates,$selected_date,$provider_id,$selected_facilities,$labels_arr);
?>