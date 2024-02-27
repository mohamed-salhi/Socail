@forelse($posts as $post)

@foreach($post->attachments as $i)
    <div class="row">
        <img width="200px" height="200px" src="{{ @$i['attachment'] }}">
    </div>
@endforeach

@empty
    <p>no there</p>
@endforelse


