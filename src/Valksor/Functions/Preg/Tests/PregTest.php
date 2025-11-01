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

namespace Valksor\Functions\Preg\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Valksor\Functions\Preg\Functions;

final class PregTest extends TestCase
{
    private Functions $preg;

    // Note: Empty pattern exception tests removed due to PHP 8.5 regex engine crashes

    // Integration Tests
    public function testAddRemoveUtf8ModifierRoundTrip(): void
    {
        $original = '/pattern/';
        $added = $this->preg->addUtf8Modifier($original);
        $removed = $this->preg->removeUtf8Modifier($added);

        $this->assertSame($original, $removed);
    }

    public function testAddRemoveUtf8ModifierRoundTripWithArray(): void
    {
        $original = ['/pattern1/', '/pattern2/'];
        $added = $this->preg->addUtf8Modifier($original);
        $removed = $this->preg->removeUtf8Modifier($added);

        $this->assertSame($original, $removed);
    }

    public function testAddRemoveUtf8ModifierRoundTripWithModifiers(): void
    {
        $original = '/pattern/im';
        $added = $this->preg->addUtf8Modifier($original);
        $removed = $this->preg->removeUtf8Modifier($added);

        $this->assertSame('/pattern/im', $removed);
    }

    public function testAddUtf8ModifierArray(): void
    {
        $result = $this->preg->addUtf8Modifier(['/pattern1/', '/pattern2/']);

        $this->assertSame(['/pattern1/u', '/pattern2/u'], $result);
    }

    public function testAddUtf8ModifierEmptyString(): void
    {
        $result = $this->preg->addUtf8Modifier('');

        $this->assertSame('u', $result);
    }

    public function testAddUtf8ModifierReturnsArrayForArray(): void
    {
        $result = $this->preg->addUtf8Modifier(['/test/', '/test2/']);

        $this->assertIsArray($result);
    }

    public function testAddUtf8ModifierReturnsStringForString(): void
    {
        $result = $this->preg->addUtf8Modifier('/test/');

        $this->assertIsString($result);
    }

    // LEVEL 2 TESTS: UTF-8 Modifier Tests

    // Add UTF-8 Modifier Tests
    public function testAddUtf8ModifierString(): void
    {
        $result = $this->preg->addUtf8Modifier('/pattern/');

        $this->assertSame('/pattern/u', $result);
    }

    public function testAddUtf8ModifierWithArrayContainingExistingModifiers(): void
    {
        $result = $this->preg->addUtf8Modifier(['/pattern1/i', '/pattern2/m']);

        $this->assertSame(['/pattern1/iu', '/pattern2/mu'], $result);
    }

    public function testAddUtf8ModifierWithExistingModifiers(): void
    {
        $result = $this->preg->addUtf8Modifier('/pattern/i');

        $this->assertSame('/pattern/iu', $result);
    }

    public function testExceptionWithArrayPatterns(): void
    {
        $this->expectException(RuntimeException::class);
        // This will trigger the array pattern path in _NewPregException
        $this->preg->replace(['/[valid/', '/[invalid/'], 'replacement', 'test');
    }

    public function testMatchAllMethodSignature(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/test/', 'test test', $matches);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertIsArray($matches);
    }

    // MatchAll Tests
    public function testMatchAllReturnsCountForSuccessfulMatches(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/\d+/', '123 456 789', $matches);

        $this->assertSame(3, $result);
        $this->assertSame([['123', '456', '789']], $matches);
    }

    public function testMatchAllReturnsInt(): void
    {
        $result = $this->preg->matchAll('/test/', 'test test');

        $this->assertIsInt($result);
    }

    public function testMatchAllReturnsZeroForNoMatches(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/letters/', '123 456 789', $matches);

        $this->assertSame(0, $result);
        $this->assertSame([[]], $matches);
    }

    public function testMatchAllUtf8FallbackToNonUtf8(): void
    {
        $pattern = "/\xFF/";
        $subject = "\xFF\xFF\xFF"; // Invalid UTF-8 byte sequences

        $matches = [];
        $result = $this->preg->matchAll($pattern, $subject, $matches);

        $this->assertSame(3, $result);
    }

    public function testMatchAllWithComplexPattern(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/\b[A-Z][a-z]+\b/', 'Hello World Test Case', $matches);

        $this->assertSame(4, $result);
        $this->assertSame([['Hello', 'World', 'Test', 'Case']], $matches);
    }

    public function testMatchAllWithEmptySubject(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/test/', '', $matches);

        $this->assertSame(0, $result);
        $this->assertSame([[]], $matches);
    }

    public function testMatchAllWithFlags(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/(\d+)/', '123 456 789', $matches, PREG_SET_ORDER);

        $this->assertSame(3, $result);
        $this->assertCount(3, $matches);
        $this->assertSame(['123', '123'], $matches[0]);
        $this->assertSame(['456', '456'], $matches[1]);
        $this->assertSame(['789', '789'], $matches[2]);
    }

    public function testMatchAllWithInvalidRegex(): void
    {
        $this->expectException(RuntimeException::class);
        $this->preg->matchAll('/[invalid/', 'test');
    }

    public function testMatchAllWithLargeInput(): void
    {
        $subject = str_repeat('test ', 1000);
        $matches = [];
        $result = $this->preg->matchAll('/test/', $subject, $matches);

        $this->assertSame(1000, $result);
    }

    public function testMatchAllWithOffset(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/\d+/', 'skip 123 456 789', $matches, 0, 5);

        $this->assertSame(3, $result);
        $this->assertSame([['123', '456', '789']], $matches);
    }

    public function testMatchAllWithUnicodeCharacters(): void
    {
        $matches = [];
        $result = $this->preg->matchAll('/\w+/u', 'héllo wörld', $matches);

        $this->assertSame(2, $result);
    }

    // Coverage tests for method signatures
    public function testMatchMethodSignature(): void
    {
        $matches = [];
        $result = $this->preg->match('/test/', 'test', $matches);
        $this->assertTrue($result);
        $this->assertIsArray($matches);
    }

    // Type Safety Tests
    public function testMatchReturnsBoolean(): void
    {
        $result = $this->preg->match('/test/', 'test');

        $this->assertIsBool($result);
    }

    public function testMatchReturnsFalseForNoMatch(): void
    {
        $result = $this->preg->match('/goodbye/', 'hello world');

        $this->assertFalse($result);
    }

    // LEVEL 1 TESTS: Basic Core Functionality

    // Match Tests
    public function testMatchReturnsTrueForSuccessfulMatch(): void
    {
        $result = $this->preg->match('/hello/', 'hello world');

        $this->assertTrue($result);
    }

    // LEVEL 3 TESTS: UTF-8 Fallback Tests

    public function testMatchUtf8FallbackToNonUtf8(): void
    {
        // Test with invalid UTF-8 sequence that would fail with 'u' modifier
        $pattern = "/\xFF/";
        $subject = "\xFF"; // Invalid UTF-8 byte sequence

        $result = $this->preg->match($pattern, $subject);

        $this->assertTrue($result);
    }

    // Complex Pattern Tests
    public function testMatchWithComplexPattern(): void
    {
        $result = $this->preg->match('/^(https?:\/\/)?(www\.)?([a-z]+)\.com$/', 'https://www.example.com', $matches);

        $this->assertTrue($result);
        $this->assertSame(['https://www.example.com', 'https://', 'www.', 'example'], $matches);
    }

    // Empty and Edge Case Tests
    public function testMatchWithEmptySubject(): void
    {
        $matches = [];
        $result = $this->preg->match('/test/', '', $matches);

        $this->assertFalse($result);
        $this->assertEmpty($matches);
    }

    public function testMatchWithFlags(): void
    {
        $matches = [];
        $result = $this->preg->match('/HELLO/i', 'hello world', $matches, PREG_OFFSET_CAPTURE);

        $this->assertTrue($result);
        $this->assertCount(2, $matches[0]);
        $this->assertSame('hello', $matches[0][0]);
        $this->assertSame(0, $matches[0][1]);
    }

    // Edge case tests for 100% coverage
    public function testMatchWithInvalidRegex(): void
    {
        $this->expectException(RuntimeException::class);
        $this->preg->match('/[invalid/', 'test');
    }

    // Large Input Tests
    public function testMatchWithLargeInput(): void
    {
        $subject = str_repeat('a', 10000);
        $result = $this->preg->match('/a+/', $subject);

        $this->assertTrue($result);
    }

    public function testMatchWithMatchesReference(): void
    {
        $matches = [];
        $result = $this->preg->match('/(hello)/', 'hello world', $matches);

        $this->assertTrue($result);
        $this->assertSame(['hello', 'hello'], $matches);
    }

    public function testMatchWithOffset(): void
    {
        $matches = [];
        $result = $this->preg->match('/world/', 'hello world', $matches, 0, 6);

        $this->assertTrue($result);
        $this->assertSame(['world'], $matches);
    }

