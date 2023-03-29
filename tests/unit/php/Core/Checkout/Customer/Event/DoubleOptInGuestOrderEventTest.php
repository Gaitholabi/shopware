<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Checkout\Customer\Event;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\DoubleOptInGuestOrderEvent;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Flow\Dispatching\Storer\ScalarValuesStorer;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 *
 * @covers \Shopware\Core\Checkout\Customer\Event\DoubleOptInGuestOrderEvent
 */
class DoubleOptInGuestOrderEventTest extends TestCase
{
    public function testScalarValuesCorrectly(): void
    {
        $event = new DoubleOptInGuestOrderEvent(
            $this->createMock(CustomerEntity::class),
            $this->createMock(SalesChannelContext::class),
            'my-confirm-url'
        );

        $storer = new ScalarValuesStorer();

        $stored = $storer->store($event, []);

        $flow = new StorableFlow('foo', Context::createDefaultContext(), $stored);

        $storer->restore($flow);

        static::assertArrayHasKey('confirmUrl', $flow->data());
        static::assertEquals('my-confirm-url', $flow->data()['confirmUrl']);
    }
}