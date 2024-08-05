<?php

namespace App\Http\Resources\Tickets;

use App\Http\Resources\Companies\CompanieselectResource;
use App\Http\Resources\TicketsReply\TicketsReplyDetailsResource;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Tickets $Tickets */
        $Tickets = $this;
        return [
            'id' => $Tickets->id,
            'uuid' => $Tickets->uuid,
            'subject' => $Tickets->subject,
            'Companies' => new CompanieselectResource($Tickets->Companies),
            'Companies_id' => $Tickets->Companies_id,
            'created_at' => $Tickets->created_at->toISOString(),
            'updated_at' => $Tickets->updated_at->toISOString(),
            'TicketsReplies' => TicketsReplyDetailsResource::collection($Tickets->TicketsReplies()->orderByDesc('created_at')->get()),
        ];
    }
}