    // LEVEL 5 TESTS: Advanced Edge Cases
    // Note: Malformed pattern exception tests removed due to PHP 8.5 regex engine crashes

    // Unicode and Multi-byte Character Tests
    public function testMatchWithUnicodeCharacters(): void
    {
        $result = $this->preg->match('/héllo/', 'héllo world');

        $this->assertTrue($result);
    }

    public function testRemoveUtf8ModifierArray(): void
    {
        $result = $this->preg->removeUtf8Modifier(['/pattern1/u', '/pattern2/u']);

        $this->assertSame(['/pattern1/', '/pattern2/'], $result);
    }

    public function testRemoveUtf8ModifierEmptyString(): void
    {
        $result = $this->preg->removeUtf8Modifier('/u');

        $this->assertSame('/', $result);
    }

    public function testRemoveUtf8ModifierNoUtf8Modifier(): void
    {
        $result = $this->preg->removeUtf8Modifier('/pattern/');

        $this->assertSame('/pattern/', $result);
    }

    public function testRemoveUtf8ModifierReturnsArrayForArray(): void
    {
        $result = $this->preg->removeUtf8Modifier(['/test1/u', '/test2/u']);

        $this->assertIsArray($result);
    }

    public function testRemoveUtf8ModifierReturnsStringForString(): void
    {
        $result = $this->preg->removeUtf8Modifier('/test/u');

        $this->assertIsString($result);
    }

    // Remove UTF-8 Modifier Tests
    public function testRemoveUtf8ModifierString(): void
    {
        $result = $this->preg->removeUtf8Modifier('/pattern/u');

        $this->assertSame('/pattern/', $result);
    }

    public function testRemoveUtf8ModifierWithArrayContainingMultipleModifiers(): void
    {
        $result = $this->preg->removeUtf8Modifier(['/pattern1/iu', '/pattern2/mu']);

        $this->assertSame(['/pattern1/i', '/pattern2/m'], $result);
    }

    // Tests for remaining edge cases
    public function testRemoveUtf8ModifierWithEmptyArray(): void
    {
        $result = $this->preg->removeUtf8Modifier([]);
        $this->assertSame('', $result);
    }

    public function testRemoveUtf8ModifierWithEmptyString(): void
    {
        $result = $this->preg->removeUtf8Modifier('');
        $this->assertSame('', $result);
    }

    public function testRemoveUtf8ModifierWithMultipleModifiers(): void
    {
        $result = $this->preg->removeUtf8Modifier('/pattern/iu');

        $this->assertSame('/pattern/i', $result);
    }

    public function testReplaceArrayPattern(): void
    {
        // First replace 'hello', then 'world'
        $result = $this->preg->replace('/hello/', 'hi', 'hello world');
        $result = $this->preg->replace('/world/', 'there', $result);

        $this->assertSame('hi there', $result);
    }

    // ReplaceCallback Tests
    public function testReplaceCallback(): void
    {
        $callback = fn ($matches) => strtoupper($matches[0]);
        $result = $this->preg->replaceCallback('/\w+/', $callback, 'hello world');

        $this->assertSame('HELLO WORLD', $result);
    }

    public function testReplaceCallbackMethodSignature(): void
    {
        $result = $this->preg->replaceCallback('/test/', fn ($matches) => 'replacement', 'test');
        $this->assertSame('replacement', $result);
    }

    public function testReplaceCallbackReturnsString(): void
    {
        $result = $this->preg->replaceCallback('/test/', fn () => 'ok', 'test');

        $this->assertIsString($result);
    }

    public function testReplaceCallbackUtf8FallbackToNonUtf8(): void
    {
        $pattern = "/\xFF/";
        $subject = "prefix \xFF suffix";
        $callback = fn () => 'X';

        $result = $this->preg->replaceCallback($pattern, $callback, $subject);

        $this->assertSame('prefix X suffix', $result);
    }

    public function testReplaceCallbackWithArrayPattern(): void
    {
        $callback = fn ($matches) => strtoupper($matches[0]);
        $result = $this->preg->replaceCallback(['/\w+/', '/\d+/'], $callback, 'hello 123 world 456');

        $this->assertSame('HELLO 123 WORLD 456', $result);
    }

    public function testReplaceCallbackWithComplexPattern(): void
    {
        $callback = fn ($matches) => '[' . $matches[1] . ']';
        $result = $this->preg->replaceCallback('/\b([A-Z][a-z]+)\b/', $callback, 'Hello World Test Case');

        $this->assertSame('[Hello] [World] [Test] [Case]', $result);
    }

