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
use Ampersa\JsonSigner\JsonCollection;

class Signer
{
    /** @var string */
    protected $signingKey;

    /** @var string A valid hash algorithm to be passed to hash() */
    protected $hashAlgo;

    /** @var string The key to use for the signature within the JSON string */
    protected $signatureKey = '__s';

    /**
     * Construct the signer and set the signing key, if provided
     * @param string|null $signingKey
     */
    public function __construct($signingKey = null, $hashAlgo = 'sha256')
    {
        $this->signingKey = $signingKey;
        $this->hashAlgo = $hashAlgo;
    }

    /**
     * Sign a JSON string
     *
     * @param  string $json
     * @return string
     */
    public function sign($json)
    {
        $collection = new JsonCollection($json);

        if ($collection->exists($this->signatureKey)) {
            throw new Exception('Signature key already exists within this JSON.');
        }

        $collection->{$this->signatureKey} = $this->signature($collection);

        return $collection->toJson();
    }

    /**
     * Sign a JSON string and return the signature, rather than the signed JSON
     * @param  string $json
     * @return string
     */
    public function signature($json)
    {
        $collection = new JsonCollection($json);

        // Lose the signature field, if exists, and sort by top-level keys, ascending
        $collection->forget($this->signatureKey)->sortKeys();

        $signature = $this->createSignature($collection->toJson());

        return $signature;
    }

    /**
     * Verify a signature string from a signed JSON string
     *
     * @param  string       $json
     * @param  string|null  $signature
     * @return bool
     */
    public function verify($json, $signature = null)
    {
        $collection = new JsonCollection(json_decode($json));

        if (!$collection->exists($this->signatureKey) and empty($signature)) {
            throw new InvalidArgumentException('The provided JSON is not signed');
        }

        $ProvidedSignature = $signature;

        if (empty($signature)) {
            $ProvidedSignature = $collection->get($this->signatureKey);
        }

        $tempCollection = $collection->forget($this->signatureKey)->sortKeys();

        $SignatureActual = $this->createSignature($tempCollection->toJson());

        return $ProvidedSignature === $SignatureActual;
    }

    /**
     * Utility method to set the signing key
     * @param string $signingKey
     */
    public function setSigningKey($signingKey)
    {
        $this->signingKey = $signingKey;

        return $this;
    }

    /**
     * Utility method to set the hash algorithm
     * @param string $hashAlgo
     */
    public function setAlgorithm($hashAlgo)
    {
        $this->hashAlgo = $hashAlgo;

        return $this;
    }

    /**
     * Set the signature key for the signed JSON string
     * @param string $signatureKey
     */
    public function setSignatureKey($signatureKey)
    {
        $this->signatureKey = $signatureKey;

        return $this;
    }

    /**
     * Create a hashed signature from a JSON string, signing key
     * and hash method
     *
     * @param  string $json
     * @return string
     */
    protected function createSignature($json)
    {
        return hash($this->hashAlgo, $json.$this->signingKey);
    }
}
