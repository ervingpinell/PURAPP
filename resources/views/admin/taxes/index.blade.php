@extends('adminlte::page')

@section('title', __('taxes.title'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{ __('taxes.title') }}</h1>
    <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('taxes.create') }}
    </a>
</div>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('taxes.fields.name') }}</th>
                    <th>{{ __('taxes.fields.code') }}</th>
                    <th>{{ __('taxes.fields.rate') }}</th>
                    <th>{{ __('taxes.fields.type') }}</th>
                    <th>{{ __('taxes.fields.apply_to') }}</th>
                    <th>{{ __('taxes.fields.is_inclusive') }}</th>
                    <th>{{ __('taxes.fields.is_active') }}</th>
                    <th>{{ __('m_general.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taxes as $tax)
                <tr>
                    <td>{{ $tax->name }}</td>
                    <td><code>{{ $tax->code }}</code></td>
                    <td>
                        @if($tax->type == 'percentage')
                        {{ number_format($tax->rate, 2) }}%
                        @else
                        ${{ number_format($tax->rate, 2) }}
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">
                            {{ __('taxes.types.' . $tax->type) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-secondary">
                            {{ __('taxes.apply_to_options.' . $tax->apply_to) }}
                        </span>
                    </td>
                    <td>
                        @if($tax->is_inclusive)
                        <span class="badge badge-success"><i class="fas fa-check"></i></span>
                        @else
                        <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.taxes.toggle', $tax) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $tax->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $tax->is_active ? __('m_general.active') : __('m_general.inactive') }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.taxes.edit', $tax) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.taxes.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('m_general.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('m_general.no_records') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop