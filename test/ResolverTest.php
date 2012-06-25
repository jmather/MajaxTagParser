<?php

use \Majax\TagParser\Result\Group;
use \Majax\TagParser\Result\Tag;
use \Majax\TagParser\Resolver;


class ResolverTest extends PHPUnit_Framework_TestCase
{
    private $one_to_ten = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    private $eleven_to_twenty = array(11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
    private $six_to_fifteen = array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15);

    public function testValidResolveForBasicAdd()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->eleven_to_twenty))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $g->addObject(new Tag('tag1'), 'add');
        $g->addObject(new Tag('tag2'), 'add');

        $response = $res->process($g);

        $expected = array_merge(array(), $this->one_to_ten, $this->eleven_to_twenty);

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForBasicSubtract()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->eleven_to_twenty))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $g->addObject(new Tag('tag1'), 'add');
        $g->addObject(new Tag('tag2'), 'subtract');

        $response = $res->process($g);

        $expected = array_merge(array(), $this->one_to_ten);

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForBasicSubtract2()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->six_to_fifteen))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $g->addObject(new Tag('tag1'), 'add');
        $g->addObject(new Tag('tag2'), 'subtract');

        $response = $res->process($g);

        $expected = array(1, 2, 3, 4, 5);

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForTagLimit()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->six_to_fifteen))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $t = new Tag('tag1');
        $t->addLimit(new Tag('tag2'));
        $g->addObject($t);

        $response = $res->process($g);

        $expected = array(6, 7, 8, 9, 10);

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForTagExclude()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->six_to_fifteen))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $t = new Tag('tag1');
        $t->addExclude(new Tag('tag2'));
        $g->addObject($t);

        $response = $res->process($g);

        $expected = array(1, 2, 3, 4, 5);

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForTagLimitAndExclude()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->six_to_fifteen))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $t = new Tag('tag1');
        $t->addLimit(new Tag('tag2'));
        $t->addExclude(new Tag('tag2'));
        $g->addObject($t);

        $response = $res->process($g);

        $expected = array();

        $this->assertEquals($expected, $response);
    }

    public function testValidResolveForTagLimitAndExcludeWithAddition()
    {
        $response_map = array(
            array('tag1', array_merge(array(), $this->one_to_ten)),
            array('tag2', array_merge(array(), $this->six_to_fifteen))
        );


        $repo = $this->getMock('Majax\TagParser\TagRepositoryInterface');
        $repo->expects($this->any())
            ->method('getIdsForTag')
            ->will($this->returnValueMap($response_map));

        $res = new \Majax\TagParser\Resolver($repo);

        $g = new Group();
        $t = new Tag('tag1');
        $t->addLimit(new Tag('tag2'));
        $t->addExclude(new Tag('tag2'));
        $g->addObject($t);
        $g->addObject(new Tag('tag1'));

        $response = $res->process($g);

        $expected = array_merge(array(), $this->one_to_ten);

        $this->assertEquals($expected, $response);
    }
}