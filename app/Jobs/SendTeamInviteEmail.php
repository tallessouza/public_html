<?php

namespace App\Jobs;

use App\Mail\InviteEmail;
use App\Mail\InviteTeamEmail;
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

class SendTeamInviteEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $sendTo;
    protected $settings;
    protected $template;


    public function __construct(User $user, $sendTo)
    {
        $this->user = $user;
        $this->sendTo = $sendTo;
        $this->settings = Setting::first();
        $this->template = EmailTemplates::where('id', 4)->first();
    }

    public function handle()
    {
        Mail::to($this->sendTo)->send(
            new InviteTeamEmail($this->user, $this->settings, $this->template)
        );
    }
}
