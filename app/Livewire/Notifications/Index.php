<?php

namespace App\Livewire\Notifications;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $type = 'all'; // all, production_order_created, production_order_status_changed

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'type' => ['except' => 'all'],
    ];

    public function mount()
    {
        // Mark all notifications as read when user visits the page
        if (Auth::check()) {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        }
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $notificationService = app(NotificationService::class);
            $notificationService->markAsRead(Auth::user(), $notificationId);
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        }
    }

    public function deleteNotification($notificationId)
    {
        if (Auth::check()) {
            Auth::user()->notifications()->find($notificationId)?->delete();
        }
    }

    public function render()
    {
        if (!Auth::check()) {
            return view('livewire.notifications.index', ['notifications' => collect()]);
        }

        $query = Auth::user()->notifications()->latest();

        // Apply filters
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($this->type !== 'all') {
            $query->where('type', 'App\\Notifications\\' . ucfirst($this->type) . 'Notification');
        }

        $notifications = $query->paginate(20);

        return view('livewire.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
