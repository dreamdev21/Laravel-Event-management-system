<?php namespace App\Http\Controllers;

class EventPromoteController extends MyBaseController {
    
    public function showPromote($event_id) {
        
        $data = [
          'event' => Event::scope()->find($event_id)  
        ];
        
        return View::make('ManageEvent.Promote', $data);
    }
    
}
