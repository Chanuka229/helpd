<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\StoreRequest;
use App\Http\Requests\Tickets\TicketsReplyRequest;
use App\Http\Resources\Companies\CompanieselectResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Tickets\TicketsDetailsResource;
use App\Http\Resources\Tickets\TicketsListResource;
use App\Http\Resources\Tickets\TicketsManageResource;
use App\Models\Companies;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tickets;
use App\Models\TicketsReply;
use App\Notifications\Tickets\AssignTicketsToCompanies;
use App\Notifications\Tickets\NewTicketsReplyFromUserToAgent;
use App\Notifications\Tickets\NewTicketsReplyFromUserToUser;
use App\Notifications\Tickets\NewTicketsRequest;
use Auth;
use Carbon\Carbon;
use Exception;
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
        $sort = json_decode($request->get('sort', json_encode(['order' => 'asc', 'column' => 'created_at'], JSON_THROW_ON_ERROR)), true, 512, JSON_THROW_ON_ERROR);
        $items = Tickets::filter($request->all())
            ->where('user_id', Auth::id())
            ->orderBy($sort['column'], $sort['order'])
            ->paginate((int) $request->get('perPage', 10));
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
        $Tickets->status_id = 1;
        if ($request->has('Companies_id')) {
            $Tickets->Companies_id = $request->get('Companies_id');
        }
        $Tickets->user_id = Auth::id();
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
            $Tickets->user->notify((new NewTicketsRequest($Tickets))->locale(Setting::getDecoded('app_locale')));
            if ($Tickets->Companies_id !== null) {
                foreach ($Tickets->Companies->agents() as $agent) {
                    $agent->notify(new AssignTicketsToCompanies($Tickets, $agent));
                }
            }
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
        if ($Tickets->user_id !== Auth::id()) {
            return response()->json(['message' => __('Not found')], 404);
        }
        return response()->json(new TicketsDetailsResource($Tickets));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TicketsReplyRequest  $request
     * @param  Tickets  $Tickets
     * @return JsonResponse
     */
    public function reply(TicketsReplyRequest $request, Tickets $Tickets): JsonResponse
    {
        if ($Tickets->user_id !== Auth::id()) {
            return response()->json(['message' => __('Not found')], 404);
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
            $Tickets->updated_at = Carbon::now();
            $Tickets->save();
            $Tickets->user->notify((new NewTicketsReplyFromUserToUser($Tickets))->locale(Setting::getDecoded('app_locale')));
            if ($Tickets->agent) {
                $Tickets->agent->notify((new NewTicketsReplyFromUserToAgent($Tickets, $Tickets->agent))->locale(Setting::getDecoded('app_locale')));
            }
            return response()->json(['message' => __('Data saved correctly'), 'Tickets' => new TicketsManageResource($Tickets)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    public function Companies(): JsonResponse
    {
        return response()->json(CompanieselectResource::collection(Companies::where('public', '=', true)->get()));
    }

    public function statuses(): JsonResponse
    {
        return response()->json(StatusResource::collection(Status::all()));
    }
}
