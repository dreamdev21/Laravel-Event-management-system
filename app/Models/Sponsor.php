<?php
namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */
class Sponsor extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Get the full logo path of the sponsor.
     *
     * @return mixed|string
     */
    public function getFullLogoPathAttribute()
    {
        if ($this->logo_path && (file_exists(config('attendize.cdn_url_user_assets') . '/' . $this->logo_path) || file_exists(public_path($this->logo_path)))) {
            return config('attendize.cdn_url_user_assets') . '/' . $this->logo_path;
        }

        return config('attendize.fallback_organiser_logo_url');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}