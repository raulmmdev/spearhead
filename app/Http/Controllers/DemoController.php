<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Business\Demo\DemoService;

class DemoController extends Controller
{

    /**
     * The demo service.
     *
     * @var demoService
     */
    protected $demoService;

    /**
     * Create a new controller instance.
     *
     * @param  DemoService  $demoService
     * @return void
     */
    public function __construct(DemoService $demoService)
    {
        $this->demoService = $demoService;
    }


    public function index()
    {
        echo("Hola mundo");
        \Log::info($this->demoService->serviceMethod());
        //throw new \Exception("This is just a test exception!");
        die();
    }
}
