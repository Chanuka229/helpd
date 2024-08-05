<?php

namespace App\Http\Resources\TicketsReply;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\TicketsReply;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsReplyDetailsResource extends JsonResource
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
            'user' => new UserDetailsResource($TicketsReply->user),
            'body' => preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $TicketsReply->body),
            'created_at' => $TicketsReply->created_at->toISOString(),
            'attachments' => FileResource::collection($TicketsReply->TicketsAttachments)
        ];
    }
}
