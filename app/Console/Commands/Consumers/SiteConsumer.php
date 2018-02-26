<?php

namespace App\Console\Commands\Consumers;

use App\Business\Api\Request\ApiRequest;
use App\Business\Message\MessageManager;
use Illuminate\Console\Command;

class SiteConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer:site {--daemon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read message from site queue and create sites accordingly';

    /**
     * The console command description.
     *
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
     * @return mixed
     */
    public function handle()
    {
        $asDaemon = $this->option('daemon');

        $this->messageManager->consumeJobMessage(ApiRequest::MSG_CREATE_SITE, $asDaemon);
    }
}
