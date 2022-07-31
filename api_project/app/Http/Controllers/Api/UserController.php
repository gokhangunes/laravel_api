<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Device;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->middleware('ClientToken');

        return response()->json([
            'success' => true,
            'response' => auth()->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'uid' => 'required|min:5',
            'app_id' => 'required',
            'language' => 'required|min:1',
            'os' => 'required|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'response' => $validator->messages(),
            ], Response::HTTP_FORBIDDEN);
        }

        $input['application_id'] = $input['app_id'];

        $device = Device::where([
            'uid' => $input['uid'],
            'application_id' => $input['application_id'],
        ])->first();

        if (!$device) {

            $user = User::create([
                'client-token' => Str::random(60),
            ]);

            $input = array_merge($input, [
                'user_id' => $user->id
            ]);

            $device = Device::create($input);
        }

        $user = $device->user()->first();

        return response()->json([
            'success' => true,
            'response' => $user,
        ]);
    }
}
