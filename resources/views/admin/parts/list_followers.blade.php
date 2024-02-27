@forelse($followers as $follower)

            <div class="chip">
                <img src="{{ $follower->user->image }}" alt="Contact Person">{{ ($type == 'followers') ?  $follower->user->name : $follower->receiver->name }}</div>
            </div>

@empty
    <div class="col">
        <div class="card radius-10 border-0 border-start border-tiffany border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <p>no item yet !</p>
                </div>
            </div>
        </div>
    </div>
@endforelse
