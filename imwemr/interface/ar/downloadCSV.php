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
PURPOSE : FORCE CSV DOWNLOAD
ACCESS TYPE : INCLUDED
*/
$file=$_POST['file'];
if($_POST['file_format']=='csv'){
	
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename="'.basename($file).'"');
	header("Content-length: " . filesize($file));  
	readfile($file);
	exit;

}else{

	header('Content-type: application/zip');
	header('Content-Disposition: attachment; filename="'.basename($file));
	header("Content-length: ".filesize($file));  
	readfile($file);
	exit;
}?>
