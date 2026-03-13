<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $roleName = null;
        if ($user) {
            // Jika $user->role adalah relasi, ambil name, jika string, pakai langsung
            if (is_object($user->role) && isset($user->role->name)) {
                $roleName = $user->role->name;
            } elseif (is_string($user->role)) {
                $roleName = $user->role;
            }
        }
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? array_merge(
                    $user->toArray(),
                    [
                        'role' => $roleName,
                    ]
                ) : null,
            ],
            // Tambahkan CSRF token agar selalu tersedia di semua halaman Inertia
            'csrf_token' => csrf_token(),
            // Share flash message agar bisa diakses di frontend
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
