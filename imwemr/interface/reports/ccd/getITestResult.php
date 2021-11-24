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
FILE : getlTestResult.php
PURPOSE : Sending CURL request.
ACCESS TYPE : Indirect
*/

//Global File
include("../../../config/globals.php");
$rqPId = (int)$_REQUEST['pId'];
$rqType = $_REQUEST['type'];
$rqFileName = $_REQUEST['fileName'];
$rqENCSHA = $_REQUEST['encSHA'];
$rqENCKey = $_REQUEST['ENCKey'];
$intCurrentUser = $_SESSION['authId'];
$currentUser = $_SESSION['authProviderName'];
$currentUser = str_replace(", ","-",$currentUser);
$currentUser = str_replace(" ","--",$currentUser);
$rqPlainSHA = $_REQUEST['plainSHA'];
$rqPatName = $_REQUEST['patName'];
$rqPatName = str_replace(", ","-",$rqPatName);
$rqPatName = str_replace(" ","--",$rqPatName);
$intITestID = 0;
$strITestFilename = "";
if(empty($rqFileName) == false && isset($_REQUEST['fileName']) && trim($rqFileName) != "ccda_r2_xml.xml" && $rqType == "ENC"){
	$strITestFilename = "temp/".$rqFileName;	
}
elseif(empty($rqFileName) == false && isset($_REQUEST['fileName']) && trim($rqFileName) != "ccda_r2_xml.xml" && $rqType == "PLAIN"){
	$strITestFilename = "temp/".$rqFileName;	
}
if(file_exists($strITestFilename) == true){	
	$dir = explode('/',$_SERVER['HTTP_REFERER']);
	//print_r($_SERVER);
	//die;
	$httpPro = $dir[0];
	$httpHost = $dir[2];
	$httpfolder = $dir[3];	
	
	if($rqType == "ENC"){
		$iTestHTTPAddress = $httpPro."//".$myExternalIP."/iTest/post_test.php?patId=".$rqPId."&ENCSHA=".$rqENCSHA."&ENCKey=".$rqENCKey."&cUser=".$currentUser."&type=".$rqType."&patName=".$rqPatName."";
	}
	elseif($rqType == "PLAIN"){
		$iTestHTTPAddress = $httpPro."//".$myExternalIP."/iTest/post_test.php?patId=".$rqPId."&PLAINSHA=".$rqPlainSHA."&cUser=".$currentUser."&type=".$rqType."&patName=".$rqPatName."";
	}
	$postedFilePath = $_SERVER['DOCUMENT_ROOT']."/".$web_RootDirectoryName."/interface/reports/ccd/".$strITestFilename;
	$file_to_upload = array('file_contents'=>'@'.$postedFilePath);	
	//echo $iTestHTTPAddress;
	//die;
	$cur = curl_init();	
	$url = $iTestHTTPAddress;	
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt($cur,CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($cur,CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur,CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($cur,CURLOPT_POST,1); 
	curl_setopt($cur,CURLOPT_POSTFIELDS, $file_to_upload); 
	echo $data = curl_exec($cur);
	curl_close($cur);				
	
	
	
	/*if($rqType == "ENC"){
		$qryInsertITestData = "insert into itest patient_id,enc_sha2_imedic,enc_key_enc_file,tested_by,current_date_time VALUES	 
								('".$rqPId."','".$rqENCSHA."','".$rqENCKey."','".$intCurrentUser."',NOW)";
		$rsInsertITestData = mysql_query($qryInsertITestData);
		if($rsInsertITestData){
			$intITestID = mysql_insert_id();
		}						
		$ENCCCD = file_get_contents($strITestFilename);
		if((int)$intITestID > 0){
			
		}
	}*/
}

?>