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
FILE : donwload_zip.php
PURPOSE : For downloding of ZIP for CCD report.
ACCESS TYPE : Direct
*/

$zipfilename = $_GET["fileName"];
$zip_file_name=end(explode("/",$zipfilename));
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zip_file_name);
header('Content-Length: ' . filesize($zipfilename));
readfile($zipfilename);

?>

