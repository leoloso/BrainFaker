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

use Brain\Faker\MonkeyWpUser;
use Brain\Monkey;

class User extends Provider
{
    public const ROLES = [
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber',
    ];

    const CAPS = [
        'administrator' => [
            'switch_themes',
            'edit_themes',
            'activate_plugins',
            'edit_plugins',
            'edit_users',
            'edit_files',
            'manage_options',
            'moderate_comments',
            'manage_categories',
            'manage_links',
            'upload_files',
            'import',
            'unfiltered_html',
            'edit_posts',
            'edit_others_posts',
            'edit_published_posts',
            'publish_posts',
            'edit_pages',
            'read',
            'level_10',
            'level_9',
            'level_8',
            'level_7',
            'level_6',
            'level_5',
            'level_4',
            'level_3',
            'level_2',
            'level_1',
            'level_0',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',
            'delete_posts',
            'delete_others_posts',
            'delete_published_posts',
            'delete_private_posts',
            'edit_private_posts',
            'read_private_posts',
            'delete_private_pages',
            'edit_private_pages',
            'read_private_pages',
            'delete_users',
            'create_users',
            'unfiltered_upload',
            'edit_dashboard',
            'update_plugins',
            'delete_plugins',
            'install_plugins',
            'update_themes',
            'install_themes',
            'update_core',
            'list_users',
            'remove_users',
            'promote_users',
            'edit_theme_options',
            'delete_themes',
            'export',
            'manage_woocommerce',
            'view_woocommerce_reports',
            'edit_product',
            'read_product',
            'delete_product',
            'edit_products',
            'edit_others_products',
            'publish_products',
            'read_private_products',
            'delete_products',
            'delete_private_products',
            'delete_published_products',
            'delete_others_products',
            'edit_private_products',
            'edit_published_products',
            'manage_product_terms',
            'edit_product_terms',
            'delete_product_terms',
            'assign_product_terms',
            'edit_shop_order',
            'read_shop_order',
            'delete_shop_order',
            'edit_shop_orders',
            'edit_others_shop_orders',
            'publish_shop_orders',
            'read_private_shop_orders',
            'delete_shop_orders',
            'delete_private_shop_orders',
            'delete_published_shop_orders',
            'delete_others_shop_orders',
            'edit_private_shop_orders',
            'edit_published_shop_orders',
            'manage_shop_order_terms',
            'edit_shop_order_terms',
            'delete_shop_order_terms',
            'assign_shop_order_terms',
            'edit_shop_coupon',
            'read_shop_coupon',
            'delete_shop_coupon',
            'edit_shop_coupons',
            'edit_others_shop_coupons',
            'publish_shop_coupons',
            'read_private_shop_coupons',
            'delete_shop_coupons',
            'delete_private_shop_coupons',
            'delete_published_shop_coupons',
            'delete_others_shop_coupons',
            'edit_private_shop_coupons',
            'edit_published_shop_coupons',
            'manage_shop_coupon_terms',
            'edit_shop_coupon_terms',
            'delete_shop_coupon_terms',
            'assign_shop_coupon_terms',
        ],
        'editor' => [
            'moderate_comments',
            'manage_categories',
            'manage_links',
            'upload_files',
            'unfiltered_html',
            'edit_posts',
            'edit_others_posts',
            'edit_published_posts',
            'publish_posts',
            'edit_pages',
            'read',
            'level_7',
            'level_6',
            'level_5',
            'level_4',
            'level_3',
            'level_2',
            'level_1',
            'level_0',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',
            'delete_posts',
            'delete_others_posts',
            'delete_published_posts',
            'delete_private_posts',
            'edit_private_posts',
            'read_private_posts',
            'delete_private_pages',
            'edit_private_pages',
            'read_private_pages',
        ],
        'author' => [
            'upload_files',
            'edit_posts',
            'edit_published_posts',
            'publish_posts',
            'read',
            'level_2',
            'level_1',
            'level_0',
            'delete_posts',
            'delete_published_posts',
        ],
        'contributor' => [
            'edit_posts',
            'read',
            'level_1',
            'level_0',
            'delete_posts',
        ],
        'subscriber' => [
            'read',
            'level_0',
        ],
    ];

    const LEVELS = [
        'administrator' => 10,
        'editor' => 7,
        'author' => 2,
        'contributor' => 1,
        'subscriber' => 0,
    ];

