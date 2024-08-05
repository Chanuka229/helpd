<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\TicketsReply
 *
 * @property int $id
 * @property int|null $Tickets_id
 * @property int|null $user_id
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Tickets|null $Tickets
 * @property-read Collection|\App\Models\File[] $TicketsAttachments
 * @property-read int|null $Tickets_attachments_count
 * @property-read \App\Models\User|null $user
 * @method static Builder|TicketsReply newModelQuery()
 * @method static Builder|TicketsReply newQuery()
 * @method static Builder|TicketsReply query()
 * @method static Builder|TicketsReply whereBody($value)
 * @method static Builder|TicketsReply whereCreatedAt($value)
 * @method static Builder|TicketsReply whereId($value)
 * @method static Builder|TicketsReply whereTicketsId($value)
 * @method static Builder|TicketsReply whereUpdatedAt($value)
 * @method static Builder|TicketsReply whereUserId($value)
 * @mixin Eloquent
 */
class TicketsReply extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Tickets(): BelongsTo
    {
        return $this->belongsTo(Tickets::class);
    }

    public function TicketsAttachments(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'Tickets_attachments');
    }
}
