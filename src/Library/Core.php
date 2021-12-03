<?php

namespace Idler8\Laravel\Library;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use RuntimeException;

class Core
{
    protected $builder;
    public function __construct($key = null, $type = null)
    {
        if (empty($type)) $type = new Sha256();
        if (empty($key)) $key = getenv('APP_KEY');
        $this->builder = Configuration::forSymmetricSigner($type, InMemory::plainText($key));
    }
    public function builder($expire)
    {
        $now = new \DateTimeImmutable();
        $builder = $this->builder->builder()->issuedAt($now);
        if ($expire) $builder = $builder->expiresAt($now->modify($expire));
        return $builder;
    }
    public function encode($value, $expire = '+1 hour')
    {
        if (empty($value)) throw new RuntimeException('JWT Builder No Header');
        $builder = $this->builder($expire);
        $builder = $builder->withHeader('_value', $value);
        return $builder->getToken($this->builder->signer(), $this->builder->signingKey())->toString();
    }
    public function decode($jwt)
    {
        $jwt = trim(str_ireplace('bearer', '', $jwt));
        $parser = $this->builder->parser();
        $token = $parser->parse($jwt);
        if (!$this->builder->validator()->validate($token)) throw new RuntimeException('Permission authentication failed');
        $now = new \DateTimeImmutable();
        if (!$this->token->isExpired($now)) throw new RuntimeException('Permission expired');
        $headers = $this->token->headers();
        return $headers['_value'];
    }
}
