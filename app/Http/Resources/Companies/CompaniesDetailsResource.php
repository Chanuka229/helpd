<?php

namespace App\Http\Resources\Companies;

use App\Http\Resources\User\UserDetailsResource;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesDetailsResource extends JsonResource
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
            'all_agents' => (bool) $Companies->all_agents,
            'public' => (bool) $Companies->public,
            'agents' => !$Companies->all_agents ? UserDetailsResource::collection($Companies->agent->take(5)) : []
        ];
    }
}
