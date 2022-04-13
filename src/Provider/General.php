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

        $this->functionExpectations->mock('get_option')
            ->zeroOrMoreTimes()
            ->andReturnUsing(
                function ($option) { // phpcs:ignore
                    return $option;
                }
            );

        $this->stopMockingFunctions();
    }
}
