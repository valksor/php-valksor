<?php declare(strict_types = 1);

/*
 * This file is part of the Valksor package.
 *
 * (c) Davis Zalitis (k0d3r1s)
 * (c) SIA Valksor <packages@valksor.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valksor\Functions\Queue\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Valksor\Functions\Queue\Queue;

use function count;

final class QueueTest extends TestCase
{
    public function testClearAndReuse(): void
    {
        $queue = new Queue([1, 2, 3]);
        $queue->clear();
        $queue->push(4);
        $this->assertSame(1, $queue->count());
        $this->assertSame(4, $queue->peek());
    }

    public function testClearOnEmptyQueue(): void
    {
        $queue = new Queue();
        $queue->clear();
        $this->assertTrue($queue->isEmpty());
    }

    // clear() tests

    public function testClearRemovesAllItems(): void
    {
        $queue = new Queue([1, 2, 3, 4, 5]);
        $queue->clear();
        $this->assertTrue($queue->isEmpty());
        $this->assertSame(0, $queue->count());
    }
    // Constructor tests

    public function testConstructorWithEmptyArray(): void
    {
        $queue = new Queue();
        $this->assertTrue($queue->isEmpty());
        $this->assertSame(0, $queue->count());
    }

    public function testConstructorWithInitialItems(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertFalse($queue->isEmpty());
        $this->assertSame(3, $queue->count());
        $this->assertSame(1, $queue->peek());
    }

    public function testContainsAfterPop(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertTrue($queue->contains(1));
        $queue->pop();
        $this->assertFalse($queue->contains(1));
        $this->assertTrue($queue->contains(2));
    }

    public function testContainsReturnsFalseForNonExistingItem(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertFalse($queue->contains(4));
    }

    // contains() tests

    public function testContainsReturnsTrueForExistingItem(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertTrue($queue->contains(2));
    }

    public function testContainsUsesStrictComparison(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertFalse($queue->contains('1'));
        $this->assertTrue($queue->contains(1));
    }

    public function testContainsWithObjects(): void
    {
        $obj1 = new stdClass();
        $obj1->id = 1;
        $obj2 = new stdClass();
        $obj2->id = 2;
        $obj3 = new stdClass();
        $obj3->id = 1;

        $queue = new Queue([$obj1, $obj2]);
        $this->assertTrue($queue->contains($obj1));
        $this->assertFalse($queue->contains($obj3)); // Different object instance
    }

    public function testCountAfterOperations(): void
    {
        $queue = new Queue([1, 2]);
        $this->assertSame(2, $queue->count());
        $queue->push(3);
        $this->assertSame(3, $queue->count());
        $queue->pop();
        $this->assertSame(2, $queue->count());
    }

    // count() tests

    public function testCountReturnsNumberOfItems(): void
    {
        $queue = new Queue([1, 2, 3, 4, 5]);
        $this->assertSame(5, $queue->count());
    }

    public function testCountReturnsZeroForEmptyQueue(): void
    {
        $queue = new Queue();
        $this->assertSame(0, $queue->count());
    }

    public function testCountableInterface(): void
    {
        $queue = new Queue([1, 2, 3]);
        $this->assertCount(3, $queue);
    }

    public function testIsEmptyAfterClear(): void
    {
        $queue = new Queue([1, 2, 3]);
        $queue->clear();
        $this->assertTrue($queue->isEmpty());
    }

    public function testIsEmptyAfterPopAll(): void
    {
        $queue = new Queue([1]);
        $queue->pop();
        $this->assertTrue($queue->isEmpty());
    }

    public function testIsEmptyReturnsFalseForNonEmptyQueue(): void
    {
        $queue = new Queue([1]);
        $this->assertFalse($queue->isEmpty());
    }

    // isEmpty() tests

    public function testIsEmptyReturnsTrueForEmptyQueue(): void
    {
        $queue = new Queue();
        $this->assertTrue($queue->isEmpty());
    }

    public function testLargeQueue(): void
    {
        $items = range(1, 1000);
        $queue = new Queue($items);
        $this->assertSame(1000, $queue->count());
        $this->assertSame(1, $queue->peek());

        for ($i = 1; $i <= 500; $i++) {
            $this->assertSame($i, $queue->pop());
        }

        $this->assertSame(500, $queue->count());
        $this->assertSame(501, $queue->peek());
    }

    // Complex scenarios and edge cases

    public function testMultipleOperations(): void
    {
        $queue = new Queue();
        $this->assertTrue($queue->isEmpty());

        $queue->push(1);
        $queue->push(2);
        $this->assertSame(2, $queue->count());

        $this->assertSame(1, $queue->pop());
        $queue->push(3);
        $this->assertSame(2, $queue->count());

        $this->assertSame(2, $queue->peek());
        $this->assertSame(2, $queue->pop());
        $this->assertSame(3, $queue->pop());
        $this->assertTrue($queue->isEmpty());
    }

    public function testPeekAfterPartialPop(): void
    {
        $queue = new Queue(['a', 'b', 'c']);
        $queue->pop();
        $this->assertSame('b', $queue->peek());
        $queue->pop();
        $this->assertSame('c', $queue->peek());
    }

    public function testPeekMultipleTimes(): void
    {
        $queue = new Queue(['test']);
        $this->assertSame('test', $queue->peek());
        $this->assertSame('test', $queue->peek());
        $this->assertSame(1, $queue->count());
    }

    public function testPeekOnEmptyQueueReturnsFalse(): void
    {
        $queue = new Queue();
        $this->assertFalse($queue->peek());
    }

    // peek() tests

    public function testPeekReturnsFirstItemWithoutRemoving(): void
    {
        $queue = new Queue([1, 2, 3]);
        $item = $queue->peek();
        $this->assertSame(1, $item);
        $this->assertSame(3, $queue->count());
    }

    // pop() tests

    public function testPopRemovesAndReturnsFirstItem(): void
    {
        $queue = new Queue([1, 2, 3]);
        $item = $queue->pop();
        $this->assertSame(1, $item);
        $this->assertSame(2, $queue->count());
    }

    public function testPopReturnsFalseWhenQueueIsEmpty(): void
    {
        $queue = new Queue();
        $this->assertFalse($queue->pop());
    }

    public function testPopReturnsItemsInFIFOOrder(): void
    {
        $queue = new Queue(['first', 'second', 'third']);
        $this->assertSame('first', $queue->pop());
        $this->assertSame('second', $queue->pop());
        $this->assertSame('third', $queue->pop());
    }

    public function testPopUntilEmpty(): void
    {
        $queue = new Queue([1, 2]);
        $queue->pop();
        $queue->pop();
        $this->assertTrue($queue->isEmpty());
        $this->assertFalse($queue->pop());
    }

    // push() tests

    public function testPushAddsItemToQueue(): void
    {
        $queue = new Queue();
        $queue->push(1);
        $this->assertSame(1, $queue->count());
        $this->assertTrue($queue->contains(1));
    }

    public function testPushAfterPoppingAll(): void
    {
        $queue = new Queue([1, 2]);
        $queue->pop();
        $queue->pop();
        $this->assertTrue($queue->isEmpty());
        $queue->push(3);
        $this->assertFalse($queue->isEmpty());
        $this->assertSame(3, $queue->peek());
    }

    public function testPushIgnoresNullItems(): void
    {
        $queue = new Queue();
        $queue->push(null);
        $this->assertTrue($queue->isEmpty());
        $this->assertSame(0, $queue->count());
    }

    public function testPushMultipleItems(): void
    {
        $queue = new Queue();
        $queue->push(1);
        $queue->push(2);
        $queue->push(3);
        $this->assertSame(3, $queue->count());
    }

    public function testPushNullDoesNotAddToQueue(): void
    {
        $queue = new Queue([1, 2]);
        $queue->push(null);
        $this->assertSame(2, $queue->count());
        $this->assertFalse($queue->contains(null));
    }

    public function testQueueMaintainsFIFOWithMixedOperations(): void
    {
        $queue = new Queue();
        $queue->push('A');
        $queue->push('B');
        $this->assertSame('A', $queue->pop());
        $queue->push('C');
        $queue->push('D');
        $this->assertSame('B', $queue->pop());
        $this->assertSame('C', $queue->pop());
        $this->assertSame('D', $queue->pop());
        $this->assertTrue($queue->isEmpty());
    }

    public function testQueueWithArrays(): void
    {
        $queue = new Queue([[1, 2], [3, 4], [5, 6]]);
        $this->assertSame(3, $queue->count());
        $this->assertSame([1, 2], $queue->pop());
        $this->assertSame([3, 4], $queue->pop());
    }

    public function testQueueWithDifferentTypes(): void
    {
        $queue = new Queue([1, 'string', 3.14, true, []]);
        $this->assertSame(5, $queue->count());
        $this->assertSame(1, $queue->pop());
        $this->assertSame('string', $queue->pop());
        $this->assertSame(3.14, $queue->pop());
        $this->assertTrue($queue->pop());
        $this->assertSame([], $queue->pop());
    }

    public function testQueueWithObjects(): void
    {
        $obj1 = new stdClass();
        $obj1->name = 'first';
        $obj2 = new stdClass();
        $obj2->name = 'second';

        $queue = new Queue([$obj1, $obj2]);
        $this->assertSame(2, $queue->count());

        $popped = $queue->pop();
        $this->assertSame('first', $popped->name);
        $this->assertSame(1, $queue->count());
    }

    public function testQueueWithZeroAndFalse(): void
    {
        $queue = new Queue([0, false, '']);
        $this->assertSame(3, $queue->count());
        $this->assertSame(0, $queue->pop());
        $this->assertFalse($queue->pop());
        $this->assertSame('', $queue->pop());
    }
}
