<?php

use PHPUnit\Framework\TestCase;
use Ampersa\JsonSigner\JsonCollection;

class JsonCollectionTest extends TestCase
{
    /**
     * Test the Collection initializes with an Array
     * @return void
     */
    public function testCollectionInitsWithArray()
    {
        $collection = new JsonCollection(['key1' => 'value1', 'key2' => 'value2', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2']]);

        $this->assertInstanceOf(JsonCollection::class, $collection);
        $this->assertEquals($collection->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test the Collection initializes with a valid JSON string
     * @return void
     */
    public function testCollectionInitsWithString()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertInstanceOf(JsonCollection::class, $collection);
        $this->assertEquals($collection->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test the Collection throws an exception when passed bad JSON
     * @return void
     */
    public function testCollectionExceptsWithBadString()
    {
        $this->expectException(InvalidArgumentException::class);

        $collection = new JsonCollection('{key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test the collect initialzes with an object
     * @return void
     */
    public function testCollectionInitsWithObject()
    {
        $collection = new JsonCollection(json_decode('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}'));

        $this->assertInstanceOf(JsonCollection::class, $collection);
        $this->assertEquals($collection->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test the collection initializes with an instance of JsonCollection
     * @return void
     */
    public function testCollectionInitsWithSelf()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
        $collection2 = new JsonCollection($collection);

        $this->assertInstanceOf(JsonCollection::class, $collection2);
        $this->assertEquals($collection2->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test that all() returns an array and inspect contents
     * @return void
     */
    public function testAllReturnsArrayContent()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertEquals($collection->all(), ['key1' => 'value1', 'key2' => 'value2', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2']]);
    }

    /**
     * Test that count() returns the correct integer
     * @return void
     */
    public function testCountReturnsCorrectInteger()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertEquals($collection->count(), 3);
    }

    /**
     * Test that exists() returns true on present and false on missing
     * @return void
     */
    public function testExistsReturnsCorrect()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertTrue($collection->exists('key2'));
        $this->assertFalse($collection->exists('key4'));
    }

    /**
     * Test that forget() forgets a key/value
     * @return void
     */
    public function testForgetForgetsKey()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $collection->forget('key1');

        $this->assertFalse($collection->exists('key1'));
    }

    /**
     * Test the magic __get and __set methods
     * @return void
     */
    public function testMagicMethods()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertEquals($collection->key1, 'value1');

        $collection->key1 = 'new_value';

        $this->assertEquals($collection->key1, 'new_value');
    }

    /**
     * Test that sort() correctly sorts by values
     * @return void
     */
    public function testSortSortsByValues()
    {
        $collection = new JsonCollection('{"key2":"value2","key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $collection->sort();

        $this->assertEquals($collection->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test that sort(true) correctly sorts by values descencding
     * @return void
     */
    public function testSortSortsByValuesDesc()
    {
        $collection = new JsonCollection('{"key2":"value2","key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $collection->sort(true);

        $this->assertEquals($collection->toJson(), '{"array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2","key1":"value1"}');
    }

    /**
     * Test that sortKeys() sorts correctly by keys
     * @return void
     */
    public function testSortKeysSortsByKeys()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $collection->sortKeys();

        $this->assertEquals($collection->toJson(), '{"array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key1":"value1","key2":"value2"}');
    }

    /**
     * Test that toJson() outputs correctly as JSON string
     * @return void
     */
    public function testOutputAsJson()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertEquals($collection->toJson(), '{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');
    }

    /**
     * Test that toArray() correctly outputs as an array
     * @return void
     */
    public function testOutputAsArray()
    {
        $collection = new JsonCollection('{"key1":"value1","key2":"value2","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"}}');

        $this->assertEquals($collection->toArray(), ['key1' => 'value1', 'key2' => 'value2', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2']]);
    }
}
