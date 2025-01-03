<div class="sidebar">
    <button class="sidebar__compose btn_all_open" data-target="#create_mail_box1"> <span class="material-symbols-outlined">
            add
        </span>Compose</button>
    <div class="sidebarOption {{ Request::is('user/mail') ? 'sidebarOption__active' : '' }}"
        data-route="{{ route('mail.index') }}">
        <span class="material-symbols-outlined"> inbox </span>
        <h3>Inbox</h3>
    </div>
    <div class="sidebarOption {{ Request::is('user/mail/sent') || Request::is('user/mail/sent-mail-view/*') ? 'sidebarOption__active' : '' }}"
        data-route="{{ route('mail.sentList') }}">
        <span class="material-symbols-outlined">
            near_me
        </span>
        <h3>Sent</h3>
    </div>
    <div class="sidebarOption {{ Request::is('user/mail/star') || Request::is('user/mail/star-mail-view/*') ? 'sidebarOption__active' : '' }}"
        data-route="{{ route('mail.starList') }}">
        <span class="material-symbols-outlined"> star_border </span>
        <h3>Starred</h3>
    </div>

    <div class="sidebarOption {{ Request::is('user/mail/trash') || Request::is('user/mail/trash-mail-view/*') ? 'sidebarOption__active' : '' }}"
        data-route="{{ route('mail.trashList') }}">
        <span class="material-symbols-outlined">
            delete
        </span>
        <h3>Trash</h3>
    </div>
</div>
