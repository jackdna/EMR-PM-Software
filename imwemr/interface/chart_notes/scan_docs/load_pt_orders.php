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
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
$ordersQry = "select order_file_content from print_orders_data 
			where print_orders_data_id = '".$print_orders_data_id."'";
$ordersQryRes = get_array_records_query($ordersQry);
$order_file_content = $ordersQryRes[0]['order_file_content'];
$file_path = write_html(html_entity_decode($order_file_content));
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Pt.Docs</title>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
		<script type="text/javascript">
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			html_to_pdf('<?php echo $file_path; ?>','p','',true);
        </script>
    </head>
</html>
