<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code', // Hide OTP for security
    ];

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
            'otp_expires_at' => 'datetime', // Cast OTP expiry as datetime
        ];
    } 
    
    // ========== RELATIONSHIPS ==========
    
    // User has many memories
    public function memories()
    {
        return $this->hasMany(Memory::class);
    }
    
    // User has many events
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    
    // User can send many birthday surprises
    public function sentSurprises()
    {
        return $this->hasMany(BirthdaySurprise::class, 'sender_user_id');
    }
    
    // User can receive many birthday surprises
    public function receivedSurprises()
    {
        return $this->hasMany(BirthdaySurprise::class, 'receiver_user_id');
    }
    
    // ========== OTP METHODS ==========
    
    /**
     * Generate and save OTP code
     */
    public function generateOTP()
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10), // OTP valid for 10 minutes
        ]);
        
        return $otp;
    }
    
    /**
     * Verify OTP code
     */
    public function verifyOTP($inputOTP)
    {
        // Check if OTP exists and not expired
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }
        
        // Check if OTP expired
        if (Carbon::now()->isAfter($this->otp_expires_at)) {
            return false;
        }
        
        // Check if OTP matches
        return $this->otp_code === $inputOTP;
    }
    
    /**
     * Clear OTP after successful verification
     */
    public function clearOTP()
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }
    
    /**
     * Check if OTP is expired
     */
    public function isOTPExpired()
    {
        if (!$this->otp_expires_at) {
            return true;
        }
        
        return Carbon::now()->isAfter($this->otp_expires_at);
    }
}