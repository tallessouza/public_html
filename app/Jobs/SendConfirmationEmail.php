<?php

namespace App\Jobs;

use App\Mail\ConfirmationEmail;
use App\Models\Setting;
use App\Models\User;
use App\Models\EmailTemplates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $settings;
    protected $template;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = Setting::first();
        $this->template = EmailTemplates::where('id', 1)->first();
    }

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new ConfirmationEmail($this->user, $this->settings, $this->template));
    }
}
