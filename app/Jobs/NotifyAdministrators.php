<?php

namespace App\Jobs;

use App\Enums\Role;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Notifications\RequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class NotifyAdministrators implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private RequestModel $request, private int $authUserId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $admin = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [Role::MAKER, Role::CHECKER]);
        })->whereNotIn('id', $this->authUserId)
            ->get();

        Notification::send($admin, new RequestNotification($this->request));
    }
}
