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

require_once "inc_classes/patient_messages.php"; 
$authId = $_REQUEST['phyId'];
$serviceObj = new patient_messages($_REQUEST['patId']);
$serviceObj->reqModule = $reqModule;
$servicesArr = explode(",",$_REQUEST["service"]);
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
				//-----------start of new changes-------------------
				  if($_REQUEST['app']=='android'){
				   
				   
							   if(empty($responseArray[trim($service)])){
								$responseArray =array_merge(array("Data_status"=>0) ,$responseArray );
							   }
							   else {
								$responseArray =array_merge(array("Data_status"=>1) ,$responseArray );
							   }
				   
				  }
		  //-----------end of new changes-------------------
	}
	else{
		$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
	}	
}
if($_REQUEST['pre'])
pre($responseArray);


$json= json_encode($responseArray);	
$json=str_replace("\/","/",$json); 	
$json=str_ireplace("\\n","<br>",$json);
$json=str_ireplace("\\r","<br>",$json);
$json=str_ireplace("\\t","&#09;",$json);

						
echo $json;		
?>