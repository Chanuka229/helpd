<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Api\File\FileController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Tickets\StoreRequest;
use App\Http\Requests\Dashboard\Tickets\TicketsReplyRequest;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\CannedReply\CannedReplyResource;
use App\Http\Resources\Companies\CompanieselectResource;
use App\Http\Resources\Label\LabelSelectResource;
use App\Http\Resources\Priority\PriorityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Tickets\TicketsListResource;
use App\Http\Resources\Tickets\TicketsManageResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\CannedReply;
use App\Models\Companies;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tickets;
use App\Models\TicketsReply;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\Tickets\NewTicketsFromAgent;
use App\Notifications\Tickets\NewTicketsReplyFromAgentToUser;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use Throwable;

class TicketsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $sort = json_decode($request->get('sort', json_encode(['order' => 'asc', 'column' => 'created_at'], JSON_THROW_ON_ERROR)), true, 512, JSON_THROW_ON_ERROR);
        if ($user->role_id !== 1) {
            $items = Tickets::filter($request->all())
                ->where(function (Builder $query) use ($user) {
                    $query->where('agent_id', $user->id);
                    $query->orWhere('closed_by', $user->id);
                    $query->orWhereIn('Companies_id', $user->Companies()->pluck('id')->toArray());
                    $query->orWhere(function (Builder $query) use ($user) {
                        $Companies = array_unique(array_merge($user->Companies()->pluck('id')->toArray(), Companies::where('all_agents', 1)->pluck('id')->toArray()));
                        $query->whereNull('agent_id');
                        $query->whereIn('Companies_id', $Companies);
                    });
                })
                ->orderBy($sort['column'], $sort['order'])
                ->paginate((int) $request->get('perPage', 10));
        } else {
            $items = Tickets::filter($request->all())
                ->orderBy($sort['column'], $sort['order'])
                ->paginate((int) $request->get('perPage', 10));
        }
        return response()->json([
            'items' => TicketsListResource::collection($items->items()),
            'pagination' => [
                'currentPage' => $items->currentPage(),
                'perPage' => $items->perPage(),
                'total' => $items->total(),
                'totalPages' => $items->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $request->validated();
        $Tickets = new Tickets();
        $Tickets->uuid = Str::uuid();
        $Tickets->subject = $request->get('subject');
        $Tickets->status_id = $request->get('status_id');
        $Tickets->priority_id = $request->get('priority_id');
        $Tickets->Companies_id = $request->get('Companies_id');
        $Tickets->user_id = $request->get('user_id');
        $Tickets->agent_id = Auth::id();
        $Tickets->saveOrFail();
        $TicketsReply = new TicketsReply();
        $TicketsReply->user_id = Auth::id();
        $TicketsReply->body = $request->get('body');
        if ($Tickets->TicketsReplies()->save($TicketsReply)) {
            if ($request->has('attachments')) {
                $TicketsReply->TicketsAttachments()->sync(collect($request->get('attachments'))->map(function ($attachment) {
                    return $attachment['id'];
                }));
            }
            $Tickets->user->notify((new NewTicketsFromAgent($Tickets))->locale(Setting::getDecoded('app_locale')));
            return response()->json(['message' => __('Data saved correctly'), 'Tickets' => new TicketsManageResource($Tickets)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  Tickets  $Tickets
     * @return JsonResponse
     */
    public function show(Tickets $Tickets): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$Tickets->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this Tickets')], 403);
        }
        return response()->json(new TicketsManageResource($Tickets));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TicketsReplyRequest  $request
     * @param  Tickets  $Tickets
     * @return JsonResponse
     */
    public function reply(Tickets $Tickets, TicketsReplyRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$Tickets->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this Tickets')], 403);
        }
        $request->validated();
        $TicketsReply = new TicketsReply();
        $TicketsReply->user_id = Auth::id();
        $TicketsReply->body = $request->get('body');
        if ($Tickets->TicketsReplies()->save($TicketsReply)) {
            if ($request->has('attachments')) {
                $TicketsReply->TicketsAttachments()->sync(collect($request->get('attachments'))->map(function ($attachment) {
                    return $attachment['id'];
                }));
            }
            $Tickets->status_id = $request->get('status_id');
            $Tickets->updated_at = Carbon::now();
            $Tickets->save();
            $Tickets->user->notify((new NewTicketsReplyFromAgentToUser($Tickets))->locale(Setting::getDecoded('app_locale')));
            return response()->json(['message' => __('Data saved correctly'), 'Tickets' => new TicketsManageResource($Tickets)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tickets  $Tickets
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Tickets $Tickets): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$Tickets->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this Tickets')], 403);
        }
        $Tickets->labels()->sync([]);
        foreach ($Tickets->TicketsReplies()->get() as $TicketsReply) {
            $TicketsReply->TicketsAttachments()->sync([]);
        }
        $Tickets->TicketsReplies()->delete();
        if ($Tickets->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }

    public function filters(): JsonResponse
    {
        $roles = UserRole::where('dashboard_access', true)->pluck('id');
        $agents = User::whereIn('role_id', $roles)->where('status', true)->get();

        return response()->json([
            'agents' => UserDetailsResource::collection($agents),
            'customers' => UserDetailsResource::collection(User::where('status', true)->get()),
            'Companies' => CompanieselectResource::collection(Companies::all()),
            'labels' => LabelSelectResource::collection(Label::all()),
            'statuses' => StatusResource::collection(Status::all()),
            'priorities' => PriorityResource::collection(Priority::orderBy('value')->get()),
        ]);
    }

    public function cannedReplies(): JsonResponse
    {
        return response()->json(CannedReplyResource::collection(CannedReply::where(function ($builder) {
            /** @var Builder|CannedReply $builder */
            $builder->where('shared', '=', true)
                ->orWhere('user_id', '=', Auth::id());
        })->get()));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function quickActions(Request $request): JsonResponse
    {
        $action = $request->get('action');
        /** @var User $user */
        $user = Auth::user();
        $Tickets = Tickets::whereIn('id', $request->get('Tickets'));
        if ($user && $user->role_id !== 1) {
            $Tickets->where(function (Builder $query) use ($user) {
                $query->where('agent_id', $user->id);
                $query->orWhere('closed_by', $user->id);
                $query->orWhereIn('Companies_id', $user->Companies()->pluck('id')->toArray());
                $query->orWhere(function (Builder $query) use ($user) {
                    $Companies = array_unique(array_merge($user->Companies()->pluck('id')->toArray(), Companies::where('all_agents', 1)->pluck('id')->toArray()));
                    $query->whereNull('agent_id');
                    $query->whereIn('Companies_id', $Companies);
                });
            });
        }
        if ($Tickets->count() < 1) {
            return response()->json(['message' => __('You have not selected a Tickets or do not have permissions to perform this action')], 403);
        }
        if ($action === 'agent') {
            $Tickets->update(['agent_id' => $request->get('value')]);
            return response()->json(['message' => __('Tickets assigned to the selected agent')]);
        }
        if ($action === 'Companies') {
            $Tickets->update(['Companies_id' => $request->get('value')]);
            return response()->json(['message' => __('Tickets assigned to the selected Companies')]);
        }
        if ($action === 'label') {
            foreach ($Tickets->get() as $Tickets) {
                /** @var Tickets $Tickets */
                $Tickets->labels()->syncWithoutDetaching($request->get('value'));
                $Tickets->updated_at = Carbon::now();
                $Tickets->save();
            }
            return response()->json(['message' => __('The label has been added to selected Tickets')]);
        }
        if ($action === 'priority') {
            $Tickets->update(['priority_id' => $request->get('value')]);
            return response()->json(['message' => __('The priority of the selected Tickets has been changed')]);
        }
        if ($action === 'delete') {
            foreach ($Tickets->get() as $Tickets) {
                /** @var Tickets $Tickets */
                $Tickets->labels()->sync([]);
                foreach ($Tickets->TicketsReplies()->get() as $TicketsReply) {
                    $TicketsReply->TicketsAttachments()->sync([]);
                }
                $Tickets->TicketsReplies()->delete();
                $Tickets->delete();
            }
            return response()->json(['message' => __('The selected Tickets have been deleted')]);
        }
        return response()->json(['message' => __('Quick action not found')], 404);
    }

    /**
     * @param  Tickets  $Tickets
     * @param  Request  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function TicketsQuickActions(Tickets $Tickets, Request $request): JsonResponse
    {
        $value = $request->get('value');
        $action = $request->get('action');
        /** @var User $user */
        $user = Auth::user();
        if (!$Tickets->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this Tickets')], 403);
        }
        if ($action === 'agent') {
            $Tickets->agent_id = $value;
            $Tickets->saveOrFail();
            return response()->json(['message' => __('Tickets assigned to the selected agent'), 'Tickets' => new TicketsManageResource($Tickets), 'access' => $Tickets->verifyUser($user)]);
        }
        if ($action === 'Companies') {
            $Tickets->Companies_id = $value;
            $Tickets->saveOrFail();
            return response()->json(['message' => __('Tickets assigned to the selected Companies'), 'Tickets' => new TicketsManageResource($Tickets), 'access' => $Tickets->verifyUser($user)]);
        }
        if ($action === 'label') {
            $Tickets->labels()->syncWithoutDetaching($request->get('value'));
            $Tickets->updated_at = Carbon::now();
            $Tickets->save();
            return response()->json(['message' => __('Label has been assigned to Tickets'), 'Tickets' => new TicketsManageResource($Tickets), 'access' => true]);
        }
        if ($action === 'priority') {
            $Tickets->priority_id = $value;
            $Tickets->saveOrFail();
            return response()->json(['message' => __('Tickets priority has been changed'), 'Tickets' => new TicketsManageResource($Tickets), 'access' => true]);
        }
        return response()->json(['message' => __('Quick action not found')], 404);
    }

    public function removeLabel(Tickets $Tickets, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$Tickets->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this Tickets')], 403);
        }
        if ($Tickets->labels()->detach($request->get('label'))) {
            return response()->json(['message' => __('Data saved correctly'), 'Tickets' => new TicketsManageResource($Tickets)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    public function uploadAttachment(StoreFileRequest $request): JsonResponse
    {
        return (new FileController())->uploadAttachment($request);
    }
}
