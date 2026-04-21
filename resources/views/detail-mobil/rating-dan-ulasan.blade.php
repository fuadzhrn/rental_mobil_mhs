<section class="detail-section" aria-label="Rating dan ulasan pengguna">
    <div class="container">
        <article class="reviews-wrapper">
            <div class="section-heading">
                <p class="eyebrow">Testimoni Pengguna</p>
                <h2>Rating dan Ulasan</h2>
            </div>

            <div class="review-summary">
                <div class="score-card">
                    <strong>{{ $totalReviews > 0 ? number_format($averageRating, 1) : '-' }}</strong>
                    <span>/ 5.0</span>
                    <p>{{ $totalReviews }} ulasan terverifikasi dari booking selesai.</p>
                </div>

                <div class="score-bars">
                    @for ($star = 5; $star >= 1; $star--)
                        @php
                            $count = $ratingBreakdown[$star] ?? 0;
                            $maxValue = max($totalReviews, 1);
                        @endphp
                        <div>
                            <label>{{ $star }} Bintang ({{ $count }})</label>
                            <div class="bar">
                                <progress value="{{ $count }}" max="{{ $maxValue }}" style="width:100%;"></progress>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="review-list">
                @forelse ($reviews as $review)
                    @php
                        $customerName = $review->customer?->name ?? 'Customer';
                        $nameLength = function_exists('mb_strlen') ? mb_strlen($customerName) : strlen($customerName);

                        if ($nameLength <= 2) {
                            $displayName = $customerName;
                        } else {
                            $first = function_exists('mb_substr') ? mb_substr($customerName, 0, 1) : substr($customerName, 0, 1);
                            $last = function_exists('mb_substr') ? mb_substr($customerName, -1) : substr($customerName, -1);
                            $displayName = $first . str_repeat('*', max($nameLength - 2, 1)) . $last;
                        }
                    @endphp

                    <article class="review-card">
                        <div class="review-head">
                            <div>
                                <h3>{{ $displayName }}</h3>
                                <p>{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                            <div aria-label="Rating {{ $review->rating }} dari 5" style="color:#f59e0b; font-size:16px;">
                                @for ($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>
                        <p>{{ $review->review ?: 'Customer memberikan rating tanpa komentar tambahan.' }}</p>
                    </article>
                @empty
                    <article class="review-card">
                        <div class="review-head">
                            <div>
                                <h3>Belum ada ulasan</h3>
                                <p>Jadilah penyewa pertama yang memberikan penilaian untuk kendaraan ini.</p>
                            </div>
                        </div>
                        <p>Ulasan akan muncul setelah customer menyelesaikan booking dan mengirimkan review.</p>
                    </article>
                @endforelse
            </div>
        </article>
    </div>
</section>
