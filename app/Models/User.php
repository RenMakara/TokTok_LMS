<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'full_name', 'email', 'phone', 'role'];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->user_id) {
                // get last user_id from DB
                $lastUser = self::orderByDesc('user_id')->first();
                
                if ($lastUser) {
                    $user->user_id = (int) $lastUser->user_id + 1; // increment last user_id
                } else {
                    $user->user_id = 10001; // first user
                }
                // phone manage
                if ($user->phone && !str_starts_with($user->phone, '+855')) {
                    $user->phone = '+855' . $user->phone; 
                }
                

            }
        });
    }
}
