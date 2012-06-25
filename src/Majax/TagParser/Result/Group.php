<?php

namespace Majax\TagParser\Result;

class Group
{
    private $objects;
    private $parent;

    public function __construct(Group $parent = null)
    {
        $this->tags = array();
        $this->parent = $parent;
    }

    public function addObject($object, $method = 'add')
    {
        $this->objects[] = array('object' => $object, 'method' => $method);
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function __toString()
    {
        $out = '';
        $objs = array();
        foreach($this->objects as $t)
        {
            $objs[] = $t['method'].': '.$t['object'];
        }
        return '['.implode(', ', $objs).']';
    }

    public function toString()
    {
        return $this->__toString();
    }
}