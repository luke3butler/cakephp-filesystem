<?php
namespace Josbeir\Filesystem\Test\TestCase;

use Cake\TestSuite\TestCase;
use Josbeir\Filesystem\FileEntity;
use Josbeir\Filesystem\Filesystem;

class FileEntityTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->manager = new Filesystem([
            'adapterArguments' => [ dirname(__DIR__) . '/test_app/assets' ]
        ]);

        $this->file = $this->manager->upload(dirname(__DIR__) . '/test_app/dummy.png');
    }

    public function tearDown()
    {
        exec('rm -rf ' . dirname(__DIR__) . '/test_app/assets/*');
        unset($this->file);

        parent::tearDown();
    }

    public function testConstructor()
    {
        $arrayData = $this->file->toArray();
        $entity = new FileEntity($arrayData);

        $this->assertInstanceOf('Cake\i18n\Time', $entity->created);
    }

    public function testBadConstructorParams()
    {
        $this->expectException('\Josbeir\Filesystem\Exception\FileEntityException');

        $arrayData = $this->file->toArray();
        $entity = new FileEntity([
            'this' => 'is',
            'not' => 'a',
            'good' => 'constructor'
        ]);
    }

    public function testStringDate()
    {
        $arrayData = $this->file->toArray();
        $expected = (string)$arrayData['created'];
        $arrayData['created'] = $expected;

        $entity = new FileEntity($arrayData);
        $this->assertEquals($expected, (string)$entity->created);
    }

    public function testUnavailableAttributes()
    {
        $this->expectException('\Josbeir\Filesystem\Exception\FileEntityException');

        $this->file->helloworld;
    }

    public function testAttributes()
    {
        $this->assertEquals($this->file->getPath(), 'dummy.png');
        $this->assertEquals($this->file->originalFilename, 'dummy.png');
        $this->assertEquals($this->file->filesize, 59992);
        $this->assertEquals($this->file->mime, 'image/png');
        $this->assertEquals($this->file->hash, '3ba92ed92481b4fc68842a2b3dcee525');

        $this->assertInstanceOf('Cake\i18n\Time', $this->file->created);

        $this->assertInternalType('int', $this->file->filesize);
        $this->assertInternalType('array', $this->file->toArray());
    }

    public function testGetHasHash()
    {
        $this->assertEquals('3ba92ed92481b4fc68842a2b3dcee525', $this->file->getHash());
        $this->assertTrue($this->file->hasHash('3ba92ed92481b4fc68842a2b3dcee525'));
    }

    public function testMagic()
    {
        $json = json_encode($this->file);
        $this->assertJson($json);

        $string = (string)$this->file;
        $this->assertEquals('dummy.png', $string);
    }
}
