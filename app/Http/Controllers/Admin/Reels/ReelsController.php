<?php

namespace App\Http\Controllers\Admin\Reels;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Upload;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReelsController extends Controller
{
    function index(){
        return view('admin.reels.index');
    }

    function getdata(){
        $posts = Post::query()->where('status' , '1')->where('type' , 'rails')->get();
        return DataTables::of($posts)
            ->addIndexColumn()
            ->addColumn('image' , function ($qur){
                return '<img src="'. @$qur->imagesPost .'" style="width:100px;height:100px;"  class="img-fluid img-thumbnail">';
            })
            ->addColumn('post' , function ($qur){
                return $qur->content ;
            })
            ->addColumn('user' , function ($qur){
                return   '<a href="'. route('admin.user.details' , $qur->user->uuid) .'">
                          <div class="chip" style="max-width: 125px ; ">
                             <img src="'.@$qur->user->image .'" alt="Contact Person">'. $qur->user->name .'</div>
                           </div>
                         </a>';
            })
            ->addColumn('comment' , function ($qur){
                if(count($qur->comments) >= 3){
                    return '<div  data-bs-toggle="modal" data-bs-target="#comments-modal" class="comments comments_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->comments[0]->user->image. '" alt="صورة 1">
                                 </div>
                                 <div class="circle circle2">
                                     <img src="' .@$qur->comments[1]->user->image . '" alt="صورة 2">
                                 </div>
                                 <div class="circle circle3">
                                     <img src="' .@$qur->comments[2]->user->image . '" alt="صورة 3">
                                 </div>
                             </div>
                         </div>';
                }elseif(count($qur->comments) == 2){
                    return '<div  data-bs-toggle="modal" data-bs-target="#comments-modal" class="comments comments_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->comments[0]->user->image. '" alt="صورة 1">
                                 </div>
                                 <div class="circle circle2">
                                     <img src="' .@$qur->comments[1]->user->image . '" alt="صورة 2">
                                 </div>
                             </div>
                         </div>';
                }elseif(count($qur->comments) == 1){
                    return '<div  data-bs-toggle="modal" data-bs-target="#comments-modal" class="comments comments_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->comments[0]->user->image. '" alt="صورة 1">
                                 </div>
                             </div>
                         </div>';
                }else{
                    return '<div  data-bs-toggle="modal" data-bs-target="#comments-modal" class="comments comments_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                               '.  __('no_comments') .'
                             </div>
                         </div>';
                }

            })
            ->addColumn('likes' , function ($qur){
                if(count($qur->likes) >= 3){
                    return '<div  data-bs-toggle="modal" data-bs-target="#likes-modal" class="comments likes_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->likes[0]->user->image. '" alt="صورة 1">
                                 </div>
                                 <div class="circle circle2">
                                     <img src="' .@$qur->likes[1]->user->image . '" alt="صورة 2">
                                 </div>
                                 <div class="circle circle3">
                                     <img src="' .@$qur->likes[2]->user->image . '" alt="صورة 3">
                                 </div>
                             </div>
                         </div>';
                }elseif(count($qur->likes) == 2){
                    return '<div  data-bs-toggle="modal" data-bs-target="#likes-modal" class="comments likes_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->likes[0]->user->image. '" >
                                 </div>
                                 <div class="circle circle2">
                                     <img src="' .@$qur->likes[1]->user->image . '" >
                                 </div>
                             </div>
                         </div>';
                }elseif(count($qur->likes) == 1){
                    return '<div  data-bs-toggle="modal" data-bs-target="#likes-modal" class="comments likes_btn" data-id="'. $qur->uuid .'">
                             <div class="circle-container">
                                 <div class="circle circle1">
                                     <img src="' .@$qur->likes[0]->user->image. '" alt="صورة 1">
                                 </div>
                             </div>
                         </div>';
                }else{
                    return  __('no_likes');
                }
            })
            ->addColumn('actions' , function ($qur){
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $qur->uuid  . '"';
                $data_attr .= 'data-content="' . $qur->content . '"';

                $string = '' ;
                $string .= '
                    <div class="d-flex align-items-center gap-3 fs-6">
                      <div class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
                      <div class="text-danger delete_btn" data-url="/admin/posts/delete" data-id="'. $qur->uuid .'"><i class="bi bi-trash-fill"></i></div>
                    </div>';
                return $string ;
            })
            ->rawColumns(['actions' , 'image' , 'post' , 'user' , 'comment' , 'likes'])
            ->make(true);
    }

    function getcomments(Request $request){
        $post = Post::query()->where('uuid' , $request->id)->first();
        //dd($post->comments[0]);
        return view('admin.parts.list_comments' , compact('post'))->render();
    }

    function deletecomment(Request $request){
        $comment = Comment::query()->where('uuid' , $request->id)->first();
        $comment->update([
            'status' => Comment::BLOCK ,
        ]);
        $post = Post::query()->where('uuid' , $request->idpost)->first();

        //dd($post->comments[0]);
        return view('admin.parts.list_comments' , compact('post'))->render();
    }

    function getlikes(Request $request){
        $likes = Like::query()->where('content_uuid' , $request->id)->get();
        //dd($post->comments[0]);
        return view('admin.parts.list_likes' , compact('likes'))->render();
    }

    function update(Request $request){
        $post = Post::query()->where('uuid' , $request->id)->first();
        $post->update([
            'content' => $request->desc
        ]);

        if ($request->hasFile('image')) {
            UploadImage($request->image, Post::PATH_IMAGE, Post::class, $post->uuid, true, null, Upload::IMAGE);
        }

        return response()->json();
    }

    function delete(Request $request){
        $post = Post::query()->where('uuid' , $request->id)->first();
        $post->update([
            'status' => Post::BLOCK ,
        ]);

        return response()->json(["success" => "Deleted Successful"] , 201) ;
    }
}
