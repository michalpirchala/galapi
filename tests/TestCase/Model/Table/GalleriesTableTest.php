<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GalleriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GalleriesTable Test Case
 */
class GalleriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GalleriesTable
     */
    public $Galleries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Galleries',
        'app.Images',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Galleries') ? [] : ['className' => GalleriesTable::class];
        $this->Galleries = TableRegistry::getTableLocator()->get('Galleries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Galleries);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->Galleries->associations()->has('Images'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = new \Cake\Validation\Validator();
        $validator = $this->Galleries->validationDefault($validator);

        $this->assertTrue($validator->hasField('id'));
        $this->assertTrue($validator->hasField('name'));

        $gallery = $this->Galleries->newEntity([
            'name' => 'Test',
        ]);
        $errors = $gallery->getErrors();
        $this->assertFalse(isset($errors['name']));

        $gallery = $this->Galleries->newEntity([
            'name' => '',
        ]);
        $errors = $gallery->getErrors();
        $this->assertTrue(isset($errors['name']));

        $gallery = $this->Galleries->newEntity([
            'name' => 'dd/dd',
        ]);
        $errors = $gallery->getErrors();
        $this->assertTrue(isset($errors['name']));

        $gallery = $this->Galleries->newEntity([
            'name' => '/dd',
        ]);
        $errors = $gallery->getErrors();
        $this->assertTrue(isset($errors['name']));
    }
}
