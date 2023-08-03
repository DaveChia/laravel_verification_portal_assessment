<?php

namespace App\DataTransferObjects;

use Illuminate\Http\UploadedFile;
use App\DataTransferObjects\VerificationObject;

class JsonDocument extends VerificationObject
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

    public function __construct(UploadedFile $uploaded_json_file)
    {
        $jsonFile = file_get_contents($uploaded_json_file->getRealPath());

        $jsonFileDecoded = json_decode($jsonFile);

        if (!isset($jsonFileDecoded->data)) {
            throw new \Exception('Missing data key to form JsonDocument');
        }

        $data = $jsonFileDecoded->data;

        if (!isset($data->id)) {
            throw new \Exception('Missing id key to form JsonDocument');
        }

        $this->id = $data->id;

        if (!isset($data->name)) {
            throw new \Exception('Missing name key to form JsonDocument');
        }

        $this->name = $data->name;

        if (!isset($data->recipient)) {
            throw new \Exception('Missing recipient key to form JsonDocument');
        }

        $this->recipient = $data->recipient;

        if (!isset($data->issuer)) {
            throw new \Exception('Missing issuer key to form JsonDocument');
        }

        if (!isset($data->issuer->name)) {
            throw new \Exception('Missing issuer name key to form JsonDocument');
        }

        $this->issuerName = $data->issuer->name;

        if (!isset($data->issuer->identityProof)) {
            throw new \Exception('Missing issuer identityProof key to form JsonDocument');
        }

        if (!isset($data->issuer->identityProof->type)) {
            throw new \Exception('Missing issuer identityProof type key to form JsonDocument');
        }

        $this->issuerIdentityProofType = $data->issuer->identityProof->type;

        if (!isset($data->issuer->identityProof->key)) {
            throw new \Exception('Missing issuer identityProof key key to form JsonDocument');
        }

        $this->issuerIdentityProofKey = $data->issuer->identityProof->key;
        
        if (!isset($data->issuer->identityProof->location)) {
            throw new \Exception('Missing issuer identityProof location key to form JsonDocument');
        }

        $this->issuerIdentityProofLocation = $data->issuer->identityProof->location;
        
        if (!isset($data->issued)) {
            throw new \Exception('Missing issued key to form JsonDocument');
        }

        $this->issuedTimestamp = $data->issued;
        
        if (!isset($jsonFileDecoded->signature)) {
            throw new \Exception('Missing issuer signature key to form JsonDocument');
        }

        if (!isset($jsonFileDecoded->signature->type)) {
            throw new \Exception('Missing issuer signature type key to form JsonDocument');
        }

        $this->signatureType = $jsonFileDecoded->signature->type;

        if (!isset($jsonFileDecoded->signature->targetHash)) {
            throw new \Exception('Missing issuer signature hash key to form JsonDocument');
        }

        $this->signatureTargetHash = $jsonFileDecoded->signature->targetHash;

        if (!isset($jsonFileDecoded->signature->targetHash)) {
            throw new \Exception('Missing issuer signature hash key to form JsonDocument');
        }

        $this->signatureTargetHash = $jsonFileDecoded->signature->targetHash;
    }
}