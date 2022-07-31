<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IosController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $receipt
     *
     * @return JsonResponse
     */
    public function check(string $receipt)
    {
        // $receipt son karakteri tek ve küçük harf ise true
        $endCharacter = substr($receipt, -1);
        $status = false;

        if (is_numeric($endCharacter) && $endCharacter % 2 == 1) {
            $status = true;
        }

        if (ctype_lower($endCharacter)) {
            $status = true;
        }

        if ($status) {
            return response()->json([
                'status' => true,
                'expire-date' => (new \DateTime("+1 hour"))->setTimezone(new \DateTimeZone("-6"))->format('Y-m-d H:i:s'),
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
        ], Response::HTTP_OK);
    }
}
