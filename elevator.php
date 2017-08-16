<?php


require_once('libraries/elevator.php');


$elevator = new Elevator();


$elevator->current_floor_int = 1;
$elevator->current_direction_str = 'stand';



$floor_requests_arr = [];

// take all requests in pairs like:
//  6,1
// our example has this:
//  6,1,5,7,3,1,1,7

// run them through function to get which direction each set is going
//  down
// our example has this:
//  down,up,down,up

// sort the list by direction, favoring the direction that the elevator has to
// travel to fulfill the "first" request
// our example has this:
//  up (since the elevator is standing at floor 1 it needs to go up to get to floor 6)

// move the elevator upward checking each floor to see if anyone needs to go up 
// (even if it goes past floor 6)

// if it does stop on its way up, remove that request from the queue

// once it fulfills all of the up requests that it was able to
// (it may have started above another up request so then not ALL up requests have been cleared)

// change directions and head DOWN, checking at each floor to see if there are any down requests
// from that floor, if so then open and shut doors and clear out the request

// continue heading down until all down requests have been fulfilled

// switch if any other requests exist then change directions again and continue process

 

$elevator->set_elevator_queue();


