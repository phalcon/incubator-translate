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

class Mongo extends AbstractAdapter implements ArrayAccess
{
    /**
     * @var TranslateCollectionInterface
     */
    protected $collection;

    /**
     * @var string
     */
    protected $language;

    /**
     * Database constructor.
     *
     * @param TranslateCollectionInterface $collection
     * @param string                       $language
     * @param InterpolatorFactory          $interpolator
     * @param array                        $options
     */
    public function __construct(
        TranslateCollectionInterface $collection,
        string $language,
        InterpolatorFactory $interpolator,
        array $options = []
    ) {
        parent::__construct($interpolator, $options);

        $this->collection = $collection;
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
        $translation = $this->collection::count([
            [
                'key' => $index,
            ],
        ]);

        return $translation > 0;
    }

    /**
     * @param string $translateKey
     * @param array  $placeholders
     *
     * @return string
     */
    public function query(string $translateKey, array $placeholders = []): string
    {
        $translations = $this->getTranslations($translateKey);

        if (!isset($translations)) {
            return $translateKey;
        }

        $value = $translations->getValue($this->language) ?: $translateKey;

        return $this->replacePlaceholders($value, $placeholders);
    }

    /**
     * Check whether a translation key exists
     *
     * @param mixed $translateKey
     * @return bool
     */
    public function offsetExists($translateKey): bool
    {
        return $this->exists($translateKey);
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
        $this->update($offset, "");
    }

    /**
     * Gets the translations set.
     *
     * @param string $translateKey
     *
     * @return TranslateCollectionInterface
     */
    protected function getTranslations(string $translateKey): ?TranslateCollectionInterface
    {
        /** @var TranslateCollectionInterface $translations */
        $translations =  $this->collection::findFirst([
            [
                'key' => $translateKey,
            ],
        ]);

        return $translations;
    }

    /**
     * Update a translation for given key
     *
     * @param string $translateKey
     * @param string $value
     *
     * @return bool
     */
    protected function update(string $translateKey, string $value): bool
    {
        $translations = $this->getTranslations($translateKey);

        if ($translations === null) {
            $className = get_class($this->collection);
            $translations = new $className();

            $translations->setKey($translateKey);
        }

        $translations->setValue($this->language, $value);

        return $translations->save();
    }
}
