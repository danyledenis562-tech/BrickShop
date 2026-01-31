<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::query()->first() ?? new Setting();

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone_support' => ['nullable', 'string', 'max:50'],
            'telegram_support_url' => ['nullable', 'url', 'max:255'],
            'show_support_widget' => ['nullable', 'boolean'],
        ]);

        $setting = Setting::query()->first();

        if (! $setting) {
            $setting = Setting::create([
                'phone_support' => $data['phone_support'] ?? null,
                'telegram_support_url' => $data['telegram_support_url'] ?? null,
                'show_support_widget' => $request->boolean('show_support_widget'),
            ]);
        } else {
            $setting->update([
                'phone_support' => $data['phone_support'] ?? null,
                'telegram_support_url' => $data['telegram_support_url'] ?? null,
                'show_support_widget' => $request->boolean('show_support_widget'),
            ]);
        }

        return back()->with('toast', __('messages.settings_updated'));
    }
}
