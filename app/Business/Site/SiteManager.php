<?php
namespace App\Business\Site;

use App\Http\Requests\Qwindo\SaveSiteRequest;
use App\Model\Entity\Site;
use App\Model\Document\BusinessLog;

class SiteManager
{
	/**
     * Create a new site from a Request.
     *
     * @param SaveSiteRequest $request
     * @return Site
     */
    public function createSiteFromRequest(SaveSiteRequest $request)
    {
        try {
    		$site = new Site();
    		$site->name = $request->name;
    		$site->save();

    		return $site;
    	} catch(\Exception $e) {
    		\Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

    		return null;
    	}
    }
}