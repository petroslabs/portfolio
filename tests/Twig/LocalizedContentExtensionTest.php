<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\LocalizedContentExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LocalizedContentExtensionTest extends TestCase
{
    public function testReturnsValueForCurrentLocale(): void
    {
        $extension = $this->extensionForLocale('en');

        self::assertSame('Hello', $extension->localize(['fr' => 'Bonjour', 'en' => 'Hello']));
    }

    public function testFallsBackToFrenchWhenCurrentLocaleIsMissing(): void
    {
        $extension = $this->extensionForLocale('en');

        self::assertSame('Bonjour', $extension->localize(['fr' => 'Bonjour']));
    }

    public function testFallsBackToFirstValueWhenNeitherLocaleNorFrenchIsPresent(): void
    {
        $extension = $this->extensionForLocale('en');

        self::assertSame('Hallo', $extension->localize(['de' => 'Hallo']));
    }

    public function testPlainStringIsReturnedUnchanged(): void
    {
        $extension = $this->extensionForLocale('en');

        self::assertSame('Symfony', $extension->localize('Symfony'));
    }

    public function testNullIsReturnedUnchanged(): void
    {
        $extension = $this->extensionForLocale('en');

        self::assertNull($extension->localize(null));
    }

    public function testDefaultsToFrenchWhenThereIsNoCurrentRequest(): void
    {
        $extension = new LocalizedContentExtension(new RequestStack());

        self::assertSame('Bonjour', $extension->localize(['fr' => 'Bonjour', 'en' => 'Hello']));
    }

    private function extensionForLocale(string $locale): LocalizedContentExtension
    {
        $requestStack = new RequestStack();
        $request = Request::create('/');
        $request->setLocale($locale);
        $requestStack->push($request);

        return new LocalizedContentExtension($requestStack);
    }
}
