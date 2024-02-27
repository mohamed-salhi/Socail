<?php

namespace App\Http\Controllers\Admin\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class CommentController extends Controller
{
    function index(){
        return view('admin.comments.index');
    }

    function getdata(Request $request){
            $comments = Comment::query();
            return DataTables::of($comments)
            ->addIndexColumn()
            ->addColumn('commenter' , function ($qur){
                return ' <a href="'. route('admin.user.details' , $qur->user->uuid) .'">
                 <div class="chip">
                        <img src="'.  $qur->user->image . '" alt="Contact Person">' .  $qur->user->name .'</div>
                      </div>
                 </a>  ' ;
            })
            ->addColumn('publisher' , function ($qur){
               return '  <a href="'. route('admin.user.details' , $qur->post->user->uuid) .'"> <div class="chip">
                <img src="'. $qur->post->user->image . '" alt="Contact Person">' . $qur->post->user->name .'</div>
            </div></a>' ;
            })
            ->addColumn('post' , function ($qur){
                return '   <div class="chip">
                <img src="" alt="Contact Person">' . $qur->post->content .'</div>
            </div>' ;
            })
            ->addColumn('actions' , function ($qur){
                $data_attr = '';
                $data_attr .= 'data-id="' . $qur->uuid  . '" ';
                $data_attr .= 'data-comment="' . $qur->comment . '"';
                $string = '' ;
                $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div  class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
                      <div  class="text-danger delete_btn" data-id="'. $qur->uuid .'" data-url="/admin/comments/delete"><i class="bi bi-trash-fill"></i></div>
                    </div>';
                return $string ;
            })
            ->addColumn('date' , function ($qur){
                $date = Carbon::parse($qur->created_at->toDateString());
                return $date->format('Y-M-d');;
            })
            ->addColumn('time' , function ($qur){
                return $qur->created_at->format('h:i A');;
            })
            ->rawColumns(['commenter', 'post' ,'publisher' , 'date' , 'time' , 'actions'])
            ->make(true);
    }

    function update(Request $request){
        $request->validate([
            'comment' => 'required|string' ,
        ]);

        $comment = Comment::query()->where('uuid' , $request->id)->first();
        $comment->update([
            'comment' => $request->comment ,
        ]);

        return response()->json([
            'success' => __('add_successfully')
        ] , 201);
    }


    function details($id){
         $user = User::query()->where('uuid' , $id)->first();
        return view('admin.comments.details' , compact('user'));
    }


   function getcomments(Request $request){
       $comments = Comment::query()->where('user_uuid' , $request->id);
       return DataTables::of($comments)
           ->addIndexColumn()
           ->addColumn('post' , function ($qur){
               return '   <div class="chip">
                <img src="" alt="Contact Person">' . $qur->post->content .'</div>
            </div>' ;
           })
           ->addColumn('publisher' , function ($qur){
               return '  <a href="'. route('admin.user.details' , $qur->post->user->uuid) .'"> <div class="chip">
                <img src="'. $qur->post->user->image . '" alt="Contact Person">' . $qur->post->user->name .'</div>
            </div></a>' ;
           })
           ->addColumn('actions' , function ($qur){
               $data_attr = '';
               $data_attr .= 'data-id="' . $qur->uuid  . '" ';
               $data_attr .= 'data-comment="' . $qur->comment . '"';
               $string = '' ;
               $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div  class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
                      <div  class="text-danger delete_btn" data-id="'. $qur->uuid .'" data-url="/admin/comments/delete"><i class="bi bi-trash-fill"></i></div>
                    </div>';
               return $string ;
           })
           ->addColumn('date' , function ($qur){
               $date = Carbon::parse($qur->created_at->toDateString());
               return $date->format('Y-M-d');;
           })
           ->addColumn('time' , function ($qur){
               return $qur->created_at->format('h:i A');;
           })
           ->rawColumns([ 'post' ,'publisher' , 'date' , 'time' , 'actions'])
           ->make(true);
   }






















}
