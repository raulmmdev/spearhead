<?php

namespace App\Console\Commands;

use \Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\WithFaker;

class MessagePusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:push
                    {queue=site : Queue to be pushed to ["site", "product", "category", "image"]}
                    {number=10 : Number of messages to be created.}
                    {usleep=0 : Max microseconds between messages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pushes messages into the Message Queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

            try {
                $response = $client->post($endpoint, [
                    \GuzzleHttp\RequestOptions::HEADERS => $headers,
                    \GuzzleHttp\RequestOptions::JSON => $values,
                ]);

                if ($response->getStatusCode() === Response::HTTP_CREATED) {
                    $this->info('[ '. $i .' ] Message with values [ '. json_encode($values) .' ]... Success');
                } else {
                    $this->error('[ '. $i .' ] Message with values [ '. json_encode($values) .' ]... Fail');
                }
            } catch(\Exception $e) {
                $this->error('[ '. $i .' ] Message with values [ '. json_encode($values) .' ]... Exception');
                $this->error($e->getMessage());
            }

            usleep(rand(0, $usleep));
        }
    }

    private function fillRequest(string $queue)
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
