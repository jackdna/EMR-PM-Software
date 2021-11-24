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
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Optical API calls.
*/

$patientId = 0;

$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
    $minRec = 1;
    $maxRec = 100;
    
    if($request->__isset('maxRecord') && trim($request->__get('maxRecord')) !== '' ){
        $service->validateParam('maxRecord', 'Please provide a valid max record value to fetch')->notNull()->isInt();
        
        if(trim($request->__get('maxRecord')) <= 1000 && trim($request->__get('maxRecord')) > 0){
            $maxRec = trim($request->__get('maxRecord'));
        }
    }
    
    if($maxRec < 0) $maxRec = 100;
    
    $service->__set('minRec', $minRec);
    $service->__set('maxRec', $maxRec);
});

/* Return requested item data from optical */
$this->get('/getFrames', function($request, $response, $service,$app) use(&$patientId){
    $minLimit = $service->__get('minRec');
    $maxLimit = $service->__get('maxRec');
    
    if(empty($minLimit) || $minLimit < 0) $minLimit = 1;
    if($maxLimit > 1000 || empty($maxLimit) || $maxLimit < 0) $maxLimit = 100;
    
    //Getting Details from in_item table
    $sqlQuery = "SELECT 
            itm.id as itemId,
            itm.upc_code as UPC,
            itm.name as Name,
            manufac.manufacturer_name as Manufacturer,
            frameSource.frame_source as Brand,
            frameTyp.type_name as FrameType,
            itm.color_code as ColorCode,
            frameColor.color_name as Color,
            frameShape.shape_name as Shape,
            frameStyles.style_name as Style,
            itm.qty_on_hand as QtyOnHand
        FROM 
            in_item itm
        LEFT JOIN 
            in_manufacturer_details manufac ON (manufac.id = itm.manufacturer_id)
        LEFT JOIN
            in_frame_types frameTyp ON (frameTyp.id = itm.type_id)   
        LEFT JOIN
            in_frame_color frameColor ON (frameColor.id = itm.color)
        LEFT JOIN
            in_frame_shapes frameShape ON (frameShape.id = itm.frame_shape)
        LEFT JOIN
            in_frame_styles frameStyles ON (frameStyles.id = itm.frame_style)
        LEFT JOIN
            in_frame_sources frameSource ON (frameSource.id = itm.brand_id)
        WHERE 
            itm.del_status = 0
        LIMIT  
            ".$minLimit.", ".$maxLimit."   
    ";
    
    $frameArr = array();
    $res = $app->dbh->imw_query($sqlQuery);
    if($res && $app->dbh->imw_num_rows($res) > 0){
        while($row = $app->dbh->imw_fetch_assoc($res)){
            //Decoding all entities in the received array
            array_walk_recursive($row, function (&$value) {
                $value = html_entity_decode($value);
            });
            
            array_push($frameArr, $row);
        }
    }
    
    $response = array('Frames' => $frameArr);
    if(count($frameArr) == 0) $response = 'No record found';
    return json_encode($response);
});






?>