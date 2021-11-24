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
	//-- DOWNLOAD CSV FILE ---------
	function downloadFiles1($file,$content){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen($content));
		//ob_clean();
		//flush();
		echo($content);
		exit;
	}

	$csv_text=$_POST['csv_text'];
	$csv_file_name=$_POST['csv_file_name'];
	$edi837text=$_POST['edi837text'];
	$self_pay_report=$_POST['self_pay_report'];
	$edi_file_name=$_POST['edi_file_name'];
	if(trim($csv_text) != '' and trim($csv_file_name) != ''){
		$csv_text = htmlentities($csv_text);
		$csv_text=html_entity_decode($csv_text);
		$csv_text=str_replace('&nbsp;', ' ', $csv_text);
		downloadFiles1($csv_file_name,$csv_text);
	}elseif(trim($edi837text) != '' and trim($self_pay_report) == 'yes'){
		$edi837text = htmlentities($edi837text);
		$edi837text=html_entity_decode($edi837text);
		$edi837text=str_replace('&nbsp;', ' ', $edi837text);
		downloadFiles1($edi_file_name,$edi837text);
	}
?>