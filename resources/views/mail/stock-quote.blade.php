<x-mail::message>
# Your Stock Quotes
Here are the latest stock quotes you requested.

Below is a summary of your selected stocks:

<x-mail::table>
| Name              | Symbol           | High               | Low                | Open               | Close                  |
|-------------------|-----------------|--------------------|--------------------|--------------------|------------------------|
@foreach ($quotes as $quote)
| {{ $quote['name'] }} | {{ $quote['symbol'] }} | {{ $quote['high'] }} | {{ $quote['low'] }} | {{ $quote['open'] }} | {{ $quote['close'] }} |
@endforeach
</x-mail::table>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
