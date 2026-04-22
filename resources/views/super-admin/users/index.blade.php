@extends('layouts.admin')

@section('title', 'Semua User')
@section('page_title', 'Semua User')

@section('content')
    <p class="page-description">Pantau semua akun customer, admin rental, dan super admin.</p>

    <form method="GET" action="{{ route('super-admin.users.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px; margin-bottom:18px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">

        <select name="role" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="">Semua Role</option>
            <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            <option value="admin_rental" @selected(request('role') === 'admin_rental')>Admin Rental</option>
            <option value="super_admin" @selected(request('role') === 'super_admin')>Super Admin</option>
        </select>

        <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Filter</button>
    </form>

    <div style="overflow:auto; background:#fff; border-radius:16px; padding:14px; border:1px solid #e5e7eb;">
        <table style="width:100%; border-collapse:collapse; min-width:860px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px;">Nama</th>
                    <th style="text-align:left; padding:12px;">Email</th>
                    <th style="text-align:left; padding:12px;">Telepon</th>
                    <th style="text-align:left; padding:12px;">Role</th>
                    <th style="text-align:left; padding:12px;">Terdaftar</th>
                    <th style="text-align:left; padding:12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px; font-weight:600;">{{ $user->name }}</td>
                        <td style="padding:12px;">{{ $user->email }}</td>
                        <td style="padding:12px;">{{ $user->phone ?: '-' }}</td>
                        <td style="padding:12px; text-transform:capitalize;">{{ str_replace('_', ' ', $user->role) }}</td>
                        <td style="padding:12px;">{{ $user->created_at->format('d M Y H:i') }}</td>
                        <td style="padding:12px;"><a href="{{ route('super-admin.users.show', $user) }}" style="padding:8px 10px; border-radius:8px; text-decoration:none; border:1px solid #cbd5e1; color:#0f172a; font-weight:600;">Detail</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:24px; text-align:center; color:#6b7280;">Tidak ada user untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:14px;">{{ $users->links() }}</div>
@endsection
