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

use Phalcon\Incubator\MongoDB\Mvc\CollectionInterface;
use Phalcon\Translate\Adapter\AbstractAdapter;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\InterpolatorFactory;

class Mongo extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var string
     */
    protected $language;

    /**
     * Database constructor.
     *
     * @param CollectionInterface $collection
     * @param string $language
     * @param InterpolatorFactory $interpolator
     * @param array $options
     */
    public function __construct(
        CollectionInterface $collection,
        string $language,
        InterpolatorFactory $interpolator,
        array $options = []
    ) {
        parent::__construct($interpolator, $options);

        $this->collection = $collection;
        $this->language = $language;
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
     * Gets the translations set.
     *
     * @param string $translateKey
     *
     * @return CollectionInterface
     */
    protected function getTranslations(string $translateKey): CollectionInterface
    {
        return $this->collection::findFirst([
            [
                'key' => $translateKey,
            ],
        ]);
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

        $value = isset($translations->{$this->language}) ? $translations->{$this->language} : $translateKey;

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
    public function offsetGet($translateKey): string
    {
        return $this->query($translateKey);
    }
}
