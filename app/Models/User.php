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
        'spotify_id',
        'spotify_access_token',
        'spotify_refresh_token',
        'spotify_token_expires_at',
        'spotify_user_data',
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
        'spotify_access_token', // Hide Spotify tokens for security
        'spotify_refresh_token',
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
            'spotify_token_expires_at' => 'datetime', // Cast Spotify token expiry as datetime
            'spotify_user_data' => 'array', // Cast Spotify user data as array
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
    
    // ========== SPOTIFY METHODS ==========
    
    /**
     * Check if user has connected Spotify account
     */
    public function hasSpotifyConnection()
    {
        return !empty($this->spotify_access_token) && 
               (!$this->spotify_token_expires_at || Carbon::now()->isBefore($this->spotify_token_expires_at));
    }
    
    /**
     * Check if Spotify token needs refresh
     */
    public function needsSpotifyTokenRefresh()
    {
        if (!$this->spotify_access_token || !$this->spotify_token_expires_at) {
            return false;
        }
        
        // Refresh token if it expires in the next 5 minutes
        return Carbon::now()->addMinutes(5)->isAfter($this->spotify_token_expires_at);
    }
    
    /**
     * Get Spotify user display name
     */
    public function getSpotifyDisplayName()
    {
        if (!$this->spotify_user_data) {
            return null;
        }
        
        return $this->spotify_user_data['display_name'] ?? 'Unknown';
    }
    
    /**
     * Get Spotify user profile image
     */
    public function getSpotifyProfileImage()
    {
        if (!$this->spotify_user_data || !isset($this->spotify_user_data['images'])) {
            return null;
        }
        
        return $this->spotify_user_data['images'][0]['url'] ?? null;
    }
}