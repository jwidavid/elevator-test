<?php
    

class Elevator {
    
    public $current_floor_int = 1;
    
    private $bot_floor_int = 1;
    private $top_floor_int = 10;
    private $current_direction_str = '';
    private $direction_arr = ['up', 'down', 'stand', 'maintenance'];
    private $floor_requests_arr = [];
    private $signals_arr = ['alarm', 'door open', 'door close'];
    private $current_signal_str = '';
    private $requests_served_int = 0;
    private $log_str = '';
    private $floor_requests_sub_arr = [];
    private $error_bool = False;
    private $floor_last_served_int = 0;
    
    
    public function elevator($bot_floor_int=1, $top_floor_int=10) {
        $this->bot_floor_int = $bot_floor_int;
        $this->top_floor_int = $top_floor_int;    
    } 

    public function serve_requests() {
        
        $this->set_current_direction($this->choose_direction());
        
        $this->sort_floor_requests();

        // reverse the array so we can use pop
        $this->floor_requests_arr = array_reverse($this->floor_requests_arr);
        
        while (count($this->floor_requests_arr) > 0) {
            
            $current_request_arr = $this->get_next_request();

            if ($current_request_arr['floor'] != $this->floor_last_served_int) {
                
                if ($this->set_current_floor($current_request_arr['floor'])) {
                    
                    $this->floor_last_served_int = $current_request_arr['floor'];
                    $this->add_to_log("Served floor ".$current_request_arr['floor']);
    
                    if ($this->set_current_signal('door open')) {
                        $this->add_to_log("Signal set to 'door open'");
                    }
                    
                    if ($this->set_current_signal('door close')) {
                        $this->add_to_log("Signal set to 'door close'");
                    }
                }
            }
            
            
            if ($this->error_bool) {
                if ($this->set_current_signal('alarm')) {
                    $this->add_to_log("Signal set to 'alarm'");
                }
                return False;
            }
            
            
            // Check to see if we now need to change directions
            if ($current_request_arr['direction'] != $this->current_direction_str) {
                
                // Is the next floor above or below our current floor?
                if ($this->set_current_direction($current_request_arr['direction'])) {
                    $this->add_to_log("Switched directions to '".$current_request_arr['direction']."'");
                }
            }
            
            // Check to see if we need to change direction
/*
            $next_dir_str = $current_request_arr['floor'] > $this->current_floor_int ? 'up' : 'down';
            
            if ($next_dir_str != $this->current_direction_str) {
                
                if ($this->set_current_direction($next_dir_str)) {
                    $this->add_to_log("Switched direction to '".$current_request_arr['direction']."'");
                }
            }
*/
            
            
            // Check if there is a floor_requests_sub_arr index that matches current_request_arr['request_id']
            if (key_exists($current_request_arr['request_id'], $this->floor_requests_sub_arr)) {
            
                // If there is, then create a proper request array
                $requested_floor_int = $this->floor_requests_sub_arr[$current_request_arr['request_id']];
                
                if ($requested_floor_int > $this->current_floor_int) {
                    $requested_direction_str = 'up';
                }
                elseif ($requested_floor_int < $this->current_floor_int) {
                    $requested_direction_str = 'down';
                }
                else {
                    $requested_direction_str = $this->current_direction_str;
                }
                
                $new_request_arr = [
                    'floor' => $requested_floor_int,
                    'direction' => $requested_direction_str,
                    'request_id' => NULL
                ];
                
                // Pass it into add_floor_request()
                if ($this->add_floor_request($new_request_arr)) {
                    
                    // unfortunately, now we have to resort the array (see note in add_floor_request function)
                    $this->sort_floor_requests();
                    
                    // reverse the array so we can use pop
                    $this->floor_requests_arr = array_reverse($this->floor_requests_arr);
                }
            }
            
            $this->requests_served_int++;
        }
    }
    
    public function add_floor_request($request_arr) {
        
        // Check if the requested floor is undergoing maintenance; if so, log the request then return false.
        
        // For now we'll just add it to the end of the array and then sort it later
        // Ideally, we would do a binary search and insert this new command specifically where we want
        $this->floor_requests_arr[] = $request_arr;
        
        // Log this new request
        return True;
    }
    
