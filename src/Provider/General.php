<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the BrainFaker package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brain\Faker\Provider;

class General extends FunctionMockerProvider
{
    /**
     * @param array $properties
     * @return void
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function __invoke(array $properties = []): void
    {
        $this->mockFunctions();
    }

    /**
     * @return void
     */
    private function mockFunctions(): void
    {
        if (!$this->canMockFunctions()) {
            return;
        }

        $this->functionExpectations->mock('esc_sql')
            ->zeroOrMoreTimes()
            ->andReturnUsing($this->escSql(...));

        $this->functionExpectations->mock('get_option')
            ->zeroOrMoreTimes()
            ->andReturnUsing($this->getOption(...));

        $this->functionExpectations->mock('mysql2date')
            ->zeroOrMoreTimes()
            ->andReturnUsing($this->mySQL2Date(...));

        $this->stopMockingFunctions();
    }

    private function escSql(string|array $data): string|array
    {
        return $data;
    }

    private function getOption(string $option, mixed $default = false): mixed
    {
        if ($default !== false) {
            return $default;
        }
        return $option;
    }

    /**
     * Convert given MySQL date string into a different format.
     *
     *  - `$format` should be a PHP date format string.
     *  - 'U' and 'G' formats will return an integer sum of timestamp with timezone offset.
     *  - `$date` is expected to be local time in MySQL format (`Y-m-d H:i:s`).
     *
     * Historically UTC time could be passed to the function to produce Unix timestamp.
     *
     * If `$translate` is true then the given date and format string will
     * be passed to `wp_date()` for translation.
     *
     * @since 0.71
     *
     * @param string $format    Format of the date to return.
     * @param string $date      Date string to convert.
     * @param bool   $translate Whether the return date should be translated. Default true.
     * @return string|int|false Integer if `$format` is 'U' or 'G', string otherwise.
     *                          False on failure.
     */
    function mySQL2Date(string $format, string $date, bool $translate = true): string|int|false
    {
        if (empty($date)) {
            return false;
        }

        $datetime = date_create($date/*, wp_timezone()*/);

        if (false === $datetime) {
            return false;
        }

        // Returns a sum of timestamp with timezone offset. Ideally should never be used.
        if ('G' === $format || 'U' === $format) {
            return $datetime->getTimestamp() + $datetime->getOffset();
        }

        // if ($translate) {
        //     return wp_date($format, $datetime->getTimestamp());
        // }

        return $datetime->format($format);
    }
}
