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
$without_pat = "yes";
require_once("reports_header.php");
extract($_REQUEST);
?>
<html>
	<head>
		<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
        <link rel="stylesheet" href="<?php echo $css_patient;?>" type="text/css">
	</head>
	<body class="body_c">
		<?php
		echo urldecode($prn_header);
		echo urldecode($prn_body);
		?>
		<script>
			window.print();
		</script>
	</body>
</html>