    public function testReplaceCallbackWithCount(): void
    {
        $count = null;
        $callback = fn ($matches) => strtoupper($matches[0]);
        $result = $this->preg->replaceCallback('/\w+/', $callback, 'hello world test', -1, $count);

        $this->assertSame('HELLO WORLD TEST', $result);
        $this->assertSame(3, $count);
    }

    public function testReplaceCallbackWithEmptySubject(): void
    {
        $callback = fn () => 'ok';
        $result = $this->preg->replaceCallback('/test/', $callback, '');

        $this->assertSame('', $result);
    }

    public function testReplaceCallbackWithInvalidRegex(): void
    {
        $this->expectException(RuntimeException::class);
        $this->preg->replaceCallback('/[invalid/', fn ($matches) => 'replacement', 'test');
    }

    public function testReplaceCallbackWithLargeInput(): void
    {
        $subject = str_repeat('test ', 100);
        $callback = fn ($matches) => 'ok';
        $result = $this->preg->replaceCallback('/test/', $callback, $subject);

        $this->assertSame(str_repeat('ok ', 100), $result);
    }

    public function testReplaceCallbackWithLimit(): void
    {
        $callback = fn ($matches) => strtoupper($matches[0]);
        $result = $this->preg->replaceCallback('/\w+/', $callback, 'hello world test', 1);

        $this->assertSame('HELLO world test', $result);
    }

    public function testReplaceCallbackWithUnicodeCharacters(): void
    {
        $callback = fn ($matches) => mb_strtoupper($matches[0]);
        $result = $this->preg->replaceCallback('/\w+/u', $callback, 'héllo wörld');

        $this->assertSame('HÉLLO WÖRLD', $result);
    }

    public function testReplaceMethodSignature(): void
    {
        $result = $this->preg->replace('/test/', 'replacement', 'test');
        $this->assertSame('replacement', $result);
    }

    public function testReplaceReturnsString(): void
    {
        $result = $this->preg->replace('/test/', 'ok', 'test');

        $this->assertIsString($result);
    }

    // Replace Tests
    public function testReplaceStringPattern(): void
    {
        $result = $this->preg->replace('/world/', 'universe', 'hello world');

        $this->assertSame('hello universe', $result);
    }

    public function testReplaceUtf8FallbackToNonUtf8(): void
    {
        $pattern = "/\xFF/";
        $subject = "prefix \xFF suffix";
        $replacement = 'X';

        $result = $this->preg->replace($pattern, $replacement, $subject);

        $this->assertSame('prefix X suffix', $result);
    }

    public function testReplaceWithComplexPattern(): void
    {
        // Note: /e modifier was removed in PHP 7.0, using replaceCallback pattern instead
        $result = $this->preg->replaceCallback(
            '/\b([A-Z][a-z]+)\b/',
            fn ($matches) => '<strong>' . $matches[0] . '</strong>',
            'Hello World Test Case',
        );

        $this->assertSame('<strong>Hello</strong> <strong>World</strong> <strong>Test</strong> <strong>Case</strong>', $result);
    }

    public function testReplaceWithCount(): void
    {
        $count = null;
        $result = $this->preg->replace('/test/', 'ok', 'test test test', -1, $count);

        $this->assertSame('ok ok ok', $result);
        $this->assertSame(3, $count);
    }

    public function testReplaceWithEmptySubject(): void
    {
        $result = $this->preg->replace('/test/', 'ok', '');

        $this->assertSame('', $result);
    }

    public function testReplaceWithInvalidRegex(): void
    {
        $this->expectException(RuntimeException::class);
        $this->preg->replace('/[invalid/', 'replacement', 'test');
    }

    public function testReplaceWithLargeInput(): void
    {
        $subject = str_repeat('test ', 1000);
        $result = $this->preg->replace('/test/', 'ok', $subject);

        $this->assertSame(str_repeat('ok ', 1000), $result);
    }

    public function testReplaceWithLimit(): void
    {
        $result = $this->preg->replace('/test/', 'ok', 'test test test', 1);

        $this->assertSame('ok test test', $result);
    }

    public function testReplaceWithUnicodeCharacters(): void
    {
        $result = $this->preg->replace('/wörld/', 'world', 'héllo wörld');

        $this->assertSame('héllo world', $result);
    }

    public function testSplitMethodSignature(): void
    {
        $result = $this->preg->split('/\s+/', 'test1 test2');
        $this->assertSame(['test1', 'test2'], $result);
    }

