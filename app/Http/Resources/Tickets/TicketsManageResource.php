<?php

namespace App\Http\Resources\Tickets;

use App\Http\Resources\Companies\CompanieselectResource;
use App\Http\Resources\Label\LabelSelectResource;
use App\Http\Resources\Priority\PriorityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\TicketsReply\TicketsReplyDetailsResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsManageResource extends JsonResource
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
            'status' => new StatusResource($Tickets->status),
            'status_id' => $Tickets->status_id,
            'priority' => new PriorityResource($Tickets->priority),
            'priority_id' => $Tickets->priority_id,
            'Companies' => new CompanieselectResource($Tickets->Companies),
            'Companies_id' => $Tickets->Companies_id,
            'labels' => LabelSelectResource::collection($Tickets->labels),
            'user' => new UserDetailsResource($Tickets->user),
            'user_id' => $Tickets->user_id,
            'agent' => new UserDetailsResource($Tickets->agent),
            'agent_id' => $Tickets->agent_id,
            'closedBy' => new UserDetailsResource($Tickets->closedBy),
            'closed_by' => $Tickets->closed_by,
            'closed_at' => $Tickets->closed_at ? $Tickets->closed_at->toISOString() : null,
            'created_at' => $Tickets->created_at ? $Tickets->created_at->toISOString() : null,
            'updated_at' => $Tickets->updated_at ? $Tickets->updated_at->toISOString() : null,
            'TicketsReplies' => TicketsReplyDetailsResource::collection($Tickets->TicketsReplies()->orderByDesc('created_at')->get()),
        ];
    }
}
