<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GovernanceRule extends Model
{
    protected $guarded = [];
    protected $casts = ['config' => 'array', 'is_active' => 'boolean'];

    public function getConfigValueAttribute(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
}
