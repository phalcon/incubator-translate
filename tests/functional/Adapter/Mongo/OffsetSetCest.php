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

use FunctionalTester;
use Phalcon\Incubator\Translate\Test\Fixtures\Collections\Translate;

class OffsetSetCest extends Base
{
    public function translateAdapterMongoOffsetSetWithoutExists(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Mongo - offset set without exists');

        $translator = $this->initTranslate();

        $translator->offsetSet("sign_in", "Sign In");

        $translate = Translate::findFirst([["key" => "sign_in"]]);

        $I->assertSame($translate->en, "Sign In");
    }

    public function translateAdapterMongoOffsetSetWithExists(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Mongo - offset set with exists');

        $translator = $this->initTranslate("ru");

        $translator->offsetSet(self::TRANSLATE_KEY, "Поменяй меня 2");

        $translate = Translate::findFirst([["key" => self::TRANSLATE_KEY]]);

        $I->assertSame($translate->en, self::TRANSLATE_VALUE_EN);
        $I->assertSame($translate->ru, "Поменяй меня 2");
    }
}
