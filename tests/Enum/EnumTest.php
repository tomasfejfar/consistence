<?php

namespace Consistence\Enum;

use Consistence\Type\ArrayType\ArrayType;

class EnumTest extends \Consistence\TestCase
{

	public function testGet()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);
		$this->assertInstanceOf(StatusEnum::class, $review);
	}

	public function testGetValue()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);
		$this->assertSame(StatusEnum::REVIEW, $review->getValue());
	}

	public function testSameInstances()
	{
		$review1 = StatusEnum::get(StatusEnum::REVIEW);
		$review2 = StatusEnum::get(StatusEnum::REVIEW);

		$this->assertSame($review1, $review2);
	}

	public function testDifferentInstances()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);
		$draft = StatusEnum::get(StatusEnum::DRAFT);

		$this->assertNotSame($review, $draft);
	}

	public function testEquals()
	{
		$review1 = StatusEnum::get(StatusEnum::REVIEW);
		$review2 = StatusEnum::get(StatusEnum::REVIEW);

		$this->assertTrue($review1->equals($review2));
	}

	public function testNotEquals()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);
		$draft = StatusEnum::get(StatusEnum::DRAFT);

		$this->assertFalse($review->equals($draft));
	}

	public function testEqualsValue()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);

		$this->assertTrue($review->equalsValue(StatusEnum::REVIEW));
	}

	public function testNotEqualsValue()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);

		$this->assertFalse($review->equalsValue(StatusEnum::DRAFT));
	}

	public function testGetAvailableValues()
	{
		$this->assertEquals([
			'DRAFT' => StatusEnum::DRAFT,
			'REVIEW' => StatusEnum::REVIEW,
			'PUBLISHED' => StatusEnum::PUBLISHED,
		], StatusEnum::getAvailableValues());
	}

	public function testIsValidValue()
	{
		$this->assertTrue(StatusEnum::isValidValue(StatusEnum::DRAFT));
	}

	public function testNotValidValue()
	{
		$this->assertFalse(StatusEnum::isValidValue(0));
	}

	public function testInvalidEnumValue()
	{
		try {
			StatusEnum::get(0);
			$this->fail();
		} catch (\Consistence\Enum\InvalidEnumValueException $e) {
			$this->assertSame(0, $e->getValue());
			$this->assertEquals([
				'DRAFT' => StatusEnum::DRAFT,
				'REVIEW' => StatusEnum::REVIEW,
				'PUBLISHED' => StatusEnum::PUBLISHED,
			], $e->getAvailableValues());
		}
	}

	public function testCheckValue()
	{
		StatusEnum::checkValue(StatusEnum::DRAFT);
		$this->ok();
	}


	public function testCheckInvalidValue()
	{
		try {
			StatusEnum::checkValue('foo');
			$this->fail();
		} catch (\Consistence\Enum\InvalidEnumValueException $e) {
			$this->assertSame('foo', $e->getValue());
			$this->assertEquals([
				'DRAFT' => StatusEnum::DRAFT,
				'REVIEW' => StatusEnum::REVIEW,
				'PUBLISHED' => StatusEnum::PUBLISHED,
			], $e->getAvailableValues());
		}
	}

	public function testComparingDifferentEnums()
	{
		$review = StatusEnum::get(StatusEnum::REVIEW);
		$foo = FooEnum::get(FooEnum::FOO);
		try {
			$review->equals($foo);

			$this->fail();
		} catch (\Consistence\Enum\OperationSupportedOnlyForSameEnumException $e) {
			$this->assertSame($review, $e->getExpected());
			$this->assertSame($foo, $e->getGiven());
		}
	}

	public function testAvailableValuesFooEnum()
	{
		$this->assertEquals([
			'FOO' => FooEnum::FOO,
		], FooEnum::getAvailableValues());
	}

	public function testIgnoredConstant()
	{
		try {
			StatusEnum::get(StatusEnum::BAR);
			$this->fail();
		} catch (\Consistence\Enum\InvalidEnumValueException $e) {
			$this->assertSame(StatusEnum::BAR, $e->getValue());
			$this->assertEquals([
				'DRAFT' => StatusEnum::DRAFT,
				'REVIEW' => StatusEnum::REVIEW,
				'PUBLISHED' => StatusEnum::PUBLISHED,
			], $e->getAvailableValues());
		}
	}

	/**
	 * @return mixed[][]
	 */
	public function typesProvider()
	{
		return ArrayType::mapValuesByCallback(TypeEnum::getAvailableValues(), function ($value) {
			return [$value];
		});
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param mixed $value
	 */
	public function testTypes($value)
	{
		$enum = TypeEnum::get($value);
		$this->assertInstanceOf(TypeEnum::class, $enum);
		$this->assertSame($enum->getValue(), $value);
	}

	public function testDuplicateSpecifiedValues()
	{
		try {
			DuplicateValuesEnum::get(DuplicateValuesEnum::BAZ);
			$this->fail();
		} catch (\Consistence\Enum\DuplicateValueSpecifiedException $e) {
			$this->assertSame(DuplicateValuesEnum::FOO, $e->getValue());
			$this->assertSame(DuplicateValuesEnum::class, $e->getClass());
		}
	}

}
