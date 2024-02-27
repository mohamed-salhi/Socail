<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
      <div>
        <img src="{{ asset('admin_assets/images/logo-icon.png')}}" class="logo-icon" alt="logo icon">
      </div>
      <div>
        <h4 class="logo-text">Snacked</h4>
      </div>
      <div class="toggle-icon ms-auto"> <i class="bi bi-list"></i>
      </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
      <li>
        <a href="javascript:;">
          <div class="parent-icon"><i class="bi bi-house-fill"></i>
          </div>
          <div class="menu-title">@lang('dashboard')</div>
        </a>
      </li>
      <li class="menu-label">@lang('admin_tools')</li>
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="lni lni-users"></i>
          </div>
          <div class="menu-title">@lang('mang_user')</div>
        </a>
        <ul>
            <li>
            <a href="{{ route("admin.user.index")  }}">
                <i class="bi bi-circle"></i>
                 @lang('all_users')
            </a>
            </li>
            <li>
                <a href="{{ route("admin.user.index_ban")  }}">
                    <i class="bi bi-circle"></i>
                    @lang('user_ban')
                </a>
            </li>
            <li>
                <a href="{{ route("admin.user.index_verified")  }}">
                    <i class="bi bi-circle"></i>
                    @lang('user_veri')
                </a>
            </li>
        </ul>
      </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-images"></i></i>
                </div>
                <div class="menu-title">@lang('mang_post')</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route("admin.post.index") }}">
                        <i class="bi bi-circle"></i>
                        @lang('posts') <small class="mx-1"> (@lang('images'))</small>
                    </a>
                </li>
                <li>
                    <a href="{{ route("admin.post.index") }}">
                        <i class="bi bi-circle"></i>
                        @lang('posts') <small class="mx-1"> (@lang('reels'))</small>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route("admin.comment.index") }}">
                <div class="parent-icon"><i class="lni lni-comments"></i>
                </div>
                <div class="menu-title">@lang('comments')</div>
            </a>
            <ul>
            </ul>
        </li>
    </ul>
    <!--end navigation-->
 </aside>
