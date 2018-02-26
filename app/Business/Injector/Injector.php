<?php
namespace App\Business\Injector;

use App\Business\Site\SiteManager;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\Qwindo\Interfaces\SiteManagerAwareInterface;

class Injector
{
	public function __construct(SiteManager $siteManager)
	{
		$this->siteManager = $siteManager;
	}

	public function inject(BaseRequest $request)
	{
		if ($request instanceof SiteManagerAwareInterface) {
			$request->setSiteManager($this->siteManager);
		}

		return $request;
	}
}