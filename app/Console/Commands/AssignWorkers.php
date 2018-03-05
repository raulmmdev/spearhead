<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * AssignWorkers
 */
class AssignWorkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @access protected
     * @var string
     */
    protected $signature = 'consumer:assign-workers
                            {queue=site : Queue name}
                            {workers=1 : Number of workers assigned to the queue}';

    /**
     * The console command description.
     *
     * @access protected
     * @var string
     */
    protected $description = 'Create workers for a specified queue';

    /**
     * Create a new command instance.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @access public
     * @return void
     */
    public function handle(): void
    {
        $queue = $this->argument('queue');
        $workers = (int) $this->argument('workers');

        $command = "php artisan consumer:{$queue} --daemon";
        $backgroundCommand = "{$command} > /dev/null 2>&1 &";

        foreach (range(1, $workers) as $i) {
            exec($backgroundCommand);
        }

        $strpadWorkers = str_pad($workers, 8, ' ', STR_PAD_LEFT);
        $strpadQueue = str_pad($queue, 8, ' ', STR_PAD_RIGHT);
        
        $this->info("Successfully assigned [ {$strpadWorkers} ] workers to queue [ {$strpadQueue} ]");
    }
}
