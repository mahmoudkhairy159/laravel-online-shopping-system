<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Exception;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Order\App\Http\Requests\Admin\Discount\ApplyDiscountRequest;
use Modules\Order\App\Http\Requests\Admin\Order\ChangeOrderStatusRequest;
use Modules\Order\App\Http\Requests\Admin\Order\StoreOrderRequest;
use Modules\Order\App\Http\Requests\Admin\Order\UpdateOrderRequest;
use Modules\Order\App\Repositories\OrderRepository;
use Modules\Order\App\resources\Order\OrderCollection;
use Modules\Order\App\resources\Order\OrderResource;
use Modules\Order\App\Services\PurchaseOrderService;
use Modules\Order\App\Services\UpdateOrderService;

class OrderController extends Controller
{
    use ApiResponseTrait;


    protected $orderRepository;

    protected $_config;
    protected $guard;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->guard = 'admin-api';
        request()->merge(['token' => 'true']);
        Auth::setDefaultDriver($this->guard);
        $this->_config = request('_config');
        $this->orderRepository = $orderRepository;
        // permissions
        $this->middleware('auth:' . $this->guard);
        $this->middleware(['permission:orders.show'])->only(['index', 'getByUserId', 'show']);
        $this->middleware(['permission:orders.create'])->only(['store']);
        $this->middleware(['permission:orders.update'])->only(['update']);
        $this->middleware(['permission:orders.destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->orderRepository->getAll()->paginate();
            return $this->successResponse(new OrderCollection($data));
        } catch (Exception $e) {
            return $this->errorResponse(
                [$e->getMessage(), $e->getCode()],
                __('app.something-went-wrong'),
                500
            );
        }
    }

    public function getByUserId($user_id)
    {
        try {
            $data = $this->orderRepository->getByUserId($user_id)->paginate();
            return $this->successResponse(new OrderCollection($data));
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
    public function store(StoreOrderRequest $request,PurchaseOrderService $purchaseOrderService)
    {
        try {
            $data =  $request->validated();
            $created = $purchaseOrderService->purchaseOrder($data);
            if ($created) {
                return $this->successResponse(
                    new OrderResource($created),
                    __('order::app.orders.created-successfully'),
                    201
                );
            } {
                return $this->messageResponse(
                    __('order::app.orders.created-failed'),
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
            $data = $this->orderRepository->findOrFail($id);
            return $this->successResponse(new OrderResource($data));
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
    public function update(UpdateOrderRequest $request, $id,UpdateOrderService $updateOrderService)
    {
        try {
            $data =  $request->validated();
            $updated = $updateOrderService->updateOrder($data, $id);

            if ($updated) {
                return $this->successResponse(
                    new OrderResource($updated),
                    __('order::app.orders.updated-successfully'),
                    200
                );
            } {
                return $this->messageResponse(
                    __('order::app.orders.updated-failed'),
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

            $deleted = $this->orderRepository->deleteOne($id);

            if ($deleted) {
                return $this->messageResponse(
                    __('order::app.orders.deleted-successfully'),
                    true,
                    200
                );
            } {
                return $this->messageResponse(
                    __('order::app.orders.deleted-failed'),
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
    // Change the status of an order
    public function changeStatus(ChangeOrderStatusRequest $request, $id)
    {
        try {
            $data =  $request->validated();
            $updated = $this->orderRepository->chnageStatus($data, $id);

            if ($updated) {
                return $this->successResponse(
                    new OrderResource($updated),
                    __('order::app.orders.updated-successfully'),
                    200
                );
            } {
                return $this->messageResponse(
                    __('order::app.orders.updated-failed'),
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

}
