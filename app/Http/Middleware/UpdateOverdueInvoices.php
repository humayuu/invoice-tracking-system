<?php

namespace App\Http\Middleware;

use App\Models\Purchase;
use App\Models\Sale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateOverdueInvoices
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Cache::remember('overdue_checked', now()->endOfDay(), function () {
            $today = now()->toDateString();

            Sale::where('status', 'pending')
                ->whereDate('due_date', '<', $today)
                ->update(['status' => 'overdue']);

            Purchase::where('status', 'pending')
                ->whereDate('due_date', '<', $today)
                ->update(['status' => 'overdue']);

            return true;
        });

        return $next($request);
    }
}
