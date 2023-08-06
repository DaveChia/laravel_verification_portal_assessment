<?php

namespace Tests\Unit;

use App\DataTransferObjects\JsonDocument;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class DocumentVerificationTest extends TestCase
{
    public function testSha256HashingMethod() : void
    {
        $target_hash = '8d79f393cc294fd3daca0402209997db5ff8a2ad1a498702f0956952677881ae';

        $data_to_hash = '{"id":"63c79bd9303530645d1cca00"}';

        $hashed_data = hash('sha256', $data_to_hash);

        $this->assertEquals($target_hash, $hashed_data);
    }

    public function testGoogleDnsRetrievalApi() : void
    {
        $identityProofLocation = 'ropstore.accredify.io';
        $dnsType = 'TXT';

        $response = Http::get("https://dns.google/resolve?name=$identityProofLocation&type=$dnsType");

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testJsonDocumentCreation() : void
    {
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

        new JsonDocument($json_file_to_test);

        $this->assertTrue(true);
    }

    public function testJsonDocumentHasValidRecipient() : void
    {
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

        $this->assertTrue($jsonDocument->verifyDocumentHasValidRecipient());
    }

    public function testJsonDocumentHasValidIssuer() : void
    {
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

        $this->assertTrue($jsonDocument->verifyDocumentHasValidIssuer());
    }

    public function testJsonDocumentHasValidSignature() : void
    {
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

        $this->assertTrue($jsonDocument->verifyDocumentHasValidSignature());
    }
}
