<?php

namespace Tests\Feature;

use App\DataTransferObjects\JsonDocument;
use Tests\TestCase;

class DocumentVerificationWorkflowTest extends TestCase
{
    public function testJsonDocumentVerificationWorkflow()
    {
        $expected_verification_code = 'verified';

        $json_file_to_test = json_decode(
            '{
                "data": {
                  "id": "63c79bd9303530645d1cca00",
                  "name": "Certificate of Completion",
                  "recipient": {
                    "name": "Marty McFly",
                    "email": "marty.mcfly@gmail.com"
                  },
                  "issuer": {
                    "name": "Accredify",
                    "identityProof": {
                      "type": "DNS-DID",
                      "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
                      "location": "ropstore.accredify.io"
                    }
                  },
                  "issued": "2022-12-23T00:00:00+08:00"
                },
                "signature": {
                  "type": "SHA3MerkleProof",
                  "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
                }
              }'
        );

        $jsonDocument = new JsonDocument($json_file_to_test);

        if ($jsonDocument->verifyDocumentHasValidRecipient() === false) {

            $this->assertEquals($expected_verification_code, 'invalid_recipient');
            exit;
        }

        if ($jsonDocument->verifyJsonHasValidIssuer() === false) {

            $this->assertEquals($expected_verification_code, 'invalid_issuer');
            exit;
        }

        if ($jsonDocument->verifyJsonHasValidSignature() === false) {

            $this->assertEquals($expected_verification_code, 'invalid_signature');
            exit;
        }

        //  The document is 'verified' at this point since all tests above are passed
        $current_verification_code = 'verified';

        $this->assertEquals($expected_verification_code, $current_verification_code);
    }

}
