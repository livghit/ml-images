<?php

namespace App\Http\Middleware;

use App\Models\DownloadCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyDownloadCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if code is in session (already verified)
        if ($request->session()->has('download_code_verified')) {
            return $next($request);
        }

        // Check if code is provided in request
        $code = $request->input('code') ?? $request->session()->get('attempted_code');
        
        if (!$code) {
            return redirect()->route('gallery.enter-code')
                ->with('error', 'Please enter a valid download code');
        }

        $downloadCode = DownloadCode::findValidCode($code);
        
        if (!$downloadCode) {
            return redirect()->route('gallery.enter-code')
                ->with('error', 'Invalid or expired download code');
        }

        // Store verification in session
        $request->session()->put('download_code_verified', true);
        $request->session()->put('download_code', $downloadCode->code);
        
        return $next($request);
    }
}
