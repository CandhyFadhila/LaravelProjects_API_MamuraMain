<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrier extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'carrier_category_id' => 'integer',
        'employee_status_id' => 'integer',
        'job_location_id' => 'integer',
        'qualification' => 'array',
    ];

    /**
     * Get the carrier_category that owns the Carrier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier_category(): BelongsTo
    {
        return $this->belongsTo(CarrierCategory::class, 'carrier_category_id', 'id');
    }

    /**
     * Get the employee_status that owns the Carrier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee_status(): BelongsTo
    {
        return $this->belongsTo(EmployeeStatus::class, 'employee_status_id', 'id');
    }

    /**
     * Get the job_location that owns the Carrier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job_location(): BelongsTo
    {
        return $this->belongsTo(JobLocation::class, 'job_location_id', 'id');
    }
}
