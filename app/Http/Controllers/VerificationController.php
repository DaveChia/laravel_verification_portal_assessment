<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\VerificationResult;
use App\Http\Requests\StoreVerificationRequest;

class VerificationController extends Controller
{
    public function verify(StoreVerificationRequest $request)
    {
        $validated = $request->validated();

        try {
            $jsonFile = file_get_contents($validated['json_file']->getRealPath());

            $jsonFileDecoded = json_decode($jsonFile);

            $verificationResult = new VerificationResult;
            $verificationResult->file_type = 'JSON';

            if ($this->checkJsonHasValidRecipient($jsonFileDecoded) === false) {
                throw new \Exception('invalid_recipient');
            }

            if ($this->checkJsonHasValidIssuer($jsonFileDecoded) === false) {
                throw new \Exception('invalid_issuer');
            }

            if ($this->checkJsonHasValidSignature($jsonFileDecoded) === false) {
                throw new \Exception('invalid_signature');
            }

            $verificationResult->verification_result = 'verified';
            $verificationResult->save();

        } catch (\Exception $e) {

            $expectedErrorResults = [
                'invalid_recipient',
                'invalid_issuer',
                'invalid_signature',
            ];

            if (in_array($e->getMessage(), $expectedErrorResults)) {

                $verificationResult->verification_result = $e->getMessage();
                $verificationResult->save();

                return response()->json([
                    'data' => [
                        'issuer' => $jsonFileDecoded->data->issuer->name,
                        'result' => $e->getMessage()
                    ],
                ], 200);
            } else {
                return response()->json([
                    'error' => 'unexpected_error',
                ], 200);
            }
        }
        
        return response()->json([
            'data' => [
                'issuer' => $jsonFileDecoded->data->issuer->name,
                'result' => 'verified'
            ],
        ], 200);
    }

    private function checkJsonHasValidRecipient(\stdClass $json) : bool
    {
        if (!isset($json->data)) {
            return false;
        }

        if (!isset($json->data->recipient)) {
            return false;
        }

        if (!isset($json->data->recipient->name) ||
            !isset($json->data->recipient->email) ||
            is_null($json->data->recipient->name) ||
            is_null($json->data->recipient->email) ||
            $json->data->recipient->name === '' ||
            $json->data->recipient->email === ''
        ) {
            return false;

        } 

        return true;
    }

    private function checkJsonHasValidIssuer(\stdClass $json, String $dnsType = 'TXT') : bool
    {
        if (!isset($json->data)) {
            return false;
        }

        if (!isset($json->data->issuer)) {
            return false;
        }

        if (!isset($json->data->issuer->name) ||
            !isset($json->data->issuer->identityProof) ||
            !isset($json->data->issuer->identityProof->key) ||
            !isset($json->data->issuer->identityProof->location) ||
            is_null($json->data->issuer->name) ||
            is_null($json->data->issuer->identityProof) ||
            is_null($json->data->issuer->identityProof->key) ||
            is_null($json->data->issuer->identityProof->location) ||
            $json->data->issuer->name === '' ||
            $json->data->issuer->identityProof === '' ||
            $json->data->issuer->identityProof->key === '' ||
            $json->data->issuer->identityProof->location === ''
        ) {
            return false;
        } 

        $identityProofLocation = $json->data->issuer->identityProof->location; 

        $response = Http::get("https://dns.google/resolve?name=$identityProofLocation&type=$dnsType");

        if ($response->status() !== 200) {
            return false;
        }

        $responseJson = $response->json();

        $ethereumWalletAddress = $json->data->issuer->identityProof->key;

        $responseAnswers = $responseJson['Answer'];

        $ethereumWalletAddressExists = false;

        foreach ($responseAnswers as $answer) {

            if (str_contains($answer['data'], $ethereumWalletAddress)) {
                $ethereumWalletAddressExists = true;
                break;
            }
        }

        if ($ethereumWalletAddressExists = false) {
            return false;
        }
        
        return true;
    }

    public function checkJsonHasValidSignature(\stdClass $json) : bool
    {    
        $associatedDataValues = [
            "id" => $json->data->id,
            "name" => $json->data->name,
            "recipient.name" => $json->data->recipient->name,
            "recipient.email" => $json->data->recipient->email,
            "issuer.name" => $json->data->issuer->name,
            "issuer.identityProof.type" => $json->data->issuer->identityProof->type,
            "issuer.identityProof.key" => $json->data->issuer->identityProof->key,
            "issuer.identityProof.location" => $json->data->issuer->identityProof->location,
            "issued" => $json->data->issued,
        ];

        $computedHashes = [];

        foreach ($associatedDataValues as $key => $value) {
            
            $keyValuePairInObject = (object) array($key => $value);

            $keyValuePairInObject_json_encoded = json_encode($keyValuePairInObject);

            $computedHashes[] = hash('sha256', $keyValuePairInObject_json_encoded);
        }

        sort($computedHashes);

        $alphabeticallyArrangedComputedHash = hash('sha256', json_encode($computedHashes));

        if ($json->signature->targetHash !== $alphabeticallyArrangedComputedHash) {
            return false;
        }

        return true;
    }
}
