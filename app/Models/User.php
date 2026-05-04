<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Contracts\HasAbilities;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method HasAbilities|null currentAccessToken()
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    /**
     * Summary of table
     * @var string
     */
    protected $table = 'users';

    /**
     * Summary of keyType
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Summary of incrementing
     * @var 
     */
    public $incrementing = false;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Summary of hidden
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Summary of casts
     * @return array{email_verified_at: string, password: string}
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
