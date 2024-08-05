<?php

namespace App\Http\Resources\Companies;

use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanieselectResource extends JsonResource
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
        ];
    }
}
