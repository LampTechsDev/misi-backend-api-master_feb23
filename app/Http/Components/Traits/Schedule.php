<?php

namespace App\Http\Components\Traits;

use App\Models\TherapistSchedule;
use App\Models\TherapistScheduleSettings;
use Carbon\Carbon;

trait Schedule{
    /**
     * Add Or Update Schedule Settings
     */
    protected function addOrUpdateScheduleSettings($request){
        $settings = TherapistScheduleSettings::where("therapist_id", $request->therapist_id)->first();
        if( empty($settings) ){
            $settings = New TherapistScheduleSettings();
        }
        $settings->therapist_id     = $request->therapist_id;
        $settings->interval_time    = $request->interval_time;
        $settings->start_time       = $request->start_time;
        $settings->end_time         = $request->end_time;
        $settings->holiday          = $request->holiday;
        $settings->save();
        return $settings;
    }

    /**
     * Generate Therapist Schedule
     */
    protected function generateSchedule($settings, $request){
        $date       = $request->start_date;
        $end_date   = $request->end_date;

        while($date <= $end_date){
            if( !$this->scheduleExists($settings->therapist_id, $date) ){
                $day_name = strtolower(Carbon::parse($date)->format('l'));
                if( !in_array($day_name, $settings->holiday) ){
                    $this->generateDailySchedule($settings, $request, $date);
                }
            }
            $date = Carbon::parse($date)->addDay()->format('Y-m-d');
        }
    }

    /**
     * Check Schedule is exists or Not
     */
    public function scheduleExists($therapist_id, $date){
        $schedule = TherapistSchedule::where("therapist_id", $therapist_id)->where("date", $date)->first();
        if( !empty($schedule) ){
            return true;
        }
        return false;
    }

    /**
     * Generate Daily Schedule
     */
    protected function generateDailySchedule($settings, $request, $date){
        $daily_schedule_arr = [];
        $day_schedule_start_time = Carbon::parse($date.' '.$request->start_time)->format("Y-m-d H:i");
        $_day_end_time = Carbon::parse($date.' '.$request->end_time)->format("Y-m-d H:i");
        while($day_schedule_start_time < $_day_end_time){

            $_schedule_end_time = Carbon::parse($day_schedule_start_time)->addMinutes($settings->interval_time)->format("Y-m-d H:i");
            array_push($daily_schedule_arr,[
                "therapist_id"  => $settings->therapist_id,
                "date"          => $date,
                "start_time"    => $day_schedule_start_time,
                "end_time"      => $_schedule_end_time,
                "remarks"       => $request->remarks,
                "created_at"    => now(),
                "updated_at"    => now(),
            ]);
            $day_schedule_start_time = $_schedule_end_time;
        }
        TherapistSchedule::insert($daily_schedule_arr);
    }
}