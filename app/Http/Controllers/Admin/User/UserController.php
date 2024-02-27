<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Jobs\TemporaryBanUser;
use App\Models\Comment;
use App\Models\Followers;
use App\Models\Post;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use function Sodium\add;
use function Termwind\render;

class UserController extends Controller
{

    function index(){
        return view('admin.users.index');
    }

    function index_ban(){
        return view('admin.users.ban.index');
    }

    function index_verified(){
        return view('admin.users.verified.index');
    }

    function getdata(){
        $users = User::query()->orderBy('created_at' , 'desc');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user' , function ($qur){

                $star = '' ;
                if ($qur->star == 1 ){
                    $star = '<i style="color: #0a58ca" class="lni lni-star-filled"></i>';
                }

                return '<a style="color: black ; " href="'. route('admin.user.details' , $qur->uuid) .'">
                           <div class="d-flex align-items-center gap-3 cursor-pointer">
                                 <img src="' . $qur->image . '" class="rounded-circle" width="44" height="44" alt="">
                                 <div class="">
                                   <p class="mb-0">' . $qur->name . '</p>
                                 </div>
                                 '. $star .'
                              </div></a>';
            })
            ->addColumn('actions' , function ($qur){

                $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('permanent_ban') .'<span class="count-followers"></span></div></li>' ;
                if ($qur->status == User::BLOCK){
                    $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('remove_permanent_ban') .'<span class="count-followers"></span></div></li>';
                }

                $vri = '' ;
                if ($qur->star == User::VERIFIED){
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="remove" data-id="'. $qur->uuid .'">'. __('cancel_verification') .'<span class="count-posts"></span></div></li>' ;
                }else{
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="vri" data-id="'. $qur->uuid .'">'. __('account_verification') .'<span class="count-posts"></span></div></li>' ;
                }

                $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('temporary_ban') . '</div></li>' ;
                if ($qur->status == User::TEMPO) {
                    $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('remove_temporary_ban') . '</div></li>';
                }

                $data_attr = '';
                $data_attr .= 'data-id="' . $qur->uuid  . '" ';
                $data_attr .= 'data-name="' . $qur->name . '"';
                $data_attr .= 'data-email="' . $qur->email . '"';
                $data_attr .= 'data-mobile="' . $qur->mobile . '"';
                $data_attr .= 'data-gender="' . $qur->gender . '"';
                $data_attr .= 'data-date-of-birth="' . $qur->dateOfBirth . '"';

                $string = '' ;
                $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div class="dropdown">
  <div class="text-primary dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-eye-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="followers">'. __('followers') .'<span class="count-followers"></span></div></li>
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="following">'. __('following').'<span class="count-following"></span></div></li>
    <li><div class="dropdown-item posts_btn" data-bs-toggle="modal" data-bs-target="#show-posts"  data-id="'. $qur->uuid .'">'. __('posts') .'<span class="count-posts"></span></div></li>
    <a href="'. route('admin.comment.details', $qur->uuid) .'" ><li><div class="dropdown-item">'. __('comments') .' <span class="count-comments"></span></div></li></a>
    '. $vri .'
  </ul>
</div>
  <div  class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
 <div class="text-danger dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-trash-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    '. $ban .'
   '.  $tempo .'
  </ul>
</div>
  </div>';
                return $string ;

            })
            ->addColumn('gender' , function ($qur){
                if($qur->gender == 1){
                    return '<div class="badge rounded-pill alert-info">'. __('male').'</div>' ;
                }
                return '<div style="background-color: #ff69b4;
                                    color: #fff;
                                    border-color: #ff69b4;"
                                    class="badge rounded-pill">'. __('female').'</div>' ;
            })
            ->addColumn('status' , function ($qur){
                if($qur->status == User::NORMAL){
                    return '<span class="badge rounded-pill alert-success">'.__('active').'</span>';
                }elseif($qur->status == User::BLOCK){
                    return '<span class="badge rounded-pill alert-danger">'. __('banned') .'</span>';
                }elseif($qur->status == User::TEMPO){
                    return '<a href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views"data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views" aria-describedby="tooltip161567"><span class="badge rounded-pill alert-warning" >'. __('temporarily_banned') .'</span></a>';
                }
               // return __('female');
            })
            ->addColumn('dateOfBirth' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->dateOfBirth);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->addColumn('dateOfSign' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->created_at);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->rawColumns(['user' , 'gender' , 'dateOfBirth' , 'dateOfSign' , 'actions' , 'status'])
            ->make(true);
    }

    function getbanaccounts(Request $request){
        $users = User::query()->orderBy('created_at' , 'desc')->where('status' , '2')->orWhere('status' , '3');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user' , function ($qur){

                $star = '' ;
                if ($qur->star == 1 ){
                    $star = '<i style="color: #0a58ca" class="lni lni-star-filled"></i>';
                }

                return '<a style="color: black ; " href="'. route('admin.user.details' , $qur->uuid) .'">
                           <div class="d-flex align-items-center gap-3 cursor-pointer">
                                 <img src="' . asset($qur->image) . '" class="rounded-circle" width="44" height="44" alt="">
                                 <div class="">
                                   <p class="mb-0">' . $qur->name . '</p>
                                 </div>
                                 '. $star .'
                              </div></a>';
            })
            ->addColumn('actions' , function ($qur){

                $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('permanent_ban') .'<span class="count-followers"></span></div></li>' ;
                if ($qur->status == User::BLOCK){
                    $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('remove_permanent_ban') .'<span class="count-followers"></span></div></li>';
                }

                $vri = '' ;
                if ($qur->star == User::VERIFIED){
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="remove" data-id="'. $qur->uuid .'">'. __('cancel_verification') .'<span class="count-posts"></span></div></li>' ;
                }else{
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="vri" data-id="'. $qur->uuid .'">'. __('account_verification') .'<span class="count-posts"></span></div></li>' ;
                }

                $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('temporary_ban') . '</div></li>' ;
                if ($qur->status == User::TEMPO) {
                    $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('remove_temporary_ban') . '</div></li>';
                }

                $data_attr = '';
                $data_attr .= 'data-id="' . $qur->uuid  . '" ';
                $data_attr .= 'data-name="' . $qur->name . '"';
                $data_attr .= 'data-email="' . $qur->email . '"';
                $data_attr .= 'data-mobile="' . $qur->mobile . '"';
                $data_attr .= 'data-gender="' . $qur->gender . '"';
                $data_attr .= 'data-date-of-birth="' . $qur->dateOfBirth . '"';

                $string = '' ;
                $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div class="dropdown">
  <div class="text-primary dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-eye-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="followers">'. __('followers') .'<span class="count-followers"></span></div></li>
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="following">'. __('following').'<span class="count-following"></span></div></li>
    <li><div class="dropdown-item posts_btn" data-bs-toggle="modal" data-bs-target="#show-posts"  data-id="'. $qur->uuid .'">'. __('posts') .'<span class="count-posts"></span></div></li>
    <a href="'. route('admin.comment.details', $qur->uuid) .'" ><li><div class="dropdown-item">'. __('comments') .' <span class="count-comments"></span></div></li></a>
    '. $vri .'
  </ul>
</div>
  <div  class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
 <div class="text-danger dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-trash-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    '. $ban .'
   '.  $tempo .'
  </ul>
</div>
  </div>';
                return $string ;

            })
            ->addColumn('gender' , function ($qur){
                if($qur->gender == 1){
                    return '<div class="badge rounded-pill alert-info">'. __('male').'</div>' ;
                }
                return '<div style="background-color: #ff69b4;
                                    color: #fff;
                                    border-color: #ff69b4;"
                                    class="badge rounded-pill">'. __('female').'</div>' ;
            })
            ->addColumn('status' , function ($qur){
                if($qur->status == User::NORMAL){
                    return '<span class="badge rounded-pill alert-success">'.__('active').'</span>';
                }elseif($qur->status == User::BLOCK){
                    return '<span class="badge rounded-pill alert-danger">'. __('banned') .'</span>';
                }elseif($qur->status == User::TEMPO){
                    return '<a href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views"data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views" aria-describedby="tooltip161567"><span class="badge rounded-pill alert-warning" >'. __('temporarily_banned') .'</span></a>';
                }
                // return __('female');
            })
            ->addColumn('dateOfBirth' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->dateOfBirth);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->addColumn('dateOfSign' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->created_at);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->rawColumns(['user' , 'gender' , 'dateOfBirth' , 'dateOfSign' , 'actions' , 'status'])
            ->make(true);
    }

    function getverifiedaccounts(Request $request){
        $users = User::query()->orderBy('created_at' , 'desc')->where('star' , '1');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('user' , function ($qur){

                $star = '' ;
                if ($qur->star == 1 ){
                    $star = '<i style="color: #0a58ca" class="lni lni-star-filled"></i>';
                }

                return '<a style="color: black ; " href="'. route('admin.user.details' , $qur->uuid) .'">
                           <div class="d-flex align-items-center gap-3 cursor-pointer">
                                 <img src="' . asset($qur->image) . '" class="rounded-circle" width="44" height="44" alt="">
                                 <div class="">
                                   <p class="mb-0">' . $qur->name . '</p>
                                 </div>
                                 '. $star .'
                              </div></a>';
            })
            ->addColumn('actions' , function ($qur){

                $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('permanent_ban') .'<span class="count-followers"></span></div></li>' ;
                if ($qur->status == User::BLOCK){
                    $ban = '<li><div class="dropdown-item permanent_btn" data-ban="remove_permanent"  data-id="'. $qur->uuid .'">'. __('remove_permanent_ban') .'<span class="count-followers"></span></div></li>';
                }

                $vri = '' ;
                if ($qur->star == User::VERIFIED){
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="remove" data-id="'. $qur->uuid .'">'. __('cancel_verification') .'<span class="count-posts"></span></div></li>' ;
                }else{
                    $vri = '<li><div class="dropdown-item vri_btn" data-vri="vri" data-id="'. $qur->uuid .'">'. __('account_verification') .'<span class="count-posts"></span></div></li>' ;
                }

                $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('temporary_ban') . '</div></li>' ;
                if ($qur->status == User::TEMPO) {
                    $tempo = '<li><div class="dropdown-item tempo_btn" data-bs-toggle="modal" data-bs-target="#tempo-modal"  data-id="'. $qur->uuid .'">' . __('remove_temporary_ban') . '</div></li>';
                }

                $data_attr = '';
                $data_attr .= 'data-id="' . $qur->uuid  . '" ';
                $data_attr .= 'data-name="' . $qur->name . '"';
                $data_attr .= 'data-email="' . $qur->email . '"';
                $data_attr .= 'data-mobile="' . $qur->mobile . '"';
                $data_attr .= 'data-gender="' . $qur->gender . '"';
                $data_attr .= 'data-date-of-birth="' . $qur->dateOfBirth . '"';

                $string = '' ;
                $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div class="dropdown">
  <div class="text-primary dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-eye-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="followers">'. __('followers') .'<span class="count-followers"></span></div></li>
    <li><div class="dropdown-item followers_btn" data-bs-toggle="modal" data-bs-target="#show-followers"  data-id="'. $qur->uuid .'" data-type="following">'. __('following').'<span class="count-following"></span></div></li>
    <li><div class="dropdown-item posts_btn" data-bs-toggle="modal" data-bs-target="#show-posts"  data-id="'. $qur->uuid .'">'. __('posts') .'<span class="count-posts"></span></div></li>
    <a href="'. route('admin.comment.details', $qur->uuid) .'" ><li><div class="dropdown-item">'. __('comments') .' <span class="count-comments"></span></div></li></a>
    '. $vri .'
  </ul>
</div>
  <div  class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
 <div class="text-danger dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-trash-fill"></i>
  </div>
  <ul class="dropdown-menu" tabindex="-88888" aria-labelledby="dropdownMenuButton">
    '. $ban .'
   '.  $tempo .'
  </ul>
</div>
  </div>';
                return $string ;

            })
            ->addColumn('gender' , function ($qur){
                if($qur->gender == 1){
                    return '<div class="badge rounded-pill alert-info">'. __('male').'</div>' ;
                }
                return '<div style="background-color: #ff69b4;
                                    color: #fff;
                                    border-color: #ff69b4;"
                                    class="badge rounded-pill">'. __('female').'</div>' ;
            })
            ->addColumn('status' , function ($qur){
                if($qur->status == User::NORMAL){
                    return '<span class="badge rounded-pill alert-success">'.__('active').'</span>';
                }elseif($qur->status == User::BLOCK){
                    return '<span class="badge rounded-pill alert-danger">'. __('banned') .'</span>';
                }elseif($qur->status == User::TEMPO){
                    return '<a href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views"data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="View detail" aria-label="Views" aria-describedby="tooltip161567"><span class="badge rounded-pill alert-warning" >'. __('temporarily_banned') .'</span></a>';
                }
                // return __('female');
            })
            ->addColumn('dateOfBirth' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->dateOfBirth);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->addColumn('dateOfSign' , function ($qur){
                $dateOfBirth = Carbon::parse($qur->created_at);
                return $dateOfBirth->format('Y-M-d');;
            })
            ->rawColumns(['user' , 'gender' , 'dateOfBirth' , 'dateOfSign' , 'actions' , 'status'])
            ->make(true);
    }

    function store(Request $request){

        $request->validate([
            'name' => 'required|string:255' ,
            'username' => 'required|string:255|unique:users,user' ,
            'email' => 'required|email' ,
            'mobile' => 'required' ,
            'date_of_birth' => 'required' ,
            'password' => 'required' ,
        ]);

      $user = User::create([
            'name' => $request->name ,
            'mobile' => $request->mobile ,
            'email' => $request->email ,
            'gender' => $request->gender ,
            'user' => $request->username ,
            'dateOfBirth' => $request->date_of_birth,
            'password' => Hash::make($request->password)
        ]);

        UploadImage($request->image, User::PATH_IMAGE, User::class, $user->uuid, false, null, Upload::IMAGE);

        return response()->json([
            "success" => "success"
        ] , 201);

    }


    function update(Request $request){

        $request->validate([
            'name' => 'required|string:255' ,
            'email' => 'required|email' ,
            'mobile' => 'required' ,
            'date_of_birth' => 'required' ,
            'image' => 'nullable' ,
            'password' => 'nullable' ,
        ]);

        $user = User::query()->where('uuid' , $request->id)->first();
        $user->update([
            'name' => $request->name ,
            'mobile' => $request->mobile ,
            'email' => $request->email ,
            'gender' => $request->gender ,
            'dateOfBirth' => $request->date_of_birth
        ]);


            if($request->hasFile('image')) {
                UploadImage($request->image, User::PATH_IMAGE, User::class, $user->uuid, true, null, Upload::IMAGE);
            }

            if($request->password != null){
                $user->update([
                   'password' => Hash::make($request->password) ,
                ]);
            }



            return response()->json([
                'success' => __('add_successfully')
            ] , 201);
    }

    function getfollowers(Request $request){
        $type = $request->type ;
        if($request->type == 'followers'){
            $followers = Followers::query()->where('receiver_uuid' , $request->id)->get();
        }else if($request->type == 'following'){
            $followers = Followers::query()->where('user_uuid' , $request->id)->get();
        }

    return view('admin.parts.list_followers' , compact('followers' , 'type'))->render();
}

    function getposts(Request $request){

       $posts = Post::query()->where('user_uuid' , $request->id)->where('type' , 'post')->get() ;
        return view('admin.parts.list_posts' , compact('posts'))->render();
    }


    function comments($id){
        $user = User::query()->where('uuid' , $id)->first();
        return view('admin.comments.index' , compact('user'));
    }

    function getcomments($id){
        $comments = Post::query();
        return DataTables::of($comments)
            ->addIndexColumn()
            ->addColumn('post' , function ($qur){
                return '<div class="d-flex align-items-center gap-3 cursor-pointer">
                                 <img src="' . @$qur->attachments['attachment'] . '" class="rounded-circle" width="44" height="44" alt="">
                                 <div class="">
                                   <p class="mb-0">' .  $qur->content. '</p>
                                 </div>
                              </div>';
            })
            ->addColumn('user' , function ($qur){
                return '<div class="d-flex align-items-center gap-3 cursor-pointer">
                                 <img src="' . @$qur->user->image . '" class="rounded-circle" width="44" height="44" alt="">
                                 <div class="">
                                   <p class="mb-0">' . $qur->user->name . '</p>
                                 </div>
                              </div>';
            })
            ->addColumn('comment' , function ($qur){
                  if(count($qur->comments) > 1){
                      return '<a style="color: #0b0693 ;   font-weight: bold;">'. __('more') . " +" . '</a>';
                  }else{
                      return @$qur->comments[0]->comment ;
                  }
            })
            ->addColumn('actions' , function ($qur){
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $qur->uuid  . '" ';
                $data_attr .= 'data-comment="' . $qur->comment . '"';

                $string = '' ;
                $string .= '
               <div class="d-flex align-items-center gap-3 fs-6">
                      <div class="text-warning edit_btn" data-bs-toggle="modal" data-bs-target="#edit-modal" ' . $data_attr . '><i class="bi bi-pencil-fill"></i></div>
                      <div class="text-danger" ><i class="bi bi-trash-fill"></i></div>
                    </div>';
                return $string ;

            })
            ->rawColumns(['actions' , 'post' , 'user' , 'comment'])
            ->make(true);
    }

    function details($id){
        $user = User::query()->where('uuid' , $id)->first();
        return view('admin.users.details.index' , compact('user'));
    }

    function account_verification(Request $request){

        $user = User::query()->where('uuid' , $request->id)->first();
        if($request->vri == "vri") {
            $user->update(['star' => User::VERIFIED]);
        }elseif($request->vri == "remove"){
            $user->update(['star' => User::UNVERIFIED]);
        }
        return response()->json();
    }

    function ban(Request $request){

        $user = User::query()->where('uuid' , $request->id)->first();

        if($request->ban == "permanent"){
           $user->update([
               'status' => User::BLOCK
           ]);
        }elseif ($request->ban == "remove_permanent"){
            $user->update([
                'status' => User::UNBLOCK
            ]);
        }

        return response()->json([
            "success" => "success"
        ] , 201);
    }

    function temporary_ban(Request $request){

        dispatch(new TemporaryBanUser($request->id, User::TEMPO))->delay(now()->addMinutes(2));

        return response()->json();
    }


































}
