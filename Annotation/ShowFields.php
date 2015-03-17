<?php
namespace Ihsan\SimpleAdminBundle\Annotation;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ShowFields
{
    public $value;

    public function isValid()
    {
        if (! is_array($this->value)) {
            return false;
        }

        return true;
    }
}
