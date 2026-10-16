<?php
namespace App\Livewire;

use App\Models\User;
use App\Actions\HandleWorkspaceOnboarding;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class OnboardingWizard extends Component
{
    public int $step = 0;
    public string $name = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $hasConnectedAccount = false;
    protected HandleWorkspaceOnboarding $workspaceOnboarding; 

    public function boot(HandleWorkspaceOnboarding $workspaceOnboarding): void {
        $this->workspaceOnboarding = $workspaceOnboarding;
    }

    // If the user is already logged in auto fill 
    public function mount(): void{
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->username = $user->username ?? '';


        $this->hasConnectedAccount = auth()->check() && auth()->user()->emailAccounts()->exists();
    }

    public function next(): void{
        $this->validateStep();
        $this->step++;
    }

    public function back(): void{
        $this->step = max(0, $this->step - 1);
    }

    public function finish(): void{
        $this->validateStep();

        $user = Auth::user();

        if (! $user) {
            $user = User::create([
                'name' => strtolower($this->name),
                'username' => strtolower($this->username),
                'password' => Hash::make($this->password),
                'setup_completed_at' => now(),
            ]);

            Auth::login($user);
        } else {
            $user->name = strtolower($this->name);
            $user->username = strtolower($this->username);
            $user->password = Hash::make($this->password);
            $user->setup_completed_at = now();
            $user->save();
        }
        
        if (! $user->workspaces()->exists()) {
            $this->workspaceOnboarding->handle($user, $user->username);
        }

        if (! $this->userHasEmailAccount()) {
            $this->step = 4;
            return;
        }

        redirect()->route('mail.index');
    }

    public function render(){
            return view('livewire.onboarding-wizard')
            ->layout('layouts.onboarding');
    }

    private function validateStep(): void{
        $userId = Auth::id();

        if($this->step === 1){
            $this->validate([
                'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s\'-]+$/u']
            ]);
        }

        if($this->step === 2){
            $this->validate([
                'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)]
            ]);
        }

        if($this->step === 3){
            $this->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);
        }

    }

    private function userHasEmailAccount(): bool{
        return Auth::user()
            ->emailAccounts()
            ->exists();
    }
    
}
