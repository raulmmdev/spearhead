<?php

namespace App\Console\Commands;

use App\Http\Requests\Qwindo\SaveSiteRequest;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * MessagePusher
 */
class MessagePusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @access protected
     * @var string
     */
    protected $signature = 'message:push
                    {queue=site : Queue name to be pushed to ["site", "product", "category", "image"].}
                    {number=10 : Number of randomg messages to be created.}
                    {usleep=0 : Max microseconds between messages.}';

    /**
     * The console command description.
     *
     * @access protected
     * @var string
     */
    protected $description = 'Pushes random messages into the Message Queue';

    /**
     * Create a new command instance
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
        $number = (int) $this->argument('number');
        $usleep = (int) $this->argument('usleep');
        $stats = [];

        $console = new ConsoleOutput();
        $progressBar = new ProgressBar($console, $number);
        
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        foreach (range(1, $number) as $i) {
            $values = $this->fillRequest($queue);

            $client = new Client();

            try {
                $response = $client->post(config('app.url') ."/api/{$queue}", [
                    \GuzzleHttp\RequestOptions::HEADERS => $headers,
                    \GuzzleHttp\RequestOptions::JSON => $values,
                ]);

                if (!isset($stats[$response->getStatusCode()])) {
                    $stats[$response->getStatusCode()] = 0;
                }

                $stats[$response->getStatusCode()] ++;
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            usleep(rand(0, $usleep));

            $progressBar->advance();
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

        $this->comment('');
        $this->comment('');

        $rows = [];
        foreach ($stats as $code => $total) {
            $rows[] = [
                $total,
                sprintf("%d (%s)", $code, Response::$statusTexts[$code]),
            ];
        }
        
        $table = new Table($console);
        $table->setHeaders(['Total Messages', 'HTTP Status Response']);
        $table->setRows($rows);
        $table->render();

        $this->comment('');

        $elapsedTime = microtime($get_as_float = true) - $_SERVER['REQUEST_TIME_FLOAT'];
        $memoryUsage = Helper::formatMemory(memory_get_usage(true));

        $this->comment("Required time [ {$elapsedTime} seconds ]");
        $this->comment("Required memory [ {$memoryUsage} ]");
    }

    /**
     * Fill the request data
     *
     * @access private
     * @param  string $queue
     * @return array
     */
    private function fillRequest(string $queue): array
    {
        $faker = \Faker\Factory::create();

        switch ($queue) {
            case SaveSiteRequest::QUEUE:
                $values = [
                    'name' => $faker->company
                ];
        }

        return $values;
    }
}
