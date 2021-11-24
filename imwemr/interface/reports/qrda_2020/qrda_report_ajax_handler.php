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
  FILE : qrda_report_ajax_handler.php
  PURPOSE : Show QRDA C1 Initial Patient Population
  ACCESS TYPE : Direct
 */
include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once(dirname(__FILE__)."/qrda.php");

$performance_year = ($_REQUEST['performance_year']) ?? NULL;
$performance_year = ($performance_year) ?? date('Y');

$objMUR			= new qrda($performance_year);
$action			= trim(strip_tags($_GET['action']));
$task			= trim(strip_tags($_GET['task']));
$mur_version	= trim(strip_tags($_GET['mur_version']));


switch($action){
	case 'get_report_html':{
	    // include_once(dirname('__FILE__')."/mur_2016.php");
	    include_once(dirname('__FILE__')."/qrda_list.php");
	}
	case 'get_audit_patients':{
		$html = '';
		$comma_patients			= trim(strip_tags($_POST['comma_patients']));
		$q1 = "SELECT id,fname,lname,mname,DATE_FORMAT(DOB,'%m-%d-%Y') as DOB,sex FROM patient_data WHERE id IN ($comma_patients)";
		$res1 = imw_query($q1);
		if($q1 && imw_num_rows($res1)>0){
			$html .= '
			<table class="table table-striped">
			<thead>
				<tr>
					<th>Patient Name - ID</th>
					<th>D.O.B/Gender</th>
				</tr>
			</thead>
			<tbody>
			';
			while($rs1 = imw_fetch_assoc($res1)){
				$html .= '
				<tr>
					<td>'.$rs1['fname'].' '.$rs1['lname'].' - '.$rs1['id'].'</td>
					<td>'.$rs1['DOB'].' / '.$rs1['sex'].'</td>
				</tr>				
				';
			}
			$html .= '
			</tbody>
			</table>
			';
		}
		else{
			
		}
		echo $html;
		break;	
	}
	default:{
		die('TASK= '.$action);
	}
	
}

?>