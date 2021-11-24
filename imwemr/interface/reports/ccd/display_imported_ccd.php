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

//Global File
include("../../../config/globals.php");

header("Content-type: text/xml;");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$str = file_get_contents($_REQUEST['path']);
if($_REQUEST['ccda_type'] == "Ambulatory_CCDA" || $_REQUEST['ccda_type'] == "Inpatient_CCDA" || $_REQUEST['ccda_type'] == "Hitsp_C32_CCD"){
	
	$str = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>
					<?xml-stylesheet type="text/xsl" href="CDA.xsl"?>',$str);
	$str = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8"?>
					<?xml-stylesheet type="text/xsl" href="CDA.xsl"?>',$str);
}else if($_REQUEST['ccda_type'] == "CCR"){
	$str = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>
					<?xml-stylesheet type="text/xsl" href="CCR.xsl"?>',$str);
	$str = str_replace('<?xml version="1.0" encoding="UTF-8"?>','<?xml version="1.0" encoding="UTF-8"?>
					<?xml-stylesheet type="text/xsl" href="CCR.xsl"?>',$str);
}
echo $str;

?>