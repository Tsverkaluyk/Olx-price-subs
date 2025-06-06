<?php

namespace App\Jobs;
use App\Enums\NotificationType;
use App\Mail\SubcribeNotify;
use App\Models\Subscription;
use App\Services\OlxParser;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CheckOlxPricesJob implements ShouldQueue
{
    use Dispatchable, Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(OlxParser $parser): void
    {
        Subscription::where('is_active', true)
            ->with('priceHistories')
            ->select(['id', 'url', 'email', 'current_price', 'current_currency', 'date'])
            ->chunk(200, function ($subscriptions) use ($parser) {
                /** @var Subscription $subscription */
                foreach ($subscriptions as $subscription) {
                    $currentPrice = $parser->getPrice($subscription->url);

                    if (
                        $currentPrice &&
                        (
                            $currentPrice['price'] != $subscription->current_price ||
                            $currentPrice['currency'] != $subscription->current_currency
                        )
                    ) {
                        $currentPrice['date'] = $subscription->date;
                        $subscription->priceHistories()->create($currentPrice);

                        $subscription->update([
                            'current_price' => $currentPrice['price'],
                            'current_currency' => $currentPrice['currency'],
                            'date' => Carbon::now()
                        ]);

                        Mail::to($subscription->email)->queue(
                            new SubcribeNotify($subscription, NotificationType::PRICE_CHANGE)
                        );
                    }
                }
            });
    }
}
