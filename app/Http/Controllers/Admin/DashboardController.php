<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Member;
use App\Models\Message;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'members' => Member::count(),
            'groups' => Group::count(),
            'messages' => Message::count(),
            'students' => Member::where('type', 'student')->count(),
            'parents' => Member::where('type', 'parent')->count(),
        ];

        $recentMembers = Member::latest()->take(5)->get();
        $recentGroups = Group::withCount('members')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentMembers', 'recentGroups'));
    }
}
