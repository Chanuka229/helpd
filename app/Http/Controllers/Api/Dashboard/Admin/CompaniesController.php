<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admin\Companies\StoreRequest;
use App\Http\Requests\Dashboard\Admin\Companies\UpdateRequest;
use App\Http\Resources\Companies\CompaniesDetailsResource;
use App\Http\Resources\Companies\CompaniesResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\Companies;
use App\Models\Tickets;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\JsonResponse;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(CompaniesDetailsResource::collection(Companies::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $request->validated();
        $Companies = new Companies();
        $Companies->name = $request->get('name');
        $Companies->all_agents = $request->get('all_agents');
        $Companies->public = $request->get('public');
        $agents = [];
        if (!$Companies->all_agents) {
            $agents = $request->get('agents');
        }
        if ($Companies->save()) {
            $Companies->agent()->sync($agents);
            return response()->json(['message' => 'Data saved correctly', 'Companies' => new CompaniesResource($Companies)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  Companies  $Companies
     * @return JsonResponse
     */
    public function show(Companies $Companies): JsonResponse
    {
        return response()->json(new CompaniesResource($Companies));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Companies  $Companies
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Companies $Companies): JsonResponse
    {
        $request->validated();
        $Companies->name = $request->get('name');
        $Companies->all_agents = $request->get('all_agents');
        $Companies->public = $request->get('public');
        $agents = [];
        if (!$Companies->all_agents) {
            $agents = $request->get('agents');
        }
        if ($Companies->save()) {
            $Companies->agent()->sync($agents);
            return response()->json(['message' => 'Data updated correctly', 'Companies' => new CompaniesResource($Companies)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Companies  $Companies
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Companies $Companies): JsonResponse
    {
        Tickets::where('Companies_id', $Companies->id)->update(['Companies_id' => null]);
        $Companies->agent()->sync([]);
        if ($Companies->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }

    public function users()
    {
        $roles = UserRole::where('dashboard_access', true)->pluck('id');
        $agents = User::whereIn('role_id', $roles)->where('status', true)->get();
        return response()->json(UserDetailsResource::collection($agents));
    }
}
