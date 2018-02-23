<?php

namespace App\Http\Controllers;

use App\Business\Site\SiteManager;
use App\Business\Error\ErrorCode;
use App\Http\Requests\SaveSite;
use App\Http\Resources\SiteResource;
use Illuminate\Http\Request;

class SiteController extends Controller
{
	/**
     * @var siteManager
     */
    protected $siteManager;

    /**
     * Create a new site from a Request.
     *
     * @param  SaveSite  $request
     * @return SiteResource
     */
    public function createSite(SaveSite $request)
	{
		//create the site
		$site = $request->resolve();

		if (!$site) {
			//something went wrong?
			return json_encode([
				"errors" => [
					"code"    => ErrorCode::ERROR_SAVE_SITE,
					"status"  => "500",
					"title" => ErrorCode::getMessage(ErrorCodes::ERROR_SAVE_SITE)
				],
			]);
		}

		//response
		SiteResource::withoutWrapping(); //remove the top data element wrapper
        return new SiteResource($site);
    }
}
	