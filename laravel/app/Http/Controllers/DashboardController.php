<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceThreat;
use App\Models\EmailSecurityScan;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $userId = auth()->user()->id;
        $workspaceId = auth()->user()
            ->workspaces()
            ->first()?->id;

        $threats = WorkspaceThreat::with([
            'email',
            'securityScan',
        ])
        ->where('workspace_id', $workspaceId)
        ->latest('detected_at')
        ->take(10)
        ->get();

        $stats = [
            'totalThreats' => WorkspaceThreat::where('workspace_id', $workspaceId)->count(),

            'weeklyThreats' => WorkspaceThreat::where('workspace_id', $workspaceId)
                ->where('detected_at', '>=', now()->subDays(7))
                ->count(),

            'averageRiskScore' => EmailSecurityScan::whereHas('email.emailAccount', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->avg('score'),
        ];

        return view('dashboard', compact(
            'threats',
            'stats'
        ));
    }
}
