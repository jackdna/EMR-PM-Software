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
/*require_once "inc_classes/chart_notes.php";
$authId = $_REQUEST['phyId'];
$serviceObj = new chart_notes($_REQUEST['patId']);
$serviceObj->reqModule = $reqModule;
$servicesArr = explode(",",$_REQUEST["service"]);
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
	}
	else{
		$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
	}	
}
if($_REQUEST['pre'])
pre($responseArray);
echo json_encode($responseArray);	
die();*/
//require_once "../interface/globals.php";

include_once("../../config/globals.php");
define("PTHISTORY", "The Patient has a history of");
$service = trim($_REQUEST["service"]);	
switch($service)
{
	case "ocular":
		require_once "inc_classes/ocular.php";
		$authId = $_REQUEST['phyId'];
		$serviceObj = new ocular($_REQUEST['patId']);
		$serviceObj->reqModule = $reqModule;
		$servicesArr = explode(",",$_REQUEST["action"]);
	break;
	case "gen_health":
		require_once "inc_classes/gen_health.php";
		$authId = $_REQUEST['phyId'];
		$serviceObj = new gen_health($_REQUEST['patId']);
		$serviceObj->reqModule = $reqModule;
		$servicesArr = explode(",",$_REQUEST["action"]);
	break;
	case "medication":
		require_once "inc_classes/medication.php";
		$authId = $_REQUEST['phyId'];
		$serviceObj = new medication($_REQUEST['patId']);
		$serviceObj->reqModule = $reqModule;
		$servicesArr = explode(",",$_REQUEST["action"]);
	break;
	case "allergies":
		require_once "inc_classes/allergies.php";
		$authId = $_REQUEST['phyId'];
		$serviceObj = new allergies($_REQUEST['patId']);
		$serviceObj->reqModule = $reqModule;
		$servicesArr = explode(",",$_REQUEST["action"]);
	break;
	case "problem_list":
		require_once "inc_classes/problem_list.php";
		$authId = $_REQUEST['phyId'];
		$serviceObj = new problem_list($_REQUEST['patId']);
		$serviceObj->reqModule = $reqModule;
		$servicesArr = explode(",",$_REQUEST["action"]);
	break;
	
}

if(count($servicesArr)>0){
foreach($servicesArr as $key=>$service){
	if(method_exists($serviceObj, trim($service))){
		$responseArray[$service] = call_user_func(array($serviceObj, trim($service)));
						// used a data status for no medication condition//
							//-----------start of new changes-------------------
				  if($_REQUEST['app']=='android'){
				   
					if(!empty($responseArray[trim($service)]) && $service!="get_physician_name" ){
						$responseArray["Data_status"]=1;
						$responseArray =array_merge(array("Data_status"=>1) ,$responseArray );
							   }
							   
					else {
						$responseArray =array_merge(array("Data_status"=>0) ,$responseArray );
						}
				   
				  }
				  // no_med used for no med button in app and med_status shows the no medication checked or Unchecked // 
				 if($_REQUEST['med'] == 'show'){
				   		$pid = $_REQUEST['patId'];
						$query =  imw_query("Select * from lists where pid = '".$pid."' AND allergy_status != 'Deleted' AND type IN (1,4)");
						$result_1 = imw_num_rows($query); 
				   		$query = imw_query("select * from commonnomedicalhistory where patient_id = '".$pid."' AND no_value='NoMedications'"); 
				   		$result = imw_num_rows($query);
							if($result_1 != 0){
								$responseArray =array_merge(array("med_status"=>0,"no_med"=>"false") ,$responseArray );
							
								}
							else if ($result_1 == 0 && $result == 0){
								$responseArray =array_merge(array("med_status"=>0,"no_med"=>"true") ,$responseArray );
							
							}
							
							else{	
									$responseArray =array_merge(array("med_status"=>1,"no_med"=>"true") ,$responseArray );
								}
				  			}
				  
		  //-----------end of new changes-------------------
		 }
	 
	
	
	else{
		$responseArray[$service] = "NO SCHEDULER SERVICE EXISTS";	
	}	
}
}

if($_REQUEST['pre'])
pre($responseArray);
echo json_encode($responseArray);		
	
?>