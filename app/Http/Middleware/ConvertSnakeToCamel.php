<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ConvertSnakeToCamel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);

            $camelData = $this->convertKeysToCamelCase($data);

            $response->setData($camelData);
        }

        return $response;
    }

    /**
     * Recursively convert all array keys to camelCase
     *
     * @param array $data
     * @return array
     */
    protected function convertKeysToCamelCase(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $newKey = Str::camel($key);

            // Recursively process nested arrays
            if (is_array($value)) {
                $result[$newKey] = $this->convertKeysToCamelCase($value);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
