<?php

namespace Modules\Area\App\Http\Controllers\Admin;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Area\App\Repositories\CityRepository;
use Modules\Area\App\resources\City\CityCollection;
use Modules\Area\App\resources\City\CityResource;
use Modules\Area\App\Http\Requests\Admin\City\StoreCityRequest;
use Modules\Area\App\Http\Requests\Admin\City\UpdateCityRequest;
use Modules\Area\App\Models\City;

class CityController extends Controller
{
    use ApiResponseTrait;


    protected $cityRepository;

    protected $_config;
    protected $guard;

    public function __construct(CityRepository $cityRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->cityRepository = $cityRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:cities.show'])->only(['index', 'show', 'getByCountryId']);
        $this->middleware(['permission:cities.create'])->only(['store']);
        $this->middleware(['permission:cities.update'])->only(['update']);
        $this->middleware(['permission:cities.destroy'])->only(['destroy', 'forceDelete', 'restore', 'getOnlyTrashed']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->cityRepository->getAll()->paginate();
            return $this->successResponse(new CityCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage(), $e->getCode()],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    public function getByCountryId($country_id)
    {
        try {
            $data = $this->cityRepository->getCitiesByCountryId($country_id)->get();
            return $this->successResponse(CityResource::collection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        try {
            $data =  $request->validated();
            $data['created_by'] = auth()->guard($this->guard)->id();
            $created = $this->cityRepository->create($data);

            if ($created) {
                return $this->messageResponse(
                    __("area::app.cities.created-successfully"),
                    true,
                    201
                );
            } {
                return $this->messageResponse(
                    __("area::app.cities.created-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->cityRepository->findOrFail($id);
            return $this->successResponse(new CityResource($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, $id)
    {
        try {
            $city = $this->cityRepository->findOrFail($id);
            $data =  $request->validated();
            $data['updated_by'] = auth()->guard($this->guard)->id();
            $updated = $this->cityRepository->update($data, $id);

            if ($updated) {
                return $this->messageResponse(
                    __("area::app.cities.updated-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.cities.updated-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->cityRepository->delete($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("area::app.cities.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.cities.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    /***********Trashed model SoftDeletes**************/
    public function getOnlyTrashed()
    {
        try {
            $data = $this->cityRepository->getOnlyTrashed()->paginate();
            return $this->successResponse(new CityCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function forceDelete($id)
    {
        try {
            $deleted = $this->cityRepository->forceDelete($id);
            if ($deleted) {
                return $this->messageResponse(
                    __("area::app.cities.deleted-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.cities.deleted-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function restore($id)
    {
        try {
            $restored = $this->cityRepository->restore($id);
            if ($restored) {
                return $this->messageResponse(
                    __("area::app.cities.restored-successfully"),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __("area::app.cities.restored-failed"),
                    false,
                    400
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse(
                [],
                __('app.something-went-wrong'),
                500
            );
        }
    }
    /***********Trashed model SoftDeletes**************/
}
