<?php namespace App\Tests\Browser;

use App\Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LocalizedRoutesTest extends DuskTestCase
{
    public function data_testLocalizedHyperlinksInAuthenticationPages()
    {
        return [
            ['locale' => 'tr'],
            ['locale' => 'az']
        ];
    }

    /**
     * @dataProvider data_testLocalizedHyperlinksInAuthenticationPages
     *
     * @param string $locale
     */
    public function testHyperlinksInAuthenticationPages($locale)
    {
        $this->app->make('translator')->setLocale($locale);

        $this->browse(function (Browser $browser) use ($locale) {
            $browser->visit('/' . $locale);
            $browser->assertSourceHas(trans_choice('auth.headings.welcome', \App\Http\Controllers\Controller::TRANSLATION_TAG_GUEST_USER, ['name' => trans('auth.guest')]));

            $browser->clickLink(trans('auth.buttons.login'));
            $browser->waitForText(trans('auth.headings.login'));
            $browser->assertPathIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.login.')));

            $browser->clickLink(trans('auth.buttons.password'));
            $browser->waitForText(trans('auth.headings.password'));
            $browser->assertPathIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.password.') . '/' . trans('routes.password.email')));

            $browser->clickLink(trans('auth.buttons.login'));
            $browser->waitForText(trans('auth.headings.login'));
            $browser->clickLink(trans('auth.buttons.register'));
            $browser->waitForText(trans('auth.headings.register'));
            $browser->assertPathIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.register.')));
        });
    }

    /**
     * Converts Unicode sequence to urldecode()-compatible hexadecimal format (e.g. %XX%YY format).
     *
     * @param $string
     *
     * @return string
     */
    private function urlDecodeCompatibleUnicodeMultibyteSequence($string)
    {
        /*
         * @see http://en.wikipedia.org/wiki/UTF-8#Description
         */
        # Four-byte chars
        $string = preg_replace_callback(
            "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/",
            function ($m) {
                return '%' . strtoupper(base_convert(ord($m[1]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[2]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[3]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[4]), 10, 16));
            },
            $string
        );

        # Three-byte chars
        $string = preg_replace_callback(
            "/([\340-\357])([\200-\277])([\200-\277])/",
            function ($m) {
                return '%' . strtoupper(base_convert(ord($m[1]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[2]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[3]), 10, 16));
            },
            $string
        );

        # Two-byte chars
        $string = preg_replace_callback(
            "/([\300-\337])([\200-\277])/",
            function ($m) {
                return '%' . strtoupper(base_convert(ord($m[1]), 10, 16)) . '%' . strtoupper(base_convert(ord($m[2]), 10, 16));
            },
            $string
        );

        return $string;
    }
}
