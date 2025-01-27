<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
class BreakdownService extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "breakdown_services";
    protected $fillable = [
        "uuid", "date_time", "service_status",
        "supervisor_problem_note_id", "supervisor_note",
        "company_id", "mechine_assing_id", "technician_id",
        "creator_type", "creator_id", "updater_type", "updater_id"
    ];
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }

    public function mechine_assing()
    {
        return $this->belongsTo(MechineAssing::class, 'mechine_assing_id');
    }

    public function service_details(){
        return $this->hasMany(BreakdownServiceDetail::class);
    }
    public function deatail()
    {
        return $this->belongsTo(BreakdownServiceDetail::class, 'breakdown_service_id');
    }
   // BreakdownService মডেলে
   public function supervisorProblemNotes()
   {
       // supervisor_problem_note_id এর স্ট্রিং থেকে কোটেশন চিহ্ন সরিয়ে, কমা দিয়ে আলাদা করা আইডিগুলো বের করা
       $noteIds = explode(',', str_replace('"', '', $this->supervisor_problem_note_id));
   
       // তারপর সেগুলোর মাধ্যমে 'BreakDownProblemNote' মডেল থেকে সম্পর্কিত রেকর্ডগুলো ফিরিয়ে আনব
       return BreakDownProblemNote::whereIn('id', $noteIds)->get();
   }
}
