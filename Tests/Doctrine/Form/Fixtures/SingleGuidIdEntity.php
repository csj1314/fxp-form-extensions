<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Doctrine\Form\Fixtures;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class SingleGuidIdEntity
{
    /**
     * @Id @Column(type="guid")
     *
     * @var string
     */
    protected $id;

    /**
     * @Column(type="string")
     *
     * @var string
     */
    public $name;

    /**
     * Constructor.
     *
     * @param string $id   The id
     * @param string $name The name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
