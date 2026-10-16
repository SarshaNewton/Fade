@php
    $scan = $email->securityScan;
    $findings = $scan->security_reasons ?? [];
    $risk = $scan?->risk;
    
   $riskConfig = [
        'minimal' => [
            'background' => 'bg-info-gradient',
            'title' => 'Minimal Risk',
            'subtitle' => 'We found very few concerns in this email.',
        ],
        'low' => [
            'background' => 'bg-info-gradient',
            'title' => 'Low Risk',
            'subtitle' => 'We found minor indicators that are unlikely to pose a serious threat.',
        ],
        'medium' => [
            'background' => 'bg-warning-gradient',
            'title' => 'Medium Risk',
            'subtitle' => 'We found several indicators that suggest this email should be reviewed carefully.',
        ],
        'high' => [
            'background' => 'bg-dangerous-gradient',
            'title' => 'High Risk',
            'subtitle' => 'We found strong warning signs commonly associated with suspicious or deceptive emails.',
        ],
        'critical' => [
            'background' => 'bg-dangerous-gradient',
            'title' => 'Critical Risk',
            'subtitle' => 'We found multiple severe indicators that strongly suggest this email may be malicious.',
        ],
    ];

    $header = $riskConfig[$risk];
    $recommendedActions = collect();
    $findingDeets = collect();

    foreach($findings as $ruleKey => $messages){
        $category = findSecurityCategory($ruleKey);

        $config = $category
            ? config("security.$category.$ruleKey", [])
            : [];

        if (empty($config)) {
            continue;
        }

        $actionKeys = $config['action_keys'] ?? [];

        $actions = collect($actionKeys)
            ->map(fn ($key) => config("security.actions.$key"))
            ->filter()
            ->values();

        $findingDeets->push([
            'key' => $ruleKey,
            'category' => $category,
            'label' => $config['label'] ?? Str::headline($ruleKey),
            'description' => $config['description'] ?? null,
            'severity' => $config['severity'] ?? 'info',
            'count' => count($messages),
            'messages' => $messages,
            'action_keys' => $actionKeys,
            'actions' => $actions,
        ]);
        
        $recommendedActions = $recommendedActions->merge($actions);
    }
    $recommendedActions = $recommendedActions->unique()->values();
@endphp

<div class="flex-1 flex-col bg-white text-fade-text-primary w-full overflow-hidden">
    @if($scan)
    <div class="flex-1 min-w-0 h-full overflow-y-auto fade-scrollbar-transparent rtl-scroll">
        <div class="flex flex-col min-h-full ltr-content">
            <div class="{{$header['background']}} p-10 flex flex-col justify-center items-center w-full gap-6">
                <div class="flex flex-col w-full justify-center items-center gap-2">
                    <div class="flex items-center gap-2">
                        <x-security-badge :level="$risk" :size="'h-6'" />
                        <h1 class="text-3xl text-fade-text-accent font-bold ">{{$header['title']}}</h1>
                    </div>
                    <p>{{$header['subtitle']}}</p>
                </div>

                <div class="flex w-full gap-5">
                    <div class="flex-1 flex flex-col items-start gap-2 bg-white/30 backdrop:blur-md p-5 rounded-xl">
                        <p class="font-bold">Recommended Actions</p>
                        <ul class="list-disc list-outside pl-5 space-y-1 text-sm">
                            @foreach($recommendedActions as $recommendation)
                                <li>{{ $recommendation }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="flex-1 flex flex-col items-start gap-2 bg-white/30 backdrop:blur-md p-5 rounded-xl">
                        <p class="font-bold">Top Findings</p>
                        <div class="flex flex-row gap-2">
                            @foreach($findingDeets as $findingDeet)
                                <x-security-findings :severity="$findingDeet['severity']" :label="$findingDeet['label']"/>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 flex flex-col gap-6">
                <div x-data="{detailsExpanded : true}" id="detailedFindings" class="flex flex-col gap-2">
                    <button class="flex items-center gap-2"
                        type="button"
                        @click="detailsExpanded = !detailsExpanded"
                    >
                        <x-lucide-chevron-down 
                            class="h-5 w-5 transition-transform duration-300"
                            x-bind:class="detailsExpanded ? 'rotate-180' : ''"
                            strokewidth="1"
                        />
                        <p>Detailed Findings</p>
                    </button>

                    <div class="flex flex-col gap-5"
                        x-show="detailsExpanded"
                        x-transition
                        x-cloak
                    >
                        <div class="grid grid-cols-3 gap-5 auto-rows-min">
                        @foreach($findingDeets as $findingDeet)
                            <x-security-detailed-finding
                                :severity="$findingDeet['severity']"
                                :label="$findingDeet['label']"
                                :description="$findingDeet['description']"
                                :messages="$findingDeet['messages']"
                                :actions="$findingDeet['actions']"
                                :count="$findingDeet['count']"
                            />
                        @endforeach
                        </div>
                    </div>
                </div>

                 <div x-data="{headerExpanded : false}">
                    <button class="flex items-center gap-2"
                        type="button"
                        @click="headerExpanded = !headerExpanded"
                    >
                        <x-lucide-chevron-down 
                            class="h-5 w-5 transition-transform duration-300"
                            x-bind:class="headerExpanded ? 'rotate-180' : ''"
                            strokewidth="1"
                        />
                        <p>Header Details</p>
                    </button>

                    <div class="flex flex-col gap-5"
                        x-show="headerExpanded"
                        x-transition
                        x-cloak
                    >
                        <x-security-header-details :headers="$email->headers ?? []" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div>
            <p>Email Scan not yet available</p>
        </div>
    @endif
</div>

@include('mail.partials.toolbars.side-detail-toolbar') 