<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\JsonDocument;
use App\Exceptions\DocumentVerificationException;
use App\Exceptions\MisformedDataException;
use App\Http\Requests\FileVerificationRequest;
use App\Models\VerificationResult;
use Symfony\Component\HttpFoundation\Response;

class DocumentVerificationController extends Controller
{
    /**
     * Verify an uploaded Document as per Accredify's requirements
     *
     * @return Response
     * @throws DocumentVerificationException
     */
    public function __invoke(FileVerificationRequest $request): Response
    {
        $validated = $request->validated();

        $userId = auth()->id();

        $verificationResult = new VerificationResult;
        $verificationResult->user_id = $userId;
        $verificationResult->file_type = 'JSON';

        try {
            $jsonFile = file_get_contents($validated['file']->getRealPath());

            $jsonFileDecoded = json_decode($jsonFile);

            $jsonDocument = new JsonDocument($jsonFileDecoded);

            if ($jsonDocument->verifyDocumentHasValidRecipient() === false) {
                throw new DocumentVerificationException('invalid_recipient');
            }

            if ($jsonDocument->verifyDocumentHasValidIssuer() === false) {
                throw new DocumentVerificationException('invalid_issuer');
            }

            if ($jsonDocument->verifyDocumentHasValidSignature() === false) {
                throw new DocumentVerificationException('invalid_signature');
            }

        } catch (DocumentVerificationException $e) {

            $verificationResult->verification_result = $e->getMessage();
            $verificationResult->save();

            return response()->json([
                'data' => [
                    'issuer' => $jsonDocument->issuerName,
                    'result' => $e->getMessage(),
                ],
            ], Response::HTTP_OK);

        } catch (MisformedDataException $e) {

            return response()->json([
                'error' => 'misformed_data',
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
                'result' => 'verified',
            ],
        ], Response::HTTP_OK);
    }
}
