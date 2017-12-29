<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Event;

use Fxp\Component\Ajax\Event\GetAjaxEvent;
use Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface;
use Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Fxp\Component\FormExtensions\Form\Helper\AjaxChoiceListHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class GetAjaxChoiceListEvent extends GetAjaxEvent
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AjaxChoiceLoaderInterface
     */
    protected $choiceLoader;

    /**
     * @var AjaxChoiceListFormatterInterface
     */
    protected $formatter;

    /**
     * Constructor.
     *
     * @param string                           $id
     * @param RequestStack                     $requestStack
     * @param AjaxChoiceLoaderInterface        $choiceLoader
     * @param AjaxChoiceListFormatterInterface $formatter
     * @param string                           $format
     */
    public function __construct($id, RequestStack $requestStack, AjaxChoiceLoaderInterface $choiceLoader, AjaxChoiceListFormatterInterface $formatter, $format = 'json')
    {
        parent::__construct($id, $format);

        $this->request = $requestStack->getMasterRequest();
        $this->choiceLoader = $choiceLoader;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return AjaxChoiceListHelper::getData($this->request, $this->choiceLoader, $this->formatter, $this->getId().'_');
    }
}
