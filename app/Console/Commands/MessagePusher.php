<?php

namespace App\Console\Commands;

use \Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\WithFaker;

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
                    {queue=site : Queue to be pushed to ["site", "product", "category", "image"]}
                    {number=10 : Number of messages to be created.}
                    {usleep=0 : Max microseconds between messages}';

    /**
     * The console command description.
     *
     * @access protected
     * @var string
     */
    protected $description = 'Pushes messages into the Message Queue';

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

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        foreach (range(1, $number) as $i) {

            $values = $this->fillRequest($queue);

            $endpoint = config('app.url') ."/api/{$queue}";

            $client = new Client();

            $strpadI = str_pad($i, 8, ' ', STR_PAD_LEFT);
            $strpadQueue = str_pad($queue, 8, ' ', STR_PAD_RIGHT);
            $strpadMessage = str_pad($values['name'], 60, ' ', STR_PAD_RIGHT);
            $consoleLine = "[ {$strpadI} ] Pushing on queue [ {$strpadQueue} ] the message [ {$strpadMessage} ]...";

            try {
                $response = $client->post($endpoint, [
                    \GuzzleHttp\RequestOptions::HEADERS => $headers,
                    \GuzzleHttp\RequestOptions::JSON => $values,
                ]);

                if ($response->getStatusCode() === Response::HTTP_CREATED) {
                    $this->info("{$consoleLine} Success");
                } else {
                    $this->error("{$consoleLine} Fail");
                }
            } catch (\Exception $e) {
                $this->error("{$consoleLine} Exception");
                $this->error($e->getMessage());
            }

            usleep(rand(0, $usleep));
        }
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
