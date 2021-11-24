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

include_once("../../../config/globals.php");
$medName = trim(urldecode($_REQUEST['medName']));
$index = "";
if(isset($_REQUEST['index'])){
	$index = trim(urldecode($_REQUEST['index']));
}

$sql = "SELECT * FROM ".constant('UMLS_DB').".rxnconso WHERE LOWER(STR) LIKE '%".strtolower($medName)."%' AND SAB = 'RXNORM' order by STR asc";
$res = imw_query($sql);
$i=0;
while($row = imw_fetch_assoc($res))
{
	$sel="";
	$i++;
	$str_umls .='
		<tr>
			<td>
				<div class="radio">
					<input type="radio" value="'.$row["STR"].'" name="rxnorm_id" id="rxnorm_id'.$i.'" onChange="fill_med_code(\''.addslashes($row["STR"]).'\',\''.addslashes($row["RXCUI"]).'\',\''.addslashes($index).'\')">
					<label for="rxnorm_id'.$i.'">&nbsp;</label>
				</div>			
			</td>
			<td >'.$row["STR"].'</td>
			<td >'.$row["RXCUI"].'</td>	
		</tr>';
}


if($str_umls != ""){
	$html  = '';
	$html	.=	'<table class="table table-bordered table-hover table-striped scroll release-table ">';
	$html	.=	'<thead class="header">';
	$html	.=	'<tr class="grythead">';	
	$html	.=	'<th class="col-xs-2">&nbsp;</th>';
	$html	.=	'<th class="col-xs-5">Drug Name</th>';
	$html	.=	'<th class="col-xs-5">RxNorm</th>';
	$html	.=	'</tr>';
	$html	.=	'</thead>';
	$html	.=	'<tbody>';
	$html .= $str_umls;
	$html	.=	'</tbody>';
	$html	.=	'</table>';
}
echo $html;
?>