<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Fxp\Component\FormExtensions\Doctrine\Form\ChoiceList\QueryBuilderTransformer;
use Fxp\Component\FormExtensions\Doctrine\Form\Type\EntityType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Tests case for entity type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class EntityTypeTest extends TestCase
{
    public function testGetLoader()
    {
        /* @var ManagerRegistry $mr */
        $mr = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        /* @var ObjectManager $om */
        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        /* @var QueryBuilder $qb */
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        /* @var FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject $builder */
        $builder = $this->getMockBuilder(FormBuilderInterface::class)->getMock();

        $type = new EntityType($mr);
        $type->configureOptions(new OptionsResolver());
        $loader = $type->getLoader($om, $qb, \stdClass::class);
        $type->buildForm($builder, [
            'multiple' => false,
            'query_builder_transformer' => new QueryBuilderTransformer(),
        ]);

        $this->assertInstanceOf(ORMQueryBuilderLoader::class, $loader);
    }
}
