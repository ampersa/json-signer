<?php
/**
 * JSON Signer and Verifier
 *
 * Copyright (c) 2017 Adam Prickett
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @author Adam Prickett <adam.prickett@ampersa.co.uk>
 * @license MIT
 */
namespace Ampersa\JsonSigner;

use Exception;
use InvalidArgumentException;
use Ampersa\JsonSigner\Support\JsonCollection;
use Ampersa\JsonSigner\Signers\SignerInterface;

class Signer
{
    /** @var string */
    protected $signingKey;

    /** @var string A valid hash algorithm to be passed to hash() */
    protected $hashAlgo;

    /** @var string The key to use for the signature within the JSON string */
    protected $signatureKey = '__s';

    /** @var SignerInterface */
    public $signer = Signers\AppendSigner::class;
    
    /**
     * Construct the signer and set the signing key, if provided
     * @param string|null $signingKey
     */
    public function __construct($signingKey = null, $hashAlgo = 'sha256', SignerInterface $signerClass = null)
    {
        $this->signingKey = $signingKey;
        $this->hashAlgo = $hashAlgo;

        if (empty($signerClass)) {
            $signerClass = new $this->signer($signingKey, $hashAlgo);
        }

        $this->initializeSigner($signerClass);
    }

    /**
     *
     * @param Signer $signerClass
     */
    public function setSigner(SignerInterface $signerClass)
    {
        $this->initializeSigner($signerClass);

        return $this;
    }

    /**
     * Call argument
     * @param  string $function
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($function, $arguments)
    {
        if (method_exists($this->signer, $function)) {
            return call_user_func_array([$this->signer, $function], $arguments);
        }

        throw new Exception(sprintf('Method %s does not exist', $function));
    }

    /**
     * Initialize the signer class
     * @param  class $signerClass
     * @return void
     */
    protected function initializeSigner(SignerInterface $signerClass)
    {
        $this->signer = $signerClass;
        $this->signer->setSigningKey($this->signingKey);
        $this->signer->setAlgorithm($this->hashAlgo);
    }
}
