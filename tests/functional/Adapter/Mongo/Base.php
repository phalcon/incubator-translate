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

namespace  Phalcon\Incubator\Translate\Tests\Functional\Adapter\Mongo;

use MongoDB\Database;
use Phalcon\Incubator\Translate\Adapter\Mongo;
use Phalcon\Incubator\Translate\Test\Fixtures\Collections\Translate;
use Phalcon\Incubator\Translate\Test\Fixtures\Traits\DiTrait;
use Phalcon\Translate\InterpolatorFactory;

class Base
{
    use DiTrait;

    protected const TRANSLATE_KEY      = "replace_me";
    protected const TRANSLATE_VALUE_EN = "Replace Me!";
    protected const TRANSLATE_VALUE_RU = "Поменяй меня!";
    protected const TRANSLATE_VALUE_PT = "Muda-me!";

    /** @var string */
    private $source;

    /** @var Database */
    private $mongo;

    public function _before()
    {
        $this->setNewFactoryDefault();
        $this->setDiCollectionManager();
        $this->setDiMongo();

        $this->source = (new Translate())->getSource();
        $this->mongo  = $this->getDi()->get('mongo');

        $translate = new Translate();
        $translate->key = self::TRANSLATE_KEY;
        $translate->en = self::TRANSLATE_VALUE_EN;
        $translate->ru = self::TRANSLATE_VALUE_RU;
        $translate->pt = self::TRANSLATE_VALUE_PT;
        $translate->save();
    }

    public function _after()
    {
        $this->mongo->dropCollection($this->source);
    }

    protected function initTranslate(string $lang = "en"): Mongo
    {
        return new Mongo(
            new Translate(),
            $lang,
            new InterpolatorFactory()
        );
    }
}
