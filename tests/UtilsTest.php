<?php
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testCamelToSnake()
    {
        $this->assertSame('camel_to_snake', camel_to_snake('camelToSnake'));
        $this->assertSame('camel_to_snake_case', camel_to_snake('camelToSnakeCase'));
        $this->assertSame('hello_world_123', camel_to_snake('helloWorld123'));
    }

    public function testSnakeToCamel()
    {
        $this->assertSame('camelToSnake', snake_to_camel('camel_to_snake'));
        $this->assertSame('camelToSnakeCase', snake_to_camel('camel_to_snake_case'));
        $this->assertSame('helloWorld123', snake_to_camel('hello_world_123'));
    }
}
