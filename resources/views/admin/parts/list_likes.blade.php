@foreach($likes as $like)
    <div class="card radius-10">
        <div class="card-body">
            <div class="chip">
                <img src="{{ $like->user->image }}" alt="Contact Person">{{  $like->user->name  }}</div>
        </div>
    </div>
    </div>
@endforeach
