<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Services\Ai\AiAnalysisService;

use Illuminate\Http\Request;

class AiController extends Controller {
    
    public function analyze(Email $email, AiAnalysisService $service) {
        if ($email->emailAccount->user_id !== auth()->id()) {
            abort(403);
        }

        $insight = $service->analyzeAndStore($email);

        return response()->json([
            'message' => 'Email analyzed successfully',
            'data' => $insight,
        ]);
    }
}
