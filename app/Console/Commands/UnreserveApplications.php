<?php

namespace App\Console\Commands;

use App\Models\Branch\ApplicationReservation;
use App\Models\Branch\Applications;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnreserveApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unreserve-applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bringing applications out of reserve mode and deleting past applications';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicationReservations=ApplicationReservation::where('created_at', '<=', now()->subMinutes(1))->where('payment_status',0)->get();
        foreach ($applicationReservations as $applicationReservation){
            $application=Applications::find($applicationReservation->application_id);
            $application->reserved=0;
            $application->save();
            ApplicationReservation::find($applicationReservation->id)->delete();
        }

        $today = Carbon::today();

        $applications = Applications::where('status', 1)
            ->where('date', '<', $today)
            ->where('reserved', 0)
            ->where('status', 1)
            ->select('id', 'date')
            ->get();

        foreach ($applications as $application) {
            $application->status = 0;
            $application->save();
        }
    }
}
