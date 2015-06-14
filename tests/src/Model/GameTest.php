<?php

namespace Lorry\Model;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-06-12 at 12:51:38.
 */
class GameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Game
     */
    protected $game;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->game = new Game;
    }

    /**
     * @covers Lorry\Model\Game::setNamespace
     * @covers Lorry\Model\Game::getNamespace
     */
    public function testNamespace()
    {
        $expected = 'gwe';
        $this->game->setNamespace($expected);
        $this->assertSame($expected, $this->game->getNamespace());
    }
    
    /**
     * @covers Lorry\Model\Game::setTitle
     * @covers Lorry\Model\Game::getTitle
     */
    public function testTitle()
    {
        $expected = 'Clonk Planet';
        $this->game->setTitle($expected);
        $this->assertSame($expected, $this->game->getTitle());
    }

    /**
     * @covers Lorry\Model\Game::getAddons
     * @todo   Implement testGetAddons().
     */
    public function testGetAddons()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Lorry\Model\Game::__toString
     */
    public function test__toString()
    {
        $expected = 'Clonk Endeavour';
        $this->game->setTitle($expected);
        $this->assertEquals($expected, $this->game);
        $this->assertSame($expected, (string) $this->game);
    }

    /**
     * @covers Lorry\Model\Game::forApi
     */
    public function testForApi()
    {
        $apiGame = new Game();
        $expectedNamespace = 'extreme';
        $expectedTitle = 'Clonk Extreme';

        $apiGame->setNamespace($expectedNamespace);
        $apiGame->setTitle($expectedTitle);
        
        $this->assertSame(array(
            'namespace' => $expectedNamespace,
            'title' => $expectedTitle
        ), $apiGame->forApi());
    }
}