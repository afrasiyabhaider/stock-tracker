<?php
// app/Services/StockService.php
namespace App\Services;

use App\Contract\StockInterface;
use App\Mail\StockQuoteMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StockService extends CommonService implements StockInterface
{
    /**
     * Retrieves the stock quote for a given symbol using an external API.
     *
     * This method attempts to fetch the stock quote for the provided symbol. It first checks if the API key is configured.
     * The result is cached for 1 hour to reduce API calls. If the data is not found or the API call fails, an appropriate
     * response is returned. All exceptions are logged.
     *
     * @param string $symbol The stock symbol to retrieve the quote for.
     * @return array An associative array containing the response code, message, and data.
     */
    public function getStockQuote($symbol): array
    {
        try {
            // api key is required to make the API call
            $apiKey = config('services.stock_data_api_key');
            if (empty($apiKey)) {
                return $this->returnResponse('API key not configured', [], 500);
            }

            $cacheKey = "stock_quote_{$symbol}_" . Auth::id();
            // Check if the quote is already cached before making an API call if it exists then return it
            $quote = Cache::remember($cacheKey, 3600, function () use ($symbol, $apiKey) {

                $url = "https://api.stockdata.org/v1/data/quote?symbols={$symbol}&api_token={$apiKey}";
                $response = Http::get($url);

                if ($response->failed()) {
                    return $this->returnResponse('Failed to fetch stock data', [], 500);
                }
                $data = $response->json('data', []);

                if (!$data || empty($data)) {
                    return $this->returnResponse('Data not found, please use different symbol', [], 404);
                }

                // Save stock history for the authenticated user
                if ($data) {
                    $data = $this->saveStockHistory($data);
                }

                // Send stock quote via email
                $this->sendEmailNotification($data);

                return $this->returnResponse('Success', $data);
            });

            // return the cached quote
            return $quote;
        } catch (\Exception $ex) {
            Log::error('StockService: Failed to get stock quote', [
                'symbol' => $symbol,
                'error' => $ex->getMessage(),
            ]);

            // Return a standardized error response
            return $this->returnResponse('Failed to fetch stock quote', [], 500);
        }
    }

    /**
     * Retrieves the stock history for the authenticated user filtered by symbol and date range.
     *
     * @param string $symbol The stock symbol to filter the history by.
     * @param string $startDate The start date (inclusive) for filtering the history (format: Y-m-d or Y-m-d H:i:s).
     * @param string $endDate The end date (inclusive) for filtering the history (format: Y-m-d or Y-m-d H:i:s).
     * @return array The filtered stock history records as an array.
     */
    public function getHistory(string $symbol = null, string $startDate = null, string $endDate = null): array
    {
        return Auth::user()
            ->stockHistory()
            ->when($symbol, function ($query) use ($symbol) {
                return $query->where('symbol', $symbol);
            })
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Saves the stock history data for the authenticated user.
     *
     * @param array $data The stock history data to be saved.
     * @return mixed
     */
    public function saveStockHistory(array $data): mixed
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('StockService: User not authenticated');
            }
            $savedHistory = [];
            foreach ($data as $key => $value) {
                $history = $user
                    ->stockHistory()
                    ->create([
                        'name' => $value['name'],
                        'symbol' => $value['ticker'],
                        'open' => $value['day_open'],
                        'high' => $value['day_high'],
                        'low' => $value['day_low'],
                        'close' => $value['previous_close_price'],
                    ]);
                $savedHistory[] = $history->except(['user_id', 'created_at', 'updated_at', 'id']);
            }
            return $savedHistory;
        } catch (\Exception $ex) {
            Log::error('StockService: Failed to save stock history', [
                'error' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Sends an email notification with the provided data.
     *
     * @param array $data The data to include in the email notification.
     * @return void
     */
    public function sendEmailNotification(array $data): void
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('StockService: User not authenticated');
                return;
            }
            Mail::to($user->email)->send(new StockQuoteMail($data));
        } catch (\Exception $ex) {
            Log::error('StockService: Failed to send email notification', [
                'error' => $ex->getMessage(),
            ]);
        }
    }
}
