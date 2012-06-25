<?php

namespace Majax\TagParser;

class Resolver
{
    private $tags;
    private $expressions;

    /** @var \Majax\TagParser\TagRepositoryInterface */
    private $repository;

    public function __construct(TagRepositoryInterface $repository)
    {
        $this->tags = array();
        $this->expressions = array();

        $this->repository = $repository;
    }

    public function process(Result\Group $result)
    {
        $this->resolveGroup($result);
        $this->resolveGroupExpressions($result);
        return $this->expressions[$result->toString()];
    }

    public function resolveGroup(Result\Group $result)
    {
        foreach($result->getObjects() as $object)
        {
            $obj = $object['object'];
            if ($obj instanceof Result\Tag)
            {
                $this->resolveTag($obj);
            } else {
                $this->resolveGroup($obj);
            }
        }
    }

    public function resolveTag(Result\Tag $tag)
    {
        if (!isset($this->tags[$tag->getName()]))
        {
            $results = $this->repository->getIdsForTag($tag->getName());
            $this->tags[$tag->getName()] = $results;
        }
        foreach($tag->getRestrictions() as $restriction)
        {
            /** @var $t \Majax\TagParser\Result\Tag */
            $t = $restriction['tag'];
            $this->resolveTag($t);
        }
    }

    public function resolveGroupExpressions(Result\Group $result)
    {
        if (!isset($this->expressions[$result->toString()]))
        {
            $ret = array();
            foreach($result->getObjects() as $object)
            {
                $obj = $object['object'];
                if ($obj instanceof Result\Tag)
                {
                    $this->resolveTagExpressions($obj);
                } else {
                    $this->resolveGroupExpressions($obj);
                }

                $tmp_result = $this->expressions[$obj->toString()];
                switch($object['method'])
                {
                    case 'add':
                        $ret = array_merge($ret, $tmp_result);
                        break;
                    case 'subtract':
                        $ret = array_diff($ret, $tmp_result);
                        break;
                }
            }
            $ret = array_unique($ret);
            $this->expressions[$result->toString()] = $ret;
        }
    }

    public function resolveTagExpressions(Result\Tag $tag)
    {
        $result = $this->tags[$tag->getName()];

        foreach($tag->getRestrictions() as $restriction)
        {
            /** @var $t \Majax\TagParser\Result\Tag */
            $t = $restriction['tag'];
            $method = $restriction['method'];

            $tmp_result = $this->tags[$t->getName()];

            switch($method)
            {
                case 'limit':
                    $result = array_intersect($result, $tmp_result);
                    break;
                case 'exclude':
                    $result = array_diff($result, $tmp_result);
                    break;
            }
        }

        $result = array_values($result);
        $this->expressions[$tag->toString()] = $result;
    }
}