@extends('layouts.admin')

@section('title', 'Inventory Monitoring')
@section('page_title', 'Inventory Monitoring')

@section('content')
    <div class="admin-content">
        <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
            <form method="GET" style="display:flex; gap:8px; align-items:center;">
                <label>Filter Rental:</label>
                <select name="rental_company_id" onchange="this.form.submit()" style="padding:6px;">
                    <option value="">All</option>
                    @foreach($rentalCompanies as $rc)
                        <option value="{{ $rc->id }}" @if(request('rental_company_id') == $rc->id) selected @endif>{{ $rc->company_name }}</option>
                    @endforeach
                </select>

                <label style="margin-left:8px;">Low stock:</label>
                <input type="checkbox" name="low_stock" value="1" @if(request()->filled('low_stock')) checked @endif onchange="this.form.submit()">
                <input type="number" name="threshold" value="{{ request('threshold', 1) }}" min="0" style="width:80px; margin-left:6px;">
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Rental</th>
                    <th>Vehicle</th>
                    <th>Total</th>
                    <th>Reserved</th>
                    <th>Available</th>
                    <th>Last Checked By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $inv)
                    <tr @if($inv->available <= 0) style="background:#fff1f0;" @elseif($inv->available <= 2) style="background:#fffbeb;" @endif>
                        <td>{{ $inv->rentalCompany->company_name ?? '-' }}</td>
                        <td>{{ $inv->vehicle->name ?? '-' }}</td>
                        <td>{{ $inv->total }}</td>
                        <td>{{ $inv->reserved }}</td>
                        <td>{{ $inv->available }}</td>
                        <td>{{ $inv->lastCheckedBy->name ?? '-' }}</td>
                        <td>{{ 
                            
                            Illuminate\Support\Str::limit($inv->notes, 80)
                        }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No inventories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $inventories->links() }}
    </div>
@endsection
