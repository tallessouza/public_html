<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Integration\UserIntegration;
use App\Models\Team\Team;
use App\Models\Team\TeamMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Subscription as Subscriptions;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Billable, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'coingate_subscriber_id',
        'team_id',
        'team_manager_id',
        'name',
        'surname',
        'email',
        'password',
        'affiliate_id',
        'affiliate_code',
        'remaining_words',
        'remaining_images',
        'email_confirmation_code',
        'email_confirmed',
        'password_reset_code',
        'anthropic_api_keys',
        'api_keys',
        'defi_setting',
        'affiliate_status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'defi_setting'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'defi_setting' => 'json'
    ];

    public function isAdmin(): bool
    {
        return $this->type == 'admin';
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Setting::query()->increment('user_count');
			Usage::getSingle()->updateUserCount(1);
        });
		static::deleted(function ($user) {
			$user->orders()->delete();
		});
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(UserIntegration::class)->with('integration');
    }

    public function isUser(): bool
    {
        return $this->type == 'user';
    }

    public function teamManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_manager_id', 'id');
    }

    public function teamMember(): HasOne
    {
        return $this->hasOne(TeamMember::class, 'user_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function myCreatedTeam()
    {
        return $this->hasOne(Team::class, 'user_id', 'id');
    }

    public function relationPlan()
    {
        return $this->hasOneThrough(
            PaymentPlans::class,
            Subscriptions::class,
            'user_id',
            'id',
            'id',
            'plan_id'
        );
    }

    public function getRemainingWordsAttribute($value)
    {
        if ($this->type == 'admin') {
            return $value;
        }

        if ($this->team_id == null) {
            return $value;
        }

        $teamMember = $this->teamMember;

        if (! $teamMember) {
            return $value;
        }

        if ($teamMember?->allow_unlimited_credits) {
            return $this->teamManager->remaining_words;
        } else {
            return $this->teamMember->remaining_words;
        }

        return $value;
    }

    public function getRemainingImagesAttribute($value)
    {
        if ($this->type == 'admin') {
            return $value;
        }

        if ($this->team_id == null) {
            return $value;
        }

        $teamMember = $this->teamMember;

        if (! $teamMember) {
            return $value;
        }

        if ($teamMember?->allow_unlimited_credits) {
            return $this->teamManager->remaining_images;
        } else {
            return $this->teamMember->remaining_images;
        }

        return $value;
    }

    public function fullName()
    {
        return $this->name.' '.$this->surname;
    }

    public function email()
    {
        return $this->email;
    }

    public function openai()
    {
        return $this->hasMany(UserOpenai::class);
    }

    public function orders()
    {
        return $this->hasMany(UserOrder::class)->orderBy('created_at', 'desc');
    }

    public function plan()
    {
        return $this->hasMany(UserOrder::class)
            ->where('type', 'subscription')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function activePlan()
    {
        // $activeSub = $this->subscriptions()->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();
        // $userId=Auth::user()->id;
        $userId = $this->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();
            if ($plan == null) {
                return null;
            }
            $difference = $activeSub->updated_at->diffInDays(Carbon::now());
            if ($plan->frequency == 'monthly') {
                if ($difference < 31) {
                    return $plan;
                }
            } elseif ($plan->frequency == 'yearly') {
                if ($difference < 365) {
                    return $plan;
                }
            }else{
				return $plan;
			}
        } else {
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub != null) {
                $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();
                if ($plan == null) {
                    return null;
                }
                $difference = $activeSub->updated_at->diffInDays(Carbon::now());
                if ($plan->frequency == 'monthly' || $plan->frequency == 'lifetime_monthly') {
                    if ($difference < 31) {
                        return $plan;
                    }
                } elseif ($plan->frequency == 'yearly' || $plan->frequency == 'lifetime_yearly') {
                    if ($difference < 365) {
                        return $plan;
                    }
                }else{
					return $plan;
				}
            } else {
                return null;
            }
        }
    }

    //Support Requests
    public function supportRequests()
    {
        return $this->hasMany(UserSupport::class);
    }

    //Favorites
    public function favoriteOpenai()
    {
        return $this->belongsToMany(OpenAIGenerator::class, 'user_favorites', 'user_id', 'openai_id');
    }

    //Affiliate
    public function affiliates()
    {
        return $this->hasMany(User::class, 'affiliate_id', 'id');
    }

    public function affiliateOf()
    {
        return $this->belongsTo(User::class, 'affiliate_id', 'id');
    }

    public function withdrawals()
    {
        return $this->hasMany(UserAffiliate::class);
    }

    //Chat
    public function openaiChat()
    {
        return $this->hasMany(UserOpenaiChat::class);
    }

    //Avatar
    public function getAvatar()
    {
        if ($this->avatar == null) {
            return '<span class="avatar">'.Str::upper(substr($this->name, 0, 1)).Str::upper(substr($this->surname, 0, 1)).'</span>';
        } else {
            $avatar = $this->avatar;
            if (strpos($avatar, 'http') === false || strpos($avatar, 'https') === false) {
                $avatar = '/'.$avatar;
            }

            return ' <span class="avatar" style="background-image: url('.custom_theme_url($avatar).')"></span>';
        }
    }

    public function couponsUsed()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_users')
            ->withTimestamps();
    }

    public function twitterSettings()
    {
        if (class_exists(\App\Models\Automation\TwitterSettings::class)) {
            return $this->hasMany(\App\Models\Automation\TwitterSettings::class);
        }

        return null;
    }

    public function linkedinSettings()
    {
        if (class_exists(\App\Models\Automation\LinkedinTokens::class)) {
            return $this->hasMany(\App\Models\Automation\LinkedinTokens::class);
        }

        return null;
    }

    public function scheduledPosts()
    {
        if (class_exists(\App\Models\Automation\ScheduledPosts::class)) {
            return $this->hasMany(\App\Models\Automation\ScheduledPosts::class);
        }

        return null;
    }

    public function folders()
    {
        return $this->hasMany(Folders::class, 'created_by');
    }

    // my companies
    public function companies()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    public function getCompanies()
    {
        return $this->companies()->orderBy('name', 'asc')->get();
    }
}