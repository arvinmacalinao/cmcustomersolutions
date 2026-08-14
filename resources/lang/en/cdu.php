<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CDU Custom Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    /*
     * Job Log Msg List
     */
    'create_job'            => 'Job (:jobNo) created by :user on :date.',
    'edit_job'              => 'Job (:jobNo) is being edited by :user on :date.',
    'change_job_status'     => 'Job (:jobNo) status being edited by :user on :date.',
    'cancel_job'            => 'Job (:jobNo) is cancel by :user on :date remarks - :remark.',
    'cancel_tech_job'       => 'Technical Job :jobTechnicalId has been cancelled by :user on :date.',
    'close_job'             => 'Job (:jobNo) is closed by :user on :date remarks - :remark.',
    'assign_job'            => 'Job (:jobNo) is being assigned by :user to :assignee on :date.',
    'assign_job_qc'         => 'Job (:jobNo) is being assigned by :user to :assignee on :date for QC.',
    'route_job'             => 'Job (:jobNo) is being routed from :from to :to by :user on :date.',
    'create_job_technical'  => 'Create technical for job (:jobNo) by :user on :date.',
    'accept_job_technical'  => 'Technician :user accept job (:jobNo) on :date.',
    'accept_job_qc'         => ':user accept job (:jobNo) for QC on :date.',
    'update_job_technical'  => 'Technician :user has updated job (:jobNo) on :date with remarks (:remarks).',
    'complete_job_technical'  => 'Technician :user has complete job (:jobNo) on :date with remarks (:remarks).',
    'change_job_level'  => ':user has change the job level from :oldLevel to :newLevel for job (:jobNo) on :date.',
    'accept_job_qc'  => ':user has accepted job (:jobNo) for QC on :date.',
    'complete_job_qc'  => ':user has verify the job (:jobNo) and determine the tech job has :status on :date. Remark given :remark.',
    'pull_out_job_technical'  => 'Technician :user has pull out from job (:jobNo) on :date with remarks (:remarks).',
    'create_job_qc'         => 'Create quality control for job (:jobNo) by :user on :date.',
    'accept_job_route'      => 'Job (:jobNo) being routed to :to being accepted by :user on :date.',
    'reject_job_route'      => 'Job (:jobNo) being routed to :to being rejected by :user on :date.',
    'accept_encode_job'      => 'Job (:jobNo) has been encoded & accepted by :user on :date.',
    'reject_encode_job'      => 'Job (:jobNo) has been encoded & rejected by :user on :date because :description.',
    'missing_encode_job'      => 'Job (:jobNo) was missing according to :user on :date.',
    'store_job_device'      => 'Job (:jobNo) is being stored at :warehouse by :user on :date.',
    'create_special_case'     => 'Special Case has been created for Job (:jobNo) by :user on :date.',
    'complete_special_case'  => 'Special Case :user has :status the special case for job (:jobNo) on :date.',
    'complete_cust_device_reg'  => 'Device with IMEI :imei has been successfully being registered on :date.',
    'job_logistic_fail'      => 'Job (:jobNo) is suppose to be redirect to :company.',

    /*
     * Err Msg
     */
    'err_select_item'       => 'You must select at least 1 :item.',
    'err_job_duplicate'     => 'A job has already been created for IMEI :imei.',
    // Newly Added
    'err_user_reg'          => 'The system could not register user because :reason.',
    'err_logistic_job'      => 'Job (:jobNo) could not be shipped to :company.',

    /*
     * Notification Msg
     */
    'accept_job_success'        => 'You have successfully accepted the job.',
    'update_tech_job_success'        => 'Job (:jobNo) has been successfully been updated.',
    'complete_job_success'        => 'Job (:jobNo) has been completed.',
    'pull_out_job_success'        => 'Job (:jobNo) has been pull out.',
    'job_void_device_warranty'        => 'Technician :user has void device (:imei) warranty for Job (:jobNo) on :date.',
    'job_update_warranty'       => ':user has update the device (:imei) warranty to :warranty for Job (:jobNo) on :date.',
    'store_job_device_success'      => 'Jobs selected have been successfully stored at :warehouse.',
    'create_ticket_success'     => 'A ticket has been successfully being created for Job (:job).',
    // Newly Added
    'user_reg_success'      => 'User has been successfully registered.',
    'confirm_encode_job_deny' => 'Would you like to deny this encode job?',
    'user_activation_status' => ':user has been successfully being :status.',


    /*
    * Report Notification Msg
     */
    'report_no_data'     => 'There is no data to generate :reportType.',
    'report_not_found'     => 'Report not found.',


];
