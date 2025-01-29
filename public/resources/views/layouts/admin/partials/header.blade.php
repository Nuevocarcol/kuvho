<style>
#toggle-sidebar{
  display:none;
}
@media screen and (max-width: 991px){
  #toggle-sidebar{
  display:block;
}
}
</style>
@php
  $notification_chat = App\Models\Chat::select(DB::raw('MAX(chats.id) as id'), 'from_user', 'to_user', 'message', 'url', 'send_post', 'chats.created_at', 'users.username', 'users.profile_pic', 'users.id as user_id')
                                        ->join('users', 'users.id', '=', 'chats.from_user')
                                        ->where('to_user', Auth::id())
                                        ->where('read_message', 0)->groupBy('from_user')->orderBy('id', 'DESC')->get();
@endphp
<div class="page-main-header">
  <div class="main-header-right row m-0">
    <div class="main-header-left border-0">
      <div class="logo-wrapper"><a href=""><img class="img-fluid" src="{{asset('assets/images/logo/logo.png')}}" alt=""></a></div>
      <div class="dark-logo-wrapper"><a href=""><img class="img-fluid" src="{{asset('assets/images/logo/dark-logo.png')}}" alt=""></a></div>
      <div class="toggle-sidebar" id="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle">    </i></div>
    </div>
    <div class="nav-right col pull-right right-menu p-0">
      <ul class="nav-menus">
       
      
        <li>
          @if($screen == "light-only")
          
            <div class="mode"><i class="fa fa-moon-o screen"></i></div>
          @else
            <div class="mode"><i class="fa fa-lightbulb-o screen " style="cursor:pointer;"></i></div>
          @endif
        </li>
        <li class="onhover-dropdown">
          
          @if(count($notification_chat) > 0)
          <div class="notification-box"><span class="dot-animated"></span></div>
          @endif
          <i data-feather="message-square"></i>
          
          <ul class="chat-dropdown onhover-show-div">
            @foreach($notification_chat as $message)
        
              <li>
                <a href="{{ url('chat?eid='.$message->user_id) }}">
                <div class="media">
                  <img class="img-fluid rounded-circle me-3" src="{{asset('assets/images/user/'.$message->profile_pic)}}" alt="" style="height:50px; width: 50px;">
                  <div class="media-body">
                    <span>{{ $message->username }}</span>
                    @if($message->message)
                      <p class="f-12 light-font">{{ $message->message }}</p>
                    @elseif($message->url)
                      <p class="f-12 light-font">File</p>
                    @else
                      <p class="f-12 light-font">Post</p>
                    @endif
                  </div>
                  <p class="f-12">{{ $message->created_at->diffForHumans()}}</p>
                </div></a>
              </li>
            @endforeach
            <li class="text-center"> <a class="f-w-700" href="chat">See All</a></li>
          </ul>
        </li>
        <li class="onhover-dropdown   " widht="20px">
          <a title="Log out" href="{{url('logout')}}" class="btn btn-primary-light" type="button"><i data-feather="log-out"></i></a>
        </li>
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>
