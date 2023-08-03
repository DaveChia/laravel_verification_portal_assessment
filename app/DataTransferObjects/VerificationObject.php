<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerificationObject
{
    public function verifyDocumentHasValidRecipient() : bool
    {
        if (!isset($this->recipient->name) ||
            !isset($this->recipient->email) ||
            is_null($this->recipient->name) ||
            is_null($this->recipient->email) ||
            $this->recipient->name === '' ||
            $this->recipient->email === ''
        ) {
            return false;
        }

        return true;
    }

    public function verifyJsonHasValidIssuer(String $dnsType = 'TXT') : bool
    {
      
        $identityProofLocation = $this->issuerIdentityProofLocation; 

        $response = Http::get("https://dns.google/resolve?name=$identityProofLocation&type=$dnsType");

        if ($response->status() !== Response::HTTP_OK) {
            throw new \Exception('Failed to retrieve google dns data.');
        }

        $responseJson = $response->json();

        $ethereumWalletAddress = $this->issuerIdentityProofKey;

        
        $responseAnswers = $responseJson['Answer'];

        $ethereumWalletAddressExists = false;

        foreach ($responseAnswers as $answer) {

            if (str_contains($answer['data'], $ethereumWalletAddress)) {

                $ethereumWalletAddressExists = true;
                break;
            }
        }

        if ($ethereumWalletAddressExists === false) {

            return false;
        }
        
        return true;
    }

    public function verifyJsonHasValidSignature() : bool
    {    
        $associatedDataValues = [
            "id" => $this->id,
            "name" => $this->name,
            "recipient.name" => $this->recipient->name,
            "recipient.email" => $this->recipient->email,
            "issuer.name" => $this->issuerName,
            "issuer.identityProof.type" => $this->issuerIdentityProofType,
            "issuer.identityProof.key" => $this->issuerIdentityProofKey,
            "issuer.identityProof.location" => $this->issuerIdentityProofLocation,
            "issued" => $this->issuedTimestamp,
        ];

        $computedHashes = [];

        foreach ($associatedDataValues as $key => $value) {
            
            $keyValuePairInObject = (object) array($key => $value);

            $keyValuePairInObject_json_encoded = json_encode($keyValuePairInObject);

            $computedHashes[] = hash('sha256', $keyValuePairInObject_json_encoded);
        }

        sort($computedHashes);

        $alphabeticallyArrangedComputedHash = hash('sha256', json_encode($computedHashes));

        if ($this->signatureTargetHash !== $alphabeticallyArrangedComputedHash) {
            return false;
        }

        return true;
    }
}