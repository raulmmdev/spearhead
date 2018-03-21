<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qwindo\CreateSiteRequest;
use App\Model\Document\BusinessLog;
use App\Model\Entity\Repository\SiteRepository;
use App\Model\Entity\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SiteController
 */
class SiteController extends Controller
{
    const RESPONSE_TYPES = [
        'createSite' => 'site',
    ];

    /**
     * $messageManager
     * @access private
     * @var MessageManager
     */
    private $messageManager;

    /**
     * $apiResponseManager
     * @access private
     * @var ApiResponseManager
     */
    private $apiResponseManager;

    /**
     * $businessLogManager
     * @access private
     * @var BusinessLogManager
     */
    private $businessLogManager;

    /**
     * $SiteRepository
     * @access private
     * @var SiteRepository
     */
    private $siteRepository;

    /**
     * $userRepository
     * @access private
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @access public
     * @param MessageManager
     * @param ApiResponseManager
     * @param BusinessLogManager
     * @param SiteRepository
     * @param UserRepository
     */
    public function __construct(
        MessageManager $messageManager,
        ApiResponseManager $apiResponseManager,
        BusinessLogManager $businessLogManager,
        SiteRepository $siteRepository,
        UserRepository $userRepository
    ) {
        $this->messageManager = $messageManager;
        $this->apiResponseManager = $apiResponseManager;
        $this->businessLogManager = $businessLogManager;
        $this->siteRepository = $siteRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new site job from a Request.
     *
     * @access public
     * @param CreateSiteRequest $request
     * @return JsonResponse
     */
    public function createSite(CreateSiteRequest $request): \Illuminate\Http\JsonResponse
    {
        //$user = Auth::guard('api')->user();
        $result = $request->resolve();
        $result = json_decode($result, true);

        if (!empty($result['errors'])) {
            $this->businessLogManager->error(
                BusinessLog::USER_TYPE_MERCHANT,
                BusinessLog::ELEMENT_TYPE_SITE,
                'There was an error tryng to process a site creation request.',
                json_encode($request->all()),
                BusinessLog::HTTP_TYPE_PUSH
            );

            $this->businessLogManager->error(
                BusinessLog::USER_TYPE_ADMIN,
                BusinessLog::ELEMENT_TYPE_SITE,
                'There was an error tryng to process a site creation request.',
                json_encode($request->all()) . json_encode($result),
                BusinessLog::HTTP_TYPE_PUSH
            );

            return $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ErrorCode::ERROR_CODE_PROCESS_REQUEST
                );
        }

        $this->businessLogManager->info(
            BusinessLog::USER_TYPE_MERCHANT,
            BusinessLog::ELEMENT_TYPE_SITE,
            'Site creation request has been processed.',
            json_encode($result['object']),
            BusinessLog::HTTP_TYPE_PUSH
        );

        $siteResponse = $this->formatResultObject($result['object']);

        return $this
            ->apiResponseManager
            ->createResponse(
                Response::HTTP_CREATED,
                self::RESPONSE_TYPES[__FUNCTION__],
                $siteResponse
            );
    }

    /**
     * formatResultObject
     *
     * @access private
     * @param  array  $result
     * @return array
     */
    public function formatResultObject(array $result) : array
    {
        //retrieve the api feature and add it to the site array, make sure the key is returned
        $site = $this->siteRepository->find($result['id']);
        $feature = $site->apiFeatures()->first();
        $feature->makeVisible('key');
        $result['apiFeature'] = $feature;

        $user = $this->userRepository->find($result['user_id']);
        $result['user'] = $user;

        return $result;
    }
}
