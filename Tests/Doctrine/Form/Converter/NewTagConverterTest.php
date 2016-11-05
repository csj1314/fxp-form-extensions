<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Tests\Doctrine\Form\Converter;

use Sonatra\Component\FormExtensions\Doctrine\Form\Converter\NewTagConverter;
use Sonatra\Component\FormExtensions\Tests\Doctrine\Form\Fixtures\MockEntity;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class NewTagConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConversion()
    {
        $data = new MockEntity();
        $converter = new NewTagConverter(get_class($data), 'label');

        $this->assertNull($data->getLabel());
        $data = $converter->convert('Foo');
        $this->assertSame('Foo', $data->getLabel());
    }
}
