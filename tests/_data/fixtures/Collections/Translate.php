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

namespace Phalcon\Incubator\Translate\Test\Fixtures\Collections;

use Phalcon\Incubator\MongoDB\Mvc\Collection;
use Phalcon\Incubator\Translate\Adapter\TranslateCollectionInterface;

class Translate extends Collection implements TranslateCollectionInterface
{
    /** @var string */
    public $key;

    /** @var string */
    public $en;

    /** @var string */
    public $ru;

    /** @var string */
    public $pt;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $lang
     * @param string $value
     *
     * @return mixed
     */
    public function setValue(string $lang, string $value)
    {
        $this->{$lang} = $value;
    }

    /**
     * @param string $lang
     *
     * @return string
     */
    public function getValue(string $lang): string
    {
        return $this->{$lang};
    }
}