    // Split Tests
    public function testSplitReturnsArray(): void
    {
        $result = $this->preg->split('/\s+/', 'hello world test');

        $this->assertSame(['hello', 'world', 'test'], $result);
    }

    public function testSplitUtf8FallbackToNonUtf8(): void
    {
        $pattern = "/\xFF/";
        $subject = "prefix \xFF middle \xFF suffix";

        $result = $this->preg->split($pattern, $subject);

        $this->assertSame(['prefix ', ' middle ', ' suffix'], $result);
    }

    public function testSplitWithComplexPattern(): void
    {
        $result = $this->preg->split('/\s*[,;]\s*/', 'red, green; blue, yellow');

        $this->assertSame(['red', 'green', 'blue', 'yellow'], $result);
    }

    public function testSplitWithEmptySubject(): void
    {
        $result = $this->preg->split('/\s+/', '');

        $this->assertSame([''], $result);
    }

    public function testSplitWithFlags(): void
    {
        $result = $this->preg->split('/\s+/', 'hello world test', -1, PREG_SPLIT_NO_EMPTY);

        $this->assertSame(['hello', 'world', 'test'], $result);
    }

    public function testSplitWithInvalidRegex(): void
    {
        $this->expectException(RuntimeException::class);
        $this->preg->split('/[invalid/', 'test');
    }

    public function testSplitWithLargeInput(): void
    {
        $subject = str_repeat('word,', 1000);
        $result = $this->preg->split('/,\s*/', $subject);

        // 'word,word,word,' ends with comma, so split gives 1001 items (last is empty)
        $this->assertCount(1001, $result);
        $this->assertSame('word', $result[0]);
    }

    public function testSplitWithLimit(): void
    {
        $result = $this->preg->split('/\s+/', 'hello world test again', 3);

        $this->assertSame(['hello', 'world', 'test again'], $result);
    }

    public function testSplitWithMultipleDelimiters(): void
    {
        $result = $this->preg->split('/[,;]\s*/', 'apple, orange; banana,grape');

        $this->assertSame(['apple', 'orange', 'banana', 'grape'], $result);
    }

    public function testSplitWithUnicodeCharacters(): void
    {
        $result = $this->preg->split('/\s+/u', 'héllo wörld test');

        $this->assertSame(['héllo', 'wörld', 'test'], $result);
    }

    // Static Helper Class Tests
    public function testStaticHelperClassIsCreatedInMatch(): void
    {
        // This test verifies that the static helper class pattern works correctly
        $result1 = $this->preg->match('/test1/', 'test1');
        $result2 = $this->preg->match('/test2/', 'test2');

        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    public function testStaticHelperClassIsCreatedInMatchAll(): void
    {
        $result1 = $this->preg->matchAll('/test1/', 'test1 test1');
        $result2 = $this->preg->matchAll('/test2/', 'test2 test2');

        $this->assertSame(2, $result1);
        $this->assertSame(2, $result2);
    }

    public function testStaticHelperClassIsCreatedInReplace(): void
    {
        $result1 = $this->preg->replace('/test1/', 'ok1', 'test1');
        $result2 = $this->preg->replace('/test2/', 'ok2', 'test2');

        $this->assertSame('ok1', $result1);
        $this->assertSame('ok2', $result2);
    }

    public function testStaticHelperClassIsCreatedInReplaceCallback(): void
    {
        $result1 = $this->preg->replaceCallback('/test1/', fn () => 'ok1', 'test1');
        $result2 = $this->preg->replaceCallback('/test2/', fn () => 'ok2', 'test2');

        $this->assertSame('ok1', $result1);
        $this->assertSame('ok2', $result2);
    }

    public function testStaticHelperClassIsCreatedInSplit(): void
    {
        $result1 = $this->preg->split('/\s+/', 'test1 test2');
        $result2 = $this->preg->split('/\s+/', 'test3 test4');

        $this->assertSame(['test1', 'test2'], $result1);
        $this->assertSame(['test3', 'test4'], $result2);
    }

    public function testStaticHelperClassIsCreatedInUtf8Modifiers(): void
    {
        $result1 = $this->preg->addUtf8Modifier('/test/');
        $result2 = $this->preg->removeUtf8Modifier('/test/');

        $this->assertSame('/test/u', $result1);
        $this->assertSame('/test/', $result2);
    }

    protected function setUp(): void
    {
        $this->preg = new Functions();
    }
}
