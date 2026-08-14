<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Job;
use App\JobTechnical;
use App\JobQualityControl;
use App\EncodeJob;
use App\Logistic;
use App\Warehouse;
use App\SpecialCase;
use Auth;

class PagesController extends Controller
{
    /**
     * Display main page.
     *
     */
    public function home()
    {
        /*1 => system_admin
        2 => super_admin
        3 => hq_admin
        4 => branch_admin
        5 => oic
        6 => customer_service
        7 => technician_hq
        8 => technician_branch
        9 => quality_assurance
        10 => physical_encoder
        11 => special_case
        12 => inventory
        13 => warehouse_personnel_branch
        14 => warehouse_personnel_hq
        15 => call_support
        16 => receive_dispatch_unit*/

        $company_id = Auth::user()->company_id;
        $user_role = Auth::user()->role_id;

        if( $user_role == 7 || $user_role == 8 ) {
            // Technician home page
            $user_id = Auth::user()->id;
            $total_new_job = JobTechnical::getTotalNewJob($user_id);
            $total_ongoing_job = JobTechnical::getTotalOngoingJob($user_id);
            $total_expire_job = JobTechnical::getTotalExpireJob($user_id);

            return view('pages.homeTechnician', compact('total_new_job', 'total_ongoing_job', 'total_expire_job'));
        } elseif ( $user_role == 9 ) {
            // QC home page
            $user_id = Auth::user()->id;
            $total_new_job = JobQualityControl::getTotalNewJob($user_id);
            $total_ongoing_job = JobQualityControl::getTotalOngoingJob($user_id);
            $total_expire_job = JobQualityControl::getTotalExpireJob($user_id);

            return view('pages.homeQC', compact('total_new_job', 'total_ongoing_job', 'total_expire_job'));
        } elseif ( $user_role == 10 ) {
            // Physical Encoder home page
            // $user_id = Auth::user()->id;
            $total_encode_job = EncodeJob::getTotalEncodeJob();
            $total_expire_job = EncodeJob::getTotalExpireJob();

            return view('pages.homePE', compact('total_encode_job', 'total_expire_job'));
        } elseif ( $user_role == 13 || $user_role == 14 ) {
            // Warehouse home page
            $user_id = Auth::user()->id;
            $total_unstore_job = Warehouse::getTotalUnstoreJob($user_id);
            $total_store_job = Warehouse::getTotalStoreJob($user_id);

            return view('pages.homeWarehouse', compact('total_unstore_job', 'total_store_job'));
        } elseif ( $user_role == 11 || $user_role == 12 || $user_role == 15 ) {
            // Special Case, Inventory & Call Support home page
            $user_id = Auth::user()->id;
            if ( $user_role == 12 ) {
                // Inventory
                return redirect()->route('device_inventory.index');
            } elseif ( $user_role == 15 ) {
                // Call Support
                return redirect()->route('ticket.index');
            } else {
                //return redirect()->route('special_case.index');
                $total_special_case = SpecialCase::getTotalSpecialCase();
                $total_expire_special_case = SpecialCase::getTotalExpireSpecialCase();
                return view('pages.homeSC', compact('total_special_case', 'total_expire_special_case'));
            }
        } elseif ( $user_role == 16 ) {
            // RDU home page
            // Total Incoming Courier from Branch
            // Total device awaiting shipping from HQ to Branch

            $total_incoming_logistic = Logistic::getTotalIncomingLogistic();
            $total_ready_device = Logistic::getTotalReadyDevice();

            // dd($total_incoming_logistic);

            return view('pages.homeLogistic', compact('total_incoming_logistic', 'total_ready_device'));
        } else {
            // Admin & CSR homepage 1,2,3,4 & 6
            $total_new_job = Job::getTotalNewJob($company_id);
            $total_expire_job = Job::getTotalExpireJob($company_id);
            $total_complete_job = Job::getTotalCompleteJob($company_id);

            return view('pages.home', compact('total_new_job', 'total_expire_job', 'total_complete_job'));
        }       
    }
}
