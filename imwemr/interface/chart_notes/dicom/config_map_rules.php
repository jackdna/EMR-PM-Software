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
require_once(dirname(__FILE__).'/../../../config/globals.php');
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>    
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>	
    </head>
	<body>
		<div class="container-fluid" >			
			<div class="row" >
				<div class="col-sm-7 embed-responsive embed-responsive-4by3" >
				<iframe name="frm1" class="embed-responsive-item" src="config_map_rules_ex.php"></iframe>
				</div>
				<div class="col-sm-5 embed-responsive embed-responsive-4by3" >
				<iframe name="frm2" class="embed-responsive-item" src="dcm_data.php"></iframe>
				</div>
			</div>
		</div>
	</body>
</html>