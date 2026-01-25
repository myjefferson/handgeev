<?php

namespace App\Jobs;

use App\Models\InputConnection;
use App\Models\Topic;
use App\Services\InputConnectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExecuteInputConnectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    protected $connection;
    protected $topic;

    public function __construct(InputConnection $connection, Topic $topic)
    {
        $this->connection = $connection;
        $this->topic = $topic;
    }

    public function handle(InputConnectionService $service)
    {
        $result = $service->executeConnection($this->connection, $this->topic);
        
        if (!$result['success']) {
            $this->fail(new \Exception($result['message']));
        }
    }
}