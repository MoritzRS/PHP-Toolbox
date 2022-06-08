<?php

namespace Tools\Test;

use Exception;

class Test {
    /**
     * Test Description
     * @var string
     */
    private string $description;

    /**
     * Methods to run before all Tests
     * @var array<callable>
     */
    private array $before = [];

    /**
     * Methods to run after all Tests
     * @var array<callable>
     */
    private array $after = [];

    /**
     * Test Cases
     * @var array
     */
    private array $tests = [];

    /**
     * Creates a new Test object
     * @param string $description Test Description, usualy classname of tested class
     */
    public function __construct(string $description) {
        $this->description = "[{$description}]";
    }

    /**
     * Registers a method to run before every single test case
     * @param callable $callback Method to register 
     */
    public function beforeAll(callable $callback) {
        array_push($this->before, $callback);
    }

    /**
     * Registers a method to run after every single test case
     * @param callable $callback Method to register 
     */
    public function afterAll(callable $callback) {
        array_push($this->after, $callback);
    }

    /**
     * Registers a new test case
     * @param string $description Test case description
     * @param callable $callback Test method
     */
    public function test(string $description, callable $callback) {
        array_push($this->tests, ["description" => $description, "callback" => $callback]);
    }

    /**
     * Runs the test
     */
    public function run() {
        echo "---\n";
        echo " | \e[0;30;47m {$this->description} \e[0m\n";

        foreach ($this->tests as $test) {
            try {
                foreach ($this->before as $before) $before();
                $test["callback"]();
                foreach ($this->after as $after) $after();
                echo " | \e[0;30;42m PASS \e[1;37;40m {$test["description"]} \e[0m\n";
            } catch (Exception $e) {
                echo " | \e[1;37;41m FAIL \e[1;37;40m {$test["description"]} \e[0m\n";
                echo " | \e[1;37;41m {$e->getMessage()} \e[0m\n";
                echo "\n";
                exit(255);
            }
        }
        echo "---\n";
        echo "\n";
    }
}
