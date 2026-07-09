<?php
namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('enabled', true)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('announcements.index', compact('announcements'));
    }

    public function show(string $slug)
    {
        $announcement = Announcement::where('slug', $slug)
            ->where('enabled', true)
            ->with('author')
            ->firstOrFail();

        return view('announcements.show', compact('announcement'));
    }
}
