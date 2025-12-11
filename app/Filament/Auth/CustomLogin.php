<?php

namespace App\Filament\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class CustomLogin extends Login
{
    protected string $view = 'filament.auth.custom-login';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Username or Email'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $login = $data['login'];

        // Determine if login is email or username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();
        $login = $data['login'];
        $password = $data['password'];

        // Find user by username or email using the helper method
        $user = User::findByUsernameOrEmail($login);

        // Check if user exists and password is correct
        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            $this->fireFailedEvent(Filament::auth(), $user ?? null, $this->getCredentialsFromFormData($data));
            $this->throwFailureValidationException();
        }

        // Check if user is active
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'data.login' => __('Your account is not active. Please contact an administrator.'),
            ]);
        }

        // Check if user can access the panel (system user only)
        $panel = Filament::getCurrentPanel();
        if (!$user->canAccessPanel($panel)) {
            throw ValidationException::withMessages([
                'data.login' => __('You do not have permission to access this panel. Only system administrators can access the admin panel.'),
            ]);
        }

        // Login the user - session will expire after 20 hours (configured in config/session.php)
        Filament::auth()->login($user, $data['remember'] ?? false);

        session()->regenerate();

        // Show success notification
        Notification::make()
            ->title('Login Successful')
            ->body('Welcome back! You have successfully logged in.')
            ->success()
            ->send();

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('Invalid username/email or password.'),
        ]);
    }
}

