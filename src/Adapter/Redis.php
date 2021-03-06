<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Adapter;

use ArrayAccess;
use Phalcon\Translate\Adapter\AbstractAdapter;
use Phalcon\Translate\InterpolatorFactory;

class Redis extends AbstractAdapter implements ArrayAccess
{
    /**
     * @var string
     */
    protected $language;

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * Local cache.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Database constructor.
     *
     * @param \Redis              $redis
     * @param string              $language
     * @param InterpolatorFactory $interpolator
     * @param array               $options
     */
    public function __construct(
        \Redis $redis,
        string $language,
        InterpolatorFactory $interpolator,
        array $options = []
    ) {
        parent::__construct($interpolator, $options);

        $this->redis = $redis;
        $this->language = $language;
    }

    /**
     * Returns the translation string of the given key
     *
     * @param string $translateKey
     * @param array $placeholders
     * @return string
     */
    public function t(string $translateKey, array $placeholders = []): string
    {
        return $this->query($translateKey, $placeholders);
    }

    /**
     * Returns the translation string of the given key (alias of method 't')
     *
     * @param array $placeholders
     * @param string $translateKey
     * @return string
     */
    public function _(string $translateKey, array $placeholders = []): string
    {
        return $this->t($translateKey, $placeholders);
    }

    /**
     * @param string $index
     *
     * @return bool
     */
    public function exists(string $index): bool
    {
        return $this->getTranslation($index) !== "";
    }

    /**
     * @param string $translateKey
     * @param array  $placeholders
     *
     * @return string
     */
    public function query(string $translateKey, array $placeholders = []): string
    {
        $value = $this->getTranslation($translateKey) ?: $translateKey;

        return $this->replacePlaceholders($value, $placeholders);
    }

    /**
     * Returns the translation related to the given key
     *
     * @param mixed $translateKey
     * @return mixed
     */
    public function offsetGet($translateKey)
    {
        return $this->query($translateKey);
    }

    /**
     * Sets a translation value
     *
     * @param mixed $offset
     * @param string $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->update($offset, $value);
    }

    /**
     * Unsets a translation from the dictionary
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * Loads key from Redis to local cache.
     *
     * @param string $translateKey
     *
     * @return string
     */
    protected function getTranslation(string $translateKey): string
    {
        $key = $this->getKey($translateKey);

        if (!isset($this->cache[$key])) {
            $result = $this->redis->get($key);

            if (!is_string($result)) {
                return "";
            }

            $this->cache[$key] = $result;
        }

        return $this->cache[$key];
    }

    /**
     * Update a translation for given key
     *
     * @param  string  $translateKey
     * @param  string  $value
     * @return bool
     */
    protected function update(string $translateKey, string $value): bool
    {
        $key = $this->getKey($translateKey);

        return $this->redis->set($key, $value);
    }

    /**
     * Delete a translation for given key
     *
     * @param string $translateKey
     *
     * @return bool
     */
    protected function delete(string $translateKey): bool
    {
        $key = $this->getKey($translateKey);

        return $this->redis->del($key) > 0;
    }

    /**
     * Returns key for translate.
     *
     * @param string $translateKey
     *
     * @return string
     */
    protected function getKey(string $translateKey): string
    {
        return md5($this->language . ':' . $translateKey);
    }
}
