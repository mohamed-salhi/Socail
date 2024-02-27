<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TemporaryBanUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $userId;
    protected $newStatus;

    public function __construct($userId , $newStatus )
    {
        $this->userId = $userId ;
        $this->newStatus = $newStatus ;
    }




    public function handle(): void
    {
        $user = User::query()->findOrFail($this->userId);
        if ($user) {
            $user->update(['status' => $this->newStatus]);
        }
    }
}
