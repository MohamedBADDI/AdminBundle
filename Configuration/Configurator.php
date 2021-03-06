<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Configuration;

use Symfonian\Indonesia\AdminBundle\Annotation\Crud;
use Symfonian\Indonesia\AdminBundle\Annotation\Grid;
use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;
use Symfonian\Indonesia\AdminBundle\EventListener\AbstractListener;
use Symfonian\Indonesia\AdminBundle\Exception\ClassNotFoundException;
use Symfonian\Indonesia\AdminBundle\Exception\RuntimeException;
use Symfonian\Indonesia\AdminBundle\Extractor\ExtractorFactory;
use Symfonian\Indonesia\AdminBundle\Grid\Column;
use Symfonian\Indonesia\AdminBundle\Grid\Filter;
use Symfonian\Indonesia\AdminBundle\Grid\Sortable;
use Symfonian\Indonesia\AdminBundle\Manager\Driver;
use Symfonian\Indonesia\AdminBundle\View\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Configurator extends AbstractListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var array
     */
    private $configurations = array();

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var bool
     */
    private $freeze = false;

    /**
     * @param KernelInterface  $kernel
     * @param ExtractorFactory $extractor
     * @param FormFactory      $formFactory
     * @param string           $driver
     */
    public function __construct(KernelInterface $kernel, ExtractorFactory $extractor, FormFactory $formFactory, $driver)
    {
        $this->kernel = $kernel;
        $this->formFactory = $formFactory;
        parent::__construct($extractor, $driver);
    }

    /**
     * @param array $filter
     */
    public function setFilter(array $filter)
    {
        $this->filters = $filter;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @throws RuntimeException
     */
    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if ($this->freeze) {
            throw new RuntimeException('Can\'t change any configuration during production');
        }

        if ($configuration instanceof ContainerAwareInterface) {
            $configuration->setContainer($this->container);
        }
        if ($configuration instanceof Crud) {
            $configuration->setFormFactory($this->formFactory);
            $configuration->setTemplate($this->template);
        }

        $this->configurations[get_class($configuration)] = $configuration;
    }

    /**
     * @param $name
     *
     * @throws ClassNotFoundException
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration($name)
    {
        if (!array_key_exists($name, $this->configurations)) {
            throw new ClassNotFoundException(sprintf('Configuration for %s not found.', $name));
        }

        return $this->configurations[$name];
    }

    /**
     * @return array
     */
    public function getAllConfigurations()
    {
        return $this->configurations;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function configureTemplate(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        /** @var Crud $crud */
        $crud = $this->getConfiguration(Crud::class);
        $crud->setTemplate($this->template);

        $this->addConfiguration($crud);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function configureGrid(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        /** @var Grid $grid */
        $grid = $this->getConfiguration(Grid::class);
        $grid->setFilters($this->filters);
        $grid->setSortable($this->filters);
        $grid->setColumns(array());

        $this->addConfiguration($grid);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function parseAnnotation(FilterControllerEvent $event)
    {
        if (!$this->isValidCrudListener($event)) {
            return;
        }

        $reflectionObject = new \ReflectionObject($this->getController());

        $this->getExtractor()->extract($reflectionObject);
        foreach ($this->getExtractor()->getClassAnnotations() as $annotation) {
            if ($annotation instanceof ConfigurationInterface) {
                if ($annotation instanceof ContainerAwareInterface) {
                    $annotation->setContainer($this->container);
                }

                if ($annotation instanceof Crud) {
                    $annotation->setFormFactory($this->formFactory);
                }

                $this->addConfiguration($annotation);
            }
        }
    }

    /**
     * @param string $class
     */
    public function parseClass($class)
    {
        if ('prod' === strtolower($this->kernel->getEnvironment())) {
            return;
        }

        $filters = array();
        $columns = array();
        $sortable = array();

        $reflection = new \ReflectionClass($class);

        $this->getExtractor()->extract($reflection);

        foreach ($this->getExtractor()->getClassAnnotations() as $annotation) {
            if ($annotation instanceof ConfigurationInterface) {
                if ($annotation instanceof Driver) {
                    $this->setDriver($annotation->getDriver());
                }
            }
        }

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            $this->getExtractor()->extract($reflectionProperty);
            foreach ($this->getExtractor()->getPropertyAnnotations() as $annotation) {
                if ($annotation instanceof Filter) {
                    $filters[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Column) {
                    $columns[] = $reflectionProperty->getName();
                }
                if ($annotation instanceof Sortable) {
                    $sortable[] = $reflectionProperty->getName();
                }
            }
        }

        /** @var Grid $grid */
        $grid = $this->getConfiguration(Grid::class);
        if (!empty($filters)) {
            $grid->setFilters($filters);
        }
        if (!empty($columns)) {
            $grid->setColumns($columns);
        }
        if (!empty($sortable)) {
            $grid->setSortable($columns);
        }
        $this->addConfiguration($grid);
    }

    public function freeze()
    {
        $this->freeze = true;
    }
}
