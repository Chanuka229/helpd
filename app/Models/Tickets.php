<?php

namespace App\Models;

use Eloquent;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tickets
 *
 * @property int $id
 * @property string $uuid
 * @property string $subject
 * @property int|null $status_id
 * @property int|null $priority_id
 * @property int|null $Companies_id
 * @property int|null $user_id
 * @property int|null $agent_id
 * @property int|null $closed_by
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $agent
 * @property-read User $closedBy
 * @property-read Companies|null $Companies
 * @property-read Collection|Label[] $labels
 * @property-read int|null $labels_count
 * @property-read Priority|null $priority
 * @property-read Status|null $status
 * @property-read Collection|TicketsReply[] $TicketsReplies
 * @property-read int|null $Tickets_replies_count
 * @property-read User|null $user
 * @method static Builder|Tickets filter($input = [], $filter = null)
 * @method static Builder|Tickets newModelQuery()
 * @method static Builder|Tickets newQuery()
 * @method static Builder|Tickets paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|Tickets query()
 * @method static Builder|Tickets simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|Tickets whereAgentId($value)
 * @method static Builder|Tickets whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|Tickets whereClosedAt($value)
 * @method static Builder|Tickets whereClosedBy($value)
 * @method static Builder|Tickets whereCreatedAt($value)
 * @method static Builder|Tickets whereCompaniesId($value)
 * @method static Builder|Tickets whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|Tickets whereId($value)
 * @method static Builder|Tickets whereLike($column, $value, $boolean = 'and')
 * @method static Builder|Tickets wherePriorityId($value)
 * @method static Builder|Tickets whereStatusId($value)
 * @method static Builder|Tickets whereSubject($value)
 * @method static Builder|Tickets whereUpdatedAt($value)
 * @method static Builder|Tickets whereUserId($value)
 * @method static Builder|Tickets whereUuid($value)
 * @mixin Eloquent
 */
class Tickets extends Model
{
    use Filterable, HasFactory;

    protected $casts = [
        'status_id' => 'integer',
        'priority_id' => 'integer',
        'Companies_id' => 'integer',
        'user_id' => 'integer',
        'agent_id' => 'integer',
        'closed_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function Companies(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function TicketsReplies(): HasMany
    {
        return $this->hasMany(TicketsReply::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'Tickets_labels');
    }

    public function verifyUser(User $user): bool
    {
        if ($user->role_id !== 1) {
            $userId = $user->id;
            return $this->Companies_id === null || ($this->Companies->all_agents || $this->Companies->agent()->pluck('id')->contains($userId)) || ($this->agent_id === null || $this->agent_id === $userId) || $this->closed_by === $userId;
        }
        return true;
    }
}
