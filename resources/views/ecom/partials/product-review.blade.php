{{-- at first show own_review  --}}

@if ($own_review)
    <div class="testimonial-box">
        <div class="box-top">
            <div class="profile">
                <div class="profile-img">
                    @if (isset($own_review->user->profile_picture) && $own_review->user->profile_picture != null)
                        <img src="{{ Storage::url($own_review->user->profile_picture) }}" />
                    @else
                        <img src="{{ asset('ecom_assets/images/dummy.webp') }}" />
                    @endif
                </div>
                <div class="name-user">
                    <strong>{{ isset($own_review->user->full_name) ? $own_review->user->full_name : 'User' }}</strong>
                    <span>{{ $own_review->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="reviews">
                <ul class="star_ul">
                    @for ($i = 1; $i <= 5; $i++)
                        <li><i class="fa-{{ $i <= $own_review->rating ? 'solid' : 'regular' }} fa-star"></i></li>
                    @endfor
                </ul>
            </div>
        </div>
        <div class="client-comment">
            <p>
                {!! nl2br($own_review->review) !!}
            </p>
        </div>
    </div>
@endif

@if (count($reviews) > 0)

    @foreach ($reviews as $review)
        <div class="testimonial-box">
            <div class="box-top">
                <div class="profile">
                    <div class="profile-img">
                        @if (isset($review->user->profile_picture) && $review->user->profile_picture != null)
                            <img src="{{ Storage::url($review->user->profile_picture) }}" />
                        @else
                            <img src="{{ asset('ecom_assets/images/dummy.webp') }}" />
                        @endif
                    </div>
                    <div class="name-user">
                        <strong>{{ isset($review->user->full_name) ? $review->user->full_name : 'User' }}</strong>
                        <span>{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="reviews">
                    <ul class="star_ul">
                        @for ($i = 1; $i <= 5; $i++)
                            <li><i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i></li>
                        @endfor
                    </ul>
                </div>
            </div>
            <div class="client-comment">
                <p>
                    {!! nl2br($review->review) !!}
                </p>
            </div>
        </div>
    @endforeach
@endif

@if ((!isset($own_review) || !$own_review) && count($reviews) === 0)
    <div class="p-4 mt-2" style="border: 1px dashed #d7d7d7; border-radius: 10px; background: #fff;">
        <h5 class="mb-2" style="color:#202d4d;">No reviews yet</h5>
        <p class="mb-0" style="color:#5b667a;">
            Be the first to review this product.
            @auth
                If you’ve purchased it, you can submit your review above.
            @else
                Please log in to leave a review.
            @endauth
        </p>
    </div>
@endif
