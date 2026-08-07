<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $primaryKey = 'userID';

    /** @use HasFactory<UserFactory> */
    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'userID', 'userID');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'userID', 'userID');
    }

    public function isClient(): bool
    {
        return $this->client()->exists();
    }

    public function isEmployee(): bool
    {
        return $this->employee()->exists();
    }

    public function hasWorkerRole(array $roles): bool
    {
        if (!$this->isEmployee()) {
            return false;
        }

        $employee = $this->employee;

        // Soporte para admin_type o admin_Type según la versión
        $type = $employee->admin_type ?? $employee->admin_Type;

        return in_array($type, $roles, true);
    }
}
