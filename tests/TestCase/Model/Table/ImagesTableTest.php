<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ImagesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ImagesTable Test Case
 */
class ImagesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ImagesTable
     */
    public $Images;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Images',
        'app.Galleries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Images') ? [] : ['className' => ImagesTable::class];
        $this->Images = TableRegistry::getTableLocator()->get('Images', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Images);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->Images->initialize([]);

        $this->assertTrue($this->Images->behaviors()->has('Timestamp'));
        $this->assertTrue($this->Images->associations()->has('Galleries'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = new \Cake\Validation\Validator();
        $validator = $this->Images->validationDefault($validator);

        $this->assertTrue($validator->hasField('id'));
        $this->assertTrue($validator->hasField('name'));

        $image = $this->Images->newEntity([
            'name' => '',
            'filename' => [
                'name' => 'Test',
                'type' => '',
                'tmp_name' => '',
                'error' => UPLOAD_ERR_NO_FILE,
            ],
        ]);

        $errors = $image->getErrors();

        $this->assertTrue(isset($errors['name']));
        $this->assertTrue(isset($errors['filename']));

        $image = $this->Images->newEntity([
            'name' => 'name',
            'filename' => [
                'name' => 'Test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/test',
                'error' => UPLOAD_ERR_OK,
            ],
        ]);

        $errors = $image->getErrors();

        // $this->assertFalse(isset($errors['name']));
        $this->markTestIncomplete('Test file validation to implement.');
        $this->assertFalse(isset($errors['filename']));
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $image = $this->Images->newEntity([
            'name' => 'name',
            'gallery_id' => 123,
        ]);

        $result = $this->Images->checkRules($image);
        $this->assertFalse($result);

        $invalid = $image->getInvalid();
        $this->assertTrue(isset($invalid['gallery_id']));
    }
}
