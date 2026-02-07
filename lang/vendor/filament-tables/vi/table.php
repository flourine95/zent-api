<?php

return [

    'column_manager' => [

        'heading' => 'Cột',

        'actions' => [

            'apply' => [
                'label' => 'Áp dụng cột',
            ],

            'reset' => [
                'label' => 'Đặt lại',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Hành động|Hành động',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Hiển thị giảm :count',
                'expand_list' => 'Hiển thị thêm :count',
            ],

            'more_list_items' => 'và thêm :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chọn/bỏ chọn tất cả các mục để thực hiện tác vụ hàng loạt.',
        ],

        'bulk_select_record' => [
            'label' => 'Chọn/bỏ chọn mục :key để thực hiện tác vụ hàng loạt.',
        ],

        'bulk_select_group' => [
            'label' => 'Chọn/bỏ chọn nhóm :title để thực hiện tác vụ hàng loạt.',
        ],

        'search' => [
            'label' => 'Tìm kiếm',
            'placeholder' => 'Tìm kiếm',
            'indicator' => 'Tìm kiếm',
        ],

    ],

    'summary' => [

        'heading' => 'Tóm tắt',

        'subheadings' => [
            'all' => 'Tất cả :label',
            'group' => 'Tóm tắt :group',
            'page' => 'Trang này',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Trung bình',
            ],

            'count' => [
                'label' => 'Số lượng',
            ],

            'sum' => [
                'label' => 'Tổng cộng',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Hoàn thành sắp xếp',
        ],

        'enable_reordering' => [
            'label' => 'Sắp xếp thứ tự',
        ],

        'filter' => [
            'label' => 'Bộ lọc',
        ],

        'group' => [
            'label' => 'Nhóm',
        ],

        'open_bulk_actions' => [
            'label' => 'Hành động hàng loạt',
        ],

        'column_manager' => [
            'label' => 'Quản lý cột',
        ],

    ],

    'empty' => [

        'heading' => 'Không có :model nào',

        'description' => 'Tạo một :model để bắt đầu.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Áp dụng',
            ],

            'remove' => [
                'label' => 'Xóa',
            ],

            'remove_all' => [
                'label' => 'Xóa tất cả',
                'tooltip' => 'Xóa tất cả',
            ],

            'reset' => [
                'label' => 'Đặt lại',
            ],

        ],

        'heading' => 'Bộ lọc',

        'indicator' => 'Bộ lọc đang áp dụng',

        'multi_select' => [
            'placeholder' => 'Tất cả',
        ],

        'select' => [

            'placeholder' => 'Tất cả',

            'relationship' => [
                'empty_option_label' => 'Không có',
            ],

        ],

        'trashed' => [

            'label' => 'Bản ghi đã xoá',

            'only_trashed' => 'Chỉ bản ghi đã xoá',

            'with_trashed' => 'Bao gồm bản ghi đã xóa',

            'without_trashed' => 'Không bao gồm bản ghi đã xóa',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Nhóm theo',
                'placeholder' => 'Nhóm theo',
            ],

            'direction' => [

                'label' => 'Hướng nhóm',

                'options' => [
                    'asc' => 'Tăng dần',
                    'desc' => 'Giảm dần',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Kéo và thả các bản ghi để sắp xếp lại.',

    'selection_indicator' => [

        'selected_count' => 'Đã chọn 1 bản ghi|Đã chọn :count bản ghi',

        'actions' => [

            'select_all' => [
                'label' => 'Chọn tất cả :count',
            ],

            'deselect_all' => [
                'label' => 'Bỏ chọn tất cả',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sắp xếp theo',
            ],

            'direction' => [

                'label' => 'Hướng sắp xếp',

                'options' => [
                    'asc' => 'Tăng dần',
                    'desc' => 'Giảm dần',
                ],

            ],

        ],

    ],

    'default_model_label' => 'bản ghi',

];
