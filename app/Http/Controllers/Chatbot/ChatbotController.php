<?php

namespace App\Http\Controllers\Chatbot;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\ChatbotRequest;
use App\Http\Requests\Chatbot\ChatbotSettingRequest;
use App\Models\Chatbot\Chatbot;
use App\Models\Chatbot\ChatbotData;
use App\Models\Chatbot\ChatbotDataVector;
use App\Models\Setting;
use App\Models\SettingTwo;

class ChatbotController extends Controller
{
    public function setting()
    {
        return view('panel.admin.chatbot.setting', [
            'title' => trans('Floating Chat Settings'),
            'method' => 'post',
            'action' => route('dashboard.admin.chatbot.setting'),
            'chatbotData' => Chatbot::query()->get(),
            'chatbot' => Chatbot::query()->find(Helper::settingTwo('chatbot_template')),
        ]);
    }

    public function putSetting(ChatbotSettingRequest $request)
    {
        if (Helper::appIsDemo())
        {
            return back()->with([
                'type' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $setting = SettingTwo::first();
        $setting->chatbot_status = $request->chatbot_status;
        $setting->chatbot_template = $request->chatbot_template;
        $setting->chatbot_position = $request->chatbot_position;
        $setting->chatbot_login_require = (bool) $request->chatbot_login_require;
        $setting->chatbot_rate_limit = $request->chatbot_rate_limit;
        $setting->chatbot_show_timestamp = (bool) $request->chatbot_show_timestamp;
        $setting->save();

        if ($request->chatbot_template && $request->first_message && $request->instructions)
        {
            $template = Chatbot::query()->find($request->chatbot_template);

            $template->update([
                'first_message' => $request->first_message,
                'instructions' => $request->instructions,
            ]);
        }

        return back()->with([
            'type' => 'success',
            'message' => trans('Chatbot settings updated successfully')
        ]);
    }

    public function index()
    {
        return view('panel.admin.chatbot.index', [
            'title' => trans('Chatbot Training'),
            'items' => Chatbot::query()->paginate(10)
        ]);
    }

    public function create()
    {
        $item = Chatbot::query()->create([
            'title' => 'Untitled chatbot',
            'model' => Helper::setting('openai_default_model'),
        ]);

        return to_route('dashboard.admin.chatbot.show', $item->getAttribute('id'));
    }

    public function store(ChatbotRequest $request)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['image'] = $request->file('logo')->store('chatbot');
        }

        Chatbot::query()->create($data);

        return to_route('dashboard.admin.chatbot.index')->with('success', trans('Chatbot Template Created Successfully'));
    }

    public function show(Chatbot $chatbot)
    {
        return view('panel.admin.chatbot.training', [
            'title' => trans('Chatbot Training'),
            'item' => $chatbot,
            'data' => $chatbot->data()->get(),
            'action' => route('dashboard.admin.chatbot.update', $chatbot),
        ]);
    }

    public function edit(Chatbot $chatbot)
    {
        return view('panel.admin.chatbot.form', [
            'title' => trans('Edit Chatbot'),
            'method' => 'put',
            'action' => route('dashboard.admin.chatbot.update', $chatbot),
            'item' => $chatbot
        ]);
    }

    public function update(ChatbotRequest $request, Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('This feature is disabled in demo mode.')
                ]);
            }

            return back()->with([
                'type' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['image'] = $request->file('logo')->store('chatbot');
        }

        $chatbot->update($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => trans('Chatbot Updated Successfully'),
                'status' => 'success'
            ]);
        }

        return back()->with([
            'type' => 'success',
            'message' => trans('Chatbot Updated Successfully'),
        ]);

    }

    public function destroy(Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $chatbotId = $chatbot->getAttribute('id');

        ChatbotData::query()->where('chatbot_id', $chatbotId)->delete();

        ChatbotDataVector::query()->where('chatbot_id', $chatbotId)->delete();

        $chatbot->delete();

        return response()->json([
            'message' => trans('Chatbot Deleted Successfully'),
            'reload' => true,
            'setTimeOut' => 1000
        ]);
    }
}
