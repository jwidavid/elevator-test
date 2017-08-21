<?php


require_once('elevator_class.php');


$elevator = new Elevator();



$requests_arr = ['6d1','5u7','3d1','1u7']; 


while (count($requests_arr) > 0) {
    $new_request_arr = $elevator->parse_request(array_pop($requests_arr));        
    
    if ($new_request_arr) {
        $elevator->add_floor_request($new_request_arr);
    }
}

$elevator->serve_requests();

print($elevator->get_logs());