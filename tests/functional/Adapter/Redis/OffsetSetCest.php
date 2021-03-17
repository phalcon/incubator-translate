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

class OffsetSetCest extends Base
{
    public function translateAdapterRedisOffsetSetWithoutExists(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Redis - offset set without exists');

        $translator = $this->initTranslate();

        $translator->offsetSet("sign_in", "Sign In");

        $I->assertSame($this->getService("redis")->get(md5("en:sign_in")), "Sign In");
    }

    public function translateAdapterRedisOffsetSetWithExists(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Redis - offset set with exists');

        $translator = $this->initTranslate("ru");

        $translator->offsetSet(self::TRANSLATE_KEY, "Поменяй меня 2");

        $I->assertSame($this->getService("redis")->get(md5("ru:" . self::TRANSLATE_KEY)), "Поменяй меня 2");
    }
}
