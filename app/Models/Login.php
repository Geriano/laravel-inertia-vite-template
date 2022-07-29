<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'login_activities';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'browser',
        'platform',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
