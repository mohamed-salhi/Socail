<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FollowResource;
use App\Models\Block;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Followers;
use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    public function addPost(Request $request)
    {
        $rules = [
            'images' => 'required_if:type,==,post|array',
            'images.*' => 'required_if:type,==,post|image',
            'content' => 'required|string',
            'video' => 'required_if:type,==,rails',
            'type' => 'required|in:rails,post',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        if ($request->hasFile('images')) {
            $post = Post::query()->create([
                'content' => $request->get('content'),
                'user_uuid' => $user->uuid,
                'type' => 'post'

            ]);
            foreach ($request->images as $item) {
                UploadImage($item, Post::PATH_IMAGE, Post::class, $post->uuid, false, null, Upload::IMAGE);
            }
        }
        if ($request->hasFile('video')) {
            $post = Post::query()->create([
                'content' => $request->get('content'),
                'user_uuid' => $user->uuid,
                'type' => 'rails'

            ]);
                UploadImage($request->video, Post::PATH_VIDEO, Post::class, $post->uuid, false, null, Upload::VIDEO);

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
                ->where('content_uuid', $request->post_uuid)
                ->where('type', 'post')
                ->first();
            if (!$check) {
                Like::create([
                    'user_uuid' => $user->uuid,
                    'content_uuid' => $request->post_uuid,
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



    public function getComment($uuid)
    {
        $post = Post::query()->findOrFail($uuid);
        $block_uuid = Block::query()->where('user_uuid', $post->user_uuid)->pluck('receiver_uuid');
           $comments = Comment::query()->whereNotIn('user_uuid', $block_uuid)->where('post_uuid', $uuid)->whereNull('comment_uuid')->withCount('favorites')->get();
        $items = CommentResource::collection($comments);
        return mainResponse(true, "done", $items, [], 201);

    }

    public function addComment(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'post_uuid' => 'required|exists:posts,uuid',
            'comment' => 'required|string',
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

    public function replyComment(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'comment_uuid' => 'required|exists:comments,uuid',
            'comment' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $comment = Comment::query()->findOrFail($request->comment_uuid);
        Comment::query()->create([
            'user_uuid' => $user->uuid,
            'post_uuid' => $comment->post->uuid,
            'comment' => $request->comment,
            'comment_uuid' => $request->comment_uuid

        ]);
        return mainResponse(true, "done", [], [], 201);


    }

    public function favoritePost($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $post = Post::query()->find($uuid);
        if ($post) {
            $favorite = Favorite::query()->where('user_uuid', $user->uuid)->where('type', Favorite::POST)->where('content_uuid', $post->uuid)->first();
            if (!$favorite) {
                Favorite::query()->create([
                    'user_uuid' => $user->uuid,
                    'content_uuid' => $post->uuid,
                    'type' => Favorite::POST
                ]);
                return mainResponse(true, "done", [], [], 201);

            } else {
                $favorite->delete();
                return mainResponse(true, "deleted", [], [], 201);

            }


        } else {
            return mainResponse(false, "comment not found", [], [], 404);

        }


    }

    public function favoriteComment($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = Comment::query()->find($uuid);
        if ($comment) {
            $favorite = Favorite::query()->where('user_uuid', $user->uuid)->where('type', Favorite::COMMENT)->where('content_uuid', $comment->uuid)->first();
            if (!$favorite) {
                Favorite::query()->create([
                    'user_uuid' => $user->uuid,
                    'content_uuid' => $comment->uuid,
                    'type' => Favorite::COMMENT
                ]);
                return mainResponse(true, "done", [], [], 201);

            } else {
                $favorite->delete();
                return mainResponse(true, "deleted", [], [], 201);

            }


        } else {
            return mainResponse(false, "comment not found", [], [], 404);

        }


    }

    public function deleteComment($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = Comment::query()->find($uuid);
        if ($comment) {
            if ($user->uuid == $comment->post->user_uuid || $user->uuid == $comment->user_uuid) {
                $comment->delete();
                return mainResponse(true, "comment delete", [], [], 201);
            } else {
                return mainResponse(false, "comment not found", [], [], 403);
            }
        } else {
            return mainResponse(false, "comment not found", [], [], 404);
        }


    }

    public function reportComment($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = Comment::query()->find($uuid);
        if ($comment) {
            Report::query()->create([
                'user_uuid' => $user->uuid,
                'content_uuid' => $comment->uuid,
                'type' => Report::COMMENT,
            ]);
            return mainResponse(true, "done", [], [], 404);

        } else {
            return mainResponse(false, "comment not found", [], [], 404);
        }


    }

    public function hiddenComment($uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $comment = Comment::query()->find($uuid);
        if ($comment) {
            if ($user->uuid == $comment->post->user_uuid) {
                $comment->update([
                    'status' => Comment::HIDDEN
                ]);
                return mainResponse(true, "comment hidden", [], [], 201);
            } else {
                return mainResponse(false, "comment not found", [], [], 403);
            }
        } else {
            return mainResponse(false, "comment not found", [], [], 404);
        }


    }


}
