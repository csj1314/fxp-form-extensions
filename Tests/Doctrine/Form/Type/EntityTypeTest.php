<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Tests\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Sonatra\Component\FormExtensions\Doctrine\Form\Type\EntityType;

/**
 * Tests case for entity type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class EntityTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLoader()
    {
        /* @var ManagerRegistry $mr */
        $mr = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        /* @var ObjectManager $om */
        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        /* @var QueryBuilder $qb */
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();

        $type = new EntityType($mr);
        $loader = $type->getLoader($om, $qb, \stdClass::class);

        $this->assertInstanceOf(ORMQueryBuilderLoader::class, $loader);
    }
}
