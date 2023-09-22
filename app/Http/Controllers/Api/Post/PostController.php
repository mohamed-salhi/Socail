<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FollowResource;
use App\Models\Comment;
use App\Models\Followers;
use App\Models\Like;
use App\Models\Post;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    public function addPost(Request $request){
        $rules = [
            'images' => 'required|array',
            'images.*' => 'required|image',
            'content' => 'required|string',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
      $post= Post::query()->create([
           'content'=>$request->get('content'),
          'user_uuid'=>$user->uuid
       ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Post::PATH_IMAGE, Post::class, $post->uuid, false, null, Upload::IMAGE);
            }
        }
        return mainResponse(true, "done", [], [], 201);
    }

    public function addLike(Request $request)
    {
        $rules = [
            'post_uuid' => 'required|exists:posts,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $check = Like::query()
                ->where('user_uuid', $user->uuid)
                ->where('post_uuid', $request->post_uuid)
                ->where('type', 'post')
                ->first();
            if (!$check) {
                Like::create([
                    'user_uuid' => $user->uuid,
                    'post_uuid' => $request->post_uuid,
                    'type' => 'post'
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
    public function getComment($uuid){
       $comments= Comment::query()->where('post_uuid',$uuid)->whereNull('comment_uuid')->get();
        $items= CommentResource::collection($comments);
        return mainResponse(true, "done",$items, [], 201);

    }

    public function addComment(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'post_uuid' => 'required|exists:posts,uuid',
            'comment'=>'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        Comment::query()->create([
            'user_uuid' => $user->uuid,
            'post_uuid' => $request->post_uuid,
            'comment' => $request->comment
        ]);
        return mainResponse(true, "done", [], [], 201);


    }

    public function followersPost(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = Followers::query()
            ->where('user_uuid', $user->uuid)
            ->where('receiver_uuid', $request->receiver_uuid)
            ->first();
        if (!$check) {
            Followers::create([
                'user_uuid' => $user->uuid,
                'receiver_uuid' => $request->receiver_uuid,
            ]);
            return mainResponse(true, 'ok', [], []);
        } else {
            $check->delete();
            return mainResponse(true, 'done delete', [], []);
        }

    }

    public function getMyFollowers(Request $request)
    {
        $search = $request->search;
        $user = Auth::guard('sanctum')->user();
        $uuids = $user->followers()->pluck('user_uuid');
        $items = User::query()->whereIn('uuid', $uuids)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->paginate();
        $items = pageResource($items, FollowResource::class);

        return mainResponse(true, 'done', $items, []);

    }

    public function getMyFollowing(Request $request)
    {
        $search = $request->search;

        $user = Auth::guard('sanctum')->user();
        $uuids = $user->following()->pluck('receiver_uuid');
        $users = User::query()->whereIn('uuid', $uuids)->when($search, function ($q) use ($search) {
            $q->where('name','like', "%{$search}%");
        })->paginate();
        $items = pageResource($users, FollowResource::class);

        return mainResponse(true, 'done', $items, []);

    }

    public function deleteUser()
    {
        $user = Auth::guard('sanctum')->user();
        $user->update([
            'mobile' => $user->mobile . '_delete' . rand(1000, 9999),
            'email' => $user->email . '_delete' . rand(1000, 9999)
        ]);
        $user->fcm_tokens()->delete();
        $user->tokens()->delete();
        $user->delete();
        return mainResponse(true, 'done', [], [], 200);

    }
}
