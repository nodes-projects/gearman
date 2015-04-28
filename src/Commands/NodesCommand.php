<?php

namespace NodesGearman;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Facades\App;

class NodesCommand extends Command implements SelfHandling, ShouldBeQueued {

    use InteractsWithQueue, SerializesModels;

    protected $data = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('Processing NodesCommand with data: ' . json_encode($this->data));

        try {
            $function = $this->data['action'];
            $param = !empty($this->data['params']) ? $this->data['params'] : false;
            $result = App::make($this->data['class'])->$function($param);
        } catch(\Exception $e) {
            return \Log::error('Error Processing NodesCommand with data: ' . json_encode($this->data) . ' result: ' . $e->getMessage());
        }

        \Log::info('Done Processing NodesCommand with data: ' . json_encode($this->data) . ' result: ' . json_encode($result));
    }
}