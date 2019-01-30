<?php

use PHPUnit\Framework\TestCase;
use Ampersa\JsonSigner\Signer;
use Ampersa\JsonSigner\Signers\PackageSigner;

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

    /**
     * Test that the Signer will throw an Exception if the signature key already exists to prevent collision
     * @return void
     */
    public function testExceptionThrownBeforeCollision()
    {
        $this->setExpectedException(Exception::class);

        $signer = new Signer('SIGNINGKEY');
        $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"testing"}');
    }

    /**
     * Test that the Signer sorts the keys and so is order independent
     * @return void
     */
    public function testSignerIsOrderIndependent()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);
        $signer = new Signer('123456789');
        $signed = $signer->signature($json);

        $json2 = json_encode(['array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key1' => 'value1', 'key2' => 'value2']);
        $signer2 = new Signer('123456789');
        $signed2 = $signer2->signature($json2);

        $this->assertEquals($signed, $signed2);
    }

    /**
     * Test that the Signer accepts a third argument the change the Signer class used
     * @return void
     */
    public function testSignerAcceptsSignerArgument()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = new Signer('123456789', 'sha256', new PackageSigner);

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    /**
     * Test that the Signer accepts a setSigner() function the change the Signer class used
     * @return void
     */
    public function testSignerAcceptsSetSignerFunction()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = (new Signer('123456789'))
                    ->setSigner(new PackageSigner);

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    /**
     * Test that Signer classes can be access directly
     * @return void
     */
    public function testPackageSignerAllowsPackageKeyChange()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = (new Signer('123456789'))
                    ->setSigner(new PackageSigner)
                    ->setPackageKey('__package');

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"__package":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    public function testSignerMagicCallExceptionOnMissing()
    {
        $this->setExpectedException(Exception::class);

        $signer = new Signer;
        $signer->nonExistentFunction();
    }
}
