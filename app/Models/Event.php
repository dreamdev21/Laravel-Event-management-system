<?php namespace App\Models;

use Carbon\Carbon;
use Str, URL;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends MyBaseModel {

    use SoftDeletes;
    
    protected $rules = array(
        'title' => array('required'),
        'description' => array('required'),
        'location_venue_name' => array('required_without:venue_name_full'),
        'venue_name_full' => array('required_without:location_venue_name'),
        'start_date' => array('required'),
        'end_date' => array('required'),
        'organiser_name' => array('required_without:organiser_id'),
        'event_image' => ['mimes:jpeg,jpg,png', 'max:3000']
    );
    protected $messages = array(
        'title.required' => 'You must at least give a title for your event.',
        'organiser_name.required_without' => 'Please create an organiser or select an existing organiser.',
        'event_image.mimes' => 'Please ensure you are uploading an image (JPG, PNG, JPEG)',
        'event_image.max' => 'Pleae ensure the image is not larger then 3MB',
        'location_venue_name.required_without' => 'Please enter a venue for your event',
        'venue_name_full.required_without' => 'Please enter a venue for your event'
    );

    public function questions() {
        return $this->belongsToMany('\App\Models\Question', 'event_question'); 
    }

    public function attendees() {
        return $this->hasMany('\App\Models\Attendee');
    }
    
    public function images() {
        return $this->hasMany('\App\Models\EventImage');
    }
    public function messages() {
        return $this->hasMany('\App\Models\Message')->orderBy('created_at', 'DESC');
    }

    public function tickets() {
        return $this->hasMany('\App\Models\Ticket');
    }
    
    public function stats() {
        return $this->hasMany('\App\Models\EventStats');
    }
    
    public function affiliates() {
        return $this->hasMany('\App\Models\Affiliate');
    }

    public function orders() {
        return $this->hasMany('\App\Models\Order');
    }

    public function account() {
        return $this->belongsTo('\App\Models\Account');
    }

    public function currency() {
        return $this->belongsTo('\App\Models\Currency');
    }

    public function organiser() {
        return $this->belongsTo('\App\Models\Organiser');
    }
    
    /*
     * Getters & Setters
     */

    public function getEmbedUrlAttribute() {
        return str_replace(['http:', 'https:'], '', route('showEmbeddedEventPage', ['event' => $this->id]));
    }

    public function getFixedFeeAttribute() {
        return config('attendize.ticket_booking_fee_fixed') + $this->organiser_fee_fixed;
    }
    public function getPercentageFeeAttribute() {
        return config('attendize.ticket_booking_fee_percentage') + $this->organiser_fee_percentage;
    }
    
    public function getHappeningNowAttribute() {
        return Carbon::now()->between($this->start_date, $this->end_date);
    }
        
    public function getCurrencySymbolAttribute() {
        return $this->currency->symbol_left;
    }
    public function getCurrencyCodeAttribute() {
        return $this->currency->code;
    }
    
    public function getEmbedHtmlCodeAttribute () {
        return "<!--Attendize.com Ticketing Embed Code-->
                <iframe style='overflow:hidden; min-height: 350px;' frameBorder='0' seamless='seamless' width='100%' height='100%' src='".$this->embed_url."' vspace='0' hspace='0' scrolling='auto' allowtransparency='true'></iframe>
                <!--/Attendize.com Ticketing Embed Code-->";
    }
    
    /*
     * Get a usable address for embedding Google Maps
     */
    public function getMapAddressAttribute() {
        
        $string = $this->venue.','
                .$this->location_street_number.','
                .$this->location_address_line_1.','
                .$this->location_address_line_2.','
                .$this->location_state.','
                .$this->location_post_code.','
                .$this->location_country;
                            
        return urlencode($string);
        
    }
    
    public function getBgImageUrlAttribute() {
        return URL::to('/').'/'.$this->bg_image_path;
    }
    
    public function getEventUrlAttribute() {
        return URL::to('/').'/e/'.$this->id.'/'.Str::slug($this->title);
    }
    

    public function getSalesAndFeesVoulmeAttribute() {
        return $this->sales_volume + $this->organiser_fees_volume;
    }

    public function getDates() {
        return array('created_at', 'updated_at', 'start_date', 'end_date');
    }

}
