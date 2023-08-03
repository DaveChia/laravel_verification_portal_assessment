<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTransferObjects\JsonDocument;
use App\Models\VerificationResult;
use App\Http\Requests\StoreVerificationRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\DocumentVerificationException;

class VerificationController extends Controller
{
    public function verify(StoreVerificationRequest $request)
    {
        $validated = $request->validated();

        $verificationResult = new VerificationResult;
        $verificationResult->file_type = 'JSON';

        try {
            $jsonDocument = new JsonDocument($validated['json_file']);
       
            if ($jsonDocument->verifyDocumentHasValidRecipient() === false) {
                throw new DocumentVerificationException('invalid_recipient');
            }

            if ($jsonDocument->verifyJsonHasValidIssuer() === false) {
                throw new DocumentVerificationException('invalid_issuer');
            }

            if ($jsonDocument->verifyJsonHasValidSignature() === false) {
                throw new DocumentVerificationException('invalid_signature');
            }

        } catch (DocumentVerificationException $e) {

            $verificationResult->verification_result = $e->getMessage();
            $verificationResult->save();

            return response()->json([
                'data' => [
                    'issuer' => $jsonDocument->issuerName,
                    'result' => $e->getMessage()
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'unexpected_error',
            ], Response::HTTP_OK);
        }

        $verificationResult->verification_result = 'verified';
        $verificationResult->save();
        
        return response()->json([
            'data' => [
                'issuer' => $jsonDocument->issuerName,
                'result' => 'verified'
            ],
        ], Response::HTTP_OK);
    }
}
