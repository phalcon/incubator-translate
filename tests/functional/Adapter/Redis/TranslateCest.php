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

class TranslateCest extends Base
{

    public function translate(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();
        $translatePt = $this->initTranslate('pt');

        $I->assertSame('non-key', $translate->t('non-key'));
        $I->assertSame('Replace Me!', $translate->t('replace_me'));
        $I->assertSame('Muda-me!', $translatePt->t('replace_me'));
    }

    public function translateAlias(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();
        $translatePt = $this->initTranslate('pt');

        $I->assertSame('non-key', $translate->_('non-key'));
        $I->assertSame('Replace Me!', $translate->_('replace_me'));
        $I->assertSame('Muda-me!', $translatePt->_('replace_me'));
    }
}
