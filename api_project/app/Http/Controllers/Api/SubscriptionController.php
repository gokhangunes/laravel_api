<?php

namespace App\Http\Controllers\Api;

use App\Events\Canceled;
use App\Events\Renewed;
use App\Events\Started;
use App\Http\Controllers\Controller;
use App\Model\Device;
use App\Model\Subscription;
use App\Service\GoogleService\GoogleServie;
use App\Service\IosService\IosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request, GoogleServie $googleServie, IosService $iosService): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'receipt' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'response' => $validator->messages(),
            ], Response::HTTP_FORBIDDEN);
        }

        $device = Device::where([
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'response' => 'Device Not Found',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($device->os === 'android') {
            $status = (bool) $googleServie->check($input['receipt'])['status'];
            $expireData = $googleServie->check($input['receipt'])['expire-date'] ?? null;

        } elseif ($device->os === 'ios') {
            $status = (bool) $iosService->check($input['receipt'])['status'];
            $expireData = $iosService->check($input['receipt'])['expire-date'] ?? null;
        } else {
            return response()->json([
                'success' => false,
                'response' => 'Os Not Fount',
            ], Response::HTTP_FORBIDDEN);
        }

        $subscription = Subscription::where([
            'uid' => $device->uid,
            'app_id' => $device->application_id,
            'user_id' => $device->user_id
        ])->first();

        if (!$subscription) {
            if (!$status || is_null($expireData)) {
                return response()->json([
                    'success' => false,
                ], Response::HTTP_OK);

            }

            Subscription::create([
                'status' => $status,
                'uid' => $device->uid,
                'app_id' => $device->uid,
                'user_id' => $device->user_id,
                'expire_date' => $expireData,
            ]);

            event(new Started($device));
        }

        if ($status == $subscription->status) {
            return response()->json([
                'success' => $status,
            ], Response::HTTP_OK);
        }

        $subscription->status = $status;
        $subscription->uid = $device->uid;
        $subscription->app_id = $device->application_id;
        $subscription->expire_date = $expireData;

        $subscription->save();

        if ($expireData === null) {
            event(new Canceled($device));
        }

        event(new Renewed($device));

        return response()->json([
            'success' => $status,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request                  $request
     * @param  \App\Model\Subscription $subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $subscription = auth()->user()->subscription;

        $subscriptionStatus = false;
        if ($subscription) {
            $subscriptionStatus = $subscription->status;
        }

        return response()->json([
            'success' => true,
            'response' => [
                'subscription-status' =>(bool) $subscriptionStatus ?? false
            ],
        ], 200);
    }
}
