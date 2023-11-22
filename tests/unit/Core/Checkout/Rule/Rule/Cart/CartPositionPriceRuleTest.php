<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Checkout\Rule\Rule\Cart;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Cart\Rule\CartPositionPriceRule;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Checkout\CheckoutRuleScope;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Rule\Exception\UnsupportedOperatorException;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Test\Generator;

/**
 * @internal
 */
#[Package('business-ops')]
#[CoversClass(CartPositionPriceRule::class)]
class CartPositionPriceRuleTest extends TestCase
{
    public function testRuleWithExactAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 275, 'operator' => CartPositionPriceRule::OPERATOR_EQ]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithExactAmountNotMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 0, 'operator' => CartPositionPriceRule::OPERATOR_EQ]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualExactAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 275, 'operator' => CartPositionPriceRule::OPERATOR_LTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 300, 'operator' => CartPositionPriceRule::OPERATOR_LTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithLowerThanEqualAmountNotMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 274, 'operator' => CartPositionPriceRule::OPERATOR_LTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualExactAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 275, 'operator' => CartPositionPriceRule::OPERATOR_GTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 100, 'operator' => CartPositionPriceRule::OPERATOR_GTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleWithGreaterThanEqualAmountNotMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 276, 'operator' => CartPositionPriceRule::OPERATOR_GTE]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleNotEqualAmountMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 0, 'operator' => CartPositionPriceRule::OPERATOR_NEQ]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertTrue(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    public function testRuleNotEqualAmountNotMatch(): void
    {
        $rule = (new CartPositionPriceRule())->assign(['amount' => 275, 'operator' => CartPositionPriceRule::OPERATOR_NEQ]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    #[DataProvider('unsupportedOperators')]
    public function testUnsupportedOperators(string $operator): void
    {
        $this->expectException(UnsupportedOperatorException::class);

        $rule = (new CartPositionPriceRule())->assign(['amount' => 100, 'operator' => $operator]);

        $cart = Generator::createCart();
        $context = $this->createMock(SalesChannelContext::class);
        $cart->getPrice()->assign(['positionPrice' => 275]);

        static::assertFalse(
            $rule->match(new CartRuleScope($cart, $context))
        );
    }

    /**
     * @return array<string[]>
     */
    public static function unsupportedOperators(): array
    {
        return [
            ['random'],
            [''],
        ];
    }

    public function testMatchShouldReturnFalseScopeIsNotCartRuleScope(): void
    {
        $ruleScope = new CheckoutRuleScope($this->createMock(SalesChannelContext::class));

        static::assertFalse((new CartPositionPriceRule())->match($ruleScope));
    }

    public function testGetConstraints(): void
    {
        $result = (new CartPositionPriceRule())->getConstraints();

        static::assertArrayHasKey('amount', $result);
        static::assertIsArray($result['amount']);

        static::assertArrayHasKey('operator', $result);
        static::assertIsArray($result['operator']);
    }

    public function testGetConfig(): void
    {
        $data = (new CartPositionPriceRule())->getConfig()->getData();

        static::assertSame(RuleConfig::OPERATOR_SET_NUMBER, $data['operatorSet']['operators']);
        static::assertSame('amount', $data['fields'][0]['name']);
    }
}
