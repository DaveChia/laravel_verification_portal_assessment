<?php

namespace App\DataTransferObjects;

use App\DataTransferObjects\Service\VerificationService;
use App\Exceptions\MisformedDataException;

/**
 * Sets up a JsonDocument class for data verification purpose
 *
 * @throws MisformedDataException
 */
class JsonDocument extends VerificationService
{
    public string $id;
    public string $name;
    public object $recipient;
    public string $issuerName;
    public string $issuerIdentityProofType;
    public string $issuerIdentityProofKey;
    public string $issuerIdentityProofLocation;
    public string $signatureType;
    public string $signatureTargetHash;
    public string $issuedTimestamp;

    public function __construct(\stdClass $jsonFileDecoded)
    {
        if (!isset($jsonFileDecoded->data)) {
            throw new MisformedDataException('Missing data key to form JsonDocument');
        }

        $data = $jsonFileDecoded->data;

        if (!isset($data->id)) {
            throw new MisformedDataException('Missing id key to form JsonDocument');
        }

        $this->id = $data->id;

        if (!isset($data->name)) {
            throw new MisformedDataException('Missing name key to form JsonDocument');
        }

        $this->name = $data->name;

        if (!isset($data->recipient)) {
            throw new MisformedDataException('Missing recipient key to form JsonDocument');
        }

        $this->recipient = $data->recipient;

        if (!isset($data->issuer)) {
            throw new MisformedDataException('Missing issuer key to form JsonDocument');
        }

        if (!isset($data->issuer->name)) {
            throw new MisformedDataException('Missing issuer name key to form JsonDocument');
        }

        $this->issuerName = $data->issuer->name;

        if (!isset($data->issuer->identityProof)) {
            throw new MisformedDataException('Missing issuer identityProof key to form JsonDocument');
        }

        if (!isset($data->issuer->identityProof->type)) {
            throw new MisformedDataException('Missing issuer identityProof type key to form JsonDocument');
        }

        $this->issuerIdentityProofType = $data->issuer->identityProof->type;

        if (!isset($data->issuer->identityProof->key)) {
            throw new MisformedDataException('Missing issuer identityProof key key to form JsonDocument');
        }

        $this->issuerIdentityProofKey = $data->issuer->identityProof->key;

        if (!isset($data->issuer->identityProof->location)) {
            throw new MisformedDataException('Missing issuer identityProof location key to form JsonDocument');
        }

        $this->issuerIdentityProofLocation = $data->issuer->identityProof->location;

        if (!isset($data->issued)) {
            throw new MisformedDataException('Missing issued key to form JsonDocument');
        }

        $this->issuedTimestamp = $data->issued;

        if (!isset($jsonFileDecoded->signature)) {
            throw new MisformedDataException('Missing issuer signature key to form JsonDocument');
        }

        if (!isset($jsonFileDecoded->signature->type)) {
            throw new MisformedDataException('Missing issuer signature type key to form JsonDocument');
        }

        $this->signatureType = $jsonFileDecoded->signature->type;

        if (!isset($jsonFileDecoded->signature->targetHash)) {
            throw new MisformedDataException('Missing issuer signature hash key to form JsonDocument');
        }

        $this->signatureTargetHash = $jsonFileDecoded->signature->targetHash;

        if (!isset($jsonFileDecoded->signature->targetHash)) {
            throw new MisformedDataException('Missing issuer signature hash key to form JsonDocument');
        }

        $this->signatureTargetHash = $jsonFileDecoded->signature->targetHash;
    }
}
