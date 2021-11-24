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
require_once("../../../config/globals.php");

$del_id = (int) $_REQUEST["pf_id"];
if($del_id != "" && $del_id != 0)
{
	$req_qry = "SELECT * FROM iportal_preferred_images WHERE id = $del_id";
	$result_obj = imw_query($req_qry);

	$result_data = imw_fetch_assoc($result_obj);
	unlink('preferred_images/'.$result_data["name"]);

	$req_qry = "DELETE FROM iportal_preferred_images WHERE id = $del_id";
	imw_query($req_qry);

	$msg = 'Image has been deleted successfully.';

	header('location:preferred_images.php?msg='.$msg);
}
?>