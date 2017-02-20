# JSON Signer and Validator
Signs JSON strings with a signed hash and validates signed strings.

*Version 1.2*

## Installation
Installation is via composer:
```
composer require ampersa/json-signer
```

## Usage
To sign a JSON string, pass the signing key to the new Signer and call sign() passing the JSON string:
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}
```

Alternatively, to return the signature and leave the JSON string intact, call signature() with the JSON string:
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signed = $signer->signature('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: 6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0
```

To validate a signed JSON string, call verify() passing the signed JSON string:
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signed = $signer->verify('{"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}');

// Returns: true
```

Validating a signature separately is as simple as passing the signature as the second argument to verify():
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signed = $signer->verify('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}', '6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0');

// Returns: true
```

## Signers
2 Signer classes are included:
* AppendSigner
* PackageSigner

The Signer defaults to AppendSigner, appending the signature key to the JSON object.

PackageSigner packages the original JSON object and signature key into a new parent object, i.e:
```php
$signer = (new \Ampersa\JsonSigner\Signer('SIGNINGKEY'))
            ->setSigner(new \Ampersa\JsonSigner\Signers\PackageSigner);
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"__orig":{"key1":"value1","array1":{"key2":"value2","key3":"value3"}},"__s":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}

$signer = (new \Ampersa\JsonSigner\Signer('SIGNINGKEY'))
            ->setSigner(new \Ampersa\JsonSigner\Signers\PackageSigner);
$signed = $signer->verify('{"__orig":{"key1":"value1","array1":{"key2":"value2","key3":"value3"}},"__s":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}');

// Returns: true
```

**Be sure to use the correct Signer class for both signing and verifying**

Signer classes may also be accessed directly:
```php
$signer = new \Ampersa\JsonSigner\Signers\PackageSigner('SIGNINGKEY');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"__orig":{"key1":"value1","array1":{"key2":"value2","key3":"value3"}},"__s":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}
```

## Config

###Signature Key
Set the key used to hold to signature in the signed string. This can be used to avoid collisions with existing keys. 

**If sign() is called on a string which already contains the signature key, an Exception will be thrown**
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signer->setSignatureKey('customSignature');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"key1":"value1","array1":{"key2":"value2","key3":"value3"},"customSignature":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}
```

###Hash Algorithm
The signer defaults to using SHA256 as the signing algorithm. This can be changed, either via the second construct argument, or via setAlgorithm():
```php
$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY', 'md5');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"2eedf7bd7c18ae0e8db2f6dc86f5df57"}

$signer = new \Ampersa\JsonSigner\Signer('SIGNINGKEY');
$signer->setAlgorithm('sha1');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"key1":"value1","array1":{"key2":"value2","key3":"value3"},"__s":"e8d409703677aef50b897fa0e0cb7fc6898ae690"}
```

###Package Key
When utilising the PackageSigner class, you may set the key used to hold to original JSON package in the signed string:

```php
$signer = new \Ampersa\JsonSigner\Signers\PackageSigner('SIGNINGKEY');
$signer->setPackageKey('package');
$signed = $signer->sign('{"key1":"value1","array1":{"key2":"value2","key3":"value3"}}');

// Returns: {"package": {"key1":"value1","array1":{"key2":"value2","key3":"value3"}},"customSignature":"6bf4dbb38474dfbffa5980cae38d0e24fe73100e710f6a97efc8fb3620655ab0"}
```
