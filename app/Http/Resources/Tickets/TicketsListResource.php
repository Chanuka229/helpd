<?php

namespace App\Http\Resources\Tickets;

use App\Http\Resources\Companies\CompanieselectResource;
use App\Http\Resources\Label\LabelSelectResource;
use App\Http\Resources\Priority\PriorityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\TicketsReply\TicketsReplyQuickDetailsResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsListResource extends JsonResource
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
            'lastReply' => new TicketsReplyQuickDetailsResource($Tickets->TicketsReplies->last()),
            'status' => new StatusResource($Tickets->status),
            'priority' => new PriorityResource($Tickets->priority),
            'Companies' => new CompanieselectResource($Tickets->Companies),
            'labels' => LabelSelectResource::collection($Tickets->labels),
            'user' => new UserDetailsResource($Tickets->user),
            'agent' => new UserDetailsResource($Tickets->agent),
            'closedBy' => new UserDetailsResource($Tickets->closedBy),
            'closed_at' => $Tickets->closed_at ? $Tickets->closed_at->toISOString() : null,
            'created_at' => $Tickets->created_at->toISOString(),
            'updated_at' => $Tickets->updated_at->toISOString()
        ];
    }
}
