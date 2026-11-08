<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use App\Services\SystemMonitoringService;

class AdminController extends Controller
{
    /** Laravel injects monitoring services to prepare dashboard health and library totals. */
    public function dashboard(SystemMonitoringService $monitoring)
    {
        $metrics = $monitoring->dashboardMetrics();
        $health  = [
            'Environment'     => app()->environment(),
            'Web application' => $metrics['health']['application'],
            'Database'        => $metrics['health']['database'],
            'Storage'         => $metrics['health']['storage'],
            'Laravel'         => app()->version(),
            'PHP'             => PHP_VERSION,
        ];
        $stats = [
            'users'    => User::count(),
            'books'    => Book::count(),
            'pending'  => Borrowing::where('status', 'pending')->count(),
            'requests' => Borrowing::count()];
        $recent = ActivityLog::with('user')->latest('created_at')->limit(10)->get();

        return view('admin', compact('health', 'stats', 'recent', 'metrics'));
    }

    /** Show recent activity records with their associated users. */
    public function activities()
    {
        $activities = ActivityLog::with('user')->latest('created_at')->paginate(25);

        return view('activities', compact('activities'));
    }
}
