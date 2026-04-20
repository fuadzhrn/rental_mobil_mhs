@if ($vehicles->hasPages())
    <nav class="pagination-wrap" aria-label="Navigasi halaman katalog">
        {{ $vehicles->links() }}
    </nav>
@endif
