<?php

namespace App\Console\Commands\Consumers;

use App\Business\Message\MessageManager;
use App\Http\Requests\ApiRequest;
use Illuminate\Console\Command;

/**
 * SiteProducConsumer
 */
class ProductConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @access protected
     * @var string
     */
    protected $signature = 'consumer:product
                            {--daemon : Keeps the script listening to Message Queue after completing the task.}';

    /**
     * The console command description.
     *
     * @access protected
     * @var string
     */
    protected $description = 'Read messages from [ '. ApiRequest::QUEUE_PRODUCT .' ] queue';

    /**
     * The console command description.
     *
     * @access protected
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MessageManager $messageManager)
    {
        parent::__construct();

        $this->messageManager = $messageManager;
    }

    /**
     * Execute the console command.
     *
     * @access public
     * @return void
     */
    public function handle(): void
    {
        $asDaemon = $this->option('daemon');

        $this->messageManager->consumeJobMessage(ApiRequest::QUEUE_PRODUCT, $asDaemon);

        $elapsedTime = microtime($get_as_float = true) - $_SERVER['REQUEST_TIME_FLOAT'];
        $this->comment("Required time [ {$elapsedTime} seconds ]");
    }
}
