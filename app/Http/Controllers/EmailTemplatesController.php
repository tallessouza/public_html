<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Jobs\SendEmail;
use App\Jobs\SendTeamInviteEmail;
use App\Models\EmailTemplates;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Models\User;

class EmailTemplatesController extends Controller
{
    public function index()
    {
        $installedExtension = 0;

        $extension = Extension::where('slug', 'newsletter')->first();
        if ($extension != null && $extension->installed) {
            $installedExtension = 1;
        }

        return view('panel.email.list', [
            'list' => EmailTemplates::orderBy('id', 'asc')->get(), 'installedExtension' => $installedExtension
        ]);
    }

    public function edit(int $id)
    {
        return view('panel.email.form', [
            'action' => route('dashboard.email-templates.update', $id),
            'method' => 'PUT',
            'template' => EmailTemplates::query()->findOrFail($id),
            'title' => 'Edit Email Template'
        ]);
    }

    public function update(Request $request, int $id)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $template = EmailTemplates::query()->findOrFail($id);

        $request['content'] = $request->get('content_ace');

        $data = $request->validate([
            'title' => $template->system ? 'sometimes|nullable' : 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);

        $template->update($data);

        return redirect()
            ->route('dashboard.email-templates.index')
            ->with([
                'message' => __('Updated Successfully'), 'type' => 'success'
            ]);
    }

    public function delete(int $id)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $template = EmailTemplates::query()->findOrFail($id);

        if ($template->getAttribute('system')) {
            return back()->with([
                'message' => __('Deleted Successfully'), 'type' => 'danger'
            ]);
        }

        $template->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function sendView(int $id)
    {
        return view('panel.email.send', [
            'template' => EmailTemplates::query()->findOrFail($id),
            'title' => 'Send Email'
        ]);
    }

    public function sendQueue(Request $request, int $id)
    {
        $template = EmailTemplates::query()
            ->findOrFail($id);

        $request['all_customers'] = (bool) $request->get('all_customers');

        $request->validate([
            'receivers' => $request['all_customers'] ? 'sometimes' : 'required',
        ]);

        $arrayReceivers = Helper::multi_explode([",", "\n", "\r", ";", " ", "|"], $request->get('receivers'));

        $array = [];

        if ($arrayReceivers) {
            foreach ($arrayReceivers as $key => $receiver) {
                $array[trim($receiver)] = 'Guest';
            }
        }

        $users = [];

        if ($request['all_customers']) {
            $users = User::query()
                ->pluck('name', 'email')
                ->toArray();
        } else {
            $users = User::query()
                ->whereIn('email', $arrayReceivers)
                ->pluck('name', 'email')
                ->toArray();
        }

        $data = array_merge($array, $users);

        $data = $this->data($data);

        foreach ($data as $datum) {
            SendEmail::dispatch($datum, $datum['email'], $template);
        }

        return back()->with([
            'message' => __('Emails are being sent'),
            'type' => 'success'
        ]);
    }

    public function data(array $data): array
    {
        $data = array_map(function ($item, $key) {
            return [
                'email' => $key,
                'name' => $item
            ];
        }, $data, array_keys($data));

        return array_filter($data, function ($value) {
            return $value['email'] ?? false;
        });
    }
}