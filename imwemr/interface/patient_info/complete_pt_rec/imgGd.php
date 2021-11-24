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

if(!empty($_GET["id"]))
{
	include_once("../../../config/globals.php");
}

include_once('imgGdFun.php');
////
// id,tbl,pixelField,idField,imgName
////

	//Set values
	$pixels = "";		
	
	//Get Values
	if(!empty($_GET["id"]))
	{
		$id = $_GET["id"];
		$tbl = $_GET["tbl"];
		$pixelField = $_GET["pixelField"];
		$idField = $_GET["idField"];
		$imgName = $_GET["imgName"];
		$saveImg = $_GET["saveImg"];
		
	}else{	
		$id = $id;
		$tbl = $tblName;
		$pixelField = $pixelFieldName;
		$idField = $idFieldName;
		$imgName = $imgPath;
		$saveImg = $saveImg;
	}
	
	if(!empty($id)){
		$qry = imw_query("SELECT $pixelField FROM $tbl WHERE $idField = $id");		
		$row = imw_fetch_array($qry);	
		$pixels = $row[$pixelField];
	}
	
	//Get Image	
	drawOnImage_new($pixels,$imgName,$saveImg); 


?>
