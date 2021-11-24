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
require_once "inc_classes/imedic_monitor.php";
$authId = $_REQUEST['phyId'];
$serviceObj = new imedic_monitor();
$serviceObj->reqModule = $reqModule;
$servicesArr = explode(",",$_REQUEST["service"]);
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
		//-----------start of new changes-------------------
		if($_REQUEST['app']=='android'){
			
			
			if(!empty($responseArray[trim($service)])){
				$responseArray["Data_status"]=1;
				$responseArray =array_merge(array("Data_status"=>1) ,$responseArray );
			}
			else {
				$responseArray =array_merge(array("Data_status"=>0) ,$responseArray );
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


$responseArray=json_encode($responseArray);
$responseArray = str_replace("\/","/",$responseArray);
echo $responseArray;	
?>