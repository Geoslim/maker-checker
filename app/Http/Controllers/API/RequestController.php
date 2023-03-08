<?php

namespace App\Http\Controllers\API;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Request as RequestModel;
use App\Services\RequestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class RequestController extends Controller
{
    public function __construct(protected RequestService $requestService)
    {
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            RequestResource::collection(
                RequestModel::whereStatus(Status::PENDING)
                    ->orderBy('id', 'desc')
                    ->get()
            )
        );
    }

    /**
     * @param RequestModel $request
     * @return JsonResponse
     */
    public function approve(RequestModel $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->requestService->approve($request, auth()->user()->id);
            DB::commit();
            return $this->success('Request successfully approved.');
        } catch (InvalidArgumentException $e) {
            DB::rollback();
            Log::error($e);
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return $this->error(__('exception.request.processing'));
        }
    }

    /**
     * @param RequestModel $request
     * @return JsonResponse
     */
    public function decline(RequestModel $request): JsonResponse
    {
        try {
            $this->requestService->decline($request);
            return $this->success('Request successfully declined.');
        } catch (Exception $e) {
            Log::error($e);
            return $this->error(__('exception.request.processing'));
        }
    }
}
