<?php

return [
    'categories' => [
        'label' => '分类',
        'plural_label' => '分类',
        'navigation_label' => '分类',
        'navigation_group' => '内容管理',
        'stats_description' => '总分类数',
    ],
    'products' => [
        'label' => '产品',
        'plural_label' => '产品',
        'navigation_label' => '产品',
        'navigation_group' => '内容管理',
        'stats_description' => '总产品数',
    ],
    'tags' => [
        'label' => '标签',
        'plural_label' => '标签',
        'navigation_label' => '标签',
        'navigation_group' => '内容管理',
    ],
    'posts' => [
        'label' => '文章',
        'plural_label' => '文章',
        'navigation_label' => '文章',
        'navigation_group' => '内容管理',
        'stats_description' => '总文章数',
    ],
    'orders' => [
        'label' => '订单',
        'plural_label' => '订单',
        'navigation_label' => '订单',
        'navigation_group' => '销售管理',
        'stats_description' => '总订单数',
        'fields' => [
            'order_number' => '订单号',
            'customer_name' => '客户名称',
            'total_amount' => '总金额',
            'status' => '状态',
            'created_at' => '创建时间',
        ],
        'widgets' => [
            'latest_orders' => '最新订单',
        ],
    ],
    'warehouses' => [
        'label' => '仓库',
        'plural_label' => '仓库',
        'navigation_label' => '仓库',
        'navigation_group' => '仓库管理',
    ],
    'tags' => [
        'label' => '标签',
        'plural_label' => '标签',
        'navigation_label' => '标签',
        'navigation_group' => '内容管理',
    ],
    'users' => [
        'label' => '用户',
        'plural_label' => '用户',
        'navigation_label' => '用户',
        'navigation_group' => '用户管理',
        'stats_description' => '总用户数',
    ],
    'roles' => [
        'label' => '角色',
        'plural_label' => '角色',
        'navigation_label' => '角色',
        'sections' => [
            'details' => '角色详情',
            'permissions' => '权限',
            'permissions_description' => '为此角色选择权限。权限按模块分组以便于管理。',
        ],
        'fields' => [
            'name' => '角色名称',
            'name_helper' => '此角色的显示名称',
            'guard_name' => '守卫',
        ],
    ],
    'permissions' => [
        'label' => '权限',
        'plural_label' => '权限',
        'navigation_label' => '权限',
        'sections' => [
            'details' => '权限详情',
            'usage' => '使用信息',
            'usage_description' => '查看哪些角色正在使用此权限',
        ],
        'fields' => [
            'name' => '权限名称',
            'name_helper' => '使用 snake_case 格式：操作_资源（例如：view_any_products, create_products）',
            'description' => '描述',
            'description_helper' => '解释此权限允许用户执行的操作',
            'guard_name' => '守卫',
            'resource' => '资源',
            'roles' => '被角色使用',
            'assigned_roles' => '分配给角色',
            'users_count' => '受影响的用户',
            'created_at' => '创建时间',
        ],
        'filters' => [
            'resource' => '按资源筛选',
            'guard' => '按守卫筛选',
        ],
        'groups' => [
            'resource' => '按资源分组',
        ],
        'messages' => [
            'no_roles_yet' => '此权限将在创建后可用',
            'no_roles_assigned' => '尚未分配给任何角色',
            'users_via_roles' => '用户（通过角色）',
        ],
    ],
];
