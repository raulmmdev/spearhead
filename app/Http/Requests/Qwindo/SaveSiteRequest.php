<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Site\SiteManager;
use App\Http\Requests\BaseRequest;
use App\Http\Requests\Qwindo\Interfaces\SiteManagerAwareInterface;

class SaveSiteRequest extends BaseRequest implements SiteManagerAwareInterface
{

    protected $siteManager;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }

    public function setSiteManager(SiteManager $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    public function getSiteManager(): SiteManager
    {
        return $this->siteManager;
    }

    protected function resolve()
    {
        return $this->siteManager->createSiteFromRequest($this);
    }
}
