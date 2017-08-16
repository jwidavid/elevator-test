<?php
    

class elevator {
    
    public $current_floor_int = 1;
    private $current_direction_str = '';
    private $direction_arr = ['up', 'down', 'stand', 'maintenance'];
    private $floor_requests_arr = [];
    private $signals_arr = ['alarm', 'door open', 'door close'];
    private $current_signal_str = '';
    
    
    function elevator() {
        
    } 

    public function call_elevator($requested_floor_int) {
        
        if ($requested_floor_int < $this->current_floor_int) {
            $this->set_current_direction('down');
        }
        elseif ($requested_floor_int > $this->current_floor_int) {
            $this->set_current_direction('up');
        }
        else {
            $this->set_current_direction('stand');
            $this->set_current_signal('alarm');
        }
        
        $this->change_floor($requested_floor_int);
    }
    
    
    
    
    private function change_floor($new_floor_int) {
        $this->current_floor_int = $new_floor_int;
    }
    
    private function set_current_direction($new_direction_str) {
        
        if (in_array($new_direction_str, $this->direction_arr)) {
            $this->current_direction_str = $new_direction_str;        
        }
    }
    
    private function set_current_signal($new_signal_str) {
        
        if (in_array($new_signal_str, $this->signals_arr)) {
            $this->current_signal_str = $new_signal_str;        
        }
    }
    
}
