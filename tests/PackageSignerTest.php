<?php

use PHPUnit\Framework\TestCase;
use Ampersa\JsonSigner\Signer;
use Ampersa\JsonSigner\Signers\PackageSigner;

class PackageSignerTest extends TestCase
{
    /**
     * Test the Signer class signs JSON object
     * @return void
     */
    public function testSignerSigns()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = new PackageSigner('123456789');

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }

    /**
     * Test the signer verifies valid packaged JSON
     * @return void
     */
    public function testSignerVerifies()
    {
        $signedJSON = '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}';

        $signer = new PackageSigner('123456789');

        $verify = $signer->verify($signedJSON);

        $this->assertTrue($verify);
    }

    /**
     * Test the signer fails to verify incorrectly signed JSON
     * @return void
     */
    public function testSignerVerifyFailsOnInvalid()
    {
        $signedJSON = '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"1234567890"}';

        $signer = new PackageSigner('123456789');

        $verify = $signer->verify($signedJSON);

        $this->assertFalse($verify);
    }

    /**
     * Test sign() requires unsigned JSON
     * @return void
     */
    public function testSignRequiresUnsignedJson()
    {
        $this->setExpectedException(Exception::class);

        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2', '__s' => '1234567890']);

        $signer = new PackageSigner('123456789');

        $signed = $signer->sign($json);
    }

    /**
     * Test verify() requires signed JSON
     * @return void
     */
    public function testVerifyRequiresSignedJson()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $signedJSON = '{"__orig":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"}}';

        $signer = new PackageSigner('123456789');

        $verify = $signer->verify($signedJSON);
    }

    /**
     * Test that Signer classes can be access directly
     * @return void
     */
    public function testPackageSignerAllowsPackageKeyChangeDirect()
    {
        $json = json_encode(['key1' => 'value1', 'array1' => ['subkey1' => 'subvalue1', 'subkey2' => 'subvalue2'], 'key2' => 'value2']);

        $signer = (new PackageSigner('123456789'))
                    ->setPackageKey('__package');

        $signed = $signer->sign($json);

        $this->assertEquals($signed, '{"__package":{"key1":"value1","array1":{"subkey1":"subvalue1","subkey2":"subvalue2"},"key2":"value2"},"__s":"f93a2481b14365e53e69399b3f0b5b950d3af1eaba039a2e8089c087af5f3cd1"}');
    }
}
