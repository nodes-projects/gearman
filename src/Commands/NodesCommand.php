<?php

namespace NodesGearman;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Facades\App;

/**
 * Class NodesCommand
 * @author cr@nodes.dk
 *
 * @package NodesGearman
 * @deprecated - Use the NodesCommand in the Nodes Laravel core
 */
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
        try {
            \Log::info('Processing NodesCommand with data: ' . is_array($this->data) ? json_encode($this->data) : ' unknown ');
            $function = $this->data['action'];
            $param = !empty($this->data['params']) ? $this->data['params'] : false;
            $result = App::make($this->data['class'])->$function($param);
            \Log::info('Done Processing NodesCommand with data: ' . json_encode($this->data) . ' result: ' . json_encode($result));
        } catch(\Exception $e) {
            try {
                return \Log::error('Error Processing NodesCommand with data: ' . is_array($this->data) ? json_encode($this->data) : ' unknown ' . ' result: ' . $e->getMessage());
            } catch(\Exception $e) {
                return \Log::error('Error Processing NodesCommand');
            }
        }

    }
}