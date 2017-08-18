<?php


require_once('elevator_class.php');


$elevator = new Elevator();



$elevator->current_floor_int = 1;


$requests_arr = ['6d1','5u7','3d1','1u7'];


// Since order doesn't matter (array will be sorted) use pop to avoid 
// the slow re-indexing that array_shift would require

while (count($requests_arr) > 0) {
    $new_request_arr = $elevator->parse_request(array_pop($requests_arr));        
    
    if ($new_request_arr) {
        $elevator->add_floor_request($new_request_arr);
    }
}

$elevator->serve_requests();

print($elevator->get_requests_served_int());