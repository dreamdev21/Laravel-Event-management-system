<?php namespace App\Http\Controllers;

use View;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventStats;
use DateTime, DatePeriod, DateInterval;

class EventDashboardController extends MyBaseController {


    function showDashboard($event_id = FALSE) {
        $event = Event::scope()->findOrFail($event_id);

        $num_days= 20;
        
        /**
         * This is a fairly hackish way to get the data for the dashboard charts. I'm sure someone
         * with better SQL skill could do it in one simple query.
         *
         * Filling in the missing days here seems to be fast(ish) (with 20 days history), but the work
         * should be done in the DB
         */
        $chartData = EventStats::where('event_id', '=', $event->id)
                ->where('date', '>', Carbon::now()->subDays($num_days)->format('Y-m-d'))
                ->get()
                ->toArray();
           
        $startDate = new DateTime("-$num_days days");
        $dateItter = new DatePeriod(
                $startDate, new DateInterval('P1D'), $num_days
        );

        $original = $chartData;

        /*
         * I have no idea what I was doing here, but it seems to work;
         */
        $result = array();
        $i = 0;
        foreach ($dateItter as $date) {
            $views = 0;
            $sales_volume = 0;
            $unique_views = 0;
            $tickets_sold = 0;
            $organiser_fees_volume = 0;

            foreach ($original as $item) {
                if ($item['date'] == $date->format('Y-m-d')) {
                    $views = $item['views'];
                    $sales_volume = $item['sales_volume'];
                    $organiser_fees_volume = $item['organiser_fees_volume'];
                    $unique_views = $item['unique_views'];
                    $tickets_sold = $item['tickets_sold'];
                    
                }
                $i++;
            }

            $result[] = array(
                "date" => $date->format('Y-m-d'),
                "views" => $views,
                'unique_views' => $unique_views,
                'sales_volume' => $sales_volume + $organiser_fees_volume,
                'tickets_sold' => $tickets_sold
            );
        }

        $data = [
            'event' => $event,
            'chartData' => json_encode($result)
        ];

        return View::make('ManageEvent.Dashboard', $data);
    }

    /**
     * @param $chartData
     * @param bool|FALSE $from_date
     * @param bool|FALSE $toDate
     * @return string
     */
    public function generateChartJson($chartData, $from_date = FALSE, $toDate = FALSE) {

        $data = [];

        $startdate = '2014-10-1';
        $enddate   = '2014-11-7';
        $timestamp = strtotime($startdate);
        while ($startdate <= $enddate) {

            $startdate = date('Y-m-d', $timestamp);

            $data[] = [
                'date' => $startdate,
                'tickets_sold' => rand(0, 7),
                'views' => rand(0, 5),
                'unique_views' => rand(0, 5)
            ];


            $timestamp = strtotime('+1 days', strtotime($startdate));
        }

        return json_encode($data);
    }

}
