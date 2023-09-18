<?php

namespace Swiftly\Http\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Http\ParameterCollection;

/**
 * @covers \Swiftly\Http\ParameterCollection
 */
final class ParameterCollectionTest extends TestCase
{
    private ParameterCollection $parameters;

    public function setUp(): void
    {
        $this->parameters = new ParameterCollection([
            'name' => 'John Smith',
            'age' => '42',
            'address' => [
                'city' => 'Exampleville',
                'postcode' => 'TE5T'
            ],
            'contact' => [
                'email' => [
                    'work' => 'john@example.com'
                ]
            ],
            'height' => '1.82'
        ]);
    }

    public function testCanCheckIfParameterExists(): void
    {
        self::assertTrue($this->parameters->has('name'));
        self::assertTrue($this->parameters->has('age'));
        self::assertTrue($this->parameters->has('address'));
        self::assertFalse($this->parameters->has('nationality'));
        self::assertFalse($this->parameters->has('telephone'));
        self::assertFalse($this->parameters->has('occupation'));
    }

    public function testCanGetParameterValue(): void
    {
        self::assertSame('John Smith', $this->parameters->get('name'));
        self::assertSame('42', $this->parameters->get('age'));
        self::assertSame([
            'city' => 'Exampleville',
            'postcode' => 'TE5T'
        ], $this->parameters->get('address'));
        self::assertNull($this->parameters->get('nationality'));
        self::assertNull($this->parameters->get('telephone'));
        self::assertNull($this->parameters->get('occupation'));
    }

    public function testCanGetParameterValueAsInt(): void
    {
        self::assertSame(42, $this->parameters->getInt('age'));
        self::assertSame(1, $this->parameters->getInt('height'));
        self::assertNull($this->parameters->getInt('shoesize'));
        self::assertNull($this->parameters->getInt('favourite_number'));
    }

    public function testCanGetParameterValueAsFloat(): void
    {
        self::assertSame(42.0, $this->parameters->getFloat('age'));
        self::assertSame(1.82, $this->parameters->getFloat('height'));
        self::assertNull($this->parameters->getFloat('weight'));
        self::assertNull($this->parameters->getFloat('eyesight'));
    }

    public function testCanGetNestedParameterValue(): void
    {
        self::assertSame('Exampleville', $this->parameters->getNested('address.city'));
        self::assertSame('TE5T', $this->parameters->getNested('address.postcode'));
        self::assertSame('john@example.com', $this->parameters->getNested('contact.email.work'));
        self::assertNull($this->parameters->getNested('name.middle'));
        self::assertNull($this->parameters->getNested('address.line1'));
        self::assertNull($this->parameters->getNested('contact.personal.email'));
    }

    public function testCanGetNestedParameterValueWithCustomDelimiter(): void
    {
        self::assertSame('Exampleville', $this->parameters->getNested('address/city', '/'));
        self::assertSame('TE5T', $this->parameters->getNested('address|postcode', '|'));
        self::assertSame('john@example.com', $this->parameters->getNested('contact@email@work', '@'));
        self::assertNull($this->parameters->getNested('name~middle', '~'));
        self::assertNull($this->parameters->getNested('address!line1', '!'));
        self::assertNull($this->parameters->getNested('contact,personal,email', ','));
    }
}
