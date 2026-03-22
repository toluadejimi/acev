<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('layout.dashboard-modern', function ($view) {
            $row = Notification::query()->where('id', 1)->first();
            $message = trim((string) ($row->message ?? ''));
            $active = (bool) ($row->is_active ?? false);
            $dashAnnouncement = null;
            if ($row && $active && $message !== '') {
                $title = trim((string) ($row->title ?? ''));
                $dashAnnouncement = [
                    'title' => $title !== '' ? $title : 'Announcement',
                    'message' => $row->message,
                    'fingerprint' => hash('sha256', ($row->title ?? '') . '|' . $message . '|' . ($row->updated_at?->toIso8601String() ?? '')),
                ];
            }
            $view->with('dashAnnouncement', $dashAnnouncement);
        });
    }
}
