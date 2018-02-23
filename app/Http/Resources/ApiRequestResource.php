<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiRequestResource extends Resource
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
            'type'          => 'message',
            'attributes'    => [
                'status' => 'ok',
            ],
        ];
    }
}
