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
require_once("../../admin_header.php");
require_once("../../../../library/classes/common_function.php");
require_once("InsGroup.class.php");

$Height = $_SESSION['wn_height']-340;
$div1Height = 150;
$div2Height = ($Height-$div1Height)-20;

/*function __autoload($className){
	require_once("$className.class.php");
}*/
spl_autoload_register(function ($class) {
	require_once("$className.class.php");
});

$objInsGrp = new InsGroup();
$id = isset($_GET['id']) ? intval($_GET['id']) : false;
$objInsGrp->grpID = $id;
?>
<!DOCTYPE html>
<html>
<head>
<title>Smart Tags</title>
<script type="text/javascript" src="javascript.js"></script>
<script type="text/javascript">
function change_class(id){
		for(a=1;a<=26;a++){
			var obj = document.getElementById("image_"+a);
			if("image_"+a != id){				
				obj.className = '';
			}else{
				obj.className = 'activealpha';
			}
		}
		if(id == 'image_0'){
			var obj = document.getElementById("image_0");
			obj.className = 'num';
		}
	}
	
</script>
</head>
<body>
	<div class="whtbox">
		<div id="div_InsGroup" style="padding:5px;">
			<?php echo $objInsGrp->get_ins_grps();?>
			<?php echo $objInsGrp->ins_grp_form();?>        
		</div>
		<div id='grpName' class='hide pt10'>Insurance Group:</div>
		<div class="pt10" id="div_InsCompany">
			<?php $objInsGrp ->getInsComp();?>
		</div>
	</div>
<script type="text/javascript">
	show_loading_image('none');
	set_header_title('Insurance Group');
</script>		
<?php require_once("../../admin_footer.php"); ?>