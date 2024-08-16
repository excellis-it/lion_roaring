@if (isset($is_chat))
<div class="groupChatHead">
    <div class="main_avtar"><img src="{{ asset('user_assets/images/group.jpg') }}"
            alt=""></div>
    <div class="group_text">
        <p class="GroupName">David Johnson</p>
        <span>10 member, 5 Online</span>
    </div>
    <div class="group_text_right">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button"
                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" data-bs-toggle="modal" href="#groupInfo">Group
                        info</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="MessageContainer">
    <div class="messageSeperator"><span>Yesterday</span></div>
    <div class="message me">
        <p class="messageContent">Hello!</p>
        <div class="messageDetails">
            <div class="messageTime">3:21 PM</div>
            <i class="fas fa-check-double"></i>
        </div>
    </div>
    <div class="message me">
        <p class="messageContent">How are You!</p>
        <div class="messageDetails">
            <div class="messageTime">3:22 PM</div>
            <i class="fas fa-check-double"></i>
        </div>
    </div>
    <div class="message you">
        <p class="messageContent">I'm Fine!</p>
        <div class="messageDetails">
            <div class="messageTime">3:30 PM</div>
            <i class="fa-solid fa-check"></i>
        </div>
    </div>
    <div class="message you">
        <p class="messageContent">Lorem Ipsum is simply dummy text of the printing and typesetting
            industry.
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
            is simply
            dummy text of the printing and typesetting industry.</p>
        <div class="messageDetails">
            <div class="messageTime">3:32 PM</div>
            <i class="fa-solid fa-check"></i>
        </div>
    </div>
    <div class="message me">
        <p class="messageContent">Lorem Ipsum is simply dummy text of the printing and typesetting
            industry.
            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
            is simply
            dummy text of the printing and typesetting industry.</p>
        <div class="messageDetails">
            <div class="messageTime">3:36 PM</div>
            <i class="fas fa-check-double"></i>
        </div>
    </div>
    <div class="message me">
        <p class="messageContent">Send Me the Pics!</p>
        <div class="messageDetails">
            <div class="messageTime">3:21 PM</div>
            <i class="fas fa-check-double"></i>
        </div>
    </div>
    <div class="messageSeperator"><span>Today</span></div>
    <div class="message you">
        <p class="messageContent">Sorry for the Delay!</p>
        <div class="messageDetails">
            <div class="messageTime">8:09 AM</div>
            <i class="fa-solid fa-check"></i>
        </div>
    </div>
    <div class="message you">
        <p class="messageContent">Here are Pics!</p>
        <div class="messageDetails">
            <div class="messageTime">3:21 AM</div>
            <i class="fa-solid fa-check"></i>
        </div>
    </div>
</div>
<form id="MessageForm">
    <input type="text" id="MessageInput" placeholder="Type a message...">
    <div>
        <button class="Send">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0.82186 0.827412C0.565716 0.299519 0.781391 0.0763349 1.32445 0.339839L20.6267 9.70604C21.1614 9.96588 21.1578 10.4246 20.6421 10.7179L1.6422 21.526C1.11646 21.8265 0.873349 21.6115 1.09713 21.0513L4.71389 12.0364L15.467 10.2952L4.77368 8.9726L0.82186 0.827412Z"
                    fill="white" />
            </svg>
        </button>
    </div>
</form>

@else
<div class="icon_chat">
    <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
    <h4>Seamless Real-Time Chat | Connect Instantly</h4>
    <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group conversations, manage your contacts, and stay connected with instant updates. Experience a secure and responsive interface, perfect for personal or professional use.</p>
</div>
@endif
