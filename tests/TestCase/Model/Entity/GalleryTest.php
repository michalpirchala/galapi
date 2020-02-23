<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Gallery;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Gallery Test Case
 */
class GalleryTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Gallery
     */
    public $Gallery;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Gallery = new Gallery();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Gallery);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testPathGeneration()
    {
        $this->Gallery->name = "Wild Animals";
        $this->assertTextEquals("Wild%20Animals", $this->Gallery->path);
    }
}
