# Transcational Email Microservice #

This application represents a Laravel-based microservice that sends emails in an asynchronous manner. 
Sending can be triggered by either an API request or a CLI command. The delivery of the emails is handled by external providers.

Currently, the app supports two email delivery services (Mailjet and Sendgrid). 
## Installation instructions (requires Docker)
1. Clone the repository
2. `cd` into the recently cloned folder
3. Make a copy of `.env.example` named `.env`.
4. Fill in the desired configuration under `#Mail delivery services` section.
5. Build and start the app by running the command:
```
docker compose up -d --build;
```
6. After the build process has finished without errors, run the following commands to install composer dependencies and  create the necessary database tables:
```
docker exec -it tem-php composer install;
docker exec -it tem-php php artisan migrate;  
```
7. The app also has unit tests for critical components. You may run them using the following command:
```
docker exec -it tem-php ./vendor/bin/phpunit
```


## Option 1: Using API endpoint to send emails

The endpoint for sending emails is located at `http://localhost:8080/api/send-mail`

It expects a `POST` request with data being in `JSON` format.

Sample HTTP request:
```
POST /api/send-mail HTTP/1.1
Host: localhost:8080
Content-Type: application/json
Content-Length: 141

{
    "recipients": ["first@gmail.com", "second@gmail.com"],
    "subject": "This is the subject",
    "body": "This is the body"
}
```

## Option 2: Using CLI command to send emails
The Laravel command for sending emails is called `mail:send`.

It expects a list of options, namely `--recipient` (multiple allowed), `--subject` and `--body`.
You can also run the command directly inside the Docker container by using:

```
docker exec -it tem-php php artisan mail:send --recipient=first@gmail.com --recipient=second@gmail.com --subject="This is the subject" --body="This is the body"
```

### Important note!
Executing the commands above will only add the emails to a job queue. To trigger the actual sending of the emails, start the worker process as follows:

```
docker exec -it tem-php php artisan queue:work -v
```