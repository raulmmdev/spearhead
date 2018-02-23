<?php
namespace App\Business\Site;

use App\Http\Requests\SaveSite;
use App\Model\Entity\Site;
  
class SiteManager
{
	/**
     * Create a new site from a Request.
     *
     * @param  SaveSite  $request
     * @return Site
     */
    public function createSiteFromRequest(SaveSite $request)
    {
    	try { 
    		$site = new Site();
    		$site->name = $request->name;

    		$site->save();

    		return $site;
    	} catch(\Exception $e) {
    		\Log::error($e->getMessage());
    		return null;
    	}
    }
}