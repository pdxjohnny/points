<?php
require_once(__DIR__ . '/../vendor/autoload.php');

class Auth
{
    private $signer;
    private $privateKey;
    private $publicKey;

    public function __construct() {
        $this->signer = new Lcobucci\JWT\Signer\Rsa\Sha256();
        $this->privateKey = new Lcobucci\JWT\Signer\Key('file://' . $_ENV['TOKEN_PRIVATE_KEY']);
        $this->publicKey = new Lcobucci\JWT\Signer\Key('file://' . $_ENV['TOKEN_PUBLIC_KEY']);
    }

    public function origin() {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        return $protocol . $domainName;
    }

    public function create_token($user) {
        $token = (new Lcobucci\JWT\Builder())->setIssuer($this->origin())
            ->setAudience($this->origin())
            ->setId($user['id'] . "_" . time(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 3600)
            ->set('uid', $user['id'])
            ->set('username', $user['username'])
            ->sign($this->signer,  $this->privateKey)
            ->getToken();
        return $token->__toString();
    }

    public function verify($token) {
        return $token->verify($this->signer, $this->publicKey);
    }
}
?>
