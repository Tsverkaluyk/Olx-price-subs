<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="OLX API",
 *         version="1.0.0",
 *         description="API для підписки на зміни ціни в оголошенях"
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_HOST,
 *         description="Основний сервер"
 *     )
 * )
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
