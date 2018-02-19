<?php

namespace App\Http\Requests;

use App\Business\Site\SiteManager;
use Illuminate\Foundation\Http\FormRequest;

class SaveSite extends FormRequest
{

    protected $siteManager;

    public function __construct(SiteManager $siteManager)
    {
        parent::__construct();
        $this->siteManager = $siteManager;
    }

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

    public function resolve()
    {
        return $this->siteManager->createSiteFromRequest($this);
    }
}
