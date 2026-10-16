<?php

namespace App\Services\Ai\Providers;

use App\Models\Email;
use App\Services\Ai\DTO\AiEmailAnalysisResult;
use App\Services\Ai\Contracts\AiProviderInterface;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiProvider implements AiProviderInterface {
    
    public function analyzeEmail(Email $email, array $text): AiEmailAnalysisResult{
        // Get language of ai response
        $language = $email->emailAccount->user->userPreference->ai_language;
        $prompt = $this->buildPrompt($text, $language);

        $response = null;
        $usedModel = null;

        try {
            foreach (config('ai.gemini.models') as $model) {
                $response = Http::timeout(60)
                ->retry(3, 2000)
                ->withHeaders([
                    'x-goog-api-key' => config('ai.gemini.api_key'),
                    'Content-Type'   => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ]);

                if ($response->successful()) {
                    $usedModel = $model;
                    break;
                }

                logger()->warning('Gemini model failed', [
                    'model' => $model,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            }
            
            if (! $response?->successful()) {
                logger()->error('All Gemini models failed');
            }

            $aiResponseText = $response->json('candidates.0.content.parts.0.text');
            $cleanedResponse = trim($aiResponseText);
            $cleanedResponse = preg_replace('/^```json\s*/', '', $cleanedResponse);
            $cleanedResponse = preg_replace('/^```\s*/', '', $cleanedResponse);
            $cleanedResponse = preg_replace('/\s*```$/', '', $cleanedResponse);

            $decoded = json_decode($cleanedResponse, true);

            return AiEmailAnalysisResult::fromArray($decoded, $usedModel);

        } catch (\Exception $e) {
            Log::error('Gemini Connection Failed', ['message' => $e->getMessage()]);
            return AiEmailAnalysisResult::fromArray([]);
        }
    }

    protected function buildPrompt(array $text, string $language): string{
        return "
            Analyze this email and return ONLY valid raw JSON.
            Respond in {$language}.

            Use this EXACT structure:
            {
            \"short_summary\": \"...\",
            \"detailed_summary\": \"...\",
            \"tone\": \"neutral\",
            \"intent\": \"informative\",
            \"secondary_intents\": [],
            \"urgency\": \"low\",
            \"action_needed\": []
            }

            STRICT RULES:
            - Return ONLY raw JSON.
            - Do NOT use markdown.
            - Do NOT wrap JSON in code blocks.
            - Do NOT include explanations.

            SUMMARIZATION RULES:
            - \"short_summary\" is for inbox triage.
            - It MUST be concise (8–15 words maximum).
            - It should help the user understand the email at a glance.
            - \"detailed_summary\" is for the email detail view.
            - It may be 1–2 concise sentences.
            - Do NOT copy the email text verbatim unless necessary.

            RECIPIENT / PERSPECTIVE RULES:
            - The token [USER_NAME] represents the recipient of the email.
            - Never mention [USER_NAME] in the response.
            - Write summaries as if speaking directly to the recipient.
            - Refer to the recipient as \"you\".
            - Do NOT write summaries like \"The sender is asking Sarsha...\"
            - Instead write \"You are being asked...\"

            INTENT RULES:
            - \"intent\" must contain ONLY the SINGLE strongest PRIMARY intent.

            Allowed intent values:
            informative, requesting, thankful, apologetic, complaining,
            advisory, congratulatory, commanding, promotional,
            notification, reminder, confirmation, warning,
            invitation, other

            SECONDARY INTENT RULES:
            - Include secondary intents ONLY if they are genuinely meaningful.
            - If the email has only one clear dominant intent, return [].
            - Do NOT invent secondary intents to fill the field.
            - Do NOT repeat the primary intent.

            TONE RULES:
            Allowed tone values:
            positive, neutral, negative, formal, informal, urgent

            URGENCY RULES:
            Allowed urgency values:
            low, medium, high

            ACTION RULES:
            - Extract ONLY meaningful actionable tasks explicitly or strongly implied for the recipient.
            - If no real action is required, return [].
            - NEVER invent fake actions such as:
            \"Read the email\"
            \"Review information\"
            \"Be aware of this update\"
            - Invitations or requests may count as actionable only if participation is genuinely expected.

            Subject:
            {$text['subject']}

            Body:
            {$text['body']}
        ";
    }

}