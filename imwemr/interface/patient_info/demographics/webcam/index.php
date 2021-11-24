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
	
	File: index.php
	Purpose: To access webcam
	Access Type: Direct 
*/


include_once("../../../../config/globals.php");
$opreator_id = $_SESSION['authId'];
$patient_id = $_SESSION["patient"];

//Create Directory of Patient and upload image
if(isset($_SESSION['patient']) && !empty($_SESSION['patient']))
{
	$patient_id = $_SESSION['patient'];
	//Patient Directory Name
	$patientDir = "PatientId_".$patient_id."";
	
	//Check
	if(!is_dir($patientDir))
	{
		//Create patient directory
		 mkdir($patientDir, 0777);
	}
}

$DirPath="PatientId_".$patient_id."/";
$mydir_list="";
if (($handle=opendir($DirPath)))
{
$files = array();
$times = array();
 while ($node = readdir($handle))
 {
     $nodebase = basename($node);
     if ($nodebase!="." && $nodebase!="..")
     {
        if(!is_dir($DirPath.$node))
        {
						$pos = strrpos($node,".jpg");
						if($pos===false){ }
						else{
							//export to xml
							$filestat = stat($DirPath.$node);
							$times[] = $filestat['mtime'];
							$files[] = $DirPath.$node;
							//$mydir_list.="<img src=\"".$DirPath.$node."\" />\n";
							array_multisort($times, SORT_NUMERIC, SORT_DESC, $files);
						}
				}
     }
	}
}
foreach($files as $file) { $mydir_list.="<image src=\"".$file."\" />\n"; }
echo $mydir_list;

?>