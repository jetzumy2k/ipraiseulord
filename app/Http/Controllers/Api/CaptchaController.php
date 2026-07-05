<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function __construct(
        protected CaptchaService $captcha,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'context' => ['required', 'string', 'in:contact,login'],
        ]);

        return response()->json($this->captcha->generate($data['context']));
    }
}
