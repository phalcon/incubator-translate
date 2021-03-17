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

/**
 * Interface TranslateCollectionInterface
 *
 * @package Phalcon\Incubator\Translate\Adapter
 */
interface TranslateCollectionInterface extends CollectionInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function setKey(string $key);

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $lang
     * @param string $value
     *
     * @return mixed
     */
    public function setValue(string $lang, string $value);

    /**
     * @param string $lang
     *
     * @return string
     */
    public function getValue(string $lang): string;
}
