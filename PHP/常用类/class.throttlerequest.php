<?php

class ThrottleRequest {
    protected $cache;

    /**
     * Create a new request throttler.
     *
     * @param  Cache  $cache_handler
     * @return void
     */
    public function __construct($cache_handler) {
        $this->cache = $cache_handler;
    }

    /**
     * Determine if request has been "accessed" too many times.
     *
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return bool|mixed
     */
    public function handle($maxAttempts = 180, $decayMinutes = 1) {
        $key = $this->resolveRequestSignature();
        if ($this->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            return $this->buildResponse($key, $maxAttempts);
        }
        $this->hit($key, $decayMinutes);

        return true;
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature()
    {
        return 'throttle_'.sha1(
                      $_SERVER['SERVER_NAME'].
                '|' . $_SERVER['SCRIPT_NAME'].
                '|' . $_SERVER['REMOTE_ADDR'].
                '|' . $_SERVER['HTTP_USER_AGENT']
            );
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return bool
     */
    public function tooManyAttempts($key, $maxAttempts, $decayMinutes = 1)
    {
        if ($this->cache->exists($key.':lockout')) {
            return true;
        }

        if ($this->attempts($key) > $maxAttempts) {
            $log = date('Y-m-d H:i:s').'; SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME']. '; REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR']. ' HTTP_USER_AGENT:'.$_SERVER['HTTP_USER_AGENT']."\r\n";
            @error_log($log, 3, '/var/tmp/throttler.log');
            if ($this->cache->get($key.':lockout') === false) {
                $this->cache->setEx($key.':lockout', ($decayMinutes * 60), $decayMinutes);
            }

            $this->resetAttempts($key);

            return true;
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string  $key
     * @param  int  $decayMinutes
     * @return int
     */
    public function hit($key, $decayMinutes = 1)
    {
        if ($this->cache->get($key) === false) {
            $this->cache->setEx($key, ($decayMinutes * 60), 1);

            return 1;
        }

        return (int) $this->cache->incr($key);
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function attempts($key)
    {
        $value = $this->cache->get($key);
        return ($value === false) ? 0 : $value;
    }

    /**
     * Reset the number of attempts for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function resetAttempts($key)
    {
        return $this->cache->delete($key);
    }

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     */
    protected function buildResponse($key, $maxAttempts)
    {
        header('Content-Type:text/html; charset=utf-8');
        header('Status: ' . 429);
        exit('Too Many Attempts');
    }
}
?>