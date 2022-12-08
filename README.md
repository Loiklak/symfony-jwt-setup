# Symfony JWT authentication example repository

## Initial setup 

I scaffolded this application using :
- Symfony CLI 5.4
- PHP 8.1

Other requirements :
- Docker & docker compose
- `psql` interactive terminal

Here are the main steps I followed to initialize the project (you can also follow along the repo commits):

- [Create an app with symfony-cli](https://symfony.com/doc/current/setup.html)
- [Create your first route](https://symfony.com/doc/current/routing.html#creating-routes-as-attributes)
- [Setup a database and create a User entity for authentication](https://symfony.com/doc/current/doctrine.html#installing-doctrine)
- [Setup the security bundle](https://symfony.com/doc/current/security.html)
- [Setup a firewall](https://symfony.com/doc/current/security.html#the-firewall)

[Repo after going through all these steps](https://github.com/Loiklak/symfony-jwt-setup/tree/1cd647b527455cb567f5623ce624432b12ae4ea5)

## Start the app


### Installation
- Run `composer install`
- Run `php bin/console lexik:jwt:generate-keypair` that will be used to sign the JWTs
- Run `docker-composer up -d` to start the Postgres DB
- Insert a test user in DB :
  - Run `php bin/console security:hash-password MY_PASSWORD` to get a hashed version of `MY_PASSWORD`. Keep this hash for later
  - Run `psql postgresql://app:\!ChangeMe\!@127.0.0.1:5432/app` to access the DB client
  - In the DB client write: `INSERT INTO "user" (id, email, roles, password) VALUES (2, 'user@mail.com', '["ROLE_USER"]', 'THE_HASH');` while replacing `THE_HASH` with the hashed password we generated previously
  - Run `exit` to exit the DB client

### Run the app to check that it works
- Run `symfony server:start` to start the server

#### Get a working JWT access token with login route
##### Check that our test route is protected
`curl -X POST -H "Accept: application/json" http://localhost:8000/api/secure` should return a 401 error because the route is protected
##### Get an access token
- Get a JWT : `curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login_check -d '{"username":"user@mail.com","password":"MY_PASSWORD"}'`
  - It sends a POST request on the login route with the user we just created. You should receive an access token in return

##### Use the token to access the protected route
- Retry to access the route with the access token :`curl -X POST -H "Accept: application/json" http://localhost:8000/api/secure -H "Authorization: Bearer {ACCESS_TOKEN}"`
  - You shoud get the message "You have access to this restricted area!" 🥳

#### Get a working JWT access token with your refresh token

Run the below command :
```bash
COOKIE_JAR_FILE=cookiejar
echo 'Initial access token :'
curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login_check -d '{"username":"user@mail.com","password":"MY_PASSWORD"}' --cookie-jar $COOKIE_JAR_FILE
echo '\n\n>>>>>>>\n'
echo 'New access token retrieved with the refresh token :'
curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/token/refresh --cookie $COOKIE_JAR_FILE
rm $COOKIE_JAR_FILE
```
1. We send a login request and store the refresh cookie in the file `cookiejar`
2. We send another request on the refresh token route using the refresh token cookie in the `cookiejar` file
3. We indeed retrieve a new access token !

Now try to access the route with the new access token retrieved on the refresh token route : `curl -X POST -H "Accept: application/json" http://localhost:8000/api/secure -H "Authorization: Bearer {ACCESS_TOKEN}"`
  - You shoud get the message "You have access to this restricted area!" 🥳 meaning that everything works alright
