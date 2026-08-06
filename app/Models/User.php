<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function employee(){
        // hasOne(ModeloRelacionado, 'llave_foranea_en_employees', 'llave_primaria_en_users')
        return $this->hasOne(Employee::class, 'userID', 'userID');
    }
    public function client(){
        return $this->hasOne(Client::class, 'userID', 'userID');
    }

    public function isClient(): bool{
        return $this->client()->exists();
    }

    public function isEmployee(): bool{
        return $this->employee()->exists();
    }

    public function hasWorkerRole(array $roles): bool{
        if(!$this->isEmployee()){
            return false;
        }
        $employee = $this->employee;
        return in_array($employee->workerType, $roles);
    }
}
