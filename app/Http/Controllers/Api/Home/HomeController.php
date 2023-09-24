<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailsShahidResource;
use App\Http\Resources\LiveResource;
use App\Http\Resources\CategoryResource;

use App\Http\Resources\MapResource;
use App\Http\Resources\MaResource;

use App\Http\Resources\PostResource;
use App\Http\Resources\ShahidResource;

use App\Http\Resources\StoryResource;
use App\Models\Category;

use App\Models\Like;
use App\Models\Live;
use App\Models\Mosque;
use App\Models\Page;

use App\Models\Post;
use App\Models\Shahid;

use App\Models\Story;
use App\Models\Upload;
use App\Models\User;
use App\Models\ViewStory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function page($id)
    {
        $setting = Page::query()->where('id', $id)->first();
        return mainResponse(true, "done", $setting, []);
    }

    public function addStory(Request $request)
    {
        $rules = [
            'image' => 'nullable|image',
            'video' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        if (!$request->hasFile('image') && !$request->hasFile('image')) {
            return mainResponse(false, "file not found", [], [], 300);

        }


        $user = Auth::guard('sanctum')->user();
        $item = Story::query()->create([
            'user_uuid' => $user->uuid
        ]);
        if ($request->hasFile('image')) {
            $attachment = Upload::IMAGE;
            UploadImage($request->image, Story::PATH, Story::class, $item->uuid, false, null, $attachment);

        } else {
            $attachment = Upload::VOICE;
            UploadImage($request->video, Story::PATH, Story::class, $item->uuid, false, null, $attachment);

        }

        return mainResponse(true, "done", [], [], 201);
    }

    public function deleteStory($uuid)
    {
        $item = Story::query()->findOrFail($uuid);

        if ($item->user_uuid==\auth('sanctum')->id()){

            Storage::delete('public/' . @$item->imagesStory->path);
            $item->delete();

            return mainResponse(true, "done", [], [], 201);
        }else{
            return mainResponse(false, "err", [], [], 403);

        }

    }


    public function getStory($uuid, $story_uuid = null)
    {
        // Calculate the date and time 24 hours ago
        $twentyFourHoursAgo = Carbon::now()->subHours(24);
        $user_uuid = Auth::guard('sanctum')->id();
        // Retrieve stories created within the last 24 hours
        $stories_uuid = ViewStory::query()->where('user_uuid', $user_uuid)->pluck('story_uuid');
        $stories = Story::query()
            ->whereNotIn('uuid', $stories_uuid)
            ->where('user_uuid', $uuid)
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at')
            ->firstOrFail();
        $count = Story::query()
            ->where('user_uuid', $uuid)
            ->whereNotIn('uuid', $stories_uuid)
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at')
            ->count();
        $item = StoryResource::make($stories);
        $stories_count = Story::query()
            ->where('user_uuid', $uuid)
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at')
            ->count();
        $uuid_stores = null;
        if ($count != 0) {
            ViewStory::query()->create([
                'user_uuid' => $user_uuid,
                'story_uuid' => $stories->uuid
            ]);
            $number_story = $stories_count - $count;
            $number_story++;
        } else {
            $number_story = null;
            $uuid_stores = Story::query()->where('user_uuid', $user_uuid)
                ->where('created_at', '>=', $twentyFourHoursAgo)
                ->orderBy('created_at')
                ->pluck('uuid');
            if ($story_uuid) {
                $item = Story::query()->findOrFail($story_uuid);
                $item = StoryResource::make($item);
            } else {
                $item = Story::query()->where('user_uuid', $user_uuid)
                    ->where('created_at', '>=', $twentyFourHoursAgo)
                    ->orderBy('created_at')
                    ->first();
                $item = StoryResource::make($item);


            }
        }

        return mainResponse(true, "done", compact('item', 'stories_count', 'number_story', 'uuid_stores'), [], 201);


    }


    public function addLike(Request $request)
    {
        $rules = [
            'story_uuid' => 'required|exists:stories,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $check = Like::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_uuid', $request->story_uuid)
                ->where('type', 'story')
                ->first();
            if (!$check) {
                Like::create([
                    'user_uuid' => $user->uuid,
                    'content_uuid' => $request->story_uuid,
                    'type' => 'story'
                ]);
                return mainResponse(true, 'ok', [], []);
            } else {
                $check->delete();
                return mainResponse(true, 'done delete', [], []);
            }
        } else {
            return mainResponse(false, 'users is not register', [], []);
        }
    }

    public function home()
    {
        $user = Auth::guard('sanctum')->user();

        $uuids = $user->following()->pluck('receiver_uuid');
        $users_story = User::query()->whereIn('uuid', $uuids)->has('stories')->pluck('uuid');

//        $posts = User::query()->whereIn('uuid', $uuids)->with('posts')->get();
//       return $items = pageResource($posts->posts,PostResource::class);
//        return $posts->posts;
        $posts = Post::query()->where('type','post')->whereHas('user', function ($q) use ($uuids) {
            $q->whereIn('user_uuid', $uuids);
        })->withCount('comments')->withCount('likes')->get();

        $item = PostResource::collection($posts);
        return mainResponse(true, "done", compact('users_story', 'item'), [], 201);

    }
public function rails(){
    $user = Auth::guard('sanctum')->user();

    $uuids = $user->following()->pluck('receiver_uuid');
//        $posts = User::query()->whereIn('uuid', $uuids)->with('posts')->get();
//       return $items = pageResource($posts->posts,PostResource::class);
//        return $posts->posts;
    $posts = Post::query()->where('type','rails')->whereHas('user', function ($q) use ($uuids) {
        $q->whereIn('user_uuid', $uuids);
    })->withCount('comments')->withCount('likes')->get();

    $item = PostResource::collection($posts);
    return mainResponse(true, "done", compact( 'item'), [], 201);

}

}
