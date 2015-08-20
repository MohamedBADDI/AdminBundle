<?php

namespace Symfonian\Indonesia\AdminBundle\Annotation\Schema;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin.
 */

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CrudTemplate
{
    private $add;

    private $edit;

    private $list;

    private $show;

    private $form;

    private $showFields;

    public function setAdd($add)
    {
        $this->add = $add;
    }

    public function getAdd()
    {
        return $this->add;
    }

    public function setEdit($edit)
    {
        $this->edit = $edit;
    }

    public function getEdit()
    {
        return $this->edit;
    }

    public function setList($list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setShow($show)
    {
        $this->show = $show;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setShowFields($fields)
    {
        $this->showFields = $fields;
    }

    public function getShowFields()
    {
        return $this->showFields;
    }
}