<?php

namespace App\Providers;

use App\Models\Message;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      'App\Models\Admin' => 'App\Policies\AdminPolicy',
      'App\Models\User' => 'App\Policies\UserPolicy',
      'App\Models\Post' => 'App\Policies\PostPolicy',
      'App\Models\Group' => 'App\Policies\GroupPolicy',
      'App\Models\Message' => 'App\Policies\MessagePolicy',
      'App\Models\Comment' => 'App\Policies\CommentPolicy',
      'App\Models\Notification' => 'App\Policies\NotificationPolicy',
      Message::class => MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
