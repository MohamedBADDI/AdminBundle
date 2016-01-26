<?php

namespace Symfonian\Indonesia\AdminBundle\Configuration;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class ConfigurationFactory implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $configurations;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function addConfiguration(ConfigurationInterface $configuration)
    {
        $this->configurations[$configuration->getName()] = $configuration;
    }

    public function getConfiguration($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new \InvalidArgumentException(sprintf('Configuration with name %s not found.', $name));
        }

        return $this->configurations[$name];
    }

    /**
     * @param EntityInterface | null $formData
     *
     * @return FormInterface
     */
    public function getForm($formData = null)
    {
        /** @var Crud $crud */
        $crud = $this->getConfiguration('crud');
        $formClass = $crud->getFormClass();
        try {
            $formObject = $this->container->get($formClass);
        } catch (\Exception $ex) {
            $formObject = $this->container->get($formClass);
        }

        $form = $this->formFactory->create(get_class($formObject));
        $form->setData($formData);

        return $form;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}