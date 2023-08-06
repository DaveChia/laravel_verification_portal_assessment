INSTRUCTIONS TO ASSESS THIS PROJECT
Requirements:
PHP, MySQL, Composer, Laravel should be installed and setup on your machines.

--Repository Setup
1. Clone the repository from https://github.com/DaveChia/laravel_verification_portal_assessment to your machine.
2. Using cmd/terminal, depending on whether you are using a Windows or MAC machine, navigate to the cloned repository. Example of the location of the cloned
repository: C:\users\dave\laravel_verification_portal_assessment.  Checkout the 'main' branch of the repository.
3. In cmd/terminal, run: composer install
4. Ensure MySQL is setup in your machine. A 'verification' database should be set up in MySQL, this will be the database we will use for this repostitory.
5. Rename .env.example to .env, this will be our .env file for this repository.
6. In cmd/terminal, run: php artisan migrate
   This will create all the tables we need in the verification database
7. In cmd/terminal, run: php artisan serve
   This will start the Laravel development server
8. The repository is now ready.


--Assessment
NOTE: All APIs should have 'Accept: application/json' set in the request's header

Refer to the API Documentations for details.

1. Register for an account using the API: http://127.0.0.1:8000/api/auth/register.
    NOTE: The password must fulfil the following criteria
            Must be more or equal to 8 characters
            Must have at least one lowercase letter (a-z)
            Must have at least one uppercase letter (A-Z)
            Must have at least one number (0-9)
            Must have at least one special character ( @$!%*#?& )

    Example of a successful response:
    {
        "result": true,
        "message": "User Created Successfully"
    }
2. Using the email and password credentials chosen during account registration, login via this API: http://127.0.0.1:8000/api/auth/login
    Example of a successful response:
    {
        "result": true,
        "message": "User Logged In Successfully",
        "token": "9|45bU5XJ161zRSWhilLsfIAoppMdD6Rv5gHqH0lpB" // This will be the Beaer token to be used for API authentications
    }
3. Send the JSON file to be verified using this API: http://127.0.0.1:8000/api/verify
    Note, use the Bearer token as the 'Authorization' header in this API request so that we can be authorized to use this API

    Response Examples:
    1. Verified Document
        {
            "data": {
                "issuer": "Accredify",
                "result": "verified"
            }
        }
    2. Unverified Document with invalid_recipient error code
        {
            "data": {
                "issuer": "Accredify",
                "result": "invalid_recipient"
            }
        }
    3. Unverified Document with invalid_issuer error code
        {
            "data": {
                "issuer": "Accredify",
                "result": "invalid_issuer"
            }
        }
    4. Unverified Document with invalid_signature error code
        {
            "data": {
                "issuer": "Accredify",
                "result": "invalid_signature"
            }
        }
    5. Error response if a JSON file with invalid data is uploaded
        {
            "error": "misformed_data"
        }





