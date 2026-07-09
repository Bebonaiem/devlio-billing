<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentInvoicesWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\ServerStatusWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Http\Middleware\EnsureUserIsAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverviewWidget::class,
                RevenueChartWidget::class,
                RecentOrdersWidget::class,
                RecentInvoicesWidget::class,
                ServerStatusWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Catalog')
                    ->icon('heroicon-o-cube'),
                NavigationGroup::make()
                    ->label('Orders & Billing')
                    ->icon('heroicon-o-shopping-cart'),
                NavigationGroup::make()
                    ->label('Users')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Servers')
                    ->icon('heroicon-o-server-stack'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
