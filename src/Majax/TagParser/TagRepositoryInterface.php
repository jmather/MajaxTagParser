<?php

namespace Majax\TagParser;

interface TagRepositoryInterface
{
    /**
     * @abstract
     * @param $tag String
     * @return string[]
     */
    public function getIdsForTag($tag);
}