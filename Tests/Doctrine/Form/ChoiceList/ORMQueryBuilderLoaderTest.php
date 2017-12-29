<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Doctrine\Form\ChoiceList;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ORMQueryBuilderLoaderTest extends TestCase
{
    public function getIdentityTypes()
    {
        return [
            ['Symfony\Bridge\Doctrine\Tests\Fixtures\SingleStringIdEntity', Connection::PARAM_STR_ARRAY],
            ['Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', Connection::PARAM_INT_ARRAY],
            ['Fxp\Component\FormExtensions\Tests\Doctrine\Form\Fixtures\SingleGuidIdEntity', Connection::PARAM_STR_ARRAY],
        ];
    }

    /**
     * @dataProvider getIdentityTypes
     *
     * @param string $className
     * @param int    $expectedType
     */
    public function testCheckIdentifierType($className, $expectedType)
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(['setParameter', 'getResult', 'getSql', '_doExecute'])
            ->getMock();

        $query->expects($this->once())
            ->method('setParameter')
            ->with('ORMQueryBuilderLoader_getEntitiesByIds_id', [1, 2], $expectedType)
            ->willReturn($query);

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs([$em])
            ->setMethods(['getQuery'])
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from($className, 'e');

        $loader = new ORMQueryBuilderLoader($qb);
        $loader->getEntitiesByIds('id', [1, 2]);
    }

    public function testFilterNonIntegerValues()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(['setParameter', 'getResult', 'getSql', '_doExecute'])
            ->getMock();

        $query->expects($this->once())
            ->method('setParameter')
            ->with('ORMQueryBuilderLoader_getEntitiesByIds_id', [1, 2, 3], Connection::PARAM_INT_ARRAY)
            ->willReturn($query);

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs([$em])
            ->setMethods(['getQuery'])
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new ORMQueryBuilderLoader($qb);
        $loader->getEntitiesByIds('id', [1, '', 2, 3, 'foo']);
    }

    public function testFilterEmptyValues()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(['setParameter', 'getResult', 'getSql', '_doExecute'])
            ->getMock();

        $query->expects($this->never())
            ->method('setParameter');

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs([$em])
            ->setMethods(['getQuery'])
            ->getMock();

        $qb->expects($this->never())
            ->method('getQuery');

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new ORMQueryBuilderLoader($qb);
        $loader->getEntitiesByIds('id', []);
    }

    public function testGetEntities()
    {
        $em = DoctrineTestHelper::createTestEntityManager();

        $query = $this->getMockBuilder('QueryMock')
            ->setMethods(['execute'])
            ->getMock();

        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->setConstructorArgs([$em])
            ->setMethods(['getQuery'])
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $qb->select('e')
            ->from('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity', 'e');

        $loader = new ORMQueryBuilderLoader($qb);
        $loader->getEntities();
    }

    public function testGetQueryBuilder()
    {
        /* @var QueryBuilder|\PHPUnit_Framework_MockObject_MockObject $qb */
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $loader = new ORMQueryBuilderLoader($qb);

        $this->assertSame($qb, $loader->getQueryBuilder());
    }

    /**
     * Init the doctrine entity manager.
     *
     * @return EntityManagerInterface
     */
    protected function initEntityManager()
    {
        $em = DoctrineTestHelper::createTestEntityManager();
        $schemaTool = new SchemaTool($em);
        $classes = [
            $em->getClassMetadata('Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity'),
        ];

        try {
            $schemaTool->dropSchema($classes);
        } catch (\Exception $e) {
        }

        try {
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
        }

        return $em;
    }
}
