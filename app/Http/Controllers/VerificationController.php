<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\JsonDocument;
use App\Models\VerificationResult;
use App\Http\Requests\FileVerificationRequest;
use App\Exceptions\DocumentVerificationException;
use App\Exceptions\MisformedDataException;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function verify(FileVerificationRequest $request)
    {
        $validated = $request->validated();

        $userId = auth()->id();

        $verificationResult = new VerificationResult;
        $verificationResult->user_id = $userId;
        $verificationResult->file_type = 'JSON';

        try {
            $jsonDocument = new JsonDocument($validated['file']);
       
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

        } catch (MisformedDataException $e) {

            return response()->json([
                'error' => 'misformed_data'
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'unexpected_error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
