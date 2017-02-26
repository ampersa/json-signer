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

use Exception;
use InvalidArgumentException;
use Ampersa\JsonSigner\Support\JsonCollection;
use Ampersa\JsonSigner\Signers\AbstractSigner;

class AppendSigner extends AbstractSigner implements SignerInterface
{
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

        $collection->set($this->signatureKey, $this->signature($collection));

        return $collection->toJson();
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
}
