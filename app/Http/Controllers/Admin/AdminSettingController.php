<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'          => 'required|string|max:100',
            'site_email'         => 'nullable|email|max:150',
            'site_phone'         => 'nullable|string|max:30',
            'site_address'       => 'nullable|string|max:200',
            'site_tagline'       => 'nullable|string|max:200',
            'footer_about'       => 'nullable|string|max:300',
            'meta_description'   => 'nullable|string|max:300',
            'currency_symbol'    => 'required|string|max:5',
            'free_shipping_over' => 'required|numeric|min:0',
            'shipping_cost'      => 'required|numeric|min:0',
            'announcement_text'  => 'nullable|string|max:200',
            'announcement_link'  => 'nullable|string|max:300',
            'announcement_link_text' => 'nullable|string|max:50',
            'social_instagram'   => 'nullable|url|max:300',
            'social_facebook'    => 'nullable|url|max:300',
            'social_twitter'     => 'nullable|url|max:300',
            'site_logo'          => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $old = SiteSetting::get('site_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_logo')->store('settings', 'public');
            SiteSetting::set('site_logo', $path);
        }

        // Save all text settings
        $fields = [
            'site_name', 'site_email', 'site_phone', 'site_address', 'site_tagline',
            'footer_about', 'meta_description', 'currency_symbol',
            'free_shipping_over', 'shipping_cost',
            'announcement_text', 'announcement_link', 'announcement_link_text',
            'social_instagram', 'social_facebook', 'social_twitter',
        ];

        foreach ($fields as $key) {
            SiteSetting::set($key, $request->input($key, ''));
        }

        // Clear caches
        Cache::forget('site_settings_all');
        Cache::forget('nav_categories');

        return back()->with('success', 'Settings saved.');
    }
}
