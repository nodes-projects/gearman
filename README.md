## Nodes Gearman

Laravel 5 package to integrate Laravel Queue to Gearman

## How to use

Start by creating a Queue script. This is the script gearman should run. Here is an example file you can use in app/Commands/QueueTest.php

```php
<?php 

namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class QueueTest extends Command implements SelfHandling, ShouldBeQueued {

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
        \Log::error('Some error');

        \Log::info('Processing id: ' . $this->data['id']);
	}
}
```

To run the command from your code use:

```php
\Queue::push(new QueueTest(['id' => 1]));
```
If you just want your command to run a function in a repository or other class. You can use NodesCommand
Example

```php
\Queue::push(new NodesCommand([
    'class' => 'Oxcept\Models\ChatUser\ChatUserRepository',
    'action' => 'run',
    'params' => [
        'id' => 1,
        'name' => 'Casper is cool'
    ]
]));
```

If you integrate other files in your QueueTest.php you can print output by using Laravel log handler.

```php
\Log::info('Information from another file');
```

This will be catched and outputtet in the queue:listen function.
