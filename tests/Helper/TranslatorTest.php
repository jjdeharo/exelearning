<?php

declare(strict_types=1);

namespace App\Tests\Translation\net\exelearning\Translation;

use PHPUnit\Framework\TestCase;
use App\Translation\net\exelearning\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

class TranslatorTest extends TestCase
{
    public function testTransReturnsTranslatedMessage(): void
    {
        $inner = $this->createMock(TranslatorInterface::class);
        $inner->expects($this->once())
            ->method('trans')
            ->with('hello', ['%name%' => 'User'], null, 'es')
            ->willReturn('hola');

        $translator = new Translator($inner);
        $this->assertSame('hola', $translator->trans('hello', ['%name%' => 'User'], null, 'es'));
    }

    public function testTransFallsBackToEnglish(): void
    {
        $inner = $this->createMock(TranslatorInterface::class);
        $inner->method('trans')
            ->willReturnOnConsecutiveCalls('', 'hello');

        $translator = new Translator($inner);
        $this->assertSame('hello', $translator->trans('greet'));
    }

    public function testTransReturnsIdWhenNoTranslationExists(): void
    {
        $inner = $this->createMock(TranslatorInterface::class);
        $inner->method('trans')
            ->willReturnOnConsecutiveCalls('key', 'key');

        $translator = new Translator($inner);
        $this->assertSame('key', $translator->trans('key'));
    }

    public function testParametersAreMerged(): void
    {
        $inner = $this->createMock(TranslatorInterface::class);
        $defaultParams = ['%app%' => 'MyApp'];
        $translator = new Translator($inner, $defaultParams);

        $inner->expects($this->once())
            ->method('trans')
            ->with('message', ['%app%' => 'MyApp', '%user%' => 'Alice'], null, null)
            ->willReturn('translated');

        $this->assertSame('translated', $translator->trans('message', ['%user%' => 'Alice']));
    }

    public function testSetAndGetLocale(): void
    {
        $inner = new class implements TranslatorInterface, LocaleAwareInterface {
            private string $locale = '';

            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return '';
            }

            public function getLocale(): string
            {
                return $this->locale;
            }

            public function setLocale($locale): void
            {
                $this->locale = $locale;
            }
        };

        $translator = new Translator($inner);
        $translator->setLocale('fr');
        $this->assertSame('fr', $translator->getLocale());
    }

    public function testSwitchTemporaryLocaleSuccess(): void
    {
        $inner = new class implements TranslatorInterface, LocaleAwareInterface {
            private string $locale = 'en_US';

            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return '';
            }

            public function getLocale(): string
            {
                return $this->locale;
            }

            public function setLocale($locale): void
            {
                $this->locale = $locale;
            }
        };

        $translator = new Translator($inner);
        $this->assertTrue($translator->switchTemporaryLocale('es_ES'));
        $this->assertSame('en_US', $translator->getPreviousLocale());
    }

    public function testRestorePreviousLocaleReturnsFalseWhenNoPreviousLocale(): void
    {
        $inner = $this->createMock(TranslatorInterface::class);
        $translator = new Translator($inner);
        $this->assertFalse($translator->restorePreviousLocale());
    }

    public function testRestorePreviousLocaleSuccess(): void
    {
        $inner = new class implements TranslatorInterface, LocaleAwareInterface {
            private string $locale = 'fr_FR';

            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return '';
            }

            public function getLocale(): string
            {
                return $this->locale;
            }

            public function setLocale($locale): void
            {
                $this->locale = $locale;
            }
        };

        $translator = new Translator($inner);
        $ref = new \ReflectionClass($translator);
        $prop = $ref->getProperty('previousLocale');
        $prop->setAccessible(true);
        $prop->setValue($translator, 'en_US');

        $this->assertTrue($translator->restorePreviousLocale());
        $this->assertNull($translator->getPreviousLocale());
    }

    public function testGetCatalogue(): void
    {
        $catalogue = $this->createMock(MessageCatalogueInterface::class);

        $inner = new class($catalogue) implements TranslatorInterface, TranslatorBagInterface {
            private MessageCatalogueInterface $catalogue;

            public function __construct(MessageCatalogueInterface $catalogue)
            {
                $this->catalogue = $catalogue;
            }

            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return '';
            }

            public function getLocale(): string
            {
                return '';
            }

            public function getCatalogue(?string $locale = null): MessageCatalogueInterface
            {
                return $this->catalogue;
            }

            public function getCatalogues(): array
            {
                return [$this->catalogue];
            }
        };

        $translator = new Translator($inner);
        $this->assertSame($catalogue, $translator->getCatalogue('de'));
    }
}
