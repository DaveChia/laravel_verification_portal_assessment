REFACTORING
1. Improve app\DataTransferObjects\JsonDocument's construct method so that I can use a more elegant way to check whether the uploaded JSON file object has the correct and required parameters.
2. Improve and refactor app\DataTransferObjects\Service\VerificationService's verifyDocumentHasValidRecipient method to check whether the data has valid recipient data. Instead of doing a if statement, use a better and elegant way.



IMPROVEMENTS
1. The 3 processes that will verify the JSON document should be done asynchronously so that the verification process can be scaled easily without affecting the time needed to process the documents if done synchronously. This can be done using Laravel's Queues modules: https://laravel.com/docs/8.x/queues#main-content
2. Add 2 Factor Authentication to the current authentication workflow.
3. Add error logging to the portal, for example
    1. Logging exceptions in a database table
    2. Integrate with 3rd Party APIs to send email or Slack notifications to notify us of critical exceptions that need immediate attention.
4. Implement roles and permissions authorization module to only allow users with the correct permissions to access the Document Verification functionalies.