    /**
     * @param array $properties
     * @return \WP_User
     */
    public function __invoke(array $properties = []): \WP_User
    {
        $properties = array_change_key_case($properties, CASE_LOWER);

        $user = $this->createBaseUser($properties);

        $noPrefixKeys = [
            'user_firstname' => 'first_name',
            'user_lastname' => 'last_name',
            'user_description' => 'description',
        ];

        $login = $this->generator->userName;

        $defaults = [
            'user_login' => $login,
            'user_pass' => $this->generator->password,
            'user_nicename' => preg_replace('/[^a-z0-9-]/', '-', strtolower($login)),
            'user_email' => $this->generator->email,
            'user_url' => 'https://' . $this->generator->domainName,
            'user_registered' => $this->generator->date('Y-m-d H:i:s'),
            'user_activation_key' => rand(1, 100) > 80 ? $this->generator->password(32) : '',
            'user_status' => '0',
            'display_name' => $this->generator->name,
            'user_description' => $this->generator->sentence,
            'user_firstname' => $this->generator->firstName,
            'user_lastname' => $this->generator->lastName,
            'nickname' => $login,
            'spam' => '',
            'deleted' => '',
            'locale' => $this->generator->locale,
            'rich_editing' => $this->generator->randomElement(['true', 'false']),
            'syntax_highlighting' => $this->generator->randomElement(['true', 'false']),
            'cap_key' => 'wp_capabilities',
            'filter' => $this->generator->randomElement(['raw', null]),
        ];

        foreach ($defaults as $key => $value) {
            $hasKey = array_key_exists($key, $properties);
            $noPrefixKey = '';
            if (strpos($key, 'user_') === 0) {
                $noPrefixKey = $noPrefixKeys[$key] ?? substr($key, 5);
            }

            if (!$hasKey && $noPrefixKey) {
                $hasKey = array_key_exists($noPrefixKey, $properties);
                $hasKey and $properties[$key] = $properties[$noPrefixKey];
            }

            if (!$hasKey && $key === 'user_pass' && !empty($properties['password'])) {
                $properties[$key] = $properties['password'];
                $hasKey = true;
            }

            $field = $hasKey ? $properties[$key] : $value;
            $user->{$key} = $field;
            $get[$key] = $field;

            if ($noPrefixKey && in_array($noPrefixKey, $noPrefixKeys, true)) {
                $user->{$noPrefixKey} = $field;
            }
        }

        $toArray = [
            'ID' => $user->ID,
            'user_login' => $user->user_login,
            'user_pass' => $user->user_pass,
            'user_nicename' => $user->user_nicename,
            'user_email' => $user->user_email,
            'user_url' => $user->user_url,
            'user_registered' => $user->user_registered,
            'user_activation_key' => $user->user_activation_key,
            'user_status' => $user->user_status,
            'display_name' => $user->display_name,
        ];

        $user->data = (object)$toArray;

        $user->shouldReceive('to_array')->andReturn($toArray)->byDefault();

        $getKeys = [
            'user_description',
            'description',
            'user_firstname',
            'first_name',
            'user_lastname',
            'last_name',
            'user_level',
            'nickname',
            'spam',
            'deleted',
            'locale',
            'rich_editing',
            'syntax_highlighting',
        ];

        $get = $toArray;
        foreach ($getKeys as $key) {
            $get[$key] = $user->{$key};
        }

        $user->shouldReceive('get')
            ->andReturnUsing(
                function ($key) use ($get) {
                    if (strtoupper($key) === 'ID' && $key !== 'ID') {
                        throw new \Error('Please use `WP_User::ID` instead of `WP_User::id`.');
                    }

                    return array_key_exists($key, $get) ? (string)$get[$key] : false;
                }
            )
            ->byDefault();

        Monkey\Functions\expect('get_userdata')
            ->zeroOrMoreTimes()
            ->with(\Mockery::anyOf($user->ID, (string)$user->ID))
            ->andReturn($user);

        Monkey\Functions\expect('get_user_by')
            ->zeroOrMoreTimes()
            ->with(\Mockery::anyOf('id', 'ID'), \Mockery::anyOf($user->ID, (string)$user->ID))
            ->andReturn($user);

        Monkey\Functions\expect('get_user_by')
            ->zeroOrMoreTimes()
            ->with('slug', $user->user_nicename)
            ->andReturn($user);

        Monkey\Functions\expect('get_user_by')
            ->zeroOrMoreTimes()
            ->with('email', $user->user_email)
            ->andReturn($user);

        Monkey\Functions\expect('get_user_by')
            ->zeroOrMoreTimes()
            ->with('login', $user->user_login)
            ->andReturn($user);

        $siteId = rand(1, 100) > 80 ? $this->generator->numberBetween(1, 10) : 1;
        $user->shouldReceive('get_site_id')->andReturn($siteId)->byDefault();

        $user->shouldReceive('__monkeyMakeCurrent')
            ->zeroOrMoreTimes()
            ->withNoArgs()
            ->andReturnUsing(
                function () use (&$user) {
                    Monkey\Functions\when('get_current_user_id')->justReturn($user->ID);
                    Monkey\Functions\when('wp_get_current_user')->justReturn($user);
                    Monkey\Functions\when('current_user_can')->alias([$user, 'has_cap']);

                    return $user;
                }
            );

        return $user;
    }

