<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class LoginThrottle
{
    /**
     * Get the cache key for failed attempts (email + IP)
     */
    protected static function attemptsKey($email, $ip)
    {
        return 'login_attempts:' . sha1($email . '|' . $ip);
    }

    /**
     * Get the cache key for lockout expiry (email + IP)
     */
    protected static function lockoutKey($email, $ip)
    {
        return 'login_lockout:' . sha1($email . '|' . $ip);
    }

    /**
     * Increment failed attempts and return current count.
     * If threshold reached, activate lockout with progressive delay.
     */
    public static function incrementAttempts($email, $ip, $maxAttempts = 5)
    {
        $key = self::attemptsKey($email, $ip);
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(15)); // keep attempts for 15 min

        if ($attempts >= $maxAttempts) {
            // Get previous lockout duration (if any) – stored in separate cache key
            $lockoutKey = self::lockoutKey($email, $ip);
            $previousDuration = Cache::get($lockoutKey . '_duration', 0);
            // Increment by 30 seconds each time, starting at 30
            $newDuration = $previousDuration + 30;
            Cache::put($lockoutKey, now()->addSeconds($newDuration), $newDuration);
            Cache::put($lockoutKey . '_duration', $newDuration, $newDuration);
            // Reset attempts after lockout is triggered
            Cache::forget($key);
        }

        return $attempts;
    }

    /**
     * Check if the email+IP is currently locked out.
     * Returns false if not locked, or the remaining seconds if locked.
     */
    public static function isLocked($email, $ip)
    {
        $lockoutKey = self::lockoutKey($email, $ip);
        $expiresAt = Cache::get($lockoutKey);
        if (!$expiresAt) {
            return false;
        }
        $remaining = now()->diffInSeconds($expiresAt, false);
        return $remaining > 0 ? (int) $remaining : false;
    }

    /**
     * Clear all throttle data for this email+IP (on successful login).
     */
    public static function clear($email, $ip)
    {
        Cache::forget(self::attemptsKey($email, $ip));
        Cache::forget(self::lockoutKey($email, $ip));
        Cache::forget(self::lockoutKey($email, $ip) . '_duration');
    }
}