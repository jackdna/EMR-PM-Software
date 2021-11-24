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
require_once(dirname(__FILE__).'/../../../config/globals.php');
include("schedule_functions.php");
$schId = $_REQUEST['schId'];
$patId = $_REQUEST['patId'];
?>
<html>
	<body>	
   	<div style="height: <?php echo $_SESSION["wn_height"] - 442;?>px; overflow: auto">
		<?php 
        get_one_schdeule_details_new($patId, $schId);
    ?>
    </div>
	</body>
</html>