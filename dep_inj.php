<?php

// What is Dependendency Injection and why you need it?

// Suppose we have a service class interacting with the database
class BadDatabaseConnection {
    public function coolQuery(): int {
        return 5;
    }
}

class BadService {
    public function coolOperation(): int {
        $db = new BadDatabaseConnection();
        $result = $db->coolQuery() * 2;
        return $result;
    }
}

$service = new BadService();
echo $service->coolOperation() . "\n";

// Now, suppose you want to write a unit/integration test for BadService
// but you don't want to run it on a real production database, because
// it's slow and not very flexible
// You want to mock BadDatabaseConnection or redirect it to an in-memory
// database
// How can you do that? 

// Suggested solutions:
// 1. Replace the file where BadDatabaseConnection resides 
//    when running tests (bad)
// 2. Refactor BadDatabaseConnection so it knowns when it should 
//    connect to the real database or to the mock/in-memory (bad)
// 3. Refactor the code to use Dependency Injection Pattern (GREAT!)

// Dependency Injections mean that you pass external dependencies
// from outside instead of creating them inside your objects.
//
// ie. pass instances of the dependencies to the constructor and
// do not instantiate in your classes
//
// ie. new MyObject(new MyDependency());

// Refactored code below

// NB: interfaces are used to leverage polymorphism in
// a strong-typing manner, but it's not mandatory in PHP
// as you can simply omit types
interface DatabaseConnection {
    public function coolQuery(): int;
}

class RealDatabaseConnection implements DatabaseConnection {
    public function coolQuery(): int {
        // Suppose here we have the real logic here...
        return 5;
    }
}

class MockDatabaseConnection implements DatabaseConnection {
    public function coolQuery(): int {
        // Suppose here we have the mock logic here...
        return 0;
    }
}

class GoodService {
    private $_db;

    // Here we inject the database connection instead
    // of instanciating inside the class
    public function __construct(DatabaseConnection $db) {
        $this->_db = $db;
    }

    public function coolOperation(): int {
        $result = $this->_db->coolQuery() * 2;
        return $result;
    }
}

// Running the service with the real database
$realDb = new RealDatabaseConnection();
$realService = new GoodService($realDb);
echo $realService->coolOperation() . "\n";

// Running the service with the mock database with no
// additional effort
$mockDb = new MockDatabaseConnection();
$testService = new GoodService($mockDb);
echo $testService->coolOperation() . "\n";

// Advantages of Dependency Injection:
// 1. You can easily change implementations (mocking, using a new database,
//      a new algorithm, etc.)
// 2. You have better control over how many instances of each class are created
// 3. Dependencies are not hidden, you know exactly which class depends on which

// If you want to know more:
// https://stackoverflow.com/questions/6550700/inversion-of-control-vs-dependency-injection
// https://martinfowler.com/articles/injection.html#InversionOfControl