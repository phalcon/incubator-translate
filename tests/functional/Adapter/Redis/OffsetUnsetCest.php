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

use FunctionalTester;

class OffsetUnsetCest extends Base
{
    public function translateAdapterRedisOffsetUnset(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Redis - offset unset');

        $translator = $translator = $this->initTranslate();

        $I->assertSame($this->getService("redis")->get(md5("en:" . self::TRANSLATE_KEY)), self::TRANSLATE_VALUE_EN);

        $translator->offsetUnset(self::TRANSLATE_KEY);

        $I->assertFalse($this->getService("redis")->get(md5("en:" . self::TRANSLATE_KEY)));
    }
}
