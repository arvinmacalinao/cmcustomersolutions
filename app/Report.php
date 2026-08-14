<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the report's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Log report generated.
     *
     * @return $report_id
     */
    public static function logReport($name, $type, $dir, $file_name, $creator_id)
    {
        $report_exist = Report::where('file_name', $file_name)->where('dir', $dir)->get();

        if( $report_exist->isEmpty() ) {
            $report = Report::create([
                                    'name' => $name, 
                                    'type' => $type, 
                                    'dir' => $dir, 
                                    'file_name' => $file_name, 
                                    'created_by' => $creator_id,
                                    ]);

            return $report->id;
        } else {
            return $report_exist->first()->id;
        }
    }

    /**
     * Update report status.
     *
     */
    public static function updateReportStatus($id, $status, $creator_id)
    {
        Report::find($id)->update([
                                    'status' => $status,
                                    'updated_by' => $creator_id
                                ]);
    }
}
