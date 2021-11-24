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
/*
File: iolink_pdf_page.php
Purpose: Print pdf for iolinks
Access Type: Included
*/
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
//echo $_REQUEST['patentId'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Patient Iolink PDF(s)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript">
self.focus();
</script>
<body>
	<table>
		<tr>
			<td width="20%">
				<iframe name="patientIolinkPdfTree" id="patientIolinkPdfTreeId" src="tree_4_iolink_pdf.php?patentId=<?php echo $_REQUEST['patentId']; ?>" scrolling="auto" height="501px" width="100%" frameborder="1">
				</iframe>
			</td>
			<td width="80%">
				<iframe name="patientIolinkPdfConsent" id="patientIolinkPdfConsentId" width="100%" height="501px" frameborder="1">
				</iframe>
			</td>
		</tr>
	</table>
</body>
</html>