    public function parse_request($request_str) {
        
        try {
            // this is ugly but it's the way I know how to do it off the top of my head
            preg_match_all('!\d+!', $request_str, $floor_arr);
            preg_match_all('/[d,u]/', $request_str, $direction_arr);            
            
            $start_floor_int = (int) $floor_arr[0][0];
            $direction_str = (string) $direction_arr[0][0];
            $request_id_int = NULL;
        }
        catch (Exception $e_obj) {
            // put this in an error log
            echo 'Caught exception: ',  $e_obj->getMessage(), "\n";
            return false;
        }

        if ($direction_str == 'd') {
            $direction_str = 'down';
        }
        elseif ($direction_str == 'u') {
            $direction_str = 'up';
        }
        else {
            echo 'Invalid command';
            return false;
        }
        
        
        // The secondary floor request is not technically required someone could make a request then walk away...
        if (isset($floor_arr[0][1]) && is_numeric($floor_arr[0][1])) {
            
            // In order to simulate as close to a real-world scenario as is possible in this challenge, I am
            // storing the secondary request (end_floor) in a separate array and linking it to the first by
            // a request_id which is simply the index of the sub array
            
            $request_id_int = array_push($this->floor_requests_sub_arr, (int) $floor_arr[0][1]) - 1;            
        }
        
        $new_request_arr = [
            'floor' => $start_floor_int,
            'direction' => $direction_str,
            'request_id' => $request_id_int
        ];
        
        
        return $new_request_arr;
    }
    
    public function get_floor_requests() {
        return $this->floor_requests_arr;
    }
    
    public function get_requests_served_int() {
        return $this->requests_served_int;
    }
    
    public function get_logs() {
        return $this->log_str;
    }
    
    
    
    private function add_to_log($message_str) {
        $time = date("m.d.y H:i:s");
        $this->log_str .= "{$time} - {$message_str}<br>";
    }
    
    private function get_next_request() {
        
        $next_request_arr = array_pop($this->floor_requests_arr);
            
        if ($next_request_arr['direction'] != $this->current_direction_str) {
            
            // This is a new direction, put it back
            $this->floor_requests_arr[] = $next_request_arr;
            
            // Time to change directions, this is simple with an array_reverse
            $this->floor_requests_arr = array_reverse($this->floor_requests_arr);                        
            
            $next_request_arr = array_pop($this->floor_requests_arr);                        
        }
        
        return $next_request_arr;
    }
    
    private function sort_floor_requests() {                
        
        if ($this->current_direction_str == 'up') {
            array_multisort(
                array_column($this->floor_requests_arr, 'direction'),  SORT_DESC,
                array_column($this->floor_requests_arr, 'floor'), SORT_ASC,
                $this->floor_requests_arr
            );
        }
        else {
            array_multisort(
                array_column($this->floor_requests_arr, 'direction'),  SORT_ASC,
                array_column($this->floor_requests_arr, 'floor'), SORT_DESC,
                $this->floor_requests_arr
            );
        }
    }    
    
    private function choose_direction() {
        // Chooses the direction that the elevator must travel to fulfill the first request
        /*
        if ($this->floor_requests_arr[0]['floor'] > $this->current_floor_int) {
            $this->set_current_direction('up');
        }
        elseif ($this->floor_requests_arr[0]['floor'] < $this->current_floor_int) {
            $this->set_current_direction('down');
        }
        else {
            $this->set_current_direction($this->floor_requests_arr[0]['direction']);
        }
        */
        
        // Always start by servicing UP requests
        // but this function exists in case I wanted to change that rule
        return 'up';
    }

    private function set_current_direction($new_direction_str) {
        
        if (in_array($new_direction_str, $this->direction_arr)) {
            $this->current_direction_str = $new_direction_str;
            return True;
        }
        else {
            $this->error_bool = True;
            return False;
        }
    }
    
    private function set_current_floor($new_floor_int) {
        
        if ($this->bot_floor_int <= $new_floor_int && $new_floor_int <= $this->top_floor_int) {
            
            $this->current_floor_int = $new_floor_int; 
            return True;
        } 

        $this->error_bool = True;
        return False;
    }
    
    private function set_current_signal($new_signal_str) {
        
        // verify that the new_signal_str is a valid direction
        if (in_array($new_signal_str, $this->signals_arr)) {
            $this->current_signal_str = $new_signal_str;
            return True;
        }
        else {
            $this->error_bool = True;
            return False;
        }
    }
    
}