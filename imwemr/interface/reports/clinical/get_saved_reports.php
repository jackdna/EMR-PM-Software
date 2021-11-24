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
FILE : get_saved_reports.php
PURPOSE : GET SAVED REPORT DATA 
ACCESS TYPE : INCLUDED
*/

include("../../../config/globals.php");
$dateFormat= get_sql_date_format();

$qry = "select cr_id, date_format(cr_created_on, '".$dateFormat."') as cr_created_on, cr_report_name, cr_report_query_string from clinical_report_history where cr_created_by = '".$_SESSION["authId"]."'";
$sel = imw_query($qry);
$disabled = "";
$str_html_inner = "";

if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "div"){
	if(imw_num_rows($sel) > 0){
		$cnt = 1;
		while($row = imw_fetch_array($sel)){
			$class = "";
			if($cnt % 2 == 0){
				$class = "bgColor";
			}
			$str_html_inner .= "<tr height=\"25\" class=\"".$bgColor."\"><td width=\"60%\" class=\"text_10b\" onclick=\"javascript:generate_quick_report('".$row["cr_report_query_string"]."');\">".$row["cr_report_name"]."</td> 
			<td width=\"30%\" class=\"text_10\" onclick=\"javascript:generate_quick_report('".$row["cr_report_query_string"]."');\">".$row["cr_created_on"]."</td> 
			<td width=\"10%\" class=\"text_10\"><span class=\"glyphicon glyphicon-remove pointer\" onclick=\"top.fmain.delete_report('".$row["cr_id"]."');\"></span></td> </tr>";
			$cnt++;
		}
	}else{
		$str_html_inner = "<tr><td colspan=\"3\" class=\"text_10\">No Saved report found.</td></tr>";
	}
	$sv_rp_height = $_SESSION['wn_height']-500;
	$str_html = "<div style=\"height:".$sv_rp_height."px; overflow:auto\">
	<table width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" style=\"border-collapse:collapse;\" class=\"white\">
	<tr class=\"div_popup_heading\" height=\"25\">
		<td width=\"60%\">Report Name</td> 
		<td width=\"30%\">Saved on</td> 
		<td width=\"10%\">&nbsp;</td>
	</tr>
	";
	$str_html .= $str_html_inner;
	//$str_html .= "</table></div><div align=center class=\"padd5\"><input type=\"button\" value=\"Close\" class=\"dff_button\" name=\"print_butt2\" id=\"print_butt2\" onclick=\"javascript:document.getElementById('show_saved_report_type').style.display='none';\"/></div>";
}else{
	if(imw_num_rows($sel) > 0){
		while($row = imw_fetch_array($sel)){ 
			$str_html_inner .= "<option value=\"".$row["cr_report_query_string"]."\">".$row["cr_report_name"]."</option>";
		}
	}else{
		$disabled = "disabled";
	}
	$str_html = "<select ".$disabled." name=\"selOption\" class=\"form-control minimal\" onchange=\"javascript:generate_quick_report(this.value);\"><option value=\"\"></option>";
	$str_html .= $str_html_inner;
	$str_html .= "</select>";
}

echo $str_html;
?>