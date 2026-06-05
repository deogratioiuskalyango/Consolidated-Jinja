<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShareClassPermission extends Model
{
    protected $guarded = [];

    public function shareClass()
    {
        return $this->belongsTo(ShareClass::class, 'share_class_id');
    }
}
