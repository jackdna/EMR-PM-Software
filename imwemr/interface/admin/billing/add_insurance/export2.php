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
require_once("../../../../config/globals.php");
$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/ins.csv';
$fileInfo = pathinfo($filename);
if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
$fp1=fopen($filename,'w');
$ins_group=array();
$insg_sql="select id,title from ins_comp_groups where delete_status=0";
$insg_rs=imw_query($insg_sql);
while($ins_row=imw_fetch_assoc($insg_rs))
{
	$ins_group[$ins_row['id']]=trim($ins_row['title']);
}
$query="select name,in_house_code,groupedIn,contact_address,insurance_Practice_Code_id,City,State,zip,email,	Insurance_payment,secondary_payment_method ,claim_type,ins_del_status,Payer_id,Phone   from insurance_companies where name != '' order by name asc";
$result=imw_query($query)or die(imw_error());
$data=array();
$data_head[]=array('Company Name','Practise Code','Insurance Group','contact_address','Practice Group Id','City','state','zip','email','Primary payment','Secondary payment','Claim Type','Status','Payer id','Phone');
while($row=imw_fetch_assoc($result))
{
	if($row['groupedIn']!='0' && array_key_exists($row['groupedIn'], $ins_group)) {
        $row['groupedIn']=$ins_group[$row['groupedIn']];
    }else {
        $row['groupedIn']='';
    }
	$data[]=$row;
}

foreach ($data_head as $fields1)
{
	fputcsv($fp1, $fields1);
}
foreach ($data as $fields)
{
	fputcsv($fp1, $fields);
}
fclose($fp1);
$csv_text = file_get_contents($filename);
// echo $csv_text;+
 header("Pragma: public");
 header("Expires: 0");
 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
 header("Cache-Control: private",false);
 header("Content-Description: File Transfer");
 header("Content-Type: application/octet-stream;");
 header("Content-disposition:attachment; filename=\"".$fileInfo['basename']."\"");
 header("Content-Length: ".@filesize($filename));
 @readfile($filename) or die("File not found.");
 unlink($filename);
 exit;
?>