<?php

namespace App\Services\Ai\Contracts;

use App\Models\Email;
use App\Services\Ai\DTO\AiEmailAnalysisResult;

interface AiProviderInterface{

    public function analyzeEmail(Email $email, array $text): AiEmailAnalysisResult;
}