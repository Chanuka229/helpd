<?php

namespace App\Http\Resources\Companies;

use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Companies $Companies */
        $Companies = $this;
        return [
            'id' => $Companies->id,
            'name' => $Companies->name,
            'all_agents' => $Companies->all_agents,
            'public' => $Companies->public,
            'agents' => $Companies->agent()->pluck('id')
        ];
    }
}
