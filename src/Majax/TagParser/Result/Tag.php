<?php

namespace Majax\TagParser\Result;

class Tag
{
    private $name;
    private $restrictions;

    public function __construct($name)
    {
        $this->name = $name;
        $this->restrictions = array();
    }

    public function addLimit(Tag $tag)
    {
        $this->restrictions[] = array('tag' => $tag, 'method' => 'limit');
    }

    public function addExclude(Tag $tag)
    {
        $this->restrictions[] = array('tag' => $tag, 'method' => 'exclude');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRestrictions()
    {
        return $this->restrictions;
    }

    public function __toString()
    {
        $out = '<'.$this->name.':{';
        $restrictions = array();
        foreach($this->restrictions as $restriction)
        {
            $restrictions[] = $restriction['method'].':'.$restriction['tag'];
        }

        $out .= implode(', ', $restrictions).'}>';

        return $out;
    }

    public function toString()
    {
        return $this->__toString();
    }
}