    /**
     * @param array $properties
     * @return \Mockery\MockInterface|\WP_User
     * @throws \Exception
     */
    private function createBaseUser(array $properties): \Mockery\MockInterface
    {
        $id = array_key_exists('id', $properties)
            ? $properties['id']
            : $this->uniqueGenerator->numberBetween(1, 99999999);

        $role = $properties['role'] ?? null;
        $roles = $properties['roles'] ?? null;
        $level = $properties['user_level'] ?? $properties['level'] ?? null;
        $caps = $properties['allcaps']
            ?? $properties['caps']
            ?? $properties['capabilities']
            ?? null;

        $userRoles = is_array($roles) ? $roles : [];
        ($role && is_string($role)) and $userRoles[] = $role;

        if (is_numeric($level) && !$userRoles) {
            $foundLevels = [];
            $level = min(max((int)$level, 0), 10);
            foreach (self::LEVELS as $levelRole => $roleLevel) {
                ($level >= $roleLevel) and $foundLevels[$roleLevel] = $levelRole;
            }
            if ($foundLevels) {
                ksort($foundLevels, SORT_NUMERIC);
                $userRoles[] = end($foundLevels);
            }
        }

        if (!$userRoles) {
            $roleKeys = $this->generator->randomElements(array_keys(self::CAPS), 3);
            foreach ($roleKeys as $roleKey) {
                $userRoles[] = $roleKey;
            }
        }

        $hasCaps = is_array($caps);
        [$caps, $userCaps] = $this->prepareCapabilities(
            $userRoles,
            $hasCaps ? $caps : [],
            $hasCaps
        );

        if (!is_numeric($level)) {
            $level = null;
            foreach ($userRoles as $role) {
                $level = $level === null
                    ? (int)(self::LEVELS[$role] ?? 0)
                    : (int)max($level, self::LEVELS[$role] ?? 0);
            }

            $level === null and $level = $this->generator->numberBetween(0, 10);
        }

        $level = min(max((int)$level, 0), 10);

        for ($i = 0; $i <= $level; $i++) {
            array_key_exists("level_{$i}", $caps) or $caps["level_{$i}"] = true;
        }

        $user = \Mockery::mock(\WP_User::class);
        $user->ID = (int)$id;
        $user->roles = $userRoles;
        $user->caps = $userCaps;
        $user->allcaps = $caps;
        $user->user_level = (int)$level;

        $user->shouldReceive('exists')->andReturn($id > 0)->byDefault();

        $user->shouldReceive('has_cap')
            ->with(\Mockery::type('string'))
            ->andReturnUsing(
                function ($cap) use ($caps) {
                    return !empty($caps[$cap]);
                }
            )
            ->byDefault();

        Monkey\Functions\expect('user_can')
            ->zeroOrMoreTimes()
            ->with(\Mockery::anyOf($user, $id, (string)$id), \Mockery::any())
            ->andReturnUsing(
                function ($user, $cap) use ($caps, $id) {
                    return !empty($caps[$cap]);
                }
            );

        return $user;
    }

    /**
     * @param array $roles
     * @param array $allcaps
     * @param bool $hasCaps
     * @return array
     */
    private function prepareCapabilities(
        array $roles,
        array $allcaps,
        bool $hasCaps
    ): array {

        $userCaps = [];

        foreach ($roles as $role) {
            $userCaps[$role] = true;
            if (!$hasCaps && array_key_exists($role, self::CAPS)) {
                $roleCaps = array_fill_keys(self::CAPS[$role], true);
                $allcaps = array_merge($allcaps, $roleCaps);
            }
        }

        return [$hasCaps ? $allcaps : array_merge($allcaps, $userCaps), $userCaps];
    }
}
