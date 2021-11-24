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
FILE : more_option.php
PURPOSE : DATA EXPORT FILE
ACCESS TYPE : INCLUDED
*/

include("../../../globals.php");
//print "<pre>";
//print_r($_REQUEST);

if(isset($_REQUEST["serialized_action"]) && $_REQUEST["serialized_action"] != ""){
	switch($_REQUEST["serialized_action"]){
		case "new_window":
			$print_option = "
				<tr height=\"20\">
					<td colspan=\"6\" align=\"right\"><input type=\"button\" value=\"Print\" class=\"dff_button\" name=\"print_butt\" id=\"print_butt\" onMouseOver=\"button_over('print_butt')\" onMouseOut=\"button_over('print_butt','')\" onclick=\"javascript:window.print();\"/>&nbsp;&nbsp;</td>				
				</tr>";
			$header = "
			<html>
				<head>
					<title>imwemr</title>
					<link rel=\"stylesheet\" href=\"".$css_header."\" type=\"text/css\">
					<link rel=\"stylesheet\" href=\"".$css_patient."\" type=\"text/css\">
					<SCRIPT language=\"JavaScript\" src=\"../../common/script_function.js\"></SCRIPT>
				</head>
				<body class=\"body_c\" topmargin=\"0\" leftmargin=\"0\">
					<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">
						".$print_option."
						<tr>
							<td id=\"selection_criteria\" colspan=\"4\" class=\"text_10\" valign=\"top\">".stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_condition"])))."</td>
							<td id=\"selection_condition\" colspan=\"2\" class=\"text_10\" valign=\"top\">".stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_cond_criteria"])))."</td>
						</tr>						
					</table>";
			$footer = "
				</body>
			</html>";
			echo $header.stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_output"]))).$footer;
			die;
		break;
		case "print_window":
			
		//getting page header margin
		$str_condition = stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_condition"])));

		$arr_condition = explode("<br>",$str_condition);
			
		$int_margin_factor = 1;
		if(is_array($arr_condition)){
			$int_margin_factor = count($arr_condition);
		}
			$header = "
			<style>
				.text_b_w{					
					text-decoration: none;	
					color: #FFFFFF;	
					font-size: 12px; 
					font-weight:bold;
					background-color:#4684ab;
					padding-left:5px;
					text-align:left;
				}
				.text_10{					
					font-size:12px;
					color:#333333;
					padding-left:5px;
					text-align:left;
				}
			</style>
			<page backtop=\"35mm\" backbottom=\"10mm\">			
			<page_footer>
				<table style=\"width: 100%;\">
					<tr>
						<td style=\"text-align:center;width:100%\">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
			<table style=\"width: 100%;\">
				<tr>
					<td id=\"selection_criteria\" style=\"width:80%\" class=\"text_10\"	valign=\"top\">".stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_condition"])))."</td>
					<td id=\"selection_condition\" style=\"width:80%\" class=\"text_10\"	valign=\"top\">".stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_cond_criteria"])))."</td>
				</tr>
			</table>
			</page_header>";
			$footer = "
			</page>";
			
			$strHTML = $header.stripslashes(html_entity_decode(urldecode($_REQUEST["serialized_output"]))).$footer;
			if(trim($strHTML) != ""){
				$fp = fopen('../../common/new_html2pdf/pdffile.html','w');
				$intBytes = fputs($fp,$strHTML);
				fclose($fp);
				if($intBytes !== false){
					?>
					<html>
						<body>
							<form name="frm_print_window" action="../../common/new_html2pdf/createPdf.php" method="get" target="_self">
								<input type="hidden" name="op" value="l">
							</form>
							<script type="text/javascript">								
								document.frm_print_window.submit();
							</script>
						</body>
					</html>
					<?php
				}
			}
		break;
		case "csv_export_window":			

			include_once("../../admin/schedular/common/schedule_functions.php");
			
			$arr_data = unserialize(html_entity_decode(urldecode($_REQUEST["serialized_data"])));
			$label_val="";
			$delim=", ";
			
			if(is_array($arr_data) && count($arr_data) > 0){
				foreach($arr_data as $this_row){					
					$label_val .= addDoubleQuaote($this_row["patient_name_id"]).$delim;
					$label_val .= addDoubleQuaote($this_row["patient_dob"]).$delim;
					$label_val .= addDoubleQuaote($this_row["patient_address"]).$delim;
					$label_val .= addDoubleQuaote($this_row["patient_phone"]).$delim;
					$label_val .= addDoubleQuaote($this_row["patient_dos"])."\n";		
				}
			}

			$filename = 'clinical_report.txt';
			$handle = @fopen($filename,"w+");
			@fwrite($handle,$label_val);
			@fclose($handle);

			$content_type = "application/force-download";
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Description: File Transfer");
			header("Content-Type: ".$content_type."; charset=utf-8");
			header("Content-disposition:attachment; filename=\"".$filename."\"");
			header("Content-Length: ".@filesize($filename));
			@readfile($filename) or die("File not found.");
			exit;
		break;
	}
}
?>