<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use app\Site;

class SiteResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'type'          => 'site',
            'id'            => $this->id,
            'attributes'    => [
                'name' => $this->name,
            ],
        ];
    }
}
