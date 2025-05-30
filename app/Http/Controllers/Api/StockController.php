<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Stock Tracker API",
 *     description="API documentation for the Stock Tracker application",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use the token returned from /api/login",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 */
class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }


    /**
     * @OA\Get(
     *     path="/api/stock/quote",
     *     summary="Get stock quote",
     *     description="Returns the latest stock quote for a given symbol and emails the result to the user. Requires authentication using a Bearer token generated by the login endpoint.",
     *     operationId="getStockQuote",
     *     tags={"Stock Tracking"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Stock symbol",
     *         required=true,
     *         example="IBM",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock quote",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="International Business Machines"),
     *                     @OA\Property(property="symbol", type="string", example="IBM"),
     *                     @OA\Property(property="open", type="string", example="$263.11"),
     *                     @OA\Property(property="high", type="string", example="$265.00"),
     *                     @OA\Property(property="low", type="string", example="$259.96"),
     *                     @OA\Property(property="close", type="string", example="$263.23")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to fetch stock quote"
     *     )
     * )
     *
     * This endpoint requires the user to be authenticated using a Bearer token generated by the login endpoint.
     */
    public function quote(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|max:15|regex:/^[A-Za-z,]+$/',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()->first()], 422);
        }
        $symbol = $request->query('q');
        $user = Auth::user() ?? Auth::guard('web')->user();
        if (!$user) {
            return $this->sendError(
                'Unauthenticated. Please log in to access this resource.',
                401
            );
        }

        try {
            $quote = $this
                ->stockService
                ->getStockQuote($symbol);

            // Check if the quote was successfully fetched
            if ($quote['code'] !== 200) {
                return $this->sendError(
                    $quote['message'],
                    $quote['code']
                );
            }
            return $quote;
        } catch (\Exception $e) {
            Log::error('Failed to fetch stock quote', [
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ]);
            return $this->sendError(
                "Failed to fetch stock quote for {$symbol}. Please try again later.",
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/stock/history",
     *     summary="Get stock query history",
     *     description="Returns the authenticated user's stock query history. Optional filters: symbol, start_date, end_date.",
     *     operationId="getStockHistory",
     *     tags={"Stock Tracking"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="symbol",
     *         in="query",
     *         description="Stock symbol to filter history",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date for filtering history (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date for filtering history (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock query history",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="date", type="string", format="date-time"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="symbol", type="string"),
     *                     @OA\Property(property="open", type="number"),
     *                     @OA\Property(property="high", type="number"),
     *                     @OA\Property(property="low", type="number"),
     *                     @OA\Property(property="close", type="number")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No stock history found for the given criteria",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function history(Request $request)
    {
        try {
            $request->validate([
                'symbol' => 'nullable|string|max:15|regex:/^[A-Za-z,]+$/',
                'start' => 'nullable|date_format:Y-m-d',
                'end' => 'nullable|date_format:Y-m-d',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()->first()], 422);
        }

        $queries = $this->stockService->getHistory(
            request('symbol'),
            request('start'),
            request('end')
        );

        if (empty($queries)) {
            return $this->sendError(
                'No stock history found for the given criteria.',
                404
            );
        }

        return $this->sendSuccess(
            $queries,
            'Stock query history retrieved successfully'
        );
    }
}
