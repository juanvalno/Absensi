@extends('adminlte::page')

@section('title', 'Departemen Management')

@section('content_header')
<h1>Departemen Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Departemen List</h3>
        <div class="card-tools">
            <div class="d-flex align-items-center">
                @can_show('departemens.create')
                <a href="{{ route('departemens.create') }}" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-plus"></i> Add New Departemen
                </a>
                @endcan_show

                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search departemen...">
                    <div class="input-group-append">
                        <button type="button" id="searchBtn" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
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

        <div class="table-responsive">
            <table class="table table-bordered" id="departemenTable">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Nama Departemen</th>
                        <th>Tanggal Dibuat</th>
                        @can_show('departemens.edit')
                        <th width="150">Action</th>
                        @endcan_show
                    </tr>
                </thead>
                <tbody id="departemenTableBody">
                    @foreach($departemens as $index => $departemen)
                    <tr data-id="{{ $departemen->id }}">
                        <td>{{ ($departemens->currentPage() - 1) * $departemens->perPage() + $index + 1 }}</td>
                        <td>{{ $departemen->name_departemen }}</td>
                        <td>{{ $departemen->created_at->format('d-m-Y H:i:s') }}</td>
                        @can_show('departemens.edit')
                        <td>
                            <a href="{{ route('departemens.edit', $departemen) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('departemens.destroy', $departemen) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endcan_show
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="text-muted">
                        Showing {{ $departemens->firstItem() ?? 0 }} to {{ $departemens->lastItem() ?? 0 }} of {{ $departemens->total() }} entries
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            {{ $departemens->appends(request()->except('page'))->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end align-items-center">
                        <span class="mr-2">Show</span>
                        <select id="perPageSelect" class="form-control form-control-sm d-inline-block" style="width: auto;">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="ml-2">entries</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function() {
        // Handle search button click
        $('#searchBtn').on('click', function() {
            performSearch();
        });

        // Handle Enter key in search input
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                performSearch();
            }
        });

        // Handle per page change
        $('#perPageSelect').on('change', function() {
            window.location.href = '{{ route("departemens.index") }}?per_page=' + $(this).val() +
                '&search=' + $('#searchInput').val();
        });

        // Set search input value from URL
        $('#searchInput').val('{{ request("search") }}');

        function performSearch() {
            window.location.href = '{{ route("departemens.index") }}?search=' +
                $('#searchInput').val() + '&per_page=' + $('#perPageSelect').val();
        }
    });
</script>
@stop
