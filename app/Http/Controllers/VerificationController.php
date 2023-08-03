<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\VerificationResult;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'json_file' => 'required|file|mimetypes:application/json|max:2000',
        ]);

        try {
            $json_file = file_get_contents($validated['json_file']->getRealPath());

            $json_file_decoded = json_decode($json_file);

            $verification_result = new VerificationResult;
            $verification_result->file_type = 'JSON';

            if ($this->check_json_has_valid_recipient($json_file_decoded) === false) {
                throw new \Exception('invalid_recipient');
            }

            if ($this->check_json_has_valid_issuer($json_file_decoded) === false) {
                throw new \Exception('invalid_issuer');
            }

            if ($this->check_json_has_valid_signature($json_file_decoded) === false) {
                throw new \Exception('invalid_signature');
            }

            $verification_result->verification_result = 'verified';
            $verification_result->save();

        } catch (\Exception $e) {

            $expected_error_results = [
                'invalid_recipient',
                'invalid_issuer',
                'invalid_signature',
            ];

            if (in_array($e->getMessage(), $expected_error_results)) {

                $verification_result->verification_result = $e->getMessage();
                $verification_result->save();

                return response()->json([
                    'data' => [
                        'issuer' => $json_file_decoded->data->issuer->name,
                        'result' => $e->getMessage()
                    ],
                ], 200);
            } else {
                return response()->json([
                    'error' => 'unexpected_error',
                ], 500);
            }
        }
        
        return response()->json([
            'data' => [
                'issuer' => $json_file_decoded->data->issuer->name,
                'result' => 'verified'
            ],
        ], 200);
    }

    private function check_json_has_valid_recipient(\stdClass $json) : bool
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

    private function check_json_has_valid_issuer(\stdClass $json, String $dns_type = 'TXT') : bool
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

        $identity_proof_location = $json->data->issuer->identityProof->location; 

        $response = Http::get("https://dns.google/resolve?name=$identity_proof_location&type=$dns_type");

        if ($response->status() !== 200) {
            return false;
        }

        $response_json = $response->json();

        $ethereum_wallet_address = $json->data->issuer->identityProof->key;

        $response_answers = $response_json['Answer'];

        $ethereum_wallet_address_exists = false;

        foreach ($response_answers as $answer) {

            if (str_contains($answer['data'], $ethereum_wallet_address)) {
                $ethereum_wallet_address_exists = true;
                break;
            }
        }

        if ($ethereum_wallet_address_exists = false) {
            return false;
        }
        
        return true;
    }

    public function check_json_has_valid_signature(\stdClass $json) : bool
    {    
        $associated_data_values = [
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

        $computed_hashes = [];

        foreach ($associated_data_values as $key => $value) {
            
            $key_value_pair_in_object = (object) array($key => $value);

            $key_value_pair_in_object_json_encoded = json_encode($key_value_pair_in_object);

            $computed_hashes[] = hash('sha256', $key_value_pair_in_object_json_encoded);
        }

        sort($computed_hashes);

        $alphabetically_arranged_computed_hash = hash('sha256', json_encode($computed_hashes));

        if ($json->signature->targetHash !== $alphabetically_arranged_computed_hash) {
            return false;
        }

        return true;
    }
}
