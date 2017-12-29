<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Form\Util;

use Fxp\Component\FormExtensions\Form\Util\DateTimeUtil;
use PHPUnit\Framework\TestCase;

/**
 * Tests case for datetime util.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class DateTimeUtilTest extends TestCase
{
    public function testGetJsFormat()
    {
        $this->assertTrue(in_array(DateTimeUtil::getJsFormat('en_US'), array('M/D/YYYY h:mm A', 'M/D/YYYY, h:mm A')));
    }

    public function testGetJsFormatFr()
    {
        $this->assertSame('DD/MM/YYYY HH:mm', DateTimeUtil::getJsFormat('fr_FR'));
    }
}
