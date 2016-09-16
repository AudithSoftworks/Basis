<?php namespace App\Tests\Misc;

use App\Tests\IlluminateTestCase;

class LocalizedRoutesTest extends IlluminateTestCase
{
    /**
     * This is used to memorize password reset token for tests.
     *
     * @var string
     */
    public static $passwordResetToken;

    /**
     * Create an app instance for facades to work + Migrations.
     */
    public static function setUpBeforeClass()
    {
        putenv('APP_ENV=testing');
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    }

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
        \Lang::setLocale($locale);
        $this->visit('/' . $locale);
        $this->seeStatusCode(200);
        $this->see(trans_choice('auth.headings.welcome', \App\Http\Controllers\Controller::TRANSLATION_TAG_GUEST_USER, ['name' => trans('auth.guest')]));

        $this->click(trans('auth.buttons.login'));
        $this->seeStatusCode(200);
        $this->seePageIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.login.')));

        $this->click(trans('auth.buttons.password'));
        $this->seeStatusCode(200);
        $this->seePageIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.password.') . '/' . trans('routes.password.email')));

        $this->click(trans('auth.buttons.login'));
        $this->seeStatusCode(200);
        $this->click(trans('auth.buttons.register'));
        $this->seeStatusCode(200);
        $this->seePageIs('/' . $locale . '/' . $this->urlDecodeCompatibleUnicodeMultibyteSequence(trans('routes.register.')));
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
