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
File: search_physician.php
Purpose: Handle Ajax for search physician 
Access Type: Direct 
*/

require_once("../../../config/globals.php");
require_once("../../../library/classes/cls_common_function.php"); 
$OBJCommonFunction = new CLSCommonFunction;

$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);

$return = array('action'=>$action);

$searchBy = empty($searchBy) ? 'LastName' : $searchBy;
if($searchBy=='physician_phone' && core_phone_format($val)!='')$val= ( str_ireplace("-", "", $val).'%" OR physician_phone LIKE "'.core_phone_format($val) );
if($searchBy=='physician_fax' && core_phone_format($val)!='')$val= ( str_ireplace("-", "", $val).'%" OR physician_fax LIKE "'.core_phone_format($val) );
$fields = 'Title,LastName,physician_Reffer_id,Address1,Address2,City,ZipCode,PractiseName,physician_phone,physician_fax';
$result = get_array_records('refferphysician','1','1',$fields, ' AND '.$searchBy.' LIKE "'.$val.'%" AND delete_status = 0 ', 'FirstName');
	
$html  = '';
$html	.=	'<table class="table table-bordered table-hover table-striped scroll release-table">';
$html	.=	'<thead class="header">';
$html	.=	'<tr class="grythead">';	
$html	.=	'<th class="col-xs-2">Name</th>';
$html	.=	'<th class="col-xs-3">Address</th>';
$html	.=	'<th class="col-xs-2">Practice Name</th>';
$html	.=	'<th class="col-xs-2">Phone Number</th>';
$html	.=	'<th class="col-xs-2">Fax Number</th>';
$html	.=	'<th class="col-xs-1">ID</th>';
$html	.=	'</tr>';
$html	.=	'</thead>';
$html	.=	'<tbody>';
			
if(is_array($result) && count($result) > 0 )
{
	foreach($result as $key => $row)
	{
		$row		=	array_map('trim',$row);
		$title	= ($row['Title']) ? $row['Title'] : 'Dr.';	
		$name 	= $row['LastName'].', ';
		$name		= trim($OBJCommonFunction->get_ref_phy_name($row['physician_Reffer_id']));
		
		$addr		=	'';
		$addr	 .=	$row['Address1'];
		$addr	 .=	($addr ? ', ' : '').$row['Address2'];
		$addr	 .=	($addr ? ', ' : '').$row['City'];
		$addr	 .= ' '.$row['ZipCode'];
		$addr	  =	trim($addr);
		
		$ref_id	= $row['physician_Reffer_id'];
		
		$attr	 =	'data-click="pick_physician" data-text-box="'.$textBox.'" data-id-box="'.$idBox.'" data-name="'.$name.'" data-ref-id="'.$ref_id.'"';			
		
		$html	.=	'<tr>';
		$html	.=	'<td data-label="Name"><a '.$attr.'>'.$name.'</a></td>';
		$html	.=	'<td data-label="Address"><a '.$attr.'>'.$addr.'</a></td>';
		$html	.=	'<td data-label="Practice Name"><a '.$attr.'>'.$row['PractiseName'].'</a></td>';
		$html	.=	'<td data-label="Phone Number"><a '.$attr.'>'.$row['physician_phone'].'</a></td>';
		$html	.=	'<td data-label="Fax Number"><a '.$attr.'>'.$row['physician_fax'].'</a></td>';
		$html	.=	'<td data-label="ID"><a '.$attr.'>'.$ref_id.'</a></td>';
		$html	.=	'</tr>';
	
	}
}
else
{
	$html .= '<tr><td colspan="6" class="text-center">No results found</td></tr>';
}
	
$html	.=	'</tbody>';
$html	.=	'</table>';
$return['result'] = $result;		
$return['html'] = $html;
$return['text_box']	= $textBox;
$return['id_box']	= $idBox;

echo json_encode($return);
?>