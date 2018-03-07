<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Helper;

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
                            {workers=1 : Number of workers assigned to the queue}
                            {--daemon : Keeps the script listening to Message Queue after completing the task.}
                            {--log : Send out the log process to log instead of /dev/null.}';

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
        $daemon = (bool) $this->option('daemon');
        $log = (bool) $this->option('log');

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
        
        $command  = "php artisan consumer:{$queue}";
        $command .= ($daemon) ? " --daemon" : "";

        foreach (range(1, $workers) as $i) {
            $output = ($log) ? "worker-{$queue}-{$i}.log" : "/dev/null";
            $backgroundCommand = "{$command} > {$output} 2>&1 &";

            exec($backgroundCommand);
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

        $elapsedTime = microtime($get_as_float = true) - $_SERVER['REQUEST_TIME_FLOAT'];
        $memoryUsage = Helper::formatMemory(memory_get_usage(true));
        $strpadWorkers = str_pad($workers, 4, ' ', STR_PAD_LEFT);
        $strpadQueue = str_pad($queue, 8, ' ', STR_PAD_RIGHT);
       
        $this->comment('');
        $this->info("Successfully assigned [ {$strpadWorkers} ] workers to queue [ {$strpadQueue} ]");
        $this->comment('');
        $this->comment("Required time [ {$elapsedTime} seconds ]");
        $this->comment("Required memory [ {$memoryUsage} ]");        
    }
}
