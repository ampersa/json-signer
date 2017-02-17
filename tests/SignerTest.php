<?php

use PHPUnit\Framework\TestCase;
use Ampersa\JsonSigner\Signer;

class SignerTest extends TestCase
{
    /**
     * Test that the signer correctly signs a JSON string
     * @return void
     */
    public function testSignerSigns()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = new Signer('123456789');

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2","__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    /**
     * Test that the signer returns a different signature for differing key inputs
     * @return void
     */
    public function testSignerProducesDifferentSignature()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer1 = new Signer('123456789');

        $signed1 = $signer1->sign($json);
        $json1 = json_decode($signed1);

        $signer2 = new Signer('987654321');

        $signed2 = $signer2->sign($json);
        $json2 = json_decode($signed2);

        $this->assertNotEquals($json1->__s, $json2->__s);
    }

    /**
     * Test that the signer signs and returns a detatched signature
     * @return void
     */
    public function testDetatchedSignature()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = new Signer('123456789');

        $signed = $signer->signature($json);

        $this->assertEquals($signed, 'f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1');
    }

    /**
     * Test that the signer verifies a valid signed JSON string
     * @return void
     */
    public function testSignatureVerifies()
    {
        $signedJSON = '{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2","__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}';

        $signer = new Signer('123456789');

        $signed = $signer->verify($signedJSON);

        $this->assertTrue($signed);
    }

    /**
     * Test that the signer verifies a JSON string with a detatched signature
     * @return void
     */
    public function testDetatchedSignatureVerifies()
    {
        $signedJSON = '{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"}';

        $signer = new Signer('123456789');

        $signed = $signer->verify($signedJSON, 'f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1');

        $this->assertTrue($signed);
    }

    /**
     * Test that a custom signature key for the string generates correctly
     * @return void
     */
    public function testCustomSignatureKeySigns()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = (new Signer('123456789'))
                    ->setSignatureKey('customSignature');

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2","customSignature":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    /**
     * Test that a custom signature key for the string continues to validate
     * @return void
     */
    public function testCustomSignatureKeyVerifies()
    {
        $signedJSON = '{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2","customSignature":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}';

        $signer = (new Signer('123456789'))
                    ->setSignatureKey('customSignature');

        $signed = $signer->verify($signedJSON);

        $this->assertTrue($signed);
    }

    public function testExceptionThrownBeforeCollision()
    {
        $this->expectException(Exception::class);

        $signer = new Signer('SIGNINGKEY');
        $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"testing"}');
    }
}
