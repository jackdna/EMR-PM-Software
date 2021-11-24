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
*
****************************************************************************
*
* File: act_exp_open_insu_case.php
* Purpose: Show Insurance history
* Access Type: Direct 
*
****************************************************************************/

require_once("../../../../config/globals.php");
$patient_id = $_SESSION['patient'];

$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);

$return = array('action'=>$action);

$query = "select ins_cas_type.case_name,
									ins_data.type,ins_data.provider,ins_data.actInsComp,ins_data.scan_card,
									ins_data.scan_card2,ins_data.id,ins_data.policy_number,ins_data.copay,ins_comp.name, 
									date_format(ins_data.cardscan_date,'%Y-%m-%d') as cardscan_date,
									date_format(ins_data.cardscan1_datetime,'%Y-%m-%d') as cardscan_date1,
									date_format(ins_data.effective_date,'".get_sql_date_format('','y')."') as effective_date,
									date_format(ins_data.expiration_date,'".get_sql_date_format()."') as expiration_date
									FROM insurance_case as ins_case
									join  insurance_case_types as ins_cas_type 
									on ins_cas_type.case_id = ins_case.ins_case_type
									join insurance_data as ins_data
									on 	ins_data.ins_caseid = ins_case.ins_caseid
									join insurance_companies as ins_comp
									on ins_comp.id = ins_data.provider 	
									where 
									ins_case.patient_id='".$patient_id."' and 	
									ins_case.case_status='Open' 
									order by 
									ins_cas_type.case_name,
									ins_data.type,
									ins_data.actInsComp Desc,
									ins_data.effective_date asc,
									ins_data.type";
$sql = imw_query($query);
$cnt = imw_num_rows($sql);


$html  = '';
$html	.=	'<table class="table table-bordered table-hover table-striped scroll release-table">';
$html	.=	'<thead class="header">';
$html	.=	'<tr class="grythead">';
$html	.=	'<td class="col-xs-1">Case</td>';
$html	.=	'<td class="col-xs-2">Insurance Type</td>';
$html	.=	'<td class="col-xs-2">Provider</td>';
$html	.=	'<td class="col-xs-1">Policy#</td>';
$html	.=	'<td class="col-xs-1">Copay</td>';
$html	.=	'<td class="col-xs-1">Status</td>';
$html	.=	'<td class="col-xs-1">Active Date</td>';
$html	.=	'<td class="col-xs-1">Expiration Date</td>';
$html	.=	'<td class="col-xs-1">Scans</td>';
$html	.=	'<td class="col-xs-1">Scan Date</td>';
$html	.=	'</tr>';
$html	.=	'</thead>';
$html	.=	'<tbody>';

if($cnt > 0 )
{
	$case_name = "";
	while($row = imw_fetch_assoc($sql))
	{
			$image = $image2 = '';
			$scan_card = $row['scan_card'];
			if($scan_card != '')
			{
				$imagePath = data_path() .$scan_card;
				$web_path  = str_replace($_SERVER['DOCUMENT_ROOT'],'',data_path()).$scan_card;
				
				if(file_exists($imagePath) && is_dir($imagePath) == '')
				{
					$ext = pathinfo($imagePath, PATHINFO_EXTENSION);
					$imgPath = data_path(1).'../../library/images/'.$ext.'_small.png';
					$image = '<img onClick="show_scanned(this)" data-src="'.$web_path.'" data-type="'.$ext.'" src="'.$imgPath.'" title="'.ucwords($row['type']).' Scanned Document" >';
					/*$imageSize = getimagesize($imagePath);
					if($imageSize[0]>20){
						$new_size = newImageResize($imagePath,21);
						$image = '<img onClick="show_scanned(this)" data-src="'.$web_path.'" src="'.$web_path.'"  '.$new_size.' title="Primary Scanned Document">';
					}
					else{
						$image = '<img onClick="show_scanned(this)" data-src="'.$web_path.'" src="'.$web_path.'" title="Primary Scanned Document" >';
					}*/
				}
			}
			
			$scan_card2 = $row['scan_card2'];
			if($scan_card2)
			{
				$imagePath2 = data_path() .$scan_card2;
				$web_path2  = str_replace($_SERVER['DOCUMENT_ROOT'],'',data_path()).$scan_card2;
				if(file_exists($imagePath2) && is_dir($imagePath2) == '')
				{
					$ext = pathinfo($imagePath2, PATHINFO_EXTENSION);
					$imgPath = data_path(1).'../../library/images/'.$ext.'_small.png';
					$image2 = '<img onClick="show_scanned(this)" data-src="'.$web_path2.'" data-type="'.$ext.'" src="'.$imgPath.'" title="'.ucwords($row['type']).' Scanned Document" >';
					/*$imageSize2 = getimagesize($imagePath2);
					if($imageSize2[0]>20)
					{
						$new_size2 = imageResize($imageSize2[0],$imageSize2[1],21);
						$image2 = '<img onClick="show_scanned(this)" data-src="'.$web_path2.'" src="'.$web_path2.'" title="Primary Scanned Document" '.$new_size2.'>';
					}
					else
					{
						$image2 = '<img onClick="show_scanned(this)" data-src="'.$web_path2.'" src="'.$web_path2.'" title="Primary Scanned Document">';
					}*/
				}
			}
				
			if($case_name != $row['case_name'] && $case_name!="")
				$html	.=	'<tr><td colspan="10" class="bg-default">&nbsp;</td></tr>';
			
			
			$cardscan_date1 = ($row['cardscan_date'] != '0000-00-00' ? $row['cardscan_date'] : "");
			$cardscan_date2 = ($row['cardscan_date'] != '0000-00-00' ? $row['cardscan_date1'] : "");

			if( $cardscan_date1 && $cardscan_date2) {
				$tmp1 = strtotime($cardscan_date1);
				$tmp2 = strtotime($cardscan_date2);
				$cardscan_date = $cardscan_date1 > $cardscan_date2 ? $cardscan_date1 : $cardscan_date2;
			}
			else if( $cardscan_date1)
				$cardscan_date = $cardscan_date1;
			else 
				$cardscan_date = $cardscan_date1;

			$cardscan_date = $cardscan_date ? get_date_format($cardscan_date) : '';
			
			if( !$image && !$image2 ) $cardscan_date = '';

			$html	.=	'<tr >';
			$html	.=	'<td>'.ucwords($row['case_name']).'</td>';
			$html	.=	'<td>'.ucwords($row['type']).'</td>';
			$html	.=	'<td>'.ucwords($row['name']).'</td>';
			$html	.=	'<td>'.ucwords($row['policy_number']).'</td>';
			$html	.=	'<td>'.ucwords($row['copay']).'</td>';
			$html	.=	'<td>'.($row['actInsComp'] == 1 ? "Active" : "Expired").'</td>';
			$html	.=	'<td nowrap>'.($row['effective_date'] <> '00-00-00' ? $row['effective_date'] : "").'</td>';
			$html	.=	'<td nowrap>'.(get_number($row['expiration_date'])=="000000" ? "" : $row['expiration_date']).'</td>';
			$html	.=	'<td class="pointer" nowrap>'.$image.'&nbsp;'.$image2.'</td>';
			$html	.=	'<td class="pointer" nowrap>'.$cardscan_date.'</td>';
			$html	.=	'</tr>';
			
			$case_name = $row['case_name'];
			
	}

}
else
{
	$html .= '<tr><td colspan="10" class="bg-warning">No Record Found.</td></tr>';	
}
$html	.=	'</tbody>';
$html	.=	'</table>';


$return['html'] = $html;
echo json_encode($return);
?>