<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Annotation;

use Symfonian\Indonesia\AdminBundle\Contract\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class Page implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $title = 'SIAB';

    /**
     * @var string
     */
    private $description = 'Symfonian Indonesia Admin Bundle';

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['value'])) {
            $this->title = $data['value'];
        }

        if (isset($data['title'])) {
            $this->title = $data['title'];
        }

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        unset($data);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
