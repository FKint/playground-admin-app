<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ShareAppVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $appVersion = Cache::rememberForever('app_version', function () {
            $sha1 = 'DEV';
            $timestamp = 0;
            $githubLink = 'https://github.com/FKint/playground-admin-app';
            if (App::environment('production')) {
                try {
                    $timestamp = Storage::get('version_timestamp');
                    $sha1 = Storage::get('version_sha1');
                    $githubLink = Storage::get('version_github_link');
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    Log::error('Could not read version information: '.$e->getMessage());
                }
            }

            return [
                'sha1' => $sha1,
                'timestamp' =>  (new \DateTimeImmutable())->setTimestamp($timestamp)->format('Y-m-d H:i:s'),
                'github_link' => $githubLink,
            ];
        });
        View::share('app_version', $appVersion);
        return $next($request);
    }
}
