<?php

namespace App\Http\Resources\TicketsReply;

use App\Models\TicketsReply;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class TicketsReplyQuickDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var TicketsReply $TicketsReply */
        $TicketsReply = $this;
        return [
            'id' => $TicketsReply->id,
            'body' => Str::words(strip_tags(str_ireplace(['<br />', '<br>', '<br/>'], ' ', $TicketsReply->body)), 50),
            'created_at' => $TicketsReply->created_at->toISOString(),
        ];
    }
}
