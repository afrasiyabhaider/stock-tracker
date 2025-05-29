<?php

namespace App\Contract;

interface StockInterface
{
    /**
     * Get the stock quote for a given symbol.
     *
     * @param string $symbol
     * @return array
     */
    public function getStockQuote(string $symbol): array;

    /**
     * Get the historical stock data for a given symbol.
     *
     * @param string $symbol
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getHistory(string $symbol, string $startDate, string $endDate): array;

    /**
     * Save the stock history data.
     *
     * Persists the provided stock history information.
     *
     * @param array $data The stock history data to be saved.
     * @return mixed
     */
    public function saveStockHistory(array $data): mixed;

    /**
     * Send an email notification with the provided data.
     *
     * @param array $data
     * @return void
     */
    public function sendEmailNotification(array $data): void;
}
