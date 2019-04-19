# JWT - Lite implementation

This is a lite implementation of the **JWT standard** for **PHP** incluiding RSA key generation and basic user management. It only supports **RS256** algorithm and **ias** and **exp** properties.


## Usage

Uses **Dependency Injection** to get access to database for RSA keys and user validation. It can be overwritten to provide custom functionality.

    $userRepository = new UserRepository($connection);  
    $userValidator = new UserValidator($userRepository);  
    $rsaRepository = new RSARepository($connection);  
      
    $jwt = new JWT($userValidator, $rsaRepository);

In order to **create a token** for a user:

    $token = $jwt->encode($rsaName, $username, $password, $exp);

**Validate a token:**

    $userId = $jwt->decode($token, $rsaName);

## Database

**Initialization:**

    $dbInitialization = new DBInitialization();
    $dbInitialization->createRSATable($connection);
    $dbInitialization->createRsaKey($connection, $rsaName);

**Users can be create:**

    $dbInitialization = new DBInitialization();
    $dbInitialization->createUserTable($connection);
    $dbInitialization->createUser($connection, $username, $password, $isActive);

Exception management must be added to this examples cause it throws exceptions.


## RSA generation

This functionality is provided via **RSAGenerator** class. It generates a private/public pair when instantiated which must be persisted.

## Tests

**phpunit-settings.php** must be configured to be able to run tests.

## License

[MIT License](https://opensource.org/licenses/MIT)

## Authors

 - David Moreno Cortina