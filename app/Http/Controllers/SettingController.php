<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function updateAdminEmail(Request $request)
    {
        $request->validate([
            'admin_email' => 'required|email|max:255',
        ]);

        SystemSetting::set('admin_email', $request->admin_email);

        return redirect()->back()->with('success', 'Admin Notification Email updated to: ' . $request->admin_email);
    }
}
