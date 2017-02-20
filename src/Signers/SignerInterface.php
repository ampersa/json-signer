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
namespace Ampersa\JsonSigner\Signers;

interface SignerInterface
{
    /**
     * Sign a JSON string
     *
     * @param  string $json
     * @return string
     */
    public function sign($json);

    /**
     * Verify a signature string from a signed JSON string
     *
     * @param  string       $json
     * @param  string|null  $signature
     * @return bool
     */
    public function verify($json, $signature = null);

    /**
     * Utility method to set the signing key
     * @param string $signingKey
     */
    public function setSigningKey($key);

    /**
     * Utility method to set the hash algorithm
     * @param string $hashAlgo
     */
    public function setAlgorithm($hashAlgo);

    /**
     * Set the signature key for the signed JSON string
     * @param string $signatureKey
     */
    public function setSignatureKey($signatureKey);
}
