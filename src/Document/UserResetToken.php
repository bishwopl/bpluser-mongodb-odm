<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Exception\InvalidResetTokenException;
use CirclicalUser\Exception\InvalidResetTokenFingerprintException;
use CirclicalUser\Exception\InvalidResetTokenIpAddressException;
use CirclicalUser\Exception\MismatchedResetTokenException;
use CirclicalUser\Provider\AuthenticationRecordInterface;
use CirclicalUser\Provider\UserResetTokenInterface;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Exception;
use InvalidArgumentException;
use JsonException;
use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidDigestLength;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Alerts\InvalidMessage;
use ParagonIE\Halite\Alerts\InvalidType;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use SodiumException;
use function base64_decode;
use function base64_encode;
use function implode;
use function in_array;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * A password-reset token.  This is the thing that you would exchange in a forgot-password email
 * that the user can later consume to trigger a password change.
 */
#[ODM\Document(collection: "users_auth_reset")]
#[ODM\Index(keys: ['authentication.$id' => 1], options: ["unique" => true])]
class UserResetToken implements UserResetTokenInterface {

    #[ODM\Id]
    private int $id;

    #[ODM\ReferenceOne(targetDocument: "\BplUserMongoDbODM\Document\Authentication")]
    private AuthenticationRecordInterface $authentication;

    #[ODM\Field(type: "string")]
    private string $token;

    #[ODM\Field(type: "date")]
    private DateTimeImmutable $request_time;

    #[ODM\Field(type: "string", options: ["fixed" => true])]
    private string $request_ip_address;

    #[ODM\Field(type: "int", options: ["default" => UserResetTokenInterface::STATUS_UNUSED])]
    private int $status;

    /**
     * @throws InvalidType
     * @throws InvalidDigestLength
     * @throws SodiumException
     * @throws JsonException
     * @throws InvalidKey
     * @throws InvalidMessage
     * @throws CannotPerformOperation
     * @throws Exception
     */
    public function __construct(AuthenticationRecordInterface $authentication, string $requestingIpAddress) {
        $this->authentication = $authentication;
        $this->request_time = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->request_ip_address = $requestingIpAddress;
        $this->status = UserResetTokenInterface::STATUS_UNUSED;

        $fingerprint = $this->getFingerprint();

        $key = new EncryptionKey(new HiddenString($authentication->getRawSessionKey()));
        $this->token = base64_encode(
                Crypto::encrypt(
                        new HiddenString(
                                json_encode([
                                    'fingerprint' => $fingerprint,
                                    'timestamp' => $this->request_time->format('U'),
                                    'userId' => $authentication->getUserId(),
                                        ], JSON_THROW_ON_ERROR)
                        ),
                        $key
                )
        );
    }

    public function getFingerprint(): string {
        return implode(
                ':',
                [
                    $_SERVER['HTTP_USER_AGENT'] ?? 'na',
                    $_SERVER['HTTP_ACCEPT'] ?? 'na',
                    $_SERVER['HTTP_ACCEPT_CHARSET'] ?? 'na',
                    $_SERVER['HTTP_ACCEPT_ENCODING'] ?? 'na',
                    $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'na',
                ]
        );
    }

    public function getToken(): string {
        return $this->token;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setStatus(int $status) {
        if (!in_array($status, [UserResetTokenInterface::STATUS_UNUSED, UserResetTokenInterface::STATUS_INVALID, UserResetTokenInterface::STATUS_USED], true)) {
            throw new InvalidArgumentException("An invalid status is being set!");
        }
        $this->status = $status;
    }

    /**
     * @throws InvalidResetTokenIpAddressException
     * @throws InvalidResetTokenException
     * @throws InvalidResetTokenFingerprintException
     * @throws MismatchedResetTokenException
     */
    public function isValid(
            AuthenticationRecordInterface $authenticationRecord,
            string $checkToken,
            string $requestingIpAddress,
            bool $validateFingerprint,
            bool $validateIp
    ): bool {
        if ($this->token !== $checkToken) {
            return false;
        }

        // this token is for someone else...
        if ($authenticationRecord !== $this->authentication) {
            throw new MismatchedResetTokenException();
        }

        try {
            $encryptedJson = @base64_decode($checkToken);
            $sessionKey = new HiddenString($authenticationRecord->getRawSessionKey());
            $key = new EncryptionKey($sessionKey);
            $jsonString = Crypto::decrypt($encryptedJson, $key)->getString();
        } catch (Exception $x) {
            throw new InvalidResetTokenException();
        }

        try {
            $json = @json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
            if (!isset($json['fingerprint'], $json['timestamp'], $json['userId'])) {
                throw new InvalidResetTokenException();
            }

            if ($validateFingerprint && $json['fingerprint'] !== $this->getFingerprint()) {
                throw new InvalidResetTokenFingerprintException();
            }

            if ($validateIp && $requestingIpAddress !== $this->request_ip_address) {
                throw new InvalidResetTokenIpAddressException();
            }

            if ($json['userId'] !== $authenticationRecord->getUserId()) {
                throw new InvalidResetTokenException();
            }

            return true;
        } catch (JsonException $exception) {
            
        }

        return false;
    }
}
