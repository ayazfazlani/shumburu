<?php

namespace App\Livewire\Components;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    public $showNotificationCenter = false;
    public $unreadCount = 0;
    public $notifications = [];
    public $selectedNotification = null;

    protected $listeners = [
        'notificationReceived' => 'refreshNotifications',
        'markAsRead' => 'markNotificationAsRead',
        'notifications-refreshed' => 'loadNotifications',
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $notificationService = app(NotificationService::class);
            $this->unreadCount = $notificationService->getUnreadCount(Auth::user());
            $this->notifications = $notificationService->getRecentNotifications(Auth::user(), 20);
        }
    }

    public function toggleNotificationCenter()
    {
        $this->showNotificationCenter = !$this->showNotificationCenter;
        if ($this->showNotificationCenter) {
            $this->loadNotifications();
        }
    }

    public function markNotificationAsRead($notificationId)
    {
        if (Auth::check()) {
            $notificationService = app(NotificationService::class);
            $notificationService->markAsRead(Auth::user(), $notificationId);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
            $this->loadNotifications();
        }
    }

    public function refreshNotifications()
    {
        $this->loadNotifications();
        $this->dispatch('notifications-refreshed');
    }

    public function viewNotification($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $this->selectedNotification = $notification;
            $this->markNotificationAsRead($notificationId);
        }
    }

    public function render()
    {
        return view('livewire.components.notification-center');
    }
}
