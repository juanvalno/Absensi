@extends('adminlte::page')

@section('title', 'Menu Management')

@section('content_header')
<h1>Menu Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu List</h3>
        <div class="card-tools">
            @can_show('menu.create')
            <a href="{{ route('menu.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Menu
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="menu-container">
            <ul id="menu-list" class="menu-list">
                @foreach($menus->where('parent_id', null)->sortBy('order') as $menu)
                    <li class="menu-item" data-id="{{ $menu->id }}">
                        <div class="menu-item-bar">
                            <div class="menu-item-handle">
                                <span class="menu-drag-handle"><i class="fas fa-grip-lines"></i></span>
                                <span class="menu-item-title">{{ $menu->text }}</span>
                                <span class="menu-item-type">
                                    @if($menu->type == 'header')
                                    <span class="badge badge-secondary">Header</span>
                                    @elseif($menu->type == 'item')
                                    <span class="badge badge-info">Item</span>
                                    @else
                                    <span class="badge badge-primary">{{ ucfirst($menu->type) }}</span>
                                    @endif
                                </span>
                                @if($menu->icon)
                                <span class="menu-item-icon"><i class="{{ $menu->icon }}"></i></span>
                                @endif
                                <span class="menu-item-controls">
                                    <a href="{{ route('menu.edit', $menu) }}" class="btn btn-warning btn-xs" title="Edit Menu">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('menu.destroy', $menu) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-xs delete-btn" title="Delete Menu">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-light btn-xs toggle-btn" title="Toggle">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="menu-item-details" style="display: none;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Route:</strong> {{ $menu->route ?? '-' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Permission:</strong> {{ $menu->permission ?? '-' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Order:</strong> {{ $menu->order }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($menus->where('parent_id', $menu->id)->count() > 0)
                            <ul class="submenu-list">
                                @foreach($menus->where('parent_id', $menu->id)->sortBy('order') as $submenu)
                                    <li class="menu-item" data-id="{{ $submenu->id }}">
                                        <div class="menu-item-bar">
                                            <div class="menu-item-handle">
                                                <span class="menu-drag-handle"><i class="fas fa-grip-lines"></i></span>
                                                <span class="menu-item-title">{{ $submenu->text }}</span>
                                                <span class="menu-item-type">
                                                    @if($submenu->type == 'header')
                                                    <span class="badge badge-secondary">Header</span>
                                                    @elseif($submenu->type == 'item')
                                                    <span class="badge badge-info">Item</span>
                                                    @else
                                                    <span class="badge badge-primary">{{ ucfirst($submenu->type) }}</span>
                                                    @endif
                                                </span>
                                                @if($submenu->icon)
                                                <span class="menu-item-icon"><i class="{{ $submenu->icon }}"></i></span>
                                                @endif
                                                <span class="menu-item-controls">
                                                    <a href="{{ route('menu.edit', $submenu) }}" class="btn btn-warning btn-xs" title="Edit Menu">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('menu.destroy', $submenu) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-xs delete-btn" title="Delete Menu">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-light btn-xs toggle-btn" title="Toggle">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <div class="menu-item-details" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Route:</strong> {{ $submenu->route ?? '-' }}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Permission:</strong> {{ $submenu->permission ?? '-' }}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Order:</strong> {{ $submenu->order }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i> Drag and drop menu items to reorder them. Changes will be saved automatically.
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
                <p><strong>Warning:</strong> Deleting a parent menu may affect child menu items.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .menu-container {
        margin: 20px 0;
    }
    .menu-list, .submenu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .submenu-list {
        padding-left: 30px;
        margin-top: 5px;
    }
    .menu-item {
        margin-bottom: 5px;
    }
    .menu-item-bar {
        border: 1px solid #ddd;
        background: #f9f9f9;
        border-radius: 3px;
        margin-bottom: 5px;
    }
    .menu-item-handle {
        padding: 10px;
        display: flex;
        align-items: center;
        cursor: move;
    }
    .menu-drag-handle {
        color: #999;
        margin-right: 10px;
        cursor: move;
    }
    .menu-item-title {
        flex-grow: 1;
        font-weight: 500;
    }
    .menu-item-type {
        margin-right: 10px;
    }
    .menu-item-icon {
        margin-right: 10px;
        color: #666;
    }
    .menu-item-controls {
        white-space: nowrap;
    }
    .menu-item-controls .btn {
        margin-left: 3px;
    }
    .menu-item-details {
        padding: 10px;
        background: #fff;
        border-top: 1px solid #eee;
    }
    .ui-sortable-placeholder {
        border: 2px dashed #ccc;
        background: #f7f7f7 !important;
        visibility: visible !important;
        height: 40px;
        margin-bottom: 5px;
    }
    .ui-sortable-helper {
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .btn-xs {
        padding: .125rem .25rem;
        font-size: .75rem;
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function() {
        // Initialize sortable for main menu
        $("#menu-list").sortable({
            handle: '.menu-drag-handle',
            placeholder: 'ui-sortable-placeholder',
            update: function(event, ui) {
                updateMenuOrder();
            }
        });

        // Initialize sortable for submenus
        $(".submenu-list").sortable({
            handle: '.menu-drag-handle',
            placeholder: 'ui-sortable-placeholder',
            connectWith: '.submenu-list',
            update: function(event, ui) {
                updateMenuOrder();
            }
        });

        // Toggle menu item details
        $('.toggle-btn').on('click', function() {
            const details = $(this).closest('.menu-item-bar').find('.menu-item-details');
            const icon = $(this).find('i');

            details.slideToggle(200);

            if (icon.hasClass('fa-chevron-down')) {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });

        // Function to update menu order
        function updateMenuOrder() {
            let order = [];
            let position = 1;

            // Process main menu items
            $('#menu-list > li.menu-item').each(function(index) {
                const menuId = $(this).data('id');
                order.push({
                    id: menuId,
                    parent_id: null,
                    order: position++
                });

                // Process submenu items
                $(this).find('ul.submenu-list > li.menu-item').each(function() {
                    const submenuId = $(this).data('id');
                    order.push({
                        id: submenuId,
                        parent_id: menuId,
                        order: position++
                    });
                });
            });

            // Show loading indicator
            Swal.fire({
                title: 'Updating menu order...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Update order via AJAX
            $.ajax({
                url: '{{ route("menu.update-order") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order: order
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Menu order has been updated',
                            timer: 1500
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update menu order'
                    });
                }
            });
        }

        // Delete confirmation
        let formToSubmit = null;
        $('.delete-btn').on('click', function() {
            formToSubmit = $(this).closest('form');
            $('#deleteConfirmModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            $('#deleteConfirmModal').modal('hide');
        });
    });
</script>
@stop