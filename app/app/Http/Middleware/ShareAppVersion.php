<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ShareAppVersion
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        View::share('app_version', self::appVersion());

        return $next($request);
    }

    /**
     * The version files are baked into the image at build time, so they are read
     * directly instead of being stored in a (persistent) cache: a cached value
     * would outlive a deployment and keep showing the previous version.
     * The result is memoized per process only.
     */
    private static function appVersion(): array
    {
        static $appVersion = null;
        if (null !== $appVersion) {
            return $appVersion;
        }
        $sha1 = 'DEV';
        $timestamp = 0;
        $githubLink = 'https://github.com/FKint/playground-admin-app';
        if (App::environment('production')) {
            try {
                $timestamp = trim(File::get(base_path('version_timestamp')));
                $sha1 = trim(File::get(base_path('version_sha1')));
                $githubLink = trim(File::get(base_path('version_github_link')));
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                Log::error('Could not read version information: '.$e->getMessage());
            }
        }

        return $appVersion = [
            'sha1' => $sha1,
            'timestamp' => (new \DateTimeImmutable())->setTimestamp(intval($timestamp))->format('Y-m-d H:i:s'),
            'github_link' => $githubLink,
        ];
    }
}
