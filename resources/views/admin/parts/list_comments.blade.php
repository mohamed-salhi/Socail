@forelse($post->comments as $p)
    <div class="card radius-10">
        <div class="card-body">
            <div class="chip">
                <img src="{{ $p->user->image }}" alt="Contact Person">{{ $p->user->name }} || @lang("comments") :  {{ $p->comment  }} <span class="closebtn" data-id-comment="{{ $p->uuid }}" data-id-post="{{ $p->post->uuid }}" style="color: red"> ×</span></div>
        </div>
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
