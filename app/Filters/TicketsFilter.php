<?php

namespace App\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class TicketsFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function search($search): TicketsFilter
    {
        return $this->where('subject', 'LIKE', '%'.$search.'%')
            ->orWhereHas('TicketsReplies', function (Builder $query) use ($search) {
                $query->where('body', 'LIKE', '%'.$search.'%');
            });
    }

    public function user($user): TicketsFilter
    {
        return $this->whereHas('user', function (Builder $query) use ($user) {
            $query->where('name', 'LIKE', '%'.$user.'%')
                ->orWhere('email', 'LIKE', '%'.$user.'%');
        });
    }

    public function agents($agents): TicketsFilter
    {
        return $this->whereIn('agent_id', $agents);
    }

    public function Companies($Companies): TicketsFilter
    {
        return $this->whereIn('Companies_id', $Companies);
    }

    public function labels($labels): TicketsFilter
    {
        return $this->whereHas('labels', function (Builder $query) use ($labels) {
            $query->whereIn('id', $labels);
        });
    }

    public function status($status): TicketsFilter
    {
        return $this->where('status_id', '=', $status);
    }

    public function statuses($statuses): TicketsFilter
    {
        return $this->whereIn('status_id', $statuses);
    }

    public function priorities($priorities): TicketsFilter
    {
        return $this->whereIn('priority_id', $priorities);
    }
}
