<?php

namespace App\Business\Job;

use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Injector\Injector;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use App\Http\Requests\Qwindo\UpsertSiteCategoryRequest;
use App\Model\Document\BusinessLog;

/**
 * JobFactory
 */
class JobFactory
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const CLASS_NAMES = [
        ApiRequest::QUEUE_SITE => [
            ApiRequest::ACTION_CREATE => 'App\Business\Job\CreateSiteJob',
        ],

        ApiRequest::QUEUE_CATEGORY => [
            ApiRequest::ACTION_UPSERT => 'App\Business\Job\UpsertSiteCategoryJob',
        ],

        ApiRequest::QUEUE_PRODUCT => [
            ApiRequest::ACTION_DELETE => 'App\Business\Job\DeleteProductJob',
            ApiRequest::ACTION_UPSERT => 'App\Business\Job\UpsertProductJob',
        ],
    ];

    /**
     * Job entity
     *
     * @access private
     * @var BaseJob
     */
    private $job;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object constructor
     *
     * @access public
     * @param Injector           $injector
     * @param BusinessLogManager $businessLogManager
     * @return void
     */
    public function __construct(Injector $injector, BusinessLogManager $businessLogManager)
    {
        $this->injector = $injector;
        $this->businessLogManager = $businessLogManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create an instance of specific job
     *
     * @access public
     * @param  string $queue
     * @param  array  $values
     * @return BaseJob
     */
    public function create(string $queue, array $values) : BaseJob
    {
        $className = self::CLASS_NAMES[$queue][$values['crud_operation']];

        //create the job, inject the managers
        $this->job = new $className();
        $this->job = $this->injector->inject($this->job);

        //fill the instance with data
        try {
            $this->fillJob($queue, $values);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            //business log to admin level
            $this->businessLogManager->error(
                BusinessLog::USER_TYPE_ADMIN,
                BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$queue],
                "Error processing a message from queue [ {$queue} ]",
                json_encode([
                    'request' => $values,
                    'errors' => [
                        0 => $e->getMessage(),
                        1 => $e->getTraceAsString(),
                    ],
                ])
            );

            $this->job->setErrors([
                0 => 'There was an unexpected error trying to create your job'
            ]);
        }

        return $this->job;
    }

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fills the SiteJob instance data container
     *
     * @access private
     * @param  string $queue
     * @param  array $values
     * @return void
     */
    private function fillJob(string $queue, array $values) : void
    {
        switch ($queue) {
            case ApiRequest::QUEUE_SITE:
                $this->job->data['site'] = $values['site'];
                $this->job->data['merchant'] = $values['merchant'];
                break;

            case ApiRequest::QUEUE_CATEGORY:
                $this->job->data['tree'] = $values['tree'];
                $this->job->data['user'] = $values['user'];
                break;

            case ApiRequest::QUEUE_PRODUCT:
                $this->job->data['product'] = $values['product'];
                $this->job->data['user'] = $values['user'];
                break;
        }
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
