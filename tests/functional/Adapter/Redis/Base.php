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

namespace  Phalcon\Incubator\Translate\Tests\Functional\Adapter\Redis;

use \Phalcon\Incubator\Translate\Adapter\Redis as RedisAdapter;
use Phalcon\Incubator\Translate\Test\Fixtures\Traits\DiTrait;
use Phalcon\Translate\InterpolatorFactory;
use Redis;

class Base
{
    use DiTrait;

    protected const TRANSLATE_KEY      = "replace_me";
    protected const TRANSLATE_VALUE_EN = "Replace Me!";
    protected const TRANSLATE_VALUE_RU = "Поменяй меня!";
    protected const TRANSLATE_VALUE_PT = "Muda-me!";

    public function _before()
    {
        $this->setNewFactoryDefault();
        $this->setDiRedis();

        /** @var Redis $redis */
        $redis = $this->getService("redis");

        $redis->set(md5("en:" . self::TRANSLATE_KEY), self::TRANSLATE_VALUE_EN);
        $redis->set(md5("ru:" . self::TRANSLATE_KEY), self::TRANSLATE_VALUE_RU);
        $redis->set(md5("pt:" . self::TRANSLATE_KEY), self::TRANSLATE_VALUE_PT);
    }

    protected function initTranslate(string $lang = "en"): RedisAdapter
    {
        return new RedisAdapter(
            $this->getService("redis"),
            $lang,
            new InterpolatorFactory()
        );
    }
}
