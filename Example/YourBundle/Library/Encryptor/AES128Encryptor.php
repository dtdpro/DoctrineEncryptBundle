<?php

namespace Ambta\DoctrineEncryptBundle\Encryptors;

/**
 * Class for AES128 encryption.
 */
class AES128Encryptor implements EncryptorInterface
{
    const ENCRYPT_NAME = 'AES-128';
    const ENCRYPT_MODE = 'ECB';

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $encryptMethod;

    /**
     * @var string
     */
    private $initializationVector;

    /**
     * {@inheritdoc}
     */
    public function __construct($key)
    {
        $this->secretKey = md5($key);
        $this->encryptMethod = sprintf('%s-%s', self::ENCRYPT_NAME, self::ENCRYPT_MODE);
        $this->initializationVector = openssl_random_pseudo_bytes(
            openssl_cipher_iv_length($this->encryptMethod)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        if (is_string($data)) {
            return trim(base64_encode(openssl_encrypt(
                $data,
                $this->encryptMethod,
                $this->secretKey,
                0,
                $this->initializationVector
            ))).'<ENC>';
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($data)
    {
        if (is_string($data)) {
            $data = str_replace('<ENC>', '', $data);

            return trim(openssl_decrypt(
                base64_decode($data),
                $this->encryptMethod,
                $this->secretKey,
                0,
                $this->initializationVector
            ));
        }

        return $data;
    }
}
